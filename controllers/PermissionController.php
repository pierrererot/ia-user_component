<?php

namespace app\modules\user\controllers;

use app\modules\ia\IAModule;
use app\modules\ia\lib\DisplayableException;
use app\modules\ia\lib\Flash;
use app\modules\ia\lib\TreeNode;
use app\modules\user\lib\enums\AuthItemType;
use app\modules\user\models\AuthItem;
use app\modules\user\widgets\AuthItemNodeDisplay;
use Exception;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Controller;


/**
 * Class PermissionController
 * @package app\modules\user\controllers
 */
class PermissionController extends Controller
{

    /** @inheritdoc */
    public function behaviors()
    {
        return [
            [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['managePrivileges'],
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
        // On construit l'arbre des droits à partir des droits racine
        $tree = new TreeNode();
        /** @var AuthItem $model */
        $model = Yii::createObject('user/AuthItem');
        foreach ($model->find()->rootPermissions()->all() as $item) {
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
     * Ajout d'un droit
     *
     * @return string|\yii\web\Response
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function actionCreate()
    {
        /** @var AuthItem $model */
        $model = Yii::createObject('user/AuthItem');

        if (Yii::$app->request->isPost) {
            // Traitement du formulaire
            $transaction = Yii::$app->db->beginTransaction();
            try {
                if (!$model->load(Yii::$app->request->post())) {
                    throw new DisplayableException(IAModule::t('messages', 'Load error'));
                }

                // On ajoute les infos manquantes
                $model->type = AuthItemType::PERMISSION;
                $model->created_at = time();
                $model->updated_at = time();

                // Validation & sauvegarde
                if (!$model->validate()) {
                    throw new DisplayableException(IAModule::t('messages', 'Validation error'));
                }

                if (!$model->save(false)) {
                    throw new DisplayableException(IAModule::t('messages', 'Create error'));
                }

                //
                $newParent = ArrayHelper::getValue(Yii::$app->request->post($model->formName()), 'parentPermission');
                if (!$this->updateParentPermission($model, $newParent)) {
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
        /** @var AuthItem $parent */
        $parents = [];
        foreach (Yii::createObject('user/AuthItem')->find()->rootRoles()->all() as $parent) {
            $parents = array_merge($parents, $parent->getTreeNode()->inDepth());
        }

        ArrayHelper::multisort($parents, 'name');
        return $this->render('create', [
            'model' => $model,
            'parents' => $parents,
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
        /** @var AuthItem $model */
        $authItem = Yii::createObject('user/AuthItem');
        $model = $authItem->findOne($id);

        if (Yii::$app->request->isPost) {
            // Traitement du formulaire
            $transaction = Yii::$app->db->beginTransaction();
            try {
                if (!$model->load(Yii::$app->request->post())) {
                    throw new DisplayableException(IAModule::t('messages', 'Load error'));
                }

                // On ajoute les infos manquantes
                $model->updated_at = time();

                // Validation & sauvegarde
                if (!$model->validate()) {
                    throw new DisplayableException(IAModule::t('messages', 'Validation error'));
                }

                if (!$model->save(false)) {
                    throw new DisplayableException(IAModule::t('messages', 'Update error'));
                }

                //
                $newParent = ArrayHelper::getValue(Yii::$app->request->post($model->formName()), 'parentPermission');
                if (!$this->updateParentPermission($model, $newParent)) {
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
        /** @var AuthItem $formParent */
        $parents = [];
        foreach ($authItem->find()->rootPermissions()->all() as $formParent) {
            $parents = array_merge($parents, $formParent->getTreeNode()->inDepth());
        }

        ArrayHelper::multisort($parents, 'name');
        return $this->render('update', [
            'model' => $model,
            'parents' => $parents,
        ]);

    }

    /////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * @param TreeNode $node
     * @throws Exception
     */
    private function mapTreeText(TreeNode $node)
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $node->id = $node->data ? AuthItemNodeDisplay::widget(['model' => $node->data]) : null;
        foreach ($node->children as $child) {
            $this->mapTreeText($child);
        }
    }

    /**
     * @param AuthItem $model
     * @param string $newParentName
     * @return bool
     * @throws \yii\base\Exception
     */
    private function updateParentPermission(AuthItem $model, $newParentName)
    {
        $oldParentName = $model->getParentPermissionName();
        if ($newParentName == $oldParentName) {
            // RAF
            return true;
        }

        $modelItem = $model->asRbacItem();
        $auth = Yii::$app->getAuthManager();

        // On supprime l'association avec l'ancien parent
        if ($oldParentName) {
            $oldParent = $auth->getPermission($oldParentName);
            if (!$oldParent) {
                Yii::error("Permission introuvable : $oldParentName");
                return false;
            }
            $auth->removeChild($oldParent, $modelItem);
        }

        // On crée la nouvelle association
        if ($newParentName) {
            $newParent = $auth->getPermission($newParentName);
            if (!$newParent) {
                Yii::error("Permission introuvable : $newParentName");
                return false;
            }

            $auth->addChild($newParent, $modelItem);
        }

        return true;
    }

}