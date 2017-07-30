<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\modules\Gallery\helpers;

use yii\web\User;
use app\modules\Gallery\configuration\Constants;
class RoleManager {
    static function getStatus(User $user){
        $status = -1;
        if($user->isGuest){
            $status = Constants::GUEST;
        } else {
            if($user->identity->username == "admin"){
                $status = Constants::ADMIN;
            } else {
                $status = Constants::AUTHORIZED_USER;
            }
        }
        return $status;
    }
}
