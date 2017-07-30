<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\modules\Gallery\services\abstractions;

use app\modules\Gallery\models\image\{CreateImageModel, DeleteImageModel, GetImageModel, AdminImageItem, UpdateImageModel};
use \yii\data\Pagination;
interface IImageService {
    function getImage(int $id, int $status);
    function getImagesByAuthor(string $author, Pagination &$pagination);
    function createImage(CreateImageModel $model, string $author);
    function updateImage(UpdateImageModel $model, int $status, string $author);
    function deleteImage(int  $id, int $status, string $author);
}
