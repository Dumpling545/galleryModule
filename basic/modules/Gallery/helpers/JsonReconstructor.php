<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\modules\Gallery\helpers;

use app\modules\Gallery\models\category\CategoryItem;
use app\modules\Gallery\models\category\ImageItemAdapter;


class JsonReconstructor {
    static function Reconstruct(&$model){
        $newItems =array();
        $example = new CategoryItem();
        foreach($model->items as $item){
            $isCategoryItem = (get_class($item)==get_class($example));
            $appendix ="";
            if($isCategoryItem){
                $item = new ImageItemAdapter($item);
                $appendix = "<div class='header'>".$item->header."</div>";
            }
            $string = "<div class='cell' data-name='".$item->name."'><img src='data:image/jpeg;base64," . base64_encode($item->data) . "' />".$appendix."</div>";
            array_push($newItems, $string);
        }
        $model->items = $newItems;
    }
}
