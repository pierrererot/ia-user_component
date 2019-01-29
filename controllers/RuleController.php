<?php

namespace app\modules\user\controllers;

use yii\web\Controller;


/**
 * Class RuleController
 * @package app\modules\user\controllers
 */
class RuleController extends Controller
{

//    /** @inheritdoc */
//    public function behaviors()
//    {
//        return [
//            [
//                'class' => AccessControl::class,
//                'rules' => [
//                    // actions réservées aux utilisateurs non authentifiés
//                    [
//                        'actions' => ['login'],
//                        'allow' => true, 'roles' => ['?'],
//                    ],
//                    [
//                        'actions' => ['logout'],
//                        'allow' => true, 'roles' => ['@'],
//                    ]
//                ]
//            ],
//        ];
//    }

    /**
     * Affiche & traite le formulaire de connexion
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

}