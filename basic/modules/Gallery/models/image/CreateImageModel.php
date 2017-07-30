<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\modules\Gallery\models\image;
use Yii;
use yii\base\Model;
use yii\web\UploadedFile;
use app\modules\Gallery\configuration\Constants;
class CreateImageModel extends Model{
    public $categoryId;
    public $name;
    public $accessibilityStatus;
    public $imageFileName;
    public $imageFile;
    public $watermarkPosition;
    
    public function rules()
    {
        return [
            [['watermarkPosition','categoryId', 'name', 'accessibilityStatus', 'imageFile'], 'required'],
            [['watermarkPosition','categoryId', 'accessibilityStatus'], 'integer'],
            ['imageFile','file','skipOnEmpty' => false, 'extensions' => 'png, jpg, jpeg, gif'],
            ['name', 'string',  'length' =>[5, 30]]
        ];
    }
    public function upload()
    {
        if ($this->validate()) {
            $random = random_int(1, 1000000);
            $this->imageFileName = 'upload'.$random . $this->imageFile->baseName . '.' . $this->imageFile->extension;
            $this->imageFile->saveAs(Yii::getAlias(Constants::IMAGE_PATH.'/'.$this->imageFileName));
            return true;
        } else {
            return false;
        }
    }
}
