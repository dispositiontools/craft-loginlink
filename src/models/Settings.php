<?php
/**
 * loginlink plugin for Craft CMS 3.x
 *
 * Log in with a link
 *
 * @link      https://www.disposition.tools
 * @copyright Copyright (c) 2022 Disposition Tools
 */

namespace dispositiontools\loginlink\models;

use dispositiontools\loginlink\Loginlink;

use Craft;
use craft\base\Model;

/**
 * Loginlink Settings Model
 *
 * This is a model used to define the plugin's settings.
 *
 * Models are containers for data. Just about every time information is passed
 * between services, controllers, and templates in Craft, it’s passed via a model.
 *
 * https://craftcms.com/docs/plugins/models
 *
 * @author    Disposition Tools
 * @package   Loginlink
 * @since     1.0.0
 */
class Settings extends Model
{
    // Public Properties
    // =========================================================================

    /**
     * Some field model attribute
     *
     * @var string
     */
    public $defaultRedirectUrl = '/';
    public $errorRedirectUrl = '/';
    public $loginDuration = 60*30;
    public $notificationTemplates = "default";
    public $emailSubject = "Your login link";
    public $emailMessage = "Hi,

    Your login link is {{ loginLink }} .

    thanks,
    {{ siteName }}
    ";
    public $userGroups = [];
    public $invalidCodeMessage = "This code is invalid";
    public $usedCodeMessage = "This code has been used before. Please create a new code";
    public $invalidEmailAddressMessage = "We can't find this email address. Please check and try again.";

    // Public Methods
    // =========================================================================

    /**
     * Returns the validation rules for attributes.
     *
     * Validation rules are used by [[validate()]] to check if attribute values are valid.
     * Child classes may override this method to declare different validation rules.
     *
     * More info: http://www.yiiframework.com/doc-2.0/guide-input-validation.html
     *
     * @return array
     */
    public function rules()
    {
        return [
            ['defaultRedirectUrl', 'string'],
        ];
    }
}
