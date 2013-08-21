<?php
namespace Module\Oauth\Api;

use Pi;
use Pi\Application\AbstractApi;


/**
* SDK的API接口，共有三个对外接口
*
* + token($module, $server)
*  - 客户端获取已存在的token的接口
*  - 调用实例
*    ~~~
*      Pi::service('api')->oauth->token('module', 'server');
*    ~~~
* + getAuthorizeUrl($module, $server, $scope = 'base', $callback = '')
*  - 生成用户授权链接
*  - 调用实例
*   ~~~
*      // 使用默认的授权范围，使用模块自带的callback处理程序
*     Pi::service('api')->oauth->getAuthorizeUrl('module', 'server');
*     // 申请默认范围外的数据授权，需要使用scope参数
*     Pi::service('api')->oauth->getAuthorizeUrl('module', 'server', array('one', 'two'));
*     // 自定义回调处理过程的情况
*     Pi::service('api')->oauth->getAuthorizeUrl('module', 'server', NULL, 'path');
*   ~~~
* + getAccessToken( $module, $server, $type , $keys ) 
*  - 获取token，所有获取token都使用这个接口
*  - 调用实例
*   ~~~
*   //授权码获取token
*   Pi::service('api')->oauth->getAccessToken('module', 'server','code', array('code'=>'','redirect_uri'=>''));
*   //refresh token获取 token
*   Pi::service('api')->oauth->getAccessToken('module', 'server','refresh_token', array('refresh_token'=>''));
*   //用户名密码方式
*   Pi::service('api')->oauth->getAccessToken('module', 'server','password', array('username'=>'', 'password'=>''));
*   //客户端授权方式
*   Pi::service('api')->oauth->getAccessToken('module', 'server','client', '');
*   ~~~
*
*/

class Client extends AbstractApi
{
    protected $module = 'oauth';

    /**
    * 获取已经存在的token，默认保存在session中
    * @param  module：调用API的模块名称，server：授权服务器名称
    * @return array 
    *       成功返回array('access_token'=>'value', 'refresh_token'=>'value');
    *       失败返回false
    *       token过期则返回 array('refresh_token'=>'value')
    */

    public function token($module, $server)
    {
        //考虑为token的存储加上模块的信息，方便管理
        if (isset($_SESSION['token'])) {
            $arr = $_SESSION['token'];
        }
        if (isset($arr['access_token']) && $arr['access_token']) {
            $token = array();
            if (time() < $arr['expired']) {
                $token['access_token'] = $arr['access_token'];
            }            
            if (isset($arr['refresh_token']) && $arr['refresh_token']) {
                $token['refresh_token'] = $arr['refresh_token'];
            } 
        } 
        return $token;
    }

    /**
     * 生成授权申请链接
     *
     * @param string $module 调用接口的模块名称
     * @param string $server 授权服务器的名称
     * @param array $scope 授权范围数组，array('','',...)
     * @param string $callback 回调地址 默认为模块提供的回调函数地址
     * @return string 
    */
    public function getAuthorizeUrl($module, $server, $scope = NULL, $callback = NULL)
    {
        $client = $this->getClient($module, $server);
        if (!$client) {
            return false;
        }
        $scope_str = 'base';
        if ($scope) {
            $scope_str = 'base';
        }
        $params = array();
        $params['client_id'] = $client['client_id'];
        $params['redirect_uri'] = $callback ? $callback : 'http://' . $_SERVER['HTTP_HOST'] . '/oauth/consumer/callback';
        $params['response_type'] = 'code';        
        $params['scope'] = $scope_str;
        $params['state'] = $this->setState($module, $server);
     
        return $client['server_host'] . '/oauth/authorize/index' . "?" . http_build_query($params);       
    }

    /**
     * 获取access_token
     * @param string $module 调用接口的模块名称
     * @param string $server 授权服务器的名称
     * @param string $type 请求的类型,可以为:code, password, token, client
     * @param array $keys 其他参数：
     *   - 当$type为code时： array('code'=>..., 'redirect_uri'=>...)
     *   - 当$type为password时： array('username'=>..., 'password'=>...)
     *   - 当$type为token时： array('refresh_token'=>...)
     *   - 当$type为client时：null
     * @return array 
    */
    public function getAccessToken( $module, $server, $type , $keys ) 
    {
        if (!in_array( $type, array('token', 'code', 'password', 'client'))) {
            return false;
        }
        $client = $this->getClient($module, $server);
        if (!$client) {
            return false;
        }  
        $params = array();
        $params['client_id'] = $client['client_id'];
        $params['client_secret'] = $client['client_secret'];
        if ( $type === 'token' ) {
            $params['grant_type'] = 'refresh_token';
            $params['refresh_token'] = $keys['refresh_token'];
        } elseif ( $type === 'code' ) {
            $params['grant_type'] = 'authorization_code';
            $params['code'] = $keys['code'];
            $params['redirect_uri'] = $keys['redirect_uri'];
        } elseif ( $type === 'password' ) {
            $params['grant_type'] = 'password';
            $params['username'] = $keys['username'];
            $params['password'] = $keys['password'];
        } elseif ( $type === 'client') {
            $params['grant_type'] = 'clientcresentials';            
        }
        $tokenUrl = $client['server_host'] . '/oauth/grant/index';
        $response = $this->oAuthRequest($tokenUrl, 'POST', $params);
        $token = json_decode($response, true);
        if (!$token['error']) {
            $this->setToken($token);
        } else {
            return $token;
        }
    }

    private function setToken($token)
    {
        unset($_SESSION['token']);
        $_SESSION['token'] = array(
            'access_token'  => $token['access_token'],
            'expired'       => $token['expires_in'] + time(),
            'refresh_token' => $token['refresh_token'],
        );
    }
    /**
    * 授权请求链接的state字符串，考虑添加时间标志，需要研究
    * @return string
    * @ignore
    */
    private function setState($module, $server)
    {
        $state = base64_encode($module . '-' . $server);
        if (isset($_SESSION['state'])) {
            unset($_SESSION['state']);
        }
        $_SESSION['state'] = $state;
        return $state;
    }

     /**
    * 取得某个客户端在某个授权服务提供方的身份标识
    *
    * @param $name : 客户端的名称
    * @param $server: 授权服务器的名称
    * @return array  ('client_id' => 'value','client_secret' => 'value', 'server_host' => 'value')| false
    * @ignore
    */
    private function getClient($module, $server = '')
    {
        $row = Pi::model('consumer_client', 'oauth')->select(array(
            'module'   => $module,
            'server' => $server,
        ));
        if ($data = $row->toArray()) {
            return array(
                'client_id'     => $data[0]['client_id'],
                'client_secret' => $data[0]['client_secret'],
                'server_host'   => $data[0]['server_host'],
            );
        } else {
            return false;
        }
    }

    protected function oAuthRequest($url, $method, $parameters, $basic = FALSE) 
    { 
        switch ($method) {
            case 'GET':
                $url = $url . '?' . http_build_query($parameters);
                return $this->http($url, 'GET');
                break;
            default:
                $headers = array();
                if ( is_array($parameters) ) {
                    if ($basic) {
                        $headers[] = "Authorization: Basic " . base64_encode($params['client_id'] . ":" . $params['client_secret']);           
                        unset($params['client_id']);
                        unset($params['client_secret']);
                    }
                    $body = http_build_query($parameters);
                }
                $headers[] = "X-Requested-With:XMLHttpRequest";
                $headers[] = "Accept: application/json";
                $headers[] = "Content-Type: application/x-www-form-urlencoded";
               
                return $this->http($url, $method, $body, $headers);
        }
    }
 
    /**
     * Make an HTTP request
     *
     * @return string API results
     * @ignore
     */
    protected function http($url, $method, $postfields = NULL, $headers = array()) 
    {
        $ci = curl_init();        
        curl_setopt($ci, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
        curl_setopt($ci, CURLOPT_USERAGENT, 'test');
        // curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, $this->connecttimeout);
        // curl_setopt($ci, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ci, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ci, CURLOPT_ENCODING, "");
        // curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, $this->ssl_verifypeer);
        // curl_setopt($ci, CURLOPT_SSL_VERIFYHOST, 1);
        curl_setopt($ci, CURLOPT_HEADER, FALSE);
 
        if ($method == 'POST') {
            curl_setopt($ci, CURLOPT_POST, TRUE);
            if (!empty($postfields)) {
                curl_setopt($ci, CURLOPT_POSTFIELDS, $postfields);
            }                
        }
        curl_setopt($ci, CURLOPT_URL, $url );
        curl_setopt($ci, CURLOPT_HTTPHEADER, $headers );
        curl_setopt($ci, CURLINFO_HEADER_OUT, TRUE );
 
        $response = curl_exec($ci);        
        curl_close ($ci);
        return $response;
    }
}