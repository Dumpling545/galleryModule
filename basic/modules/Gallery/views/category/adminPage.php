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
        <?php 
        use yii\grid\GridView;
        use yii\bootstrap\Html;
        ?>
        <p> Categories: </p>
        <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'id',
            'slug',
            'name',
            'count',
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}{update}{delete}{administer-images}',
                'buttons' => [
                    'view' => function ($url, $model, $key) {
                        return Html::a('View', ['/category/get/'.$model->slug.'/1'])."  ";
                    },
                    'update' => function ($url, $model, $key) {
                        return Html::a('Update', ['/category/update/'.$model->slug])."  ";
                    },
                    'delete' =>  function ($url, $model, $key) {
                        //url
                        return Html::a('Delete',['/category/delete/'.$model->slug])."  ";
                    },
                    'administer-images'  => function ($url, $model, $key) {
                        return Html::a('Administer Images', ['/image/admin-page/'.$model->slug.'/1']);
                    }
                ]     
            ]
        ],
    ]); ?>
    </body>
<!--    <script type="text/javascript">  
    /*$(document).ready(function() {
        
    });*/
</script>-->
</html>
