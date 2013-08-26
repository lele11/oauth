<?php
namespace Module\Oauth\Controller\Front;

use Pi;
use Pi\Mvc\Controller\ActionController;
use Pi\Oauth\Provider\Service as Oauth;
use Module\Oauth\Lib\UserHandler as User;

class AuthorizeController extends ActionController
{
    public function indexAction()
    {
        /**
        * if user is not logged,redirect to login page ,which is defined by resource owner
        * the login form  may provided by user module 
        * 添加强制登录选项，使用login参数，跳转到登录页面，需要去除，默认跳转不能实现，构造链接需要解决URL编码问题
        * 解决编码问题，页面跳转可使用js和程序两种方式
        *
        * 还需要判断用户已经授权的操作流程
        */ 

        // Oauth::boot($config);
        Oauth::boot($this->config());
        $authorize = Oauth::server('authorization');
        $request = Oauth::request();
        $params = $this->getParams();
        $params['resource_owner'] = Pi::user()->getUser()->id;
        $request->setParameters($params); 

        if ($authorize->validateRequest($request)) {
            $login_status = $this->params('login',0);
            if (!is_numeric($login_status)) {
                return;
            }

            if (!$login_status) {
                $login_status = !Pi::user()->hasIdentity();
            } else {
                // User::logout();
            }

            if ($login_status) {
                // $this->loginPage();
                $login_page = Pi::url('/system/login/index');//TODO
                $this->view()->assign('login',$login_page);
                $this->view()->setTemplate('authorize-redirect');
                return;
            }
            if (!$request->ispost()) {
                $client = Oauth::storage('client')->getClient($params['client_id']);
                $this->view()->assign('client', $client);
                $this->view()->assign('backuri',$params['redirect_uri']);       
                $this->view()->setTemplate('authorize-auth');
                return; 
            } else {
                $authorize->process($request);
            }            
        }
        $result = $authorize->getResult(); 
        $this->response->setStatusCode($result->getStatusCode());
        $this->response->setHeaders($result->getHeaders());
        $this->response->setContent($result->setContent()->getContent());
        return $this->response;
    }

    /**
    * redirect to login page ,the address of log page is provided by resource owner 
    * 原计划使用函数进行跳转，由于URL转码问题，使用JavaScript进行
    */
    protected function loginPage()
    {
        $loacation = Pi::url('') . $this->request->getServer('REDIRECT_URL');        
        $resource_login = 'http://pi-oauth.com/system/login/index/';
        $this->redirect()->toUrl($resource_login);
    }

    /**
    * get paramesters of request  
    *
    * @return array
    */
    protected function getParams() 
    {
        $clientid = $this->params('client_id');
        $response_type = $this->params('response_type');
        $redirect_uri = $this->params('redirect_uri');
        $state = $this->params('state');
        $scope = $this->params('scope');

        return array(
            'client_id'     => $clientid, 
            'response_type' => $response_type,
            'redirect_uri'  => urldecode(urldecode($redirect_uri)),
            'state'         => $state,
            'scope'         => $scope,
        );
    }
}