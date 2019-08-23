<?php

use humhub\widgets\ActiveForm;
use humhub\libs\Html;
use themroc\humhub\modules\mail_in\Module;

echo '<div class="panel panel-default">'."\n";
echo '	<div class="panel-heading"><strong>Mail in</strong> ' . Yii::t('MailInModule.base', 'module configuration') . '</div>'."\n";
echo '	<div class="panel-body">'."\n";

$form = ActiveForm::begin(['id' => 'container-form']);

echo '	<div class="form-group">'."\n";

if ($have_maildir)
echo '		' . $form->field($model, 'method')->radioList(Module::getMethods(), ['itemOptions'=>['class'=>'mail-in_config_method']])."\n";
#echo '		' . $form->field($model, 'method')->dropDownList($model->getMethods())."\n";

echo '		' . $form->field($model, 'source', ['options'=>['style'=>'display:'.($model->method==Module::ACCESS_METHOD_IMAP ? "block" : "none")]])."\n";
echo '		' . $form->field($model, 'maildir', ['options'=>['style'=>'display:'.($model->method==Module::ACCESS_METHOD_MAILDIR ? "block" : "none")]])."\n";
echo '		' . $form->field($model, 'address')."\n";
echo '		' . $form->field($model, 'showaddr')->checkbox()."\n";
echo '		<div class="form-group" id="group-showaddr" style="margin-left:36px; display:'.($model->showaddr ? "block" : "none").'">'."\n";
echo '			' . $form->field($model, 'sortorder')."\n";
echo '		</div>'."\n";
echo '	</div>'."\n";

echo '	<div class="form-group">'."\n";
echo '		' . Html::saveButton()."\n";;
echo '		' . Html::a(Yii::t('base', 'Back'), '/admin/modules', ['class' => 'btn btn-default pull-right'])."\n";
echo '	</div>'."\n";

ActiveForm::end();

echo '	</div>'."\n";
echo '</div>'."\n";

echo '
<script>
$(".mail-in_config_method").on("change", function() {
	$(".field-configform-source").css("display", this.value=='.Module::ACCESS_METHOD_IMAP.' ? "block" : "none");
	$(".field-configform-maildir").css("display", this.value=='.Module::ACCESS_METHOD_MAILDIR.' ? "block" : "none");
});
$("#configform-showaddr").on("change", function() {
	$("#group-showaddr").css("display", this.checked ? "block" : "none");
});
</script>
';
