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
 * Logmeinlinks Model
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
class Logmeinlinks extends Model
{
    // Public Properties
    // =========================================================================

    /**
 * Some model attribute
 *
 * @var int
 */
public $id;


/**
 * Some model attribute
 *
 * @var int
 */
public $dateCreated;


/**
 * Some model attribute
 *
 * @var int
 */
public $dateUpdated;

/**
 * Some model attribute
 *
 * @var int
 */
public $uid;


/**
 * Some model attribute
 *
 * @var int
 */
public $siteId = 1;

public $userId = null;

public $duration = 20 * 60;


public $email = "";
public $mobileNumber = "";
public $redirectUrl = "";
public $loginCode = "";
public $urlCreated = "";
public $loggedIn = null;
public $loggedInDate = null;

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
            ['someAttribute', 'string'],
            ['someAttribute', 'default', 'value' => 'Some Default'],
        ];
    }
}
