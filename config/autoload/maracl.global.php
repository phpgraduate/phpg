<?php
// To use this files:
// Copy it to path APPLICATION/config/autoload/maracl.global.php
return array(
	'MarAcl' => array(
		// Holds the full qualified class name.
		// Must implement AuthenticationService class.
		// By default "Zend\Authentication\AuthenticationService"
		'authorize_provider' => 'Zend\Authentication\AuthenticationService',

		// String
		// Default role to use when there is no identity on 'authorize_provider'
		// By default "anonymous"
		'default_role' => 'anonymous',

		// Holds information about Roles, Resources and Rules
		'data'	=> array(

			// Holds array of Roles
			// Singles role is defined by:
			// 'name' 		string 		Indicates the name of role
			// 'parents' 	array 		Indicates the parents of roles. OPTIONAL
			'roles'		=> array(
				array(
					'name' 		=> 'anonymous',					
					//'parents'	=>	array('admin', 'member'),
				),
				array(
					'name' 		=> 'admin',					
					//'parents'	=>	array('admin', 'member'),
				),
				array(
					'name' 		=> 'member',					
					//'parents'	=>	array('admin', 'member'),
				),
			),

			// Holds array of Resources.
			// All controller and action used on Rules section must be defined here.
			// Single resource is defined by:
			// 	'controller' 	string 			Indicates fully controller name
			// 	'actions' 		string|array 	Indicates name of actions belong in this controller
			'resources'	=> array(
				array(
					'controller' => 'application\controller\index',
					'actions' 	 => array('index'),
				),
				array(
					'controller' => 'album\controller\album',
					'actions' 	 => array('index'),
				),
				array(
					'controller' => 'auth\controller\login',
					'actions' 	 => array('login','logout',),
				),
				// ...
			),

			// Holds array of Rules divided in two groups: allow and deny
			// Single rule is defined by:
			// 	'role'			string
			// 	'controller'	string				Indicates fully controller name
			// 	'actions'		string|array		Optional. Indicates name of actions belong in this controller
			// 										If it's not provided then applies to all actions
			// 	'privilege'		string|array		Optional. HTTP method (GET, POST, HEAD, TRACE, OPTIONS, DELETE)
			//										By default is 'GET'
			// 	'active'		int					Optional. 1 enable. 2 disable.
			//										By default is '1'
			'rules'	=> array(
				'allow' => array(
					array(
						'role'			=> 'anonymous',
						'controller'	=> 'application\controller\index',
						'actions'		=> array('index'),
						'privilege'		=> 'GET',
						'active' 		=> 1,
					),
					array(
						'role'			=> 'anonymous',
						'controller'	=> 'auth\controller\login',
						'actions'		=> array('login','logout'),
						'privilege'		=> array('POST','GET'),
						'active' 		=> 1,
					),					
					array(
						'role'			=> 'admin',
						'controller'	=> 'album\controller\album',
						'actions'		=> array('index',),
						//'privilege'		=> 'POST',
						'active' 		=> 1,
					),
					array(
						'role'			=> 'admin',
						'controller'	=> 'auth\controller\login',
						'actions'		=> array('logout','login'),
						//'privilege'		=> 'POST',
						'active' 		=> 1,
					),
				),
				'deny' => array(
					array(
						'role'			=> 'anonymous',
						'controller'	=> 'album\controller\album',
						'actions'		=> array('index',),
						//'privilege'		=> 'POST',
						'active' 		=> 1,
					),
				),
			),
		),
	),
);