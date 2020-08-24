<?php
/**
 * @var View $this
 * @var string $content
 */

use app\assets\AppAsset;
use app\models\User;
use app\widgets\Alert;
use kartik\icons\Icon;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\Breadcrumbs;

AppAsset::register($this);

$this->registerJs("
$('[data-toggle=\"popover\"]').popover();
$('[data-toggle=\"tooltip\"]').tooltip();
");

Icon::map($this);

$this->beginPage();
?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?= Html::csrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
    </head>
    <body>
    <?php $this->beginBody() ?>
        <div class="wrap">
            <?php
            /** @var User $user */
            $user = Yii::$app->getUser()->getIdentity();

            NavBar::begin([
                'brandLabel' => 'Ping Logger',
                'brandUrl' => Yii::$app->homeUrl,
                'options' => [
                    'class' => 'navbar-inverse navbar-fixed-top',
                ],
            ]);

            echo Nav::widget([
                'options' => ['class' => 'navbar-nav navbar-right'],
                'items' => [
                    [
                        'label' => Icon::show('home') . Yii::t('app', 'Home'),
                        'url' => ['site/index'],
                        'visible' => (!Yii::$app->user->isGuest),
                    ],
                    [
                        'label' => Icon::show('map-marker') . Yii::t('app', 'Objects'),
                        'url' => ['object/index'],
                        'visible' => (!Yii::$app->user->isGuest),
                    ],
                    [
                        'label' => Icon::show('folder') . Yii::t('app', 'Groups'),
                        'url' => ['group/index'],
                        'visible' => (!Yii::$app->user->isGuest),
                    ],
                    Yii::$app->user->isGuest ?
                        ['label' => Icon::show('user') . Yii::t('app', 'Login'), 'url' => ['site/login']] :
                        [
                            'label' => Icon::show('power-off') . Yii::t('app', 'Logout') . ' (' . Html::encode($user->username) . ')',
                            'url' => ['site/logout'],
                            'linkOptions' => ['data-method' => 'post'],
                        ],
                ],
                'encodeLabels' => false,
            ]);
            NavBar::end();
            ?>

            <div class="container">
                <?= Breadcrumbs::widget([
                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                ]) ?>
                <?= Alert::widget() ?>
                <?= $content ?>
            </div>
        </div>

        <footer class="footer">
            <div class="container">
                <p class="pull-left">&copy; Ping Logger <?= date('Y') ?></p>
            </div>
        </footer>

        <?php $this->endBody() ?>
    </body>
</html>
<?php
$this->endPage();
