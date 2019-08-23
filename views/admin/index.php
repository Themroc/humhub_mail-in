<?php

use humhub\widgets\ActiveForm;
use humhub\libs\Html;

echo '<div class="panel panel-default">'."\n";
echo '	<div class="panel-heading"><strong>Mail in</strong> ' . Yii::t('MailInModule.base', 'module configuration') . '</div>'."\n";
echo '	<div class="panel-body">'."\n";

$form = ActiveForm::begin(['id' => 'configure-form']);

echo '		<div class="form-group">'."\n";
echo '			' . $form->field($model, 'altEmail')->dropDownList($model->getAltEmails()) . "\n";
echo '		</div>'."\n";

echo '		<div class="form-group">'."\n";
echo '			' . Html::saveButton();
echo '			' . Html::a(Yii::t('base', 'Back'), '/admin/modules', ['class' => 'btn btn-default pull-right']);
echo '		</div>'."\n";

ActiveForm::end();

echo '	</div>'."\n";
echo '</div>'."\n";
