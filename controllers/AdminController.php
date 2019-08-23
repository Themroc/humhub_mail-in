<?php

namespace themroc\humhub\modules\mail_in\controllers;

use Yii;
use humhub\modules\admin\components\Controller;
use themroc\humhub\modules\mail_in\models\AdminForm;

class AdminController extends Controller
{

    /**
     * Render admin only page
     *
     * @return string
     */
    public function actionIndex()
    {
        $model = new AdminForm();
        $model->loadSettings();

        if ($model->load(Yii::$app->request->post()) && $model->save())
            $this->view->saved();

        return $this->render('index', ['model' => $model]);
    }

}
