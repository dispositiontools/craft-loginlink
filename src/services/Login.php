<?php
/**
 * loginlink plugin for Craft CMS 3.x
 *
 * Log in with a link
 *
 * @link      https://www.disposition.tools
 * @copyright Copyright (c) 2022 Disposition Tools
 */

namespace dispositiontools\loginlink\services;

use dispositiontools\loginlink\Loginlink;

use Craft;
use craft\base\Component;
use craft\helpers\StringHelper;
use craft\helpers\UrlHelper;
use dispositiontools\loginlink\models\Logmeinlinks as LogmeinlinksModel;
use dispositiontools\loginlink\records\Logmeinlinks as LogmeinlinksRecord;
use craft\web\View;
use craft\mail\Message;

/**
 * Login Service
 *
 * All of your plugin’s business logic should go in services, including saving data,
 * retrieving data, etc. They provide APIs that your controllers, template variables,
 * and other plugins can interact with.
 *
 * https://craftcms.com/docs/plugins/services
 *
 * @author    Disposition Tools
 * @package   Loginlink
 * @since     1.0.0
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
    public function logMeIn($code)
    {
        $loginLinkModel = $this->getLoginLinkByCode( $code );

        if($loginLinkModel->id && $loginLinkModel->userId > 0  )
        {

            if($loginLinkModel->loggedIn )
            {
                return [
                  'success' => false,
                  'model'  => false,
                  'errorMessage' => Loginlink::$plugin->getSettings()->usedCodeMessage,
                  'errorRedirectUrl' => Loginlink::$plugin->getSettings()->errorRedirectUrl
                ];
            }

              $user = Craft::$app->getUsers()->getUserById( $loginLinkModel->userId  );
              $userSession = Craft::$app->getUser();
              $userSession->loginByUserId($user->id);

              $loginLinkModel->loggedIn = true;
              $loginLinkModel->loggedInDate = date("Y-m-d H:i:s");
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
            'errorMessage' => Loginlink::$plugin->getSettings()->invalidCodeMessage,
            'errorRedirectUrl' => Loginlink::$plugin->getSettings()->errorRedirectUrl
          ];
        }


    }


    /**
     * This function can literally be anything you want, and you can have as many service
     * functions as you want
     *
     * From any other plugin file, call it like this:
     *
     *     Loginlink::$plugin->login->createLink()
     *
     * @return mixed
     */
    public function createLink($details)
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
          'errorMessage' => Loginlink::$plugin->getSettings()->invalidEmailAddressMessage,
          'errorRedirectUrl' => Loginlink::$plugin->getSettings()->errorRedirectUrl
        ];
      }


      if(array_key_exists('redirectUrl', $details) && $details['redirectUrl'] != '')
      {
        $redirectUrl = $details['redirectUrl'];
      }
      else {
        $redirectUrl = Loginlink::$plugin->getSettings()->defaultRedirectUrl;
      }

      if(array_key_exists('siteId', $details)  && $details['siteId'] > 0 )
      {
        $siteId = $details['siteId'];
      }
      else {
          $siteId = 1;
      }



        $duration = Loginlink::$plugin->getSettings()->loginDuration;
        $loginCode = StringHelper::UUID();

        $loginLinkModel = new LogmeinlinksModel();
        $loginLinkModel->duration = $duration;
        $loginLinkModel->userId = $user->id;
        $loginLinkModel->email = $user->email;
        $loginLinkModel->redirectUrl = $redirectUrl;
        $loginLinkModel->loginCode = $loginCode;
        $loginLinkModel->siteId = $siteId;

        $savedLoginLinkModel = $this->saveLoginLink( $loginLinkModel );

        $subject = Loginlink::$plugin->getSettings()->emailSubject;
        $emailMessage = Loginlink::$plugin->getSettings()->emailMessage;

        $loginLinkUrl  = UrlHelper::actionUrl("loginlink/logmein/log-me-in", ['code'=>$savedLoginLinkModel->loginCode]);


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

        $this->sendMail($parsedEmailMessage, $parsedEmailSubject, $emailAddress);


        $view->setTemplateMode($templateMode);
        return [
          'success' => true,
          'errorMessage' => false
        ];
    }



    public function saveLoginLink( $model )
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
        $model = new LogmeinlinksModel($record);


        return $model;
    }



    public function getLoginLinkByCode( $code )
    {
        $record = LogmeinlinksRecord::findOne(
            [
                'loginCode'     => $code
            ]
        );
        if (!$record) {
            $record              = new LogmeinlinksRecord();
        }

        $model = new LogmeinlinksModel($record);


        return $model;
    }




    /**
   * @param $html
   * @param $subject
   * @param null $mail
   * @param array $attachments
   * @return bool
   */
  private function sendMail($html, $subject, $mail = null, array $attachments = array()): bool
  {
      $settings = Craft::$app->systemSettings->getSettings('email');
      $message = new Message();

      $message->setFrom([$settings['fromEmail'] => $settings['fromName']]);
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
