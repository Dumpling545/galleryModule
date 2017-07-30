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
            var $isBusy = false;
            var $isEnd = '<?= $model->isEnd ?>' == '1';
            var $page;
            var $slug;
            var $index = 0;
            var $canSend = true;
            var $pattern = 'http://localhost:9000/category/get/';
            var obj = { foo: Math.random() };
            function refreshParams(){
                var $url = window.location.href.toString();
                var $begin = '/get/';
                var $end = '.html';
                var $string = $url.substring($url.indexOf($begin) + $begin.length, $url.indexOf($end));
                var $result = $string.split('/');
                $slug = $result[0];
                $page = parseInt($result[1]);
            }
            function loadData() {
                if($(window).scrollTop() + $(window).height() == $(document).height() && !$isEnd && $canSend && !$isBusy) {
                    $canSend = false;
                    $('.loader').show();
                    $.ajax({
                       url: $pattern + $slug + '/' + ($page + 1) + '.html',
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
                           window.history.pushState(obj,"default"+obj.foo,($pattern +$slug+'/'+ $page +'.html'));
                           $canSend = true;
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                          alert($pattern +$slug+'/'+($page+1)+'.html');
                          alert('Error on loading page: ' + errorThrown +', ' + jqXHR['message']);
                          $canSend = true;
                        }
                    });
                }
            }
            function createModal(target){
                if(!$isBusy){
                    $isBusy = true;
                    $index = $(target).index();
                    var $cloned = $(target).clone();
                    var $modal = $("<div></div>");
                    var $exitButton = $("<button></button>");
                    $exitButton.append($("<b>╳</b>"));
                    var $previousButton = $("<button>◀</button>");
                    var $nextButton = $("<button>▶</button>"); 
                    $previousButton.addClass("previous");
                    $nextButton.addClass("next");
                    $exitButton.addClass("exit");
                    $modal.css({
                        "display":"block",
                        "position":"fixed",
                        "height":"100% -50px",
                        "width":"100%",
                        "z-index": "100", 
                        "left": "0",
                        "top": "50px",
                        "bottom":"0",
                        "right":"0",
                        "text-align":"center",                           
                        "background-color":"rgba(0, 0, 0, 0.8)"
                    });

                    $previousButton.css("right", "75%");
                    $nextButton.css("left", "75%");
                    $cloned.removeClass("cell");
                    $modal.addClass('modal');
                    $cloned.css({
                        "display": "inline-block",
                        "position": "absolute",
                        "background-color": "#fefefe",
                        //"margin-right": "25%",
                        //"margin-left": "25%",
                        "padding": "0",
                        "float":"left"
                    });
                    var $info = $("<div>"+$cloned.attr('data-name')+"</div>");
                    $info.css({
                        "display": "inline-block",
                        "position": "absolute",
                        //"margin-left": "25%",
                        //"margin-right": "25%",
                        "padding": "0",
                        "float":"left",
                        "color":"white",
                        "background-color":"rgba(0, 0, 0, 0.6)",
                        "width":"50%",
                        "right":"25%",
                        //"top":"50%",
                        "z-index": "101"
                    });
                    $cloned.css({"width":"50%", "right":"25%"});
                    $modal.append($cloned);
                    $modal.append($info);
                    $modal.append($exitButton);
                    $modal.append($previousButton);
                    $modal.append($nextButton);
                    $("#main").append($modal);                    
                    if($cloned.height()> $modal.height()){
                        $c = $cloned.width()/$cloned.height();
                        $cloned.height($modal.height());
                        $cloned.width($c*$cloned.height());
                        $cloned.css("right",(($modal.width()-$cloned.width())/2)+"px");
                        
                    }
                    $info.width($cloned.width());
                    
                    $info.css({
                        "top": ($cloned.height()-$info.height())+"px",
                        "right": ($cloned.css("right"))
                    });  
                    //$top = ($modal.height() - $cloned.height())/(2*$modal.height())*100;
                    //$top
                    $buttonProperties = {
                        "font-size": "6em",
                        "background-color":"rgba(0,0,0,0)",
                        "color": "rgba(255,255,255,0.3)",
                        "display":"inline-block",
                        "position":"absolute",
                        "top":"40%",
                        "border-style": "none",
                       "vertical-align":"bottom",
                        "z-index":"101"
                    };
                    $previousButton.css($buttonProperties);
                    $nextButton.css($buttonProperties);
                    $exitButton.css({
                        "font-size": "1.2em",
                        "background-color":"rgba(150,150,150,0)",
                        "color": "rgba(255,255,255,0.3)",
                        "border-style":"none",
                        //"right": "1.5em",
                        "display":"inline-block",
                        "position":"absolute",
                        //"top":($top + "%"),
                        "left":"75%",
                        "z-index":"102"
                    });
                    //$cloned.css({"top":($top + "%"), "bottom":($top + "%")});
                    /*$info.css({
                        "bottom":($top + "%")
                    });*/
                }
            }
            $(document).ready(function() {
                $('.loader').hide();
                refreshParams();
                $(document).on('click', '.cell', function(){
                    createModal(this);
                });
                $(document).on('mouseenter', '.modal button', function(event){
                    $(event.target).css('color','rgba(255,255,255,0.7)');
                });
                $(document).on('mouseleave', '.modal button', function(event){
                    $(event.target).css('color','rgba(255,255,255,0.3)');
                });
                $(document).on('click','.next', function(){
                    $('.modal').remove();
                    $isBusy = false;
                    $next = $("#main").children("div")[$index+1];
                    if($next === undefined){
                        createModal($("#main").children("div")[0]);
                    } else {
                        createModal($next);
                    }
                });
                $(document).on('click','.previous', function(){
                    $('.modal').remove();
                    $isBusy = false;
                    $next = $("#main").children("div")[$index-1];
                    if($next === undefined){
                        createModal($("#main").children("div").last());
                    } else {
                        createModal($next);
                    }
                });
                $(document).on('click','.exit', function(){
                    $('.modal').remove();
                    $isBusy = false;
                });
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
        <h1><?= $model->name?></h1>
	<div id="main">
           
            <?php 
                foreach($model->items as $image){
                    echo 
                        "<div data-name='".$image->name."' class='cell'><img src='data:image/jpeg;base64," . base64_encode($image->data) . "' /></div>";

                }
            ?>
	</div>
         <div class="loader"><i class="fa fa-spinner fa-spin"></i></div> 
    </body>
</html>