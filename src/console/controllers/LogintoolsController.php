<?php

namespace dispositiontools\loginlink\console\controllers;

use Craft;
use craft\console\Controller;
use yii\console\ExitCode;

/**
 * Logintools controller
 */
class LogintoolsController extends Controller
{
    public $defaultAction = 'index';

    public function options($actionID): array
    {
        $options = parent::options($actionID);
        switch ($actionID) {
            case 'index':
                // $options[] = '...';
                break;
        }
        return $options;
    }

    /**
     * loginlink/logintools command
     */
    public function actionIndex(): int
    {
        // ...
        return ExitCode::OK;
    }
}
