<?php
/**
 * Payeer payment module
 * 
 * @package  yupe.modules.payeer
 * @author   24-shops <24-shops@mail.ru>
 * @license  BSD http://ru.wikipedia.org/wiki/%D0%9B%D0%B8%D1%86%D0%B5%D0%BD%D0%B7%D0%B8%D1%8F_BSD
 * @link     https://github.com/24-shops/yupe-payeer
 **/

use yupe\widgets\YFlashMessages;

Yii::import('application.modules.payeer.PayeerModule');
Yii::import('application.modules.payeer.components.Payeer');

class PayeerPaymentSystem extends PaymentSystem
{
    public function renderCheckoutForm(Payment $payment,Order $order, $return = false)
    {
        $payeer = new Payeer($payment);
        $sessionId = $payeer->getSessionId($order);

        if(!$sessionId)
        {
            Yii::app()->getUser()->setFlash(
                YFlashMessages::ERROR_MESSAGE,
                Yii::t('PayeerModule.payeer', 'Payment by "{name}" is impossible', ['{name}' => $payment->name])
            );

            return false;
        }

        $action = $payeer->buildUrl();
        return Yii::app()->getController()->renderPartial(
            'application.modules.payeer.views.form',
            [
                //'action' => $payeer->getUrl('Pay'),
                //'action' => 'https://payeer.com/api/payment/',
                'action' => $action,
                'sessionId' => $sessionId
            ],
            $return
        );
    }

    public function processCheckout(Payment $payment,CHttpRequest $request)
    {
        $payeer = new Payeer($payment);
        $order = Order::model()->findByUrl($payeer->getOrderIdFromHash($request));

        if($order === null)
        {
            Yii::log(Yii::t('PayeerModule.payeer', 'The order doesn\'t exist.'), CLogger::LEVEL_ERROR);

            return false;
        }

        if($order->isPaid())
        {
            Yii::log(
                Yii::t('PayeerModule.payeer', 'The order #{n} is already payed.', $order->getPrimaryKey()),
                CLogger::LEVEL_ERROR
            );

            return $order;
        }

        if($payeer->getPaymentStatus($request) === 'Charged' && $order->pay($payment))
        {
            Yii::log(
                Yii::t('PayeerModule.payler', 'The order #{n} has been payed successfully.', $order->getPrimaryKey()),
                CLogger::LEVEL_INFO
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
