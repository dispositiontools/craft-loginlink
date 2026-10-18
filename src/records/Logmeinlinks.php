<?php

namespace dispositiontools\loginlink\records;

use Craft;
use craft\db\ActiveRecord;
use craft\db\SoftDeleteTrait;

/**
 * Logmeinlinks record
 */
class Logmeinlinks extends ActiveRecord
{
    use SoftDeleteTrait;
    
    public const tableName = '{{%loginlink_logmeinlinks}}';

    public static function tableName()
    {
        return '{{%loginlink_logmeinlinks}}';
    }
}
