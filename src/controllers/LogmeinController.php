<?php

namespace dispositiontools\loginlink\controllers;

use Craft;
use craft\web\Controller;
use yii\web\Response;
use dispositiontools\loginlink\Loginlink;
use craft\elements\User as UserElement;
use craft\elements\Entry as EntryElement;
use craft\helpers\UrlHelper;

/**
 * Logmein controller
 */
class LogmeinController extends Controller
{
    public $defaultAction = 'index';
    protected array|int|bool $allowAnonymous = ['log-me-in','create-log-in-link'];

    /**
     * loginlink/logmein action
     */
    public function actionIndex(): Response
    {
        // ...
    }
    
    
    /**
     * Handle a request going to our plugin's delete all links URL,
     * e.g.: actions/loginlink/logmein/delete-old-links
     *
     * @return mixed
     */
    public function actionDeleteOldLinks(): Response
    {
        $this->requirePostRequest();
        $request = Craft::$app->request;
        
        $makeithappen = $request->post('deletelinks');
    
        if($makeithappen=="deletelinks")
        {
          Loginlink::getInstance()->login->deleteOldLinks();
        }
        
        return $this->redirectToPostedUrl();
    
    
    }
    
    
    
    
    /**
     * Handle a request going to our plugin's actionDoSomething URL,
     * e.g.: actions/loginlink/logmein/log-me-in
     *
     * @return mixed
     */
    public function actionLogMeIn()
    {
        $request = Craft::$app->request;
        $code = $request->get('code');
    
    
        $details = Loginlink::getInstance()->login->logMeIn($code);
        if($details['success'])
        {
    
          $redirectUrl  = UrlHelper::siteUrl( $details['model']->redirectUrl );
    
        }
        else
        {
          $errorRedirect = $details['errorRedirectUrl'];
    
          $redirectUrl  = UrlHelper::siteUrl( $errorRedirect, ['loginLinkErrorId'=>$details['errorType']]);
    
          
        }
    
        return $this->redirect( $redirectUrl );
    
    
    }
    
    /**
     * Handle a request going to our plugin's actionDoSomething URL,
     * e.g.: actions/loginlink/logmein/create-log-in-link
     *
     * @return mixed
     */
    public function actionCreateLogInLink()
    {
    
          $this->requirePostRequest();
          $request = Craft::$app->request;
    
          $linkDetails = [
            'email' => $request->post('email'),
            'redirectUrl' => $request->post('afterLoginRedirectUrl'),
            'configUid' => $request->post('configUid', null),
            'siteId' => Craft::$app->getSites()->getCurrentSite()->id
          ];
    
          $redirectUrl = $request->post('redirectUrl');
    
    
              $details = Loginlink::getInstance()->login->createLink($linkDetails);
    
              if($details['success'])
              {
                  return $this->redirectToPostedUrl();
              }
              else{
                // Send the account back to the template
                Craft::$app->getUrlManager()->setRouteParams([
                  'loginLinkError'=>true,
                  'loginLinkErrorMessage'=>$details['errorMessage']
                ]);
    
                return null;
                
              }
    
    
    
            return $this->redirectToPostedUrl();
    
    }
    
    /**
     * Handle a request going to our plugin's create log in link via cp URL,
     * e.g.: actions/loginlink/logmein/create-log-in-link-via-cp
     *
     * @return mixed
     */
    public function actionCreateLogInLinkViaCp()
    {
    
          $this->requirePostRequest();
          $request = Craft::$app->request;
    
          $linkDetails = [
            'userForLink' => $request->post('userForLink'),
            'redirectUrl' => $request->post('afterLoginRedirectUrl'),
            'sendEmail' => $request->post('sendEmail'),
            'pageForRedirect' => $request->post('pageForRedirect'),
          ];
    
          
    
    
              $details = Loginlink::getInstance()->login->createLinkViaCp($linkDetails);
    
              if($details['success'])
              {
                  return $this->redirectToPostedUrl();
              }
              else
              {
                // Send the account back to the template
                Craft::$app->getUrlManager()->setRouteParams([
                  'loginLinkError'=>true,
                  'loginLinkErrorMessage'=>$details['errorMessage']
                ]);
    
                return null;
                
              }
    
    
    
            return $this->redirectToPostedUrl();
    
    }

}
