<?php

namespace WebuddhaInc\BinPacker;

class Params {

  /**
   * [__construct description]
   */
  public function __construct(){
    $args = func_get_args();
    if( $args ){
      foreach( $args AS $arg ){
        if( is_object($arg) || is_array($arg) ){
          $this->merge( $arg );
        }
      }
    }
    $this->validate();
  }

  /**
   * [validate description]
   * @return [type] [description]
   */
  public function validate(){
  }

  /**
   * [get description]
   * @param  [type] $key [description]
   * @param  [type] $def [description]
   * @return [type]      [description]
   */
  public function &get( $key, $def=null ){
    $keyChain = explode('.',$key);
    $value =& $this;
    foreach( $keyChain AS $keyStep )
      if( isset($value->{$keyStep}) )
        $value =& $value->{$keyStep};
      else
        return $def;
    return $value;
  }

  /**
   * [set description]
   * @param [type] $key [description]
   * @param [type] $val [description]
   */
  public function &set( $key, $val=null ){
    $keyChain = explode('.',$key);
    $value =& $this;
    for($i=0;$i<count($keyChain);$i++){
      if( !trim($keyChain[$i]) )return false;
      if( $i < count($keyChain)-1 ){
        if( !isset($value->{$keyChain[$i]}) )
          $value->{$keyChain[$i]} = new stdClass();
        $value =& $value->{$keyChain[$i]};
      } else if(is_string($val) && in_array(strtolower($val),array('true','false')))
        $value->{$keyChain[$i]} = (bool)preg_match('/true/i',$val);
      else
        $value->{$keyChain[$i]} = $val;
    }
    return $this;
  }

  /**
   * [add description]
   * @param [type] $key      [description]
   * @param [type] $addValue [description]
   */
  public function &add( $key, $addValue ){
    $value = $this->get( $key );
    if( is_numeric($value) ){
      $this->set( $key, $value + (real)$addValue );
    }
    else if( is_string($value) ){
      $this->set( $key, $value . (string)$addValue );
    }
    else if( is_array($value) ){
      $this->set( $key, array_merge($value, (is_array($addValue) ? $addValue : array($addValue))) );
    }
    return $this;
  }

  /**
   * [merge description]
   * @param  [type] $data [description]
   * @return [type]       [description]
   */
  public function merge( $data ){
    foreach( $data AS $key => $val ){
      $this->set( $key, $val );
    }
  }

}