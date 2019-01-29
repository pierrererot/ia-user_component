<?php

/**
 * @var $this yii\web\View
 * @var $data array
 */

// Format attendu pour le TreeView :
//        $data = [
//            [
//                'text' => 'Parent 1',
//                'nodes' => [
//                    [
//                        'text' => 'Child 1',
//                        'nodes' => [
//                            [
//                                'text' => 'Grandchild 1'
//                            ],
//                            [
//                                'text' => 'Grandchild 2'
//                            ]
//                        ]
//                    ],
//                    [
//                        'text' => 'Child 2',
//                    ]
//                ],
//            ],
//            [
//                'text' => 'Parent 2',
//            ]
//        ];


use yii\helpers\Html;
use yii\widgets\Pjax;
use app\modules\user\UserModule;

$this->title = UserModule::t('labels', 'Roles');
$this->params['breadcrumbs'][] = $this->title;

use execut\widget\TreeView;


?>
<div class="permission-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(UserModule::t('labels', 'Create Role'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?= /** @noinspection PhpUnhandledExceptionInspection */
    TreeView::widget([
        'data' => $data,
//        'size' => TreeView::SIZE_SMALL,
//        'header' => 'Categories',
        'searchOptions' => [
            'inputOptions' => [
                'placeholder' => UserModule::t('labels', 'Search role...'),
            ],
        ],
        'clientOptions' => [
//            'selectedBackColor' => 'rgb(40, 153, 57)',
//            'borderColor' => '#fff',
        ],
    ]) ?>
    <?php Pjax::end(); ?>

</div>
