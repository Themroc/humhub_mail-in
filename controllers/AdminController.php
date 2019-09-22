<?php

namespace themroc\humhub\modules\mail_in\controllers;

use Yii;
use humhub\modules\admin\components\Controller;
use themroc\humhub\modules\mail_in\models\AdminForm;

class AdminController extends Controller
{
	public $adminOnly= true;

	/**
	 * Render admin only page
	 *
	 * @return string
	 */
	public function actionIndex()
	{
		if (Yii::$app->getModule('mod-helper')===null)
			return $this->render('error', []);

		$model= new AdminForm();
		if ($model->load(Yii::$app->request->post()) && $model->save())
			$this->view->saved();

		return $this->render('@mod-helper/views/form', [
			'model'=> $model
		]);
	}
}
