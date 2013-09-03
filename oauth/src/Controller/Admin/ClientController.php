<?php
namespace Module\Oauth\Controller\Admin;

use Pi;
use Pi\Mvc\Controller\ActionController;


class ClientController extends ActionController
{
    public function indexAction()
    {
        // 提供填写信息表单，列出已有的数据 作为验证
       
        $this->listAction();
    }

    public function listAction()
    {
        $model = $this->getModel('client');
        $clientTable = $model->getTable();
        $userTable = Pi::model('user_account')->getTable();

        $select = $model->select()->join(
            array('user' => $userTable), 
            'user.id = ' . $clientTable . '.uid',
            array('username' => 'name')
        );
        $rowset = $model->selectWith($select);
    	if (!$rowset) {
    		return;
    	}
    	$data = $rowset->toArray();
    	$this->view()->assign('list', $data);
    	$this->view()->setTemplate('client-list');
    }

    /**
    * unable service for client 
    * ajax action
    */
    public function deleteAction()
    {
    	$id = $this->params('id', 0);
        if ($id == 0) {
            return ;
        }
        //some code
    }

    public function verifyAction()
    {
        $model = $this->getModel('client');
        if ($this->request->ispost()) {
            $data = $this->request->getPost()->toArray();            
            if ($data['reason']) {
                $model->update(array('verify_result' => $data['reason'],'verify' => 0), $data['id']);
            } else {
                $model->update(array('verify' => 2), $data['id']);
            }
            return 1;
        }        
        $clientTable = $model->getTable();
        $userTable = Pi::model('user_account')->getTable();
        $select = $model->select()->join(
            array('user' => $userTable), 
            'user.id = ' . $clientTable . '.uid',
            array('username' => 'name')
        );
        $select->where(array('verify' => 1));
        $rowset = $model->selectWith($select);
    	if (!$rowset) {
    		return;
    	}
    	$data = $rowset->toArray();
    	$this->view()->assign('list', $data);
    	$this->view()->setTemplate('client-verify');
    }
}