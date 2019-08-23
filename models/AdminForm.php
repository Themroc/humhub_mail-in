<?php

namespace themroc\humhub\modules\mail_in\models;

use Yii;
use humhub\modules\user\models\ProfileFieldCategory;
use themroc\humhub\modules\mail_in\Module;

/**
 * AdminForm defines the configurable fields.
 */
class AdminForm extends \yii\base\Model
{
    public $altEmail = '';

    private $mod;

    public function init()
    {
        $this->mod = Yii::$app->getModule('mail_in');
        $this->loadSettings();
    }

    /**
     * Declares the validation rules.
     */
    public function rules()
    {
        return [
            ['altEmail', 'string'],
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
            'altEmail' => Yii::t('MailInModule.base', 'Profile attribute to supply alternative email addresses'),
        ];
    }

    public function loadSettings()
    {
        foreach (array_keys($this->attributes) as $name)
            $this->{$name} = $this->mod->settings->get($name);

        return true;
    }


    public function save()
    {
        foreach (array_keys($this->attributes) as $name)
            $this->mod->settings->set($name, trim($this->{$name}));

        return $this->loadSettings();
    }

    public function getAltEmails()
    {
        $fields= [];
        foreach (ProfileFieldCategory::find()->orderBy('sort_order')->all() as $category)
            foreach ($category->fields as $field)
                $fields[$field->internal_name]= $category->title.' / '.$field->title;

        return $fields;
    }

}
