<?php
$config = array();


$config['category'] = array(
    array(
        'name'      => 'general',
        'title'     => _t('General'),
    ),
    array(
        'name'      => 'authorization_code',
        'title'     => _t('Authorization Code Setting'),
    ),
    array(
        'name'      => 'access_token',
        'title'     => _t('Access Token Setting'),
    ),
    array(
        'name'      => 'refresh_token',
        'title'     => _t('Refresh Token Setting'),
    ),
    array(
        'name'      => 'grant_type',
        'title'     => _t('The Grant Type which is supported by this module.'),
    ),  
    array(
        'name'      => 'storage',
        'title'     => _t('The storage model setting'),
    ),
    array(
        'name'      => 'scope',
        'title'     => _t('The scope of resource'),
    ),
);

// Config items

// General section

$config['item'] = array(
    // General section

    'service_role'      => array(
        'title'         => _t('Server Provider'),
        'description'   => _t('This module is work as OAuth server provider.'),
        'edit'          => 'checkbox',
        'value'         => 0,
        'filter'        => 'number_int',
        'category'      => 'general',
    ),

    //authorization code section

    'code_length'            => array(
        'title'         => _t('length'),
        'description'   => _t('The length of Authorization Code.'),
        'edit'          => 'text',
        'value'         => '40',
        'category'      => 'authorization_code',
    ),
    'code_expires'            => array(
        'title'         => _t('expire'),
        'description'   => _t('The life time of Authorization Code'),
        'edit'          => 'text',
        'value'         => '600',
        'category'      => 'authorization_code',
    ),


   
    // Access Token section

    'access_length'            => array(
        'title'         => _t('length'),
        'description'   => _t('The length of Access Token.'),
        'edit'          => 'text',
        'value'         => '40',
        'category'      => 'access_token',
    ),
    'access_expires'            => array(
        'title'         => _t('expire'),
        'description'   => _t('The life time of Access Token'),
        'edit'          => 'text',
        'value'         => '3600',
        'category'      => 'access_token',
    ),

    // Refresh Token section

    'refresh_length'            => array(
        'title'         => _t('length'),
        'description'   => _t('The length of Refresh Token.'),
        'edit'          => 'text',
        'value'         => '40',
        'category'      => 'refresh_token',
    ),
    'refresh_expires'           => array(
        'title'         => _t('expire'),
        'description'   => _t('The life time of Refresh Token'),
        'edit'          => 'text',
        'value'         => '129600',
        'category'      => 'refresh_token',
    ),

    // Grant type section

    'code'              => array(
        'title'         => _t('Enable Authorization Code'),
        'description'   => _t('Authorization Code will be supported if this option is enabled.'),
        'edit'          => 'checkbox',
        'value'         => 1,
        'filter'        => 'number_int',
        'category'      => 'grant_type',
    ),
    'implicit'          => array(
        'title'         => _t('Enable Implicit'),
        'description'   => _t('Implicit will be supported if this option is enabled.'),
        'edit'          => 'checkbox',
        'value'         => 0,
        'filter'        => 'number_int',
        'category'      => 'grant_type',
    ),
    'password'          => array(
        'title'         => _t('Enable User Password'),
        'description'   => _t('User Password will be supported if this option is enabled.'),
        'edit'          => 'checkbox',
        'value'         => 0,
        'filter'        => 'number_int',
        'category'      => 'grant_type',
    ),
    'client'            => array(
        'title'         => _t('Enable Client Credentials'),
        'description'   => _t('Client Credentials will be supported if this option is enabled.'),
        'edit'          => 'checkbox',
        'value'         => 0,
        'filter'        => 'number_int',
        'category'      => 'grant_type',
    ),

    //storage model section

    'storage'           =>array(
        'title'         => _t('Storage Model'),
        'description'   => _t('Select the storage model'),
        'edit'          => array(
            'type'      => 'select',
            'attributes'    => array(
                'options'   =>array(
                    'Database'       => _t('Default'),
                ),
            ),
        ),
        'value'         => 'Database',
        'category'      => 'storage',
    ),

    // scope section
    'scope'             => array(
        'title'         => _t('Scope of resource'),
        'description'   => _t('Scope Setting'),
        'edit'          => 'textarea',
        'value'         => '',
        'category'      => 'scope',
    ),
);

return $config;

// $config = array(
//             'server'    => array(
//                 'authorization' => array(
//                     'response_types'    => array(
//                         'code', 'token',
//                     ),
//                  ),
//                 'grant' => array(
//                     'grant_types'   => array(
//                         'authorization_code'    => 'AuthorizationCode',
//                         'password'              => 'Password',
//                         'client_credentials'    => 'ClientCredentials',
//                         'refresh_token'         => 'RefreshToken',
//                         'urn:ietf:params:oauth:grant-type:jwt-bearer'
//                                         => 'JwtBearer',
//                     ),
//                 ),
//                 'resource'  => array(
//                     'token_type'    => 'bearer',
//                     'www_realm'     => 'service',
//                 ),
//             ),
//             'storage'   => array(
//                 'model_set'             => array(
//                     'name'      => 'database',
//                     'config'    => array(
//                         'table_prefix'  => 'oauth',
//                     ),
//                 ),
//                 'client' => array(
//                     'model_set'             => array(
//                         'name'      => 'database',
//                         'config'    => array(
//                             'table_prefix'  => 'oauth',
//                         ),
//                     ),            
//                 ),
//                 'authorization_code'    => array(
//                     'expires_in'    => 300,
//                     'length'        => 40,
//                 ),
//                 'access_token'  => array(
//                     'token_type'    => 'bearer',
//                     'expires_in'    => 3600,
//                     'length'        => 40,
//                 ),
//                 'refresh_token' => array(
//                     'expires_in'    => 1209600,
//                     'length'        => 40,
//                 ),
//             )
//         );