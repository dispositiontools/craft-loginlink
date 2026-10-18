<?php

namespace dispositiontools\loginlink;

use Craft;
use craft\base\Model;
use craft\base\Plugin;
use craft\events\RegisterComponentTypesEvent;
use craft\events\RegisterUrlRulesEvent;
use craft\services\Utilities;
use craft\web\UrlManager;
use dispositiontools\loginlink\models\Settings;
use dispositiontools\loginlink\services\Configs;
use dispositiontools\loginlink\services\Login;
use dispositiontools\loginlink\utilities\Loginlinks;
use yii\base\Event;

/**
 * Loginlink plugin
 *
 * @method static Loginlink getInstance()
 * @method Settings getSettings()
 * @author Disposition Tools <support@disposition.tools>
 * @copyright Disposition Tools
 * @license https://craftcms.github.io/license/ Craft License
 * @property-read Login $login
 * @property-read Configs $configs
 */
class Loginlink extends Plugin
{
    public string $schemaVersion = '1.0.0';
    public bool $hasCpSettings = true;
    public bool $hasCpSection = true;

    public static function config(): array
    {
        return [
            'components' => ['login' => Login::class, 'configs' => Configs::class],
        ];
    }

    public function init(): void
    {
        parent::init();

        // Defer most setup tasks until Craft is fully initialized
        Craft::$app->onInit(function(): void {
            $this->attachEventHandlers();
        });
    }

    protected function createSettingsModel(): ?Model
    {
        return Craft::createObject(Settings::class);
    }

    protected function settingsHtml(): ?string
    {
        return Craft::$app->view->renderTemplate('loginlink/_settings.twig', [
            'plugin' => $this,
            'settings' => $this->getSettings(),
        ]);
    }

    private function attachEventHandlers(): void
    {
        Event::on(UrlManager::class, UrlManager::EVENT_REGISTER_CP_URL_RULES, function(RegisterUrlRulesEvent $event): void {
            $event->rules['loginlink'] = 'loginlink/cp/index';
            $event->rules['loginlink/configs/new'] = 'loginlink/cp/edit';
            $event->rules['loginlink/configs/<id:\\d+>'] = 'loginlink/cp/edit';
        });

        Event::on(Utilities::class, Utilities::EVENT_REGISTER_UTILITY_TYPES, function(RegisterComponentTypesEvent $event): void {
            $event->types[] = Loginlinks::class;
        });
    }
}
