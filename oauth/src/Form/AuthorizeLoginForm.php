<?php

namespace Module\Oauth\Form;

use Pi;
use Pi\Form\Form as BaseForm;
use Zend\Form\Zend\Form\Form;
use Zend\Form\Element;

class AuthorizationForm extends BaseForm
{
    public function init() 
    {
        $this->add(array(
            'name'          => 'submit',
            'attributes'    => array(
                'value'     => __('yes'),
            ),
            'type'          => 'submit',
        ));
        $this->add(array(
            'name'          => 'authorized',
            'attributes'    => array(
                'value'     => 1,
            ),
            'type'          => 'hidden',
        ));
    } 
}