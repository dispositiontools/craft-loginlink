<?php
/**
 * loginlink plugin for Craft CMS 3.x
 *
 * Log in with a link
 *
 * @link      https://www.disposition.tools
 * @copyright Copyright (c) 2022 Disposition Tools
 */

namespace dispositiontools\loginlink\controllers;

use dispositiontools\loginlink\Loginlink;

use Craft;
use craft\web\Controller;

/**
 * Logmein Controller
 *
 * Generally speaking, controllers are the middlemen between the front end of
 * the CP/website and your plugin’s services. They contain action methods which
 * handle individual tasks.
 *
 * A common pattern used throughout Craft involves a controller action gathering
 * post data, saving it on a model, passing the model off to a service, and then
 * responding to the request appropriately depending on the service method’s response.
 *
 * Action methods begin with the prefix “action”, followed by a description of what
 * the method does (for example, actionSaveIngredient()).
 *
 * https://craftcms.com/docs/plugins/controllers
 *
 * @author    Disposition Tools
 * @package   Loginlink
 * @since     1.0.0
 */
class LogmeinController extends Controller
{

    // Protected Properties
    // =========================================================================

    /**
     * @var    bool|array Allows anonymous access to this controller's actions.
     *         The actions must be in 'kebab-case'
     * @access protected
     */
    protected $allowAnonymous = ['index', 'log-me-in','create-log-in-link'];

    // Public Methods
    // =========================================================================

    /**
     * Handle a request going to our plugin's index action URL,
     * e.g.: actions/loginlink/logmein
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $result = 'Nope!';

        return $result;
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


        $details = Loginlink::$plugin->login->logMeIn($code);
        if($details['success'])
        {
          return $this->redirect($details['model']->redirectUrl);
        }
        else{


            return $this->redirect($details['errorRedirectUrl']."?logInLinkError=".urlencode($details['errorMessage'])  );
        }


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
            'redirectUrl' => $request->post('afterLoginRedirectUrl')
          ];

          $redirectUrl = $request->post('redirectUrl');


              $details = Loginlink::$plugin->login->createLink($linkDetails);

              if($details['success'])
              {
                  return $this->redirectToPostedUrl();
              }
              else{

                  return $this->redirect($details['errorRedirectUrl']."?logInLinkError=".urlencode($details['errorMessage'])  );
              }
      


            return $this->redirectToPostedUrl();

    }
}
