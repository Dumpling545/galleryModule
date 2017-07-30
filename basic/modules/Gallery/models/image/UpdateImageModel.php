<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\modules\Gallery\models\image;
use yii\base\Model;

class UpdateImageModel extends Model{
    public $categoryId;
    public $name;
    public $accessibilityStatus;
    public $id;
    public function rules()
    {
        return [
            [['categoryId', 'name', 'accessibilityStatus', 'id'], 'required'],
            [['accessibilityStatus', 'categoryId', 'id'], 'integer'],
            ['name', 'string',  'length' =>[5, 30]],
        ];
    }
}
