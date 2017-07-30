<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\modules\Gallery\services\implementations;

namespace app\modules\Gallery\services\implementations;
use Yii;
use yii\data\Pagination;
use app\modules\Gallery\configuration\Constants;
use app\modules\Gallery\models\image\{CreateImageModel, DeleteImageModel, AdminImageModel,AdminImageItem, UpdateImageModel};
use app\modules\Gallery\services\abstractions\IImageService;
use app\modules\Gallery\repositories\abstractions\{IImageRepository, ICategoryRepository};
use app\modules\Gallery\helpers\{ImageManager, ImageDataProvider};
use app\modules\Gallery\repositories\entities\Image;
class ImageService implements IImageService{
    private $repository;
    private $categoryRepository;
    public function __construct(IImageRepository $repository, ICategoryRepository $categoryRepository) {
        $this->repository = $repository;
        $this->categoryRepository = $categoryRepository;
    }   
    public function createImage(CreateImageModel $model, string $author) {
        try{
            if(!empty($model) && $author !== null){
                ImageManager::handleImage($model->imageFileName, $model->watermarkPosition);
                $image = new Image();
                $image->id = -1;
                $image->categoryId = $model->categoryId;
                $image->name = $model->name;
                $image->accessibilityStatus = $model->accessibilityStatus;
                $image->author = $author;
                $image->uploadDate = date(DATE_RSS);
                $id = $this->repository->createImage($image);
                $ext = ImageManager::getExtension($model->imageFileName);
                if(!rename(Yii::getAlias(Constants::IMAGE_PATH.'/'.$model->imageFileName), Yii::getAlias(Constants::IMAGE_PATH.'/'.strval($id).'.'.$ext))){
                    unlink(Yii::getAlias(Constants::IMAGE_PATH.'/'.$model->imageFileName));
                    $this->repository->deleteImage($id);
                    throw new \Exception(Constants::ERROR_ON_HANDLING_IMAGE_MESSAGE);
                }
            } else {
                throw new \Exception(Constants::NULL_ARGUMENT_MESSAGE);
            }
        } catch(\Exception $e){
            throw $e;
        }
    }

    public function deleteImage(int $id, int $status, string $author) {
        try{
            if($status == Constants::ADMIN || $author == $this->repository->getAuthorOfImage($id)){
                $this->repository->deleteImage($id);
                ImageManager::deleteImageFiles($id);
            } else {
                throw new \Exception(Constants::NOT_ALLOWED_MESSAGE);
            }
        }catch(\Exception $e){    
            throw $e;
        }
    }

    public function getImage(int $id, int $status) {
        try{
            if($id !== null){
                $image = $this->repository->getImage($id, $status);
                $model = new UpdateImageModel();
                $model->accessibilityStatus = $image->accessibilityStatus;
                $model->name = $image->name;
                $model->id = $image->id;
                return $model;
            } else {
                throw new \Exception(Constants::NOT_ALLOWED_MESSAGE);
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function updateImage(UpdateImageModel $model, int $status, string $author) {
        try{
            if($status == Constants::ADMIN || $author == $this->repository->getAuthorOfImage($model->id)){
                $image = new Image();
                $default = $this->repository->getImage($model->id, $status);
                $image->id = $model->id;
                $image->author = $default->author;
                $image->uploadDate = $default->uploadDate;
                $image->categoryId = $model->categoryId;
                $image->accessibilityStatus = $model->accessibilityStatus;
                $image->name = (!empty($model->name)) ? $model->name : $default->name;
                $this->repository->updateImage($image);
            } else {
                throw new \Exception(Constants::NOT_ALLOWED_MESSAGE);
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function getImagesByAuthor(string $author, Pagination &$pagination) {
        try{
            if($author !== null && !empty($pagination)){
                $result = [];
                $count = $this->repository->getCountOfImagesByAuthor($author);
                $pagination->totalCount = $count;
                $images = $this->repository->getImagesByAuthor(
                        $author, $pagination->offset, $pagination->limit);   
                foreach ($images as $image){
                    $img = (object) $image;
                    $item = new AdminImageItem();
                    $item->id = $img->id;
                    $item->name = $img->name;
                    $item->accessibilityStatus = $img->accessibilityStatus;
                    array_push($result, $item);
                }
                $provider = new ImageDataProvider([
                    'models' => $result,
                    'count' => $count,
                    'pagination' => $pagination
                ]);
                return $provider;
            } else {
                throw new \Exception(Constants::NULL_ARGUMENT_MESSAGE);
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

}
