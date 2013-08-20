<?php
namespace Pi\Oauth\Provider\Storage;

class AccessToken extends AbstractStorage implements CodeInterface
{
    public function add($params)
    {
        if (!$params) {
            return false;
        }
        if (!isset($params['token'])) {
            $params['token'] = $this->generateCode($this->config['length']);
        }

        $tokenData = $this->model->getToken($params);
        if (empty($tokenData)) {
            parent::add($params);
        }
        
        return array(
            'token_type'    => $this->config['token_type'],
            'expires_in'    => $this->config['expires_in'],
            'access_token'  => $params['token'],
        );
    }

    public function updateToken($params)
    {
        if (!isset($params['token'])) {
            $params['token'] = $this->generateCode($this->config['length']);
        }

        $tokenData = $this->model->getToken($params);
        parent::update($tokenData['id'],$params);
        return array(
            'token_type'    => $this->config['token_type'],
            'expires_in'    => $this->config['expires_in'],
            'access_token'  => $params['token'],
        );
    }

    public function get($token)
    {
        return $token = $this->model->get($token,'token');
    }
}