<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\modules\Gallery\services\implementations;

use yii\data\Pagination;
use app\modules\Gallery\configuration\Constants;
use app\modules\Gallery\models\category\{CreateCategoryModel, DeleteCategoryModel,GetAllCategoriesModel,
GetCategoryModel, SendCategoryModel, UpdateCategoryModel, CategoryItem, CategoryListItem};
use app\modules\Gallery\services\abstractions\ICategoryService;
use app\modules\Gallery\repositories\abstractions\{ICategoryRepository, IImageRepository};
use app\modules\Gallery\repositories\entities\Category;
use app\modules\Gallery\helpers\{ImageManager, ImageDataProvider};
use app\modules\Gallery\models\image\{ImageItem, AdminImageItem, AdminImageModel};
class CategoryService implements ICategoryService{
    public $repository;
    public $imageRepository;
    public function __construct(ICategoryRepository $repository, IImageRepository $imageRepository) {
        $this->imageRepository = $imageRepository;
        $this->repository = $repository;
    }
    public function createCategory(CreateCategoryModel $model) {
        try {
            if(!empty($model)){
                $category = new Category();
                $category->id = -1;
                $category->name = $model->name;
                $category->accessibilityStatus = $model->accessibilityStatus;
                $category->slug = $model->slug;
                return $this->repository->createCategory($category);
            } else {
                throw new \Exception(Constants::NULL_ARGUMENT_MESSAGE);
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function deleteCategory(DeleteCategoryModel $model, int $status) {
        try {
            //throw new \Exception($model->newCategoryForImagesId);
            if($status == Constants::ADMIN){
                $id = $this->repository->getIdBySlug($model->slug);
                //
                if($model->deleteStatus == Constants::DELETE_IMAGES_AFTER_CATEGORY_DELETION){
                    $ids = $this->imageRepository->deleteImagesByCategory($id);
                    array_walk($ids, function(&$value, $key){
                        $value = intval($value); 
                    });
                    ImageManager::deleteImageFiles(...$ids);
                } else if($model->deleteStatus == Constants::CHANGE_CATEGORY_AFTER_CATEGORY_DELETION){
                    $model->scenario = "";
                    $this->imageRepository->updateCategoryOfImages($id, $model->newCategoryForImagesId);
                }
                $this->repository->deleteCategory($id);
            } else {
                throw new \Exception(Constants::NOT_ALLOWED_MESSAGE);
            } 
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function getAllCategories(int $status, Pagination &$pagination, bool $hideByLink) {
        try {
            $model = new GetAllCategoriesModel();
            $tcount = $this->repository->getCountOfAllCategories($status, $hideByLink);
            $pagination->totalCount = $tcount;
            $categories = $this->repository->getAllCategories($status, $pagination->offset, $pagination->limit, $hideByLink);
            $result = array();
            foreach ($categories as $category){
                $item = new CategoryItem();
                $cat =  (object) $category;
                $item->slug = $cat->slug;
                $count = $this->imageRepository->getCountOfImagesByCategory($cat->id, $status);
                $img_id = $this->imageRepository->getLastImageIdInCategory($cat->id, $status);
                if($img_id > 0){
                    $item->data = ImageManager::createImage($img_id);
                } else {
                    $item->data = ImageManager::createEmptyImage();
                }
                $item->header = $cat->name.'('.$count.')';
                array_push($result, $item);
            }
            $model->items = $result;
            $model->isEnd = false;
            if($tcount <= $pagination->offset + $pagination->limit){
                $model->isEnd = true;
            } 
            return $model;
        } catch (\Exception $e) {
            throw $e;
        }
    }
    public function getCategoryInfo(string $slug, int $status){
        try {
        if($slug !== null && $status !== null){    
            $categoryId = $this->repository->getIdBySlug($slug);
            $query = $this->repository->getCategory($categoryId, $status);
            $category = new UpdateCategoryModel();
            $category->id = $categoryId;
            $category->name = $query->name;
            $category->slug = $query->slug;
            $category->accessibilityStatus = $query->accessibilityStatus;
            return $category;
        } else {
            throw new \Exception(Constants::NULL_ARGUMENT_MESSAGE);
        }
        } catch (\Exception $e) {
            throw $e;
        }
    }
    public function getCategory(string $slug, int $status, Pagination &$pagination, bool $hideByLink) {
        try {
            if($slug!== null && $status!==null && !empty($pagination) && $hideByLink !== null){
                $category = new SendCategoryModel();
                $categoryId = $this->repository->getIdBySlug($slug);
                $entity = $this->repository->getCategory($categoryId, $status);
                /*if(empty($entity)){
                    throw new \Exception("Category is not allowed or does not exist");
                }
                foreach (array_keys(get_object_vars($entity)) as $key){
                    throw new \Exception($key);
                }*/
                
                $category->name = $entity->name;
                $category->items = array();
                $count = $this->imageRepository->getCountOfImagesByCategory($categoryId, $status);
                $pagination->totalCount = $count;
                $images = $this->imageRepository
                      ->getImagesByCategory($categoryId, $status, $pagination->offset, $pagination->limit, $hideByLink);
                foreach ($images as $image){
                    $img = (object) $image;
                    $item = new ImageItem();
                    $item->id = $img->id;
                    $item->name = $img->name;
                    $item->data = ImageManager::createImage($img->id);
                    array_push($category->items, $item);
                }
                $category->isEnd = false;
                if($count <= $pagination->offset + $pagination->limit){
                    $category->isEnd = true;
                }
                return $category;
            } else {
                throw new \Exception(Constants::NULL_ARGUMENT_MESSAGE);
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function updateCategory(UpdateCategoryModel $model, int $status) {
        try {
            if($status == Constants::ADMIN){
                $category = new Category();
                $default =  $this->repository->getCategory($model->id, $status);
                $category->id = $model->id;
                $category->name = (!empty($model->name)) ? $model->name : $default->name;
                $category->accessibilityStatus = $model->accessibilityStatus;
                $category->slug = (!empty($model->slug)) ? $model->slug : $default->slug;
                $this->repository->updateCategory($category);
            } else {
                throw new \Exception(Constants::NOT_ALLOWED_MESSAGE);
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function getCategoryNames(int $status, bool $getInfo) {
        try{
            if($status !== null && $getInfo !== null){
                $count = $this->repository->getCountOfAllCategories($status, false);
                $queryArray = $this->repository->getAllCategories($status, 0, $count, false);
                $result = array();
                foreach ($queryArray as $item){
                    if($getInfo){
                        $newItem = new CategoryListItem();
                        $newItem->id = $item['id'];
                        $newItem->name = $item['name'];
                        $newItem->slug = $item['slug'];
                        $newItem->count = $this->imageRepository->getCountOfImagesByCategory($item['id'], $status);
                        array_push($result, $newItem);
                    } else {
                        $result[$item['id']] = $item['name'];
                    }
                }
                return $result;
            } else {
                throw new \Exception(Constants::NULL_ARGUMENT_MESSAGE);
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function getSlugById(int $id) {
        try{
            if($id !== null)
                return $this->repository->getSlugById($id);
            else
                throw new \Exception(Constants::NULL_ARGUMENT_MESSAGE);
        } catch (\Exception $e) {
            throw $e;
        }
    }
    

    public function getImageDataProviderByCategory(string $slug, int $status, Pagination &$pagination, bool $hideByLink) {
        try{
            if($slug !== null){
                $result = [];
                $id = $this->repository->getIdBySlug($slug);
                $count = $this->imageRepository->getCountOfImagesByCategory($id, $status);
                $pagination->totalCount = $count;
                $images = $this->imageRepository->getImagesByCategory(
                        $id, $status, $pagination->offset, $pagination->limit, $hideByLink);   
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
