<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Authentication\AuthenticationService,	
	Zend\Authentication\Storage\Session as SessionStorage;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {		
		$e->getApplication()->getServiceManager()->get('translator');
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
		
		$eventManager->attach('route', array($this, 'checkAcl'));		
		
        /*$eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
        $eventManager->attach(MvcEvent::EVENT_DISPATCH, array($this, 'authPreDispatch'), 1);*/	
    }
	
	public function initAcl(MvcEvent $e) {
 
		$acl = new \Zend\Permissions\Acl\Acl();
		$roles = include __DIR__ . '/config/acl.config.php';
		$allResources = array();
		foreach ($roles as $role => $resources) {
	 
			$role = new \Zend\Permissions\Acl\Role\GenericRole($role);
			$acl -> addRole($role);
	 
			$allResources = array_merge($resources, $allResources);
	 
			//adding resources
			foreach ($resources as $resource) {
				 // Edit 4
				 if(!$acl ->hasResource($resource))
					$acl -> addResource(new \Zend\Permissions\Acl\Resource\GenericResource($resource));
			}
			//adding restrictions
			foreach ($allResources as $resource) {
				$acl -> allow($role, $resource);
			}
		}
		//testing
		//var_dump($acl->isAllowed('admin','home'));
		//true
	 
		//setting to view
		$e -> getViewModel() -> acl = $acl;
	 
	}
	
	public function checkAcl(MvcEvent $e) {
		$this->initAcl($e);
		$route = $e -> getRouteMatch() -> getMatchedRouteName();
		//you set your role
		$userRole = 'guest';
		
		$authService = new AuthenticationService(new SessionStorage('auth'));
		if ($authService->hasIdentity()) { 			
			$userRole = ($authService->getIdentity()['is_admin']) ? 'admin' : 'member';
		}
		//print_r($userRole); exit;
		if(!$e -> getViewModel() -> acl ->hasResource($route)){
			throw new \Exception('Resource ' . $route . ' not defined');
		}
		
		if (!$e -> getViewModel() -> acl -> isAllowed($userRole, $route)) {
			
			$response = $e -> getResponse();
			if(!$authService->hasIdentity()){			
				$url = $e->getRouter()->assemble(array('action' => 'login'), array('name' => 'login'));				
				$response->getHeaders()->addHeaderLine('Location', $url);
				$response->setStatusCode(302);
			} else {
				$response->setStatusCode(403);
			}
			
			$response->sendHeaders();
			$e->stopPropagation();
		}
	}
	
    /**
     * Authenticate user or redirect to log in
     */
    /*public function authPreDispatch($event) {

		$authService = new AuthenticationService(new SessionStorage('auth'));		
		$routeMatch = $event->getRouteMatch();
    	$controller = explode( '\\' , $routeMatch->getParam('controller') );
    	$action     = $routeMatch->getParam('action');
				
    	if( in_array( $controller[2], array('Album') ) ) {
	    	if (! $authService->hasIdentity()) {
	    		// if not log in, redirect to login page
	    		//return $this->redirect()->toUrl('/login');
	    		//- assemble redirect url
	    		$url = $event->getRouter()->assemble(array('action' => 'login'), array('name' => 'login'));

	    		$response = $event->getResponse();
	    		$response->getHeaders()->addHeaderLine('Location', $url);//echo '<pre>';print_r($response); exit;
	    		//$response->setStatusCode(302);
	    		$response->sendHeaders();
	    		$event->stopPropagation();
	    		//header('Location:/login');
	    		exit;
	    	}
    	}
    }*/

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
}
