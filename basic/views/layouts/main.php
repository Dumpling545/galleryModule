<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use app\modules\Gallery\helpers\RoleManager;
use app\modules\Gallery\configuration\Constants;
AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

    <div class="wrap" style="z-index: 2">
    <?php
    NavBar::begin([
        'brandLabel' => 'My Company',
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);
    $array = [['label' => 'Create', 'items' => [['label' => 'Image', 'url' => ['/image/create']], ['label' => 'Category', 'url' => ['/category/create']]]],
            ['label' =>'Management', 'items' =>[['label' => 'Admin page', 'url' => ['/category/admin/1']], ['label' => 'My images', 'url' => ['/image/user-page/1']]]],
            ['label' => 'All Categories', 'url' => ['/category/all/1']]];
    switch(RoleManager::getStatus(Yii::$app->user)){
        case Constants::GUEST:
            unset($array[1]);
            unset($array[0]);
            break;
        case Constants::AUTHORIZED_USER:
            unset($array[1]['items'][0]);
            break;
    }
    array_push($array, 
            Yii::$app->user->isGuest ? (
                ['label' => 'Login', 'url' => ['/site/login']]
            ) : (
                '<li>'
                . Html::beginForm(['/site/logout'], 'post')
                . Html::submitButton(
                    'Logout (' . Yii::$app->user->identity->username . ')',
                    ['class' => 'btn btn-link logout']
                )
                . Html::endForm()
                . '</li>'
            ));
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => $array
    ]);
    NavBar::end();
    ?>

    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= $content ?>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; My Company <?= date('Y') ?></p>

        <p class="pull-right"><?= Yii::powered() ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
