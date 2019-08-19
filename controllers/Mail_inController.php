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

    public $settings= [
        'source',
        'address',
        'showaddr',
        'sortorder',
    ];

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
        $form= new ConfigForm();
        foreach ($this->settings as $s)
            $form->{$s}= $this->getSetting($s);

        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            foreach ($this->settings as $s)
                $this->setSetting($s, $form->{$s});

            return $this->redirect($this->contentContainer->createUrl('/mail_in/mail_in/config'));
        }

        return $this->render('config', [ 'model' => $form, 'have_maildir' => 0 ]);
#        return $this->render('config', [ 'model' => $form ]);
    }

}
