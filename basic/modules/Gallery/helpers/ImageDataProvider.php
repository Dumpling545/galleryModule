<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\modules\Gallery\helpers;
use app\modules\Gallery\configuration\Constants;
use app\modules\Gallery\services\abstractions;
use yii;
use yii\data\BaseDataProvider;

class ImageDataProvider extends BaseDataProvider{
    private $count;
    private $models;
    function __construct($config = array()) {
        $this->count = $config['count'];
        $this->models = $config['models'];
        $this->setPagination($config['pagination']);
    }


    protected function prepareKeys($models): array {
        return array_keys($models);
    }

    protected function prepareModels(): array {
        return $this->models;
    }

    protected function prepareTotalCount(): int {
        return $this->count;
    }

}
