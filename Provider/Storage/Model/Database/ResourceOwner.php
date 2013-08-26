<?php
namespace Pi\Oauth\Provider\Storage\Model\Database;

use Pi\Oauth\Provider\Storage\ValidateInterface;
use Pi\Oauth\Provider\Storage\Model\Database\AbstractModel;

class ResourceOwner extends AbstractModel implements ValidateInterface
{
    public function validate($username, $password)
    {
        $result = false;
        $row = $this->model->find($username, 'identity');
        if ($row) {
            if ($row->transformCredential($password) == $row->getCredential()) {
                $result = true;
            }
        }
        return $result;
    }
}