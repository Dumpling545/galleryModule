<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\modules\Gallery\models\common;

use yii\base\Model;
class ErrorModel extends Model{
    public $message;
    public function rules()
    {
        return [
            [['message'], 'required'],
            [['message'], 'string']
        ];
    }
}
