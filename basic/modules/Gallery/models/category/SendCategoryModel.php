<?php



namespace app\modules\Gallery\models\category;

use yii\base\Model;
class SendCategoryModel extends Model{
    public $name;
    public $items;
    public $isEnd;
    public function rules()
    {
        return [
            [['name','images', 'isEnd'], 'required'],
            [['name'], 'string'],
            ['images','array'],
            ['isEnd','boolean']
        ];
    }
}
