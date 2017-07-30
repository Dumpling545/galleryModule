<?php


namespace app\modules\Gallery\configuration;

use Yii;
 class Constants {    

    const ALLOWED_TO_ALL = 0;
    const ALLOWED_BY_LINK = 1;
    const ALLOWED_TO_AUTHORIZED_USERS = 2;
    const ALLOWED_TO_ADMINS = 3;
    
    const GUEST = 0;
    const AUTHORIZED_USER = 2;
    const ADMIN = 3;
    
    const DELETE_IMAGES_AFTER_CATEGORY_DELETION = 10;
    const CHANGE_CATEGORY_AFTER_CATEGORY_DELETION = 11;
    
    const OK = 200;
    const BAD_REQUEST = 400;
    const FORBIDDEN = 403;
    
    const NULL_ARGUMENT_MESSAGE = 'Argument(s) cannot be empty';
    const NON_SUPPORTED_EXTENSION_MESSAGE = 'Extension is not supported';
    const INVALID_ARGUMENT_MESSAGE = 'Invalid argument(s)';
    const ERROR_ON_HANDLING_IMAGE_MESSAGE = 'Unknown error occured while handling image on server';
    const NOT_ALLOWED_MESSAGE = 'You are not allowed to do this action';
    const INVALID_MODEL_MESSAGE = 'Something wrong with data sent to the server';
    
    const IMAGE_PATH = '@app/images/photogallery';

    const WATERMARK = 'localhost:9000';
    
    const WM_TOP_LEFT = 100;
    const WM_TOP_RIGHT = 101;
    const WM_BOTTOM_LEFT = 102;
    const WM_BOTTOM_RIGHT = 103;
    const WM_NOWHERE = 104;
    
    const SIZE_SMALL = 640;
    const SIZE_MEDIUM = 1440;
    
    const ITEM_WIDTH_IN_PERCENTS_MEDIUM = 0.188;
    const ITEM_WIDTH_IN_PERCENTS_BIG = 0.094;
    const QUALITY = 100;
}
