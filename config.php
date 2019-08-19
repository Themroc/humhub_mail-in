<?php

use humhub\components\console\Application;
use humhub\modules\space\widgets\Sidebar;
#use humhub\modules\user\widgets\ProfileSidebar;

use themroc\humhub\modules\mail_in\Events;

return [
    'id' => 'mail_in',
    'class' => 'themroc\humhub\modules\mail_in\Module',
    'namespace' => 'themroc\humhub\modules\mail_in',
    'events' => [
        [ Application::class, Application::EVENT_ON_INIT, [Events::class, 'onConsoleApplicationInit'] ],
        [ Sidebar::className(), Sidebar::EVENT_INIT, [ Events::class, 'onSidebarInit' ] ],
#        [ ProfileSidebar::className(), ProfileSidebar::EVENT_INIT, [ Events::class, 'onProfileSidebarInit' ] ],
    ],
];
