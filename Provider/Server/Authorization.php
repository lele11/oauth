<?php
namespace Pi\Oauth\Provider\Server;

use Pi\Oauth\Provider\Service;
use Pi\Oauth\Provider\Http\Request;

/**
 * Authorization server methods
 * - authorize:
 */
class Authorization extends AbstractServer
{
    /**
     * List of possible authentication response types.
     * The "authorization_code" mechanism exclusively supports 'code'
     * and the "implicit" mechanism exclusively supports 'token'.
     *
     * @var array
     * @see http://tools.ietf.org/html/rfc6749#section-4.1.1
     * @see http://tools.ietf.org/html/rfc6749#section-4.2.1
     */
    protected $responseTypes = array(
        'code'  => '',
        'token' => '',
    );
    protected $errorType = 'authorization_error';

    public function setConfig(array $config)
    {
        if (isset($config['response_types'])) {
            $this->setResponseTypes($config['response_types']);
            unset($config['response_types']);
        }

        parent::setConfig($config);
        return $this;
    }

    public function setResponseTypes(array $types)
    {
        $this->responseTypes = array();
        foreach ($types as $type) {
            $this->responseTypes[$type] = '';
        }
        return $this;
    }

    protected function responseType($type)
    {
        if (!isset($this->responseTypes[$type])) {
            return false;
        }

        if (!$this->responseTypes[$type] instanceof AbstractResponseType) {
            $this->responseTypes[$type] = Service::responseType($type);
        }

        return $this->responseTypes[$type];
    }

    /**
    * valid the request , check parameters:client_id, response_type, redirect_uri, scope
    * @return bool
    */
    public function validateRequest()
    {
        $request = $this->getRequest();

        /** Make sure a valid client id was supplied (we can not redirect because we were unable to verify the URI)  
        * get client id and secret form HTTP Basic Authorization
        */
        if (!$request->getRequest('client_id')) {          
            $this->setError('invalid_request','there is parameters missing : client_id');
            return false;            
        }
        $clientId = $request->getRequest('client_id');

        if (!$clientId) {
            // We don't have a good URI to use
            $this->setError('invalid_request','require client id');
            return false;
        }

        // Make sure a valid response_type was supplied
        $responseType = $request->getRequest('response_type');
        if (!$responseType) {
            $this->setError('invalid_request','resopnse type is required');
            return false;
        }
        if (!isset($this->responseTypes[$responseType])) {
            $this->setError('unsupported_response_type');
            return false;
        }

        // Make sure a valid redirect_uri was supplied
        // @see http://tools.ietf.org/html/rfc6749#section-3.1.2
        $redirectUri = $request->getRequest('redirect_uri');
        if ($redirectUri) {
            $redirectUri = urldecode($redirectUri);
            $parts = parse_url($redirectUri);
            if (!empty($parts['fragment'])) {
                $this->setError('invalid_request','redirect url error ');
                return false;
            }
        }

        // Get client details
        $clientData = Service::storage('client')->getClient($clientId);
        if (!$clientData) {
            $this->setError('unauthorized_client');
            return false;
        }

        // Check only public clients are allowed for token response_type
        if ('public' != $clientData['type'] && 'token' == $responseType) {
            $this->setError('unsupported_response_type');
            return false;
        }

        // Make sure a valid redirect_uri must match the clientData URI.
        // @see http://tools.ietf.org/html/rfc6749#section-3.1.2
        $redirectUri = $this->validateRedirectUri($redirectUri, $clientData['redirect_uri']);
        if (!$redirectUri) {
            $this->setError('invalid_request', 'redirect url is not validate');
            return false;
        }

        // Make sure a valid scope was supplied
        $scope = $request->getRequest('scope');
        $scopeRequested = Service::scope($scope);
        $scopeGranted = Service::scope($clientData['scope']);
        if (!$scopeRequested->isSubsetOf($scopeGranted)) {
            $this->setError('invalid_scope');
            return false;
        }
        return true;
    }

    public function process(Request $request = null)
    {
        $this->setRequest($request);
        if (!$this->validateRequest()) {
            return false;
        }
        /**
        *  request is validate
        */
        $params = array(
            'client_id'      => $request->getRequest('client_id'),
            'response_type'  => $request->getRequest('response_type'),
            'redirect_uri'   => $request->getRequest('redirect_uri'),
            'scope'          => $request->getRequest('scope'),
            'state'          => $request->getRequest('state'),
            'resource_owner' => $request->getRequest('resource_owner'),
        );      

        $redirectUri = $params['redirect_uri'];
        $responseType = $params['response_type'];
        $result = $this->responseType($responseType)->process($params);
        $uri = $this->buildUri($redirectUri, $result);
        $this->result = Service::result('redirect', $uri);
        return true;
    }

    public function setError($error, $errorDescription = null, $errorUri = null, $statusCode = 400)
    {
        $this->result = Service::error('authorization_error', $error, $errorDescription, $errorUri, $statusCode);
        return $this;
    }

    /**
     * Build the absolute URI based on supplied URI and parameters.
     *
     * @param $uri
     * An absolute URI.
     * @param $params
     * Parameters to be append as GET.
     *
     * @return
     * An absolute URI with supplied parameters.
     *
     * @ingroup oauth2_section_4
     */
    protected function buildUri($uri, $params)
    {
        $parseUrl = parse_url($uri);

        // Add our params to the parsed uri
        foreach ( $params as $k => $v ) {
            if (isset($parseUrl[$k])) {
                $parseUrl[$k] .= "&" . http_build_query($v);
            } else {
                $parseUrl[$k] = http_build_query($v);
            }
        }

        // Put humpty dumpty back together
        return
            ((isset($parseUrl["scheme"])) ? $parseUrl["scheme"] . "://" : "")
            . ((isset($parseUrl["host"])) ? $parseUrl["host"] : "")
            . ((isset($parseUrl["path"])) ? $parseUrl["path"] : "")
            . ((isset($parseUrl["query"])) ? "?" . $parseUrl["query"] : "")
            . ((isset($parseUrl["fragment"])) ? "#" . $parseUrl["fragment"] : "")
        ;
    }

    /**
     * Validates redirect_uri
     *
     * @see http://tools.ietf.org/html/rfc6749#section-3.1.2
     */
    protected function validateRedirectUri($redirectUriRequested, $redirectUriRegistered)
    {
        if (!$redirectUriRequested && !$redirectUriRegistered) {
            return false;
        }
        if (!$redirectUriRequested && !is_scalar($redirectUriRegistered)) {
            return false;
        }
        if (!$redirectUriRegistered) {
            return $redirectUriRequested;
        }
        if (!$redirectUriRequested) {
            return $redirectUriRegistered;
        }
        $redirectUriRegistered = (array) $redirectUriRegistered;
        foreach ($redirectUriRegistered as $uri) {
            if (substr($redirectUriRequested, 0, strlen($uri)) == $uri) {
                return $redirectUriRequested;
            }
        }
        return false;
    }
}