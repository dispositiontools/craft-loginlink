<?php
/**
 * loginlink plugin for Craft CMS 3.x
 *
 * Log in with a link
 *
 * @link      https://www.disposition.tools
 * @copyright Copyright (c) 2022 Disposition Tools
 */

namespace dispositiontools\loginlink\migrations;

use dispositiontools\loginlink\Loginlink;

use Craft;
use craft\config\DbConfig;
use craft\db\Migration;

/**
 * loginlink Install Migration
 *
 * If your plugin needs to create any custom database tables when it gets installed,
 * create a migrations/ folder within your plugin folder, and save an Install.php file
 * within it using the following template:
 *
 * If you need to perform any additional actions on install/uninstall, override the
 * safeUp() and safeDown() methods.
 *
 * @author    Disposition Tools
 * @package   Loginlink
 * @since     1.0.0
 */
class Install extends Migration
{
    // Public Properties
    // =========================================================================

    /**
     * @var string The database driver to use
     */
    public $driver;

    // Public Methods
    // =========================================================================

    /**
     * This method contains the logic to be executed when applying this migration.
     * This method differs from [[up()]] in that the DB logic implemented here will
     * be enclosed within a DB transaction.
     * Child classes may implement this method instead of [[up()]] if the DB logic
     * needs to be within a transaction.
     *
     * @return boolean return a false value to indicate the migration fails
     * and should not proceed further. All other return values mean the migration succeeds.
     */
    public function safeUp()
    {
        $this->driver = Craft::$app->getConfig()->getDb()->driver;
        if ($this->createTables()) {
            $this->createIndexes();
            $this->addForeignKeys();
            // Refresh the db schema caches
            Craft::$app->db->schema->refresh();
            $this->insertDefaultData();
        }

        return true;
    }

    /**
     * This method contains the logic to be executed when removing this migration.
     * This method differs from [[down()]] in that the DB logic implemented here will
     * be enclosed within a DB transaction.
     * Child classes may implement this method instead of [[down()]] if the DB logic
     * needs to be within a transaction.
     *
     * @return boolean return a false value to indicate the migration fails
     * and should not proceed further. All other return values mean the migration succeeds.
     */
    public function safeDown()
    {
        $this->driver = Craft::$app->getConfig()->getDb()->driver;
        $this->removeTables();

        return true;
    }

    // Protected Methods
    // =========================================================================

    /**
     * Creates the tables needed for the Records used by the plugin
     *
     * @return bool
     */
    protected function createTables()
    {
        $tablesCreated = false;

    // loginlink_logmeinlinks table
        $tableSchema = Craft::$app->db->schema->getTableSchema('{{%loginlink_logmeinlinks}}');
        if ($tableSchema === null) {
            $tablesCreated = true;
            $this->createTable(
                '{{%loginlink_logmeinlinks}}',
                [
                    'id' => $this->primaryKey(),
                    'dateCreated' => $this->dateTime()->notNull(),
                    'dateUpdated' => $this->dateTime()->notNull(),
                    'uid' => $this->uid(),
                // Custom columns in the table
                    'siteId' => $this->integer()->notNull(),
                    'userId' => $this->integer()->defaultValue(NULL),
                    'duration' => $this->integer()->notNull(),
                    'email' => $this->string(255)->defaultValue(NULL),
                    'mobileNumber' => $this->string(255)->defaultValue(NULL),
                    'redirectUrl' => $this->string(255)->defaultValue(NULL),
                    'loginCode' => $this->string(255)->defaultValue(NULL),
                    'loggedInDate' => $this->dateTime()->defaultValue(NULL),
                    'loggedIn' => $this->boolean()->defaultValue(NULL),
                    'urlCreated' => $this->string(255)->defaultValue(NULL),
                ]
            );
        }

        return $tablesCreated;
    }

    /**
     * Creates the indexes needed for the Records used by the plugin
     *
     * @return void
     */
    protected function createIndexes()
    {


    }

    /**
     * Creates the foreign keys needed for the Records used by the plugin
     *
     * @return void
     */
    protected function addForeignKeys()
    {
    // loginlink_logmeinlinks table
        $this->addForeignKey(
            $this->db->getForeignKeyName('{{%loginlink_logmeinlinks}}', 'siteId'),
            '{{%loginlink_logmeinlinks}}',
            'siteId',
            '{{%sites}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            $this->db->getForeignKeyName('{{%loginlink_logmeinlinks}}', 'userId'),
            '{{%loginlink_logmeinlinks}}',
            'userId',
            '{{%users}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * Populates the DB with the default data.
     *
     * @return void
     */
    protected function insertDefaultData()
    {
    }

    /**
     * Removes the tables needed for the Records used by the plugin
     *
     * @return void
     */
    protected function removeTables()
    {
    // loginlink_logmeinlinks table
        $this->dropTableIfExists('{{%loginlink_logmeinlinks}}');
    }
}
