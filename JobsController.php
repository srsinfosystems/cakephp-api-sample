<?php
App::uses('AppController', 'Controller');
App::uses('CakeEmail', 'Network/Email');

class JobsController extends AppController {
	public $components = array('RequestHandler');
	public $uses = array('Job', 'User', 'Venue', 'VenueJob');

	function beforeFilter() {
        parent::beforeFilter();

        $this->Auth->allow('add');
        $this->Auth->allow('update');
        $this->Auth->allow('delete');
        $this->Auth->allow('get_list');
        $this->Auth->allow('get_detail');
        $this->Auth->allow('apply_now');
	}

	public function getParams($field, $default_value = '') {
		if(!is_array($default_value))
			$value = isset($this->data[$field]) ? trim($this->data[$field]) : $default_value;
		else
			$value = isset($this->data[$field]) ? $this->data[$field] : $default_value;
		return $value;
	}


	/**
	 ** @purpose add job
	 ** @input auth_type, venue_id, job_title, description, location, address, phone
	 ** @output
	 */
	public function add() {
		$venue_id = $this->getParams('venue_id');
		$job_title = $this->getParams('job_title');
		$description = $this->getParams('description');
		$location = $this->getParams('location');
		$address = $this->getParams('address');
		$phone = $this->getParams('phone');

		$today = date('Y-m-d H:i:s');


		$input = array(
			'job_title'	=> $job_title,
			'description' => $description,
			'location'	=> $location,
			'address'	=> $address,
			'phone'		=> $phone,
			'created'	=> $today,
			'updated'	=> $today,
			);
		if(!empty($venue_id))
			$input['venue_id'] = $venue_id;

		$this->Job->set($input);
		if(!$this->Job->validates()) {
			$response = array('status' => 'error', 'operation' => 'add', 'validationErrors' => $this->Job->validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}


		if(!$this->Job->save($input)) {
			$response = array('status' => 'error', 'operation' => 'add', 'dataErrors' => $this->Job->validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}

		$job_id = $this->Job->getLastInsertId();

		$response = array(
			'status' => 'success',
			'operation' => 'add',
			'data' => array('msg' => 'Job has been created successfully', 'job_id' => $job_id),
			);
		$this->set(array('response' => $response, '_serialize' => array('response')));
	}


	/**
	 ** @purpose update job
	 ** @input auth_type, job_id, venue_id, job_title, description, location, address, phone
	 ** @output
	 */
	public function update() {
		$job_id = $this->getParams('job_id');
		$venue_id = $this->getParams('venue_id');
		$job_title = $this->getParams('job_title');
		$description = $this->getParams('description');
		$location = $this->getParams('location');
		$address = $this->getParams('address');
		$phone = $this->getParams('phone');

		$today = date('Y-m-d H:i:s');

		// Check if the job is available or not
		$validationErrors = array();
		$jobExists = $this->Job->findByid($job_id);
		if(empty($jobExists))
			$validationErrors['job_id'] = 'Job does not exists';

		if(!empty($validationErrors)) {
			$response = array('status' => 'error', 'operation' => 'udpate', 'validationErrors' => $validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}


		$input = array(
			'id'	=> $job_id,
			'job_title'	=> $job_title,
			'description' => $description,
			'location'	=> $location,
			'address'	=> $address,
			'phone'		=> $phone,
			'updated'	=> $today,
			);
		if(!empty($venue_id))
			$input['venue_id'] = $venue_id;

		$this->Job->set($input);
		if(!$this->Job->validates()) {
			$response = array('status' => 'error', 'operation' => 'udpate', 'validationErrors' => $this->Job->validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}


		if(!$this->Job->save($input)) {
			$response = array('status' => 'error', 'operation' => 'udpate', 'dataErrors' => $this->Job->validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}

		$response = array(
			'status' => 'success',
			'operation' => 'udpate',
			'data' => array('msg' => 'Job has been updated successfully', 'data' => $this->data),
			);
		$this->set(array('response' => $response, '_serialize' => array('response')));
	}




	/**
	 ** @purpose delete job
	 ** @input auth_type, job_id
	 ** @output
	 */
	public function delete() {
		$job_id = $this->getParams('job_id');

		$validationErrors = array();
		if(empty($job_id))
			$validationErrors['job_id'] = 'Job is not selected';

		if(empty($validationErrors)) {
			// Check if the job is available or not
			$jobExists = $this->Job->findByid($job_id);
			if(empty($jobExists))
				$validationErrors['job_id'] = 'Job does not exists';
		}

		if(!empty($validationErrors)) {
			$response = array('status' => 'error', 'operation' => 'delete', 'validationErrors' => $validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}


		if(!$this->Job->delete($job_id)) {
			$response = array('status' => 'error', 'operation' => 'delete', 'dataErrors' => $this->Job->validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}

		$response = array(
			'status' => 'success',
			'operation' => 'delete',
			'data' => array('msg' => 'Job has been deleted successfully', 'data' => $this->data),
			);
		$this->set(array('response' => $response, '_serialize' => array('response')));
	}


	/**
	 ** @purpose get the list of jobs
	 ** @input auth_type, keyword, sort, direction, page, limit, city_name, state
	 ** @output Array list of jobs
	 */
	public function get_list() {
		$auth_type = $this->getParams('auth_type');
		$keyword = $this->getParams('keyword');

		$sort = $this->getParams('sort', 'Job.updated');
		$direction = $this->getParams('direction', 'DESC');
		$page = $this->getParams('page', 1);
		$limit = $this->getParams('limit', 15);

		$city_name = $this->getParams('city_name');
		$state = $this->getParams('state');

		$options = array('recursive' => '-1','group' => 'Job.id');

		$joins = array(
						array(
							'table' => 'venues',
							'alias' => 'Venue',
							'conditions' => 'Venue.id = Job.venue_id',
							),
						array(
						'table'	=> 'cities',
						'alias'	=> 'City',
						'conditions' => array(
								'Venue.state = City.state',
								'Venue.city = City.name'
							),
						)
					);
		$conditions = array();

		if(!empty($joins))
			$options['joins'] = $joins;

		if(!empty($state))
			$conditions['OR'] = array(
				'City.state LIKE' => '%'.$state.'%',
				'City.full_state LIKE' => '%'.$state.'%'
			);


		if(!empty($city_name))
			$conditions['City.name LIKE'] = '%'.$city_name.'%';


		if(!empty($conditions))
			$options['conditions'] = $conditions;

		$vcount = $this->Job->find('count', $options);

		// If Record not found
		if(empty($vcount)) {
			$options['conditions'] = array();
			$conditions = array();

			if(!empty($state))
				$conditions['OR'] = array(
					'City.state LIKE' => '%'.$state.'%',
					'City.full_state LIKE' => '%'.$state.'%'
				);

			if(!empty($conditions))
			$options['conditions'] = $conditions;

			$vcount2 = $this->Job->find('count', $options);

			// If Record not found
			if(empty($vcount2)) {
				$options['conditions'] = array();
				$conditions = array();

				if(!empty($city_name))
					$conditions['City.name LIKE'] = '%'.$city_name.'%';

				if(!empty($conditions))
					$options['conditions'] = $conditions;

				$vcount3 = $this->Job->find('count', $options);

				if(empty($vcount3)) {
					$options['conditions'] = array();
					$conditions = array();
				}
			}
		}


		if(!empty($keyword)) {
			$conditions['OR'] = array(
				'Job.job_title LIKE ' => "%".$keyword."%",
				'Job.location LIKE ' => "%".$keyword."%",
				'Job.address LIKE ' => "%".$keyword."%",
				'Job.phone LIKE ' => "%".$keyword."%",
				'Venue.city LIKE ' => "%".$keyword."%",
				'Venue.zip LIKE ' => "%".$keyword."%",
				);
		}


		if(!empty($state))
			$conditions['OR'] = array(
				'City.state LIKE' => '%'.$state.'%',
				'City.full_state LIKE' => '%'.$state.'%'
			);

		$options['joins'] = $joins;

		$total_jobs = $this->Job->find('count', $options);


		// Get pagination related information
		$paginate_cond = array(
			'total_records'	=> $total_jobs,
			'page_num'	=> $page,
			'per_page'	=> $limit,
			);
		$pagination = get_pagination_stats($paginate_cond);


		// Get the list of jobs
		$options = array(
			'recursive' => '-1',
			'group' => 'Job.id',
			'page'	=> $page,
			'limit'	=> $limit,
			'order'	=> array($sort => $direction),
			'fields' => array('Job.id', 'Job.venue_id', 'Job.job_title', 'Job.location', 'Job.phone', 'Job.address', 'Venue.id', 'Venue.name', 'Venue.address', 'Venue.state', 'Venue.city', 'Venue.zip', 'Venue.phone1 AS phone', 'Venue.phone2 AS mobile'),
			);
		if(!empty($conditions))
			$options['conditions'] = $conditions;

		$options['joins'] = $joins;

		$result = $this->Job->find('all', $options);
		//print_r($result); exit;

		$jobs = array();
		if(!empty($result)) {
			foreach($result as $row) {
				$job = $row['Job'];
				$job['venue'] = $row['Venue'];
				$jobs[] = $job;
			}
		}


		$response = array(
			'status' => 'success',
			'operation' => 'get_list',
			'pagination' => $pagination,
			'jobs' => $jobs,
			);
		$this->set(array( 'response' => parseParams($response), '_serialize' => array('response')));
	}


	/**
	 ** @purpose get the job detail
	 ** @input auth_type, job_id
	 ** @output
	 */
	public function get_detail() {
		$job_id = $this->getParams('job_id');

		$validationErrors = array();
		if(empty($job_id))
			$validationErrors['job_id'] = 'Job is not selected';

		if(empty($validationErrors)) {
			// Check if the job is available or not
			$jobExists = $this->Job->findByid($job_id);
			if(empty($jobExists))
				$validationErrors['job_id'] = 'Job does not exists';
		}

		if(!empty($validationErrors)) {
			$response = array('status' => 'error', 'operation' => 'get_detail', 'validationErrors' => $validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}

		$job = $jobExists['Job'];
		$job['venue'] = $jobExists['Venue'];
		$job['venue']['phone'] = $jobExists['Venue']['phone1'];
		$job['venue']['mobile'] = $jobExists['Venue']['phone2'];

		if($jobExists['Venue']['logo'] == NULL || $jobExists['Venue']['logo'] == null)
			$job['venue']['logo'] = "";
		else {
			$thumb_imageName 	= Router::url('/', true).$jobExists['Venue']['logo'];
			if(!file_exists(WWW_ROOT.$jobExists['Venue']['logo'])){
				$thumb_imageName 	= Router::url('/', true)."img/default/NAthumb.png";
			}
			$job['venue']['logo'] = $thumb_imageName;
		}


		$response = array('status' => 'success', 'operation' => 'get_detail', 'data' => $job);
		$this->set(array('response' => parseParams($response), '_serialize' => array('response')));
	}


	#@11/09/2014 by 037, Send Mail Function
	public function send_mail($from, $to, $subject, $template) {

		$email = new CakeEmail();

		$email->template($template)
			->from($from)
			->to($to)
			->subject($subject)
			->emailFormat('html');
		$email_status = $email->send();

		return $email_status;
	}

	/**
	 ** @purpose Apply for Job
	 ** @input auth_type, auth_key, venue_id, content
	 ** @output
	 **/
	// @09/09/2014 by 037,
	public function apply_now() {
		$auth_type = $this->getParams('auth_type', 'web');
		$auth_key = $this->getParams('auth_key');
		$venue_id = $this->getParams('venue_id');
		$content = $this->getParams('content');

		$today = date('Y-m-d H:i:s');

		$validationErrors = array();

		##
		if(empty($auth_key))
			$validationErrors['auth_key'] = "Auth Key is not selected";
		else {
			$user_id = $this->User->get_user_id($auth_key);
			if(empty($user_id))
				$validationErrors['auth_key'] = "Invalid Auth Key";
		}
		if(empty($venue_id))
			$validationErrors['venue_id'] = "Venue ID is not selected";
		else {
			$venueExists = $this->Venue->findByid($venue_id);
			if(empty($venueExists) || !isset($venueExists))
				$validationErrors['venue_id'] = "Venue does not exists";
		}
		if(empty($content))
			$validationErrors['content'] = "Contents are Empty";

		if(!empty($validationErrors)) {
			$response = array('status' => 'error', 'operation' => 'apply_now', 'validationErrors' => $validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}
		##
		$input = array(
			'user_id' => $user_id,
			'venue_id' => $venue_id,
			'content' => $content,
			'created' => $today
		);


		if(!$this->VenueJob->save($input, $validate = false)) {
			$response = array('status' => 'error', 'operation' => 'apply_now', 'validationErrors' => $this->VenueJob->validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}
		else{

			// Send Achnowledgement to Applying User
			$userData = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));

			if(isset($userData['User']) && !empty($userData['User'])) {
				$name = $userData['User']['username'];
				$email = $userData['User']['email'];
				$to = array($email => $name);
				$from = array(SITE_EMAIL => SITE_TITLE);
				//$to = 'vishal@srs-infosystems.com';
				$subject = 'Shoutoutcity - Thank You for Applying Job';
				$template = 'job';
				$variables = array('name' => $name);
				if(!$this->User->send_mail($from, $to, $subject, $template, $variables)) {
					$response = array('status' => 'error', 'operation' => 'apply_now', 'validationErrors' => $this->User->validationErrors);
					$this->set(array('response' => $response, '_serialize' => array('response')));
					return;
				}

				//Send Job Profile to Venue Admin
				$options = array('recursive' => '-1', 'conditions' => array('role_id' => '4'));
				$venueAdminUsers = $this->User->find('all', $options);
				//print_r($venueAdminUsers); exit;
				foreach($venueAdminUsers as $venueAdminUser) {
					if(isset($venueAdminUser['User']) && !empty($venueAdminUser['User'])) {
						$venue_admin = $venueAdminUser['User']['name'];
						$venue_admin_email = $venueAdminUser['User']['email'];
						$to = array($venue_admin_email => $venue_admin);
						$from = array(SITE_EMAIL => SITE_TITLE);
						$subject = 'Shoutoutcity - New Job application';
						$template = 'job_profile';
						$variables = array(
							'venue_admin' => $venue_admin,
							'name' => $name,
							'email' => $email,
							'content' => $content,
						);
						if(!$this->User->send_mail($from, $to, $subject, $template, $variables)) {
							$response = array('status' => 'error', 'operation' => 'apply_now', 'validationErrors' => $this->User->validationErrors);
							$this->set(array('response' => $response, '_serialize' => array('response')));
							return;
						}
					}
				}

			}
		}

		$id = $this->VenueJob->getLastInsertId();

		$response = array(
			'status' => 'success',
			'operation' => 'apply_jobs',
			'data' => array('id' => $id),
			);
		$this->set(array('response' => $response, '_serialize' => array('response')));
	}

}
