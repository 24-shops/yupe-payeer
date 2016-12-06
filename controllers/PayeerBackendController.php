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
use yupe\components\controllers\BackController;

/**
 * Class PayeerBackendController
 */
class PayeerBackendController extends BackController
{
    public function filters()
    {
        return CMap::mergeArray(
            parent::filters(),
            [
                'postOnly + delete',
            ]
        );
    }

    public function actions()
    {
        return [
            'inline' => [
                'class' => 'yupe\components\actions\YInLineEditAction',
                'model' => 'Payeer',
                'validAttributes' => [
                    'status',
                ],
            ],
            'sortable' => [
                'class' => 'yupe\components\actions\SortAction',
                'model' => 'Payeer',
                'attribute' => 'sort',
            ],
        ];
    }

    public function actionIndex()
    {
        $this->render('index');
    }

    public function actionCreate()
    {
        $model = new Payeer();

        if ($data = Yii::app()->getRequest()->getPost('Payeer')) {

            $model->setAttributes($data);

            if ($model->save()) {
                Yii::app()->user->setFlash(YFlashMessages::SUCCESS_MESSAGE, 'Задача успешно добавлена');

                $this->redirect((array)Yii::app()->getRequest()->getPost('submit-type', ['create']));
            }
        }

        $this->render('create', ['model' => $model]);
    }

    public function actionUpdate($id)
    {
        $model = $this->loadModel($id);

        if ($data = Yii::app()->getRequest()->getPost('Payeer')) {

            $model->setAttributes($data);

            if ($model->update()) {
                Yii::app()->user->setFlash(YFlashMessages::SUCCESS_MESSAGE, 'Задача успешно обновлена');

                $submitType = Yii::app()->getRequest()->getPost('submit-type');

                if (isset($submitType)) {
                    $this->redirect([$submitType]);
                } else {
                    $this->redirect(['update', 'id' => $model->id]);
                }
            }
        }

        $this->render('update', ['model' => $model]);
    }

    public function actionDelete($id)
    {
        if ($this->loadModel($id)->delete()) {
            Yii::app()->user->setFlash(YFlashMessages::SUCCESS_MESSAGE, 'Задача успешно удалена');

            if (!Yii::app()->getRequest()->getParam('ajax')) {
                $this->redirect((array)Yii::app()->getRequest()->getPost('returnUrl', 'index'));
            }
        }
    }

    /**
     * @param $id
     * @return Payeer
     * @throws CHttpException
     */
    private function loadModel($id)
    {
        $model = Payeer::model()->findByPk($id);

        if ($model === null) {
            throw new CHttpException(404, 'Запрошенная страница не найдена.');
        }

        return $model;
    }
}
