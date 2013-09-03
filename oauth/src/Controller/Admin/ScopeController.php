<?php
namespace Module\Oauth\Controller\Admin;

use Pi;
use Pi\Mvc\Controller\ActionController;
use Module\Oauth\Form\ScopeAddForm;
use Module\Oauth\Form\ScopeAddFilter;


class ScopeController extends ActionController
{
    public function indexAction()
    {
        $this->view()->assign('title', __('Scope Page'));
        $model = $this->getModel('scope');        
        $rowset = $model->select(array());
        if (!$rowset) {
            return;
        }
        $data = $rowset->toArray();
        $form = new ScopeAddForm();
        $form->setAttribute('action', '/admin/oauth/scope/add');
        $this->view()->assign('form', $form);
        $this->view()->assign('list', $data);
        $this->view()->setTemplate('scope-index');
    }

    public function AddAction()
    {
        if (!$this->request->ispost()) {
            return ;
        }
        $data = $this->request->getPost()->toArray();
        unset($data['submit']);
        if (in_array('', $data)) {
            return ;
        }
        $model = $this->getModel('scope');
        $row = $model->createRow($data);
        $row->save();
        if (!$row->id) {
            return false;
        }
        $this->redirect()->toUrl('/admin/oauth/scope');
    }

    public function deleteAction()
    {
        $id = $this->params('id', 0);
        if ($id == 0) {
            return ;
        }
        $model = $this->getModel('scope');
        $model->delete(array('id' => $id));
    }

    public function verifyAction()
    {
        $model = $this->getModel('client');
        if ($this->request->ispost()) {
            $data = $this->request->getPost()->toArray();
            if ($data['reason']) {
                
            } else {
                $row = $model->find($data['id']);
                $row->scope = implode(',', array_merge(explode(',', $row->scope),explode(',', $row->scope_detail)));
                $row->scope_apply = 0;
                $row->scope_detail = '';
                var_dump($row);
                $row->save();
            }
            return 1;
        }        
        $rowset = $model->select(array('scope_apply' => 1));
        if (!$rowset) {
            return;
        }
        $client = $rowset->toArray();
        $temp = Pi::model('scope', 'oauth')->select(array())->toArray();
        foreach($temp as $value) {
            $scope[$value['name']] = $value['desc'];
        }
        foreach ($client as $value) {
            $data['id'] = $value['id'];
            $data['name'] = $value['name'];
            $temp_scope_name = explode(',', $value['scope_detail']);
            $data['scope'] = array();
            foreach($temp_scope_name as $name) {
                $data['scope'][$name] = $scope[$name];
            }
            $data_list[] = $data;
        }
        $this->view()->assign('list', $data_list);
        $this->view()->setTemplate('scope-verify');
    }
}