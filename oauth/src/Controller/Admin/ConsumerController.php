<?php
namespace Module\Oauth\Controller\Admin;

use Pi;
use Pi\Mvc\Controller\ActionController;
use Module\Oauth\Form\ClientInfoForm; 
use Module\Oauth\Form\ClientInfoFilter; 

class ConsumerController extends ActionController
{
    public function indexAction()
    {
        // 提供填写信息表单，列出已有的数据 作为验证
        $form = new ClientInfoForm('ClientInfo');
        $form->setAttribute('action', $this->url('', array('action' => 'client')));
        $this->view()->assign('form', $form);
        $this->view()->setTemplate('consumer-index');
    }

    /**
    * 提供后台页面，为每个第三方模块保存模块在OAuth服务器上的身份信息
    * 需要填写的内容： client_id， client_secret， module_name， server_name
    * @return bool   失败：错误信息
    */
    public function clientAction()
    {

        // 接收表单提交的数据，保存到数据库，如果提交的记录已经存在 则 更新已有数据
        $form = new ClientInfoForm('ClientInfo');
        $form->setAttribute('action', $this->url('', array('action' => 'client')));


        if ($this->request->isPost()) {
            $post = $this->request->getPost();
            $form->setData($post);
            $form->setInputFilter(new ClientInfoFilter);
            if (!$form->isValid()) {
                $this->view()->assign('form', $form);   
                $this->view()->setTemplate('client-info');             
            } else {
                $params = $form->getData();
                $data = array(
                    'module'        => $params['module'],
                    'server'        => $params['server'],
                    'client_id'     => $params['key'],
                    'client_secret' => $params['secret'],
                    'server_host'   => $params['host'],
                    'create_time'   => time(),
                );

                $row = Pi::model('consumer_client', 'oauth')->createRow($data);
                $row->save();
                // $this->view()->assign('form', $form);
                $this->redirect()->toUrl('/admin/oauth/consumer/list'); 
            }
        }        
    }

    /**
    * 后台查看客户端信息页面
    */
    public function listAction()
    {
        //列出已有的客户端信息
        $row = Pi::model('consumer_client', 'oauth')->select(array());
        $client = array();
        if ($row) {
            $client = $row->toArray();
        }
        $this->view()->assign('client', $client);
        $this->view()->setTemplate('consumer-list');
    }

    public function tokenAction()
    {
        $this->view()->setTemplate('client-token');
    }

    /**
    * 在客户端提供取消应用授权的功能应该是不合适的，因为本oauth模块式提供给多个客户端模块使用，
    * oauth模块只是为了简化客户端访问server的开发问题，不应该提供过多的功能接口
    */
    public function revokeAction()
    {
        $row = Pi::model('oauth_client', 'oauth')->select(array(
            'name'   => $module,
            'server' => $server,
        ));
        if ($row) {
            $config = $row->toArray();        
            $oauth = Pi::service('api')->oauth(array('server','getServer'),$config);
            // $token = $oauth->getToken();
            $oauth->revokeToken();
            return TRUE;
        } else {
            return "error";
        }
    }
}