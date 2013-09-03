<?php
namespace Module\Oauth\Form;

use Pi;
use Zend\InputFilter\InputFilter;

class ScopeAddFilter extends InputFilter
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
            'name'          => 'desc',
            'required'      => true,
            'filters'    => array(
                array(
                    'name'  => 'StringTrim',
                ),
            ),
        ));       
    }
}
