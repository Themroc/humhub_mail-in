<?php

namespace themroc\humhub\modules\mail_in\widgets;

use Yii;
use yii\helpers\Url;
use humhub\libs\Html;
use humhub\components\Widget;

/**
 * Show Space email address on sidebar
 */
class Sidebar extends Widget
{
    public $address;
    public $title;

    /**
     * @inheritdoc
     */
    public function run()
    {
        return $this->render('Sidebar', [
            'title' => $this->title,
            'address' => $this->address,
        ]);
    }
}
