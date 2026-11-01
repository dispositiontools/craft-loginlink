<?php

namespace dispositiontools\loginlink\services;

use dispositiontools\loginlink\Loginlink;
use yii\base\Component;

/**
 * Variables exposed to Twig through `craft.loginlink`.
 */
class LoginlinkVariableService extends Component
{

/*
    public function getLoginlinks(int $limit = 30): array
    {
        return Loginlink::getInstance()->login->getLoginlinks($limit);
    }

    public function getConfigs(array $criteria = []): array
    {
        return Loginlink::getInstance()->configs->getLoginConfigsModels($criteria);
    }

    public function getConfigById(int $id): ?LoginConfigs
    {
        return Loginlink::getInstance()->configs->getLoginConfigsModelById($id);
    }

    public function getConfigByUid(string $uid): ?LoginConfigs
    {
        return Loginlink::getInstance()->configs->getLoginConfigsModelByUid($uid);
    }
        */

    public function sendLoginLink(array $options): array
    {
        $details = [
            'email' => $options['emailAddress'] ?? '',
            'configUid' => $options['configUid'] ?? null,
        ];

        return Loginlink::getInstance()->login->createLink($details, true);
    }
}
