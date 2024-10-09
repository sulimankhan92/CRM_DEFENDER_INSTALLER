<?php

$manifest = array (
    'acceptable_sugar_versions' =>  array (
        'regex_matches' => array(
            '.*',
        ),
    ),
    'acceptable_sugar_flavors' => array(
        'CE',
        'PRO',
        'ENT',
        'CORP',
        'ULT',
    ),
	'readme'=>'',
	'key'=>'',
	'author' => 'Lion Solution',
	'description' => 'Module to monitor user accesses and to protect the system against bruteforce and dictionary attacks',
	'icon' => '',
	'is_uninstallable' => true,
	'name' => 'Lion Solution CRM Defender',
	'published_date' => '2023-10-31',
	'type' => 'module',
	'version' => '2.0.1_6x',
	'remove_tables' => 'prompt',
);
$installdefs = array (
	'id' => 'LS_CRM_Defender',
    'beans' => 
    array (
		0 => 
		array (
			'module' => 'LS_CRM_Defender',
			'class' => 'LS_CRM_Defender',
			'path' => 'modules/LS_CRM_Defender/LS_CRM_Defender.php',
			'tab' => false,
		),
	),
	'image_dir' => '<basepath>/icons',
	'copy' => array (
		array (
		  'from' => '<basepath>/SugarModules/modules/LS_CRM_Defender',
		  'to' => 'modules/LS_CRM_Defender',
		),
		array (
			'from' => '<basepath>/license',
			'to' => 'modules/LS_CRM_Defender',
		),
    ),
    'language' => array (
        0 => 
        array (
          'from' => '<basepath>/SugarModules/language/application/en_us.lang.php',
          'to_module' => 'application',
          'language' => 'en_us',
        ),
        1 => 
        array (
        'from'=> '<basepath>/SugarModules/language/application/en_us.LS_CRM_DefenderAdmin.php',
        'to_module'=> 'Administration',
        'language'=>'en_us',
        ),
        2 => 
        array (
          'from' => '<basepath>/SugarModules/language/application/it_it.lang.php',
          'to_module' => 'application',
          'language' => 'it_it',
        ),
        3 => 
        array (
        	'from'=> '<basepath>/SugarModules/language/application/it_it.LS_CRM_DefenderAdmin.php',
        	'to_module'=> 'Administration',
        	'language'=>'it_it',
        ),
    ),
    'administration' =>  array (
        array (
    		'from' => '<basepath>/administration/LS_CRM_DefenderAdmin.menu.php',
        ),
    ),
	'action_view_map' => array (
        array(
            'from'=> '<basepath>/license_admin/actionviewmap/LS_CRM_Defender_actionviewmap.php',
                'to_module'=> 'LS_CRM_Defender',
        ),
    ),
	'logic_hooks' => array(
     array(
        'module'         => 'Users',
        'hook'           => 'login_failed',
        'order'          => 100,
        'description'    => 'Monitor User Access status',
        'file'           => 'modules/LS_CRM_Defender/util.php',
        'class'          => 'monitorAccesses',
        'function'       => 'updateAccessControl',
     ),
	 array(
        'module'         => 'Users',
        'hook'           => 'login_failed',
        'order'          => 101,
        'description'    => 'Update .htccess file',
        'file'           => 'modules/LS_CRM_Defender/util.php',
        'class'          => 'monitorAccesses',
        'function'       => 'updateHTAccess',
     ),
     array(
        'module'         => 'Users',
        'hook'           => 'after_login',
        'order'          => 100,
        'description'    => 'Check Login status',
        'file'           => 'modules/LS_CRM_Defender/util.php',
        'class'          => 'monitorAccesses',
        'function'       => 'updateAccessControl',
     ),
   ), 

);
