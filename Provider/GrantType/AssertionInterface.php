<?php
namespace Pi\Oauth\Provider\GrantType;

/**
 * Implements the Assertion Framework for OAuth 2.0
 * @see http://tools.ietf.org/html/draft-ietf-oauth-assertions-10
 */
interface AssertionInterface
{
    public function validateAssertion($assertion);
    public function transformAssertion($assertion);
}
