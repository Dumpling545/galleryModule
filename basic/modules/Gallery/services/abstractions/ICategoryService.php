<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\modules\Gallery\services\abstractions;

use app\modules\Gallery\models\category\{CreateCategoryModel,DeleteCategoryModel,
GetCategoryModel, UpdateCategoryModel};
use yii\data\Pagination;
interface ICategoryService {
    function getAllCategories(int $status, Pagination &$pagination, bool $hideByLink);
    function createCategory(CreateCategoryModel $model);
    function updateCategory(UpdateCategoryModel $model, int $status);
    function getCategory(string $slug, int $status, Pagination &$pagination, bool $hideByLink);
    function getCategoryInfo(string $slug, int $status);
    function deleteCategory(DeleteCategoryModel $model, int $status);
    function getCategoryNames(int $status, bool $getInfo);
    function getSlugById(int $id);
    function getImageDataProviderByCategory(string $slug, int $status, Pagination &$pagination, bool $hideByLink);

}
