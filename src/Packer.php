<?php

namespace WebuddhaInc\BinPacker;

use WebuddhaInc\BinPacker\Box;
use WebuddhaInc\BinPacker\Item;
use WebuddhaInc\BinPacker\Params;

class Packer {

  /**
   * [$items description]
   * @var array
   */
  private $items = array();

  /**
   * [$packageOptions description]
   * @var array
   */
  private $packageOptions = array();

  /**
   * [__construct description]
   * @param array $params [description]
   */
  public function __construct( $params = array() ){
    $params = new Params( $params );
    if( is_array($params->get('items')) ){
      foreach( $params->get('items') AS $item ){
        $this->addItem($item);
      }
    }
    if( is_array($params->get('packageOptions')) ){
      foreach( $params->get('packageOptions') AS $packageOption ){
        $this->addPackageOption($packageOption);
      }
    }
  }

  /**
   * [addItem description]
   * @param [type] $data [description]
   */
  public function addItem( $data ){

    // Item
      $item = new Item( array(
        'quantity'    => null,
        'volume_unit' => 'IN',
        'volume'      => null,
        'width'       => null,
        'height'      => null,
        'length'      => null,
        'weight_unit' => 'LB',
        'weight'      => null
        ), $data );

    // Push onto stack
      for( $i=0; $i<$item->get('quantity'); $i++ ){
        $this->items[] = new Item(
          (array)$item,
          array('quantity' => 1)
          );
      }

  }

  /**
   * [getItems description]
   * @return [type] [description]
   */
  public function getItems(){
    return $this->items;
  }

  /**
   * [getItemsTotalWeight description]
   * @return [type] [description]
   */
  public function getItemsTotalWeight(){
    $totalWeight = 0;
    foreach( $this->getItems() AS $itemKey => $item ){
      $totalWeight += $item->get('weight');
    }
    return $totalWeight;
  }

  /**
   * [getItemsTotalVolume description]
   * @return [type] [description]
   */
  public function getItemsTotalVolume(){
    $totalVolume = 0;
    foreach( $this->getItems() AS $itemKey => $item ){
      $totalVolume += $item->get('volume');
    }
    return $totalVolume;
  }

  /**
   * [addPackageOption description]
   * @param [type] $data [description]
   */
  public function addPackageOption( $data ){

    // PackageOption
      $packageOption = new PackageOption( array(
        'volume_unit'      => 'IN',
        'volume'           => null,
        'width'            => null,
        'height'           => null,
        'length'           => null,
        'weight_unit'      => 'LB',
        'weight'           => null
        ), $data );

    // Push onto Stack
      $this->packageOptions[] = $packageOption;

  }

  /**
   * [getPackageOptions description]
   * @return [type] [description]
   */
  public function getPackageOptions(){
    $packageOptions = array();
    $packageOptions_volumes = array();
    $packageOptions_weights = array();
    for( $i=0; $i<count($this->packageOptions); $i++ ){
      $packageOption =& $this->packageOptions[$i];
      $packageOptions[] = $packageOption;
      $packageOptions_volumes[] = $packageOption->get('volume');
      $packageOptions_weights[] = $packageOption->get('weight');
    }
    array_multisort(
      $packageOptions_volumes, SORT_ASC, SORT_NUMERIC,
      $packageOptions_weights, SORT_ASC, SORT_NUMERIC,
      $packageOptions
      );
    return $packageOptions;
  }

  /**
   * [getPackages description]
   * @return [type] [description]
   */
  public function getPackages(){

    /**
     * Storage
     */
      $packedBoxes          = array();
      $items                = $this->getItems();
      $packageOptions       = $this->getPackageOptions();

    /**
     * Loop and Pack
     */
      do {

        $package_found = false;
        $this->_sortItems( $items );
        foreach( $items AS $itemKey => $item ){

          // Existing Package
            $this->_sortPackagedBoxes( $packageBoxes );
            foreach( $packedBoxes AS $packageKey => $package ){
              if( $item->get('weight') <= ($package->get('weight') - $package->get('weight_used')) ){

                /**
                 * This is a 2D evaluating only
                 */

                if(
                  $item->get('width') <= ($package->get('width') - $package->get('width_used'))
                  && $item->get('height') <= ($package->get('height') - $package->get('height_used'))
                  && $item->get('length') <= ($package->get('length') - $package->get('length_used'))
                  ){
                  $package->add('width_used', $item->get('width'));
                  $package->add('height_used', $item->get('height'));
                  $package->add('length_used', $item->get('length'));
                  $package->add('volume_used', ($item->get('width') * $item->get('height') * $item->get('length')));
                  $package->add('weight_used', $item->get('weight'));
                  $package->add('items_packed', $item);
                  $package_found = true;
                  break;
                }
                else if(
                  $item->get('height') <= ($package->get('width') - $package->get('width_used'))
                  && $item->get('length') <= ($package->get('height') - $package->get('height_used'))
                  && $item->get('width') <= ($package->get('length') - $package->get('length_used'))
                  ){
                  $item->set('rotate', 'x-axis');
                  $package->add('width_used', $item->get('height'));
                  $package->add('height_used', $item->get('length'));
                  $package->add('length_used', $item->get('width'));
                  $package->add('volume_used', ($item->get('width') * $item->get('height') * $item->get('length')));
                  $package->add('weight_used', $item->get('weight'));
                  $package->add('items_packed', $item);
                  $package_found = true;
                  break;
                }
                else if(
                  $item->get('length') <= ($package->get('width') - $package->get('width_used'))
                  && $item->get('width') <= ($package->get('height') - $package->get('height_used'))
                  && $item->get('height') <= ($package->get('length') - $package->get('length_used'))
                  ){
                  $item->set('rotate', 'y-axis');
                  $package->add('width_used', $item->get('length'));
                  $package->add('height_used', $item->get('width'));
                  $package->add('length_used', $item->get('height'));
                  $package->add('volume_used', ($item->get('width') * $item->get('height') * $item->get('length')));
                  $package->add('weight_used', $item->get('weight'));
                  $package->add('items_packed', $item);
                  $package_found = true;
                  break;
                }
              }
            }

          // New Package
            if( !$package_found ){
              foreach( $packageOptions AS $packageKey => $package ){
                if(
                  $item->get('weight') <= $package->get('weight')
                  && $item->get('width') <= $package->get('width')
                  && $item->get('height') <= $package->get('height')
                  && $item->get('length') <= $package->get('length')
                  ){
                  $new_package = new Package($package);
                  $new_package->set('width_used', $item->get('width'));
                  $new_package->set('height_used', $item->get('height'));
                  $new_package->set('length_used', $item->get('length'));
                  $new_package->set('volume_used', ($item->get('width') * $item->get('height') * $item->get('length')));
                  $new_package->set('weight_used', $item->get('weight'));
                  $new_package->set('items_packed', array($item));
                  $packedBoxes[] = $new_package;
                  $package_found = true;
                  break;
                }
              }
            }

          // Remove Item
            if( $package_found ){
              unset( $items[ $itemKey ] );
              break;
            }

        }

      } while( $package_found );

    /**
     * Complete
     */
      return $packedBoxes;

  }

  /**
   * [getVolumePackages description]
   * @return [type] [description]
   */
  public function getVolumePackages(){

    /**
     * Storage
     */
      $packedBoxes          = array();
      $items                = $this->getItems();
      $packageOptions       = $this->getPackageOptions();

    /**
     * Loop and Pack
     */
      do {

        $package_found = false;
        $this->_sortItems( $items );
        foreach( $items AS $itemKey => $item ){

          // Existing Package
            $this->_sortPackagedBoxes( $packageBoxes );
            foreach( $packedBoxes AS $packageKey => $package ){
              if( $item->get('weight') <= ($package->get('weight') - $package->get('weight_used')) ){
                $package->add('volume_used', ($item->get('width') * $item->get('height') * $item->get('length')));
                $package->add('weight_used', $item->get('weight'));
                $package->add('items_packed', $item);
                $package_found = true;
              }
            }

          // New Package
            if( !$package_found ){
              foreach( $packageOptions AS $packageKey => $package ){
                if( $item->get('weight') <= $package->get('weight') ){
                  $new_package = new Package($package);
                  $new_package->set('volume_used', ($item->get('width') * $item->get('height') * $item->get('length')));
                  $new_package->set('weight_used', $item->get('weight'));
                  $new_package->set('items_packed', array($item));
                  $packedBoxes[] = $new_package;
                  $package_found = true;
                  break;
                }
              }
            }

          // Remove Item
            if( $package_found ){
              unset( $items[ $itemKey ] );
              break;
            }

        }

      } while( $package_found );

    /**
     * Complete
     */
      return $packedBoxes;

  }

  /**
   * [getVolumePackagesByGross description]
   * @return [type] [description]
   */
  public function getVolumePackagesByGross(){

    /**
     * Storage
     */
      $itemVolumeTotal      = 0;
      $itemWeightTotal      = 0;
      $final_packages       = array();
      $items                = $this->getItems();
      $packageOptions       = $this->getPackageOptions();

    /**
     * Total Items
     */
      for( $i=0; $i<count($items); $i++ ){
        $itemVolumeTotal += $items[$i]->get('volume');
        $itemWeightTotal += $items[$i]->get('weight');
      }

    /**
     * Calculate based on Package Dimensions
     */
      if( $packageOptions && ($itemVolumeTotal || $itemWeightTotal) ){

        $itemVolumeRemain    = $itemVolumeTotal;
        $itemWeightRemain    = $itemWeightTotal;
        $itemWeightSizeRatio = $itemVolumeRemain / $itemWeightRemain;
        do {

          // Prepare
            $package_found = false;

          // Fit into New Package
            if( !$package_found ){
              foreach( $packageOptions AS $key => $package ){
                if(
                  $package->get('volume') >= $itemVolumeRemain
                  && $package->get('weight') >= $itemWeightRemain
                  ){
                  $new_package = new Package($package);
                  $new_package->set('volume_used', $itemVolumeRemain);
                  $new_package->set('weight_used', $itemWeightRemain);
                  $final_packages[] = $new_package;
                  $itemVolumeRemain = 0;
                  $itemWeightRemain = 0;
                  $package_found = true;
                  break;
                }
              }
            }

          // Split and place into largest package
            if( !$package_found ){
              $new_package = new Package(end( $packageOptions ));
              $package_maxVolume = $new_package->get('volume');
              $package_maxWeight = $new_package->get('weight');
              $package_ratio = $package_maxVolume / $package_maxWeight;
              $newVolumeUsed = null;
              $newWeightUsed = null;
              $isOverVolume = ($itemVolumeRemain > $package_maxVolume);
              $isOverWeight = ($itemWeightRemain > $package_maxWeight);
              if( $isOverVolume || $isOverWeight ){
                if( $itemWeightSizeRatio > $package_ratio ){
                  $newVolumeUsed = ($isOverVolume ? $package_maxVolume : $itemVolumeRemain);
                  $newWeightUsed = round($newVolumeUsed / $itemWeightSizeRatio, 2, PHP_ROUND_HALF_DOWN);
                  if( $newWeightUsed > $package_maxWeight ){
                    $newWeightUsed = $package_maxWeight;
                    $newVolumeUsed = round($newWeightUsed * $itemWeightSizeRatio, 2, PHP_ROUND_HALF_DOWN);
                  }
                }
                else {
                  $newWeightUsed = ($isOverWeight ? $package_maxWeight : $itemWeightRemain);
                  $newVolumeUsed = round($newWeightUsed * $itemWeightSizeRatio, 2, PHP_ROUND_HALF_DOWN);
                  if( $newVolumeUsed > $package_maxVolume ){
                    $newVolumeUsed = $package_maxVolume;
                    $newWeightUsed = round($newVolumeUsed / $itemWeightSizeRatio, 2, PHP_ROUND_HALF_DOWN);
                  }
                }
              }
              if( is_null($netVolumeUsed) || $netVolumeUsed > $itemVolumeRemain )
                $netVolumeUsed = $itemVolumeRemain;
              if( is_null($newWeightUsed) || $newWeightUsed > $itemWeightRemain )
                $newWeightUsed = $itemWeightRemain;
              $new_package->set('volume_used', ($newVolumeUsed > 0 ? $newVolumeUsed : 1));
              $new_package->set('weight_used', ($newWeightUsed > 0 ? $newWeightUsed : 1));
              $final_packages[] = $new_package;
              $itemVolumeRemain -= $newVolumeUsed;
              $itemWeightRemain -= $newWeightUsed;
              $package_found = true;
            }

        } while( $package_found && ($itemVolumeRemain || $itemWeightRemain) );

      }

    /**
     * Complete
     */
      return $final_packages;

  }

  /**
   * [_sortItems description]
   * @param  [type] &$items [description]
   * @return [type]         [description]
   */
  private function _sortItems( &$items ){
    if( empty($items) ) return;
    $items_volumes = array();
    $items_weights = array();
    foreach( $items AS $itemKey => $item ){
      $items_volumes[] = $item->get('volume');
      $items_weights[] = $item->get('weight');
    }
    array_multisort(
      $items_volumes, SORT_DESC, SORT_NUMERIC,
      $items_weights, SORT_DESC, SORT_NUMERIC,
      $items
      );
  }

  /**
   * [_sortPackagedBoxes description]
   * @param  [type] &$packedBoxes [description]
   * @return [type]               [description]
   */
  private function _sortPackagedBoxes( &$packedBoxes ){
    if( empty($packedBoxes) ) return;
    $packedBoxes_volumes = array();
    $packedBoxes_weights = array();
    foreach( $packedBoxes AS $packageKey => $package ){
      $packedBoxes_volumes[] = $package->get('volume_used');
      $packedBoxes_weights[] = $package->get('weight_used');
    }
    array_multisort(
      $packedBoxes_volumes, SORT_ASC, SORT_NUMERIC,
      $packedBoxes_weights, SORT_ASC, SORT_NUMERIC,
      $packedBoxes
      );
  }

}