<?php
namespace Module\Oauth\Lib;

use Pi;
use Pi\Mvc\Controller\ActionController ;

class UserHandler 
{
    static function getUserinfo() 
    {
        $identity = Pi::service('authentication')->getIdentity();
        if($identity) {
            $row = Pi::model('user')->find($identity, 'identity');
            return array(
                'id' => $row->id,
                'email' => $row->email,
                'name' => $row->name,
            );
        } 
    }

    static function getUserid()
    {
        $identity = Pi::service('authentication')->getIdentity();
        if($identity) {
            $row = Pi::model('user')->find($identity, 'identity');
            return $row->id;
        } 
    }

    static function isLogin()
    {
        return Pi::service('authentication')->hasIdentity();
    }

    static function userlogin($data) 
    {
        $result = Pi::service('authentication')->authenticate($data['identity'], $data['credential']);            
        $sataus = $result->isValid();
        return $sataus;
    }

    static function setUserAuthorize($data)
    {
        $model = Pi::model('user_authorization','oauth');
        $rowset = $model->select(array('cid' => $data['cid'],'uid' => $data['uid']));
        if (!$rowset->toArray()) {
            $row = $model->createRow($data);
            $row->save(); 
        }
    }

    static function logout()
    {
        Pi::service('session')->manager()->destroy();
    }
}
