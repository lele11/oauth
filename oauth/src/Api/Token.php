<?php
namespace Module\Oauth\Api;

use Pi;
use Pi\Application\AbstractApi;

class Token extends AbstractApi
{
	protected $module = 'oauth';

	/**
	* this method is not support client credentials type to vertify token
	*/
	public function check($token)
	{
		$row = Pi::model('access_token', 'oauth')->find($token, 'token');
		if ($row) {
			$data = $row->toArray();
			if ($data['expires'] > time()) {
				return array(
				'uid' => $data['resource_owner'],
				'scope'=> $data['scope'],
				);
			}
		}
	}
}