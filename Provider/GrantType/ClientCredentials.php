<?php
namespace Pi\Oauth\Provider\GrantType;

use Pi\Oauth\Provider\Service;

class ClientCredentials extends AbstractGrantType
{
    protected $identifier = 'client_credentials';

    protected function validateRequest()
    {
        $request = $this->getRequest();
        if (!$request->getRequest('client_id') || !$request->getRequest('client_secret')) {
            $this->setError('invalid_request');
            return false;
        }

        return true;
    }

    protected function authenticate()
    {
        $request = $this->getRequest();
        $client_id = $request->getRequest('client_id');
        $client_secret = $request->getRequest('client_secret');
        if (!Service::storage('client')->validate($client_id, $client_secret)) {
            $this->setError('invalid_client');
            return false;
        }
        /**
        * client id as resource owner
        */
        $request->setParameters(array('resource_owner' => $client_id));
        return true;
    }

    public function createToken($createRreshToken = false)
    {
        $request = $this->getRequest();

        // @see http://tools.ietf.org/html/rfc6749#section-4.1.4 Optional for authorization_code grant_type
        $createFreshToken = Service::server('grant')->hasGrantType('refresh_token');
        $token = parent::createToken($createFreshToken);


        return $token;
    }
}