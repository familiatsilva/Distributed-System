<?php
include_once('env.php');
include_once('RequestClass.php');

class AppClass
{
	public function sendRequestToConsumer($fields)
	{
		$fields_string = http_build_query($fields);

		$ch = curl_init();

		curl_setopt($ch,CURLOPT_URL, getEnv('CONSUMER_URL'));
		curl_setopt($ch,CURLOPT_POST, true);
		curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);

		curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 

		$result = curl_exec($ch);

		return json_decode($result);
	}	

	public function __construct(RequestClass $request)
	{
		if(!$request->get('action') || !method_exists($this, $request->get('action')))
		{
			$request->response(503,array('msg'=>'Action Undefined.'));
		}

		$action = $request->get('action');

		$this->$action($request);
	}

	public function recoveryPassword(RequestClass $request)
	{
		$username = trim($request->get('username'));

		if(!$username)
		{
			$request->response(422,array('msg'=>'Username is required'));
		}

		$user = $this->sendRequestToConsumer([
			'action' => 'getUser',
			'username' => $username
		]);
		
		if($user->error)
		{
			$request->response(404,array('msg'=>$user->data));
		}

		if((int)$user->data->status != 1)
		{
			$request->response(404,array('msg'=>'User is not active'));
		}
		
		$result = $this->sendRequestToConsumer([
			'action' 	=> 'addQueue',
			'queue' 	=> 'recoveryPassword',
			'data' 		=> json_encode(array(
				'email' 	=> $user->data->email,
				'password' 	=> $user->data->password
			))
		]);

		if($result->error)
		{
			$request->response(404,array('msg'=>'We were unable to process your request.'));
		}

		$request->response(404,array('msg'=>'Your request has been sent successfully and you will soon receive an email with your password.'));
	}

	public function login(RequestClass $request)
	{
		$username = trim($request->get('username'));
		$password = trim($request->get('password'));

		if(!$username || !$password)
		{
			$request->response(422,array('msg'=>'Username and password is required'));
		}

		$user = $this->sendRequestToConsumer([
			'action' => 'getUser',
			'username' => $username
		]);
		
		if($user->error)
		{
			$request->response(404,array('msg'=>$user->data));
		}

		if((int)$user->data->status != 1)
		{
			$request->response(404,array('msg'=>'User is not active'));
		}

		if($user->data->password != $password)
		{
			$request->response(404,array('msg'=>'Username and password is wrong.'));
		}		

		$request->response(200,array('msg'=>'Data OK, you can access the platform.'));
	}
}

new AppClass(new RequestClass($_POST));