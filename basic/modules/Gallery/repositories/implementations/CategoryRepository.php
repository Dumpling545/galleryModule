<?php



namespace app\modules\Gallery\repositories\implementations;

use app\modules\Gallery\repositories\abstractions\ICategoryRepository;
use app\modules\Gallery\repositories\entities\Category;
use Yii;
use app\modules\Gallery\configuration\Constants;
class CategoryRepository implements ICategoryRepository{
    private $tableName;
    public function __construct() {
        $this->tableName = (new \ReflectionClass(new Category()))->getShortName();
    }
    public function createCategory(Category $category) {
        if(!empty($category)){
            try{
                $array = get_object_vars($category);
                unset($array['id']);
                $db = Yii::$app->db;
                $db->createCommand()->insert($this->tableName, $array)->execute();
                return $db->createCommand("SELECT MAX(id) AS id FROM ".$this->tableName)->queryOne()['id'];
            } catch (\Exception $e) {
                throw $e;
            }
        } else {
            throw new \InvalidArgumentException(Constants::NULL_ARGUMENT_MESSAGE);
        }
    }

    public function deleteCategory(int $id) {
        if($id !== null){
            try{
                $db = Yii::$app->db;
                $db->createCommand()->delete($this->tableName, "id = {$id}")->execute();
            } catch (\Exception $e) {
                throw $e;
            }
        } else {
            throw new \InvalidArgumentException(Constants::NULL_ARGUMENT_MESSAGE);
        } 
    }

    public function getCategory(int $id, int $status) {
        if($id !== null && $status !== null){
            try{
                $db = Yii::$app->db;
                $query = $db->createCommand("SELECT * FROM ".$this->tableName." WHERE id = :ctgrId".
                    " AND (accessibilityStatus <= :status OR (:status = 0 AND accessibilityStatus = 1))")
                        ->bindValue(":ctgrId", $id)
                        ->bindValue(":status", $status)
                        ->queryOne();
                return (object) $query;
            } catch (\Exception $e) {
                throw $e;
            }
        } else {
            throw new \InvalidArgumentException(Constants::NULL_ARGUMENT_MESSAGE);
        } 
    }

    public function updateCategory(Category $category) {
        if(!empty($category)){
            try{
                $db = Yii::$app->db;
                $db->createCommand()->update($this->tableName, get_object_vars($category), "id={$category->id}")->execute();
            } catch (\Exception $e) {
                throw $e;
            }
        } else {
            throw new \InvalidArgumentException(Constants::NULL_ARGUMENT_MESSAGE);
        }
    }

    public function getAllCategories(int $status, int $offset, int $limit, bool $hideByLink = true) {
        if($status !== null && $offset !== null && $limit !== null && $hideByLink !== null){
            try{
                $appendix = "";
                if($hideByLink){
                    $appendix = 'AND accessibilityStatus <> 1';
                }
                $command = Yii::$app->db->createCommand('SELECT id, name, slug FROM '.$this->tableName.' WHERE '.
                    ' accessibilityStatus <= :status '.$appendix.' LIMIT :limit OFFSET :offset');
                $query = $command->bindValue(":status", $status)
                    ->bindValue(":limit", $limit)
                    ->bindValue(":offset", $offset)
                    ->queryAll();
                return (array)$query;
            } catch(\Exception $e) { 
                throw $e;
            }
        } else {
            throw new \InvalidArgumentException(Constants::NULL_ARGUMENT_MESSAGE);
        } 
    }

    public function getIdBySlug(string $slug) {
        if(!empty($slug)){
            try{
                $db = Yii::$app->db;
                $query = $db->createCommand("SELECT id FROM ".$this->tableName." WHERE slug = :slug")
                    ->bindValue(":slug", $slug)
                    ->queryOne();
                return $query['id'];
            } catch (\Exception $e) {
                throw $e;
            }
        } else {
            throw new \InvalidArgumentException(Constants::NULL_ARGUMENT_MESSAGE);
        }
    }

    public function getCountOfAllCategories(int $status, bool $hideByLink) {
        if($status !== null){
            try{
                $appendix ="";
                if($hideByLink){
                    $appendix = "accessibilityStatus <> 1 AND";
                }
                $db = Yii::$app->db;
                $query = $db->createCommand("SELECT COUNT(*) FROM ".$this->tableName." WHERE ".
                    $appendix." accessibilityStatus <= :status")
                        ->bindValue(":status", $status)
                        ->queryOne();
                return (int) $query['COUNT(*)'];
            } catch (\Exception $e) {
                throw $e;
            }
        } else {
            throw new \InvalidArgumentException(Constants::NULL_ARGUMENT_MESSAGE);
        } 
    }

    public function getSlugById(int $id) {
    if($id !== null){
        try{
            $db = Yii::$app->db;
            $query = $db->createCommand("SELECT slug FROM ".$this->tableName." WHERE id = :id")
                ->bindValue(":id", $id)
                ->queryOne();
            return $query['slug'];
        } catch (\Exception $e) {
            throw $e;
        }
        } else {
            throw new \InvalidArgumentException(Constants::NULL_ARGUMENT_MESSAGE);
        }
    }

}
