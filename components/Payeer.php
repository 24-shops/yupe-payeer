<?php
/**
 * Payeer payment module
 * 
 * @package  yupe.modules.payeer
 * @author   24-shops <24-shops@mail.ru>
 * @license  BSD http://ru.wikipedia.org/wiki/%D0%9B%D0%B8%D1%86%D0%B5%D0%BD%D0%B7%D0%B8%D1%8F_BSD
 * @link     https://github.com/24-shops/yupe-payeer
 **/

class Payeer
{
  private $m_shop;
  private $m_key;
  private $payment;
  private $action;
  private $order;
  public function __construct(Payment $payment)
  {
    $settings=$payment->getPaymentSystemSettings();

    $this->m_shop=$settings['m_shop'];
    $this->m_key=$settings['m_key'];
    
    $this->payment=$payment;
    $this->action=(isset($_GET["my_action"])?$_GET["my_action"]:"");
    $this->_handler($this->action);
  }
  public function getSessionId(Order $order)
  {
    $this->order=$order;
  }
  public function buildUrl()
  {
    $m_shop=$this->m_shop;
    $m_orderid=$this->order->id;
    $m_amount=$this->order->getTotalPriceWithDelivery()*100;
    $m_curr='RUB';
    $m_desc=base64_encode(
      Yii::t(
        'PayeerModule.payeer',
        'Payment order #{n} on "{site}" website',
        [
          '{n}'=>$this->order->id,
          '{site}'=>Yii::app()->getModule('yupe')->siteName,
        ]
      )
    );
    $m_key=$this->m_key;

    $arHash = array(
      $m_shop,
      $m_orderid,
      $m_amount,
      $m_curr,
      $m_desc,
      $m_key
    );
    $m_sign = strtoupper(hash('sha256',implode(':',$arHash)));
    
    return $this->getUrl(compact("m_shop","m_orderid","m_amount","m_curr","m_desc","m_sign"));
  }
  public function getUrl($params)
  {
    $params=http_build_query($params);
    return 'https://payeer.com/merchant/?'.$params;
  }
  private function _handler($action)
  {
    switch($action)
    {
      case "success":
      $this->order->pay($this->payment,Order::PAID_STATUS_PAID);
      break;
      
      case "fail":
      $this->order->pay($this->payment,Order::PAID_STATUS_NOT_PAID);
      break;
      
      // Возращение номер платежа платежной системе, предварительно сверить с помощью цифровой подписью.
      case "handler":
      {
        if(!in_array($_SERVER['REMOTE_ADDR'],array('185.71.65.92','185.71.65.189'))) die("Not access");
        if(isset($_POST['m_operation_id']) && isset($_POST['m_sign']))
        {
          $m_key=$this->m_key;
          $arHash=array(
            $_POST['m_operation_id'],
            $_POST['m_operation_ps'],
            $_POST['m_operation_date'],
            $_POST['m_operation_pay_date'],
            $_POST['m_shop'],
            $_POST['m_orderid'],
            $_POST['m_amount'],
            $_POST['m_curr'],
            $_POST['m_desc'],
            $_POST['m_status'],
            $m_key
          );
          $sign_hash=strtoupper(hash('sha256',implode(':',$arHash)));
          if($_POST['m_sign']==$sign_hash && $_POST['m_status']=='success')
          {
            echo $_POST['m_orderid'].'|success';
            exit;
          }
          echo $_POST['m_orderid'].'|error';
          exit;
        }
      }
      break;
    }
  }
}

?>