<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\modules\Gallery\helpers;
use Yii;
use app\modules\Gallery\configuration\Constants;
use app\modules\Gallery\helpers\ImageDisplayer;
class ImageManager {
    const PNG_EXTENSION = 'png';
    const JPG_EXTENSION = 'jpg';
    const JPEG_EXTENSION = 'jpeg';
    const GIF_EXTENSION = 'gif';
    private static $_textWidth = 127;
    private static $_textHeight =14;
    public static function getFileNameById(int $id){
        $files = scandir(Yii::getAlias(Constants::IMAGE_PATH));
        foreach ($files as $file){
            $name = explode(".", $file);
            if(intval($name[0]) == $id){
                return $name;
            }
        }
    }
    static function getExtension (string $fileName){
        $name = explode(".", $fileName);
        return $name[1];
    }
    static function handleImage(string $fileName, int $watermarkPosition){
        $image = null;
        $save = null;
        $path = Yii::getAlias(Constants::IMAGE_PATH.'/'.$fileName);
        switch (ImageManager::getExtension($fileName)){
            case ImageManager::PNG_EXTENSION:
                $image = imagecreatefrompng($path);
                $save = function($img, string $path){
                    imagepng($img, $path);
                };
                break;
            case ImageManager::JPG_EXTENSION:
            case ImageManager::JPEG_EXTENSION:
                $image = imagecreatefromjpeg($path);
                $save = function($img, string $path){
                    imagejpeg($img, $path);
                };
                break;
            case ImageManager::GIF_EXTENSION:
                $image = imagecreatefromgif($path);
                $save = function($img, string $path){
                    imagegif($img, $path);
                };
                break;
            default:
                throw new \Exception(Constants::NON_SUPPORTED_EXTENSION_MESSAGE);
        }
        $text = Constants::WATERMARK;
        $width = imagesx($image);
        $height = imagesy($image);
       // $white = imagecolorallocatealpha($image, 255, 255, 255, 64);
        $size = 5;
        $watermark = imagecreatetruecolor(ImageManager::$_textWidth, ImageManager::$_textHeight);
        //$color = imagecolorallocatealpha($watermark, 255, 255, 255, 128);
        //imagefill($image, 0, 0, $color);
        $white = imagecolorallocatealpha($watermark, 255, 255, 255, 64);
        $resize_coefficient = max((0.2 * $width) / ImageManager::$_textWidth,
                (0.025 * $height) / ImageManager::$_textHeight);
        imagestring($watermark, $size, 0, 0, $text, $white);
        $dst_w = $resize_coefficient * ImageManager::$_textWidth;
        $dst_h = $resize_coefficient * ImageManager::$_textHeight;
        switch($watermarkPosition){
            case Constants::WM_TOP_LEFT:
                imagecopyresampled($image, $watermark, 0, 0, 0, 0, $dst_w, $dst_h,
                        ImageManager::$_textWidth, ImageManager::$_textHeight);
                //imagestring($image, $size, 0, 0, $text, $white);
                break;
            case Constants::WM_TOP_RIGHT:
                imagecopyresampled($image, $watermark, $width - $dst_w, 0, 0, 0, $dst_w, $dst_h,
                        ImageManager::$_textWidth, ImageManager::$_textHeight);
                //imagestring($image, $size, $width - ImageManager::$_textWidth, 0, $text, $white);
                break;
            case Constants::WM_BOTTOM_LEFT:
                imagecopyresampled($image, $watermark, 0, $height - $dst_h, 0, 0, $dst_w, $dst_h,
                        ImageManager::$_textWidth, ImageManager::$_textHeight);
                //imagestring($image, $size, 0, $height - ImageManager::$_textHeight, $text, $white);
                break;
            case Constants::WM_BOTTOM_RIGHT:
                imagecopyresampled($image, $watermark, $width - $dst_w, $height - $dst_h,
                         0, 0, $dst_w, $dst_h,
                        ImageManager::$_textWidth, ImageManager::$_textHeight);
                //imagestring($image, $size, $width - ImageManager::$_textWidth, $height - ImageManager::$_textHeight, $text, $white);
                break;
            case Constants::WM_NOWHERE:
                break;
            default:
                throw new \Exception(Constants::INVALID_ARGUMENT_MESSAGE);
        }
        imagedestroy($watermark);
        $save($image, $path);
        
    }
    static function createEmptyImage(){
        $image = imagecreate(100,100);
        $background_color = imagecolorallocate($image, 0, 0, 0);
        ob_start();
        imagepng($image);
        $imgData = ob_get_clean();
        return $imgData;
    }
    static function createImage(int $id){
        $name = ImageManager::getFileNameById($id);
        $image = null;
        $fileName = Yii::getAlias(Constants::IMAGE_PATH.'/'.$name[0].'.'.$name[1]);
        ob_start();
        switch ($name[1]){
            case ImageManager::PNG_EXTENSION:
                $image = imagecreatefrompng($fileName);
                imagepng($image, null);
                break;
            case ImageManager::JPG_EXTENSION:
            case ImageManager::JPEG_EXTENSION:
                $image = imagecreatefromjpeg($fileName);
                imagejpeg($image, null);
                break;
            case ImageManager::GIF_EXTENSION:
                $image = imagecreatefromgif($fileName);
                imagegif($image, null);
                break;
            default:
                throw new \Exception(Constants::NON_SUPPORTED_EXTENSION_MESSAGE);
        }
        $imgData = ob_get_clean();
        return $imgData;
    }
    public static function deleteImageFiles(int ...$ids){
        foreach($ids as $id){
            $nameInfo = ImageManager::getFileNameById($id);
            $filename = Yii::getAlias(Constants::IMAGE_PATH.'/'.$nameInfo[0].".".$nameInfo[1]);
            unlink($filename);
        }
    }
}
