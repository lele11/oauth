<?php
return array(
    'item'  => array(
        'front'     => false,
        'admin'     => array(
            'consumer'     => array(
                'label'       => '客户端管理',
                'route'       => 'admin',
                'controller'  => 'consumer',
                'action'      => 'index',

                'pages'       => array(
                    'register'  => array(
                        'label'      => '客户端信息填写',
                        'route'      => 'admin',
                        'controller' => 'consumer',
                        'action'     => 'client',   
                        'visible'       => 0,
                    ),
                    'list'     => array(
                        'label'      => '信息列表',
                        'route'      => 'admin',
                        'controller' => 'consumer',
                        'action'     => 'list',
                        'visible'       => 0,
                    ),
                ),
            ),

            'provider'      => array(
                'label'         => '服务端管理',
                'route'         => 'admin',
                'controller'    => 'provider',
                'action'        => 'index',

                'pages'       => array(
                    'list'  => array(
                        'label'      => '客户端列表',
                        'route'      => 'admin',
                        'controller' => 'provider',
                        'action'     => 'list',   
                    ),
                    'check'     => array(
                        'label'      => '审核管理',
                        'route'      => 'admin',
                        'controller' => 'provider',
                        'action'     => 'consumer',
                    ),
                    'scope'     => array(
                        'label'      => '权限控制',
                        'route'      => 'admin',
                        'controller' => 'provider',
                        'action'     => 'scope',
                    ),
                ),
            ),

         'scope'      => array(
                'label'         => '授权范围管理',
                'route'         => 'admin',
                'controller'    => 'scope',
                'action'        => 'index',
            ),
        ),
    ),
);
