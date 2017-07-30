<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\modules\Gallery\repositories\abstractions;

use app\modules\Gallery\repositories\entities\Image;
interface IImageRepository {
    function createImage(Image $image);
    function updateImage(Image $image);
    function deleteImage(int $id);
    function getImage(int $id, int $status);
    function getImagesByCategory(int $categoryId, int $status, int $offset, int $limit,bool $hideByLink);
    function getImagesByAuthor(string $author, int $offset, int $limit);
    function getCountOfImagesByCategory(int $categoryId, int $status);
    function getCountOfImagesByAuthor(string $author);
    function getLastImageIdInCategory(int $categoryId, int $status);
    function updateCategoryOfImages(int $oldCategoryId, int $newCategoryId);
    function deleteImagesByCategory(int $categoryId);
    function getAuthorOfImage(int $imageId);
}
