<?php
namespace Pi\Oauth\Provider\Storage\Model\Database;

use Pi\Oauth\Provider\Storage\ValidateInterface;
use Pi\Oauth\Provider\Storage\Model\Database\AbstractModel;

class AuthorizationCode extends AbstractModel implements ValidateInterface
{
    public function validate($id, $secret)
    {

    }

    /**
    * get code data by client_id and user id
    * @return array
    */
    public function getCode($params)
    {
        $code = $this->model->select(array(
            'client_id'     => $params['client_id'],
            'resource_owner'=> $params['resource_owner'],
        ))->toArray();
        return $code[0];
    }

    /**
    * delete authorization code which has been used
    */
    public function delete($code)
    {
        $result = $this->model->delete(array('code' => $code));
        return $result;
    }
}

