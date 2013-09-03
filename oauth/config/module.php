<?php 
return array(
    // Module meta
    'meta'  => array(
        // Module title, required
        'title'         => 'OAuth',
        // Description, for admin, optional
        'description'   => 'demo for test',
        // Version number, required
        'version'       => '1.0.0',
        // Distribution license, required
        'license'       => 'New BSD',
        // Logo image, for admin, optional
        //'logo'          => 'image/logo.jpg',
        // Readme file, for admin, optional
        'readme'        => 'docs/readme.txt',
        // Direct download link, available for wget, optional
        //'download'      => 'http://dl.xoopsengine.org/module/Oauth',
        // Oauth site link, optional
        //'Oauth'          => 'http://Oauth.xoopsengine.org/Oauth',

        // Module is ready for clone? Default as false
        'clonable'      => true,
    ),
    // Author information
    'author'    => array(
        // Author full name, required
        'name'      => 'lele',
        // Email address, optional
        'email'     => 'lele@eefocus.com', 
        // Website link, optional
        'website'   => 'http://www.xoopsengine.org',
        // Credits and aknowledgement, optional
        'credits'   => 'Zend Framework Team; Pi Engine Team; EEFOCUS Team.'
    ),
    // Module dependency: list of module directory names, optional
    'dependency'    => array(
    ),
    // Maintenance actions
    'maintenance'   => array(
        // Class for module maintenace
        // Methods for action event:
        //  preInstall, install, postInstall;
        //  preUninstall, uninstall, postUninstall;
        //  preUpdate, update, postUpdate
        //'class' => 'Module\\Oauth\\Maintenance',

        // resource
        'resource' => array(
            // Database meta
            'database'  => array(
                // SQL schema/data file
                'sqlfile'   => 'sql/mysql.sql',
                // Tables to be removed during uninstall, optional - the table list will be generated automatically upon installation
                'schema'    => array(
                    'authorization_code'    => 'table',
                    'client'                => 'table',
                    'access_token'          => 'table',
                    'refresh_token'         => 'table',
                    'user_authorization'    => 'table',
                    'consumer_client'       => 'table',
                    'scope'                 => 'table',
                   )
            ),
            // Module configs
            'config'    => 'config.php',
            // ACL specs
            'acl'       => 'acl.php',
            // // Block definition
            // 'block'     => 'block.php',
            // // Bootstrap, priority
            // 'bootstrap' => 1,
            // // Event specs
            // 'event'     => 'event.php',
            // // Search registry, 'class:method'
            // 'search'    => array('callback' => array('search', 'index')),
            // // View pages
            // 'page'      => 'page.php',
            // // Navigation definition
            'navigation'    => 'navigation.php',
            // // Routes, first in last out; bigger priority earlier out

           'route'     => 'route.php',
            // // Callback for stats and monitoring
            // 'monitor'   => array('callback' => array('monitor', 'index')),
            // // Additional custom extension
            // 'test'      => array(
            //     'config'    => 'For test'
            // )
        )
    )
);
