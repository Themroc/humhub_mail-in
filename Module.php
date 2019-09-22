<?php

namespace themroc\humhub\modules\mail_in;

use Yii;
use yii\helpers\Url;
use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\content\components\ContentContainerModule;
use humhub\modules\space\models\Space;
use humhub\modules\user\models\User;

class Module extends ContentContainerModule
{
	const ACCESS_METHOD_IMAP= 1;
	const ACCESS_METHOD_MAILDIR= 2;
	const ACCESS_METHODS= [
		self::ACCESS_METHOD_IMAP=> 'IMAP',
		self::ACCESS_METHOD_MAILDIR=> 'Maildir',
	];

	/**
	* @inheritdoc
	*/
	public function getConfigUrl()
	{
		return Url::to(['/mail_in/admin']);
	}

	/**
	* @inheritdoc
	*/
	public function getContentContainerTypes()
	{
		return [
			Space::class,
			User::class
		];
	}

	/**
	* @inheritdoc
	*/
	public function getContentContainerConfigUrl(ContentContainerActiveRecord $container)
	{
		return $container->createUrl('/mail_in/mail_in/config');
	}

	/**
	* @inheritdoc
	*/
	public function disable()
	{
		// Cleanup all module data, don't remove the parent::disable()!!!
		parent::disable();
	}

	/**
	* @inheritdoc
	*/
	public function disableContentContainer(ContentContainerActiveRecord $container)
	{
		parent::disableContentContainer($container);
	}

	/**
	* @inheritdoc
	*/
	public function getContentContainerName(ContentContainerActiveRecord $container)
	{
		return Yii::t('MailInModule.base', 'Mail in');
	}

	/**
	* @inheritdoc
	*/
	public function getContentContainerDescription(ContentContainerActiveRecord $container)
	{
		return Yii::t('MailInModule.base', 'Allow posting via Email.');
	}

	public function getMethods()
	{
		return self::ACCESS_METHODS;
	}
}
