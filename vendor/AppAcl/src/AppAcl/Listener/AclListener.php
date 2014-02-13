<?php
namespace AppAcl\Listener;

use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\MvcEvent;

use AppAcl\Service\Acl as AppAcl;
use AppAcl\Model\Resource as AppAclResource;
use AppAcl\Model\Role as AppAclRole;
use AppAcl\Model\Rule as AppAclRule;

use Zend\Authentication\AuthenticationService,	
	Zend\Authentication\Storage\Session as SessionStorage;

class AclListener implements  ListenerAggregateInterface
{
	protected $_listeners = array();

	/**
	 * Attach one or more listeners
	 *
	 * Implementors may add an optional $priority argument; the EventManager
	 * implementation will pass this to the aggregate.
	 *
	 * @param EventManagerInterface $events
	 */
	public function attach(EventManagerInterface $events)
	{
		$this->_listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH, array($this, 'initAcl'), 100);
	}

	/**
	 * Detach all previously attached listeners
	 *
	 * @param EventManagerInterface $events
	 */
	public function detach(EventManagerInterface $events)
	{
		foreach ($this->_listeners as $index => $listener) {
			if ($events->detach($listener)) {
				unset($this->_listeners[$index]);
			}
		}
	}

	public function initAcl(MvcEvent $e)
	{
		/* @var \Zend\Mvc\Application $app */
		$app = $e->getApplication();

		// Get SM
		$sm = $app->getServiceManager();

		/* @var AppAcl $appAcl*/
		$appAcl = $sm->get('maralc_acl');

		// Get params 'controller', 'action' and 'privilege' from route match
		$matches = $e->getRouteMatch();

		// Resource based on request params
		$resource = new AppAclResource();
		$resource->setController($matches->getParam('controller'));
		$resource->setAction($matches->getParam('action', 'index'));

		// 404 response if resource does not exist
		if (!$appAcl->hasResource($resource, true)) {
			$e->getResponse()->setStatusCode(404);
			return;
		}

		// Get config for AppAcl
		$config = $sm->get('config');;
		$configAppAcl = $config['AppAcl'];

		// Role
		//$auth = $sm->get('appacl_auth_service');
		$auth = new AuthenticationService(new SessionStorage('auth'));
		
		$role = new AppAclRole();
        
        $userRole = $configAppAcl['default_role'];
        if ($authService->hasIdentity()) { 			
			$identity = $authService->getIdentity();
			$userRole = ($identity['is_admin']) ? 'admin' : 'member';
		}
		
		$role->setName($userRole);

		// Query ACL
		$result = $appAcl->isAllowed($role, $resource, $e->getRequest()->getMethod());

		// 403 Unauthorized
		if ($result === false) {		
			
			
			if(!$auth->hasIdentity()){
				$response = $e -> getResponse();
				$url = $e->getRouter()->assemble(array('action' => 'login'), array('name' => 'login'));				
				$response->getHeaders()->addHeaderLine('Location', $url);
				$response->setStatusCode(302);
				$response->sendHeaders();
				$e->stopPropagation();
			} else {
			// Create ViewModel
			$model = new \Zend\View\Model\ViewModel();
			$model->setTemplate('error/403');
			$model->setVariable('reason', $appAcl::ERROR_UNAUTHORIZED);

			// Add $model as a child and set 403 status code
			$e->getViewModel()->addChild($model);
			$e->getResponse()->setStatusCode(403);

			// Stop propagation
			$e->stopPropagation();
			return;
			}			
		}
	}
}
