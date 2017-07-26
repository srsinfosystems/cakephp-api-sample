<?php
App::uses('AppController', 'Controller');
App::uses('CakeEmail', 'Network/Email');

class UsersController extends AppController {

	public $components = array('Paginator', 'RequestHandler', 'Auth', 'Session');
	public $uses = array('User', 'City', 'CitiesUser', 'Area', 'Neighborhood', 'Category', 'Feature', 'BottleType');

	function beforeFilter() {
        parent::beforeFilter();
        
        $this->Auth->allow('register');
        $this->Auth->allow('login');
        $this->Auth->allow('social_login');
        $this->Auth->allow('social_register');
        $this->Auth->allow('logout');
        $this->Auth->allow('save_profile');
        $this->Auth->allow('change_password');
        
        $this->Auth->allow('reset_password_request');
        $this->Auth->allow('send_feedback');
        
        //@10/09/2014 by 037, 
        $this->Auth->allow('get_message_list');
        $this->Auth->allow('delete_message');
        $this->Auth->allow('send_message');
        $this->Auth->allow('get_users_data');
        $this->Auth->allow('check_availability');
        
          
        $this->Auth->allow('check_activation');
        $this->Auth->allow('update_password');

        $this->Auth->allow('get_cities_new');

	}  
   
	# @19/08/2014 by 003, Filter/trim the input parameter or set the default value
	public function getParams($field, $default_value = '') {
		if(!is_array($default_value))
			$value = isset($this->data[$field]) ? trim($this->data[$field]) : $default_value;
		else
			$value = isset($this->data[$field]) ? $this->data[$field] : $default_value;
		return $value;
	}
	
	# @10/09/2014 by 037, Filter/trim the input parameter or set the default value
	public function humanTiming ($time)
	{

		$time = time() - $time; // to get the time since that moment

		$tokens = array (
			31536000 => 'year',
			2592000 => 'month',
			604800 => 'week',
			86400 => 'day',
			3600 => 'hour',
			60 => 'minute',
			1 => 'second'
		);

		foreach ($tokens as $unit => $text) {
			if ($time < $unit) continue;
			$numberOfUnits = floor($time / $unit);
			return $numberOfUnits.' '.$text.(($numberOfUnits>1)?'s':'');
		}

	}
	
	
	/**
	 ** @purpose register a new user
	 ** @input auth_type, role_id, name, username, password, conf_password, email, agree_tnc (y/n)
	 ** @output 
	 */
	public function register() {
		$auth_type = $this->getParams('auth_type', 'web');
		$role_id = $this->getParams('role_id', '2');
		$name = $this->getParams('name');
		$username = $this->getParams('username');
		$password = $this->getParams('password');
		$conf_password = $this->getParams('conf_password');
		$email = $this->getParams('email');
		$gender = $this->getParams('gender');
		$dob = $this->getParams('dob');
		$converted_dob = strtotime($dob);
		$converted_dob_show = str_replace('-', '', $dob);
		$eligibility_dob = date('Ymd', strtotime("-18 years"));
		$eligibility_dob_show = date('Y-m-d', strtotime("-18 years"));
		$agree_tnc = $this->getParams('agree_tnc');
		
				
		$validationErrors = array();
		if($auth_type == 'iphone') {
			if(strlen($password) < 6)
				$validationErrors['password'][] = 'Passwords must be at least 6 characters long';
			else if(!empty($password) && $password != $conf_password)
				$validationErrors['password'][] = 'Password and confirm password does not match';
			if($agree_tnc != 'y')
				$validationErrors['agree_tnc'][] = 'Terms and conditions is not selected';	
		}
		else {
			if(strlen($password) < 6)
				$validationErrors['password'] = 'Passwords must be at least 6 characters long';
			else if(!empty($password) && $password != $conf_password)
				$validationErrors['password'] = 'Password and confirm password does not match';
			if($agree_tnc != 'y')
				$validationErrors['agree_tnc'] = 'Terms and conditions is not selected';	
		}
		
		if(!in_array($gender, array('m', 'f')))
			$validationErrors['gender'] = 'Select a gender';
			
		if(!empty($dob)) {
			if(empty($converted_dob_show) || $converted_dob_show < 0)
				$validationErrors['dob'] = 'Invalid date of birth';
			else if($converted_dob_show > $eligibility_dob)
				$validationErrors['dob'] = 'You are not 18 years old. DOB before ' . $eligibility_dob_show . ' is only allowed.';
		}
		
$activationKey = md5(uniqid());
		$password = $this->User->get_encrypted_password($password);
		$today = date('Y-m-d H:i:s');
		$user_input = array(
			'role_id'	=> $role_id,
			'name'		=> $name,
			'gender'  	=> $gender,
			'dob'  		=> $dob,
			'username'	=> $username,
			'password'	=> $password,
			'email'		=> $email,
			'status'	=> '1',
			'created'	=> $today,
			'updated'	=> $today,
'activation_key' => $activationKey,
			);
		
		if(empty($validationErrors)) {
			// validate the user information
			$this->User->set( $user_input );
			if(!$this->User->validates()) {
				$validationErrors = $this->User->validationErrors;
			}
		}
		
		if(!empty($validationErrors)) {
			$response = array(
				'status' => 'error',
				'operation' => 'register',
				'validationErrors' => $validationErrors,
				);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}
		
		$this->User->save($user_input);
		
		$user_id = $this->User->getLastInsertId();
		
					//*************** save data in acos table start ***************
					$this->Aro = ClassRegistry::init('Aro');
					$this->Aro->create();
					$user_input_aros = array(
						'alias'	=> $username,
						'parent_id' => 1,
						'model' => 'User',
						'foreign_key' => $user_id,
					);					
					
					if(!$this->Aro->save($user_input_aros)) {							
						//$response = array('status' => "error", 'operation' => "save_venue_profile", 'dataErrors' => $this->User->validationErrors, 'line' => __LINE__);
						//$this->set(array('response' => $response, '_serialize' => array('response')));
						//return;							
					}	
										 					
					//*************** save data in acos table end ***************				

		// Make entry in the user_auth table
		$auth_key = $this->build_auth_key($username, $auth_type);
		$auth_input = array(
			'key'	=> $auth_key,
			'username'	=> $username,
			'user_id'	=> $user_id,
			'email'		=> $email,
			'auth_type'	=> $auth_type,
			'ip'	=> isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '',
			'user_agent' => isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '',
			);
		$this->set_auth_key($auth_input);
		
		$userData = array(
			'auth_key' => $auth_key,
			'user_id' => $user_id,
			'username' => $this->data['username'],
			//'name' => $this->data['name'],
			'gender' => $this->data['gender'],
			'dob' => $this->data['dob'],
			'email' => $this->data['email'],
			'password' => $this->data['password'],
		);
		
		$data = array('msg'	=> 'User has been registered successfully', 'data' => $userData);
		$response = array(
			'status' => 'success',
			'operation' => 'register',
			'data' => $data,
			);
		
		$this->set(array(
            'response' => $response,
            '_serialize' => array('response'),
        ));
	}


	/**
	 ** @purpose authenticate the user
	 ** @input auth_type (android/iphone/web), username, password
	 ** @output On success array of user information
	 */
	public function login() {
		$auth_type = $this->getParams('auth_type', 'web');
		$username = $this->getParams('username');
		$password = $this->getParams('password');
		$device_token = $this->getParams('device_token');
		
		$password = $this->User->get_encrypted_password($password);
	
		$validationErrors = array();
		if(empty($username) || empty($password)) {
			$validationErrors = array('login' => 'Either username and/or password is empty.');
		} else {
			$options = array(
				'conditions' => array('User.username' => $username, 'User.password' => $password),
				'fields' => array('Role.*', 'User.id', 'User.city_id', 'User.username', 'User.name', 'User.email', 'User.status', 'User.address', 'User.gender', 'User.dob', 'User.website', 'User.activation_key', 'User.bio', 'User.image', 'User.timezone', 'User.updated', 'User.created'),
				);
			$user_data = $this->User->find('first', $options);
			if(empty($user_data)) {
				$validationErrors = array('login' => 'Either username and/or password does not match.');
			} else if($user_data['User']['status'] != '1') {
				$validationErrors = array('login' => 'Your account is not activated yet.');
			}
		}
		//print_r($user_data['User']);exit;
		/*set null value blank */
		if(empty($user_data['User']['city_id']) || is_null($user_data['User']['city_id']) || $user_data['User']['city_id'] == null){
				$user_data['User']['city_id'] ='';
			}
		if(empty($user_data['User']['website']) || is_null($user_data['User']['website']) || $user_data['User']['website'] == null){
				$user_data['User']['website'] ='';
			}
		if(empty($user_data['User']['bio']) || is_null($user_data['User']['bio']) || $user_data['User']['bio'] == null){
				$user_data['User']['bio'] ='';
			}
		if(empty($user_data['User']['image']) || is_null($user_data['User']['image']) || $user_data['User']['image'] == null){
				$user_data['User']['image'] ='';
			}
		if(empty($user_data['User']['activation_key']) || is_null($user_data['User']['activation_key']) || $user_data['User']['activation_key'] == null){
				$user_data['User']['activation_key'] ='';
			}
		if(empty($user_data['User']['dob']) || is_null($user_data['User']['dob']) || $user_data['User']['dob'] == null){
				$user_data['User']['dob'] ='0000-00-00';
			}
		if(empty($user_data['User']['gender']) || is_null($user_data['User']['gender']) || $user_data['User']['gender'] == null){
				$user_data['User']['gender'] ='';
			}					
		/*set null value blank */	
		if(empty($validationErrors)) {
			
			//Check if user have city
			$city_opt = array(
					'conditions' => array('City.id' => $user_data['User']['city_id']),
					'fields' => array('City.id','City.name', 'City.state'),
					);	
			$cityExists = $this->City->find('first', $city_opt);
			if(!empty($cityExists) && isset($cityExists['City'])) {
				$user_data['User']['city'] = $cityExists['City']['name'];
				$user_data['User']['state'] = $cityExists['City']['state'];
			}
			else {
				$user_data['User']['city'] = '';
				$user_data['User']['state'] = '';
			}
			
			$user_data['User']['Role'] = $user_data['Role'];
			
			// Make entry in the user_auth table
			$auth_key = $this->build_auth_key($username, $auth_type);
			$auth_input = array(
				'key'	=> $auth_key,
				'username'	=> $username,
				'user_id'	=> $user_data['User']['id'],
				'email'		=> $user_data['User']['email'],
				'auth_type'	=> $auth_type,
				'ip'	=> isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '',
				'user_agent' => isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '',
				);
			$this->set_auth_key($auth_input);
			
			//@07/11/2014 by 037, Save device token
			if(!empty($device_token)) {
				if($auth_type == 'iphone') {
					$update_token = array(
						'id' => $user_data['User']['id'],
						'ios_device_token' => $device_token
					);
					$this->User->save($update_token);
				}
				if($auth_type == 'android') {
					$update_token = array(
						'id' => $user_data['User']['id'],
						'android_device_token' => $device_token
					);
					$this->User->save($update_token);
				}
			}
				
			$get_cities = '';	
			$get_cities = $this->get_cities_new();
				
			$response = array(
				'status' => 'success',
				'operation' => 'login',
				'data' => $user_data['User'],
				'auth_key'	=> $auth_key,
				'area_list'	=> $get_cities,
				);
		} else {
			$response = array(
				'status' => 'error',
				'operation' => 'login',
				'validationErrors' => $validationErrors,
 				);
		}
		
		$this->set(array(
            'response' => $response,
            '_serialize' => array('response'),
        ));
	}
	
	public function social_login() {
		$auth_type = $this->getParams('auth_type', 'web');
		$email = $this->getParams('email');
		$socialtoken = $this->getParams('socialtoken');
		$name = $this->getParams('name');
		$gender = $this->getParams('gender');
		$device_token = $this->getParams('device_token');
		
	
		$validationErrors = array();
		if(empty($email) || empty($socialtoken)) {
			$validationErrors = array('login' => 'Unable to authenticate social media login');
		} else {
			
			if(!empty($email)) {
				$options = array(
				'conditions' => array('User.email' => $email),
				'fields' => array('Role.*', 'User.id', 'User.city_id', 'User.username', 'User.name', 'User.email', 'User.status', 'User.address', 'User.gender', 'User.dob', 'User.website', 'User.activation_key', 'User.bio', 'User.image', 'User.timezone', 'User.updated', 'User.created'),
				);
				$user_data = $this->User->find('first', $options);
				if(!empty($user_data)) {
					$sql = "UPDATE users SET social_key='".$socialtoken."' WHERE email='".$email."'";
					$this->User->query($sql);
				}
			}
			/*
			else if(!empty($socialtoken)) {
				$options = array(
				'conditions' => array('User.social_key' => $socialtoken),
				'fields' => array('Role.*', 'User.id', 'User.city_id', 'User.username', 'User.name', 'User.email', 'User.status', 'User.address', 'User.gender', 'User.dob', 'User.website', 'User.activation_key', 'User.bio', 'User.image', 'User.timezone', 'User.updated', 'User.created'),
				);
				$user_data = $this->User->find('first', $options);
			}
			*/
			//print_r($user_data['User']['dob']);
			
			if(empty($user_data)) {
				$register = $this->social_register($email, $socialtoken, $auth_type, $name, $gender);
				if($register) {
					$options = array(
							'conditions' => array('User.email' => $email),
							'fields' => array('Role.*', 'User.id', 'User.city_id', 'User.username', 'User.name', 'User.email', 'User.status', 'User.address', 'User.gender', 'User.dob', 'User.website', 'User.activation_key', 'User.bio', 'User.image', 'User.timezone', 'User.updated', 'User.created'),
							);
					$user_data = $this->User->find('first', $options);
				}
				else {
					$validationErrors = array('login' => 'Unable to register new user');
				}
				# Try to get again
				
			} else if($user_data['User']['status'] != '1') {
				$validationErrors = array('login' => 'Your account is not activated yet.');
			}
			if(empty($user_data['User']['dob']) || is_null($user_data['User']['dob']) || $user_data['User']['dob'] == null){
				$user_data['User']['dob'] ='0000-00-00';
			}
			
		}
		/*set null value blank */
		if(empty($user_data['User']['city_id']) || is_null($user_data['User']['city_id']) || $user_data['User']['city_id'] == null){
				$user_data['User']['city_id'] ='';
			}
		if(empty($user_data['User']['website']) || is_null($user_data['User']['website']) || $user_data['User']['website'] == null){
				$user_data['User']['website'] ='';
			}
		if(empty($user_data['User']['bio']) || is_null($user_data['User']['bio']) || $user_data['User']['bio'] == null){
				$user_data['User']['bio'] ='';
			}
		if(empty($user_data['User']['image']) || is_null($user_data['User']['image']) || $user_data['User']['image'] == null){
				$user_data['User']['image'] ='';
			}
		if(empty($user_data['User']['activation_key']) || is_null($user_data['User']['activation_key']) || $user_data['User']['activation_key'] == null){
				$user_data['User']['activation_key'] ='';
			}
		if(empty($user_data['User']['email']) || is_null($user_data['User']['email']) || $user_data['User']['email'] == null){
				$user_data['User']['email'] ='';
			}	
		
			
		if(empty($user_data['User']['gender']) || is_null($user_data['User']['gender']) || $user_data['User']['gender'] == null){
				$user_data['User']['gender'] ='';	
	    }		
	   /*set null value blank */
		if(empty($validationErrors)) {
			
			//Check if user have city
			$city_opt = array(
					'conditions' => array('City.id' => $user_data['User']['city_id']),
					'fields' => array('City.id','City.name', 'City.state'),
					);	
			$cityExists = $this->City->find('first', $city_opt);
			if(!empty($cityExists) && isset($cityExists['City'])) {
				$user_data['User']['city'] = $cityExists['City']['name'];
				$user_data['User']['state'] = $cityExists['City']['state'];
			}
			else {
				$user_data['User']['city'] = '';
				$user_data['User']['state'] = '';
			}
			
			$user_data['User']['Role'] = $user_data['Role'];
			
			// Make entry in the user_auth table
			$username = explode("@", $email);
			$auth_key = $this->build_auth_key($username[0], $auth_type);
			
			
			$auth_input = array(
				'key'	=> $auth_key,
				'username'	=> $username[0],
				'user_id'	=> $user_data['User']['id'],
				'email'		=> $user_data['User']['email'],
				'auth_type'	=> $auth_type,
				'ip'	=> isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '',
				'user_agent' => isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '',
				);
			$this->set_auth_key($auth_input);
			
			//@07/11/2014 by 037, Save device token
			if(!empty($device_token)) {
				if($auth_type == 'iphone') {
					$update_token = array(
						'id' => $user_data['User']['id'],
						'ios_device_token' => $device_token
					);
					$this->User->save($update_token);
				}
				if($auth_type == 'android') {
					$update_token = array(
						'id' => $user_data['User']['id'],
						'android_device_token' => $device_token
					);
					$this->User->save($update_token);
				}
			}
				
			$get_cities = '';	
			$get_cities = $this->get_cities_new();
				
			$response = array(
				'status' => 'success',
				'operation' => 'social_login',
				'data' => $user_data['User'],
				'auth_key'	=> $auth_key,
				'area_list'	=> $get_cities,
				);
		} else {
			$response = array(
				'status' => 'error',
				'operation' => 'login',
				'validationErrors' => $validationErrors,
 				);
		}
		
		$this->set(array(
            'response' => $response,
            '_serialize' => array('response'),
        ));
	}
	
	public function social_register($email, $social_key, $auth_type, $name, $gender) {
		$role_id = '2';
		$username = explode("@", $email);
		$username = @$username[0];
		$password = rand(12000, 50000);
		$conf_password = $password;
		$agree_tnc = 'y';
		$original_password = $password;
		$password = $this->User->get_encrypted_password($password); 
		$today = date('Y-m-d H:i:s');
		$user_input = array(
			'role_id'	=> $role_id,
			'city_id' 	=> 0,
			'name'		=> '',
			'address'	=> '',
			'username'	=> $username,
			'name'		=> $name,
			'gender'	=> $gender,
			'password'	=> $password,
			'social_rand_p' => $original_password,
			'email'		=> $email,
			'status'	=> '1',
			'created'	=> $today,
			'updated'	=> $today,
			'social_key' => $social_key,
			);
		
		$this->User->save($user_input);
		$user_id = $this->User->getLastInsertId();	
		if(!$user_id) return false;
		
		$this->Aro = ClassRegistry::init('Aro');
		$this->Aro->create();
		$user_input_aros = array(
			'alias'	=> $username,
			'parent_id' => 1,
			'model' => 'User',
			'foreign_key' => $user_id,
		);					
		
		if(!$this->Aro->save($user_input_aros)) {}	
		
		$auth_key = $this->build_auth_key($username, $auth_type);
		$auth_input = array(
			'key'	=> $auth_key,
			'username'	=> $username,
			'user_id'	=> $user_id,
			'email'		=> $email,
			'auth_type'	=> $auth_type,
			'ip'	=> isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '',
			'user_agent' => isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '',
			);
		$this->set_auth_key($auth_input);
		
		/*
		$userData = array(
			'auth_key' => $auth_key,
			'user_id' => $user_id,
			'username' => $username,
			'gender' => '',
			'dob' => '',
			'email' => $email,
			'password' => $original_password,
		);
		*/
		//send email
		$Email = new CakeEmail();
        $Email->from(array('info@shoutoutmycity.com' => 'Shoutoutcity'));
        $Email->to($email);
        $Email->subject('Registration Successfull');
        
                    
        $Email->send('Hello '.$username.',
        
                    Welcome To Shoutoutcity 
                    
                    Your Account is Registered Successfully.
                    
                    Username: '.$username.'
                    Email: '.$email.' 
                    Password: '.$original_password.'
                    Thank You for Contacting Us.
                    
                    Regards
                    The Shoutoutcity Team');
		return true;
		
					
	}
	/**
	 ** @purpose logout the user
	 ** @input auth_type, auth_key
	 ** @output
	 */
	public function logout() {
		$this->UserAuth = ClassRegistry::init('UserAuth');
			
		$auth_type = $this->getParams('auth_type', 'web');
		$auth_key = $this->getParams('auth_key');
		
		$validationErrors = array();
		if(empty($auth_key))
			$validationErrors['auth_key'] = 'Auth key is blank';
		else {
			$user_id = $this->User->get_user_id($auth_key);
			if(empty($user_id))
				$validationErrors['auth_key'] = 'Invalid auth key';
		}
		
		if(!empty($validationErrors)) {
			$response = array('status' => 'error', 'operation' => 'logout', 'validationErrors' => $validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}
		
		
		if(!$this->UserAuth->deleteAll(array('key' => $auth_key))) {
			$response = array('status' => 'error', 'operation' => 'logout', 'dataErrors' => $this->UserAuth->validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}
		
		$response = array(
			'status' => 'success',
			'operation' => 'logout',
			'data' => array('msg' => 'You have successfully logged out'));
		$this->set(array('response' => parseParams($response), '_serialize' => array('response')));
	}
	
	
	/**
	 ** @purpose save user profile
	 ** @input auth_type, auth_key, name, address, gender, dob
	 ** @output
	 */
	public function save_profile() {
		$auth_type = $this->getParams('auth_type', 'web');
		$auth_key = $this->getParams('auth_key');
		$name = $this->getParams('name');
		$address = $this->getParams('address');
		$gender = $this->getParams('gender');
		$city_id = $this->getParams('city_id');
		$dob = $this->getParams('dob');
		$converted_dob = str_replace('-', '', $dob);
		$eligibility_dob = date('Ymd', strtotime("-18 years"));
		$eligibility_dob_show = date('Y-m-d', strtotime("-18 years"));
		
		$validationErrors = array();
		if(empty($auth_key))
			$validationErrors['auth_key'] = 'Auth key is blank';
		else {
			$user_id = $this->User->get_user_id($auth_key);
			if(empty($user_id))
				$validationErrors['auth_key'] = 'Invalid auth key';
		}
		/*
		if(empty($name))
			$validationErrors['name'] = 'This is required field';
		*/
		/*	
		if(empty($address))
			$validationErrors['address'] = 'This is required field';
		*/	
		if(!in_array($gender, array('m', 'f')))
			$validationErrors['gender'] = 'Select a gender';
		if(!empty($dob)) {
			if(empty($converted_dob) || $converted_dob < 0)
				$validationErrors['dob'] = 'Invalid date of birth';
			else if($converted_dob > $eligibility_dob)
				$validationErrors['dob'] = 'You are not 18 years old. DOB before ' . $eligibility_dob_show . ' is only allowed.';
		}
		/*
		if(empty($city_id))
			$validationErrors['city_id'] = 'City ID is not seleted';
		else {
			$cityExists = $this->City->find('first', array('conditions' => array('City.id' => $city_id)));
			//print_r($cityExists); exit;
			if(empty($cityExists) || !isset($cityExists['City']))
				$validationErrors['city_id'] = 'City does not exist';
		}
		*/
		###
		if(!empty($city_id)) {
			$cityExists = $this->City->find('first', array('conditions' => array('City.id' => $city_id)));
			if(empty($cityExists) || !isset($cityExists['City']))
				$validationErrors['city_id'] = 'City does not exist';
		}
		###
		if(!empty($validationErrors)) {
			$response = array('status' => 'error', 'operation' => 'save_profile', 'validationErrors' => $validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}
		
		$user_input = array(
			'id'	=> $user_id,
			'name'	=> $name,
			'city_id'	=> $city_id,
			'address' => $address,
			'gender'  => $gender,
			'updated' => date('Y-m-d H:i:s'),
			);
		if(!empty($dob))
			$user_input['dob'] = $dob;
			
		if(!$this->User->save($user_input)) {
			$response = array('status' => 'error', 'operation' => 'save_profile', 'dataErrors' => $this->User->validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}
		
		$response = array(
			'status' => 'success',
			'operation' => 'save_profile',
			'data' => array('msg' => 'Changes has been saved successfully', 'data' => $this->data));
		$this->set(array('response' => parseParams($response), '_serialize' => array('response')));
	}
	
	/**
	 ** @purpose change user password
	 ** @input auth_type, auth_key, old_password, new_password, conf_password
	 ** @output
	 */
	public function change_password() {
		$auth_type = $this->getParams('auth_type', 'web');
		$auth_key = $this->getParams('auth_key');
		$old_password = $this->getParams('old_password');
		$new_password = $this->getParams('new_password');
		$conf_password = $this->getParams('conf_password');
		
		$validationErrors = array();
		if(empty($auth_key))
			$validationErrors['auth_key'] = 'Auth key is blank';
		else {
			$user_id = $this->User->get_user_id($auth_key);
			if(empty($user_id))
				$validationErrors['auth_key'] = 'Invalid auth key';
		}
		if(empty($old_password))
			$validationErrors['old_password'] = 'Old password is required';
		if(strlen($new_password) < 6)
			$validationErrors['new_password'] = 'New passwords must be at least 6 characters long';
		else if(!empty($new_password) && $new_password != $conf_password)
			$validationErrors['new_password'] = 'New password and confirm password does not match';
		
		if(empty($validationErrors)) {
			// Check if the logged in user password and the entered user password is same
			$password = $this->User->get_encrypted_password($old_password);
			$options = array('conditions' => array('User.id' => $user_id, 'User.password' => $password));
			$userData = $this->User->find('first', $options);
			if(empty($userData))
				$validationErrors['old_password'] = 'Invalid old password';
		}
		
		if(!empty($validationErrors)) {
			$response = array('status' => 'error', 'operation' => 'change_password', 'validationErrors' => $validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}
		
		
		$password = $this->User->get_encrypted_password($new_password);
		$input_user = array(
			'id' => $user_id,
			'password' => $password,
			);
		if(!$this->User->save($input_user)) {
			$response = array('status' => 'error', 'operation' => 'change_password', 'dataErrors' => $this->User->validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}
		
		$response = array(
			'status' => 'success',
			'operation' => 'change_password',
			'data' => array('msg' => 'Password has been changed successfully', 'data' => $this->data));
		$this->set(array('response' => parseParams($response), '_serialize' => array('response')));
	}
	
	/**
	 ** @purpose reset password request
	 ** @input auth_type, email
	 ** @output
	 */
	public function reset_password_request() {
		$auth_type = $this->getParams('auth_type');
		$email = $this->getParams('email');
		
		$validationErrors = array();
		if(empty($email))
			$validationErrors['email'] = 'Email is not provided';
		else {
			$userExists = $this->User->findByemail($email);
			//changes by @ 062
			$key = $userExists['User']['activation_key'];
			// changes end
			if(empty($userExists))
				$validationErrors['email'] = 'User does not exists';
		}
		
		if(!empty($validationErrors)) {
			$response = array('status' => 'error', 'operation' => 'reset_password_request', 'validationErrors' => $validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}
		
		
		$this->MailTemplate = ClassRegistry::init('MailTemplate');
		
		// Get the mail template information
		$template_name = 'PASSWORD_RESET_REQUEST';
		$options = array('conditions' => array('slug' => $template_name));
		$mail_data = $this->MailTemplate->find('first', $options);
		if(empty($mail_data)) {
			$response = array('status' => 'error', 'operation' => 'reset_password_request', 'data' => array('msg' => 'Email template is not defined'));
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}
		
		$reset_link = SITE_URL."reset_password?activation_key=$key";
		
		$headers = "From: Shoutout Admin <admin@shoutoutcity.com>\r\n";
		$headers .= "MIME-Version: 1.0\r\n";
		$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
		$subject = $mail_data['MailTemplate']['subject'];
		$message = nl2br($mail_data['MailTemplate']['body']);

		$search = array('{%username%}', '{%reset_link%}', '{%signature%}');
		$replace = array($userExists['User']['username'], $reset_link, $signature = 'Shoutout Support');
		$message = str_replace($search, $replace, $message);
		
		if(!mail($to = $email, $subject, $message, $headers)) {
			$response = array('status' => 'error', 'operation' => 'reset_password_request', 'data' => array('msg' => 'Email could not be sent. Please try again later.'));
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}
		
		$response = array(
			'status' => 'success',
			'operation' => 'reset_password_request',
			'data' => array('msg' => 'Reset password confirmation mail has been sent. Please check email inbox.', 'data' => $this->data));
		$this->set(array('response' => parseParams($response), '_serialize' => array('response')));
	}
	
	
	/**
	 ** @purpose reset password request
	 ** @input auth_type, auth_key, message
	 ** @output
	 */
	public function send_feedback() {
		$auth_type = $this->getParams('auth_type');
		$auth_key = $this->getParams('auth_key');
		$feedback_message = $this->getParams('message');
		
		$validationErrors = array();
		if(empty($auth_key))
			$validationErrors['auth_key'] = 'Auth key is blank';
		else {
			$user_id = $this->User->get_user_id($auth_key);
			if(empty($user_id))
				$validationErrors['auth_key'] = 'Invalid auth key';
		}
		if(empty($feedback_message))
			$validationErrors['message'] = 'Feedback message is blank';
		
		if(!empty($validationErrors)) {
			$response = array('status' => 'error', 'operation' => 'send_feedback', 'validationErrors' => $validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}
		
		$userData = $this->User->findByid($user_id);
		
		
		$this->MailTemplate = ClassRegistry::init('MailTemplate');
		
		// Get the mail template information
		$template_name = 'SEND_FEEDBACK';
		$options = array('conditions' => array('slug' => $template_name));
		$mail_data = $this->MailTemplate->find('first', $options);
		if(empty($mail_data)) {
			$response = array('status' => 'error', 'operation' => 'send_feedback', 'data' => array('msg' => 'Email template is not defined'));
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}
		
		
		$from_name = $userData['User']['username'];
		$from_email = $userData['User']['email'];	
		$headers = "From: $from_name <".$from_email.">\r\n";
		$headers .= "MIME-Version: 1.0\r\n";
		$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
		$subject = $mail_data['MailTemplate']['subject'];
		$message = nl2br($mail_data['MailTemplate']['body']);


		// Get the list of all shoutout admin users
		$options = array('recursive' => '-1', 'conditions' => array('role_id' => '1'));
		$adminUsers = $this->User->find('all', $options);
		
		if(!empty($adminUsers)) {
			foreach($adminUsers as $user) {
				
				$search = array('{%username%}', '{%message%}', '{%signature%}');
				$replace = array($userData['User']['username'], $feedback_message, $signature = 'Shoutout Support');
				$message = str_replace($search, $replace, $message);
				
				if(!mail($to = $user['User']['email'], $subject, $message, $headers)) {
					$response = array('status' => 'error', 'operation' => 'send_feedback', 'data' => array('msg' => 'Email could not be sent. Please try again later.'));
					$this->set(array('response' => $response, '_serialize' => array('response')));
					return;
				}
			}
			$userData = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
			//print_r($userData); exit;
			
			if(isset($userData['User']) && !empty($userData['User'])) {
				$name = $userData['User']['username'];
				$email = $userData['User']['email']; 
				$to = array($email => $name);
				$from = array(SITE_EMAIL => SITE_TITLE);
				$subject = 'Shoutoutcity - Thank You for your valuable feedback';
				$template = 'feedback';
				$variables = array('name' => $name);
				if(!$this->User->send_mail($from, $to, $subject, $template, $variables)) {
					$response = array('status' => 'error', 'operation' => 'apply_now', 'validationErrors' => $this->User->validationErrors);
					$this->set(array('response' => $response, '_serialize' => array('response')));
					return;
				}
			}
		}
		
		$response = array(
			'status' => 'success',
			'operation' => 'send_feedback',
			'data' => array('msg' => 'Thanks for your feedback.', 'data' => $this->data));
		$this->set(array('response' => parseParams($response), '_serialize' => array('response')));
	}
	
	
	
	
	
	// Build auth key for the logged in user (to make authenticated web service calls)
	public function build_auth_key($username, $auth_type = 'web') {
		$key = $auth_type."KEY";
		$ip  = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
		//$key = $username.','.$key.','.$ip.','.date('YMDHIS');
		$key = $username.','.$key.','.$ip.','.microtime();
		$key = sha1($key);
		return $key;
	}
	
	// Set the auth key
	public function set_auth_key($data) {
		$this->UserAuth = ClassRegistry::init('UserAuth');
		$input = array(
			'key'	=> $data['key'],
			'params'	=> serialize($data),
			'created_at' => date('Y-m-d H:i:s'),
			);
		$this->UserAuth->create();
		$this->UserAuth->save($input);
		return;
	}
	
	// Authenticate user via auth key
	public function auth_user() {
		$key = $this->getParams('key');
		
		$this->UserAuth = ClassRegistry::init('UserAuth');
		$options = array('conditions' => array('UserAuth.key' => $key), 'order' => array('id' => 'DESC'));
		$data = $this->UserAuth->find('first', $options);
		$data = isset($data['UserAuth']['params']) ? unserialize($data['UserAuth']['params']) : array();
			
		$response = array(
				'status' => 'success',
				'operation' => 'auth_user',
				'data' => $data,
				);
		$this->set(array(
            'response' => parseParams($response),
            '_serialize' => array('response'),
        ));
	}


	/**
	 ** @purpose list of messages for mobile users
	 ** @input auth_type, auth_key, message_id, sort, direction, page, limit
	 ** 
	 **/
	//@ 10/09/2014 by 037 
	public function get_message_list() {
		$auth_type = $this->getParams('auth_type', 'web');
		$auth_key = $this->getParams('auth_key');
		$message_id = $this->getParams('message_id');
		
		$page = $this->getParams('page', 1);
		$limit = $this->getParams('limit', 10);
		$sort = $this->getParams('sort', 'VenueMessageQueue.log_date');
		$direction = $this->getParams('direction', 'DESC');
		
		$validationErrors = array();
		if(empty($auth_key))
			$validationErrors['auth_key'] = "Auth Key is not selected";
		else {
			$user_id = $this->User->get_user_id($auth_key);
			if(empty($user_id))
				$validationErrors['auth_key'] = "Invalid Auth Key";
		}
		
		if(!empty($validationErrors)) {
			$response = array('status' => 'error', 'operation' => 'get_message_list', 'validationErrors' => $validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}
		
		$joins = array();
		$options = array('recursive' => '-1');
		$conditions = array('OR' => array('VenueMessageQueue.message_status' => 1, 'VenueMessageQueue.push_noti_image_status' => 1),
		'AND' => array('VenueMessageQueue.message_status != ' => 3)
		
		);	
		//$conditions = array('VenueMessageQueue.message_status' => 1);
		
		$joins = array(
						array('table' => 'venues',
							'alias' => 'Venue',
							'type' => 'LEFT',
							'conditions' => array(
								'Venue.id = VenueMessageQueue.venue_id',
							)
						),
						array('table' => 'venue_admin_messages',
							'alias' => 'VenueAdminMessage',
							'type' => 'LEFT',
							'conditions' => array(
								'VenueAdminMessage.id = VenueMessageQueue.message_id',
							)
						),
					);
		
		$userExists = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
		
		
		$email = $userExists['User']['email'];
		## Conditions
		if(!empty($email)) 
			$conditions['VenueMessageQueue.email'] = $email;
		
		if(!empty($message_id))
			$conditions['VenueMessageQueue.message_id'] = $message_id;
		##
			
		if(!empty($conditions))
			$options['conditions'] = $conditions;
		if(!empty($joins))
			$options['joins'] = $joins;
			
		$this->VenueMessageQueue = ClassRegistry::init('VenueMessageQueue');
		$total_msg = $this->VenueMessageQueue->find('count', $options);
		
		// Get pagination related information
		$paginate_cond = array(
			'total_records'	=> $total_msg,
			'page_num'	=> $page,
			'per_page'	=> $limit,
			);
		$pagination = get_pagination_stats($paginate_cond);
		
		
		// Get the list of messages
		$options = array(
			'recursive' => '-1',
			'page'	=> $page,
			'limit'	=> $limit,
			'order'	=> array($sort => $direction),
			'fields' => array('VenueMessageQueue.id','VenueMessageQueue.email','VenueMessageQueue.log_date','Venue.logo','Venue.name', 'VenueAdminMessage.message', ''),
			);
		if(!empty($conditions))
			$options['conditions'] = $conditions;
		
		if(!empty($joins))
			$options['joins'] = $joins;
			
		$results = $this->VenueMessageQueue->find('all', $options);	
		
		$data = array();
		foreach ($results as $res) {
			if($res['Venue']['logo'] == NULL || $res['Venue']['logo'] == null || $res['Venue']['logo'] == "")
				$logo_val = "";
			else {						
				$logo_val 	= Router::url('/', true).$res['Venue']['logo'];
				if(!file_exists(WWW_ROOT.$res['Venue']['logo'])){
					$logo_val 	= Router::url('/', true)."img/default/NAthumb.png";
				}		
			}	
			
			$data[] = array(
				'id' => $res['VenueMessageQueue']['id'],
				'email' => $res['VenueMessageQueue']['email'],
				'short_message' => substr($res['VenueAdminMessage']['message'],0,100),
				'full_message' => $res['VenueAdminMessage']['message'],
				'venue_name' => $res['Venue']['name'],
				'venue_logo' => $logo_val,
				'log_date' => humanTiming(strtotime($res['VenueMessageQueue']['log_date'])).' ago',
			);
		}
		
		$response = array(
			'status' => 'success',
			'operation' => 'get_message_list',
			'pagination' => $pagination,
			'message' => $data,
			);
		$this->set(array('response' => parseParams($response), '_serialize' => array('response')));
		
	}


	/**
	 ** @purpose Delete Messages for mobile users 
	 ** @input auth_type, auth_key, message_id
	 **  
	 **/
	//@10/09/2014 by 037,  
	public function delete_message() {
		$auth_type = $this->getParams('auth_type', 'web');
		$auth_key = $this->getParams('auth_key');
		$message_id = $this->getParams('message_id');
		
		$this->VenueMessageQueue = ClassRegistry::init('VenueMessageQueue');
		
		$validationErrors = array();
		
		if(empty($auth_key))
			$validationErrors['auth_key'] = "Auth Key is not selected";
		else {
			$user_id = $this->User->get_user_id($auth_key);
			if(empty($user_id))
				$validationErrors['auth_key'] = "Invalid Auth Key.";
		}
		if(empty($message_id))
			$validationErrors['message_id'] = "Message ID is not selected";
		else{
			$messageExists = $this->VenueMessageQueue->find('first', array('conditions' => array('VenueMessageQueue.id' => $message_id)));
			if(empty($messageExists))
				$validationErrors['message_id'] = "Message ID does not exist";
		}
		
		if(!empty($validationErrors)) {
			$response = array('status' => 'error', 'operation'=> 'delete_message', 'validationErrors' => $validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}
		
		if(!$this->VenueMessageQueue->delete($message_id)){
			$response = array('status' => 'error', 'operation' => 'delete_message', 'validationErrors' => $this->VenueMessageQueue->validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}
		
		$response = array(
			'status' => 'success',
			'operation' => 'delete_message',
			'data' => array('msg' => 'Message Successfully Deleted', 'data' => $this->data),
		);
		
		$this->set(array('response' => $response, '_serialize' => array('response')));
	}
	
	
	/**
	 ** @purpose
	 ** @input auth_type, auth_key, content, city_id
	 **	@output
	 */
	//@22/096/2014 by 037, Send Message to App Registered Users from Shoutoutadmin
	
	public function send_message() {
		
		$auth_type = $this->getParams('auth_type', 'web');
		$auth_key = $this->getParams('auth_key');
		$city_id = $this->getParams('city_id');
		$content = $this->getParams('content');
				
		
		//Validations
		$validationErrors = array();
		
		if($auth_type != 'web')
			$validationErrors['auth_type'] = 'Invalid auth type';
		if(empty($auth_key))
			$validationErrors['auth_key'] = 'Auth Key is not selected';
		else {
			$user_id = $this->User->get_user_id($auth_key);
			if(empty($user_id))
				$validationErrors['auth_key'] = 'Invalid Auth Key';
		}
		
		if(empty($city_id))
			$validationErrors['city_id'] = 'City ID is not seleted';
		else {
			$cityExists = $this->City->find('first', array('conditions' => array('City.id' => $city_id)));
			//print_r($cityExists); exit;
			if(empty($cityExists) || !isset($cityExists['City']))
				$validationErrors['city_id'] = 'City does not exist';
		}	
			
		if(empty($content))
			$validationErrors['content'] = 'Message Contents are Empty';
			
		if(!empty($validationErrors)) {
			$response = array('status' => 'error', 'operation' => 'send_message', 'validationErrors' => $validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}

		$status = array();

		$status = $cityExists;	
		if(isset($cityExists['User'])) {
			foreach($cityExists['User'] as $user) {
				if($user['role_id'] == 2) {
					$name = $user['username'];
					$email = $user['email']; 
					$to = array($email => $name);
					$from = array(SITE_EMAIL => SITE_TITLE);
					
				
					$subject = 'Shoutoutcity - From Admin';
					$template = 'admin_msg';
					$variables = array('name' => $name, 'content' => $content);
					if(!$this->User->send_mail($from, $to, $subject, $template, $variables)) {
						$status['error'][] = 'Unable to send Message to '.$email;
					} else
						$status['success'][] = 'Successfully sent Message to '.$email;
				}	//If User is registered
			}	//Foreach on User
		}	//If User Exists
		/*	
		if(!empty($validationErrors)) {
			$response = array('status' => 'error', 'operation' => 'send_message', 'validationErrors' => $validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}
		*/
		$response = array(
			'status' => 'success',
			'operation' => '',
			'data' => array('status' => $status)
		);
		$this->set(array('response' => $response, '_serialize' => array('response')));
	}
	
	/**
	 ** @purpose Get Registered User data depending upon City 
	 ** @i/p auth_type, auth_key, city_id
	 ** @output Users Array
	 **/
	
	public function get_users_data() {
		$auth_type = $this->getParams('auth_type', 'web');
		$auth_key = $this->getParams('auth_key');
		$city_id = $this->getParams('city_id');
		
		$validationErrors = array();
		
		if($auth_type != 'web')
			$validationErrors['auth_type'] = 'Invalid auth type';
		if(empty($auth_key))
			$validationErrors['auth_key'] = 'Auth Key is not selected';
		else {
			$user_id = $this->User->get_user_id($auth_key);
			if(empty($user_id))
				$validationErrors['auth_key'] = 'Invalid Auth Key';
		}
			
		if(!empty($city_id)) {
			$cityExists = $this->City->find('first', array('conditions' => array('City.id' => $city_id)));
			//print_r($cityExists); exit;
			if(empty($cityExists) || !isset($cityExists['City']))
				$validationErrors['city_id'] = 'City does not exist';
		}
		
		if(!empty($validationErrors)) {
			$response = array('status' => 'error', 'operation' => 'send_message', 'validationErrors' => $validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}
		
		
		$options = array(
			'recursive' => '-1',
			'fields' => array('User.name','User.email','User.address','User.dob','User.gender', 'City.name', 'City.state', )
		);
		
		$joins = array(
						array(
						'table' => 'cities',
						'alias' => 'City',
						'conditions' => 'User.city_id = City.id',
						)
					);
		
		$conditions = array('User.role_id' => 2);
		
		if(!empty($city_id))
			$conditions['User.city_id'] = $city_id;
			
		$options['conditions'] = $conditions;
		$options['joins'] = $joins;	
		
		$data = $this->User->find('all', $options);
		
		$users = array();
		$i = 1;
		if(!empty($data)) {
			foreach($data as $d) {
				$users[] = array(
					'Sr.No.' => $i,
					'Name' => $d['User']['name'],
					'Gender' => $d['User']['gender'],
					'DOB' => $d['User']['dob'],
					'Email' => $d['User']['email'],
					'Address' => $d['User']['address'],
					'City' => $d['City']['name'],
					'State' => $d['City']['state'],
				);
				$i++;
			}
		}
		
		//print_r($users); exit;
		
		$response = array(
			'status' => 'success',
			'operation' => 'get_users_data',
			'data' => $users
		);
		$this->set(array('response' => $response, '_serialize' => array('response')));	
		
	}
	
		/*
	 ** @url		users/check_availability.json
	 ** @purpose	To Check whether email is already registered or not
	 ** @input 		auth_type, auth_key, email, username
	 ** @output		Array of Users data if email exist or if username exists
	 ** 
	 */
	
	public function check_availability() {
		 
		$auth_type = $this->getParams('auth_type');
		$auth_key = $this->getParams('auth_key');
		$email = $this->getParams('email');
		$username = $this->getParams('username');
		
		//Validations
		$validationErrors = array();
		
		if(empty($auth_type))
			$validationErrors['auth_type'] = 'Invalid auth type';
		if(empty($auth_key))
			$validationErrors['auth_key'] = 'Auth Key is not selected';
		else {
			$user_id = $this->User->get_user_id($auth_key);
			if(empty($user_id))
				$validationErrors['auth_key'] = 'Invalid Auth Key';
		}
		
		if(empty($email) && empty($username))
			$validationErrors['email'] = 'Email ID or username is required';
		/*if(empty($email))
			$validationErrors['email'] = 'Email ID is not entered';
		if(empty($username))
			$validationErrors['username'] = 'Username is not entered';	*/
		
		if(!empty($validationErrors)) {
			$response = array('status' => 'error', 'operation' => 'check_availability', 'validationErrors' => $validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}
		
		if(!empty($email))
			$userExists = $this->User->find('first', array('conditions' => array('User.email' => $email)));
		else if(!empty($username))
			$userExists = $this->User->find('first', array('conditions' => array('User.username' => $username)));		
		
		$data = array();
		$userData = array();
		if(!empty($userExists) && isset($userExists['User'])) {
			$userData = $userExists['User'];
			if(!empty($email))
				$data = array('msg' => 'Email Found', 'userData' => $userData);
			else if(!empty($username))
				$data = array('msg' => 'Username Found', 'userData' => $userData);
		}
		else{
			if(!empty($email))
				$data = array('msg' => 'Email not Found', 'userData' => $userData);
			else if(!empty($username))	
				$data = array('msg' => 'Username not Found', 'userData' => $userData);
		}	
		$response = array(
			'status' => 'success',
			'operation' => 'check_availability',
			'data' => $data,
		);
		
		$this->set(array('response' => $response, '_serialize' => array('response')));
	}
	
	/*
	 ** @url		users/check_activation.json
	 ** @purpose	To Check whether activation key is present/valid or not for reset password
	 ** @input 		auth_type, activation_key 
	 ** @output		if 1) Success  => Reset Page
	 **					2)Failure => Inavlid Activation Key
	 ** 
	 */
	
	public function check_activation() {
		$auth_type = $this->getParams('auth_type', 'web');
		$activationKey = $this->getParams('activationKey');
		
		
		//Validations
		$validationErrors = array();
		if($auth_type != 'web')
			$validationErrors['auth_type'] = 'Invalid auth type';
		
		if(empty($activationKey) )
			$validationErrors['activationKey'] = 'activation key required';
		else {
			$userExists = $this->User->findByActivationKey($activationKey);
			
			
			if(empty($userExists))
				$validationErrors['activationKey'] = 'Invalid Activation Key';
		}	
		if(!empty($validationErrors)) {
			$response = array('status' => 'error', 'operation' => 'check_activation', 'validationErrors' => $validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}
		$userData = isset($userExists['User']) ? $userExists['User'] : array();
		$response = array(
			'status' => 'success',
			'operation' => 'check_activation',
			'data' => array('msg' => 'activation Key Found', 'userData' => $userData),
		);

		$this->set(array('response' => $response, '_serialize' => array('response')));
	}
	
	/*
	 ** @url		users/update_password.json
	 ** @purpose	To reset password
	 ** @input 		auth_type, id , password 
	 ** @output		Password Updated successfully
	 */
	
	public function update_password() {
		$auth_type = $this->getParams('auth_type', 'web');
		$id =  $this->getParams('id');
		$password = $this->getParams('password');
		$confirm_password = $this->getParams('confirm_password');
	
		
		//Validations
		$validationErrors = array();
		
		if($auth_type != 'web')
			$validationErrors['auth_type'] = 'Invalid auth type';
			
		if(empty($id))
			$validationErrors['id'] = 'id  is required';
			else {
			$userExists = $this->User->findById($id);
			if(empty($userExists))
				$validationErrors['id'] = 'User does not exists';
			}
		
		if(empty($password))
			$validationErrors['password'] = 'Password  is required';
			
		if(strlen($password) < 6)
				$validationErrors['password'] = 'Passwords must be at least 6 characters long';
		if(empty($confirm_password))
			$validationErrors['confirm_password'] = 'Confirm password  is required';
		 
		if($password != $confirm_password)
			$validationErrors['password'] = 'Password and confirm password does not match';

		
		if(!empty($validationErrors)) {
			$response = array('status' => 'error', 'operation' => 'update_password', 'validationErrors' => $validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}
		
		$password = $this->User->get_encrypted_password($password);
		$input = array(
			'id' => $id,
			'password' => $password,
		);
		
	
		if(!$this->User->save($input)) {
			$response = array('status' => 'error', 'operation' => 'update_password', 'msg' => 'error in changing password', 'validationErrors' => $this->validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}
		
		$response = array(
			'status' => 'success',
			'operation' => 'update_password',
			'data' => array('msg' => 'Password has been changed successfully, Login Now', 'data' => $this->data));
	    $this->set(array('response' => $response, '_serialize' => array('response')));
		
	}

 
   public function get_cities_new() {		
		
		$get_list = $this->getParams('get_list', 'y'); //isset($this->data['get_list']) ? $this->data['get_list'] : "y";
		$city_id = $this->getParams('city_id');
		$search = $this->getParams('search');
		$city_name = $this->getParams('city_name');
		$state = $this->getParams('state');
		
		$sort = $this->getParams('sort', 'City.name');
		$direction = $this->getParams('direction', 'ASC');
		
		$conditions = array();
		
		if(!empty($city_id))
			$conditions['City.id'] = $city_id;
		
		if(!empty($search)) {
			$conditions['OR'] = array(
				'City.name LIKE' => '%'.$search.'%',
				'City.state LIKE' => '%'.$search.'%', 
			);
		}
			
		if(!empty($state)) 
			$conditions['OR'] = array(
				'City.state LIKE' => '%'.$state.'%',
				'City.full_state LIKE' => '%'.$state.'%'
			);
		
			
		if(!empty($city_name))
			$conditions['City.name LIKE'] = '%'.$city_name.'%';
			
		$options = array(
			'order'	=> array($sort => $direction),
		);
	//	if($get_list == 'y') {
			$options['fields'] = array('id', 'name', 'state');
			$options['recursive'] = '-1';
			$options['conditions'] = $conditions;
	//	}
		
		$cities = $this->City->find('all', $options);
		
		if($get_list == 'y' && !empty($cities)) {
			$result = array();
			foreach($cities as $crow) {
				$city_name = $crow['City']['name'];
				if(!empty($crow['City']['state'])) {
					$city_name .= ', ' . $crow['City']['state'];
				}
				$result[$crow['City']['id']] = $city_name;
			}
			$cities = $result;
		}
		return $cities;
		$response = array(
			'status' => 'success',
			'operation' => 'get_cities',
			'cities' => $cities,
			);
		
		$this->set(array(
            'response' => $response,
            '_serialize' => array('response'),
        ));
	}	 
	 
}
