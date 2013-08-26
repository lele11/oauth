<?php
namespace Pi\Oauth\Provider\GrantType;

use Pi\Oauth\Provider\Service;

class AuthorizationCode extends AbstractGrantType
{
    protected $identifier = 'authorization_code';

    protected function validateRequest()
    {
        $request = $this->getRequest();

        if (!$request->getRequest('code')) {
            $this->setError('invalid_request');
            return false;
        }

        return true;
    }

    protected function authenticate()
    {
        $request = $this->getRequest();
        $code = $request->getRequest('code');
        $codeData = Service::storage('authorization_code')->getbyCode($code);
        if (!$codeData) {
            $this->setError('invalid_grant', 'invalid Authorization code');
            return false;
        }

        if ($codeData['client_id'] != $request->getRequest('client_id')) {
            $this->setError('invalid_grant', 'this code is not grant');
            return false;
        }

        if ($codeData['expires'] < time()) {
            Service::storage('authorization_code')->expire();
            $this->setError('invalid_grant', 'this code is expired');
            return false;
        }

        /*
         * 4.1.3 - ensure that the "redirect_uri" parameter is present if the "redirect_uri" parameter was included in the initial authorization request
         * @uri - http://tools.ietf.org/html/rfc6749#section-4.1.3
         */
        if (!empty($codeData['redirect_uri'])) {
            if (!$request->getRequest('redirect_uri') || urldecode($request->getRequest('redirect_uri')) != $codeData['redirect_uri']) {
                $this->setError('invalid_grant', 'redirect uri is not match');
                return false;
            }
        }
        /**
        * set authorization code scope and resource owner
        */
        $request->setParameters(array(
            'scope'             => $codeData['scope'],
            'resource_owner'    => $codeData['resource_owner']
        ));

        return true;
    }

    public function createToken($createRreshToken = false)
    {
        $request = $this->getRequest();
        Service::storage('authorization_code')->deleteCode($request->getRequest('code'));

        // @see http://tools.ietf.org/html/rfc6749#section-4.1.4 Optional for authorization_code grant_type
        $createFreshToken = Service::server('grant')->hasGrantType('refresh_token');
        $token = parent::createToken($createFreshToken);

        return $token;
    }
}