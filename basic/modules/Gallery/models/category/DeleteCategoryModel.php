<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\modules\Gallery\models\category;

use yii\base\Model;
class DeleteCategoryModel extends Model{
    public $slug;
    public $deleteStatus;
    public $newCategoryForImagesId;
    const SCENARIO_DELETE_IMAGES = 'deleteImages';
    const SCENARIO_CHANGE_CATEGORY_FOR_IMAGES = 'changeCategoryForImages';
   /* public function scenarios()
    {
        return [
            self::SCENARIO_DELETE_IMAGES => ['slug', 'deleteStatus'],
            self::SCENARIO_CHANGE_CATEGORY_FOR_IMAGES => ['slug', 'deleteStatus', 'newCategoryForImagesId']
        ];
    }*/
    public function rules()
    {
        return [
            [['slug', 'deleteStatus'], 'required'],
            //['newCategoryForImagesId', 'required','on' => self::SCENARIO_CHANGE_CATEGORY_FOR_IMAGES],
            [['slug'], 'string'],
            ['slug', 'trim'],
            ['slug', 'string', 'length' =>[3, 15]],
            ['slug', 'compare', 'compareValue' => 'slug', 'operator' => '!='],
            [['deleteStatus','newCategoryForImagesId'], 'integer']
        ];
    }
}
