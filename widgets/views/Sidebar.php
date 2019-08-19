<?php

use yii\helpers\Url;
use humhub\libs\Html;

echo '<div id="mail_in-widget" class="panel">'."\n";

echo '	<div class="panel-heading">'."\n";
echo '		'.Yii::t('MailInModule.base', '<strong>'.$title.'</strong>-Email address')."\n";
echo '	</div>'."\n";

echo '	<div class="panel-body" style="padding:0px 5px 5px 10px">'."\n";
echo '		<a href="mailto:'.$address.'">'.$address.'</a>'."\n";
echo '	</div>'."\n";

echo '</div>'."\n";
