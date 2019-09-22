<?php

use humhub\components\console\Application;
use humhub\modules\admin\widgets\AdminMenu;
use humhub\modules\space\widgets\Sidebar;
use themroc\humhub\modules\mail_in\Events;

return [
	'id'=> 'mail_in',
	'class'=> 'themroc\humhub\modules\mail_in\Module',
	'namespace'=> 'themroc\humhub\modules\mail_in',
	'events'=> [
		[ Application::class, Application::EVENT_ON_INIT, [Events::class, 'onConsoleApplicationInit'] ],
		[ AdminMenu::class, AdminMenu::EVENT_INIT, [Events::class, 'onAdminMenuInit'] ],
		[ Sidebar::class, Sidebar::EVENT_INIT, [Events::class, 'onSidebarInit'] ],
	],
];
