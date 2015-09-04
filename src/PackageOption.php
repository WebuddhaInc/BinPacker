<?php

namespace WebuddhaInc\BinPacker;

class PackageOption extends Params {

  public function validate(){

    // Prepare Volume
      $packageVolumeUnit = strtoupper($this->get('volume_unit', 'IN'));
      switch( $packageVolumeUnit ){
        case 'CM':
          $packageVolumeUnit = 'IN';
          $packageVolumeModifier = 0.3937008;
          break;
        default:
          $packageVolumeUnit = 'IN';
          $packageVolumeModifier = 1;
          break;
      }
      $packageWidth  = (real)$this->get('width', 0.0) * $packageVolumeModifier;
      $packageHeight = (real)$this->get('height', 0.0) * $packageVolumeModifier;
      $packageLength = (real)$this->get('length', 0.0) * $packageVolumeModifier;
      if( $packageWidth && $packageHeight && $packageLength ){
        $packageVolume = ($packageWidth * $packageHeight * $packageLength);
      }
      else if( (real)$this->get('volume', 0.0) ){
        $packageVolume = (real)$this->get('volume', 0.0) * $packageVolumeModifier;
        $packageWidth = $packageHeight = $packageLength = sqrt( $packageVolume );
      }

    // Prepare Weight
      $packageWeightUnit = strtoupper($this->get('weight_unit', 'LB'));
      switch( $packageWeightUnit ){
        case 'KG':
          $packageWeightUnit = 'LB';
          $packageWeightModifier = 2.20462;
          break;
        case 'OZ':
          $packageWeightUnit = 'LB';
          $packageWeightModifier = 0.0625;
          break;
        default:
          $packageWeightUnit = 'LB';
          $packageWeightModifier = 1;
          break;
      }
      $packageWeight = (real)$this->get('weight') * $packageWeightModifier;

    // Push onto Stack
      $this->merge(array(
        'volume_unit' => $packageVolumeUnit,
        'volume'      => round($packageVolume, 2, PHP_ROUND_HALF_DOWN),
        'width'       => round($packageWidth, 2, PHP_ROUND_HALF_DOWN),
        'height'      => round($packageHeight, 2, PHP_ROUND_HALF_DOWN),
        'length'      => round($packageLength, 2, PHP_ROUND_HALF_DOWN),
        'weight_unit' => $packageWeightUnit,
        'weight'      => round($packageWeight, 2, PHP_ROUND_HALF_DOWN)
        ));

  }

}