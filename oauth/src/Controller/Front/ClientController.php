<?php
namespace Module\Oauth\Controller\Front;

use Pi;
use Pi\Mvc\Controller\ActionController;
use Pi\Oauth\Provider\Service as Oauth;
use Module\Oauth\Form\ClientRegisterForm;
use Module\Oauth\Form\ClientRegisterFilter;

class ClientController extends ActionController
{
    public function indexAction()
    {
        $this->view()->setTemplate('client-index');
    }

    /**
    * register action , should login before register 
    */
    public function registerAction()
    {
        if (!Pi::user()->hasIdentity()) {
            $login_page = Pi::url('system/login');
            $this->view()->assign('login',$login_page);
            $this->view()->setTemplate('authorize-redirect');
            return;
        }
        $form = new ClientRegisterForm();
        $form->setAttribute('method','POST');
        $form->setAttribute('action', $this->url('', array('action' => 'register')));
        if ($this->request->ispost()) {
            $post = $this->request->getPost();
            $form->setData($post);
            $form->setInputFilter(new ClientRegisterFilter);
            if(!$form->isValid()) {
                $this->view()->assign('form', $form);
                return;
            }
            $uid = Pi::user()->getUser()->id;
            $data = $form->getData();
            if (!$data['logo']) {
                $data['logo'] = "/asset/oauth/logo.png";
            }        
            $data = array(
                'name'          => $data['name'],
                'redirect_uri'  => urldecode(urldecode($data['redirect_uri'])),
                'uid'           => $uid,
                'type'          => 'public',
                'description'   => $data['description'],
                'address'       => $data['address'],
                'logo'          => $data['logo'],
                );
            Oauth::boot($this->config());
            $result = Oauth::storage('client')->addClient($data);
            $this->redirect()->toUrl('/oauth/client/list');
            return;
        }        
        $this->view()->assign('title', __('Client register'));
        $this->view()->assign('form', $form);
        $this->view()->setTemplate('client-register');        
    }

    /**
    * there is a client id ,show info of this client
    * or show client list with brife info
    */
    public function listAction()
    {
        $id = $this->params('id', '');
        $userid = Pi::user()->getUser()->id;
        Oauth::boot($this->config());
        if (!$id) {
            $result = Oauth::storage('client')->getClientByUid($userid);
            $this->view()->assign('client', $result);
            $this->view()->setTemplate('client-list');
        } else {
            $result = Oauth::storage('client')->get($id);
            $this->view()->assign('client', $result);
            $this->view()->assign('uid', $userid);
            $this->view()->setTemplate('client-info');
        }      
    }
    /**
    * delete a client 
    */
    public function deleteAction()
    {
        if ($this->request->ispost()) {
            return false;
        }
        Oauth::boot($this->config());
        $result = Oauth::storage('client')->delete($id);
    }

    /*
    * an ajax action , update client info, must POST request
    */
    public function updateAction()
    {
        if (!$this->request->ispost()) {
            return false;
        }
        $post = $this->request->getPost();
        $post = $post->toArray();
        Oauth::boot($this->config());
        $result = Oauth::storage('client')->update($post['id'],$post);
        return $result;
    }

    /**
    * an ajax action , verify client info request
    */
    public function verifyAction()
    {
        $id = $this->params('id','');
        if (!$id) {
            return ;
        }
        Oauth::boot($this->config());
        $result = Oauth::storage('client')->update($id, array('verify' => 1));
        return $result;
    }

    public function scopeAction()
    {
        if ($this->request->ispost()) {
            $param = $this->request->getPost()->toArray();
            Oauth::boot($this->config());
            $result = Oauth::storage('client')->update($param['client'], 
                array('scope_apply' => 1, 'scope_detail' => $param['scopeid'])
            );
            return ;
        }
        $clientid = $this->params('id');
        $userid = Pi::user()->getUser()->id;
        $client_model = Pi::model('client', 'oauth');
        $select = $client_model->select()
                        ->columns(array('id', 'name', 'scope'))
                        ->where(array('uid' => $userid));
        $client = $client_model->selectWith($select)->toArray();
        foreach($client as $value) {
            $client_data[$value['id']] = array(
                'name' => $value['name'],
                'scope' => explode(',', $value['scope']),
            );
        }
        $model = $this->getModel('scope');
        $scope = $model->select(array())->toArray();
        $this->view()->assign('client', $client_data);
        $this->view()->assign('scope', $scope);
        $this->view()->assign('clientid', $clientid);
        $this->view()->setTemplate('client-scope');
    }
}