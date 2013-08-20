<?php
namespace Pi\Oauth\Provider\Result;

use Pi\Oauth\Provider\Http\Response as HttpResponse;

class Redirect extends HttpResponse
{
    public function __construct($uri)
    {
        $this->getHeaders()->addHeaderLine('Location', $uri);
        // $this->getHeaders()->addHeaderLine('Content-Type','application/x-www-form-urlencoded');
        $this->setStatusCode(302);
    }
}