<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\modules\Gallery\repositories\abstractions;

use app\modules\Gallery\repositories\entities\Category;
interface ICategoryRepository {
    function createCategory(Category $category);
    function updateCategory(Category $category);
    function getCategory(int $id, int $status);
    function deleteCategory(int $id);
    function getAllCategories(int $status, int $offset, int $limit, bool $hideByLink);
    function getIdBySlug(string $slug);
    function getSlugById(int $id);
    function getCountOfAllCategories(int $status, bool $hideByLink);  
}
