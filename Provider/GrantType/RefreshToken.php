<?php
namespace Pi\Oauth\Provider\GrantType;

use Pi\Oauth\Provider\Service;

/**
 * Create refresh_code if required
 *
 * @see http://tools.ietf.org/html/rfc6749#section-4.1.4 Optional for authorization_code grant_type
 * @see http://tools.ietf.org/html/rfc6749#section-4.2.2 Must not for implicit grant_type
 * @see http://tools.ietf.org/html/rfc6749#section-4.3.3 Optional for password grant_type
 * @see http://tools.ietf.org/html/rfc6749#section-4.4.3 Must not for client_credentials grant_type
 */
class RefreshToken extends AbstractGrantType
{
    protected $identifier = 'refresh_token';

    protected function validateRequest()
    {
        $request = $this->getRequest();
        if (!$request->getRequest('client_id')) {
            $this->setError('invalid_request');
            return false;
        }
        if (!$request->getRequest('refresh_token')) {
            $this->setError('invalid_request');
            return false;
        }

        return true;
    }

    protected function authenticate()
    {
        $request = $this->getRequest();
        $token = $request->getRequest('refresh_token');
        $tokenData = Service::storage('refresh_token')->get($token);
        if (!$tokenData) {
            $this->setError('invalid_grant');
            return false;
        }

        if ($tokenData['client_id'] != $request->getRequest('client_id')) {
            $this->setError('invalid_grant');
            return false;
        }

        if ($tokenData['expires'] < time()) {
            $this->setError('invalid_grant');
            return false;
        }
        
        $request->setParameters($tokenData);
        return true;
    }

    /*
    * return new access token 
    */
    public function createToken($createRreshToken = false)
    {
        $request = $this->getRequest();
        $params = array(
            'client_id' => $request->getRequest('client_id'),
            'resource_owner' => $request->getRequest('resource_owner'),
        );
        $token = Service::storage('access_token')->updateToken($params);
        $token['refresh_token'] = $request->getRequest('refresh_token');
        return $token;
    }
}