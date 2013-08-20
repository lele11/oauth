<?php
namespace Pi\Oauth\Provider\Http;

use Zend\Http\PhpEnvironment\Request as HttpRequest;

class Request extends HttpRequest
{
    protected $parameters = array();

    public function getRequest($name)
    {
        if (isset($this->parameters[$name])) {
            $result = $this->parameters[$name];
        } else {
            $result = $this->getPost($name);
        }
        if (null === $result) {
            $result = $this->getQuery($name);
        }
        return $result;
    }

    public function setParameters($params = array())
    {
        $this->parameters = array_merge($this->parameters,$params);
        return $this;
    }
}