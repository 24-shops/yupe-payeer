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
 * @var $this PayeerBackendController - Управление кашельком по API с Payeer.com
 * @var $model Payeer
 */

$this->pageTitle = 'Payeer - Баланс кошелька';

$this->breadcrumbs = [ $this->pageTitle ];

$this->menu = [
  ['icon' => 'fa fa-fw fa-list-alt', 'label' => 'Список задач', 'url' => ['/payeer/PayeerBackend/index']],
  //['icon' => 'fa fa-fw fa-plus-square', 'label' => 'Создать задачу', 'url' => ['/todo/todoBackend/create']],
];

?>
<div class="page-header">
  <h1><?= $this->pageTitle ?></h1>
</div>
