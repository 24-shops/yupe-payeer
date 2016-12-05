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
 * Payeer payment module
 * 
 * @package  yupe.modules.payeer
 * @author   24-shops <24-shops@mail.ru>
 * @license  BSD http://ru.wikipedia.org/wiki/%D0%9B%D0%B8%D1%86%D0%B5%D0%BD%D0%B7%D0%B8%D1%8F_BSD
 * @link     https://github.com/24-shops/yupe-payeer
 **/

use yupe\components\WebModule;

/**
 * Class PayeerModule
 */
class PayeerModule extends WebModule
{
  /**
   * 
   */
  const VERSION='0.6';

  /**
   * @return array
   */
  public function getDependencies()
  {
    return ['payment'];
  }

  /**
   * @return array
   */
  public function getNavigation()
  {
    return [
      [
        'icon'  => 'fa fa-fw fa-list-alt',
        'label' => 'Баланс кошелька',
        'url'   => [
          '/payeer/payeerBackend/index'
        ]
      ],
    ];
  }

  /**
   * @return bool
   */
  public function getAdminPageLink()
  {
    return '/payeer/payeerBackend/index';
  }

  /**
   * @return bool
   */
  /*public function getIsShowInAdminMenu()
  {
    return true;
  }*/

  /**
   * @return string
   */
  public function getVersion()
  {
    return self::VERSION;
  }

  /**
   * @return array
   */
  public function getEditableParams()
  {
    return [];
  }

  /**
   * @return string
   */
  public function getCategory()
  {
    return Yii::t('PayeerModule.payeer','Store');
  }

  /**
   * @return string
   */
  public function getName()
  {
    return Yii::t('PayeerModule.payeer','Payeer');
  }

  /**
   * @return string
   */
  public function getDescription()
  {
    return Yii::t('PayeerModule.payeer','Payeer payment module');
  }

  /**
   * @return string
   */
  public function getAuthor()
  {
    return Yii::t('PayeerModule.payeer','24-shops');
  }

  /**
   * @return string
   */
  public function getAuthorEmail()
  {
    return Yii::t('PayeerModule.payeer','24-shops@mail.ru');
  }

  /**
   * @return string
   */
  public function getUrl()
  {
    return 'https://github.com/24-shops/yupe-payeer';
  }

  /**
   * @return string
   */
  public function getIcon()
  {
    return 'fa fa-rub';
  }

  public function init()
  {
    parent::init();

    $this->setImport(
      [
        'application.modules.payeer.models.*',
        'application.modules.payeer.components.widgets.*',
      ]
    );
  }
}

?>