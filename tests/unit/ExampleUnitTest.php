<?php
/**
 * loginlink plugin for Craft CMS 3.x
 *
 * Log in with a link
 *
 * @link      https://www.disposition.tools
 * @copyright Copyright (c) 2022 Disposition Tools
 */

namespace dispositiontools\loginlinktests\unit;

use Codeception\Test\Unit;
use UnitTester;
use Craft;
use dispositiontools\loginlink\Loginlink;

/**
 * ExampleUnitTest
 *
 *
 * @author    Disposition Tools
 * @package   Loginlink
 * @since     1.0.0
 */
class ExampleUnitTest extends Unit
{
    // Properties
    // =========================================================================

    /**
     * @var UnitTester
     */
    protected $tester;

    // Public methods
    // =========================================================================

    // Tests
    // =========================================================================

    /**
     *
     */
    public function testPluginInstance()
    {
        $this->assertInstanceOf(
            Loginlink::class,
            Loginlink::$plugin
        );
    }

    /**
     *
     */
    public function testCraftEdition()
    {
        Craft::$app->setEdition(Craft::Pro);

        $this->assertSame(
            Craft::Pro,
            Craft::$app->getEdition()
        );
    }
}
