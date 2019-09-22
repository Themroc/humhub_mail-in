<?php

namespace themroc\humhub\modules\mail_in\models;

use Yii;
use humhub\modules\user\models\ProfileFieldCategory;
use themroc\humhub\modules\mail_in\Module;

/**
 * AdminForm defines the configurable fields.
 */
class AdminForm extends \themroc\humhub\modules\modhelper\models\AdminForm
{
	public $altEmail= Module::ACCESS_METHOD_MAILDIR;

	protected $vars= [
		'altEmail'=> [
			'label'=> 'Profile attribute to supply alternative email addresses',
			'form'=> [
				'type'=> 'dropdown',
				'params'=> [self::class, 'getAltEmails'],
			],
		],
	];

	public function getAltEmails()
	{
		$fields= [];
		foreach (ProfileFieldCategory::find()->orderBy('sort_order')->all() as $category)
			foreach ($category->fields as $field)
				$fields[$field->internal_name]= $category->title.' / '.$field->title;

		return $fields;
	}
}
