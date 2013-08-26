<?php

namespace Module\Oauth\Form;

use Pi;
use Zend\InputFilter\InputFilter;

class ClientInfoFilter extends InputFilter
{
    public function __construct()
    {
        $this->add(array(
            'name'          => 'module',
            'required'      => true,
            'filters'    => array(
                array(
                    'name'  => 'StringTrim',
                ),
            ),
        ));

        $this->add(array(
            'name'          => 'server',
            'required'      => true,
            'filters'    => array(
                array(
                    'name'  => 'StringTrim',
                ),
            ),
        ));

        $this->add(array(
            'name'          => 'key',
            'required'      => true,
            'filters'    => array(
                array(
                    'name'  => 'StringTrim',
                ),
            ),
        ));

        $this->add(array(
            'name'          => 'secret',
            'required'      => true,
            'filters'    => array(
                array(
                    'name'  => 'StringTrim',
                ),
            ),
        ));

        $this->add(array(
            'name'          => 'host',
            'required'      => true,
            'filters'    => array(
                array(
                    'name'  => 'StringTrim',
                ),
            ),
        ));
    }

  
}
