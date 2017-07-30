<!DOCTYPE html>
<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\Gallery\configuration\Constants;
$form = ActiveForm::begin([
    'id' => 'create-form',
    'options' => ['class' => 'form-horizontal', 'enctype' => 'multipart/form-data'],
]) ?>
<h1>Update Image</h1>
    <?= $form->field($model, 'id')->hiddenInput()->label(false) ?>
    <?= $form->field($model, 'name') ?>
    <?= $form->field($model, 'categoryId')->dropDownList($categories)->label("Category") ?>
    <?= $form->field($model, 'accessibilityStatus')->dropDownList([
        Constants::ALLOWED_TO_ALL => "Allowed to all", 
        Constants::ALLOWED_TO_AUTHORIZED_USERS => "Allowed to authorized", 
        Constants::ALLOWED_TO_ADMINS => "Allowed to admins"]) ?>
    <div class="form-group">
        <div class="col-lg-offset-1 col-lg-11">
            <?= Html::submitButton('Update Image', ['class' => 'btn btn-primary']) ?>
        </div>
    </div>
<?php ActiveForm::end() ?>