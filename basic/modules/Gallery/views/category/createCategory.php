<!DOCTYPE html>
<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\Gallery\configuration\Constants;
$form = ActiveForm::begin([
    'id' => 'create-form',
    'options' => ['class' => 'form-horizontal'],
]) ?>
<h1>Create Category</h1>
    <?= $form->field($model, 'slug') ?>
    <?= $form->field($model, 'name') ?>
    <?= $form->field($model, 'accessibilityStatus')->dropDownList([
        Constants::ALLOWED_TO_ALL => "Allowed to all", 
        Constants::ALLOWED_BY_LINK => "Allowed by link", 
        Constants::ALLOWED_TO_AUTHORIZED_USERS => "Allowed to authorized", 
        Constants::ALLOWED_TO_ADMINS => "Allowed to admins"]) ?>
    <div class="form-group">
        <div class="col-lg-offset-1 col-lg-11">
            <?= Html::submitButton('Create Category', ['class' => 'btn btn-primary']) ?>
        </div>
    </div>
<?php ActiveForm::end() ?>