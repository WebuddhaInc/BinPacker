<?php

namespace WebuddhaInc\BinPacker;

class Item extends Params {

  public function validate(){

    // Quantity
      $itemQuantity = (float)$this->get('quantity', 1.0);

    // Volume
      $itemVolumeUnit = strtoupper($this->get( 'volume_unit', 'IN' ));
      switch( $itemVolumeUnit ){
        case 'CM':
          $itemVolumeUnit = 'IN';
          $itemVolumeModifier = 0.3937008;
          break;
        default:
          $itemVolumeUnit = 'IN';
          $itemVolumeModifier = 1;
          break;
      }
      $itemWidth  = (float)$this->get('width', 0.0) * $itemVolumeModifier;
      $itemHeight = (float)$this->get('height', 0.0) * $itemVolumeModifier;
      $itemLength = (float)$this->get('length', 0.0) * $itemVolumeModifier;
      if( (float)$this->get('volume', 0.0) ){
        $itemVolume = (float)$this->get('volume', 0.0) * $itemVolumeModifier;
      }
      else {
        $itemVolume = ($itemWidth * $itemHeight * $itemLength);
      }

    // Weight
      $itemWeightUnit = strtoupper($this->get('weight_unit', 'LB'));
      switch( $itemWeightUnit ){
        case 'KG':
          $itemWeightUnit = 'LB';
          $itemWeightModifier = 2.20462;
          break;
        case 'OZ':
          $itemWeightUnit = 'LB';
          $itemWeightModifier = 0.0625;
          break;
        default:
          $itemWeightUnit = 'LB';
          $itemWeightModifier = 1;
          break;
      }
      $itemWeight = (float)$this->get('weight') * $itemWeightModifier;

    // Apply Validated
      $this->merge(array(
        'quantity'    => $itemQuantity,
        'volume_unit' => $itemVolumeUnit,
        'volume'      => round($itemVolume, 2, PHP_ROUND_HALF_DOWN),
        'width'       => round($itemWidth, 2, PHP_ROUND_HALF_DOWN),
        'height'      => round($itemHeight, 2, PHP_ROUND_HALF_DOWN),
        'length'      => round($itemLength, 2, PHP_ROUND_HALF_DOWN),
        'weight_unit' => $itemWeightUnit,
        'weight'      => round($itemWeight, 2, PHP_ROUND_HALF_DOWN)
        ));

  }

}