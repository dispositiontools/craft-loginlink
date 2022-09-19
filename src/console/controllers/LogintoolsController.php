<?php
/**
 * loginlink plugin for Craft CMS 3.x
 *
 * Log in with a link
 *
 * @link      https://www.disposition.tools
 * @copyright Copyright (c) 2022 Disposition Tools
 */

namespace dispositiontools\loginlink\console\controllers;

use dispositiontools\loginlink\Loginlink;

use Craft;
use yii\console\Controller;
use yii\helpers\Console;

/**
 * Logintools Command
 *
 * The first line of this class docblock is displayed as the description
 * of the Console Command in ./craft help
 *
 * Craft can be invoked via commandline console by using the `./craft` command
 * from the project root.
 *
 * Console Commands are just controllers that are invoked to handle console
 * actions. The segment routing is plugin-name/controller-name/action-name
 *
 * The actionIndex() method is what is executed if no sub-commands are supplied, e.g.:
 *
 * ./craft loginlink/logintools
 *
 * Actions must be in 'kebab-case' so actionDoSomething() maps to 'do-something',
 * and would be invoked via:
 *
 * ./craft loginlink/logintools/do-something
 *
 * @author    Disposition Tools
 * @package   Loginlink
 * @since     1.0.0
 */
class LogintoolsController extends Controller
{
    // Public Methods
    // =========================================================================

    /**
     * Handle loginlink/logintools console commands
     *
     * The first line of this method docblock is displayed as the description
     * of the Console Command in ./craft help
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $result = 'something';

        echo "Welcome to the console LogintoolsController actionIndex() method\n";

        return $result;
    }

    /**
     * Handle loginlink/logintools/do-something console commands
     *
     * The first line of this method docblock is displayed as the description
     * of the Console Command in ./craft help
     *
     * @return mixed
     */
    public function actionDoSomething()
    {
        $result = 'something';

        echo "Welcome to the console LogintoolsController actionDoSomething() method\n";

        return $result;
    }
}
