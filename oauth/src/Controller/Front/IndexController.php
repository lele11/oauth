<?php
namespace Module\Oauth\Controller\Front;

use Pi;
use Pi\Mvc\Controller\ActionController;

// use Module\Oauth\Form\AuthorizationForm;

class IndexController extends ActionController
{
    public function indexAction()
    {
    	d($this->config());
    }
}