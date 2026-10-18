<?php

namespace dispositiontools\loginlink\services;

use Craft;
use dispositiontools\loginlink\models\LoginConfigs as LoginConfigsModel;
use dispositiontools\loginlink\records\LoginConfigs as LoginConfigsRecord;
use DateTime;
use yii\base\Component;

/**
 * Configs service
 */
class Configs extends Component
{


    //   Loginlink::getInstance()->Configs->saveLoginConfigs($model);
    public function saveLoginConfigs(LoginConfigsModel $model,  bool $runValidation = true): LoginConfigsModel
    {
        $isNew = !$model->id;

        if ($runValidation && !$model->validate()) {
            return $model;
        }

        if (!$isNew) {
            $record = LoginConfigsRecord::findOne(['id' => $model->id]);
        } else {
            $record = new LoginConfigsRecord();
        }
            
            
        $fieldsToUpdate = [
            'loginDuration', 
            'defaultRedirectUrl', 
            'errorRedirectUrl', 
            'notificationTemplates', 
            'emailSubject', 
            'emailMessage', 
            'fromEmailAddress',
            'fromEmailName',
            'userGroups', 
            'invalidCodeMessage', 
            'usedCodeMessage', 
            'timedoutCodeMessage', 
            'invalidEmailAddressMessage',
            'userId',
        ];
            
        foreach ($fieldsToUpdate as $handle) {
            if (property_exists($model, $handle)) {
                $record->$handle = $handle === 'userGroups'
                    ? json_encode($model->$handle ?? [])
                    : $model->$handle;
            }
        }
        
        if ($runValidation &&  !$record->validate()) {
            Craft::info("LoginLinks Config not saved due to validation error.");

            $model->addErrors($record->getErrors());
            return $model;
        }

        $record->save(false);

        if ($isNew) {
            $model->id = $record->id;
        }
        
        return  $model;
    }


    //   Loginlink::getInstance()->Configs->getLoginConfigsModels($criteria);
    public function getLoginConfigsModels($criteria = null): array
    {
        $recordsQuery = LoginConfigsRecord::find();

        $criteria ??= [];
        $recordsQuery->andWhere(['dateDeleted' => null]);

        if (array_key_exists("where", $criteria)) {
            $x = 0;

            foreach ($criteria["where"] as $criteriaItem => $criteriaValue) {
                $x++;
                if ($x == 1) {
                    $recordsQuery->where([$criteriaItem => $criteriaValue]);
                } else {
                    $recordsQuery->andWhere([$criteriaItem => $criteriaValue]);
                }
            }
        }
        if (array_key_exists("limit", $criteria)) {
            $recordsQuery->limit($criteria["limit"]);
        }
        if (array_key_exists("offset", $criteria)) {
            $recordsQuery->offset($criteria["offset"]);
        }

        $records = $recordsQuery->all();

        $models = [];

        foreach ($records as $record) {
            $model = $this->createModel($record);
            $models[] = $model;
        }

        return $models;
    }

    //   Loginlink::getInstance()->Configs->getLoginConfigsModelById($id);
    public function getLoginConfigsModelById($id): ?LoginConfigsModel
    {
        $record = LoginConfigsRecord::findOne(["id" => $id, 'dateDeleted' => null]);

        if (!$record) {
            return null;
        }

        return $this->createModel($record);
    }

     //   Loginlink::getInstance()->Configs->getLoginConfigsModelByUid($uid);
    public function getLoginConfigsModelByUid($uid): ?LoginConfigsModel
    {
        $record = LoginConfigsRecord::findOne(["uid" => $uid]);

        if (!$record) {
            return null;
        }

        return $this->createModel($record);
    }

    private function createModel(LoginConfigsRecord $record): LoginConfigsModel
    {
        $attributes = $record->getAttributes();
        $attributes['userGroups'] = json_decode($attributes['userGroups'] ?: '[]', true) ?: [];
        foreach (['dateDeleted', 'dateUpdated', 'dateCreated'] as $attribute) {
            if (is_string($attributes[$attribute] ?? null)) {
                $attributes[$attribute] = new DateTime($attributes[$attribute]);
            }
        }
        return new LoginConfigsModel($attributes);
    }

}
