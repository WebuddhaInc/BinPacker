<pre>
<?php

include 'autoload.php';

$packager = new \WebuddhaInc\BinPacker\Packer();

$packager->addItem(array(
  'description' => 'Product A',
  'quantity'    => 1,
  'volume_unit' => 'IN',
  'width'       => 2,
  'height'      => 4,
  'length'      => 2,
  'weight_unit' => 'LB',
  'weight'      => 20
  ));
$packager->addItem(array(
  'description' => 'Product B',
  'quantity'    => 4,
  'volume_unit' => 'IN',
  'width'       => 2,
  'height'      => 3,
  'length'      => 4,
  'weight_unit' => 'LB',
  'weight'      => 5
  ));

$packager->addItem(array(
  'description' => 'Product C',
  'quantity'    => 3,
  'volume_unit' => 'IN',
  'width'       => 4,
  'height'      => 1,
  'length'      => 2,
  'weight_unit' => 'LB',
  'weight'      => 5
  ));

$packager->addPackageOption(array(
  'volume_unit' => 'IN',
  'volume'      => 1000,
  'width'       => 10,
  'height'      => 10,
  'length'      => 10,
  'weight_unit' => 'LB',
  'weight'      => 40
  ));
$packager->addPackageOption(array(
  'volume_unit' => 'IN',
  'volume'      => 1500,
  'width'       => 15,
  'height'      => 10,
  'length'      => 10,
  'weight_unit' => 'LB',
  'weight'      => 40
  ));
$packager->addPackageOption(array(
  'volume_unit' => 'CM',
  'volume'      => 1000,
  'width'       => 10,
  'height'      => 10,
  'length'      => 10,
  'weight_unit' => 'OZ',
  'weight'      => 40
  ));

print_r(array(
  'Packages',
  $packager->getPackages()
  ));

print_r(array(
  'Volume Only Packages',
  $packager->getVolumePackages()
  ));

