<?php

namespace dispositiontools\loginlink\migrations;

use Craft;
use craft\db\Migration;
use dispositiontools\loginlink\records\Logmeinlinks as LogmeinlinksRecord;
/**
 * Install migration.
 */
class Install extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp(): bool
    {
        $tablesCreated = false;
        $tableSchema = Craft::$app->db->schema->getTableSchema(LogmeinlinksRecord::tableName);
        if ($tableSchema === null) {
            $tablesCreated = true;
            
            
                $this->createTable(
                    LogmeinlinksRecord::tableName,
                    [
                        'id' => $this->primaryKey(),
                        // Custom columns in the table
                        'siteId' => $this->integer()->defaultValue(NULL),
                        'userId' => $this->integer()->defaultValue(NULL),
                        'loginConfigId' => $this->integer()->defaultValue(NULL),
                        'duration' => $this->integer()->notNull(),
                        'email' => $this->string(255)->defaultValue(NULL),
                        'mobileNumber' => $this->string(255)->defaultValue(NULL),
                        'redirectUrl' => $this->string(255)->defaultValue(NULL),
                        'loginCode' => $this->string(255)->defaultValue(NULL),
                        'loggedInDate' => $this->dateTime()->defaultValue(NULL),
                        'loggedIn' => $this->boolean()->defaultValue(NULL),
                        'urlCreated' => $this->string(255)->defaultValue(NULL),
                        'dateDeleted' => $this->dateTime()->null(),
                        'dateCreated' => $this->dateTime()->notNull(),
                        'dateUpdated' => $this->dateTime()->notNull(),
                        'uid' => $this->uid()->notNull(),
                    ]
                );

                if (!$this->db->tableExists('{{%loginlink_loginconfigs}}')) {
                    $this->createTable('{{%loginlink_loginconfigs}}', [
                        'id' => $this->primaryKey(),
                        'loginDuration' => $this->integer()->defaultValue(NULL),
                        'defaultRedirectUrl' => $this->string()->defaultValue(NULL),
                        'errorRedirectUrl' => $this->string()->defaultValue(NULL),
                        'notificationTemplates' => $this->string()->defaultValue(NULL),
                        'fromEmailAddress' => $this->string()->defaultValue(NULL),
                        'fromEmailName' => $this->string()->defaultValue(NULL),
                        'emailSubject' => $this->string()->defaultValue(NULL),
                        'emailMessage' => $this->text()->defaultValue(NULL),
                        'userGroups' => $this->text()->defaultValue(NULL),
                        'invalidCodeMessage' => $this->string()->defaultValue(NULL),
                        'usedCodeMessage' => $this->string()->defaultValue(NULL),
                        'timedoutCodeMessage' => $this->string()->defaultValue(NULL),
                        'invalidEmailAddressMessage' => $this->string()->defaultValue(NULL),
                        'siteId' => $this->integer()->defaultValue(NULL),
                        'userId' => $this->integer()->defaultValue(NULL),
                        'dateDeleted' => $this->dateTime()->null(),
                        'dateUpdated' => $this->dateTime()->notNull(),
                        'dateCreated' => $this->dateTime()->notNull(),
                        'uid' => $this->uid(),
                    ]);
                }
                
                $this->addForeignKey(   
                    $this->db->getForeignKeyName(LogmeinlinksRecord::tableName, 'siteId'),
                    LogmeinlinksRecord::tableName,
                    'siteId',
                    '{{%sites}}',
                    'id',
                    'CASCADE',
                    'CASCADE'
                );
                
                $this->addForeignKey(
                    $this->db->getForeignKeyName(LogmeinlinksRecord::tableName, 'userId'),
                    LogmeinlinksRecord::tableName,
                    'userId',
                    '{{%users}}',
                    'id',
                    'CASCADE',
                    'CASCADE'
                );
        }
        
        
        return $tablesCreated;
    }

    /**
     * @inheritdoc
     */
    public function safeDown(): bool
    {
        // Place uninstallation code here...
        $this->dropTableIfExists(LogmeinlinksRecord::tableName);
        return true;
    }
}
