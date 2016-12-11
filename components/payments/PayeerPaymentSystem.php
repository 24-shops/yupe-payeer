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
 * Class PayeerPaymentSystem
 * @link
 */

use yupe\widgets\YFlashMessages;

Yii::import('application.modules.payeer.PayeerModule');
/**
 * Class PayeerPaymentSystem
 */
class PayeerPaymentSystem extends PaymentSystem
{
    /**
     * @param Payment $payment
     * @param Order $order
     * @param bool|false $return
     * @return mixed|string
     */
    public function renderCheckoutForm(Payment $payment,Order $order,$return = false)
    {
        $action = 'https://payeer.com/merchant/';
        $settings = $payment->getPaymentSystemSettings();
        /*$sbank = new Sberbank($payment);
        $action = $sbank->getFormUrl($order);
        
        if (!$action)
        {
            Yii::app()->getUser()->setFlash(
                YFlashMessages::ERROR_MESSAGE,
                Yii::t('SberbankModule.sberbank', 'Payment by "{name}" is impossible', ['{name}' => $payment->name])
            );
            
            return false;
        }*/
        
        $m_shop = $settings["m_shop"];
        $m_orderid = $order->id;
        $m_amount = number_format($order->total_price + $order->delivery_price, 2, '.', '');
        $m_curr = 'RUB';
        $m_desc = base64_encode('Оплата за товар(ы), номер выставленного счета ' . $order->id);
        #$m_key = '123';
        $m_key = $settings["m_key"];
        
        $arHash = array(
            $m_shop,
            $m_orderid,
            $m_amount,
            $m_curr,
            $m_desc
        );
        
        /*$arParams = [
            //'success_url' => 'http://shop.24-shops.ru/new_success_url',
            //'fail_url' => 'http://shop.24-shops.ru/new_fail_url',
            //'status_url' => 'http://shop.24-shops.ru/new_status_url',
            //'reference' => [
                //'var1' => '1',
                //'var2' => '2',
                //'var3' => '3',
                //'var4' => '4',
                //'var5' => '5',
            //],
            "$csrfTokenName" => $csrfTokenValue,
        ];
        
        $key = md5($settings["m_key_extra_options"].$m_orderid);
        $m_params = urlencode(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, json_encode($arParams), MCRYPT_MODE_ECB)));
        $arHash[] = $m_params;*/
        
        $arHash[] = $m_key;
        $m_sign = strtoupper(hash('sha256', implode(':', $arHash)));
        
        return Yii::app()->getController()->renderPartial(
            'application.modules.payeer.views.form',
            [
                'action' => $action,
                #'params' => [$csrfTokenName,$csrfTokenValue],
                'm_shop' => $m_shop,
                'm_orderid' => $m_orderid,
                'm_amount' => $m_amount,
                'm_curr' => $m_curr,
                'm_desc' => $m_desc,
                'm_sign' => $m_sign,
                #'order' => $order,
            ],
            $return
        );
    }
    
    /**
     * @param Payment $payment
     * @param CHttpRequest $request
     * @return bool
     */
    public function processCheckout(Payment $payment,CHttpRequest $request)
    {
        $m_orderid = $request->getParam('m_orderid');
        $order = Order::model()->findByNumber(['id' => $m_orderid]);
        
        // Сообщить платежной системе что такого ордера не существует, и логировать системную ошибку
        if ($order === null) {
            echo $m_orderid . "|error";
            Yii::log(
                Yii::t('PayeerModule.payeer', 'The order doesn\'t exist.'),
                CLogger::LEVEL_ERROR
            );
            return false;
        }
        
        // Сообщать платежной системе по поводу этого ордера ранее оплаченым со событием ошибки, и логировать ошибку.
        if ($order->isPaid())
        {
            echo $m_orderid . "|error";
            Yii::log(
                Yii::t('PayeerModule.payeer', 'The order #{n} is already payed.', $order->getPrimaryKey()),
                CLogger::LEVEL_ERROR
            );
            
            return $order;
        }
        
        $settings = $payment->getPaymentSystemSettings();
        
        $action = $request->getParam('my_action');
        
        $paid_status = Order::PAID_STATUS_NOT_PAID;
        switch($action)
        {
            case "success":
                $paid_status = Order::PAID_STATUS_PAID;
                break;
            case "fail":
                break;
            case "handler":
            {
                $merchant_ips = $settings['merchant_ips'];
                if ($merchant_ips)
                {
                    $merchant_ips = explode(",", $merchant_ips);
                    foreach($merchant_ips as &$ip)
                        $ip = trim($ip);
                }
                else {
                    $merchant_ips = [];
                }
                if (!in_array($_SERVER['REMOTE_ADDR'], $merchant_ips)) return;
                // Проверка сигнала при передачи на наш обработчик,
                // и надо ответить платежной системе о том как произошла операция
                if (
                    Yii::app()->getRequest()->getPost('m_operation_id') &&
                    Yii::app()->getRequest()->getPost('m_sign')
                )
                {
                    $m_key = $settings['m_key'];
                    
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
                    
                    $m_params = Yii::app()->getRequest()->getPost('m_params');
                    if ($m_params != null)
                    {
                        $arHash[] = $m_params;
                    }
                    
                    $arHash[] = $m_key;
                    
                    $sign_hash = strtoupper(hash('sha256', implode(':', $arHash)));
                    
                    if (
                        Yii::app()->getRequest()->getPost('m_sign') == $sign_hash &&
                        Yii::app()->getRequest()->getPost('m_status') == 'success'
                    )
                    {
                        echo $m_orderid . '|success';
                        $order->pay($payment, Order::PAID_STATUS_PAID);
                        exit;
                    }
                    
                    echo $m_orderid . '|error';
                    $order->pay($payment, Order::PAID_STATUS_NOT_PAID);
                    exit;
                }
                break;
            } // case "handler"
        }
        
        if ($order->pay($payment, $paid_status))
        {
            Yii::log(
                Yii::t('PayeerModule.payeer', 'The order #{n} has been payed successfully.', $order->getPrimaryKey()),
                CLogger::LEVEL_INFO
            );
            Yii::app()->getUser()->setFlash(
                YFlashMessages::SUCCESS_MESSAGE,
                Yii::t('PayeerModule.payeer', 'The order #{n} has been payed successfully.', $order->getPrimaryKey())
            );
        }
        else
        {
            Yii::app()->getUser()->setFlash(
                YFlashMessages::ERROR_MESSAGE,
                Yii::t('PayeerModule.payeer', 'Attempt to pay failed')
            );
            Yii::log(
                Yii::t(
                    'PayeerModule.payeer',
                    'An error occurred when you pay the order #{n}.',
                    $order->getPrimaryKey()
                ),
                CLogger::LEVEL_ERROR
            );
        }
        
        return $order;
    }
}
