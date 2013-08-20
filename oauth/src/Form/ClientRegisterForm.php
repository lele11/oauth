<?php

namespace Module\Oauth\Form;

use Pi;
use Pi\Form\Form as BaseForm;
use Zend\Form\Zend\Form\Form;
use Zend\Form\Element;


class ClientRegisterForm extends BaseForm
{
    public function init() 
    {
        $this->add(array(
            'name'          => 'name',
            'options'       => array(
                'label' => __('Client Name'),
            ),
            'attributes'    => array(
                'type'  => 'text',
            )
        ));

        $this->add(array(
            'name'          => 'address',
            'options'       => array(
                'label' => __('Client Address'),
            ),
            'attributes'    => array(
                'type'  => 'text',
            )
        ));

        $this->add(array(
            'name'          => 'redirect_uri',
            'options'       => array(
                'label' => __('OAuth Callback Page'),
            ),
            'attributes'    => array(
                'type'  => 'text',
            )
        ));

        $this->add(array(
            'name'          => 'description',
            'options'       => array(
                'label' => __('Client Description'),
            ),
            'attributes'    => array(
                'type'  => 'textarea',
            )
        ));

        $this->add(array(
            'name'          => 'logo',
            'options'       => array(
                'label' => __('Client Logo'),
            ),
            'attributes'    => array(
                'type'  => 'text',
            )
        ));
       
        $this->add(array(
            'name'          => 'submit',
            'attributes'    => array(
                'value'     => __('submit'),
            ),
            'type'          => 'submit',
        ));
    } 
}
