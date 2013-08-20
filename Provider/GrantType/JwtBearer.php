<?php
namespace Pi\Oauth\Provider\GrantType;

use Pi\Oauth\Provider\Service;
use Pi\Oauth\Provider\GrantType\AbstractGrantType;
use Pi\Oauth\Provider\Utility\Jwt as JwtEncryption;

/**
* Implements the JSON Web Token (JWT) Bearer Token Profiles for OAuth 2.0.
* @see http://tools.ietf.org/html/rfc6750
*/
class JwtBearer extends AbstractGrantType implements AssertionInterface
{
    protected $identifier = 'urn:ietf:params:oauth:grant-type:jwt-bearer';

    protected $assertionParam;
    protected $assertionData;
    protected $audience;
    protected $jwt;

    /**
     * Validates the assertion data using the rules in the IETF draft.
     * @see http://tools.ietf.org/html/draft-ietf-oauth-jwt-bearer-04#section-3
     */
    public function validateAssertion($assertion)
    {
        $requiredData = array('iss', 'sub', 'aud', 'exp', 'nbf', 'jti');
        foreach ($requiredData as $key) {
            if (!isset($assertion[$key])) {
                return false;
            }
        }

        //Check the expiration time
        $expiration = $assertion['exp'];
        if (!ctype_digit($expiration) || $expiration <= time()) {
            return false;
        }

        //Check the not before time
        $notBefore = $assertion['nbf'];
        if ($notBefore) {
            if (!ctype_digit($notBefore) || $notBefore > time()) {
                return false;
            }
        }

        //Check the audience if required to match
        $aud = $assertion['aud'];
        if ($aud != $this->config['audience']) {
            return false;
        }

        //Get the iss's public key (http://tools.ietf.org/html/draft-ietf-oauth-json-web-token-06#section-4.1.1)
        $key = Service::storage('jwt')->getClientKey($assertion['iss'], $assertion['sub']);
        $jwt = JwtEncryption::decode($this->assertionParam, $key, true);
        if (!$jwt) {
            return false;
        }

        return true;
    }

    public function transformAssertion($assertion)
    {
        $data = (array) $assertion;
        $data['client_id'] = $data['iss'];
        $data['resource_owner'] = $data['sub'];
        return $data;
    }

    protected function validateRequest()
    {
        $request = $this->getRequest();
        $assertion = $request->request("assertion");
        if (!$assertion) {
            $this->setError('invalid_request');
            return false;
        }
        $this->assertionParam = $assertion;

        //Decode the JWT
        $jwt = JwtEncryption::decode($assertion);
        if (!$jwt) {
            $this->setError('invalid_request');
            return false;
        }
        if (!$this->validateAssertion($jwt)) {
            $this->setError('invalid_grant');
            return false;
        }

        $this->assertionData = $this->transformAssertion($jwt);

        return true;
    }

    protected function authenticate()
    {
        return true;
    }

    public function createToken($createRreshToken = false)
    {
        $request = $this->getRequest();
        $params = array(
            'client_id'         => $this->assertionData('client_id'),
            'scope'             => $request->getRequest('scope'),
            'resource_owner'    => $this->assertionData('resource_owner'),
        );
        $tokenData = Service::storage('access_token')->add($params);
        return $tokenData;
    }
}
