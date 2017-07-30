<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\modules\Gallery\controllers;

use yii\web\Controller;
use yii\filters\{VerbFilter,AccessControl};
use yii\data\{Pagination};
use Yii;
use yii\web\UploadedFile;
use app\modules\Gallery\configuration\Constants;
use app\modules\Gallery\models\common\ErrorModel;
use app\modules\Gallery\helpers\{RoleManager, ImageDataProvider};
use app\modules\Gallery\services\abstractions\{ICategoryService, IImageService};
use app\modules\Gallery\models\image\{CreateImageModel, DeleteImageModel, 
 AdminImageItem, UpdateImageModel};
class ImageController extends Controller {
    private $imageService;
    private $categoryService;
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'create-image' => ['GET', 'POST'],
                    'update-image' => ['GET', 'POST'],
                    'delete-image' => ['GET'],
                    'admin-page' => ['GET'],
                    'user-page' => ['GET']
                ]
            ],
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['create-image', 'delete-image','update-image','admin-page','user-page'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['admin-page'],
                        'roles' => ['admin']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create-image', 'delete-image','update-image', 'user-page'],
                        'roles' => ['admin', 'user']
                    ]
                ]
            ]
        ];
    }
    public function afterAction($action, $result)
    {
        Yii::$app->getUser()->setReturnUrl(Yii::$app->request->url);
        return parent::afterAction($action, $result);
    } 
    function __construct($id, $module, $config = array()) {
        parent::__construct($id, $module, $config);
        $this->imageService = Yii::$container->get(IImageService::class);
        $this->categoryService = Yii::$container->get(ICategoryService::class);
        $authManager = \Yii::$app->authManager;
        if(empty($authManager->getRole("admin"))){
            $admin = $authManager->createRole("admin");
            $user = $authManager->createRole("user");
            $authManager->add($admin);
            $authManager->add($user);
        }
    }
    function actionCreateImage(){
        try{
            $model = new CreateImageModel();
            if(Yii::$app->request->method == "GET"){
                $status = RoleManager::getStatus(Yii::$app->user);
                $categories = $this->categoryService->getCategoryNames($status, false);
                return $this->render('createImage', [
                    'model' => $model,
                    'categories' => $categories
                    
                ]);
            } else if($model->load(Yii::$app->request->post())){
                $model->imageFile = UploadedFile::getInstance($model, 'imageFile');
                if (!$model->upload()){
                    throw new \Exception("something wrong");
                }
                $this->imageService->createImage($model, Yii::$app->user->identity->username);
                $slug = $this->categoryService->getSlugById($model->categoryId);
                return $this->redirect('/category/get/'.$slug.'/1.html');
            } else {
                throw new \Exception(Constants::INVALID_MODEL_MESSAGE);
            }
        } catch (\Exception $e) {
            Yii::$app->response->setStatusCode(Constants::BAD_REQUEST);
            $model = new ErrorModel();
            $model->message = $e->getMessage();
            return $this->render('/common/Error', [
                'model' => $model
            ]);
        }
    }
    function actionDeleteImage(int $id){
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        try{
            if($id != null){
                $user = Yii::$app->user;
                $status = RoleManager::getStatus($user);
                $author = $user->identity->username;
                $this->imageService->deleteImage($id, $status, $author);
                Yii::$app->response->setStatusCode(Constants::OK);
                return [];
            } else {
                throw new \Exception(Constants::INVALID_MODEL_MESSAGE);
            }
        } catch (\Exception $e) {
            Yii::$app->response->setStatusCode(Constants::BAD_REQUEST);
            return ['message' => $e->getMessage()];
        }
    }
    function actionUpdateImage(int $id){
        try{
            $model = new UpdateImageModel();
            $status = RoleManager::getStatus(Yii::$app->user);
            if(Yii::$app->request->method == "GET"){
                $model = $this->imageService->getImage($id, $status);
                $categories = $this->categoryService->getCategoryNames($status, false);
                return $this->render('updateImage', [
                    'model' => $model,
                    'categories' => $categories
                    
                ]);
            } else if($model->load(Yii::$app->request->post())){
                $this->imageService->updateImage($model, $status, Yii::$app->user->identity->username);
                $slug = $this->categoryService->getSlugById($model->categoryId);
                return $this->redirect('/category/get/'.$slug.'/1.html');
            } else {
                throw new \Exception(Constants::INVALID_MODEL_MESSAGE);
            }
        } catch (\Exception $e) {
            Yii::$app->response->setStatusCode(Constants::BAD_REQUEST);
            $model = new ErrorModel();
            $model->message = $e->getMessage();
            return $this->render('/common/Error', [
                'model' => $model
            ]);
        }
    }  
    function actionAdminPage(string $categorySlug){
        
        try{
            $pagination = new Pagination([
                'defaultPageSize' => 10
            ]);
            $status = RoleManager::getStatus(Yii::$app->user);
            $provider = $this->categoryService->getImageDataProviderByCategory(
                    $categorySlug, $status, $pagination, false);
            return $this->render('adminPage', [
                'provider' => $provider,
                'name' => $this->categoryService->getCategoryInfo($categorySlug, $status)->name
            ]);
        } catch (\Exception $e) {
            Yii::$app->response->setStatusCode(Constants::BAD_REQUEST);
            $model = new ErrorModel();
            $model->message = $e->getMessage();
            return $this->render('/common/Error', [
                'model' => $model
            ]);
        }
    }
    function actionUserPage(){
        try{
            $pagination = new Pagination([
                'defaultPageSize' => 10
            ]);
            $provider = $this->imageService->getImagesByAuthor(Yii::$app->user->identity->username, $pagination);
            return $this->render('adminPage', [
                'provider' => $provider,
                'name' => Yii::$app->user->identity->username."'s photos"
            ]);
        } catch (\Exception $e) {
            Yii::$app->response->setStatusCode(Constants::BAD_REQUEST);
            $model = new ErrorModel();
            $model->message = $e->getMessage();
            return $this->render('/common/Error', [
                'model' => $model
            ]);
        }
    }
}
