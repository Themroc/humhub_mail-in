<?php

namespace themroc\humhub\modules\mail_in\controllers;

use Yii;
use yii\web\HttpException;
use humhub\modules\user\models\User;
use humhub\modules\space\models\Space;
use humhub\modules\post\permissions\CreatePost;
use humhub\modules\content\components\ContentContainerController;
use themroc\humhub\modules\mail_in\models\ConfigForm;

class Mail_inController extends ContentContainerController
{
	const TYPE_USER= 1;
	const TYPE_SPACE= 2;

	private function getContainerType()
	{
		if ($this->contentContainer instanceof \humhub\modules\user\models\User) {
			return self::TYPE_USER;
		} else if ($this->contentContainer instanceof \humhub\modules\space\models\Space) {
			return self::TYPE_SPACE;
		}

		return null;
	}

	public function getSetting($var) {
		return $this->contentContainer->getSetting($var, 'mail_in');
	}

	public function setSetting($var, $value) {
		return $this->contentContainer->setSetting($var, $value, 'mail_in');
	}

	/**
	 * Space Configuration Action for Admins
	 */
	public function actionConfig()
	{
		if (Yii::$app->getModule('mod-helper')===null)
			return $this->render('@mail_in/views/admin/error', [
				'msg'=> 'Please install and activate the <a href="https://github.com/Themroc/humhub_mod-helper" target="_blank">Mod-Helper plugin</a>.',
			]);

		$model= new ConfigForm();
		foreach (array_keys($model->getVars()) as $name)
			$model->{$name}= $this->getSetting($name);

		if ($model->load(Yii::$app->request->post()) && $model->validate()) {
			foreach (array_keys($model->getVars()) as $name)
				$this->setSetting($name, $model->{$name});

			return $this->redirect($this->contentContainer->createUrl('/mail_in/mail_in/config'));
		}

		return $this->render('@mod-helper/views/form', [
			'model'=> $model,
		]);
	}
}
