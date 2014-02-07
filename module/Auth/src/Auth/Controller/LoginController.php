<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Auth\Controller;


use Zend\Mvc\Controller\AbstractActionController,
    Zend\Authentication\Adapter\DbTable,
    Zend\Session\Container as SessionContainer,
    Zend\View\Model\ViewModel,
    Auth\Model\User,
    Auth\Form\Login,
	ZendOAuth\Google\OAuth\OAuth,
    Auth\Auth\Adapter\Twitter as AuthTwitter;

class LoginController extends AbstractActionController
{
	protected $userTable;
	protected $_config;

    public function __construct() {
        $this->_config = array(
            "approval_prompt" => "force",
            "client_id" => "267794622312.apps.googleusercontent.com",
            "client_secret" => "qIWt5DIQ1ynQTiHRacuHqSfU",
            "redirect_uri" => "http://zendsite.lcl/login/googlecallback",
            "scope" => array(
                "https://www.googleapis.com/auth/userinfo.email",
                "https://www.googleapis.com/auth/userinfo.profile",
            )
        );
    }
	
    public function loginAction()
    {
            $authService = $this->serviceLocator->get('auth_service');
            if ($authService->hasIdentity()) {
                    // if not log in, redirect to login page
                    return $this->redirect()->toUrl('/album');
            }

            $form = new Login;
            $loginMsg = array();
            if ($this->getRequest()->isPost()) {
                    $form->setData($this->getRequest()->getPost());
                    if (! $form->isValid()) {
                            // not valid form
                            return new ViewModel(array(
                                                                            'title' => 'Log In',
                                                                            'form'  => $form
                                                                    ));
                    }

                    $dbAdapter = $this->serviceLocator->get('Zend\Db\Adapter\Adapter');
                    $loginData = $form->getData();
                    $authAdapter = new DbTable($dbAdapter, 'user', 'username', 'password', 'MD5(?)');
                    $authAdapter->setIdentity($loginData['username'])
                                    ->setCredential($loginData['password']);
                        $authService = $this->serviceLocator->get('auth_service');
                        $authService->setAdapter($authAdapter);


                    $result = $authService->authenticate();
                    if ($result->isValid()) {
                // set id as identifier in session				
                //$userId = $authAdapter->getResultRowObject('id')->id;
				$username = ( $authAdapter->getResultRowObject('username')->username ) ? $authAdapter->getResultRowObject('username')->username : $authAdapter->getResultRowObject('twitter')->twitter;
				
				$sessionData = array(
								'user_id' => $authAdapter->getResultRowObject('id')->id,
								'user_name' => $username,
								'is_admin' => $authAdapter->getResultRowObject('is_admin')->is_admin);
								
                $authService->getStorage()
                            ->write($sessionData);
                            return $this->redirect()->toUrl('/album');
                    } else {
                            $loginMsg = $result->getMessages();
                    }
            }

        return new ViewModel(array('title' => 'Log In',
                                     'form'  => $form,
                                                                 'loginMsg' => $loginMsg
                                     ));
    }

    public function logoutAction()
    {
            $authService = $this->serviceLocator->get('auth_service');
            if (! $authService->hasIdentity()) {
                    // if not log in, redirect to login page
                    return $this->redirect()->toUrl('/login');
            }

            $authService->clearIdentity();
            $form = new Login();
            $viewModel = new ViewModel(array('loginMsg' => array('You have been logged out'),
                                                                              'form'  => $form,
                                                                              'title' => 'Log out'
                                                                            ));
            $viewModel->setTemplate('auth/login/login.phtml');
            return $viewModel;
    }

    public function twitterAction()
    {
        $consumer = $this->serviceLocator->get('twitter_oauth');
        $token   = $consumer->getRequestToken();
        $session = new SessionContainer('twitter_oauth');
        $session->requestToken = serialize($token);
        $consumer->redirect();
    }

	public function googleAction()
    {
		/*$consumer = $this->serviceLocator->get('google_oauth');
		$token   = $consumer->getRequestToken();
		$session = new SessionContainer('google_oauth');
        $session->requestToken = serialize($token);
        $consumer->redirect();*/
        $googleAuth = new OAuth($this->_config);
        $googleAuth->getRequestToken();
    }
	
    public function twitterCallbackAction()
    {
            $session  = new SessionContainer('twitter_oauth');
            $consumer = $this->serviceLocator->get('twitter_oauth');
            try {
                    // get access token
                    $token = $consumer->getAccessToken($this->params()->fromQuery(),
                                                       unserialize($session->requestToken));
            $userTable = $this->getUserTable();
            try {
                // get user by twitter username
                $user = $userTable->getUserByTwitter($token->getParam('screen_name'));
                $userId = $user->id;
            } catch (\Exception $e) {
                // create new user with empty username & password
                $data = array('username' => '',
                              'password' => '',
                              'twitter'  => $token->getParam('screen_name')
                             );
                $user = new User();
                $user->exchangeArray($data);
                $userTable->saveUser($user);
                $userId = $userTable->getLastInsertUserId();
            }

                $authService = $this->serviceLocator->get('auth_service');
                // get session storage
                $storage = $authService->getStorage();
                // write to session storage
                $storage->write($userId);
                    return $this->redirect()->toUrl('/album');
            } catch (\ZendOauth\Exception\InvalidArgumentException $e) {
                    // if there is error when get access token
                    $form = new Login();
                    $viewModel = new ViewModel(array('loginMsg' => array($e->getMessage()),
                                                                                      'form'  => $form,
                                                                                      'title' => 'Twitter Sign In'
                                                                                    ));
                    $viewModel->setTemplate('auth/login/login.phtml');
                    return $viewModel;
            }
    }
	
	public function googleCallbackAction() {
	
	 $requestToken = (String) $this->params()->fromQuery('code', 0);
        $googleAuth = new OAuth($this->_config, array("sslverifypeer" => false));
        $response = $googleAuth->getAccessToken($requestToken);
		
		/*$session  = new SessionContainer('twitter_oauth');
        $consumer = $this->serviceLocator->get('twitter_oauth');
		
        $requestToken = (String) $this->params()->fromQuery('code', 0);
        //$googleAuth = new OAuth($this->_config, array("sslverifypeer" => false));
		try {
			//get access token
			$token = $consumer->getAccessToken($requestToken); print_r($token); exit;
			$userTable = $this->getUserTable();
			try {
					// get user by twitter username
					$user = $userTable->getUserByGoogle($token->getParam('screen_name'));
					$userId = $user->id;
				} catch (\Exception $e) {
					// create new user with empty username & password
					$data = array('username' => '',
								  'password' => '',
								  'twitter'  => $token->getParam('screen_name')
								 );
					$user = new User();
					$user->exchangeArray($data);
					$userTable->saveUser($user);
					$userId = $userTable->getLastInsertUserId();
				}

			$authService = $this->serviceLocator->get('auth_service');
			// get session storage
			$storage = $authService->getStorage();
			// write to session storage
			$storage->write($userId);
				return $this->redirect()->toUrl('/album');
		} catch (\ZendOauth\Exception\InvalidArgumentException $e) { echo '<pre>'; print_r($e); exit;
				// if there is error when get access token
				$form = new Login();
				$viewModel = new ViewModel(array('loginMsg' => array($e->getMessage()),
																				  'form'  => $form,
																				  'title' => 'Twitter Sign In'
																				));
				$viewModel->setTemplate('auth/login/login.phtml');
				return $viewModel;
		}*/	
		
        // echo $response->getAccessToken();
        // echo $response->getRefreshToken();
    }

        public function getUserTable()
    {
        if (!$this->userTable) {
            $sm = $this->getServiceLocator();
            $this->userTable = $sm->get('Auth\Model\UserTable');
        }

        return $this->userTable;
    }
}