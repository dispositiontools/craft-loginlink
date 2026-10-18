<?php

namespace dispositiontools\loginlink\services;

use Craft;
use yii\base\Component;

use dispositiontools\loginlink\Loginlink;

use craft\helpers\StringHelper;
use craft\helpers\UrlHelper;
use craft\helpers\App;
use dispositiontools\loginlink\models\Logmeinlinks as LogmeinlinksModel;
use dispositiontools\loginlink\records\Logmeinlinks as LogmeinlinksRecord;


use dispositiontools\loginlink\models\LoginConfigs as LoginConfigsModel; 
use dispositiontools\loginlink\records\LoginConfigs as LoginConfigsRecord; 

use craft\web\View;
use craft\mail\Message;
use DateTime;
use DateInterval;

/**
 * Login service
 */
class Login extends Component
{
    
    
    // Public Methods
        // =========================================================================
    
        /**
         * This function can literally be anything you want, and you can have as many service
         * functions as you want
         *
         * From any other plugin file, call it like this:
         *
         *     Loginlink::$plugin->login->logMeIn($code);
         *
         * @return mixed
         */
        public function logMeIn($code): array
        {
            $loginLinkModel = $this->getLoginLinkByCode( $code );
    
            if($loginLinkModel->id && $loginLinkModel->userId > 0  )
            {
    
                if( $loginLinkModel->loggedIn )
                {
                    return [
                      'success' => false,
                      'model'  => false,
                      'errorMessage' => Loginlink::getInstance()->getSettings()->usedCodeMessage,
                      'errorRedirectUrl' => Loginlink::getInstance()->getSettings()->errorRedirectUrl,
                      'errorType' => '1'
                    ];
                }
    
                $nowDate = (new \DateTime());
                $loginDateCreated= $loginLinkModel->dateCreated;
                // print_r($loginDateCreated);
                $duration = new DateInterval( 'PT'.$loginLinkModel->duration.'S' );
                $loginDateCreated->add($duration);
    
                // print_r($nowDate );
                // print_r($loginDateCreated);
    
              
                if( $nowDate > $loginDateCreated )
                {
                    return [
                      'success' => false,
                      'model'  => false,
                      'errorMessage' => Loginlink::getInstance()->getSettings()->usedCodeMessage,
                      'errorRedirectUrl' => Loginlink::getInstance()->getSettings()->errorRedirectUrl,
                      'errorType' => '3'
                    ];
                }
    
    
                  $user = Craft::$app->getUsers()->getUserById( $loginLinkModel->userId  );
                  $userSession = Craft::$app->getUser();

                   
                  $userSession->loginByUserId($user->id);
    
                  $loginLinkModel->loggedIn = true;
                  $loginLinkModel->loggedInDate = new DateTime();
                  $loginLinkModel = $this->saveLoginLink($loginLinkModel);
                  return [
                    'success' => true,
                    'model'  => $loginLinkModel
                  ];
            }
            else
            {
              return [
                'success' => false,
                'model'  => false,
                'errorMessage' => Loginlink::getInstance()->getSettings()->invalidCodeMessage,
                'errorRedirectUrl' => Loginlink::getInstance()->getSettings()->errorRedirectUrl,
                'errorType' => '2'
              ];
            }
    
    
        }
    
        // Loginlink::$plugin->login->getLoginlinks()
        public function getLoginlinks($limit = 30): array
        {
          $loginlinkRecordsQuery = LogmeinlinksRecord::find();
          $loginlinkRecordsQuery->orderBy("id DESC");
          $loginlinkRecordsQuery->limit($limit);
          $loginlinkRecords =  $loginlinkRecordsQuery->all();
    
          $models = [];
          foreach($loginlinkRecords as $loginlinkRecord)
          {
              $model = new LogmeinlinksModel($loginlinkRecord);
              $models[] = $model ;
    
          }
          return $models;
        }
    
    
        // Loginlink::$plugin->login->deleteOldLinks()
        public function deleteOldLinks(): ?bool
        {
    
          $loginlinkRecordsQuery = LogmeinlinksRecord::find();
          $loginlinkRecordsQuery->orderBy("id DESC");
          $loginlinkRecords =  $loginlinkRecordsQuery->all();
    
    
          $nowDate = (new \DateTime());
    
          foreach($loginlinkRecords as $loginlinkRecord)
          {
              
              $loginLinkModel = new LogmeinlinksModel($loginlinkRecord);
    
              $loginDateCreated= $loginLinkModel->dateCreated;
       
              $duration = new DateInterval( 'PT'.$loginLinkModel->duration.'S' );
              $loginDateCreated->add($duration);
            
              if( $nowDate > $loginDateCreated )
              {
                echo "Deleting link ".$loginLinkModel->email."...";
                $loginlinkRecord->delete();
                echo "\n";
              }
          }
          return true;
    
    
        }
    
    
    
    
    
        /**
         * This function create the Login Link model, saves it and if needed send the email.
         *
         * From any other plugin file, call it like this:
         *
         *     Loginlink::$plugin->login->createLink()
         *
         * @return mixed
         */
        public function createLink($details, $sendEmail = true): array
        {
    
          $user = false;
          if($details['email'] && $details['email'] != '')
          {
            $user = Craft::$app->users->getUserByUsernameOrEmail($details['email']);
          }

          if(!$user)
          {
            return [
              'success' => false,
              'errorMessage' => Loginlink::getInstance()->getSettings()->invalidEmailAddressMessage,
              'errorRedirectUrl' => Loginlink::getInstance()->getSettings()->errorRedirectUrl
            ];
          }

          $loginConfigId = null;
          $loginConfig = null;
          if(array_key_exists('configUid', $details) && $details['configUid'] != ''){
            $loginConfig = Loginlink::getInstance()->configs->getLoginConfigsModelByUid($details['configUid']);
            $loginConfigId = $loginConfig->id ?? null;
          }
    
    
          if(array_key_exists('redirectUrl', $details) && $details['redirectUrl'] != '')
          {
            $redirectUrl = $details['redirectUrl'];
          }elseif($loginConfig){
             $redirectUrl = $loginConfig->defaultRedirectUrl;
          }
          else {
            $redirectUrl = Loginlink::getInstance()->getSettings()->defaultRedirectUrl;
          }
    

          // 
          if($loginConfig && $loginConfig->siteId && is_int($loginConfig->siteId) && $loginConfig->siteId > 0){
            $siteId = $loginConfig->siteId;
          }
          elseif(array_key_exists('siteId', $details)  && $details['siteId'] > 0 )
          {
            $siteId = $details['siteId'];
          }
          else {
              $siteId = 1;
          }
    
    
    
            $duration = Loginlink::getInstance()->getSettings()->loginDuration;
            $loginCode = StringHelper::UUID();
    
            $loginLinkModel = new LogmeinlinksModel();
            $loginLinkModel->duration = $duration;
            $loginLinkModel->userId = $user->id;
            $loginLinkModel->email = $user->email;
            $loginLinkModel->loginConfigId = $loginConfigId;
            $loginLinkModel->redirectUrl = $redirectUrl;
            $loginLinkModel->loginCode = $loginCode;
            $loginLinkModel->siteId = $siteId;
    
            $savedLoginLinkModel = $this->saveLoginLink( $loginLinkModel );
    
    
            if($sendEmail)
            {
                  $this->sendLoginLinkEmail($savedLoginLinkModel);
            }
    
    
    
            
            return [
              'success' => true,
              'errorMessage' => false
            ];
        }
    
    
    
        /**
         * This function create the Login Link model, saves it and if needed send the email.
         *
         * From any other plugin file, call it like this:
         *
         *     Loginlink::$plugin->login->createLinkViaCp()
         *
         * @return mixed
         */
        public function createLinkViaCp($details): array
        {
    
     
    
          $user = false;
          if( $details['userForLink']  && $details['userForLink'] > 0 )
          {
            $user = Craft::$app->users->getUserById($details['userForLink']);
          }
    
          if(!$user)
          {
            return [
              'success' => false,
              'errorMessage' => Loginlink::getInstance()->getSettings()->invalidEmailAddressMessage,
              'errorRedirectUrl' => Loginlink::getInstance()->getSettings()->errorRedirectUrl
            ];
          }
    
          
    
    
          if(array_key_exists('redirectUrl', $details) && $details['redirectUrl'] != '')
          {
            $redirectUrl = $details['redirectUrl'];
          }
          elseif( $details['pageForRedirect']  && $details['pageForRedirect'] > 0 )
          {
            $element = Craft::$app->elements->getElementById($details['pageForRedirect']);
            if ( $element->uri != "" )
            {
              $redirectUrl = $element->uri;
            }
          }
          else {
            $redirectUrl = Loginlink::getInstance()->getSettings()->defaultRedirectUrl;
          }
    
          if(array_key_exists('siteId', $details)  && $details['siteId'] > 0 )
          {
            $siteId = $details['siteId'];
          }
          else {
              $siteId = 1;
          }
    
    
    
            $duration = Loginlink::getInstance()->getSettings()->loginDuration;
            $loginCode = StringHelper::UUID();
    
            $loginLinkModel = new LogmeinlinksModel();
            $loginLinkModel->duration = $duration;
            $loginLinkModel->userId = $user->id;
            $loginLinkModel->email = $user->email;
            $loginLinkModel->redirectUrl = $redirectUrl;
            $loginLinkModel->loginCode = $loginCode;
            $loginLinkModel->siteId = $siteId;
    
            $savedLoginLinkModel = $this->saveLoginLink( $loginLinkModel );
    
    
            if($details['sendEmail'])
            {
                  $this->sendLoginLinkEmail($savedLoginLinkModel);
            }
    
    
    
            
            return [
              'success' => true,
              'errorMessage' => false
            ];
        }
    
    
    
    
        public function sendLoginLinkEmail($savedLoginLinkModel): bool
        {
          

          $subject = Loginlink::getInstance()->getSettings()->emailSubject;
          $emailMessage = Loginlink::getInstance()->getSettings()->emailMessage;
          $fromEmailDetails = null;
          if ($savedLoginLinkModel->loginConfigId) {
              $loginConfig = Loginlink::getInstance()->configs->getLoginConfigsModelById($savedLoginLinkModel->loginConfigId);

              if ($loginConfig) {
                  $subject = $loginConfig->emailSubject;
                  $emailMessage = $loginConfig->emailMessage;

                  if($loginConfig->fromEmailAddress){
                    if($loginConfig->fromEmailName){
                      $fromEmailDetails = [$loginConfig->fromEmailAddress => $loginConfig->fromEmailName];
                    }else{
                      $fromEmailDetails = [$loginConfig->fromEmailAddress];
                    }
                  }
              }
          }
    

          // todo: how do I updated the urlHelper::actionUrl to take into account the siteId in $savedLoginLinkModel->siteId
          
          $loginLinkUrl  = UrlHelper::actionUrl("loginlink/logmein/log-me-in", ['code'=>$savedLoginLinkModel->loginCode]);
    
          $user = Craft::$app->users->getUserById($savedLoginLinkModel->userId);
          $loginLink = '<a href="'.$loginLinkUrl.'">'.$loginLinkUrl.'</a>';
          $variables = [
            'user'=> $user,
            'loginLink' => $loginLink,
            'loginCode' => $savedLoginLinkModel->loginCode
          ];
    
          $view = Craft::$app->getView();
          $templateMode = $view->getTemplateMode();
          $view->setTemplateMode($view::TEMPLATE_MODE_SITE);
    
          $parsedEmailMessage = $view->renderString(nl2br( $emailMessage),$variables);
          $parsedEmailSubject = $view->renderString($subject,$variables);
    
          $emailAddress = $savedLoginLinkModel->email;
        
          $this->sendMail($parsedEmailMessage, $parsedEmailSubject, $emailAddress, $fromEmailDetails );
          $view->setTemplateMode($templateMode);
    
          return true;
        }
    
        public function saveLoginLink( $model ): LogmeinlinksModel
        {
            $record = LogmeinlinksRecord::findOne(
                [
                    'id'     => $model->id,
                ]
            );
            if (!$record) {
                $record              = new LogmeinlinksRecord();
            }
            $record->setAttributes($model->getAttributes(), false);
    
            $save = $record->save();
            $model = new LogmeinlinksModel($record->getAttributes());
    
    
            return $model;
        }
    
    
    
        public function getLoginLinkByCode( $code ): LogmeinlinksModel
        {
            $record = LogmeinlinksRecord::findOne(
                [
                    'loginCode'     => $code
                ]
            );
            if (!$record) {
                $record              = new LogmeinlinksRecord();
            }
    
            $model = new LogmeinlinksModel($record->getAttributes());
    
    
            return $model;
        }
    
    
    
    
        /**
       * @param $html
       * @param $subject
       * @param null $mail
       * @param array $attachments
       * @return bool
       */
      private function sendMail($html, $subject, $mail = null, $from = null, array $attachments = array()): bool
      {
          $settings = App::mailSettings();
          $message = new Message();
          if(!$from){
            $from = [$settings->fromEmail => $settings->fromName];
          }
          $message->setFrom($from);
          $message->setTo($mail);
          $message->setSubject($subject);
          $message->setHtmlBody($html);
          if (!empty($attachments) && \is_array($attachments)) {
    
              foreach ($attachments as $fileId) {
                  if ($file = Craft::$app->assets->getAssetById((int)$fileId)) {
                      $message->attach($this->getFolderPath() . '/' . $file->filename, array(
                          'fileName' => $file->title . '.' . $file->getExtension()
                      ));
                  }
              }
          }
    
          return Craft::$app->mailer->send($message);
      }
}
