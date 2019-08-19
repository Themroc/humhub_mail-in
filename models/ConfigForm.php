<?php

namespace themroc\humhub\modules\mail_in\models;

use Yii;
use \yii\base\Model;
use themroc\humhub\modules\mail_in\Module;

/**
 * ContainerForm defines the configurable fields.
 *
 * @package humhub.modules.linklist.forms
 * @author Sebastian Stumpf
 */
class ConfigForm extends Model
{
    public $method = Module::ACCESS_METHOD_IMAP;
    public $source = '';
    public $maildir = '';
    public $address = '';
    public $showaddr = 0;
    public $sortorder = -10;

    private $mod;

    public function init()
    {
        $this->mod = Yii::$app->getModule('mail_in');
#        $this->method = $this->mod->settings->get('accessMethod');
    }

    /**
     * Declares the validation rules.
     */
    public function rules()
    {
        return [
            [ ['source', 'maildir', 'address'],  'string'],
            [ ['showaddr', 'sortorder'],  'number'],
        ];
    }

    /**
     * Declares customized attribute labels.
     * If not declared here, an attribute would have a label that is
     * the same as its name with the first letter in upper case.
     */
    public function attributeLabels()
    {
        return [
            'method' => Yii::t('MailInModule.base', 'Access method'),
            'source' => Yii::t('MailInModule.base', 'IMAP account'),
            'maildir' => Yii::t('MailInModule.base', 'Maildir location'),
            'address' => Yii::t('MailInModule.base', 'Email address'),
            'showaddr' => Yii::t('MailInModule.base', 'Enable sidebar widget'),
            'sortorder' => Yii::t('MailInModule.base', 'Widget sort order'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeHints()
    {
        return [
            'source' => Yii::t('MailInModule.base', 'E.g. user:password@example.com'),
            'maildir' => Yii::t('MailInModule.base', 'E.g. /home/spaces/welcome/Maildir'),
        ];
    }

}
