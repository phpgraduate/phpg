<?php

namespace AppAcl;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\EventManager\EventManager;
use Zend\EventManager\EventInterface;
use Zend\Mvc\MvcEvent;
use Zend\Http\Response;
use Zend\ServiceManager\ServiceManager;

use AppAcl\Service\Acl;
use AppAcl\Listener\AclListener;
use AppAcl\Model\RulesDao;
use AppAcl\Model\RulesMapper;
use AppAcl\Model\Resource as AppAclResource;
use AppAcl\Model\Role as AppAclRole;
use AppAcl\Model\Rule as AppAclRule;

class Module implements
    AutoloaderProviderInterface,
    BootstrapListenerInterface,
    ConfigProviderInterface,
    ServiceProviderInterface
{
	public function onBootstrap(EventInterface $e)
	{
		/* @var ServiceManager $sm */
		$sm = $e->getApplication()->getServiceManager();

		/* @var EventManager $eventManager */
		$eventManager = $e->getApplication()->getEventManager();

		/* @var AclListener $aclListener */
		// Attach aclListener to eventManager
		$aclListener = $sm->get('appacl_acl_listener');
		$aclListener->attach($eventManager);
	}

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

	public function getServiceConfig()
	{
		return array(
			'invokables' 	=> array(
				'appacl_acl_listener' => 'AppAcl\Listener\AclListener',
			),
			'factories' 	=> array(
				'maralc_acl' => function(\Zend\ServiceManager\ServiceManager $sm) {
					$acl = new Acl($sm->get('appacl_rules_dao'));
					return $acl;
				},
				'appacl_auth_service' => function(\Zend\ServiceManager\ServiceManager $sm) {
					$config = $sm->get('config');
					$authorizeClass = $config['AppAcl']['authorize_provider'];
					return new $authorizeClass;
				},
				'appacl_rules_dao' => function(\Zend\ServiceManager\ServiceManager $sm) {
					$rulesMapper = $sm->get('appacl_rules_mapper');
					return new RulesDao($rulesMapper);
				},
				'appacl_rules_mapper' => function(\Zend\ServiceManager\ServiceManager $sm) {
					$config = $sm->get('config');// print_r($config); exit;
					return new RulesMapper($config['AppAcl']['data']);
				},
			),
		);
	}
}
