<?php
/**
 * Payeer payment module
 * 
 * @package  yupe.modules.payeer
 * @author   24-shops <24-shops@mail.ru>
 * @license  BSD http://ru.wikipedia.org/wiki/%D0%9B%D0%B8%D1%86%D0%B5%D0%BD%D0%B7%D0%B8%D1%8F_BSD
 * @link     https://github.com/24-shops/yupe-payeer
 **/

/**
 * Class Payeer
 *
 * @property integer $id
 * @property string $description
 * @property integer $status
 * @property integer $sort
 */
class Payeer extends \yupe\models\YModel
{
  const STATUS_DRAFT = 0;
  const STATUS_PUBLIC = 1;
  const STATUS_PERSONAL = 2;
  const STATUS_PRIVATE = 3;
  
  /**
   * @return string
   */
  public function tableName()
  {
    return '{{payeer}}';
  }

  /**
   * @return array
   */
  public function rules()
  {
    return [
      [
        'userid, description','required'
      ],
      [
        'userid, sort','numerical','integerOnly' => true
      ],
      [
        'description','length','max' => 255
      ],
      [
        'status, sort','numerical','integerOnly' => true
      ],
      [
        'description, sort','safe','on' => 'search'
      ],
    ];
  }

  /**
   * @return array
   */
  public function attributeLabels()
  {
    return [
      'userid' => 'Владелец',
      'description' => 'Описание',
      'status' => 'Статус',
      'sort' => 'Сортировка',
    ];
  }

  protected function beforeSave()
  {
    if($this->isNewRecord)
    {
      $this->sort = Yii::app()->db->createCommand()->select('MAX(sort) + 1')->from($this->tableName())->queryScalar();
    }

    return parent::beforeSave();
  }

  /**
   * @return CActiveDataProvider
   */
  public function search()
  {
    $criteria = new CDbCriteria;

    $criteria->compare('description',$this->description,true);
    $criteria->compare('status',$this->status);

    return new CActiveDataProvider(get_class($this),['criteria' => $criteria,'sort' => ['defaultOrder' => 'sort']]);
  }

  /**
   * Count all unfinished tasks
   *
   * @return int
   */
  public function countUnfinished()
  {
    return self::model()->count('status != :status',[':status' => TodoStatusHelper::STATUS_DONE]);
  }
}
