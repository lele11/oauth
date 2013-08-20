<?php
namespace Pi\Oauth\Provider\GrantType;

use Pi\Oauth\Provider\Service;
use Pi\Oauth\Provider\GrantType\AbstractGrantType;

/**
* Implements the SAML 2.0 Bearer Assertion Profiles for OAuth 2.0.
* @see http://tools.ietf.org/html/draft-ietf-oauth-saml2-bearer-15
*/
class SamlsBearer extends AbstractGrantType implements AssertionInterface
{
    protected $identifier = 'urn:ietf:params:oauth:grant-type:saml2-bearer';
}