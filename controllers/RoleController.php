<?php

namespace app\modules\user\controllers;

use app\modules\ia\IAModule;
use app\modules\ia\lib\DisplayableException;
use app\modules\ia\lib\Flash;
use app\modules\ia\lib\TreeNode;
use app\modules\user\lib\enums\AuthItemType;
use app\modules\user\models\AuthItem;
use app\modules\user\models\form\AuthItemForm;
use app\modules\user\widgets\AuthItemNodeDisplay;
use Exception;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;


/**
 * Class RoleController
 * @package app\modules\user\controllers
 */
class RoleController extends Controller
{

    /** @inheritdoc */
    public function behaviors()
    {
        return [
            [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true, 'roles' => ['managePrivileges'],
                    ]
                ]
            ],
        ];
    }

    /**
     * Affiche & traite le formulaire de connexion
     *
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function actionIndex()
    {
        $tree = new TreeNode();
        /** @var AuthItem $item */
        $model = Yii::createObject('user/AuthItem');
        foreach ($model->find()->rootRoles()->all() as $item) {
            $tree->addChild($item->getTreeNode());
        }

        $keysMap = ['id' => 'text', 'children' => 'nodes', 'data' => null];
        /** @noinspection PhpUnhandledExceptionInspection */
        $this->mapTreeText($tree);
        return $this->render('index', [
            'data' => $tree->asArray($keysMap)['nodes'],
        ]);
    }

    /**
     * Ajout d'un rôle
     *
     * @return string|\yii\web\Response
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function actionCreate()
    {
        /** @var AuthItemForm $model */
        /** @var AuthItem $authItem */
        $model = Yii::createObject('user/AuthItemForm');
        $authItem = Yii::createObject('user/AuthItem');
        $authItem->type = AuthItemType::ROLE;
        $model->loadAuthItem($authItem);

        if (Yii::$app->request->isPost) {
            // Traitement du formulaire
            $transaction = Yii::$app->db->beginTransaction();
            try {
                if (!$model->load(Yii::$app->request->post())) {
                    throw new DisplayableException(IAModule::t('messages', 'Load error'));
                }

                // Validation & sauvegarde
                if (!$model->validate()) {
                    throw new DisplayableException(IAModule::t('messages', 'Validation error'));
                }

                if (!$model->create(false)) {
                    throw new DisplayableException(IAModule::t('messages', 'Create error'));
                }

                $transaction->commit();

                // Tout s'est bien passé, on revient sur la page d'index
                return $this->redirect(['index']);
            } catch (DisplayableException $x) {
                Flash::error($x->getMessage());
            } catch (Exception $x) {
                Flash::error(IAModule::t('messages', 'Server error'));
                Yii::error($x->getMessage());
            }

            $transaction->rollBack();
        }

        // Affichage initial ou ré-affichage après erreur de validation
        return $this->render('create', [
            'model' => $model,
            'permissions' => AuthItem::find()->permissions()->all(),
        ]);
    }

    /**
     * Modification d'un droit
     *
     * @param string $id identifiant du droit (colonne 'name' de la table auth_item)
     * @return string|\yii\web\Response
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function actionUpdate($id)
    {
        /** @var AuthItemForm $model */
        $model = Yii::createObject('user/AuthItemForm');
        $model->loadAuthItem(Yii::createObject('user/AuthItem')::findOne($id));

        if (Yii::$app->request->isPost) {
            // Traitement du formulaire
            $transaction = Yii::$app->db->beginTransaction();
            try {
                if (!$model->load(Yii::$app->request->post())) {
                    throw new DisplayableException(IAModule::t('messages', 'Load error'));
                }

                // Validation & sauvegarde
                if (!$model->validate()) {
                    throw new DisplayableException(IAModule::t('messages', 'Validation error'));
                }

                if (!$model->update(false)) {
                    throw new DisplayableException(IAModule::t('messages', 'Update error'));
                }

                $transaction->commit();

                // Tout s'est bien passé, on revient sur la page d'index
                return $this->redirect(['index']);
            } catch (DisplayableException $x) {
                Flash::error($x->getMessage());
            } catch (Exception $x) {
                Flash::error(IAModule::t('messages', 'Server error'));
                Yii::error($x->getMessage());
            }

            $transaction->rollBack();
        }

        // Affichage initial ou ré-affichage après erreur de validation
        return $this->render('update', [
            'model' => $model,
            'permissions' => AuthItem::find()->all(),
        ]);

    }

    ////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////

    /**
     * @param TreeNode $node
     * @throws \Exception
     */
    private function mapTreeText(TreeNode $node)
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $node->id = $node->data ? AuthItemNodeDisplay::widget(['model' => $node->data, 'managePermissions' => false]) : null;
        foreach ($node->children as $child) {
            $this->mapTreeText($child);
        }
    }

}