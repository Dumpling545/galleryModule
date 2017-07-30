<?php

namespace app\modules\Gallery\models\category;
use yii\base\Model;
class CreateCategoryModel extends Model{
    public $name;
    public $slug;
    public $accessibilityStatus;
    public function rules()
    {
        return [
            [['slug', 'name', 'accessibilityStatus'], 'required'],
            [['accessibilityStatus'], 'integer'],
            ['slug', 'trim'],
            ['slug', 'string', 'length' =>[3, 15]],
            ['name', 'string',  'length' =>[5, 30]],
            ['slug', 'compare', 'compareValue' => 'slug', 'operator' => '!=']
        ];
    }
}
