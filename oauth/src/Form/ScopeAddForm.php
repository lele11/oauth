<?php
namespace Module\Oauth\Form;

use Pi;
use Pi\Form\Form as BaseForm;
use Zend\Form\Zend\Form\Form;
use Zend\Form\Element;


class ScopeAddForm extends BaseForm
{
    public function init() 
    {
        $this->add(array(
            'name'          => 'name',
            'options'       => array(
                'label' => __('Scope Name'),
            ),
            'attributes'    => array(
                'type'  => 'text',
            )
        ));

        $this->add(array(
            'name'          => 'desc',
            'options'       => array(
                'label' => __('Scope Info'),
            ),
            'attributes'    => array(
                'type'  => 'text',
            )
        ));
       
        $this->add(array(
            'name'          => 'submit',
            'attributes'    => array(
                'value'     => __('OK'),
            ),
            'type'          => 'submit',
        ));
    } 
}
