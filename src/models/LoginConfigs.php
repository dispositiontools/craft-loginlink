<?php

namespace dispositiontools\loginlink\models;

use Craft;
use craft\base\Model;
use DateTime;

/**
 * Login Configs model
 */
class LoginConfigs extends Model
{

    public ?int $id = null;
    public ?int $loginDuration = null;
    public ?string $defaultRedirectUrl = null;
    public ?string $errorRedirectUrl = null;
    public ?string $notificationTemplates = null;
    public ?string $emailSubject = null;
    public ?string $emailMessage = null;
    public ?string $fromEmailAddress = null;
    public ?string $fromEmailName = null;
    public ?array $userGroups = null;
    public ?int $siteId = null;
    public ?string $invalidCodeMessage = null;
    public ?string $usedCodeMessage = null;
    public ?string $timedoutCodeMessage = null;
    public ?string $invalidEmailAddressMessage = null;
    public ?int $userId = null;
    public ?DateTime $dateDeleted = null;
    public ?DateTime $dateUpdated = null;
    public ?DateTime $dateCreated = null;
    public ?string $uid = null;

    protected function defineRules(): array
    {
        return array_merge(parent::defineRules(), [
            [['loginDuration'], 'integer', 'min' => 1],
            [['defaultRedirectUrl', 'errorRedirectUrl', 'notificationTemplates', 'emailSubject', 'fromEmailAddress', 'fromEmailName'], 'string', 'max' => 255],
            [['emailMessage', 'invalidCodeMessage', 'usedCodeMessage', 'timedoutCodeMessage', 'invalidEmailAddressMessage'], 'string'],
            [['userGroups'], 'each', 'rule' => ['integer']],
        ]);
    }
}
