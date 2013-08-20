<?php
namespace Pi\Oauth\Provider\Storage;
use Pi\Oauth\Provider\Service;


class AuthorizationCode extends AbstractStorage implements CodeInterface
{
    /**
    * add code ,if existed update the record by new code
    * 
    */
    public function add($params)
    {
        if (!isset($params['code'])) {
            $params['code'] = $this->generateCode($this->config['length']);
        }

        if (!isset($params['resource_owner'])) {
            $params['resource_owner'] = Service::resourceOwner('test_resource_owner');
        }

        $codeData = $this->model->getCode($params);
        if ($codeData) {
            $update = array(
                'code'      => $params['code'],
                // 'expires'   => $params['expires'],
            );
            parent::update($codeData['id'],$update);
        } else {
            parent::add($params);
        }
        return $params['code'];
    }

    /**
    * get code data by code
    * @param authorization code 
    * @return array
    */
    public function getbyCode($code)
    {
        return $this->model->get($code,'code');
    }

    public function deleteCode($code)
    {
        return $this->model->delete($code);
    }
}