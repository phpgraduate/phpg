<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\ZfcMenu\View\Helper\Navigation\Menu;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {		
		$authService = $this->serviceLocator->get('auth_service');
        if ($authService->hasIdentity()) {
			$username = $authService->getIdentity()['user_name'];
		}	
		
		$this->layout()->setVariables(array(
				'username' => $username,			
			)
		);	
		
		//$this->layout()->setVariable('username', 'pavang');
		
        $viewModel = new ViewModel();		
		return $viewModel;
    }
}
