<?php
namespace Module\Oauth\Controller\Front;

use Pi;
use Pi\Mvc\Controller\ActionController;
use Pi\Oauth\Provider\Service as Oauth;

class GrantController extends ActionController
{
    public function indexAction()
    {
        Oauth::boot($this->config());
        $grant = Oauth::server('grant');
        $request = Oauth::request();
        $request->setParameters($this->getParams());
        $grant->process($request);
        $result = $grant->getResult();
        $this->response->setStatusCode($result->getStatusCode());
        $this->response->setHeaders($result->getHeaders());
        $this->response->setContent($result->setContent()->getContent());
        // $this->view()->setTemplate(false);
        return $this->response;
    }

    /**
    * 根据不同的授权请求 获取不同的参数
    *  code：
    */
    protected function getParams()
    {
        $params = array();
        $params['client_id'] = $this->params('client_id','');
        $params['client_secret'] = $this->params('client_secret','');

        //get client id and secret form HTTP Basic Authorization        
        if (!$params['client_id']) {
            if ($this->request->getServer('HTTP_AUTHORIZATION')) {
                $basic = explode(' ',$this->request->getServer('HTTP_AUTHORIZATION'));
                if ($basic[0] == 'Basic') {
                    list($client_id , $client_secret) = explode(":", base64_decode($basic[1]));
                    $params['client_id'] = $client_id;
                    $params['client_secret'] = $client_secret;
                }
            } 
        }
        // there could add more method  to get client identify

        $params['grant_type'] = $this->params('grant_type','');

        switch ($params['grant_type']) {
            case 'authorization_code':
                $params['code'] = $this->params('code','');
                $params['redirect_uri'] = urldecode($this->params('redirect_uri',''));
                break;
            
            case 'password':
                $params['username'] = $this->params('username', '');
                $params['password'] = $this->params('password', '');
                break;

            case 'refresh_token':
                $params['refresh_token'] = $this->params('refresh_token', '');
                break;

            case 'clientcresentials':
                break;

            default:
                $params = array();
                break;
        }
        return $params;
    }
}