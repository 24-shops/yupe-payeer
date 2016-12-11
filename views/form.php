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
 * @var $action Ссылка на платежную систему
 */
?>

<!-- [<?php /*var_dump($params)*/?>] -->

<?php /* CHtml::beginForm($action,'post',['target'=>'_blank']) */ ?>

<?= CHtml::beginForm($action,'post') ?>
<?= CHtml::hiddenField('m_shop',$m_shop) ?>
<?= CHtml::hiddenField('m_orderid',$m_orderid) ?>
<?= CHtml::hiddenField('m_amount',$m_amount) ?>
<?= CHtml::hiddenField('m_curr',$m_curr) ?>
<?= CHtml::hiddenField('m_desc',$m_desc) ?>
<?= CHtml::hiddenField('m_sign',$m_sign) ?>
<?= CHtml::endForm() ?>
