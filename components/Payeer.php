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
    private $merchant_ips;

    private $m_shop;
    private $m_key;
    private $payment;
    private $action;
    private $order;

    public function __construct(Payment $payment)
    {
        $settings = $payment->getPaymentSystemSettings();

        $this->merchant_ips = !isset($settings['merchant_ips'])?false:$settings['merchant_ips'];
        if (!$this->merchant_ips)
        {
            $this->merchant_ips = ['185.71.65.92', '185.71.65.189', '149.202.17.210'];
        }
        else
        {
            if (strpos($this->merchant_ips, ',')>0)
            {
                $ips = explode(',', $this->merchant_ips);
                $tmp = [];
                foreach($ips as $ip) $tmp[] = trim($ip);
                $this->merchant_ips = $tmp;
            }
            else
            {
                if (empty(trim($this->merchant_ips)))
                {
                    $this->merchant_ips = ['185.71.65.92', '185.71.65.189', '149.202.17.210'];
                }
                else
                {
                    $this->merchant_ips = [trim($this->merchant_ips)];
                }
            }
        }

        $this->m_shop = $settings['m_shop'];
        $this->m_key = $settings['m_key'];

        $this->payment = $payment;
        $this->action = Yii::app()->getRequest()->getQuery('my_action', '');
        $this->_handler($this->action);
    }

    public function getSessionId(Order $order)
    {
        $this->order = $order;

        $data = [
            #'m_orderid' => $this->key,
        ];

        #$sessionData = $this->sendRequest

        #if (!isset($sessionData))

        #echo "<pre>";var_dump(['ORDER'=>$order,'SELF CLASS'=>$this]);die();
    }

    public function buildUrl()
    {
        $m_shop = $this->m_shop;
        $m_orderid = $this->order->id;
        $m_amount = $this->order->getTotalPriceWithDelivery()*100;
        $m_curr = 'RUB';
        $m_desc = base64_encode(
            Yii::t(
                'PayeerModule.payeer',
                'Payment order #{n} on "{site}" website',
                [
                    '{n}' => $this->order->id,
                    '{site}' => Yii::app()->getModule('yupe')->siteName,
                ]
            )
        );
        $m_key = $this->m_key;

        $arHash = array(
            $m_shop,
            $m_orderid,
            $m_amount,
            $m_curr,
            $m_desc,
            $m_key
        );
        $m_sign = strtoupper(hash('sha256', implode(':', $arHash)));

        return $this->getUrl(compact("m_shop", "m_orderid", "m_amount", "m_curr", "m_desc", "m_sign"));
    }

    public function getUrl($params)
    {
        $params=http_build_query($params);
        return 'https://payeer.com/merchant/?' . $params;
    }

    private function _handler($action)
    {
        switch($action)
        {
            case "success":
                $this->order->pay($this->payment, Order::PAID_STATUS_PAID);
                break;

            case "fail":
                $this->order->pay($this->payment, Order::PAID_STATUS_NOT_PAID);
                break;

            // Возращение номер платежа платежной системе, предварительно сверить с помощью цифровой подписью.
            case "handler":
            {
                if (!in_array($_SERVER['REMOTE_ADDR'], $this->merchant_ips)) die("Not access");
                if (isset($_POST['m_operation_id']) && isset($_POST['m_sign']))
                {
                    $m_key = $this->m_key;
                    $arHash = [];
                    $arHash[] = Yii::app()->getRequest()->getPost('m_operation_id');
                    $arHash[] = Yii::app()->getRequest()->getPost('m_operation_ps');
                    $arHash[] = Yii::app()->getRequest()->getPost('m_operation_date');
                    $arHash[] = Yii::app()->getRequest()->getPost('m_operation_pay_date');
                    $arHash[] = Yii::app()->getRequest()->getPost('m_shop');
                    $arHash[] = Yii::app()->getRequest()->getPost('m_orderid');
                    $arHash[] = Yii::app()->getRequest()->getPost('m_amount');
                    $arHash[] = Yii::app()->getRequest()->getPost('m_curr');
                    $arHash[] = Yii::app()->getRequest()->getPost('m_desc');
                    $arHash[] = Yii::app()->getRequest()->getPost('m_status');
                    $arHash[] = $m_key;
                    $sign_hash = strtoupper(hash('sha256', implode(':', $arHash)));
                    if (
                        Yii::app()->getRequest()->getPost('m_sign') == $sign_hash &&
                        Yii::app()->getRequest()->getPost('m_status') == 'success'
                    )
                    {
                        echo Yii::app()->getRequest()->getPost('m_orderid') . '|success';
                        exit;
                    }
                    echo Yii::app()->getRequest()->getPost('m_orderid') . '|error';
                    exit;
                }
            } // case "handler"
            break;
        }
    }
}
