<?php


namespace app\modules\Gallery\models\category;

use yii\base\Model;
class GetAllCategoriesModel extends Model{
    public $items;
    public $isEnd;
    public function rules()
    {
        return [
            [['categories', 'isEnd'], 'required'],
            [['isEnd'], 'boolean'],
            [['categories'], 'array']
        ];
    }
}
