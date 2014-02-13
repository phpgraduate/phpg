<?php
/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */

return array(
     'db' => array(
         'driver'         => 'Pdo',
         //'dsn'            => 'mysql:dbname=zf2tutorial;host=localhost',
         'dsn'            => $dsn,
         //'username' => get_cfg_var('zend_developer_cloud.db.username'),
         //'password' => get_cfg_var('zend_developer_cloud.db.password'),
         'driver_options' => array(
             PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
         ),
     ),
	 
	 /*'db' => array(
         'driver'         => 'Pdo',
         'dsn'            => 'pgsql:host=HOSTIP;port=PORTNUMBER;dbname=DATABASENAME;',
         'driver_options' => array(
             PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
         ),
     ),*/
	 'twitter' => array(
            'consumerKey' => 'jMspQRu5FMjf99aoDzIe8w',
            'consumerSecret' => 'oSJPP0VlN91Fv2KudPOAqcICm0GCeqSNUKy2v13kR4',
            'callbackUrl' => 'http://zendsite.lcl/login/twittercallback',
            'siteUrl' => 'http://twitter.com/oauth',
            'requestTokenUrl' => 'https://api.twitter.com/oauth/request_token',
            'accessTokenUrl'  => 'https://api.twitter.com/oauth/access_token',
            'userAuthorizationUrl' => 'https://api.twitter.com/oauth/authenticate',
//            'userAuthorizationUrl' => 'https://api.twitter.com/oauth/authorize'
        ),
	 'google' => array(
		"approval_prompt" => "force",
            "client_id" => "267794622312.apps.googleusercontent.com",
            "client_secret" => "qIWt5DIQ1ynQTiHRacuHqSfU",
            "redirect_uri" => 'http://zendsite.lcl/login/googlecallback',
            "scope" => array(
                "https://www.googleapis.com/auth/userinfo.email",
                "https://www.googleapis.com/auth/userinfo.profile",
            )
	 ),
     'service_manager' => array(
         'factories' => array(
             'Zend\Db\Adapter\Adapter'
                     => 'Zend\Db\Adapter\AdapterServiceFactory',			 
         ),
     ),
 );
