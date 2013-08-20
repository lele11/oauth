<?php
namespace Pi\Oauth\Provider\Storage\Model\Database;

use Pi\Oauth\Provider\Storage\ValidateInterface;
use Pi\Oauth\Provider\Storage\Model\Database\AbstractModel;

class Client extends AbstractModel implements ValidateInterface
{
    public function validate($id, $secret)
    {
        $rowset = $this->model->select(array(
            'client_id'     => $id,
            'client_secret' => $secret,
        ));
        return $rowset->count() == 1 ? true : false;
    }

    public function addClient($client)
    {
        $row = $this->model->find($client['client_id'],'client_id');
        if (!$row) {
            if ($this->add($client)) {
                return $client;
            }
        }
        return false;
    }

    public function getClient($clientid)
    {
        return $this->get($clientid,'client_id');
    }

    public function getByUid($uid)
    {
        $rowset = $this->model->select(array(
            'uid' => $uid,
        ));
        if (!empty($rowset)) {
            return $rowset->toArray();
        }
        return $false;
    }
}