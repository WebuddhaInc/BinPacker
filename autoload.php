<?php

function WebuddhaInc_BinPacker_SPLAutoLoad( $class ){
  $prefix = 'WebuddhaInc\\BinPacker\\';
  if( strpos($class, $prefix) === 0 ){
    require_once __DIR__ . '/src/' . str_replace( '\\', '/', substr($class, strlen($prefix)) ) . '.php';
  }
}
spl_autoload_register( 'WebuddhaInc_BinPacker_SPLAutoLoad' );
