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
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    </head>
    <body>
        <?php 
        use yii\grid\GridView;
        use yii\bootstrap\Html;
        use yii\helpers\Url;
        use yii\widgets\LinkPager;
        ?>
        <h1> <?= $name ?> </h1>
        <?php echo GridView::widget([
        'dataProvider' => $provider,
        'columns' => [
            'id',
            'name',
            'accessibilityStatus',
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update}{delete}',
                'buttons' => [
                    'update' => function ($url, $model, $key) {
                        return Html::a('Update', ['/image/update/'.$model->id])."  ";
                    },
                    'delete' =>  function ($url, $model, $key) {
                        //url
                        return Html::button('Delete',['class'=>'deleteButton','data-id' => $model->id])."  ";
                    }
                ]     
            ]
        ],
    ]); ?>
    </body>
<script type="text/javascript">  
    $(document).ready(function() {
        $('.deleteButton').click(function(){
            var $id = $(this).attr("data-id");
            var $button = $(this);
            if(confirm("Do you want to delete this image?")){
            $.ajax({
               url: "<?php echo Url::toRoute('/image/delete') ?>",
               type: 'get',
               data: {
                         id: $id, 
                         _csrf : '<?=Yii::$app->request->getCsrfToken()?>'
                     },
               success: function (data) {
                   alert('Image was deleted');
                   $button.parent().parent().remove();
               },
                error: function (jqXHR, textStatus, errorThrown) {
                  alert('Error on deleting image: ' + errorThrown);
                }
            });
          }
        });
    });
</script>
</html>
