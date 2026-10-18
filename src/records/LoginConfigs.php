<?php

namespace dispositiontools\loginlink\records;

use Craft;
use craft\db\ActiveRecord;
use craft\db\SoftDeleteTrait;

/**
 * Login Configs record
 */
class LoginConfigs extends ActiveRecord
{
    use SoftDeleteTrait;

    public static function tableName()
    {
        return '{{%loginlink_loginconfigs}}';
    }
}
