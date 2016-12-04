<?php

class PayeerStatusHelper
{
  const STATUS_FAIL=-1;
  const STATUS_CANCELLED=0;
  const STATUS_DEFAULT=1;
  const STATUS_URGENT=2;
  const STATUS_DONE=3;
  
  /**
   * Return a list of statuses
   * 
   * @return array
   */
  public static function getList()
  {
    return [
      self::STATUS_CANCELLED=>'Отменена',
      self::STATUS_DEFAULT=>'Обычная',
      self::STATUS_URGENT=>'Срочная',
      self::STATUS_DONE=>'Выполнена',
    ];
  }
  
  /**
   * Get css class names list
   * 
   * @return array
   */
  public static function getStylesList()
  {
    return [
      self::STATUS_CANCELLED=>['class'=>'label-danger'],
      self::STATUS_DEFAULT=>['class'=>'label-default'],
      self::STATUS_URGENT=>['class'=>'label-primary'],
      self::STATUS_DONE=>['class'=>'label-success'],
    ];
  }
  
  /**
   * Get status label by id
   * 
   * @param int $id
   * @return string
   */
  public static function getLabel($id)
  {
    $list=self::getList();
    
    return $list[$id];
  }
}
