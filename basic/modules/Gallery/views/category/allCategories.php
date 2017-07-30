<?php 
use yii\helpers\HtmlPurifier;
use yii\helpers\Url;
?>
<html>
    <head>
        <meta charset="UTF-8">      
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title></title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <link rel="stylesheet" type="text/css" href="<?= Yii::getAlias('@app').'\modules\Gallery\views\common\css\flexibleGallery.css' ?>">
        <style>
            img {
                padding: 0;
                width:100%;
            }
            body{
                margin: 0;
                padding: 0;
            }
            #main {
                display: block;
                width: 100%;
            }
            
            .header {
                background-color: rgba(0, 0, 0, 0.6);
                color: white;
                position:relative;
                bottom: calc(1em+2px);
                display: block;
            }
            .cell {
                width: 100%;
                margin:0.3%;
                padding: 0;
                display:inline-block;
                float: left;
                z-index: 1;
            }
            
            .loader {
                float: none;
                text-align: center;
                width: 100%;
                display: inline-block;
                border: none; /* Remove borders */
                color: black;
            }
            <?php
            for($i = 2;$i <= 10; $i++){
                echo "  @media only screen and (min-width: ".($i*180)."px) {";
                echo "      .cell {";
                echo "          width:".round((100/$i - 0.6),5)."%;";
                echo "      }";
                echo "  }";
            }
            ?>
        </style>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
        <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/themes/smoothness/jquery-ui.css" />
        <script type="text/javascript">
            var $isEnd = '<?= $model->isEnd ?>' == '1';
            var $page;
            var $canSend = true;
            var $pattern = 'http://localhost:9000/category/';
            var $all_method = 'all/';
            var $get_method = 'get/';
            var obj = { foo: Math.random() };
            function refreshParams(){
                var $url = window.location.href.toString();
                var $begin = '/all/';
                var $end = '.html';
                var $string = $url.substring($url.indexOf($begin) + $begin.length, $url.indexOf($end));
                $page = parseInt($string);
            }
            $(document).ready(function() {
                $('.loader').hide();
                refreshParams();
                $(document).on('click', '.cell', function(){
                    document.location.href = $pattern + $get_method +  $(this).attr('data-name') + '/1.html';
                });
                function loadData() {
                    if($(window).scrollTop() + $(window).height() == $(document).height() && !$isEnd && $canSend) {
                        $canSend = false;
                        $('.loader').show();
                        $.ajax({
                           url: $pattern + $all_method + ($page + 1) + '.html',
                           type: 'get',
                           dataType: 'json',
                           data: { 
                                    _csrf : '<?=Yii::$app->request->getCsrfToken()?>',
                                    isJson: true
                                },
                           success: function (data) {
                                //data = JSON.parse(data);
                               for(var i = 0; i < data['model']['items'].length; i++){
                                   var element = $(data['model']['items'][i]);
                                   $("#main").append(element);
                                }
                                $('.loader').hide();
                               $isEnd = data['model']['isEnd'];
                               $page++;
                               window.history.pushState(obj,"default"+obj.foo,($pattern +$all_method+ $page +'.html'));
                               $canSend = true;                               
                            },
                            error: function (jqXHR, textStatus, errorThrown) {
                              alert($pattern + $all_method + ($page+1)+'.html');
                              alert('Error on loading page: ' + errorThrown +', ' + jqXHR['error']);
                              $canSend = true;
                            }
                        });
                    }
                }
                $(window).scroll(loadData);
                if ($(document).height() > $(window).height()) {
                    $(window).scroll(loadData);
                } else {
                    var interval;
                    interval = setInterval(function(){
                        loadData();
                        if($isEnd)
                            clearInterval(interval);
                    }, 500);
                }
            });
        </script>
    </head>
    <body>
	<div id="main">
           
            <?php 
                foreach($model->items as $item){
                    echo 
                        "<div data-name=".$item->slug." class='cell'><img src='data:image/jpeg;base64," . base64_encode($item->data) . "' /><div class='header'>".$item->header."</div></div>";
                }
            ?>
	</div>
         <div class="loader"><i class="fa fa-spinner fa-spin"></i></div> 
    </body>
</html>