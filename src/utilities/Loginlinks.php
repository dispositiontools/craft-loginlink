<?php

namespace dispositiontools\loginlink\utilities;

use dispositiontools\loginlink\Loginlink;
use dispositiontools\loginlink\assetbundles\loginlinksutility\LoginlinksUtilityAsset;

use Craft;
use craft\base\Utility;

/**
 * Loginlinks utility
 */
class Loginlinks extends Utility
{
    public static function displayName(): string
    {
        return Craft::t('loginlink', 'Loginlinks');
    }

    static function id(): string
    {
        return 'loginlinks';
    }

    public static function iconPath(): ?string
    {
        return Craft::getAlias("@dispositiontools/loginlink/assetbundles/loginlinksutility/dist/img/Loginlinks-icon.svg");
    }

    public static function badgeCount(): int
    {
        return 0;
    }

    /**
     * Returns the utility's content HTML.
     *
     * @return string
     */
    public static function contentHtml(): string
    {
        Craft::$app->getView()->registerAssetBundle(LoginlinksUtilityAsset::class);

        $someVar = 'Have a nice day!';
        return Craft::$app->getView()->renderTemplate(
            'loginlink/_components/utilities/Loginlinks_content',
            [
                'someVar' => $someVar
            ]
        );
    }
}
