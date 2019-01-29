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

$this->title = UserModule::t('labels', 'Permissions');
$this->params['breadcrumbs'][] = $this->title;

use execut\widget\TreeView;


?>
<div class="permission-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php Pjax::begin(); ?>

    <p>
        <?= Html::a(UserModule::t('labels', 'Create Permission'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= /** @noinspection PhpUnhandledExceptionInspection */
    TreeView::widget([
        'data' => $data,
//        'size' => TreeView::SIZE_SMALL,
//        'header' => 'Categories',
        'searchOptions' => [
            'inputOptions' => [
                'placeholder' => UserModule::t('labels', 'Search permission...'),
            ],
        ],
        'clientOptions' => [
//            'selectedBackColor' => 'rgb(40, 153, 57)',
//            'borderColor' => '#fff',
        ],
    ]) ?>

    <?php Pjax::end(); ?>

</div>
