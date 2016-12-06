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
 * Payeer Form - Кнопка обработки платежа - работает
 *
 * @var string $action Form action url
 * @var string $sessionId
 */
?>

<?= CHtml::beginForm($action, "get", ["_target" => "blank"]) ?>
<?= CHtml::hiddenField('session_id', $sessionId) ?>
<?= CHtml::submitButton(Yii::t('PayeerModule.payeer', 'Pay')) ?>
<?= CHtml::endForm() ?>
