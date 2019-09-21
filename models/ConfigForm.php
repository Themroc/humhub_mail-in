<?php

namespace themroc\humhub\modules\mail_in\models;

use Yii;
use yii\base\Model;
use themroc\humhub\modules\mail_in\Module;

/**
 * ConfigForm defines the configurable fields.
 */
class ConfigForm extends \themroc\humhub\modules\modhelper\models\AdminForm
{
#	public $method = Module::ACCESS_METHOD_IMAP;
    public $source = '';
#	public $maildir = '';
    public $address = '';
    public $showaddr = 0;
    public $sortorder = -10;

	protected $vars= [
/*
		'method'=> [
			'label'=> 'Access method',
			'rules'=> 'number',
			'form'=> [
				'type'=> 'radio',
				'params'=> Module::ACCESS_METHODS,
			],
		],
     */
		'source'=> [
			'label'=> 'IMAP account',
			'hints'=> 'E.g. user:password@example.com',
/*
			'form'=> [
				'depends'=> ['method'=> Module::ACCESS_METHOD_IMAP],
			],
     */
		],
/*
		'maildir'=> [
			'label'=> 'Maildir location',
			'hints'=> 'E.g. /home/spaces/welcome/Maildir',
			'form'=> [
				'depends'=> ['method'=> Module::ACCESS_METHOD_MAILDIR],
			],
		],
     */
		'address'=> [
			'label'=> 'Email address',
		],
		'showaddr'=> [
			'label'=> 'Enable sidebar widget',
			'rules'=> 'number',
			'form'=> [
				'type'=> 'checkbox',
			],
		],
		'sortorder'=> [
			'label'=> 'Widget sort order',
			'rules'=> 'number',
			'form'=> [
				'depends'=> ['showaddr'=> 1 ],
			],
		],
        ];
    }
