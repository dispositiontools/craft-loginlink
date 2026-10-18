<?php

namespace dispositiontools\loginlink\controllers;

use Craft;
use craft\web\Controller;
use dispositiontools\loginlink\Loginlink;
use dispositiontools\loginlink\models\LoginConfigs;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Cp controller
 */
class CpController extends Controller
{
    public $defaultAction = 'index';
    protected array|int|bool $allowAnonymous = self::ALLOW_ANONYMOUS_NEVER;

    /**
     * loginlink/cp action
     */
    public function actionIndex(): Response
    {
        return $this->renderTemplate('loginlink/configs/index.twig', [
            'configs' => Loginlink::getInstance()->configs->getLoginConfigsModels(),
        ]);
    }

    public function actionEdit(?int $id = null, ?LoginConfigs $config = null): Response
    {
        if ($config === null) {
            $config = $id === null
                ? new LoginConfigs()
                : Loginlink::getInstance()->configs->getLoginConfigsModelById($id);
        }

        if ($config === null) {
            throw new NotFoundHttpException('Login config not found.');
        }

        return $this->renderTemplate('loginlink/configs/edit.twig', [
            'config' => $config,
            'userGroups' => Craft::$app->userGroups->getAllGroups(),
        ]);
    }

    public function actionSave(): ?Response
    {
        $this->requirePostRequest();

        $request = Craft::$app->request;
        $id = $request->getBodyParam('id');
        $config = $id ? Loginlink::getInstance()->configs->getLoginConfigsModelById((int)$id) : new LoginConfigs();

        if ($config === null) {
            throw new NotFoundHttpException('Login config not found.');
        }

        $config->setAttributes($request->getBodyParams(), false);
        $config->userGroups = array_map('intval', (array)$request->getBodyParam('userGroups', []));

        Loginlink::getInstance()->configs->saveLoginConfigs($config);
        if ($config->hasErrors()) {
            Craft::$app->session->setError(Craft::t('loginlink', 'Couldn’t save login config.'));
            Craft::$app->urlManager->setRouteParams(['config' => $config]);
            return null;
        }

        Craft::$app->session->setNotice(Craft::t('loginlink', 'Login config saved.'));
        return $this->redirectToPostedUrl($config);
    }
}
