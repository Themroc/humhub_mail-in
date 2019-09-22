<?php

namespace themroc\humhub\modules\mail_in;

use Yii;
use yii\helpers\Url;
use humhub\components\console\Application;
use themroc\humhub\modules\mail_in\widgets\Sidebar;

class Events
{
	public static function onConsoleApplicationInit($event)
	{
		$application= $event->sender;
		$application->controllerMap['mail_in'] = commands\ConsoleController::class;
	}

	/**
	 * Defines what to do if admin menu is initialized.
	 *
	 * @param $event
	 */
	public static function onAdminMenuInit($event)
	{
		if (Yii::$app->user->isGuest || empty($event) || empty($event->sender))
			return;

		$event->sender->addItem([
			'group'=> 'manage',
			'label'=> 'Mail in',
			'url'=> Url::to(['/mail_in/admin']),
			'icon'=> '<i class="fa fa-envelope-o"></i>',
			'isActive'=> (Yii::$app->controller->module && Yii::$app->controller->module->id == 'mail_in' && Yii::$app->controller->id == 'admin'),
			'sortOrder'=> 90000,
		]);
	}

	/**
	 * Defines what to do if space sidebar is initialized.
	 *
	 * @param $event
	 */
	public static function onSidebarInit($event)
	{
		if (Yii::$app->user->isGuest || empty($event) || empty($event->sender))
			return;

		$space = $event->sender->space;
		if (empty($space) || ! $space->isModuleEnabled('mail_in') || ! $space->getSetting('showaddr', 'mail_in'))
			return;

		$event->sender->addWidget(Sidebar::className(), [
			'address'=> $space->getSetting('address', 'mail_in'),
			'title'=> 'Space',
		], [
			'sortOrder'=> $space->getSetting('sortorder', 'mail_in'),
		]);

	}
}
