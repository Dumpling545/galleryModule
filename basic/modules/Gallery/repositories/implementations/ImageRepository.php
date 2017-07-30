<?php


namespace app\modules\Gallery\repositories\implementations;

use app\modules\Gallery\repositories\abstractions\IImageRepository;
use app\modules\Gallery\repositories\entities\Image;
use Yii;
use app\modules\Gallery\configuration\Constants;

class ImageRepository implements IImageRepository{
    private $tableName;
    public function __construct() {
        $this->tableName = (new \ReflectionClass(new Image()))->getShortName();
    }
    public function createImage(Image $image) {
        if(!empty($image)){
            try{
                $array = get_object_vars($image);
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

    public function deleteImage(int $id) {
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

    public function getImage(int $id, int $status) {
        if($id !== null && $status !== null){
            try{
                $db = Yii::$app->db;
                $query = $db->createCommand("SELECT * FROM ".$this->tableName." WHERE id = :imgId ".
                    "AND (accessibilityStatus <= :status OR (:status = 0 AND accessibilityStatus = 1))")
                        ->bindValue(":imgId", $id)
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

    public function getImagesByCategory(int $categoryId, int $status, int $offset, int $limit, bool $hideByLink = true) {
        if($categoryId !== null && $status !== null && $limit !== null && $offset!== null){
            try{
                $appendix = "";
                if($hideByLink)
                    $appendix = "AND accessibilityStatus <> 1";
                $db = Yii::$app->db;
                $query = $db->createCommand("SELECT * FROM ".$this->tableName." WHERE categoryId=:categoryId ".
                    $appendix." AND accessibilityStatus <= :status LIMIT :limit OFFSET :offset")
                        ->bindValue(":categoryId", $categoryId)
                        ->bindValue(":status", $status)
                        ->bindValue(":offset", $offset)
                        ->bindValue(":limit", $limit)
                        ->queryAll();
                return (array) $query;
            } catch (\Exception $e) {
                throw $e;
            }
        } else {
            throw new \InvalidArgumentException(Constants::NULL_ARGUMENT_MESSAGE);
        }
    }

    public function updateImage(Image $image) {
        if(!empty($image)){
            try{
                $db = Yii::$app->db;
                $db->createCommand()->update($this->tableName, get_object_vars($image), "id={$image->id}")->execute();
            } catch (\Exception $e) {
                throw $e;
            }
        } else {
            throw new \InvalidArgumentException(Constants::NULL_ARGUMENT_MESSAGE);
        }
    }

    public function getCountOfImagesByCategory(int $categoryId, int $status) {
        if($categoryId !== null && $status !== null){
            try{
                $db = Yii::$app->db;
                $query = $db->createCommand("SELECT COUNT(*) FROM ".$this->tableName." WHERE categoryId=:categoryId ".
                    "AND accessibilityStatus <> 1 AND accessibilityStatus <= :status")
                        ->bindValue(":categoryId", $categoryId)
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

    public function getLastImageIdInCategory(int $categoryId, int $status) {
        if($categoryId !== null && $status !== null){
            try{
                $db = Yii::$app->db;
                $query = $db->createCommand("SELECT MAX(id) FROM ".$this->tableName." WHERE categoryId=:categoryId ".
                    "AND accessibilityStatus <> 1 AND accessibilityStatus <= :status")
                        ->bindValue(":categoryId", $categoryId)
                        ->bindValue(":status", $status)
                        ->queryOne();
                return (int) $query['MAX(id)'];
            } catch (\Exception $e) {
                throw $e;
            }
        } else {
            throw new \InvalidArgumentException(Constants::NULL_ARGUMENT_MESSAGE);
        }
    }

    public function deleteImagesByCategory(int $categoryId) {
        if($categoryId !== null){
            try{
                $db = Yii::$app->db;
                $query = $db->createCommand("SELECT id FROM ".$this->tableName." WHERE categoryId = :categoryId")->
                        bindValue(":categoryId", $categoryId)->queryColumn();
                $db->createCommand()->delete($this->tableName, "categoryId = {$categoryId}")
                        ->execute();
                return $query;
            } catch (\Exception $e) {
                throw $e;
            }
        } else {
            throw new \InvalidArgumentException(Constants::NULL_ARGUMENT_MESSAGE);
        }
    }

    public function updateCategoryOfImages(int $oldCategoryId, int $newCategoryId) {
        if($oldCategoryId !== null && $newCategoryId !== null){
            try{
                $db = Yii::$app->db;
                $query = $db->createCommand()->update($this->tableName, ["categoryId" => $newCategoryId],"categoryId = ".$oldCategoryId)
                        ->execute();
            } catch (\Exception $e) {
                throw $e;
            }
        } else {
            throw new \InvalidArgumentException(Constants::NULL_ARGUMENT_MESSAGE);
        }
    }

    public function getAuthorOfImage(int $imageId) {
        if($imageId !== null){
            try{
                $db = Yii::$app->db;
                $query = $db->createCommand("SELECT author FROM ".$this->tableName." WHERE id = :imageId")
                        ->bindValue(":imageId", $imageId)
                        ->queryOne();
                return $query['author'];
            } catch (\Exception $e) {
                throw $e;
            }
        } else {
            throw new \InvalidArgumentException(Constants::NULL_ARGUMENT_MESSAGE);
        } 
    }

    public function getImagesByAuthor(string $author, int $offset, int $limit) {
        if($author !== null && $limit !== null && $offset!== null){
            try{
                $db = Yii::$app->db;
                $query = $db->createCommand("SELECT * FROM ".$this->tableName." WHERE author=:author ".
                    " LIMIT :limit OFFSET :offset")
                        ->bindValue(":author", $author)
                        ->bindValue(":offset", $offset)
                        ->bindValue(":limit", $limit)
                        ->queryAll();
                return (array) $query;
            } catch (\Exception $e) {
                throw $e;
            }
        } else {
            throw new \InvalidArgumentException(Constants::NULL_ARGUMENT_MESSAGE);
        }
    }

    public function getCountOfImagesByAuthor(string $author) {
        if($author !== null){
            try{
                $db = Yii::$app->db;
                $query = $db->createCommand("SELECT COUNT(*) FROM ".$this->tableName." WHERE author=:author")
                        ->bindValue(":author", $author)
                        ->queryOne();
                return (int)$query['COUNT(*)'];
            } catch (\Exception $e) {
                throw $e;
            }
        } else {
            throw new \InvalidArgumentException(Constants::NULL_ARGUMENT_MESSAGE);
        }
    }

}
