<?php
namespace Module\Oauth\Form;

use Pi;
use Zend\InputFilter\InputFilter;

class ClientRegisterFilter extends InputFilter
{
    public function __construct()
    {
       $this->add(array(
            'name'          => 'name',
            'required'      => true,
            'filters'    => array(
                array(
                    'name'  => 'StringTrim',
                ),
            ),
        ));

        $this->add(array(
            'name'          => 'redirect_uri',
            'required'      => true,
            'filters'    => array(
                array(
                    'name'  => 'StringTrim',
                ),
            ),
        ));       
    }
}
