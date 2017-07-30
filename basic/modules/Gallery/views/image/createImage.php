<!DOCTYPE html>
<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\Gallery\configuration\Constants;
$form = ActiveForm::begin([
    'id' => 'create-form',
    'options' => ['class' => 'form-horizontal', 'enctype' => 'multipart/form-data'],
]) ?>
<h1>Upload Image</h1>
    <?= $form->field($model, 'imageFile')->fileInput()->label("Image") ?>
    <?= $form->field($model, 'name') ?>
    <?= $form->field($model, 'categoryId')->dropDownList($categories)->label("Category") ?>
    <?= $form->field($model, 'accessibilityStatus')->dropDownList([
        Constants::ALLOWED_TO_ALL => "Allowed to all", 
        Constants::ALLOWED_TO_AUTHORIZED_USERS => "Allowed to authorized", 
        Constants::ALLOWED_TO_ADMINS => "Allowed to admins"]) ?>
    <?= $form->field($model, 'watermarkPosition')->dropDownList([
        Constants::WM_TOP_LEFT => "top-left", 
        Constants::WM_TOP_RIGHT => "top-right", 
        Constants::WM_BOTTOM_LEFT => "bottom-left",
        Constants::WM_BOTTOM_RIGHT => "bottom-right",
        Constants::WM_NOWHERE => "no watermark"])->label("Position of watermark") ?>
    <div class="form-group">
        <div class="col-lg-offset-1 col-lg-11">
            <?= Html::submitButton('Create Image', ['class' => 'btn btn-primary']) ?>
        </div>
    </div>
<?php ActiveForm::end() ?>