<?php

namespace dispositiontools\loginlink\models;
use Craft;
use craft\base\Model;
use DateTime;
/**
 * Logmeinlinks model
 */
class Logmeinlinks extends Model
{
    
    // Public Properties
        // =========================================================================
    
        /**
     * Some model attribute
     *
     * @var int
     */
    public ?int $id = null;
    
    
    /**
     * Some model attribute
     *
     * @var int
     */
    public ?DateTime $dateCreated = null;
    
    
    /**
     * Some model attribute
     *
     * @var int
     */
    public ?DateTime $dateUpdated = null;
    
    /**
     * Some model attribute
     *
     * @var int
     */
    public ?string $uid = null;
    
    
    /**
     * Some model attribute
     *
     * @var int
     */
    public ?int $siteId = 1;
    public ?int $userId = null;
    public ?int $duration = 20 * 60;
    public ?int $loginConfigId = null;
    public ?string $email = "";
    public ?string $mobileNumber = "";
    public ?string $redirectUrl = "";
    public ?string $loginCode = "";
    public ?string $urlCreated = "";
    public ?bool $loggedIn = null;
    public ?DateTime  $loggedInDate = null;
    public ?DateTime  $dateDeleted = null;
    
    protected function defineRules(): array
    {
        return array_merge(parent::defineRules(), [
            // ...
        ]);
    }
}
