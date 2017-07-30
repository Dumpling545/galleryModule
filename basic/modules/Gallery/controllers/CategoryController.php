<?php

namespace app\modules\Gallery\controllers;

use yii\web\Controller;
use yii\data\{Pagination,ArrayDataProvider};
use yii\web\NotFoundHttpException;
use yii\filters\{VerbFilter,AccessControl};
use Yii;
use app\modules\Gallery\configuration\Constants;
use app\modules\Gallery\helpers\{RoleManager, JsonReconstructor};
use app\modules\Gallery\services\abstractions\ICategoryService;
use app\modules\Gallery\models\category\{CreateCategoryModel, DeleteCategoryModel, 
GetAllCategoriesModel, SendCategoryModel, UpdateCategoryModel};
use app\modules\Gallery\models\common\ErrorModel;
class CategoryController extends Controller {
    private $categoryService;
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'get-category' => ['GET'],
                    'create-category' => ['GET', 'POST'],
                    'update-category' => ['GET', 'POST'],
                    'delete-category' => ['GET', 'POST'],
                    'admin-page' => ['GET'],
                    'get-all-categories' => ['GET']
                ]
            ],
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['create-category', 'delete-category','update-category','admin-page'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['delete-category','update-category','admin-page'],
                        'roles' => ['admin']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create-category'],
                        'roles' => ['@']
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
        $this->categoryService = Yii::$container->get(ICategoryService::class);
        $authManager = \Yii::$app->authManager;
        if(empty($authManager->getRole("admin"))){
            $admin = $authManager->createRole("admin");
            $user = $authManager->createRole("user");
            $authManager->add($admin);
            $authManager->add($user);
        }
    }
    //createCategory: $model
    //errorPage: $model
    function actionCreateCategory(){
        try{
            $model = new CreateCategoryModel();
            if(Yii::$app->request->method == "GET"){
                //throw new Exception(\Yii::$app->user->identity);
                return $this->render('createCategory', [
                    'model' => $model
                ]);
            } else if($model->load(Yii::$app->request->post())){
                $this->categoryService->createCategory($model);
                return $this->redirect('/category/get/'.$model->slug.'/1.html');
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
    function actionDeleteCategory(string $slug){
        try{
            $status = RoleManager::getStatus(Yii::$app->user); 
            $model = new DeleteCategoryModel();
            if(Yii::$app->request->method == "GET"){
                //$this->categoryService = new ICategoryService();
                $model->slug = $slug;
                $categories =$this->categoryService->getCategoryNames($status, false);
                foreach (array_keys($categories) as $key){
                    if($this->categoryService->getSlugById($key) === $slug){
                        unset($categories[$key]);
                        break;
                    }
                }
                return $this->render('deleteCategory', [
                    'model' => $model,
                    'categories' => $categories
                ]);
            } else if($model->load(Yii::$app->request->post())){
                $this->categoryService->deleteCategory($model, $status);
                return $this->redirect('/category/all/1.html');
            } else {
                throw new \Exception(Constants::INVALID_MODEL_MESSAGE);
            }
        }
        catch (\Exception $e) {
            Yii::$app->response->setStatusCode(Constants::BAD_REQUEST);
            $model = new ErrorModel();
            $model->message = $e->getMessage();
            return $this->render('/common/Error', [
                'model' => $model
            ]);
        }
    }
    function actionGetCategory(string $slug, bool $isJson = false){
        try{
            $pagination = new Pagination([
                'defaultPageSize' => 20
            ]);
            $status = RoleManager::getStatus(Yii::$app->user);
            $model = $this->categoryService->getCategory($slug, $status, $pagination, true);
            
            if(!$isJson){
                return $this->render('getCategory', [
                    'model' => $model,
                    'pagination' => $pagination
                ]);
            } else {
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                Yii::$app->response->statusCode = 200;
                JsonReconstructor::Reconstruct($model);
                return [
                    'model' => /*json_encode($model)*/$model,
                    'pagination' => json_encode($pagination)
                ];
            }
        } catch (Exception $e) {
            Yii::$app->response->setStatusCode(Constants::BAD_REQUEST);
            if($isJson){
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON; 
                return ["message" => $e->getMessage()];
            }
            $model = new ErrorModel();
            $model->message = $e->getMessage();
            return $this->render('/common/Error', [
                'model' => $model
            ]);
        }
    }
    function actionGetAllCategories(bool $isJson = false){
        try{
            $pagination = new Pagination([
                'defaultPageSize' => 20
            ]);
            $status = RoleManager::getStatus(Yii::$app->user);
            $model = $this->categoryService->getAllCategories($status, $pagination, true);
            if(!$isJson){
                return $this->render('allCategories', [
                    'model' => $model,
                    'pagination' => $pagination
                ]);
            } else {
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                Yii::$app->response->statusCode = 200;
                JsonReconstructor::Reconstruct($model);
                return [
                    'model' => $model,
                    'pagination' => json_encode($pagination)
                ];
            }
        } catch (Exception $e) {
            Yii::$app->response->setStatusCode(Constants::BAD_REQUEST);
            if($isJson){
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON; 
                return ["message" => $e->getMessage()];
            }
            $model = new ErrorModel();
            $model->message = $e->getMessage();
            return $this->render('/common/Error', [
                'model' => $model
            ]);
        }
    }
    function actionUpdateCategory(string $slug){
        try{
            $status = RoleManager::getStatus(Yii::$app->user);
            $model = $this->categoryService->getCategoryInfo($slug, $status);
            if(Yii::$app->request->method == "GET"){
                return $this->render('updateCategory', [
                    'model' => $model
                ]);
            } else if($model->load(Yii::$app->request->post())){     
                $this->categoryService->updateCategory($model, $status);
                return $this->redirect('/category/get/'.$model->slug.'/1.html');
            } else {
                throw new \Exception(Constants::INVALID_MODEL_MESSAGE);
            }
        } catch (Exception $e) {
            Yii::$app->response->setStatusCode(Constants::BAD_REQUEST);
            $model = new ErrorModel();
            $model->message = $e->getMessage();
            return $this->render('/common/Error', [
                'model' => $model
            ]);
        }
    }
    function actionAdminPage(){
        try{
            $status = RoleManager::getStatus(Yii::$app->user);
            $dataProvider = new ArrayDataProvider([
                'allModels' => $this->categoryService->getCategoryNames($status, true),
                'pagination' => [
                    'pageSize' => 10
                ]
            ]);
            return $this->render('adminPage', [
                'dataProvider' => $dataProvider
            ]);
        } catch (Exception $e) {
            Yii::$app->response->setStatusCode(Constants::BAD_REQUEST);
            $model = new ErrorModel();
            $model->message = $e->getMessage();
            return $this->render('/common/Error', [
                'model' => $model
            ]);
        }
    }
}
