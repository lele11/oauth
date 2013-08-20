<?php
namespace Module\Oauth\Form;

use Pi;
use Pi\Form\Form as BaseForm;
use Zend\Form\Zend\Form\Form;
use Zend\Form\Element;

class ClientInfoForm extends BaseForm
{
    public function init() 
    {
        $this->add(array(
            'name'          => 'module',
            'attributes'    => array(
                'value'     => '',
            ),
            'options'       => array(
                'label' => '模块名称',
            ),
            'type'          => 'text',
        ));
        $this->add(array(
            'name'          => 'server',
            'attributes'    => array(
                'value'     => '',
            ),
            'options'       => array(
                'label' => '服务器名称',
            ),
            'type'          => 'text',
        ));
        $this->add(array(
            'name'          => 'key',
            'attributes'    => array(
                'value'     => '',
            ),
            'options'       => array(
                'label' => 'Client ID',
            ),
            'type'          => 'text',
        ));
        $this->add(array(
            'name'          => 'secret',
            'attributes'    => array(
                'value'     => '',
            ),
            'options'       => array(
                'label' => 'Client Secret',
            ),
            'type'          => 'text',
        ));
        $this->add(array(
            'name'          => 'host',
            'attributes'    => array(
                'value'     => '',
            ),
            'options'       => array(
                'label' => '服务器地址',
            ),
            'type'          => 'text',
        ));
        $this->add(array(
            'name'          => 'submit',
            'attributes'    => array(
                'value'     => 'ok',
            ),
            'options'       => array(
                'label' => '',
            ),
            'type'          => 'submit',
        ));
    } 
}