<?php
namespace Module\Oauth\Controller\Admin;

use Pi;
use Pi\Mvc\Controller\ActionController;


class ProviderController extends ActionController
{
    public function indexAction()
    {
        // 提供填写信息表单，列出已有的数据 作为验证
       
        $this->view()->setTemplate('provider-index');
    }
}