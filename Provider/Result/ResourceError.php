<?php
namespace Pi\Oauth\Provider\Result;

/**
 * @see http://tools.ietf.org/html/rfc6749#section-7.2
 */
class ResourceError extends GrantError
{
    protected $errorType = 'token';
    protected $errors = array(
        'invalid_token'           =>  'The token is valid or expire',
   );
}