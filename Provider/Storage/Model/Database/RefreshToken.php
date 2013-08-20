<?php
namespace Pi\Oauth\Provider\Storage\Model\Database;

use Pi\Oauth\Provider\Storage\CodeInterface;
use Pi\Oauth\Provider\Storage\Model\Database\AbstractModel;


class RefreshToken extends AbstractModel
{
    public function getToken($params)
    {
        $token = $this->model->select(array(
            'client_id'     => $params['client_id'],
            'resource_owner'=> $params['resource_owner'],
        ))->toArray();
        return $token[0];
    }

    public function get($token)
    {
        return parent::get($token,'token');
    }
}