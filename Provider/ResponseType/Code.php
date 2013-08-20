<?php
namespace Pi\Oauth\Provider\ResponseType;

use Pi\Oauth\Provider\Service;

class Code extends AbstractResponseType
{
    protected function validateRequest()
    {
        
    }
    public function process(array $params)
    {
        $code = Service::storage('authorization_code')->add(array(
            'client_id'     => $params['client_id'],
            'redirect_uri'  => $params['redirect_uri'],
            'scope'         => $params['scope'],
            'resource_owner'=> $params['resource_owner'],
        ));

        // build the URL to redirect to
        $result = array('query' => array(
            'code'  => $code,
        ));

        if (isset($params['state'])) {
            $result['query']['state'] = $params['state'];
        }

        return $result;
    }
}