<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
        
    </head>
    <body>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
        <p>Deletion of category</p>
        <?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\Gallery\configuration\Constants;
    $form = ActiveForm::begin([
        'id' => 'delete-form',
        'options' => ['class' => 'form-horizontal'],
    ]) ?>
        <?php 
            $actions = [Constants::DELETE_IMAGES_AFTER_CATEGORY_DELETION => "Delete"];
            $isAlternativeExists = count($categories) > 0;
            if($isAlternativeExists){
                $actions[Constants::CHANGE_CATEGORY_AFTER_CATEGORY_DELETION] =  "Change category";
            }
            ?>
        <?= $form->field($model, 'slug')->hiddenInput()->label(false)?>
        <?= $form->field($model, 'deleteStatus')->dropDownList($actions,['id' => 'deleteStatusInput'])
                ->label('Choose action with images of category:') ?>
        <div id="newCategory">
            <?= $form->field($model, 'newCategoryForImagesId')->dropDownList($categories)
                ->label("New Category for images") ?>
        </div>
        <div class="form-group">
            <div class="col-lg-offset-1 col-lg-11">
                <?= Html::submitButton('Delete Category', ['class' => 'btn btn-primary']) ?>
            </div>
        </div>
    <?php ActiveForm::end() ?>
    </body>
    <script type="text/javascript">
       $(document).ready(function() {
        $('#deleteStatusInput').change(function(){
            if($('#deleteStatusInput').val() == parseInt('<?= Constants::CHANGE_CATEGORY_AFTER_CATEGORY_DELETION ?>')){
                $('#newCategory').show();
            } else {
                $('#newCategory').hide();
            }
        });
        $('#deleteStatusInput').change();
    });
    </script>
</html>
