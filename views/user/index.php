<?php

/**
 * Affiche la liste des utilisateurs enregistrés dans l'application
 *
 * @var $this yii\web\View
 * @var $searchModel app\modules\user\models\search\UserSearch
 * @var $dataProvider yii\data\ActiveDataProvider
 */

use app\modules\ia\IAModule;
use app\modules\ia\widgets\DisplayModels;
use app\modules\user\lib\enums\UserStatus;
use app\modules\user\models\User;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use app\modules\user\UserModule;

$this->title = UserModule::t('labels', 'Users');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(UserModule::t('labels', 'Create User'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?= /** @noinspection PhpUnhandledExceptionInspection */
    GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'email:email',
            [
                'label' => IAModule::t('labels', 'Name'),
                'value' => function (User $model) {
                    return $model->profile->formatName();
                }
            ],
            [
                'attribute' => 'authorizationName',
                'label' => UserModule::t('labels', 'Authorizations'),
                'value' => function (User $model) {
                    return DisplayModels::widget([
                        'models' => $model->authorizations,
                        'labelField' => 'item_name',
                    ]);
                },
                'format' => 'html',
            ],
            [
                'attribute' => 'status',
                'value' => function (User $model) {
                    return UserStatus::getLabel($model->status);
                },
                'filter' => UserStatus::getList(),
            ],
            'blocked_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
