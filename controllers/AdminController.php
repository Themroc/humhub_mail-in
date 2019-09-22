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
		if (Yii::$app->getModule('mod-helper')===null)
			return $this->render('error', [
				'msg'=> 'Please install and activate the <a href="https://github.com/Themroc/humhub_mod-helper" target="_blank">Mod-Helper plugin</a>.',
			]);

		$model= new AdminForm();
		$model->loadSettings();

		if ($model->load(Yii::$app->request->post()) && $model->save())
			$this->view->saved();

		return $this->render('@mod-helper/views/form', [
			'model'=> $model
		]);
	}
}
