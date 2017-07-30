<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\modules\Gallery\models\image;

use yii\base\Model;

class AdminImageItem extends Model{
    public $id;
    public $name;
    public $accessibilityStatus;
    public function rules()
    {
        return [
            [['accessibilityStatus', 'id', 'name'], 'required'],
            ['name', 'string'],
            [['id','accessibilityStatus'],'integer']
        ];
    }
}
