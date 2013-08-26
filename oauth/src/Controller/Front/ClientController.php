<?php
namespace Module\Oauth\Controller\Front;

use Pi;
use Pi\Mvc\Controller\ActionController;
use Pi\Oauth\Provider\Service as Oauth;
use Module\Oauth\Form\ClientRegisterForm;
use Module\Oauth\Form\ClientRegisterFilter;
use Module\Oauth\Lib\UserHandler as User;

class ClientController extends ActionController
{
    public function indexAction()
    {
        // $this->view()->setTemplate('client-index');
    }

    /**
    * register action , should login before register 
    */
    public function registerAction()
    {
        if (!Pi::user()->hasIdentity()) {
            $login_page = 'http://pi-oauth.com/system/login/index';
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
            d($data);     
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
    * an ajax action , update client info
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
}