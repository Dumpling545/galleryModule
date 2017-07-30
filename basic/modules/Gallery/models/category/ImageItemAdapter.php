<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\modules\Gallery\models\category;
use app\modules\Gallery\models\image\ImageItem;
use app\modules\Gallery\models\category\CategoryItem;
class ImageItemAdapter extends ImageItem {
        public $header;
    public function __construct(CategoryItem $categoryItem) {
        $this->name = $categoryItem->slug;
        $this->data = $categoryItem->data;
        $this->header = $categoryItem->header;
    }
}
