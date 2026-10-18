<?php

namespace dispositiontools\loginlink\models;

use Craft;
use craft\base\Model;

/**
 * Loginlink settings
 */
class Settings extends Model
{
    
    public ?string $defaultRedirectUrl = '/';
    public ?string $errorRedirectUrl = '/';
    public ?int $loginDuration = 60*30;
    public ?string $notificationTemplates = "default";
    public ?string $emailSubject = "Your login link";
    public ?string $emailMessage = "Hi,
    
    Your login link is {{ loginLink }} .
    
    thanks,
    {{ siteName }}
    ";
    public ?array $userGroups = [];
    public ?string $invalidCodeMessage = "This link is invalid";
    public ?string $usedCodeMessage = "This link has been used before. Please create a new link";
    public ?string $timedoutCodeMessage = "This link wasn't used in time. Please create a new link";
    public ?string $invalidEmailAddressMessage = "We can't find this email address. Please check and try again.";
    
    
}
