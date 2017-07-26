<?php
App::uses('AppController', 'Controller');
App::uses('CakeEmail', 'Network/Email');

//use PHPImageWorkshop\ImageWorkshop;
//include_once(WWW_ROOT . 'PHPImageWorkshop/Image.php');

class VenuesController extends AppController {

	public $components = array('Paginator', 'RequestHandler', 'Image');
	public $uses = array('Venue','Category', 'CategoriesVenue', 'VenueAdminMessage', 'VenueReview', 'VenueMessageQueue', 'User','VenuePhoto','VenuePhotoQueue','VenueReview','VenueDaysTime', 'AreasVenue', 'UserFavoriteVenue', 'VenueFloorPlan', 'VenuePromotion', 'Area');

	function beforeFilter() {
        parent::beforeFilter();
        //$this->image = new Custom_Model_Image();
        //$this->Auth->deny('*'); //Disallow access to all actions
        $this->Auth->allow('index');
        $this->Auth->allow('updateVenueBottleValue');
        $this->Auth->allow('updateVenueMessage');
        $this->Auth->allow('getVenueDetail');
        $this->Auth->allow('sendCronEmailForVenue');
        $this->Auth->allow('deleteVenuePhoto');
		$this->Auth->allow('deleteVenuePhotoBeforeUpload');
		$this->Auth->allow('uploadVenuePhotoBeforeUpload');
		$this->Auth->allow('uploadVenuePhotoAfterUpload');
 		$this->Auth->allow('testSingleImageUpload');
 		$this->Auth->allow('check_user_data');



 		// @08/08/2014 by 003
 		$this->Auth->allow('save_venue_profile');
 		$this->Auth->allow('get_venues_list');
 		$this->Auth->allow('get_venue_details');
 		$this->Auth->allow('upload_venue_logo');
 		$this->Auth->allow('upload_photos');
 		$this->Auth->allow('delete_logo');
 		$this->Auth->allow('delete_photos');
 		$this->Auth->allow('delete_venue');

        $this->Auth->allow('save_table_areas');
        $this->Auth->allow('delete_table_area');
        $this->Auth->allow('get_table_areas');

        $this->Auth->allow('add_bottle_service_box');
        $this->Auth->allow('delete_bottle_service_box');
        $this->Auth->allow('save_bottle_service');
        $this->Auth->allow('save_categories_features');
        $this->Auth->allow('upload_bottle_service_logo');

        $this->Auth->allow('mark_favorite');
        $this->Auth->allow('favorite_list');

        $this->Auth->allow('rate_venue');

        // @08/09/2014 by 037
        $this->Auth->allow('delete_bottle_service_logo');
        $this->Auth->allow('get_review');
        $this->Auth->allow('get_venue_rating'); //@12/12/2016 by 079
        $this->Auth->allow('capture_rating');   //@12/12/2016 by 079
        $this->Auth->allow('save_venue_admin_video');   //@23/12/2016 by 079
        $this->Auth->allow('add_new_category');   //@14/12/2016 by 079
        $this->Auth->allow('add_new_subcategory');   //@14/12/2016 by 079
        $this->Auth->allow('delete_venue_video'); //@27/12/2016 by 079
        
         // @11/09/2014 by 044
        $this->Auth->allow('get_venue_photos');
        $this->Auth->allow('save_venue_photos');
        $this->Auth->allow('test_photo_api');
        $this->Auth->allow('set_venue_draft_script'); //@18/09/2014 by 044
        $this->Auth->allow('unlink_venueadmin');
        $this->Auth->allow('add_categoryfeature_other');
        $this->Auth->allow('delete_categoryfeature_other');

        //@29/09/2014 by 037
        $this->Auth->allow('upload_promos');
        $this->Auth->allow('delete_promos');
        $this->Auth->allow('upload_floor_plans');
        $this->Auth->allow('delete_floor_plans');

        $this->Auth->allow('get_venue_floor_plans');
        $this->Auth->allow('get_venue_promos');

        //@07/10/2014 by 037
        $this->Auth->allow('get_bottle_boxes');
        $this->Auth->allow('get_bottle_types');

        $this->Auth->allow('get_mixer_types');
        $this->Auth->allow('add_mixer_service_box');
        $this->Auth->allow('delete_mixer_service_box');
        $this->Auth->allow('get_mixer_boxes');
        $this->Auth->allow('process_cc_payment');
        $this->Auth->allow('place_order');

        //@08/07/2014 by 037,
        $this->Auth->allow('add_limo_service_box');
        $this->Auth->allow('delete_limo_service_box');
        $this->Auth->allow('get_limo_boxes');

        $this->Auth->allow('get_orders_list');
        $this->Auth->allow('get_order_details');

        //@13/10/2014 by 037,
        $this->Auth->allow('update_order_status');
        $this->Auth->allow('update_table_area');
        $this->Auth->allow('get_table_area_details');

        $this->Auth->allow('get_reviews_list');

        //@16/10/2014 by 037,
        $this->Auth->allow('check_table_area_availability');
       //@30/10/2014 by 044,
        $this->Auth->allow('delete_venue_multiple_photos');
        $this->Auth->allow('delete_venue_multiple_promotion_photos');
        $this->Auth->allow('delete_venue_multiple_floorplan_photos');

       //@16/10/2014 by 062,
        $this->Auth->allow('update_pay_status');

	//@16/02/2015 by 044,
	$this->Auth->allow('get_parent_category_alias');

	//@09/03/2015 by 044,		
	$this->Auth->allow('sendCronEmailForPushNotiForImage');	
	$this->Auth->allow('get_only_parent_category_alias');
	
	$this->Auth->allow('send_mail_to_all');
	

 	
        
   }  
   
	# @08/08/2014 by 003, Filter/trim the input parameter or set the default value
	public function getParams($field, $default_value = '') {
		if(!is_array($default_value))
			$value = isset($this->data[$field]) ? trim($this->data[$field]) : $default_value;
		else
			$value = isset($this->data[$field]) ? $this->data[$field] : $default_value;
		return $value;
	}


    /**
     * INPUT : array(
	 *		'category' => '1',
	 *		'sub_category' => '2',
	 *		'features' => array(
	 *			(int) 0 => '11',
	 *			(int) 1 => '12',
	 *			(int) 2 => '15'
	 *		),
	 *		'search' => 'bars',
	 *		'limit' => '10',
	 *		'page' => '1'
	 *	)
  	 *
     *
     */
	public function index() {
		$category  		= isset($this->request->data['category']) ? $this->request->data['category'] : "";
		$sub_category  	= isset($this->request->data['sub_category']) ? $this->request->data['sub_category'] : "";
		$features  		= isset($this->request->data['features']) ? $this->request->data['features'] : array();
		$areas  		= isset($this->request->data['areas']) ? $this->request->data['areas'] : array();
		$search  		= isset($this->request->data['search']) ? $this->request->data['search'] : "";
		$page  			= isset($this->request->data['page']) ? $this->request->data['page'] : 1;
		if(empty($page) or $page == "" or $page == 0 or $page < 0){
			$page = 1;
		}
		$limit = isset($this->request->data['limit']) ? $this->request->data['limit'] : 10;
		if(empty($limit) or $limit == "" or $limit == 0 or $limit < 0){
			$limit = 10;
		}


		$params['joins'] = array(
		    array('table' => 'categories_venues',
		        'alias' => 'CategoriesVenue',
		        'type' => 'LEFT',
		        'conditions' => array(
		            'CategoriesVenue.venue_id = Venue.id',
		        )
		    ),
		    array('table' => 'features_venues',
		        'alias' => 'FeaturesVenue',
		        'type' => 'LEFT',
		        'conditions' => array(
		            'FeaturesVenue.venue_id = Venue.id',
		        )
		    ),
		     array('table' => 'areas_venues',
		        'alias' => 'AreasVenue',
		        'type' => 'LEFT',
		        'conditions' => array(
		            'AreasVenue.venue_id = Venue.id',
		        )
		    )
		);

		$or_cond = array();
		if(!empty($search)) {
			$or_cond = array(
				array('Venue.name LIKE' 	=> "%${search}%"),
				array('Venue.address LIKE' 	=> "%${search}%"),
				array('Venue.state LIKE' 	=> "%${search}%"),
				array('Venue.city LIKE' 	=> "%${search}%"),
				array('Venue.zip LIKE' 		=> "%${search}%"),
				);
		}

		if(!empty($features)){
			$featuresExp = explode(",", $features);
			$featuresExpCount = count($featuresExp);
			if($featuresExpCount == 1){
				$feature_cond = array('FeaturesVenue.feature_id' => $features);
			}else{
				$feature_cond = array('FeaturesVenue.feature_id IN ' => $featuresExp);
			}
			array_push($or_cond, $feature_cond);
		}

		if(!empty($areas)){
			$areasExp = explode(",", $areas);
			$areasExpCount = count($areasExp);
			if($areasExpCount == 1){
				$area_cond = array('AreasVenue.area_id' => $areas);
			}else{
				$area_cond = array('AreasVenue.area_id IN ' => $areasExp);
			}
			array_push($or_cond, $area_cond);
		}


		$params['conditions'] = array(
		    'OR' => array(
		        array('CategoriesVenue.category_id' => $category),
		        array('CategoriesVenue.category_id' => $sub_category)
		    ),
		    'AND' => array(
		        array(
		            'OR' => $or_cond
		        )
		    )
		);


		$params['limit'] 		= $limit;
		$params['page'] 		= $page;

		$params['fields'] 		= array('Venue.*');

		$venues = $this->Venue->find('all',$params);

		# get the total records
		unset($params['limit']);
		unset($params['page']);
		$totalvenues = $this->Venue->find('all', $params);
		$total_venues = count($totalvenues);
		$total_pages = ceil($total_venues/$limit);
		$page_num = (isset($page) && !empty($page)) ? $page : 1;
		$per_page = (isset($limit) && !empty($limit)) ? $limit : 10;
		$start = (($page_num - 1)*$limit) + 1;
		$end = ($start+$limit) - 1;



		$responce['pegination']['total_records'] = $total_venues;
		$responce['pegination']['total_pages'] = $total_pages;
		$responce['pegination']['page_num'] = $page_num;
		$responce['pegination']['per_page'] = $per_page;
		$responce['pegination']['start'] = $start;
		$responce['pegination']['end'] = $end;
		$responce['venues'] = $venues;
		$this->set(array(
            'venues' => $responce,
            '_serialize' => array('venues'),
            'total_records'=> $total_venues,
        ));

        unset($venues); unset($features); unset($search);
	}

	/* this function is made for manage your venue to turn
	 * on and off the bottle values in venues table
	 */
	public function updateVenueBottleValue() {
		$user_id  				= isset($this->request->data['user_id']) ? $this->request->data['user_id'] : "";
		$turn_bottle_serv_on  	= isset($this->request->data['turn_bottle_serv_on']) ? $this->request->data['turn_bottle_serv_on'] : "0";
		$venue_id  				= isset($this->request->data['venue_id']) ? $this->request->data['venue_id'] : "";

		// Check if previous entry of venue bottle service is available or not
		$this->BottleService = ClassRegistry::init('BottleService');
		$options = array('conditions' => array('BottleService.venue_id' => $venue_id));
		$bottleServiceExists = $this->BottleService->find('first', $options);
		$service_id = isset($bottleServiceExists['BottleService']['id']) ? $bottleServiceExists['BottleService']['id'] : '';

		if($turn_bottle_serv_on == "y")
			$tbso = 1;
		else
			$tbso = 0;

		$today = date('Y-m-d H:i:s');
		if(!empty($service_id)) {
			$this->BottleService->id = $service_id;
			$input = array(
				'turn_bottle_service_on' => $tbso,
				);
		} else {
			$input = array(
				'venue_id'	=> $venue_id,
				'turn_bottle_service_on' => $tbso,
				'created' 	=> $today,
				'updated'	=> $today,
				);
		}

		if($this->BottleService->save($input)){
			$responce['status'] = "success";
			if($turn_bottle_serv_on == "y")
				$responce['message'] = "Bottle Service Turn on succssfully.";
			elseif($turn_bottle_serv_on == "n")
				$responce['message'] = "Bottle Service Turn off succssfully.";
		}
		else {
			$responce['status'] 	= "error";
			$responce['message'] 	= "Error occured while saving the record.";
		}

		$this->set(array(
            'venues' 		=> $responce,
            '_serialize' 	=> array('venues'),
        ));
        unset($venues);
	}

	/* this function is made for manage your venue to save messages
	 *  in venues_admin_message table and send message to all the person which
	 *  is belong to that venue in venue_review table
	 */
	public function updateVenueMessage() {
		$user_id  				= isset($this->request->data['user_id']) ? $this->request->data['user_id'] : "";
		$message  				= isset($this->request->data['message']) ? strip_tags($this->request->data['message']) : "";
		$venue_id  				= isset($this->request->data['venue_id']) ? $this->request->data['venue_id'] : "";

		if(empty($message)){
			$responce['status'] 	= "error";
			$responce['message'] 	= "Message not available.";
			 $this->set(array(
				'venues' 		=> $responce,
				'_serialize' 	=> array('venues'),
			));
			 unset($venues);
		}
		$VenueReviewCount = $this->VenueReview->find('count', array('conditions' => array('VenueReview.venue_id' => $venue_id)));


		if(empty($VenueReviewCount))
			$num_receipient = 0;
		else
			$num_receipient = $VenueReviewCount;
		if($num_receipient == 0){
			$responce['status'] 	= "error";
			$responce['message'] 	= "No user available to sent message .";
			 $this->set(array(
				'venues' 		=> $responce,
				'_serialize' 	=> array('venues'),
			));
			 unset($venues);

		}

		$input = array(
			'user_id' 			=> $user_id,
			'message' 			=> $message,
			'created_date' 		=> date('Y-m-d H:i:s'),
			'num_receipient' 	=> $num_receipient,
		);


		if($this->VenueAdminMessage->save($input)){
			$messageInsertId = $this->VenueAdminMessage->getLastInsertId();
			$VenueReviewDetail = $this->VenueReview->find('all', array('conditions' => array('VenueReview.venue_id' => $venue_id)));

			if(empty($VenueReviewDetail)) {
				$responce['status'] 	= "error";
				$responce['message'] 	= "No user available to sent message .";
			}
			else {
				foreach($VenueReviewDetail as $key => $VenueReview) {
					$UserDetails = $this->User->find('first', array('conditions' => array('User.id' => $VenueReview['VenueReview']['user_id'])));
					if(empty($UserDetails)) continue;
					if(empty($UserDetails['User']['email'])) continue;

					$VenueMessageQueueInput = array(
						'venue_id' 			=> $venue_id,
						'message_id' 		=> $messageInsertId,
						'log_date' 			=> date('Y-m-d H:i:s'),
						'email' 			=> $UserDetails['User']['email'],
					);


					$this->VenueMessageQueue->create();
					$this->VenueMessageQueue->save($VenueMessageQueueInput);
				}

				## to run the uel from curl not from cron
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, "http://shoutoutmycity.com/api/v1.0/venues/sendCronEmailForVenue.json");
				curl_setopt($ch, CURLOPT_HEADER, 0);
				$res = curl_exec($ch);
				curl_close($ch);
				//print_r($res); exit;
				if($res == 1){
					$responce['status'] 	= "success";
					$responce['message'] 	= "Message sent succssfully.";
				}
				else{
					$responce['status'] 	= "error";
					$responce['message'] 	= "Error occured while sending the message.";
				}
				##
					$responce['status'] 	= "success";
					$responce['message'] 	= "Message sent succssfully.";

			}

		}
		else {
			$responce['status'] 	= "error";
			$responce['message'] 	= "Error occured while sending the message.";
		}

		 $this->set(array(
            'venues' 		=> $responce,
            '_serialize' 	=> array('venues'),
        ));
         unset($venues);

	}

	/* this function is made for manage your venue to get
	 * details of bottole service and venue photos
	 */
	public function getVenueDetail() {
		$venue_id  				= isset($this->request->data['venue_id']) ? $this->request->data['venue_id'] : "";

		if($venue_id == ''){
			$responce['status'] = "error";
			$responce['message'] = "Venue not exists.";
		}
		else {
			$VenueDetail = $this->Venue->find('first', array('conditions' => array('Venue.id' => $venue_id)));
			if(!empty($VenueDetail)){
				# get venue reviews
				$Venuereveiews = $this->VenueReview->find('all', array('conditions' => array('VenueReview.venue_id' => $venue_id)));
				$VenueDetail['VenueReview']	= (isset($Venuereveiews) && !empty($Venuereveiews)) ? $Venuereveiews : array();
			}

			if(!empty($VenueDetail)) {
				$responce['VenueDetail'] = $VenueDetail;
			}
			else {
				$responce['VenueDetail'] = "";
			}
		}
		$this->set(array(
            'venues' 		=> $responce,
            '_serialize' 	=> array('venues'),
        ));
        unset($venues);
	}



	function send_push_notification($device_token, $message, $params = null) {
		// Put your device token here (without spaces):
		$deviceToken = $device_token;

		// Put your private key's passphrase here:
		$passphrase = 'Shoutoutcity'; //'Shareaflash';

		// Put your alert message here:
		// $message = $message;

		////////////////////////////////////////////////////////////////////////////////

		$ctx = stream_context_create();
		stream_context_set_option($ctx, 'ssl', 'local_cert', CUSTOM_PATH.DS.'Model/ck.pem');
		stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);

		// Open a connection to the APNS server
		$fp = stream_socket_client(
		'ssl://gateway.push.apple.com:2195', $err,
		$errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);


		$status_msg = "";

		if (!$fp) {

			$status_msg = "Failed to connect: $err $errstr";

		} else {

			// Create the payload body
			$body['aps'] = array(
				'alert' => $message,
				'sound' => 'default'
				);

			/*
			if(isset($params['receiver_id'])){
				$body['receiver_id'] = $params['receiver_id'];
			}
			*/

			// Encode the payload as JSON
			$payload = json_encode($body);

			// Build the binary notification
			$msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;

			// Send it to the server
			$result = fwrite($fp, $msg, strlen($msg));

			// Close the connection to the server
			fclose($fp);
		}
	}


	/*
	**	@13/12/2014 by 037, Send Push Notification to the android device
	**	@param $android_device_token The message to send
	**	@param $message The message to send
	**	@param $params Array of data to accompany the message
	*/
	// Note: Multiple android device token can be set in single request (the data type for both android_device_token and message is, array)
	public function sendAndroidPushNotification($android_device_token, $message, $params = null)
	{
		try {

			// Set POST variables
			$url = 'https://android.googleapis.com/gcm/send';

			$fields = array(
				'registration_ids' => array($android_device_token),
				'data' => array( "message" => $message ),
			);

			// Other Params
			if(is_array($params)){
				foreach ($params as $key => $value) {
					$fields['data'][$key] = $value;
				}
			}

			$headers = array(
				'Authorization: key=' . GOOGLE_API_KEY,
				'Content-Type: application/json'
			);

			// Open connection
			$ch = curl_init();

			// Set the url, number of POST vars, POST data
			curl_setopt( $ch, CURLOPT_URL, $url );
			curl_setopt( $ch, CURLOPT_POST, true );
			curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
			curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode( $fields ) );

			// Avoids problem with https certificate
			curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false);

			// Execute post
			$result = curl_exec($ch);

			// Close connection
			curl_close($ch);

			//Result
			$result = json_decode($result, TRUE);

		} catch (Exception $e) {
			$result = array();
		}

		return $result;
	}

	/*
	**	@13/12/2014 by 037, Send Push Notification to the iphone device
	**	@param $iphone_device_token The message to send
	**	@param $message The message to send
	**	@param $params Array of data to accompany the message
	*/
	public function sendIphonePushNotification($iphone_device_token, $message, $params = null ){

		try {
			// Put your device token here (without spaces):
			$deviceToken = $iphone_device_token;

			// Put your private key's passphrase here:
			$passphrase = 'srsinfo201';
			$development = PUSH_DEVELOPMENT;
			$apns_url = '';
			$apns_cert = '';
			$apns_port = 2195;

			////////////////////////////////////////////////////////////////////////////////
			if($development)
			{
				$apns_url = 'gateway.sandbox.push.apple.com';
				$apns_cert = KEY_PATH.'key.pem';
			}
			else
			{
				$apns_url = 'gateway.push.apple.com';
				$apns_cert = KEY_PATH.'key.pem';
			}

			$stream_context = stream_context_create();
			stream_context_set_option($stream_context, 'ssl', 'local_cert', $apns_cert);
			stream_context_set_option($stream_context, 'ssl', 'passphrase', $passphrase);

			$fp = stream_socket_client('ssl://' . $apns_url . ':' . $apns_port, $err, $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $stream_context);

			$status_msg = "";

			if (!$fp) {

				return	$status_msg = "Failed to connect: $err $errstr";

			} else {

				// Create the payload body
				$body['aps'] = array(
					'alert' => $message,					
					'sound' => 'default',

					);
				
				if(!is_null($params)) {
					$body['aps']['params'] = $params;					
				}


				if(isset($params['message_owner_id'])){
					$body['message_owner_id'] = $params['message_owner_id'];
				}

				// Encode the payload as JSON
				$payload = json_encode($body);

				// Build the binary notification
				$msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;

				// Send it to the server
				$result = fwrite($fp, $msg, strlen($msg));

				if (!$result) {
					$status_msg = 'Message not delivered';
				}
				else {
					$status_msg = 'Message successfully delivered';

				}

				// Close the connection to the server
				fclose($fp);
			}
		} catch (Exception $e) {
			$status_msg = '';
		}

		return $status_msg;
	}



	public function check_user_data() {
		$auth_type = $this->getParams('auth_type', 'web');
		$auth_key = $this->getParams('auth_key');
		$message = $this->getParams('message', 'Welcome, to ShoutoutCity');

		//Validations
		$validationErrors = array();
		if(empty($auth_key))
			$validationErrors['auth_key'] = 'Auth key is blank';
		else {
			$user_id = $this->User->get_user_id($auth_key);
			if(empty($user_id))
				$validationErrors['auth_key'] = 'Invalid auth key';
		}

		if(!empty($validationErrors)) {
			$response = array('status' => 'error', 'operation' => 'check_user_data', 'validationErrors' => $validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}

		$userExists = $this->User->findById($user_id);

		if(isset($userExists['ios_device_token']) && !empty($userExists['ios_device_token'])){

			$this->send_push_notification($userExists['ios_device_token'], $message);
		}
	}

	/**
	 * this cron function is made for email to all the assoiciated reviewers' email
	 * which is belog to this venue. it is cron which send email in every 5 min
	 * with the limit of 500 per 5 minute from venue_message_queues table
	 */
	public function sendCronEmailForVenue() {
     		$this->autoRender = false;

		//$site_title  				= Configure::read('site_title');
		//$site_email  				= Congifure::read('site_email');
		$site_title  				= "Shoutout";
		$site_email  				= SITE_EMAIL;

		$params['conditions'] = array(
		    'AND' => array(
		        array('VenueMessageQueue.message_status = ' => 0),
		    )
		);
		$params['limit'] 		= "100";
		$VenueMessageQueueDetail = $this->VenueMessageQueue->find('all', $params);

		// ---------------email send code start	-----------------------------
		if(!empty($VenueMessageQueueDetail) ){
			$iphone_given = "";
			$android_given = "";
			foreach($VenueMessageQueueDetail as $VenueMessageQueue){
				$to = "";
				$email_status = false;
				$message_id = $VenueMessageQueue['VenueMessageQueue']['message_id'];
				$id 		= $VenueMessageQueue['VenueMessageQueue']['id'];

				$VenueAdminMessageDetail = $this->VenueAdminMessage->find('first', array('conditions' => array('VenueAdminMessage.id' => $message_id)));

				$user_id 	= $VenueAdminMessageDetail['VenueAdminMessage']['user_id'];
				$UserDetail = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
				$userName 	= $UserDetail['User']['name'];

				$from 		= $site_email;
				$to 		= $VenueMessageQueue['VenueMessageQueue']['email'];

				$message 	= $VenueAdminMessageDetail['VenueAdminMessage']['message'];
				//$message .= "\n\n".$site_title;

				$Email = new CakeEmail();
				$Email->from($from)
					->to($to)
					->subject($userName.' - Venue Detail - '.$site_title)
					->emailFormat('html');
				$email_status = $Email->send($message);

				if($email_status)
				{
					//--------------if email send the make message_status as 1 -------------------
					$this->VenueMessageQueue->id = $VenueMessageQueue['VenueMessageQueue']['id'];
					$VenueMessageQueueInput = array(
						'message_status' 		=> "1",

					);
					$this->VenueMessageQueue->save($VenueMessageQueueInput);

					//Send Push Notification
					$userExists = $this->User->findByEmail($to);
					if($iphone_given == ""){
						if(isset($userExists['User']['ios_device_token']) && !empty($userExists['User']['ios_device_token'])){
							//$this->send_push_notification($userExists['ios_device_token'], $message);
							$this->sendIphonePushNotification($userExists['User']['ios_device_token'], $message);
							$iphone_given = "done";
						}
					}
					
					if($android_given == ""){
						if(isset($userExists['User']['android_device_token']) && !empty($userExists['User']['android_device_token'])){
							$this->sendAndroidPushNotification($userExists['User']['android_device_token'], $message);
							$android_given = "done";
						}
					}
					
					/*//--------uncomment below line when cron will set on online
					#$this->VenueMessageQueue->delete($id);
					$responce['status'] = "success";
					$responce['message'] = "Email send successfully\n".$to."----".$from;
					//print_r($responce);


					 $this->set(array(
					    'venues' 		=> $responce,
					    '_serialize' 	=> array('venues'),
					));*/


				}

			}
		
		
			//$this->sendIphonePushNotification($userExists['User']['ios_device_token'], $message);
			//$this->sendAndroidPushNotification($userExists['User']['android_device_token'], $message);
			//--------uncomment below line when cron will set on online
			#$this->VenueMessageQueue->delete($id);
			$responce['status'] = "success";
			$responce['message'] = "Email send successfully\n".$to."----".$from;
			//print_r($responce);


			 $this->set(array(
				'venues' 		=> $responce,
				'_serialize' 	=> array('venues'),
			));
			
			
		}

	}

	protected function _sendEmail($from, $to, $subject, $template, $emailType, $theme = null, $viewVars = null) {
		if (is_null($theme)) {
			$theme = $this->theme;
		}
		$success = false;

		try {
			$email = new CakeEmail();
			$email->from($from[1], $from[0]);
			$email->emailFormat('html');
			$email->to($to);
			$email->subject($subject);
			$email->template($template);
			$email->viewVars($viewVars);
			$email->theme($theme);
			$success = $email->send();
		} catch (SocketException $e) {
			$this->log(sprintf('Error sending %s notification : %s', $emailType, $e->getMessage()));
		}

		return $success;
	}

	/* this function is made for delete your venue photo
	 *  which belong to venue_photo table
	 */
	public function deleteVenuePhoto() {
		$photo_id  				= isset($this->request->data['photo_id']) ? $this->request->data['photo_id'] : "";
		$folder_location  		= WWW_ROOT;

		$VenuePhotoDetail = $this->VenuePhoto->find('first', array('conditions' => array('VenuePhoto.id' => $photo_id)));
		if(empty($VenuePhotoDetail)) {
			$responce['status'] 	= "error";
			$responce['message'] 	= "This photo doesnot exists!";
		}
		else {
			$venuePhoto = $VenuePhotoDetail['VenuePhoto']['photo'];
			$path_parts 	= pathinfo($venuePhoto);


			$venuePhotoOrignal 		= $folder_location.$venuePhoto;
			$venuePhotoLarge 		= $folder_location.$path_parts['dirname'].'/'.$path_parts['filename'].'_large.'.$path_parts['extension'];
			$venuePhotoThumb 		= $folder_location.$path_parts['dirname'].'/'.$path_parts['filename'].'_thumb.'.$path_parts['extension'];

			unlink($venuePhotoOrignal);
			unlink($venuePhotoLarge);
			unlink($venuePhotoThumb);

			$this->VenuePhoto->delete($VenuePhotoDetail['VenuePhoto']['id']);
			$responce['status'] 	= "success";
			$responce['message'] 	= "Photo deleted successfully.";
		}
		$this->set(array(
            'venues' 		=> $responce,
            '_serialize' 	=> array('venues'),
        ));
        unset($venues);
	}

	/* this function is made for delete your venue photo
	 *  which is in queue table only before upload button click
	 *
	 */
	public function deleteVenuePhotoBeforeUpload() {
		$user_id  				= isset($this->request->data['user_id']) ? $this->request->data['user_id'] : "";
		$venue_id  				= isset($this->request->data['venue_id']) ? $this->request->data['venue_id'] : "";
		$unique_id  			= isset($this->request->data['unique_id']) ? $this->request->data['unique_id'] : "";
		$ajax_row_id  			= isset($this->request->data['ajax_row_id']) ? $this->request->data['ajax_row_id'] : "";
		$folder_location  		= WWW_ROOT;

		$VenuePhotoQueueDetail = $this->VenuePhotoQueue->find('first', array('conditions' => array(
							'VenuePhotoQueue.user_id' => $user_id,
							'VenuePhotoQueue.venue_id' => $venue_id,
							'VenuePhotoQueue.unique_id' => $unique_id,
							'VenuePhotoQueue.ajax_row_id' => $ajax_row_id
						)));
		if(empty($VenuePhotoQueueDetail)) {
			$responce['status'] 	= "error";
			$responce['message'] 	= "Photo not deleted successfully.";
		}
		else {
			$venuePhotoQueue		= $VenuePhotoQueueDetail['VenuePhotoQueue']['photo'];
			$path_parts 			= pathinfo($venuePhotoQueue);


			$venuePhotoOrignal 		= $folder_location.$venuePhotoQueue;
			$venuePhotoLarge 		= $folder_location.$path_parts['dirname'].'/'.$path_parts['filename'].'_large.'.$path_parts['extension'];
			$venuePhotoThumb 		= $folder_location.$path_parts['dirname'].'/'.$path_parts['filename'].'_thumb.'.$path_parts['extension'];

			unlink($venuePhotoOrignal);
			unlink($venuePhotoLarge);
			unlink($venuePhotoThumb);

			$this->VenuePhotoQueue->delete($VenuePhotoQueueDetail['VenuePhotoQueue']['id']);
			$responce['status'] 	= "success";
			$responce['message'] 	= "Photo deleted successfully.";
		}
		$this->set(array(
            'venues' 		=> $responce,
            '_serialize' 	=> array('venues'),
        ));
        unset($venues);
	}

	/**
	 * This action save photos in temporary queue table
	 * this action will run before click on upload button
	 */
	public function uploadVenuePhotoBeforeUpload() {

		$user_id  				= isset($this->request->data['user_id']) ? $this->request->data['user_id'] : "";
		$venue_id  				= isset($this->request->data['venue_id']) ? $this->request->data['venue_id'] : "";
		$unique_id  			= isset($this->request->data['unique_id']) ? $this->request->data['unique_id'] : "";
		$ajax_val  				= isset($this->request->data['ajax_val']) ? $this->request->data['ajax_val'] : "";
		$folder_location  		= WWW_ROOT;
		$attachment  			= isset($this->request->data['attachment']) ? $this->request->data['attachment'] : "";
		$attachmentName  		= isset($this->request->data['attachmentName']) ? $this->request->data['attachmentName'] : "";
		$attachmentType  		= isset($this->request->data['attachmentType']) ? $this->request->data['attachmentType'] : "";
		$attachmentSize  		= isset($this->request->data['attachmentSize']) ? $this->request->data['attachmentSize'] : "";
		$attachmentError  		= isset($this->request->data['attachmentError']) ? $this->request->data['attachmentError'] : "";
		$attachmentTmpName  	= isset($this->request->data['attachmentTmpName']) ? $this->request->data['attachmentTmpName'] : "";
		$image_data  			= isset($this->request->data['image_data']) ? $this->request->data['image_data'] : "";


		if(isset($attachment) && !empty($attachment)){
			# Create a array to feed to
			$attachement = array();
			$attachement['file_name'] = isset($attachmentName) ? $attachmentName : "error";

			$remove_these 	= array(' ','`','"','\'','\\','/','»','>>');
			$file_name 		= $attachmentName;
			$file_name 		= str_replace($remove_these, '', $file_name);
			$path_parts 	= pathinfo($file_name);
			//$file_name 		= $path_parts['filename'].'_'.time().'.'.$path_parts['extension'];
			$file_name 		= $venue_id.'_' . rand()."_".microtime() . '.'.$path_parts['extension'];
			$file_name 		= str_replace(" ", '', $file_name);

			$upload_path 	= 'uploads'.DS.'venues'.DS;
			$upload_dir  	= $folder_location.$upload_path;

			$errors     = array();
			$maxsize    = 7340032;
			$acceptable = array('image/gif', 'image/png', 'image/jpg', 'image/jpeg', 'image/bmp', 'image/x-icon');

			if(($attachmentSize >= $maxsize) || ($attachmentSize == 0)) {
				$responce['status'] = 'error';
				$responce['message'] = "image must be less than 7MB.";
				$this->set(array(
					'venues' 		=> $responce,
					'_serialize' 	=> array('venues'),
				));
				unset($venues);
			}

			if(!in_array($attachmentType, $acceptable) AND (!empty($attachmentType))) {
				$responce['status'] = 'error';
				$responce['message'] = "Please only upload png, jpg, jpeg, bmp, x-icon, gif Images.";
				$this->set(array(
					'venues' 		=> $responce,
					'_serialize' 	=> array('venues'),
				));
				unset($venues);
			}

			$staticTime = time();
			$remove_these 	= array(' ','`','"','\'','\\','/');
			$file_name 		= $attachmentName;
			$file_name 		= str_replace($remove_these, '', $file_name);
			$path_parts 	= pathinfo($file_name);
			//$file_name 		= $path_parts['filename'].'_'.$staticTime.'.'.$path_parts['extension'];
			$file_name 		= $venue_id.'_'. rand()."_".microtime() . '.'.$path_parts['extension'];
			$file_name 		= str_replace(" ", '', $file_name);


		    $file_name 		= explode("»", $file_name);
			$file_name 		= implode("", $file_name);


			$upload_path 	= 'uploads'.DS.'venues'.DS;
			$upload_dir  	= $folder_location.$upload_path;
			$upload_url 	= Router::url('/', true) . $upload_path;

			if(!file_exists($upload_dir)) mkdir($upload_dir, 0755, true);
			chmod($upload_dir, 0755);

			$arr = base64_decode($image_data);
			$fname = $upload_dir.$file_name;
			$handle = fopen($fname, "w+");

			//fclose($handle);
			//if(!file_exists($fname))

			if(fwrite($handle, $arr)) {
				//-------------- image compression start -----------------
				// here we are replacing the compressed image with the same orignal image
				$source_image = $upload_dir.$file_name;
				$destination_image = $upload_dir.$file_name;

				//$new_compressed_image = $this->image->generateCompressImage($source_image, $destination_image);
				$new_compressed_image = $this->Image->generateCompressImagePhoto($source_image, $destination_image);
				//-------------- image compression end -----------------

				//-------------- make the thumbnail start -----------------
				$new_image = array();
				$new_image['Image']['name']['name'] 	= $file_name;
				$new_image['Image']['name']['type'] 	= $attachmentType;
				$new_image['Image']['name']['tmp_name'] = $upload_dir.$file_name;
				$new_image['Image']['name']['error'] 	= $attachmentError;
				$new_image['Image']['name']['size'] 	= $attachmentSize;

				$image_path_large = $this->Image->upload_image_and_thumbnail($new_image,"name",500,600,true, "_large");
				$image_path_thumb = $this->Image->upload_image_and_thumbnail($new_image,"name",161,161,true, "_thumb");
				//-------------- make the thumbnail end -----------------



				$fileName = $upload_path.$file_name;

				$this->VenuePhotoQueue = ClassRegistry::init('VenuePhotoQueue');

				//------------- firstly delete the photo for this user_id, venue_id and not this unique_id
				//------------- if exist then remove from database and also from folder
				$VenuePhotoQueueDetail = $this->VenuePhotoQueue->find('all', array('conditions' => array(
																	'VenuePhotoQueue.user_id' => $user_id,
																	'VenuePhotoQueue.venue_id' => $venue_id,
																	'VenuePhotoQueue.unique_id != ' => $unique_id))
																);
				foreach($VenuePhotoQueueDetail as $VenuePhotoQueue) {
					$VenuePhotoQueueId  = $VenuePhotoQueue['VenuePhotoQueue']['id'];
					$venuePhoto 		= $VenuePhotoQueue['VenuePhotoQueue']['photo'];
					$path_parts 		= pathinfo($venuePhoto);

					$venuePhotoOrignal 	= $folder_location.$venuePhoto;
					$venuePhotoLarge 	= $folder_location.$path_parts['dirname'].'/'.$path_parts['filename'].'_large.'.$path_parts['extension'];
					$venuePhotoThumb 	= $folder_location.$path_parts['dirname'].'/'.$path_parts['filename'].'_thumb.'.$path_parts['extension'];

					@unlink($venuePhotoOrignal);
					@unlink($venuePhotoLarge);
					@unlink($venuePhotoThumb);

					$this->VenuePhotoQueue->delete($VenuePhotoQueueId);
				}

				$this->VenuePhotoQueue->create();
				$inputPhoto = array(
					'photo' 		=> $fileName,
					'created' 		=> date('Y-m-d H:i:s'),
					'venue_id' 		=> $venue_id,
					'user_id' 		=> $user_id,
					'unique_id' 	=> $unique_id,
					'ajax_row_id' 	=> $ajax_val,
					);

				if($this->VenuePhotoQueue->save($inputPhoto)) {
				} else {
				}
				$responce['status']  = 'success';
				$responce['message'] = "photo saved successfully.";
				$this->set(array(
					'venues' 		=> $responce,
					'_serialize' 	=> array('venues'),
				));
				unset($venues);
			}
			else {
				$responce['status']  = 'error';
				$responce['message'] = 'Error occured while saving data.';
				$this->set(array(
					'venues' 		=> $responce,
					'_serialize' 	=> array('venues'),
				));
				unset($venues);
			}
		}
	}


	/**
	 * This action save photos in temporary queue table
	 * this action will run before click on upload button
	 */
	public function uploadVenuePhotoAfterUpload() {
		$unique_id  				= isset($this->request->data['unique_id']) ? $this->request->data['unique_id'] : "";
		$venue_id_orignal  			= isset($this->request->data['venue_id']) ? $this->request->data['venue_id'] : "";
		$user_id_orignal  			= isset($this->request->data['user_id']) ? $this->request->data['user_id'] : "";
		$get_result = "";
											
//////////////////////////////////////////////////////////////////////
		//$this->VenueReview = ClassRegistry::init('VenueReview');		
		$message  				= "promotion";		
		$VenueReviewCount = $this->VenueReview->find('count', array('conditions' => array('VenueReview.venue_id' => $venue_id_orignal)));
		if(empty($VenueReviewCount))
			$num_receipient = 0;
		else
			$num_receipient = $VenueReviewCount;
		if($num_receipient != 0){

			$input = array(
				'user_id' 			=> $user_id_orignal,
				'message' 			=> $message,
				'created_date' 		=> date('Y-m-d H:i:s'),
				'num_receipient' 	=> $num_receipient,
			);
			//$this->VenueAdminMessage = ClassRegistry::init('VenueAdminMessage');	
			if($this->VenueAdminMessage->save($input)){
				$messageInsertId = $this->VenueAdminMessage->getLastInsertId();
				$VenueReviewDetail = $this->VenueReview->find('all', array('conditions' => array('VenueReview.venue_id' => $venue_id_orignal)));
				//$this->VenueMessageQueue = ClassRegistry::init('VenueMessageQueue');
				if(!empty($VenueReviewDetail)) {					
					foreach($VenueReviewDetail as $key => $VenueReview) {
						//$this->User = ClassRegistry::init('User');
						$UserDetails = $this->User->find('first', array('conditions' => array('User.id' => $VenueReview['VenueReview']['user_id'])));
						if(empty($UserDetails)) continue;
						if(empty($UserDetails['User']['email'])) continue;

						$VenueMessageQueueInput = array(
							'venue_id' 			=> $venue_id_orignal,
							'message_id' 		=> $messageInsertId,
							'log_date' 			=> date('Y-m-d H:i:s'),
							'email' 			=> $UserDetails['User']['email'],
							'message_status'	=> '3',

						);
				
						$this->VenueMessageQueue->create();
						$this->VenueMessageQueue->save($VenueMessageQueueInput);
					}
					$get_result = "success";

				}
			}
		}		
/////////////////////////////////////////////////////////////////////////

		if( isset($unique_id) && !empty($unique_id)){
			$this->VenuePhotoQueue = ClassRegistry::init('VenuePhotoQueue');
			$VenuePhotoQueueDetail = $this->VenuePhotoQueue->find('all', array('conditions' => array('VenuePhotoQueue.unique_id' => $unique_id)));
			foreach($VenuePhotoQueueDetail as $VenuePhotoQueue) {
				$VenuePhotoQueueId = $VenuePhotoQueue['VenuePhotoQueue']['id'];
				$VenuePhotoQueuePhoto = $VenuePhotoQueue['VenuePhotoQueue']['photo'];
				$this->VenuePromotion = ClassRegistry::init('VenuePromotion');
				$this->VenuePromotion->create();


				$inputPhoto = array(
					'promotion' 	=> $VenuePhotoQueue['VenuePhotoQueue']['photo'],
					'created' 	=> $VenuePhotoQueue['VenuePhotoQueue']['created'],
					'venue_id' 	=> $VenuePhotoQueue['VenuePhotoQueue']['venue_id'],
					'user_id' 	=> $VenuePhotoQueue['VenuePhotoQueue']['user_id'],
					);
				if($this->VenuePromotion->save($inputPhoto)) {
				} else {
				}
				//----------------------and after that also delete that data from photo queue table ------------------
				$this->VenuePhotoQueue->delete($VenuePhotoQueueId);
			}		

			if($get_result == "success"){
					## to run the uel from curl not from cron
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, "http://shoutoutmycity.com/api/v1.0/venues/sendCronEmailForPushNotiForImage.json");
					curl_setopt($ch, CURLOPT_HEADER, 0);
					$res = curl_exec($ch);
					curl_close($ch);
					
					if($res == 1){
						$responce['status'] 	= "success";
						$responce['message'] 	= "Message sent succssfully.";
					}
					else{
						$responce['status'] 	= "error";
						$responce['message'] 	= "Error occured while sending the message.";
					}
					##
				
			}

			
			$responce['status']  = 'success';
			$responce['message'] = 'Photo uploaded successfully.';
		}
		else {
			$responce['status']  = 'error';
			$responce['message'] = 'Error occured while uploading images.';
		}

		$this->set(array(
            'venues' 		=> $responce,
            '_serialize' 	=> array('venues'),
        ));
        unset($venues);
	}


	/**
	 * This is test action
	 */
	public function testSingleImageUpload() {
		$folder_location  				= isset($this->request->data['folder_location']) ? $this->request->data['folder_location'] : "";
		$attachment  					= isset($this->request->data['attachment']) ? $this->request->data['attachment'] : "";
		$image_data = isset($this->request->data['image_data']) ? $this->request->data['image_data'] : '';


		$arr = base64_decode($image_data);
		$fname = "/var/www/AIMS/shoutoutcity/".$attachment['name'];
		$handle = fopen($fname, "w+");
		fwrite($handle, $arr);
		fclose($handle);
		if(!file_exists($fname))

		$responce['status']  = 'error';
		$responce['message'] = 'Error occured while uploading images';
		$this->set(array(
            'venues' 		=> $responce,
            '_serialize' 	=> array('venues'),
        ));
        unset($venues);
		exit();
	}











	###############################################################################

	//@30/09/2014 by 037, Returns Tiemstamp from the day(like SUN, MON,..) and time(2AM, 3PM, ...)
	public function get_timestamp($day, $time) {
		switch ($day) {
			case 'SUN':
				$date = date("Y-m-d",strtotime('sunday this week'));
				break;
			case 'MON':
				$date = date("Y-m-d",strtotime('monday this week'));
				break;
			case 'TUES':
				$date = date("Y-m-d",strtotime('tuesday this week'));
				break;
			case 'WED':
				$date = date("Y-m-d",strtotime('wednesday this week'));
				break;
			case 'THURS':
				$date = date("Y-m-d",strtotime('thursday this week'));
				break;
			case 'FRI':
				$date = date("Y-m-d",strtotime('friday this week'));
				break;
			case 'SAT':
				$date = date("Y-m-d",strtotime('saturday this week'));
				break;
			default :
				$date = date("Y-m-d",strtotime('monday this week'));

		}

		$time = date("H:i:s", strtotime($time));
		$datetime = date("Y-m-d H:i:s", strtotime($date.$time));

		return strtotime($datetime);
	}

	/**
	 ** @purpose save the venue profile information (add/update)
	 ** @input auth_type, auth_key, venue_id (optional), name, address, city, state, zip, phone, mobile, email, description, logo, mark_as_prefered (y/n), area_id, weekday_from (array), weekday_to (array), weekday_time_from (array), weekday_time_to (array), happy_hour_from, happy_hour_to, happy_hour_time_from, happy_hour_time_to
	 ** @output
	 */
	public function save_venue_profile() {
		$auth_type = $this->getParams('auth_type', 'web');
		$auth_key = $this->getParams('auth_key');
		$venue_id = $this->getParams('venue_id');
		$name = $this->getParams('name');
		$address = $this->getParams('address');
		$state = $this->getParams('state');
		$city = $this->getParams('city');
		$zip = $this->getParams('zip');
		$phone = $this->getParams('phone');
		$mobile = $this->getParams('mobile');
		$website = $this->getParams('website');
		$description = $this->getParams('description');
		$mark_as_prefered = $this->getParams('mark_as_prefered');	// y/n
        $video = $this->getParams('video'); #79 22-12-2016
		$area_id = $this->getParams('area_id');
		$city_id = $this->getParams('city_id');

		$weekday_from = $this->getParams('weekday_from', $default = array());
		$weekday_to = $this->getParams('weekday_to', $default = array());
		$weekday_time_from = $this->getParams('weekday_time_from', $default = array());
		$weekday_time_to = $this->getParams('weekday_time_to', $default = array());
		$happy_hour_from = $this->getParams('happy_hour_from', $default = array());
		$happy_hour_to = $this->getParams('happy_hour_to', $default = array());
		$happy_hour_time_from = $this->getParams('happy_hour_time_from', $default = array());
		$happy_hour_time_to = $this->getParams('happy_hour_time_to', $default = array());

		$assigned_user_id = $this->getParams('assigned_user_id');
		$contact_person = $this->getParams('contact_person');
		$username = $this->getParams('username');
		$password = $this->getParams('password');
		$email = $this->getParams('email');

		$venue_email = $this->getParams('venue_email');
		$role_id = $this->getParams('role_id', '5');

		$dob = $this->getParams('dob');
		$converted_dob = strtotime($dob);
		$eligibility_dob = strtotime(date('Y-m-d', strtotime("-18 years")));
		$chk_assign_form = $this->getParams('chk_assign_form');

		$lattitude = $this->getParams('lattitude');
		$longitude = $this->getParams('longitude');
		
		$today = date('Y-m-d H:i:s');

		 if(empty($venue_id) ){
            // Validate venue information
            $venue_input = array(
                'assigned_user_id' => $assigned_user_id,
                'name'      => $name,
                'address'   => $address,
                'state'     => $state,
                'city'      => $city,
                'zip'       => $zip,
                'phone1'    => $phone,
                'phone2'    => $mobile,
                'email'     => $venue_email,
                'website'   => $website,
                'contact_person'   => $contact_person,
                'description' => $description,
                'prefered'  => $mark_as_prefered,
                'city_id'  => $city_id,
                'created'   => $today,
                'updated'   => $today,
                'venue_type' => "D",
				'venue_status' => 1,
                'lattitude'   => $lattitude,
                'video'       => $video,
                'longitude'   => $longitude,
            );
        } else {
            // Validate venue information
            $venue_input = array(
                'name'      => $name,
                'address'   => $address,
                'state'     => $state,
                'city'      => $city,
                'zip'       => $zip,
                'phone1'    => $phone,
                'phone2'    => $mobile,
                'email'     => $venue_email,
                'website'   => $website,
                'contact_person'   => $contact_person,
                'description' => $description,
                'prefered'  => $mark_as_prefered,
                'city_id'  => $city_id,
                'created'   => $today,
                'updated'   => $today,
				'venue_status' => 1,
                'lattitude'   => $lattitude,
                'video'       => $video,
                'longitude'   => $longitude,
            );
        }
      //
      //return $venue_input;
		$validationErrors = array();
		if(empty($auth_key))
			$validationErrors['auth_key'] = 'Auth key is blank';
		else {
			$user_id = $this->User->get_user_id($auth_key);
			if(empty($user_id))
				$validationErrors['auth_key'] = 'Invalid auth key';
			else
				$venue_input['user_id'] = $user_id;
		}


		// Validate the venue information
		$this->Venue->set( $venue_input );
		if(!$this->Venue->validates()) {
			$validationErrors = array_merge($validationErrors, $this->Venue->validationErrors);
		}

		// Validate the venue existence
		if(empty($validationErrors) && !empty($venue_id)) {
			$venueExists = $this->Venue->findByid($venue_id);
			if(empty($venueExists))
				$validationErrors['venue_id'] = 'Venue does not exists';
		}

		// Validate the selected area
		if(empty($validationErrors) && empty($area_id)) {
			//$validationErrors['area_id'] = 'Venue area is not selected';
		}

		// Validate the venue weekdays timing
		if(empty($validationErrors)) {
			if(empty($weekday_from) || empty($weekday_to) || empty($weekday_time_from) || empty($weekday_time_to)) {
				/*if(empty($weekday_from))
					$validationErrors['weekday_from'] = "This is a required field";
				if(empty( $weekday_to))
					$validationErrors['weekday_to'] = "@This is a required field";
				if(empty($weekday_time_from))
					$validationErrors['weekday_time_from'] = "@This is a required field";
				if(empty($weekday_time_to))
					$validationErrors['weekday_time_to'] = "@This is a required field";*/
			}
			else {
				foreach($weekday_from as $index => $start_from) {
					$end_to = $weekday_to[$index];
					if($start_from == 'SUN' && $end_to != 'SUN')
						$validationErrors['weekday_from'][$index + 1] = "Weekday From can not be SUN, pls select other day.";
				}
			}
		}

		# @ 04/09/2014 by 003, If the request is from web and venue_id is not set, then validate photos as well
		if($auth_type == 'web' ) {
			$photos = $this->getParams('photos', array());
			$promos = $this->getParams('promos', array());
			$floor_plans = $this->getParams('floor_plans', array());
			$logo = $this->getParams('logo');


			/*if(!empty($venueExists)){
				if($venueExists['Venue']['logo'] != ""){
				}
				else{
					if(empty($logo))
						$validationErrors['logo'] = 'Logo is not selected';
				}
			}
			else{
				if(empty($logo))
					$validationErrors['logo'] = 'Logo is not selected';
			}*/
		}

		if(!empty($dob) && empty($venue_id)) {
			if(empty($converted_dob) || $converted_dob < 0)
				$validationErrors['dob'] = 'Invalid date of birth';
			else if($converted_dob > $eligibility_dob)
				$validationErrors['dob'] = 'You are not 18 years old. DOB before ' . date('Y-m-d', $eligibility_dob) . ' is only allowed.';
		}

		if(!empty($validationErrors)) {
			$response = array('status' => 'error', 'operation' => 'save_venue_profile', 'validationErrors' => $validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}

		//Check if Happy Hours entered are valid or not
		if(!empty($happy_hour_from) && !empty($happy_hour_to) && !empty($happy_hour_time_from) && !empty($happy_hour_time_to)) {

			$weekcount = count($weekday_from);
			foreach($happy_hour_from as $key => $hour_from) {
				$hour_time_from = $happy_hour_time_from[$key];
				$hour_to = $happy_hour_to[$key];
				$hour_time_to = $happy_hour_time_to[$key];

				if($hour_from == 'SUN' && $hour_to != 'SUN')
					$validationErrors['happy_hours'][$key + 1] = "Invalid Happy Hours From Day, Please Select Other Day.";

				$happy_hr_from = $this->get_timestamp($hour_from, $hour_time_from);
				$happy_hr_to = $this->get_timestamp($hour_to, $hour_time_to);

				$i = 0 ;
				foreach($weekday_from as $index => $start_from) {
					$end_to = $weekday_to[$index];
					$time_from = $weekday_time_from[$index];
					$time_to = $weekday_time_to[$index];

					$week_from = $this->get_timestamp($start_from, $time_from);
					$week_to = $this->get_timestamp($end_to, $time_to);

					if( !($happy_hr_from >= $week_from) || !($happy_hr_to <= $week_to))
						$i++;
				}
				if($i == $weekcount)
					$validationErrors['happy_hours'][$key + 1] = 'Invalid Happy Hours : '.$hour_from.' - '.$hour_to.' '.$hour_time_from.' - '.$hour_time_to;
			}
		}



		if(!empty($validationErrors)) {
			$response = array('status' => 'error', 'operation' => 'save_venue_profile', 'validationErrors' => $validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}


		// Begin transaction
		$this->Venue->begin();


		//$venue_input['is_new_assignment'] = 0;
		//$old_assigned_user_id = $venueExists['Venue']['assigned_user_id'];
		//if($assigned_user_id != $old_assigned_user_id)
			//$venue_input['is_new_assignment'] = 1;


		if($chk_assign_form == 'y'){
			
			if(empty($assigned_user_id) && empty($venue_id))	{
				//if this is new record
				$enc_password = $this->User->get_encrypted_password($password);

				$userExists = $this->User->find('first', array('conditions' => array('User.id' => $assigned_user_id, 'User.email' => $email)));
				if(!empty($userExists) && isset($userExists['User'])) {

					
					$venue_input['assigned_user_id'] = $assigned_user_id;

					
					
					//$this->data['assigned_user_id'] = $assigned_user_id;
					$assigned_user_id_val = $assigned_user_id_val;
					$venue_input['is_new_user'] = 0;
				##if user role_id = 2 make it to role_id = 5
				$userExists = $this->User->find('first', array('conditions' => array('User.id' => $assigned_user_id, 'User.email' => $email)));
				$exists_user_role = $userExists['User']['role_id'];
				$exists_user_name = $userExists['User']['name'];
				if($exists_user_name == '')
					$user_name = $contact_person;
				else
					$user_name = $userExists['User']['name'];

				if($exists_user_role == 2){
					$user_input = array(
						'role_id'	=> '5',
						'name' => $user_name,
					);
					$this->User->id  =  $assigned_user_id;
					if(!$this->User->save($user_input, $validate = false)) {
						// Rollback transaction
						$this->Venue->rollback();
						$response = array('status' => 'error', 'operation' => 'save_venue_profile', 'dataErrors' => $this->User->validationErrors, 'line' => __LINE__);
						$this->set(array('response' => $response, '_serialize' => array('response')));
						return;
					}
				}
				##


				}
				else{
				$activationKey = md5(uniqid());
					$user_input = array(
						'role_id'	=> $role_id,
						'username' => $username,
						'password' => $enc_password,
						'name' => $contact_person,
						'email' => $email,
						'dob' => $dob,
						'status'	=> '1',
'activation_key' => $activationKey,
						'created'	=> $today,
						'updated'	=> $today,
					);



					if(!$this->User->save($user_input)) {
						// Rollback transaction
						$this->Venue->rollback();

						$response = array('status' => 'error', 'operation' => 'save_venue_profile', 'dataErrors' => $this->User->validationErrors, 'line' => __LINE__);
						$this->set(array('response' => $response, '_serialize' => array('response')));
						return;
					}



					$id = $this->User->getLastInsertId();

					$venue_input['is_new_user'] = 1;
					
					$venue_input['assigned_user_id'] = $id;

					//$this->data['assigned_user_id'] = $id;
					$assigned_user_id_val = $id;

					//*************** save data in acos table start ***************
					$this->Aro = ClassRegistry::init('Aro');
					$this->Aro->create();
					$user_input_aros = array(
						'alias'	=> $username,
						'parent_id' => 1,
						'model' => 'User',
						'foreign_key' => $id,
					);

					if(!$this->Aro->save($user_input_aros)) {
						$response = array('status' => "error", 'operation' => "save_venue_profile", 'dataErrors' => $this->User->validationErrors, 'line' => __LINE__);
						$this->set(array('response' => $response, '_serialize' => array('response')));
						return;
					}
					//*************** save data in acos table end ***************


				}
			}
			elseif(empty($assigned_user_id) && !empty($venue_id))	{
				//if this is existing venue
				$enc_password = $this->User->get_encrypted_password($password);

				$userExists = $this->User->find('first', array('conditions' => array('User.id' => $assigned_user_id, 'User.email' => $email)));
				if(!empty($userExists) && isset($userExists['User'])) {
					$venue_input['is_new_assignment'] = 1;
					$venue_input['assigned_user_id'] = $assigned_user_id;
					//$this->data['assigned_user_id'] = $assigned_user_id;
					$assigned_user_id_val = $assigned_user_id_val;
					
				##if user role_id = 2 make it to role_id = 5
				$userExists = $this->User->find('first', array('conditions' => array('User.id' => $assigned_user_id, 'User.email' => $email)));
				$exists_user_role = $userExists['User']['role_id'];
				$exists_user_name = $userExists['User']['name'];
				if($exists_user_name == '')
					$user_name = $contact_person;
				else
					$user_name = $userExists['User']['name'];

				if($exists_user_role == 2){
					$user_input = array(
						'role_id'	=> '5',
						'name' => $user_name,
					);
					$this->User->id  =  $assigned_user_id;
					if(!$this->User->save($user_input, $validate = false)) {
						// Rollback transaction
						$this->Venue->rollback();
						$response = array('status' => 'error', 'operation' => 'save_venue_profile', 'dataErrors' => $this->User->validationErrors, 'line' => __LINE__);
						$this->set(array('response' => $response, '_serialize' => array('response')));
						return;
					}
				}
				##

				}
				else{
				$activationKey = md5(uniqid());
					$user_input = array(
						'role_id'	=> $role_id,
						'username' => $username,
						'password' => $enc_password,
						'name' => $contact_person,
						'email' => $email,
						'dob' => $dob,
						'status'	=> '1',
						'activation_key' => $activationKey,
						'created'	=> $today,
						'updated'	=> $today,
					);



					if(!$this->User->save($user_input)) {
						// Rollback transaction
						$this->Venue->rollback();

						$response = array('status' => 'error', 'operation' => 'save_venue_profile', 'dataErrors' => $this->User->validationErrors, 'line' => __LINE__);
						$this->set(array('response' => $response, '_serialize' => array('response')));
						return;
					}



					$id = $this->User->getLastInsertId();
					$venue_input['is_new_user'] = 1;
					
					$venue_input['assigned_user_id'] = $id;

					//$this->data['assigned_user_id'] = $id;
					$assigned_user_id_val = $id;

					//*************** save data in acos table start ***************
					$this->Aro = ClassRegistry::init('Aro');
					$this->Aro->create();
					$user_input_aros = array(
						'alias'	=> $username,
						'parent_id' => 1,
						'model' => 'User',
						'foreign_key' => $id,
					);

					if(!$this->Aro->save($user_input_aros)) {
						$response = array('status' => "error", 'operation' => "save_venue_profile", 'dataErrors' => $this->User->validationErrors, 'line' => __LINE__);
						$this->set(array('response' => $response, '_serialize' => array('response')));
						return;
					}
					//*************** save data in acos table end ***************



				}
			}
			else
			{
				$assigned_user_id_val = $assigned_user_id;
				$venue_input['assigned_user_id'] = $assigned_user_id;
				$venue_input['is_new_user'] = 0;
				##if user role_id = 2 make it to role_id = 5
				$userExists = $this->User->find('first', array('conditions' => array('User.id' => $assigned_user_id, 'User.email' => $email)));
				$exists_user_role = $userExists['User']['role_id'];
				$exists_user_name = $userExists['User']['name'];
				if($exists_user_name == '')
					$user_name = $contact_person;
				else
					$user_name = $userExists['User']['name'];

				if($exists_user_role == 2){
					$user_input = array(
						'role_id'	=> '5',
						'name' => $user_name,
					);
					$this->User->id  =  $assigned_user_id;
					if(!$this->User->save($user_input, $validate = false)) {
						// Rollback transaction
						$this->Venue->rollback();
						$response = array('status' => 'error', 'operation' => 'save_venue_profile', 'dataErrors' => $this->User->validationErrors, 'line' => __LINE__);
						$this->set(array('response' => $response, '_serialize' => array('response')));
						return;
					}
				}
				##

			}

		}
		else
		{
			$assigned_user_id_val = $assigned_user_id;
			$venue_input['assigned_user_id'] = $assigned_user_id;
			$venue_input['is_new_user'] = 0;
			##if user role_id = 2 make it to role_id = 5
			$userExists = $this->User->find('first', array('conditions' => array('User.id' => $assigned_user_id, 'User.email' => $email)));
			if(!empty($userExists)){
				$exists_user_role = $userExists['User']['role_id'];
				$exists_user_name = $userExists['User']['name'];
				if($exists_user_name == '')
					$user_name = $contact_person;
				else
					$user_name = $userExists['User']['name'];



				if($exists_user_role == 2){
					$user_input = array(
						'role_id'	=> '5',
						'name' => $user_name,
					);
					$this->User->id  =  $assigned_user_id;
					if(!$this->User->save($user_input, $validate = false)) {
						// Rollback transaction
						$this->Venue->rollback();
						$response = array('status' => 'error', 'operation' => 'save_venue_profile', 'dataErrors' => $this->User->validationErrors, 'line' => __LINE__);
						$this->set(array('response' => $response, '_serialize' => array('response')));
						return;
					}
				}
				##
			}

		}
		
		


		if(!empty($venue_id))
			$venue_input['id'] = $venue_id;


		/* Save area and sub_area for current venue :) */
		if($area_id) {
			$this->Area->recursive = -1;
			$area_details = $this->Area->findById($area_id);
			if(isset($area_details['Area']['parent_id']) && !empty($area_details['Area']['parent_id'])) {
				// Get parent details
				$this->Area->recursive = -1;
				$parent_area_details = $this->Area->findById($area_details['Area']['parent_id']);

				$venue_input['area'] = isset($parent_area_details['Area']['name']) ? $parent_area_details['Area']['name'] : "";
				$venue_input['sub_area'] = isset($area_details['Area']['name']) ? $area_details['Area']['name'] : "";

			} else {
				$venue_input['area'] = isset($area_details['Area']['name']) ? $area_details['Area']['name'] : "";
			}
		}

				
			
		// Make entry in the venues table
		if(!$this->Venue->save($venue_input)) {
			// Rollback transaction
			$this->Venue->rollback();

			$response = array('status' => 'error', 'operation' => 'save_venue_profile', 'dataErrors' => $this->Venue->validationErrors, 'line' => __LINE__);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;

		}

		if(!empty($venue_id)) {		// If the request is to update the venue information

			// Delete all the previous associations of venue and the days available
			if(!$this->VenueDaysTime->deleteAll($conditions = array('VenueDaysTime.venue_id' => $venue_id))) {
				// Rollback transaction
				$this->Venue->rollback();

				$response = array('status' => 'error', 'operation' => 'save_venue_profile', 'dataErrors' => $this->VenueDaysTime->validationErrors, 'line' => __LINE__);
				$this->set(array('response' => $response, '_serialize' => array('response')));
				return;
			}

			// Delete venue area association
			if(!$this->AreasVenue->deleteAll($conditions = array('AreasVenue.venue_id' => $venue_id))) {
				// Rollback transaction
				$this->Venue->rollback();

				$response = array('status' => 'error', 'operation' => 'save_venue_profile', 'dataErrors' => $this->AreasVenue->validationErrors, 'line' => __LINE__);
				$this->set(array('response' => $response, '_serialize' => array('response')));
				return;
			}
			
			if(!empty($venue_id)){			
				$this->send_mail_to_all($venue_id);			
			}

		} else {	// When creating a new venue

			$venue_id = $this->Venue->getLastInsertId();

		}


		# @ 04/09/2014 by 003, Save the venue photos, if the request is from web
		if($auth_type == 'web') {
			# @ 11/09/2014 by 044, Save the venue photos, if the request is from web
			//$photo_ids = $this->save_venue_photos($venue_id, $user_id, $photos);
			$photos = $this->getParams('photos', array());
			$photo_ids = array();
			if(!empty($photos)){
				foreach($photos as $key => $photo) {

					$file_loc = 'uploads'.DS.'venues'.DS;
					$file_name = $venue_id.'_' . rand()."_".microtime() . '.jpg';
					$file_name 		= str_replace(" ", '', $file_name);

					$file_dir = WWW_ROOT . $file_loc;
					$file_url = Router::url('/', true) . $file_loc . $file_name;
					$fname = $file_dir . $file_name;

					if(!is_dir($file_dir))
						mkdir($file_dir, 0755, true);

					$raw_file = base64_decode($photo);
					$handle = fopen($fname, "w+");
					fwrite($handle, $raw_file);
					fclose($handle);

					if(!file_exists($fname) || filesize($fname) <= 0) {
						$validationErrors['photos'][$key + 1] = 'File could not be uploaded';
						continue;
					}

					$venue_photo_input = array(
						'venue_id' 	=> $venue_id,
						'user_id' 	=> $user_id,
						'photo' 	=> $file_loc . $file_name,
						'created' 	=> date('Y-m-d H:i:s')
					);

					$this->VenuePhoto->create();
					if(!$this->VenuePhoto->save($venue_photo_input)) {
						$validationErrors['photos'][$key + 1] = $this->VenuePhoto->validationErrors;
						continue;
					}
					else{
						list($width, $height, $type, $attr) = getimagesize($file_loc . $file_name);
						$source_image = $file_loc . $file_name;
						$destination_image = $file_loc . $file_name;

						$new_compressed_image = $this->Image->generateCompressImagePhoto($source_image, $destination_image);
						$attachmentSize = array('width' => $width, 'height' => $height);

						$new_image = array();
						$new_image['Image']['name']['name'] 	= $file_name;
						$new_image['Image']['name']['type'] 	= "image/jpg";
						$new_image['Image']['name']['tmp_name'] = $source_image;
						$new_image['Image']['name']['error'] 	= "";
						$new_image['Image']['name']['size'] 	= $attachmentSize;

						$image_path_large = $this->Image->upload_image_and_thumbnail($new_image,"name",500,600,true, "_large");
						$image_path_thumb = $this->Image->upload_image_and_thumbnail($new_image,"name",161,161,true, "_thumb");

					}
					$photo_ids[] = array(
						'photo_id' => $this->VenuePhoto->getLastInsertId(),
						'photo_url' => $file_url,
						);

				}
			}

			// @29/09/2014, 037 Upload Promotions
			$promo_ids = array();
			if(!empty($promos)){
				foreach($promos as $key => $promo) {

					$file_loc = 'uploads'.DS.'venues'.DS;
					$file_name = $venue_id.'_' . rand()."_".microtime() . '.jpg';
					$file_name 		= str_replace(" ", '', $file_name);

					$file_dir = WWW_ROOT . $file_loc;
					$file_url = Router::url('/', true) . $file_loc . $file_name;
					$fname = $file_dir . $file_name;

					if(!is_dir($file_dir))
						mkdir($file_dir, 0755, true);

					$raw_file = base64_decode($promo);
					$handle = fopen($fname, "w+");
					fwrite($handle, $raw_file);
					fclose($handle);

					if(!file_exists($fname) || filesize($fname) <= 0) {
						$validationErrors['promos'][$key + 1] = 'File could not be uploaded';
						continue;
					}

					$venue_promo_input = array(
						'venue_id' 	=> $venue_id,
						'user_id' 	=> $user_id,
						'promotion' 	=> $file_loc . $file_name,
						'created' 	=> date('Y-m-d H:i:s')
					);

					$this->VenuePromotion->create();
					if(!$this->VenuePromotion->save($venue_promo_input)) {
						$validationErrors['promos'][$key + 1] = $this->VenuePromotion->validationErrors;
						continue;
					}
					else{
						list($width, $height, $type, $attr) = getimagesize($file_loc . $file_name);
						$source_image = $file_loc . $file_name;
						$destination_image = $file_loc . $file_name;

						$new_compressed_image = $this->Image->generateCompressImagePhoto($source_image, $destination_image);
						$attachmentSize = array('width' => $width, 'height' => $height);

						$new_image = array();
						$new_image['Image']['name']['name'] 	= $file_name;
						$new_image['Image']['name']['type'] 	= "image/jpg";
						$new_image['Image']['name']['tmp_name'] = $source_image;
						$new_image['Image']['name']['error'] 	= "";
						$new_image['Image']['name']['size'] 	= $attachmentSize;

						$image_path_large = $this->Image->upload_image_and_thumbnail($new_image,"name",500,600,true, "_large");
						$image_path_thumb = $this->Image->upload_image_and_thumbnail($new_image,"name",161,161,true, "_thumb");

					}
					$promo_ids[] = array(
						'promo_id' => $this->VenuePromotion->getLastInsertId(),
						'promo_url' => $file_url,
						);
				}
			}

			// @29/09/2014, 037 Upload Floor Plans
			$floor_plan_ids = array();
			if(!empty($floor_plans)){
				foreach($floor_plans as $key => $floor_plan) {

					$file_loc = 'uploads'.DS.'venues'.DS;
					$file_name = $venue_id.'_' . rand()."_".microtime() . '.jpg';
					$file_name 		= str_replace(" ", '', $file_name);

					$file_dir = WWW_ROOT . $file_loc;
					$file_url = Router::url('/', true) . $file_loc . $file_name;
					$fname = $file_dir . $file_name;

					if(!is_dir($file_dir))
						mkdir($file_dir, 0755, true);

					$raw_file = base64_decode($floor_plan);
					$handle = fopen($fname, "w+");
					fwrite($handle, $raw_file);
					fclose($handle);

					if(!file_exists($fname) || filesize($fname) <= 0) {
						$validationErrors['floor_plans'][$key + 1] = 'File could not be uploaded';
						continue;
					}

					$venue_floor_plan_input = array(
						'venue_id' 	=> $venue_id,
						'user_id' 	=> $user_id,
						'floor_plan' 	=> $file_loc . $file_name,
						'created' 	=> date('Y-m-d H:i:s')
					);

					$this->VenueFloorPlan->create();
					if(!$this->VenueFloorPlan->save($venue_floor_plan_input)) {
						$validationErrors['floor_plans'][$key + 1] = $this->VenueFloorPlan->validationErrors;
						continue;
					}
					else{
						list($width, $height, $type, $attr) = getimagesize($file_loc . $file_name);
						$source_image = $file_loc . $file_name;
						$destination_image = $file_loc . $file_name;

						$new_compressed_image = $this->Image->generateCompressImagePhoto($source_image, $destination_image);
						$attachmentSize = array('width' => $width, 'height' => $height);

						$new_image = array();
						$new_image['Image']['name']['name'] 	= $file_name;
						$new_image['Image']['name']['type'] 	= "image/jpg";
						$new_image['Image']['name']['tmp_name'] = $source_image;
						$new_image['Image']['name']['error'] 	= "";
						$new_image['Image']['name']['size'] 	= $attachmentSize;

						$image_path_large = $this->Image->upload_image_and_thumbnail($new_image,"name",500,600,true, "_large");
						$image_path_thumb = $this->Image->upload_image_and_thumbnail($new_image,"name",161,161,true, "_thumb");

					}
					$floor_plan_ids[] = array(
						'floor_plan_id' => $this->VenueFloorPlan->getLastInsertId(),
						'floor_plan_url' => $file_url,
						);

				}
			}


			if(!empty($validationErrors)) {
				// Rollback transaction
				$this->Venue->rollback();

				$response = array('status' => 'error', 'operation' => 'save_venue_profile', 'validationErrors' => $validationErrors);
				$this->set(array('response' => $response, '_serialize' => array('response')));
				return;
			}

			$this->request->data['photos'] = array();
			$this->request->data['promos'] = array();
			$this->request->data['floor_plans'] = array();



			// @09/09/2014 by 037, Upload Venue logo
			if(!empty($logo) && isset($logo)) {
				$file_loc = 'uploads'.DS.'logos'.DS;
				$file_name = $venue_id.'_' . rand()."_".microtime() . '.jpg';
				$file_name 		= str_replace(" ", '', $file_name);

				$file_dir = WWW_ROOT . $file_loc;
				$file_url = Router::url('/', true) . $file_loc . $file_name;
				$fname = $file_dir . $file_name;

				if(!is_dir($file_dir))
					mkdir($file_dir, 0755, true);

				$raw_file = base64_decode($logo);
				$handle = fopen($fname, "w+");
				fwrite($handle, $raw_file);
				fclose($handle);

				if(!file_exists($fname) || filesize($fname) <= 0)
					$validationErrors['logo'] = 'File could not be uploaded';


				if(!empty($validationErrors)) {
					// Rollback transaction
					$this->Venue->rollback();

					$response = array('status' => 'error', 'operation' => 'save_venue_profile', 'validationErrors' => $validationErrors);
					$this->set(array('response' => $response, '_serialize' => array('response')));
					return;
				}

				$data = array(
					'id' => $venue_id,
					'logo' => $file_loc . $file_name,
				);

				if(!$this->Venue->save($data, $validate = false)) {
					// Rollback transaction
					$this->Venue->rollback();

					$response = array('status' => 'error', 'operation' => 'save_venue_profile',	'dataErrors' => $this->Venue->validationErrors);
					$this->set(array('response' => $response, '_serialize' => array('response')));
					return;
				}
			}

		}



		// Make entry in the regular venue_days_time
		foreach($weekday_from as $key => $start_from) {
			$end_to = $weekday_to[$key];
			$time_from = $weekday_time_from[$key];
			$time_to = $weekday_time_to[$key];

			$input_day_time = array(
				'venue_id'	=> $venue_id,
				'from_day'	=> $start_from,
				'to_day'	=> $end_to,
				'from_time'	=> $time_from,
				'to_time'	=> $time_to,
				'is_happy_hours' => 'n',
				'created'	=> $today,
				'updated'	=> $today,
				);

			$this->VenueDaysTime->create();
			$this->VenueDaysTime->save($input_day_time);
		}


		// Make entry for the happy hours timing of venue_days_time
		if(!empty($happy_hour_from) && !empty($happy_hour_to) && !empty($happy_hour_time_from) && !empty($happy_hour_time_to)) {

			foreach($happy_hour_from as $key => $hour_from) {
				$hour_to = $happy_hour_to[$key];
				$hour_time_from = $happy_hour_time_from[$key];
				$hour_time_to = $happy_hour_time_to[$key];

				$input_day_time = array(
					'venue_id'	=> $venue_id,
					'from_day'	=> $hour_from,
					'to_day'	=> $hour_to,
					'from_time'	=> $hour_time_from,
					'to_time'	=> $hour_time_to,
					'is_happy_hours' => 'y',
					'created'	=> $today,
					'updated'	=> $today,
				);

				$this->VenueDaysTime->create();
				$this->VenueDaysTime->save($input_day_time);
			}

		}

		if(!empty($area_id)) {
			// Make entry for venue areas
			$area_input = array(
				'venue_id'	=> $venue_id,
				'area_id'	=> $area_id,
				);
			$this->AreasVenue->save($area_input);

		}

		// Commit transaction
		$this->Venue->commit();


		

		$response = array(			
			'status' => 'success',
			'operation' => 'save_venue_profile',
			'promo_ids' => $promo_ids,
			'floor_plan_ids' => $floor_plan_ids,
			'photo_ids' => $photo_ids,
			'is_new_user' => $is_new_user,
			'data' => array('msg' => 'Changes has been saved successfully', 'id' => $venue_id, 'assigned_user_id' => $assigned_user_id_val,'data' => $this->data),
			);
		$this->set(array('response' => $response, '_serialize' => array('response')));
	}




	/**
	 ** @purpose upload venue logo
	 ** @input auth_type, venue_id, logo
	 ** @output Array of data
	 */
	public function upload_venue_logo() {
		$auth_type = $this->getParams('auth_type', 'web');
		$venue_id = $this->getParams('venue_id');
		$logo = $this->getParams('logo');


		$validationErrors = array();
		if(empty($venue_id))
			$validationErrors['venue_id'] = 'Venue is not selected';
		/*if(empty($logo))
			$validationErrors['logo'] = 'Logo is not selected';*/

		if(empty($validationErrors)) {
			// Check if the venue is available or not
			$venueExists = $this->Venue->findByid($venue_id);
			if(empty($venueExists))
				$validationErrors['venue_id'] = 'Venue does not exists';
			else if(!empty($venueExists['Venue']['logo']))
				$validationErrors['logo'] = 'Venue logo is already available. Remove it, and then try again.';
  		        else if(empty($logo))
				$validationErrors['logo'] = 'Logo is not selected';
		}
		if(!empty($validationErrors)) {
			$response = array('status' => 'error', 'operation' => 'upload_venue_logo', 'validationErrors' => $validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}

		// Validate uploaded venue logo
		if(empty($validationErrors)) {
			$file_loc = 'uploads'.DS.'logos'.DS;
			$file_name = $venue_id.'_' . rand()."_".microtime() . '.jpg';
			$file_name 		= str_replace(" ", '', $file_name);

			$file_dir = WWW_ROOT . $file_loc;
			$file_url = Router::url('/', true) . $file_loc . $file_name;
			$fname = $file_dir . $file_name;

			if(!is_dir($file_dir))
				mkdir($file_dir, 0755, true);

			$raw_file = base64_decode($logo);
			$handle = fopen($fname, "w+");
			fwrite($handle, $raw_file);
			fclose($handle);

			if(!file_exists($fname) || filesize($fname) <= 0)
				$validationErrors['logo'] = 'File could not be uploaded';
		}

		if(!empty($validationErrors)) {
			$response = array('status' => 'error', 'operation' => 'upload_venue_logo', 'validationErrors' => $validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}

		// Save Data
		$this->Venue->id = $venue_id;
		$data = array(
			'id' 	=> $venue_id,
			'logo' 	=> $file_loc . $file_name,
			);

		if(!$this->Venue->save($data, $validate = false)) {
			$response = array('status' => 'error', 'operation' => 'upload_venue_logo', 'dataErrors' => $this->Venue->validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}

		// SuccessResponse
		$response = array(
			'status' => 'success',
			'operation' => 'upload_venue_logo',
			'data' => array('msg' => 'Venue logo uploaded successfully', 'logo_url' => $file_url),
			);
		$this->set(array('response' => $response, '_serialize' => array('response')));
	}


	/**
	 ** @purpose upload venue photos
	 ** @input auth_type, auth_key, venue_id, photos (array)
	 ** @output Array of data
	 */
	public function upload_photos() {
		$auth_type = $this->getParams('auth_type', 'web');
		$auth_key = $this->getParams('auth_key');
		$venue_id = $this->getParams('venue_id');
		$photos = $this->getParams('photos', array());


		// Check If Empty
		$validationErrors = array();
		if(empty($auth_key))
			$validationErrors['auth_key'] = 'Auth key is blank';
		else {
			$user_id = $this->User->get_user_id($auth_key);
			if(empty($user_id))
				$validationErrors['auth_key'] = 'Invalid auth key';
		}
		if(empty($venue_id))
			$validationErrors['venue_id'] = 'Venue is not selected';

		if(empty($validationErrors)) {
			// Check if the venue is available or not
			$venueExists = $this->Venue->findByid($venue_id);
			if(empty($venueExists))
				$validationErrors['venue_id'] = 'Venue does not exists';
		}

		if(empty($photos))
			$validationErrors['photos'] = 'Photos are not selected';
		else {
			foreach($photos as $key => $photo) {
				if(empty($photo)) {
					$validationErrors['photos'][$key + 1] = 'Photo is not selected';
				}
			}
		}

		if(!empty($validationErrors)) {
			$response = array('status' => 'error', 'operation' => 'upload_photos', 'validationErrors' => $validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}

		$photo_ids = array();
		foreach($photos as $key => $photo) {
			$file_loc = 'uploads'.DS.'venues'.DS;
			$file_name = $venue_id.'_' . rand()."_".microtime() . '.jpg';
			$file_name 		= str_replace(" ", '', $file_name);

			$file_dir = WWW_ROOT . $file_loc;
			$file_url = Router::url('/', true) . $file_loc . $file_name;
			$fname = $file_dir . $file_name;

			if(!is_dir($file_dir))
				mkdir($file_dir, 0755, true);

			$raw_file = base64_decode($photo);
			$handle = fopen($fname, "w+");
			fwrite($handle, $raw_file);
			fclose($handle);

			if(!file_exists($fname) || filesize($fname) <= 0) {
				$validationErrors['photos'][$key + 1] = 'File could not be uploaded';
				continue;
			}

			$venue_photo_input = array(
				'venue_id' 	=> $venue_id,
				'user_id' 	=> $user_id,
				'photo' 	=> $file_loc . $file_name,
				'created' 	=> date('Y-m-d H:i:s')
			);

			$this->VenuePhoto->create();
			if(!$this->VenuePhoto->save($venue_photo_input)) {
				$validationErrors['photos'][$key + 1] = $this->VenuePhoto->validationErrors;
				continue;
			}
			else{
					list($width, $height, $type, $attr) = getimagesize($file_dir . $file_name);
					$source_image = $file_loc . $file_name;
					$destination_image = $file_loc . $file_name;

					$new_compressed_image = $this->Image->generateCompressImagePhoto($source_image, $destination_image);
					$attachmentSize = array('width' => $width, 'height' => $height);

					$new_image = array();
					$new_image['Image']['name']['name'] 	= $file_name;
					$new_image['Image']['name']['type'] 	= "image/jpg";
					$new_image['Image']['name']['tmp_name'] = $source_image;
					$new_image['Image']['name']['error'] 	= "";
					$new_image['Image']['name']['size'] 	= $attachmentSize;

					$image_path_large = $this->Image->upload_image_and_thumbnail($new_image,"name",500,600,true, "_large");
					$image_path_thumb = $this->Image->upload_image_and_thumbnail($new_image,"name",161,161,true, "_thumb");

				}

			$photo_ids[] = array(
				'photo_id' => $this->VenuePhoto->getLastInsertId(),
				'photo_url' => $file_url,
				);
		}

		if(!empty($validationErrors)) {
			$response = array('status' => 'error', 'operation' => 'upload_photos', 'dataErrors' => $validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}


		// SuccessResponse
		$response = array(
			'status' => 'success',
			'operation' => 'upload_photos',
			'data' => array('msg' => 'Photos uploaded successfully', 'photo_ids' => $photo_ids),
			);
		$this->set(array(
			'response' => $response,
			'_serialize' => array('response'),
		));
	}



	/**
	 ** @purpose delete the venue logo
	 ** @input auth_type, venue_id
	 ** @output
	 */
	public function delete_logo() {
		$venue_id = $this->getParams('venue_id');

		$validationErrors = array();
		if(empty($venue_id))
			$validationErrors['venue_id'] = 'Venue is not selected';
		else {
			// Check if the venue is available or not
			$venueExists = $this->Venue->findByid($venue_id);
			if(empty($venueExists))
				$validationErrors['venue_id'] = 'Venue does not exists';
		}

		if(!empty($validationErrors)) {
			$response = array('status' => 'error', 'operation' => 'delete_logo', 'validationErrors' => $validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}


		$venue_input = array(
			'id' 	=> $venue_id,
			'logo'	=> '',
			);
		if(!$this->Venue->save($venue_input, $validate = false)) {
			$response = array('status' => 'error', 'operation' => 'delete_logo', 'dataErrors' => $this->Venue->validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}


		// Unlink the venue logo
		$venue_logo = $venueExists['Venue']['logo'];
		$venue_path = WWW_ROOT . DS . $venue_logo;
		@unlink($venue_path);


		// SuccessResponse
		$response = array(
			'status' => 'success',
			'operation' => 'delete_logo',
			'data' => array('msg' => 'Venue logo has been deleted successfully', 'data' => $this->data),
			);
		$this->set(array('response' => $response, '_serialize' => array('response')));
	}

	/**
	 ** @purpose delete the venue photo
	 ** @input auth_type, venue_id, photo_id
	 ** @output
	 */
	public function delete_photos() {
		$venue_id = $this->getParams('venue_id');
		$photo_id = $this->getParams('photo_id');

		$validationErrors = array();
		if(empty($venue_id))
			$validationErrors['venue_id'] = 'Venue is not selected';
		else {
			// Check if the venue is available or not
			$venueExists = $this->Venue->findByid($venue_id);
			if(empty($venueExists))
				$validationErrors['venue_id'] = 'Venue does not exists';
		}
		if(empty($photo_id))
			$validationErrors['photo_id'] = 'Venue photo is not selected';
		else {
			// Check if the venue photo is available or not
			$options = array('conditions' => array('VenuePhoto.venue_id' => $venue_id, 'VenuePhoto.id' => $photo_id));
			$venuePhotoExists = $this->VenuePhoto->find('first', $options);
			if(empty($venuePhotoExists))
				$validationErrors['photo_id'] = 'Venue photo does not exists';
		}

		if(!empty($validationErrors)) {
			$response = array('status' => 'error', 'operation' => 'delete_photos', 'validationErrors' => $validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}


		if(!$this->VenuePhoto->delete($photo_id)) {
			$response = array('status' => 'error', 'operation' => 'delete_photos', 'dataErrors' => $this->VenuePhoto->validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}


		// Unlink the venue logo
		$venue_photo = $venuePhotoExists['VenuePhoto']['photo'];
		$venue_path = WWW_ROOT . DS . $venue_photo;

		$path_parts 			= pathinfo($venue_photo);
		$venuePhotoLarge 		= WWW_ROOT . DS . $path_parts['dirname'].'/'.$path_parts['filename'].'_large.'.$path_parts['extension'];
		$venuePhotoThumb 		= WWW_ROOT . DS . $path_parts['dirname'].'/'.$path_parts['filename'].'_thumb.'.$path_parts['extension'];
		@unlink($venuePhotoLarge);
		@unlink($venuePhotoThumb);
		@unlink($venue_path);


		// SuccessResponse
		$response = array(
			'status' => 'success',
			'operation' => 'delete_photos',
			'data' => array('msg' => 'Venue photo has been deleted successfully', 'data' => $this->data),
			);
		$this->set(array('response' => $response, '_serialize' => array('response')));
	}


	/**
	 ** @purpose delete the venue
	 ** @input auth_type, venue_id
	 ** @output
	 */
	public function delete_venue() {
		$venue_id = $this->getParams('venue_id');
		$photos = $this->VenuePhoto->find('all', array('conditions' => array('venue_id' => $venue_id)));


		$validationErrors = array();
		if(empty($venue_id))
			$validationErrors['venue_id'] = 'Venue is not selected';
		else {
			// Check if the venue is available or not
			$venueExists = $this->Venue->findByid($venue_id);
			if(empty($venueExists))
				$validationErrors['venue_id'] = 'Venue does not exists';
		}

		if(!empty($validationErrors)) {
			$response = array('status' => 'error', 'operation' => 'delete_logo', 'validationErrors' => $validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}

		// Begin transaction
		$this->Venue->begin();


		/*// Delete all the venue categories association
		if(!$this->CategoriesVenue->deleteAll($conditions = array('CategoriesVenue.venue_id' => $venue_id))) {
			// Rollback transaction
			$this->Venue->rollback();

			$response = array('status' => 'error', 'operation' => 'delete_venue', 'dataErrors' => $this->CategoriesVenue->validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}

		// Delete venue days time association
		if(!$this->VenueDaysTime->delete($conditions = array('VenueDaysTime.venue_id' => $venue_id))) {
			// Rollback transaction
			$this->Venue->rollback();

			$response = array('status' => 'error', 'operation' => 'delete_venue', 'dataErrors' => $this->VenueDaysTime->validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			//return;
		}



		// Delete all the venue photos
		if(!$this->VenuePhoto->deleteAll($conditions = array('VenuePhoto.venue_id' => $venue_id))) {
			// Rollback transaction
			$this->Venue->rollback();

			$response = array('status' => 'error', 'operation' => 'delete_venue', 'dataErrors' => $this->VenuePhoto->validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}

		//@29/09/2014 by 037,
		// Delete all the venue promotions
		if(!$this->VenuePromotion->deleteAll($conditions = array('VenuePromotion.venue_id' => $venue_id))) {
			// Rollback transaction
			$this->Venue->rollback();

			$response = array('status' => 'error', 'operation' => 'delete_venue', 'dataErrors' => $this->VenuePromotion->validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}


		//@29/09/2014 by 037,
		// Delete all the venue floor plans
		if(!$this->VenueFloorPlan->deleteAll($conditions = array('VenueFloorPlan.venue_id' => $venue_id))) {
			// Rollback transaction
			$this->Venue->rollback();

			$response = array('status' => 'error', 'operation' => 'delete_venue', 'dataErrors' => $this->VenueFloorPlan->validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}

		// Get the list of all the venue photos
		// Delete the venue information
		if(!$this->Venue->delete($venue_id)) {
			// Rollback transaction
			$this->Venue->rollback();

			$response = array('status' => 'error', 'operation' => 'delete_venue', 'dataErrors' => $this->Venue->validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}


		// Delete all the venue directory photos
		//$venue_photos_path = WWW_ROOT . 'uploads/venues/' . $venue_id;
		//$this->recursive_remove_directory($venue_photos_path);


		// Unlink venue images if any
		if(!empty($photos)) {
			foreach($photos as $event_photo) {
				$photo_path = WWW_ROOT . DS . $event_photo['VenuePhoto']['photo'];
				$path_parts 			= pathinfo($event_photo['VenuePhoto']['photo']);
				$venuePhotoLarge 		= WWW_ROOT . DS . $path_parts['dirname'].'/'.$path_parts['filename'].'_large.'.$path_parts['extension'];
				$venuePhotoThumb 		= WWW_ROOT . DS . $path_parts['dirname'].'/'.$path_parts['filename'].'_thumb.'.$path_parts['extension'];
				@unlink($venuePhotoLarge);
				@unlink($venuePhotoThumb);
				@unlink($photo_path);
			}
		}*/

         	## dont delete data related to venue.. just only make the venue_status as 2
		$venue_input = array(
			'venue_status' => '0',
		);
		$venue_input['id'] = $venue_id;

		// Make entry in the venues table
		if(!$this->Venue->save($venue_input, $validate = false)) {
			// Rollback transaction
			$this->Venue->rollback();
			$response = array('status' => 'error', 'operation' => 'delete_venue', 'data' => array('msg' => 'Error while deleting venue.', 'data' => $this->data),);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}
		##


		// Commit transaction
		$this->Venue->commit();


		// SuccessResponse
		$response = array(
			'status' => 'success',
			'operation' => 'delete_venue',
			'data' => array('msg' => 'Venue has been deleted successfully', 'data' => $this->data),
			);
		$this->set(array('response' => $response, '_serialize' => array('response')));
	}

	public function get_venue_url($venue_id, $category_id) {
		$where = "where v.id='".$venue_id."'";
		if(!empty($category_id))
			$where .= " AND cv.category_id='".$category_id."'";
		$sql = "SELECT c.alias, c.parent FROM venues v left join categories_venues cv on v.id=cv.venue_id left join categories c on cv.category_id=c.id ".$where;
		$result = $this->Venue->query($sql);

		$parent_cat = "";
		if(isset($result[0]['c']['parent']) && $result[0]['c']['parent'] != '0') {
			$parent_id = $result[0]['c']['parent'];
			$sql = "select alias FROM categories c WHERE id='".$parent_id."'";
			$cat_result = $this->Venue->query($sql);
			if(isset($cat_result[0]['c']['alias']) && $cat_result[0]['c']['alias'] != '') {
				$parent_cat = $cat_result[0]['c']['alias'];
				$parent_cat .= DS;
			}
		}
		$venue_url = SITE_URL.DS;

		if(isset($result[0]['c']['alias'])) {
			$venue_url .= $parent_cat.$result[0]['c']['alias'].DS.'?id='.$venue_id;

		}

		return $venue_url;
		//print_r($result);	exit;
	}

	/**
	 ** @purpose get the list of venues
	 ** @input auth_type, category_id, sort, direction, page, limit, feature_ids, area_ids, zip_code, distance, assigned_user_id(optional)
	 ** @output Array list of venues
	 */
	public function get_venues_list() {
		$this->UserFavoriteVenue = ClassRegistry::init('UserFavoriteVenue');

		$auth_type = $this->getParams('auth_type', 'web');
		$category_id = $this->getParams('category_id');
		$user_id = $this->getParams('user_id');
		$keyword = $this->getParams('keyword');
		$city_name = ''; //$this->getParams('city_name');
		$state = ''; //$this->getParams('state');

		$sort = $this->getParams('sort', 'Venue.updated');
		$direction = $this->getParams('direction', 'DESC');
		$page = $this->getParams('page', 1);
		$limit = $this->getParams('limit', 15);

		$feature_ids = $this->getParams('feature_ids', array());
		$area_ids = $this->getParams('area_ids', array());

		$zip_code = $this->getParams('zip_code');
		$distance = $this->getParams('distance');
		$venue_req_id = $this->getParams('request_id');

		$assigned_user_id = $this->getParams('assigned_user_id');

		$auth_key = $this->getParams('auth_key');
		$loggedin_user_id = $this->User->get_user_id($auth_key);

		/******** this variable we have used to make the draft functionality *******************/
		$type = $this->getParams('type');

		// Get latitude and longitude related information
		$current_lat_deg = $this->getParams('latitude', '');
		$current_lon_deg = $this->getParams('longitude', '');
		$radians_to_degs = 57.2957795;
		$earths_radius 	 = 6371;

		//$latitude 	= !empty($current_lat_deg) ? ($current_lat_deg / $radians_to_degs) : '';
		//$longitude 	= !empty($current_lon_deg) ? ($current_lon_deg / $radians_to_degs) : '';

		$latitude  = $current_lat_deg;
		$longitude = $current_lon_deg;		

		if( !empty($latitude) && !empty($longitude) ) {
			$sort 		= 'ACOS(SIN('.$latitude.' * PI() / 180) * SIN(Venue.lattitude * PI() / 180) + COS('.$latitude.' * PI() / 180) * COS(Venue.lattitude * PI() / 180) * COS((Venue.longitude - ('.$longitude.'))* PI() / 180)) * 60 * 1.1515 ';
			$direction 	= 'ASC';
		}
		
		
		// Get the total count of matching venues

		$joins[] = array(
					'table'	=> 'cities',
					'alias'	=> 'City',
					'conditions' => array(
							'Venue.state = City.state',
							'Venue.city = City.name'
						),
					);
		$options = array('recursive' => '-1', 'group' => array('Venue.id'));

		if(!empty($joins))
			$options['joins'] = $joins;

		$conditions = array();

		if(!empty($state))
			$conditions['OR'] = array(
				'City.state LIKE' => '%'.$state.'%',
				'City.full_state LIKE' => '%'.$state.'%'
			);


		if(!empty($city_name))
			$conditions['City.name LIKE'] = '%'.$city_name.'%';


		if(!empty($conditions))
			$options['conditions'] = $conditions;

		$vcount = $this->Venue->find('count', $options);

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

			$vcount2 = $this->Venue->find('count', $options);

			// If Record not found
			if(empty($vcount2)) {
				$options['conditions'] = array();
				$conditions = array();

				if(!empty($city_name))
					$conditions['City.name LIKE'] = '%'.$city_name.'%';

				if(!empty($conditions))
					$options['conditions'] = $conditions;

				$vcount3 = $this->Venue->find('count', $options);

				if(empty($vcount3)) {
					$options['conditions'] = array();
					$conditions = array();
				}
			}
		}


		if(!empty($user_id)) {
			$conditions['Venue.user_id'] = $user_id;
		}

		if(!empty($assigned_user_id)) {
			$conditions['Venue.assigned_user_id'] = $assigned_user_id;
		}

		if(!empty($type)) {
			$conditions['Venue.venue_type'] = $type;
		}
		$conditions['Venue.venue_status'] = 1;
		
		if(!empty($category_id)) {
		$joins2[] = array(
				'table'	=> 'categories_venues',
				'alias'	=> 'CategoryVenue',
				'conditions' => array('Venue.id = CategoryVenue.venue_id'),
				);
		$joins = array_merge($joins, $joins2);

		
		$conditions['CategoryVenue.category_id'] = $category_id;
		}

		if(!empty($keyword)) {

			$conditions['OR'] = array(
				'Venue.name LIKE ' => "%".$keyword."%",
				'Venue.contact_person LIKE ' => "%".$keyword."%",
				'Venue.email LIKE ' => "%".$keyword."%",
				'Venue.phone1 LIKE ' => "%".$keyword."%",
				'Venue.city LIKE ' => "%".$keyword."%",
				'Venue.zip LIKE ' => "%".$keyword."%",
				'Venue.state LIKE ' => "%".$keyword."%",
				'Venue.country LIKE ' => "%".$keyword."%",
				'Venue.address LIKE ' => "%".$keyword."%",
				'Venue.area LIKE ' => "%".$keyword."%",
				'Venue.sub_area LIKE ' => "%".$keyword."%",
				);

			# In case of search result should be alphabetically sorted
			//$sort = "Venue.name";
			//$direction = "ASC";
		}

		if(!empty($venue_req_id)) {
			$conditions['Venue.id'] = $venue_req_id;
		}

		######		Advance search filter: feature_ids, area_ids, zip_code, distance (in miles)		#####

		# In case of iPhone and Android areas are comming comma seperated
		if(!is_array($feature_ids)) {
			$feature_ids = explode(",", $feature_ids);
			$feature_ids = array_map('trim', $feature_ids);
		}

		$venue_ids = array();
		if(!empty($feature_ids)) {
			$this->FeaturesVenue = ClassRegistry::init('FeaturesVenue');
			$fvconditions = array('FeaturesVenue.feature_id' => $feature_ids[0]);
			if(count($feature_ids) > 1)
				$fvconditions = array('FeaturesVenue.feature_id IN ' => $feature_ids);
			$fvoptions = array(
				'recursive' => '-1',
				'conditions' => $fvconditions,
				'fields' => array('FeaturesVenue.venue_id'),
				'group' => 'FeaturesVenue.venue_id',
				);
			$featuredVenues = $this->FeaturesVenue->find('list', $fvoptions);
			if(!empty($featuredVenues))
				$venue_ids = array_values($featuredVenues);
		}

		# In case of iPhone and Android areas are comming comma seperated
		if(!is_array($area_ids)) {
			$area_ids = explode(",", $area_ids);
			$area_ids = array_map('trim', $area_ids);
		}

		#@14/01/2015 by 037, 25874, Shoutoutcity > make some API change for CIty Listing (Location setting page)
		$area_names = array();

		if(!empty($area_ids)) {

			for($k=0; $k<count($area_ids); $k++) {
				$sql = "SELECT `name` FROM areas WHERE id='".$area_ids[$k]."'";
				$array_area = $this->AreasVenue->query($sql);
				if(isset($array_area[0]['areas']['name']) && !empty($array_area[0]['areas']['name']) ) {
					$temp_area = array(
						'LOWER(Venue.sub_area) LIKE' => "%".strtolower($array_area[0]['areas']['name'])."%",
					);
					$area_names[] = $temp_area;


				}
			}
			/*
			$this->AreasVenue = ClassRegistry::init('AreasVenue');
			$avconditions = array('AreasVenue.area_id' => $area_ids[0]);
			if(count($area_ids) > 1)
				$avconditions = array('AreasVenue.area_id IN ' => $area_ids);
			$avoptions = array(
				'recursive' => '-1',
				'conditions' => $avconditions,
				'fields' => array('AreasVenue.venue_id'),
				'group' => 'AreasVenue.venue_id',
				);
			$areaVenues = $this->AreasVenue->find('list', $avoptions);
			if(!empty($areaVenues))
				$venue_ids = array_merge($venue_ids, array_values($areaVenues));
			*/
		}
		//print_r(array_values($areaVenues)); exit;
		if(!empty($area_names)) {
			$conditions['OR'] = $area_names;
		}
		if(!empty($venue_ids)) {
			$conditions['Venue.id'] = $venue_ids;
			if(count($venue_ids) > 1) {
				$conditions['Venue.id IN '] = $venue_ids;
			}
		}

	/*	else if(!empty($feature_ids) || !empty($area_ids)) {	// If the user has requested filter results
			$conditions['Venue.id'] = 'NO_RECORDS';
		} */

		if(!empty($zip_code)) {
			$conditions['Venue.zip'] = $zip_code;
		}

		################


		if(!empty($joins))
			$options['joins'] = $joins;

		if(!empty($conditions))
			$options['conditions'] = $conditions;

		$total_venues = $this->Venue->find('count', $options);

		if($auth_type == "web_dropdown"){
			// Get pagination related information
			$paginate_cond = array(
				'total_records'	=> intval($total_venues),
				'page_num'	=> $page,
				'per_page'	=> '',
				);
			$pagination = get_pagination_stats($paginate_cond);
			}
		else{
			// Get pagination related information
			$paginate_cond = array(
				'total_records'	=> intval($total_venues),
				'page_num'	=> $page,
				'per_page'	=> $limit,
				);
			$pagination = get_pagination_stats($paginate_cond);
		}

		if($auth_type == "web_dropdown"){
		// Get the list of venues
		$options = array(
			'recursive' => '-1',
			'page'	=> $page,
			'limit'	=> '',
			'group' => array('Venue.id'),
			'order'	=> array($sort => $direction),
			'fields' => array('Venue.id', 'Venue.name', 'Venue.contact_person', 'Venue.address', 'Venue.state', 'Venue.city', 'Venue.zip', 'Venue.country', 'Venue.phone1 AS phone','Venue.phone2 AS mobile', 'Venue.email', 'Venue.avg_rating', 'Venue.description', 'Venue.details', 'Venue.logo as image', 'Venue.website', 'Venue.lattitude', 'Venue.longitude', 'Venue.video'),
			);
		}
		else {
		// Get the list of venues
		$options = array(
			'recursive' => '-1',
			'page'	=> $page,
			'limit'	=> $limit,
			'group' => array('Venue.id'),
			'order'	=> array($sort => $direction, 'Venue.prefered' => 'ASC'),
			'fields' => array('Venue.id', 'Venue.name', 'Venue.contact_person', 'Venue.address', 'Venue.state', 'Venue.city', 'Venue.zip', 'Venue.country', 'Venue.phone1 AS phone','Venue.phone2 AS mobile', 'Venue.email', 'Venue.avg_rating', 'Venue.description', 'Venue.details', 'Venue.logo as image', 'Venue.website', 'Venue.lattitude', 'Venue.longitude', 'Venue.video'),
			);
		}


		if(!empty($conditions))
			$options['conditions'] = $conditions;

		if(!empty($joins))
			$options['joins'] = $joins;
		$result = $this->Venue->find('all', $options);

		/*
		ob_start();
		$query = $this->Venue->getDataSource()->getLog();
		echo $query;
		print_r($query);
		$b = ob_get_contents();
		ob_end_clean();
		mail("payal@srs-infosystems.com", "23", $b);
		return $query;
		*/
        
		$venues = array();
		if(!empty($result)) {
			foreach($result as $row) {
				$venue_id = $row['Venue']['id'];
				$avg_ratings = $row['Venue']['avg_rating'];
				$cat_id = $row['Venue']['category_id'];
				$total_reviews = $this->VenueReview->get_total_reviews_count($venue_id);
                ##calculate total favs #79
				$sql1 = "SELECT venue_id FROM `user_favorite_venues` WHERE `venue_id` = '".$venue_id."'";
				$total_fav = $this->UserFavoriteVenue->query($sql1);
				$row['Venue']['total_favorite'] = !empty($total_fav)?count($total_fav):'0';
				## 
				$row['Venue']['is_favorite'] = $this->UserFavoriteVenue->is_user_venue_favorite($loggedin_user_id, $venue_id);
				if(empty($row['Venue']['contact_person']) || $row['Venue']['contact_person'] == null)
					$row['Venue']['contact_person'] = 'NA';
				//$row['Venue']['contact_email'] = $row['Venue']['email'];
				//$row['Venue']['contact_email'] = 'xyz@gmail.com';
				$row['Venue']['user_rating'] = $this->VenueReview->get_user_venue_rating_value($loggedin_user_id, $venue_id);
           
				if($row['Venue']['image'] == NULL || $row['Venue']['image'] == null)
					$row['Venue']['image'] = "";
				else {
					$thumb_imageName 	= Router::url('/', true).$row['Venue']['image'];
					if(!file_exists(WWW_ROOT.$row['Venue']['image'])){
						$thumb_imageName 	= Router::url('/', true)."img/default/NAthumb.png";
					}
					$row['Venue']['image'] = $thumb_imageName;
				}

				$row['Venue']['total_reviews'] = $total_reviews;
				$venue_url = $this->get_venue_url($venue_id, $cat_id);
				if($auth_type == 'android')
					$row['Venue']['url'] = $venue_url;
				else
					$row['Venue']['venue_url'] = $venue_url;
				if($auth_type == 'web') {

					// Get the venue photos list
					$row['Venue']['venue_photos'] = $this->get_venue_photos($venue_id);
					$row['Venue']['venue_promotions'] = $this->get_venue_promos($venue_id);
					$row['Venue']['venue_floor_plans'] = $this->get_venue_floor_plans($venue_id);

					/*$voptions = array('recursive' => '-1', 'conditions' => array('venue_id' => $venue_id));
					$vphotos = $this->VenuePhoto->find('all', $voptions);
					$venue_photos = array();
					if(!empty($vphotos)) {
						$base_url = Router::url('/', true);
						foreach($vphotos as $vrow) {
							$row_data = array(
								'id'	=> $vrow['VenuePhoto']['id'],
								'photo'	=> $base_url . $vrow['VenuePhoto']['photo']
								);
							$venue_photos[] = $row_data;
						}
					}
					$row['Venue']['venue_photos'] = $venue_photos;*/


					// Get the venue day time
					$doptions = array('recursive' => '-1', 'conditions' => array('venue_id' => $venue_id));
					$vDayTime = $this->VenueDaysTime->find('all', $doptions);
					$venue_days_time = array('regular_hours' => array(), 'happy_hours' => array());
					if(!empty($vDayTime)) {
						foreach($vDayTime as $vdrow) {
							$drow = $vdrow['VenueDaysTime'];
							$is_happy_hours = $drow['is_happy_hours'];

							$row_data = array(
								'id' 		=> $drow['id'],
								'from_day'	=> $drow['from_day'],
								'to_day'	=> $drow['to_day'],
								'from_time'	=> $drow['from_time'],
								'to_time'	=> $drow['to_time'],
								);

							if($is_happy_hours == 'y')
								$venue_days_time['happy_hours'][] = $row_data;
							else
								$venue_days_time['regular_hours'][] = $row_data;
						}
					}
					$row['Venue']['venue_days_time'] = $venue_days_time;

				}

				$venues[] = $row['Venue'];
			}
		}

		//'type' => $type,
		$response = array(
			'status' => 'success',
			'operation' => 'get_venues_list',
			'pagination' => $pagination,
			'venues' => $venues,
			'sort' => $sort,
			'direction' => $direction
			);
		$this->set(array('response' => $response, '_serialize' => array('response')));
	}

	/**
	 ** @purpose get the venue details
	 ** @input auth_type (android/iphone/web), venue_id
	 ** @output Array of venue detail
	 */
	public function get_venue_details() {
		$this->UserFavoriteVenue = ClassRegistry::init('UserFavoriteVenue');


		$auth_type = $this->getParams('auth_type', 'web');
		$venue_id = $this->getParams('venue_id');

		$auth_key = $this->getParams('auth_key');
		$category_id = $this->getParams('category_id');
		$loggedin_user_id = $user_id = $this->User->get_user_id($auth_key);

		$validationErrors = array();
		if(empty($venue_id))
			$validationErrors['venue_id'] = 'Venue is not selected';
		else {
			// Check if the venue is available or not
			$options = array(
				'fields' => array('Venue.id', 'Venue.name', 'Venue.contact_person', 'Venue.address', 'Venue.state', 'Venue.city', 'Venue.zip', 'Venue.phone1 AS phone', 'Venue.phone2 AS mobile', 'Venue.email', 'Venue.avg_rating', 'Venue.prefered', 'Venue.description', 'Venue.logo', 'Venue.details', 'Venue.promotions', 'Venue.website', 'Venue.venue_type', 'Venue.custom_feature', 'Venue.city_id', 'Venue.assigned_user_id', 'Venue.is_new_user', 'Venue.venue_status', 'Venue.lattitude', 'Venue.longitude', 'Venue.video'),
				'conditions' => array('Venue.id' => $venue_id),
				);
			$venueExists = $this->Venue->find('first', $options);
			//print_r($venueExists); exit;
			if(empty($venueExists))
				$validationErrors['venue_id'] = 'Venue does not exists';
		}

		
		if(!empty($validationErrors)) {
			$response = array('status' => 'error', 'operation' => 'get_venue_details', 'validationErrors' => $validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}

		if($venueExists['Venue']['venue_status'] == 0)
			$validationErrors['venue_not_exists'] = 'Venue is not exists';



		$venue_data = $venueExists['Venue'];


		if($venueExists['Venue']['logo'] == NULL || $venueExists['Venue']['logo'] == null)
			$venue_data['logo'] = "";
		else {
			$thumb_imageName 	= Router::url('/', true).$venueExists['Venue']['logo'];
			if(!file_exists(WWW_ROOT.$venueExists['Venue']['logo'])){
				$thumb_imageName 	= Router::url('/', true)."img/default/NAthumb.png";
			}
			$venue_data['logo'] = $thumb_imageName;
		}

		// Parse the venue area
		$area = isset($venueExists['Area']) ? $venueExists['Area'] : array();
		$venue_areas = array();
		if(!empty($area)) {
			foreach($area as $row) {
				$row_data = array('id' => $row['id'], 'name' => $row['name']);
				$venue_areas[] = $row_data;
			}
		}
		$venue_data['venue_areas'] = $venue_areas;

		//print_r($venue_data); exit;
		
		// Few calculations and stats
		$avg_ratings = $venue_data['avg_rating'];
		$total_reviews = $this->VenueReview->get_total_reviews_count($venue_id);

		$venue_data['is_favorite'] = $this->UserFavoriteVenue->is_user_venue_favorite($loggedin_user_id, $venue_id);
		if(empty($venue_data['contact_person']) || $venue_data['contact_person'] == null)
			$venue_data['contact_person'] = 'NA';		//$venue_data['contact_email'] = 'xyz@gmail.com';
		//$venue_data['contact_email'] = $venue_data['email'];
		$venue_data['user_rating'] = $this->VenueReview->get_user_venue_rating_value($loggedin_user_id, $venue_id);

		$venue_data['area'] = isset($venue_areas[0]['name']) ? $venue_areas[0]['name'] : '';
		$venue_data['website'] = $venueExists['Venue']['website'];
		$venue_data['total_reviews'] = $total_reviews;
		if($auth_type == 'android')
			$venue_data['url'] = $this->get_venue_url($venue_id, $category_id);
		else
			$venue_data['venue_url'] = $this->get_venue_url($venue_id, $category_id);

		// Parse the venue days time
		$days_time = isset($venueExists['VenueDaysTime']) ? $venueExists['VenueDaysTime'] : array();
		$venue_days_time = array('regular_hours' => array(), 'happy_hours' => array());
		if(!empty($days_time)) {
			foreach($days_time as $row) {
				$is_happy_hours = $row['is_happy_hours'];

				$row_data = array(
					'id' 		=> $row['id'],
					'from_day'	=> $row['from_day'],
					'to_day'	=> $row['to_day'],
					'from_time'	=> $row['from_time'],
					'to_time'	=> $row['to_time'],
					);

				if($is_happy_hours == 'y')
					$venue_days_time['happy_hours'][] = $row_data;
				else
					$venue_days_time['regular_hours'][] = $row_data;
			}
		}
		$venue_data['venue_days_time'] = $venue_days_time;


		// Parse the venue photos
		$venue_data['venue_photos'] = $this->get_venue_photos($venue_id);
		$venue_data['venue_promotions'] = $this->get_venue_promos($venue_id);
		$venue_data['venue_floor_plans'] = $this->get_venue_floor_plans($venue_id);
		/*$photos = isset($venueExists['VenuePhoto']) ? $venueExists['VenuePhoto'] : array();
		$venue_photos = array();
		if(!empty($photos)) {
			$base_url = Router::url('/', true);
			foreach($photos as $row) {
				$row_data = array(
					'id'	=> $row['id'],
					'photo'	=> $base_url . $row['photo']
					);
				$venue_photos[] = $row_data;
			}
		}
		$venue_data['venue_photos'] = $venue_photos;*/


		if($auth_type == 'web') {

			if(!empty($venue_data['assigned_user_id'])) {
				$userExists = $this->User->find('first', array('conditions' => array('User.id' => $venue_data['assigned_user_id'])));
				if(!empty($userExists) && isset($userExists['User']))
					$venue_data['assigned_user_data'] = $userExists['User'];
			}

			// Parse the venue categories
			$categories = isset($venueExists['Category']) ? $venueExists['Category'] : array();
			$venue_categories = array('parent' => array(), 'child' => array());
			if(!empty($categories)) {
				foreach($categories as $row) {
					$row_data = array('id' => $row['id'], 'name' => $row['name']);
					if($row['parent'] == 0)
						$venue_categories['parent'][] = $row_data;
					else
						$venue_categories['child'][] = $row_data;
				}
			}
			$venue_data['venue_categories'] = $venue_categories;



			// Get the venue table areas
			$this->TableArea = ClassRegistry::init('TableArea');
			$options = array(
				'conditions' => array('TableArea.venue_id' => $venue_id),
				'fields' => array('TableArea.id', 'TableArea.table_area', 'TableArea.no_of_persons', 'TableArea.price'),
				);
			$tableAreas = $this->TableArea->find('all', $options);
			$table_areas = array();
			if(!empty($tableAreas)) {
				foreach($tableAreas as $row) {
					$row_data = $row['TableArea'];
					$table_areas[] = $row_data;
				}
			}
			$venue_data['table_areas'] = $table_areas;


			//Get Venue Bottle Service Box
			$this->BottleServiceBox = ClassRegistry::init('BottleServiceBox');
			$options = array(
				'conditions' => array('BottleServiceBox.venue_id' => $venue_id),
				'joins' => array(
					array(
						'table' => 'bottle_types',
						'alias' => 'BottleType',
						'conditions' => 'BottleServiceBox.bottle_type_id = BottleType.id',
						),
					),
				'fields' => array('BottleServiceBox.*', 'BottleType.*'),
				);
			$bottleBox = $this->BottleServiceBox->find('all', $options);
			$bottle_service_boxes = array();
			if(!empty($bottleBox)) {
				foreach($bottleBox as $row) {
					$row_data = array(
						'id'	=> $row['BottleServiceBox']['id'],
						'brand' => $row['BottleServiceBox']['brand'],
						'price'	=> $row['BottleServiceBox']['price'],
						'box_number' => $row['BottleServiceBox']['box_number'],
						'other_bottle_type' => $row['BottleServiceBox']['other_bottle_type'],
						'bottle_type_id' => $row['BottleServiceBox']['bottle_type_id'],
						'bottle_type_name' => $row['BottleType']['name'],
						);
					$bottle_service_boxes[] = $row_data;
				}
			}
			$venue_data['bottle_service_boxes'] = $bottle_service_boxes;
		}



		//Get Mixer data
		$this->MixerServiceBox = ClassRegistry::init('MixerServiceBox');

		$options = array(
			'conditions' => array('MixerServiceBox.venue_id' => $venue_id),
			'fields' => array('MixerServiceBox.*'),
		);

		$mixerBox = $this->MixerServiceBox->find('all', $options);

		$mixerServiceBoxes = array();
		if(!empty($mixerBox)) {
			foreach($mixerBox as $row) {
				$row_data = array(
					'id'	=> $row['MixerServiceBox']['id'],
					'mixer' => $row['MixerServiceBox']['mixer'],
					'price'	=> $row['MixerServiceBox']['price'],
					);
				$mixerServiceBoxes[] = $row_data;
			}
			$venue_data['mixer_service_boxes'] = $mixerServiceBoxes;
		}

		//Get Limo Service data
		$this->LimoServiceBox = ClassRegistry::init('LimoServiceBox');

		$options = array(
			'conditions' => array('LimoServiceBox.venue_id' => $venue_id),
			'fields' => array('LimoServiceBox.*'),
		);

		$limoBox = $this->LimoServiceBox->find('all', $options);

		$limoServiceBoxes = array();
		if(!empty($limoBox)) {
			foreach($limoBox as $row) {
					$row_data = array(
						'id'	=> $row['LimoServiceBox']['id'],
						'limo_service' => $row['LimoServiceBox']['limo_service'],
						'price'	=> $row['LimoServiceBox']['price'],
						);
					$limoServiceBoxes[] = $row_data;
				}
			$venue_data['limo_service_boxes'] = $limoServiceBoxes;
		}

		// Parse the venue features
		$features = isset($venueExists['Feature']) ? $venueExists['Feature'] : array();
		$venue_features = array();
		if(!empty($features)) {
			foreach($features as $row) {
				$row_data = array('id' => $row['id'], 'name' => $row['name']);
				$venue_features[$row['type']][] = $row_data;
			}
		}
		$venue_data['venue_features'] = $venue_features;
		//$venue_data['venue_features']['custom_feature'] = $venueExists['Venue']['custom_feature'];

		// Get the venue bottle services
		$this->BottleService = ClassRegistry::init('BottleService');
		$this->BottleServiceBox = ClassRegistry::init('BottleServiceBox');

		$bottleService = $this->BottleService->findByvenue_id($venue_id);
		$bottle_service = array();
		if(!empty($bottleService)) {
			$bottle_logo = '';
			if(!empty($bottleService['BottleService']['logo']))
				$bottle_logo = Router::url('/', true) . $bottleService['BottleService']['logo'];
			$allow_bottle_service = ($bottleService['BottleService']['allow_bottle_service'] == 1) ? 'y' : 'n';
			$turn_bottle_service_on = ($bottleService['BottleService']['turn_bottle_service_on'] == 1) ? 'y' : 'n';
			$bottle_service = array(
				'id'	=> $bottleService['BottleService']['id'],
				'logo'	=> $bottle_logo,
				'allow_bottle_service'	=> $allow_bottle_service,
				'turn_bottle_service_on'	=> $turn_bottle_service_on,
				);
			if($auth_type == 'web') {
				$mail_info = array(
					'email' => $bottleService['BottleService']['email'],
					'email2' => $bottleService['BottleService']['email2'],
					'phone' => $bottleService['BottleService']['phone'],
					'phone2' => $bottleService['BottleService']['phone2'],
					);

				$bottle_service = array_merge($bottle_service, $mail_info);
			}
		}
		else {
			$bottle_service = array(
				'allow_bottle_service'	=> 'n',
				'turn_bottle_service_on' => 'n',
				);
		}
		$venue_data['bottle_service'] = $bottle_service;

		//Get User Reviews
		$options = array(
			'conditions' => array('VenueReview.venue_id' => $venue_id, 'VenueReview.comment !=' => ''),
			'joins' => array(
				array(
					'table'	=> 'users',
					'alias'	=> 'User',
					'conditions' => array(
							'User.id = VenueReview.user_id',
					),
				),
			),
			'fields' => array('VenueReview.id', 'VenueReview.rating', 'VenueReview.comment', 'VenueReview.created', 'User.id', 'User.username'),
			'limit'	=> 10,
			'group' => 'VenueReview.user_id',
			'order'	=> array('VenueReview.created' => 'DESC'),

		);
		$venue_review = $this->VenueReview->find('all', $options);
		$venue_data['venue_review'] = $venue_review;
		
		
		
		$response = array(
			'status' => 'success',
			'operation' => 'get_venue_details',
			'data' => $venue_data,
			);
		$this->set(array('response' => parseParams($response), '_serialize' => array('response')));
	}



	/**
	 ** @purpose save venue table area (add/update)
	 ** @input auth_type (android/iphone/web), venue_id, area_ids, price_list
	 ** @output
	 */
	public function save_table_areas() {
		$this->Venue = ClassRegistry::init('Venue');
		$this->TableArea = ClassRegistry::init('TableArea');


		$venue_id = $this->getParams('venue_id');
		$table_areas = $this->getParams('table_areas', array());
		$price_list = $this->getParams('price_list', array());
		$no_of_persons = $this->getParams('no_of_persons', array());

		$validationErrors = array();
		if(empty($venue_id))
			$validationErrors['venue_id'] = 'Venue is not selected';
		if(empty($table_areas) || !is_array($table_areas)) {
			$validationErrors['table_areas'] = 'Area is not selected';
		} else {
			foreach($table_areas as $table_area) {
				if(empty($table_area)) {
					$validationErrors['table_areas'] = 'Invalid area value';
					break;
				}
			}
		}

		if(empty($price_list) || !is_array($price_list)) {
			$validationErrors['price_list'] = 'Price is not set';
		} else {
			foreach($price_list as $price) {
				if($price == '' || !is_numeric($price)) {
					$validationErrors['price_list'] = 'Invalid price value';
					break;
				}
			}
		}

		if(empty($no_of_persons) || !is_array($no_of_persons)) {
			$validationErrors['no_of_persons'] = 'No. of Persons is not set';
		} else {
			foreach($no_of_persons as $person) {
				if($person == '' || !is_numeric($person)) {
					$validationErrors['no_of_persons'] = 'Invalid No of Persons value';
					break;
				}
			}
		}

		if(empty($validationErrors)) {
			if(count($table_areas) != count($price_list))
				$validationErrors['table_areas'] = 'Total selected areas and total entered price does not match';


			if(count($table_areas) != count($no_of_persons))
				$validationErrors['table_areas'] = 'Total selected areas and total entered persons does not match';


			if(count($no_of_persons) != count($price_list))
				$validationErrors['table_areas'] = 'Total entered persons and total entered price does not match';

			// Check if the selected venue is available or not
			$venueExists = $this->Venue->findByid($venue_id);
			if(empty($venueExists))
				$validationErrors['venue_id'] = 'Venue does not exists';
		}

		if(!empty($validationErrors)) {
			$response = array(
				'status' => 'error',
				'operation' => 'save_table_areas',
				'validationErrors' => $validationErrors,
				);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}


		// Begin transaction
		$this->TableArea->begin();

		/*
		// First delete all the previous entries for venue tabble area price
		$conditions = array('TableArea.venue_id' => $venue_id);
		$this->TableArea->deleteAll($conditions, $cascade_delete = false);
		*/

		$table_area_ids = array();
		$today = date('Y-m-d H:i:s');
		foreach($table_areas as $key => $table_area) {
			// Make entry in the table_areas table
			$area_input = array(
				'venue_id'	=> $venue_id,
				'table_area'	=> $table_area,
				'no_of_persons'		=> $no_of_persons[$key],
				'price'		=> $price_list[$key],
				'created'	=> $today,
				);
			$this->TableArea->create();
			if(!$this->TableArea->save($area_input)) {
				// Rollback transaction
				$this->TableArea->rollback();

				$response = array(
					'status' => 'error',
					'operation' => 'save_table_areas',
					'dataErrors' => 'Table area price could not be saved',
					);
				$this->set(array('response' => $response, '_serialize' => array('response')));
				return;
			}

			$table_area_ids[] = $this->TableArea->getLastInsertId();
		}

		// Commit transaction
		$this->TableArea->commit();

		if(!empty($venue_id)){	
			$this->send_mail_to_all($venue_id);				
		}



		$data = array('msg'	=> 'Changes saved successfully', 'table_area_ids' => $table_area_ids, 'data' => $this->data);
		$response = array(
			'status' => 'success',
			'operation' => 'save_table_areas',
			'data' => $data,
			);

		$this->set(array('response' => $response, '_serialize' => array('response')));
	}


	/**
	 ** @url v1.0/venues/update_table_area.json
	 ** @purpose Update table area detail
	 ** @input auth_type, auth_key, table_area_id, table_area, price, no_of_persons
	 **
	 **/
	//13/10/2014 by 037, Update  Table Area
	public function update_table_area() {
		$this->Venue = ClassRegistry::init('Venue');
		$this->TableArea = ClassRegistry::init('TableArea');

		$auth_type = $this->getParams('auth_type', 'web');
		//$venue_id = $this->getParams('venue_id');
		$table_area_id = $this->getParams('table_area_id');
		$table_area = $this->getParams('table_area');
		$price = $this->getParams('price');
		$no_of_persons = $this->getParams('no_of_persons');

		//Validations
		$validationErrors = array();
		/*
		if(empty($venue_id))
			$validationErrors['venue_id'] = "Venue ID is empty";
		else {
			// Check if the selected venue is available or not
			$venueExists = $this->Venue->findByid($venue_id);
			if(empty($venueExists))
				$validationErrors['venue_id'] = 'Venue does not exists';
		}
		*/
		if(empty($table_area_id))
			$validationErrors['table_area_id'] = "Table Area ID is empty";
		else {
			// Check if the selected venue is available or not
			$tableAreaExists = $this->TableArea->findByid($table_area_id);
			if(empty($tableAreaExists))
				$validationErrors['table_area_id'] = 'Table Area does not exists';
		}

		if(empty($table_area))
			$validationErrors['table_area'] = "Table Area is empty";
		if(empty($price) || !is_numeric($price))
			$validationErrors['price'] = "Invalid Price";
		if(empty($no_of_persons) || !is_numeric($no_of_persons))
			$validationErrors['no_of_persons'] = "Invalid No of Persons";

		//Return Validations
		if(!empty($validationErrors)) {
			$response = array('status' => 'error', 'operation' => 'update_table_area', 'validationErrors' => $validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}

		$input = array(
			'id' => $table_area_id,
			'table_area' => $table_area,
			'no_of_persons' => $no_of_persons,
			'price' => $price,
			'modified' => date('Y-m-d H:i:s'),
		);

		if(!$this->TableArea->save($input)) {

			$response = array('status' => 'error', 'operation' => 'update_table_area', 'dataErrors' => $this->TableArea->validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}

		//Return Response
		$response = array(
			'status' => 'success',
			'operation' => 'update_table_area',
			'data' => array('msg' => 'Table Area is updated successfully', 'data' => $this->data),
			);
		$this->set(array('response' => $response, '_serialize' => array('response')));
	}



	/**
	 ** @purpose delete the table area price
	 ** @input auth_type (android/iphone/web), table_area_id
	 ** @output
	 */
	public function delete_table_area() {
		$this->TableArea = ClassRegistry::init('TableArea');
		$table_area_id = $this->getParams('table_area_id');

		$validationErrors = array();
		if(empty($table_area_id)) {
			$validationErrors['table_area_id'] = "Table area is not selected";
		} else {
			$tableArea = $this->TableArea->findByid($table_area_id);
			if(empty($tableArea))
				$validationErrors['table_area_id'] = "Table area does not exists";
		}

		if(!empty($validationErrors)) {
			$response = array(
				'status' => 'error',
				'operation' => 'delete_table_area',
				'validationErrors' => $validationErrors,
				);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}


		if(!$this->TableArea->delete($table_area_id)) {
			$response = array(
				'status' => 'error',
				'operation' => 'delete_table_area',
				'dataErrors' => 'Table area could not be deleted',
				);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}


		$data = array('msg'	=> 'Record deleted successfully', 'data' => $this->data);
		$response = array(
			'status' => 'success',
			'operation' => 'delete_table_area',
			'data' => $data,
			);

		$this->set(array(
            'response' => $response,
            '_serialize' => array('response'),
        ));
	}

	/**
	 ** @purpose get the list of venue table areas
	 ** @input auth_type, venue_id
	 ** @output
	 */
	public function get_table_areas() {
		$this->TableArea = ClassRegistry::init('TableArea');
		$auth_type = $this->getParams('auth_type', 'web');
		$venue_id = $this->getParams('venue_id');

		$validationErrors = array();
		if(empty($venue_id))
			$validationErrors['venue_id'] = 'Venue is not selected';
		else {
			$venueExists = $this->Venue->findByid($venue_id);
			if(empty($venueExists))
				$validationErrors['venue_id'] = 'Venue does not exists';
		}

		if(!empty($validationErrors)) {
			$response = array('status' => 'error', 'operation' => 'get_table_areas', 'validationErrors' => $validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}

		$options = array(
			'conditions' => array('TableArea.venue_id' => $venue_id),
			'fields' => array('TableArea.*'),
			);
		$result = $this->TableArea->find('all', $options);

		$table_areas = array();
		if(!empty($result)) {
			foreach($result as $row) {
				$table_areas[] = array(
					'id'	=> $row['TableArea']['id'],
					'name' => $row['TableArea']['table_area'],
					'no_of_persons' => $row['TableArea']['no_of_persons'],
					'price'	=> $row['TableArea']['price'],
					);
			}
		}

		$response = array(
			'status' => 'success',
			'operation' => 'get_table_areas',
			'table_areas' => $table_areas,
			);
		$this->set(array('response' => $response, '_serialize' => array('response')));
	}


	/**
	 ** @purpose add bottle service box
	 ** @input auth_type (android/iphone/web), venue_id, bottle_type_ids, box_number, brand, price
	 ** @output
	 */
	public function add_bottle_service_box() {
		$this->Venue = ClassRegistry::init('Venue');
		$this->BottleServiceBox = ClassRegistry::init('BottleServiceBox');


		$venue_id = $this->getParams('venue_id');
		$bottle_type_ids = $this->getParams('bottle_type_ids', array());
		$box_number = $this->getParams('box_number');
		$brand = $this->getParams('brand');
		$price = $this->getParams('price');
		$other_bottle_type = $this->getParams('other_bottle_type');

		$allowed_boxes = array('1', '2', '3');

		$validationErrors = array();
		if(empty($venue_id))
			$validationErrors['venue_id'] = 'Venue is not selected';
		if(empty($bottle_type_ids) || !is_array($bottle_type_ids)) {
			$validationErrors['bottle_type_ids'] = 'Bottle type is not selected';
		} else {
			foreach($bottle_type_ids as $type_id) {
				if(empty($type_id) || !is_numeric($type_id)) {
					$validationErrors['bottle_type_ids'] = 'Invalid bottle type value';
					break;
				}
			}
		}
		if(empty($box_number) || !in_array($box_number, $allowed_boxes))
			$validationErrors['box_number'] = 'Box number is not selected';
		if(empty($brand))
			$validationErrors['brand'] = 'Brand name is not set';
		if($price == '' || !is_numeric($price))
			$validationErrors['price'] = 'Invalid bottle price value';

		if(empty($validationErrors)) {
			// Check if the selected venue is available or not
			$venueExists = $this->Venue->findByid($venue_id);
			if(empty($venueExists))
				$validationErrors['venue_id'] = 'Venue does not exists';
		}

		if(!empty($validationErrors)) {
			$response = array(
				'status' => 'error',
				'operation' => 'add_bottle_service_box',
				'validationErrors' => $validationErrors,
				);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}


		$bottle_service_box_ids = array();
		$today = date('Y-m-d H:i:s');
		foreach($bottle_type_ids as $type_id) {
			$bs_input = array(
				'venue_id'	=> $venue_id,
				'bottle_type_id' => $type_id,
				'box_number' => $box_number,
				'other_bottle_type' => $other_bottle_type,
				'brand'	=> $brand,
				'price'	=> $price,
				'created'	=> $today,
				);
			$this->BottleServiceBox->create();
			$this->BottleServiceBox->save($bs_input);
			$bottle_service_box_ids[] = $this->BottleServiceBox->getLastInsertId();
		}


		$data = array('msg'	=> 'Changes saved successfully', 'bottle_service_box_ids' => $bottle_service_box_ids, 'data' => $this->data);
		$response = array(
			'status' => 'success',
			'operation' => 'add_bottle_service_box',
			'data' => $data,
			);

		$this->set(array(
            'response' => $response,
            '_serialize' => array('response'),
        ));
	}

	/**
	 ** @purpose delete bottle service box
	 ** @input auth_type, venue_id, bottle_service_box_id
	 ** @output
	 */
	public function delete_bottle_service_box() {
		$this->BottleServiceBox = ClassRegistry::init('BottleServiceBox');

		$auth_type = $this->getParams('auth_type');
		$venue_id = $this->getParams('venue_id');
		$bottle_service_box_id = $this->getParams('bottle_service_box_id');

		$validationErrors = array();
		if(empty($venue_id))
			$validationErrors['venue_id'] = 'Venue is not selected';
		else {
			// Check if the selected venue is available or not
			$venueExists = $this->Venue->findByid($venue_id);
			if(empty($venueExists))
				$validationErrors['venue_id'] = 'Venue does not exists';
		}
		if(empty($bottle_service_box_id))
			$validationErrors['bottle_service_box_id'] = 'Bottle service box is not selected';
		else {
			// Check if the selected bottle service is correct or not
			$options = array('conditions' => array('venue_id' => $venue_id, 'id' => $bottle_service_box_id));
			$bottleServiceBoxExists = $this->BottleServiceBox->find('first', $options);
			if(empty($bottleServiceBoxExists))
				$validationErrors['bottle_service_box_id'] = 'Invalid bottle service box';
		}

		if(!empty($validationErrors)) {
			$response = array('status' => 'error', 'operation' => 'delete_bottle_service_box', 'validationErrors' => $validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}


		if(!$this->BottleServiceBox->delete($bottle_service_box_id)) {
			$response = array('status' => 'error', 'operation' => 'delete_bottle_service_box', 'dataErrors' => $this->BottleServiceBox->validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}


		$response = array(
			'status' => 'success',
			'operation' => 'delete_bottle_service_box',
			'data' => array('msg' => 'Bottle service box has been deleted successfully', 'data' => $this->data),
			);
		$this->set(array('response' => $response, '_serialize' => array('response')));
	}

		/**
	 ** @purpose save bottle service
	 ** @input auth_type, venue_id, allow_bottle_service (y/n), turn_bottle_service_on (y/n)
	 ** @output
	 */
	public function save_bottle_service() {
		$venue_id = $this->getParams('venue_id');
		$allow_bottle_service = $this->getParams('allow_bottle_service');
		$turn_bottle_service_on = $this->getParams('turn_bottle_service_on');
		$email = $this->getParams('email');
		$email2 = $this->getParams('email2');
		$phone = $this->getParams('phone');
		$phone2 = $this->getParams('phone2');
		//$logo = $this->getParams('logo');

		$allowed_status = array('y', 'n');

		$validationErrors = array();
		if(empty($venue_id))
			$validationErrors['venue_id'] = 'Venue is not selected';
		else {
			// Check if the selected venue is available or not
			$venueExists = $this->Venue->findByid($venue_id);
			if(empty($venueExists))
				$validationErrors['venue_id'] = 'Venue does not exists';
		}

		if(!in_array($allow_bottle_service, $allowed_status))
			$validationErrors['allow_bottle_service'] = 'Invalid allowed bottle service value';
		if(!in_array($turn_bottle_service_on, $allowed_status))
			$validationErrors['turn_bottle_service_on'] = 'Invalid turn bottle service on value';
	/*	if(empty($logo))
			$validationErrors['logo'] = 'logo image is required';	*/

		if($turn_bottle_service_on == 'y') {
			if(empty($email) && empty($email2))
				$validationErrors['email'] = 'Email is not selected';
			if(empty($phone) && empty($phone2))
				$validationErrors['phone'] = 'Phone is not selected';
		}

		if(!empty($validationErrors)) {
			$response = array('status' => 'error', 'operation' => 'save_bottle_service', 'validationErrors' => $validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}

		$abs = $tbso = '0';
		if($allow_bottle_service == 'y')
			$abs = '1';
		if($turn_bottle_service_on == 'y')
			$tbso = '1';


		// Check if previous entry of venue bottle service is available or not
		$this->BottleService = ClassRegistry::init('BottleService');
		$options = array('conditions' => array('BottleService.venue_id' => $venue_id));
		$bottleServiceExists = $this->BottleService->find('first', $options);
		$service_id = isset($bottleServiceExists['BottleService']['id']) ? $bottleServiceExists['BottleService']['id'] : '';

		$today = date('Y-m-d H:i:s');

		if(!empty($service_id)) {
			$input = array(
				'id'	=> $service_id,
				'venue_id'	=> $venue_id,
				'email' => $email,
				'email2' => $email2,
				'phone' => $phone,
				'phone2' => $phone2,
				'allow_bottle_service' 	 => $abs,
				'turn_bottle_service_on' => $tbso,
				'updated'	=> $today,
				);
		} else {
			$input = array(
				'venue_id'	=> $venue_id,
				'email' => $email,
				'email2' => $email2,
				'phone' => $phone,
				'phone2' => $phone2,
				'allow_bottle_service' 	 => $abs,
				'turn_bottle_service_on' => $tbso,
				'created' 	=> $today,
				'updated'	=> $today,
				);
		}

		// Begin transaction
		$this->BottleService->begin();

		if(!$this->BottleService->save($input)){
			// Rollback transaction
			$this->BottleService->rollback();

			$response = array('status' => 'error', 'operation' => 'add_event',	'dataErrors' => $this->BottleService->validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}


		#@05/09/2014 by 037, Upload Bottle Serice logo

		$bottle_service_id = $this->BottleService->getLastInsertId();
		if($service_id == '')		$return_id = $bottle_service_id;
		else                        $return_id = $service_id;

		/*
		// Validate uploaded Bottle Service logo
		if(empty($validationErrors)) {
			$file_loc = 'uploads'.DS.'logos'.DS;
			$file_name = $venue_id.'_' . rand()."_".microtime() . '.jpg';
			$file_name 		= str_replace(" ", '', $file_name);


			$file_dir = WWW_ROOT . $file_loc;
			$file_url = Router::url('/', true) . $file_loc . $file_name;
			$fname = $file_dir . $file_name;

			if(!is_dir($file_dir))
				mkdir($file_dir, 0755, true);

			$raw_file = base64_decode($logo);
			$handle = fopen($fname, "w+");
			fwrite($handle, $raw_file);
			fclose($handle);

			if(!file_exists($fname) || filesize($fname) <= 0)
				$validationErrors['logo'] = 'File could not be uploaded';
		}

		if(!empty($validationErrors)) {
			// Rollback transaction
			$this->BottleService->rollback();

			$response = array('status' => 'error', 'operation' => 'save_bottle_service', 'validationErrors' => $validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}

		$this->BottleService->id = $bottle_service_id;
		$data = array(
			'id' => $bottle_service_id,
			'venue_id' => $venue_id,
			'logo' => $file_loc . $file_name,
		);

		if(!$this->BottleService->save($data, $validate = false)) {
			// Rollback transaction
			$this->BottleService->rollback();

			$response = array('status' => 'error', 'operation' => 'save_bottle_service',	'dataErrors' => $this->BottleService->validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}
		*/

		// Commit transaction
		$this->BottleService->commit();
		
		if(!empty($venue_id)){
			$this->send_mail_to_all($venue_id);	
		}

		

		$data = array('msg'	=> 'Changes saved successfully', 'data' => $this->data);
		$response = array('status' => 'success', 'operation' => 'save_bottle_service', 'data' => $data, 'bottle_service_id' => $return_id);
		$this->set(array('response' => $response, '_serialize' => array('response')));
	}



	/**
	 ** @purpose save venue categories and features
	 ** @input auth_type (android/iphone/web), venue_id, category_ids, feature_ids, details, promotions
	 ** @output
	 */
	public function save_categories_features() {
		$this->Venue = ClassRegistry::init('Venue');
		$this->CategoriesVenue = ClassRegistry::init('CategoriesVenue');
		$this->FeaturesVenue = ClassRegistry::init('FeaturesVenue');

		$venue_id = $this->getParams('venue_id');
		$category_ids = $this->getParams('category_ids', array());
		$feature_ids = $this->getParams('feature_ids', array());
		$custom_feature = $this->getParams('custom_feature');
		$chk_send_noti = $this->getParams('chk_send_noti');
		$user_password = $this->getParams('user_password');
		$user_detail = $this->getParams('user_detail');
		$is_new_user = 0;

		$validationErrors = array();
		if(empty($venue_id))
			$validationErrors['venue_id'] = 'Venue is not selected';
		if(empty($category_ids))
			$validationErrors['category_ids'] = 'Category is not selected';
		//if(empty($feature_ids))
			//$validationErrors['feature_ids'] = 'Feature is not selected';

		if(empty($validationErrors)) {
			// Check if the selected venue is available or not
			$venueExists = $this->Venue->findById($venue_id);
			if(empty($venueExists))
				$validationErrors['venue_id'] = 'Venue does not exists';
			else{
				$assigned_user_id = isset($venueExists['Venue']['assigned_user_id']) ? $venueExists['Venue']['assigned_user_id'] : 0;
				$contact_person = isset($venueExists['Venue']['contact_person']) ? $venueExists['Venue']['contact_person'] : '';
				$userExists = $this->User->findById($assigned_user_id);
				$username = isset($userExists['User']['username']) ? $userExists['User']['username'] : '';
				$email = isset($userExists['User']['email']) ? $userExists['User']['email'] : '';
				$is_new_user = isset($venueExists['Venue']['is_new_user']) ? $venueExists['Venue']['is_new_user'] : 0;
				$activation_key = isset($userExists['User']['activation_key']) ? $userExists['User']['activation_key'] : '';
			}
		}


		if(!empty($validationErrors)) {
			$response = array(
				'status' => 'error',
				'operation' => 'save_categories_features',
				'validationErrors' => $validationErrors,
				);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}

		$today = date('Y-m-d H:i:s');


		// Begin transaction
		$this->Venue->begin();

		$venue_input = array(
			'id'	=> $venue_id,
			'updated'	=> $today,
			'venue_type'	=> "C",
			'custom_feature' => $custom_feature
			);
		if(!$this->Venue->save($venue_input, $validate = false)) {
			// Rollback transaction
			$this->Venue->rollback();

			$response = array(
				'status' => 'error',
				'operation' => 'save_categories_features',
				'dataErrors' => 'Venue information could not be saved',
				);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}


		// First delete all the previous entries for venue categories
		$conditions = array('CategoriesVenue.venue_id' => $venue_id);
		$this->CategoriesVenue->deleteAll($conditions, $cascade_delete = false);

		foreach($category_ids as $key => $category_id) {
			// Make entry in the categories_venue table
			$cat_input = array(
				'category_id'	=> $category_id,
				'venue_id'		=> $venue_id,
				);
			$this->CategoriesVenue->create();
			if(!$this->CategoriesVenue->save($cat_input)) {
				// Rollback transaction
				$this->Venue->rollback();

				$response = array(
					'status' => 'error',
					'operation' => 'save_categories_features',
					'dataErrors' => 'Venue categories could not be saved',
					);
				$this->set(array('response' => $response, '_serialize' => array('response')));
				return;
			}
		}


		// First delete all the previous entries for venue features
		$conditions = array('FeaturesVenue.venue_id' => $venue_id);
		$this->FeaturesVenue->deleteAll($conditions, $cascade_delete = false);
		if(!empty($feature_ids)){
			foreach($feature_ids as $key => $feature_id) {
				// Make entry in the features_venue table
				$fea_input = array(
					'feature_id'	=> $feature_id,
					'venue_id'		=> $venue_id,
					);
				$this->FeaturesVenue->create();
				if(!$this->FeaturesVenue->save($fea_input)) {
					// Rollback transaction
					$this->Venue->rollback();

					$response = array(
						'status' => 'error',
						'operation' => 'save_categories_features',
						'dataErrors' => 'Venue features could not be saved',
						);
					$this->set(array('response' => $response, '_serialize' => array('response')));
					return;
				}
			}
		}
		// commit transaction
		$this->Venue->commit();




			 // @17/10/2014 by 037, Shoutoutcity API > Venues > Block Venue assign/unlink Notification mails
		if($chk_send_noti == 'y') {
			//-------- this mail is for new user registartion---------------
			if($is_new_user == 1){
				$reset_link = SITE_URL."reset_password?activation_key=".$activation_key;
				$to = array($email => $contact_person);
				$from = array(SITE_EMAIL => SITE_TITLE);
				$subject = 'Shoutoutcity - You have been registered as Venue admin';
				$template = 'venue_admin';
				$variables = array('name' => $contact_person, 'username' => $username, 'reset_link' => $reset_link);

				if(!$this->User->send_mail($from, $to, $subject, $template, $variables)) {
					$response = array('status' => 'error', 'operation' => 'save_categories_features', 'validationErrors' => $this->User->validationErrors);
					$this->set(array('response' => $response, '_serialize' => array('response')));
					return;
				}
			}
		}
		if(!empty($userExists)){
			#-------- this mail is for new venue assignment---------------
			$venuename = $venueExists['Venue']['name'];
			$to = array($email => $contact_person);
			$from = array(SITE_EMAIL => SITE_TITLE);
			$subject = 'Shoutoutcity - Venue assigned successfully';
			$template = 'venue_assign';
			$variables = array('name' => $contact_person, 'username' => $username, 'venuename' => $venuename);

			if(!$this->User->send_mail($from, $to, $subject, $template, $variables)) {
				$response = array('status' => 'error', 'operation' => 'save_categories_features', 'validationErrors' => $this->User->validationErrors);
				$this->set(array('response' => $response, '_serialize' => array('response')));
				return;
			}
			##
			////////////////////////////////
			# s
			if(!empty($venue_id)){
				$venueData = $this->Venue->findByid($venue_id);			

				#send update mail to all 4 (shoutoutadmin, notificationemail1, notificationemail2)
				$cc = array();			
				$to = array();			
				
				#shoutoutadmin
				/*$shoutoutadmin_id = $venueData['Venue']['user_id'];          
				if(!empty($shoutoutadmin_id)){
					$shoutoutadminExists = $this->User->findByid($shoutoutadmin_id);
					if(!empty($shoutoutadminExists)){			
						$shoutoutadmin_name = $shoutoutadminExists['User']['name'];
						$shoutoutadmin_email = $shoutoutadminExists['User']['email'];
						if(!empty($shoutoutadmin_email))
							array_push($to, $shoutoutadmin_email); 
					}
				}*/
				##
				
				#venueadmin
				/*$venueadmin_id = $venueData['Venue']['assigned_user_id'];  
				if(!empty($venueadmin_id)){
					$venueadminExists = $this->User->findByid($venueadmin_id);
					if(!empty($venueadminExists)){			
						$venueadmin_name = $venueadminExists['User']['name'];
						$venueadmin_email = $venueadminExists['User']['email'];
						if(!empty($venueadmin_email))
							array_push($to, $venueadmin_email); 
					}
				}*/
				##
				
				#bottle mail1 and mail2
				$this->BottleService = ClassRegistry::init('BottleService');
				$options = array('conditions' => array('BottleService.venue_id' => $venue_id));
				$bottleServiceExists = $this->BottleService->find('first', $options);
				if(!empty($bottleServiceExists)){
					$notification_mail1 = $bottleServiceExists['BottleService']['email'];
					if(!empty($notification_mail1))
							array_push($to, $notification_mail1); 
							
					$notification_mail2 = $bottleServiceExists['BottleService']['email2'];
					if(!empty($notification_mail2))
							array_push($to, $notification_mail2); 
					
				}
				##
				
				$from = array(SITE_EMAIL => SITE_TITLE);
				$subject = 'Shoutoutcity - Venue assigned successfully';
				$template = 'venue_assign_mail_to_others';
				$variables = array(		
					 'name' => $contact_person, 'username' => $username, 'venuename' => $venuename	
					);
					
				if(!$this->User->send_mail($from, $to, $subject, $template, $variables)) {
					//$response = array('status' => 'error', 'operation' => 'save_venue_profile');
					//$this->set(array('response' => $response, '_serialize' => array('response')));
					//return;
				}	
				
			}
			
			////////////////////////////////////
			
		}

			$this->Venue->begin();
			$venue_input = array(
				'id'	=> $venue_id,
				'is_new_user'	=> 0,
				);

			// Make entry in the venues table
			if(!$this->Venue->save($venue_input, $validate = false)) {

			}
			// commit transaction
			$this->Venue->commit();

					

		
		if(!empty($venue_id)){
			$this->send_mail_to_all($venue_id);	
		}



		$data = array('msg'	=> 'Changes saved successfully', 'data' => $this->data);
		$response = array(			
			'status' => 'success',
			'operation' => 'save_categories_features',
			'chk_send_noti' => $chk_send_noti,
			'is_new_user' => $is_new_user,
			'userExists' => $userExists,
			'user_detail' => $user_detail,
			'data' => $data,
			);

		$this->set(array(
            'response' => $response,
            '_serialize' => array('response'),
        ));
	}



	/**
	 ** @purpose upload bottle service logo
	 ** @input auth_type, bottle_service_id, venue_id, logo
	 ** @output Array of data
	 */

	//@20/08/2014 by 037, Upload bottle service Logo
	public function upload_bottle_service_logo() {
		$this->BottleService = ClassRegistry::init('BottleService');

		$auth_type = $this->getParams('auth_type', 'web');
		$bottle_service_id = $this->getParams('bottle_service_id');
		$venue_id = $this->getParams('venue_id');
		$logo = $this->getParams('logo');


		// Check If Empty
		$validationErrors = array();
		if(empty($bottle_service_id))
			$validationErrors['bottle_service_id'] = 'Bottle Service ID is not selected';
		if(empty($venue_id))
			$validationErrors['venue_id'] = 'Venue is not selected';
		if(empty($logo))
			$validationErrors['logo'] = 'Logo is not selected';


		if(empty($validationErrors)) {
			// Check if the venue is available or not
			$venueExists = $this->Venue->findByid($venue_id);
			$bottleExists = $this->BottleService->findByid($bottle_service_id);
			if(empty($venueExists))
				$validationErrors['venue_id'] = 'Venue does not exists';
			else if(empty($bottleExists))
				$validationErrors['bottle_service_id'] = 'Bottle Service does not exists';
			else if(!empty($bottleExists) && !empty($bottleExists['BottleService']['logo']))
				$validationErrors['logo'] = 'Logo is already available. Remove it, and then try again.';
		}

		// Validate uploaded venue logo
		if(empty($validationErrors)) {
			$file_loc = 'uploads'.DS.'logos'.DS;
			$file_name = $venue_id.'_' . rand()."_".microtime() . '.jpg';
			$file_name 		= str_replace(" ", '', $file_name);


			$file_dir = WWW_ROOT . $file_loc;
			$file_url = Router::url('/', true) . $file_loc . $file_name;
			$fname = $file_dir . $file_name;

			if(!is_dir($file_dir))
				mkdir($file_dir, 0755, true);

			$raw_file = base64_decode($logo);
			$handle = fopen($fname, "w+");
			fwrite($handle, $raw_file);
			fclose($handle);

			if(!file_exists($fname) || filesize($fname) <= 0)
				$validationErrors['logo'] = 'File could not be uploaded';
		}

		if(!empty($validationErrors)) {
			$response = array('status' => 'error', 'operation' => 'upload_bottle_service_logo', 'validationErrors' => $validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}


		// Save Data
		$this->BottleService->id = $bottle_service_id;
		$data = array(
			'id' => $bottle_service_id,
			'venue_id' => $venue_id,
			'logo' => $file_loc . $file_name,
		);

		if(!$this->BottleService->save($data, $validate = false)) {
			$response = array('status' => 'error', 'operation' => 'upload_bottle_service_logo',	'dataErrors' => $this->BottleService->validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}


		// SuccessResponse
		$response = array(
			'status' => 'success',
			'operation' => 'upload_bottle_service_logo',
			'data' => $data
			);
		$this->set(array(
			'response' => $response,
			'_serialize' => array('response'),
		));
	}


	/**
	 ** @purpose delete bottle service logo
	 ** @input auth_type, bottle_service_id
	 ** @output
	 **/
	// @08/09/2014 by 037, Delete Bottle Service Logo
	public function delete_bottle_service_logo() {
		$this->BottleService = ClassRegistry::init('BottleService');

		$auth_type = $this->getParams('auth_type', 'web');
		$bottle_service_id = $this->getParams('bottle_service_id');

		$validationErrors = array() ;
		if(empty($bottle_service_id))
			$validationErrors['bottle_service_id'] = "Bottle Service ID is not selected";

		if(empty($validationErrors)) {
			// Check if the Bottle Service is available or not
			$bottleExists = $this->BottleService->findByid($bottle_service_id);
			if(empty($bottleExists))
				$validationErrors['bottle_service_id'] = "Bottle Service does not exist";
		}

		if(!empty($validationErrors)) {
			$response = array('status' => 'error', 'operation' => 'delete_bottle_service_logo', 'validationErrors' => $validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}

		$logo = $bottleExists['BottleService']['logo'];

		$input = array('id' => $bottle_service_id, 'logo' => '');
		if(!$this->BottleService->save($input)) {
			$response = array('status' => 'error', 'operation' => 'delete_bottle_service_logo', 'dataErrors' => $this->BottleService->validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}

		// Unlink logo image if any
		$logo_path = WWW_ROOT . DS . $logo;
		@unlink($logo_path);


		// SuccessResponse
		$response = array(
			'status' => 'success',
			'operation' => 'delete_bottle_service_logo',
			'data' => array('msg' => 'Logo has been deleted successfully', 'data' => $this->data),
			);
		$this->set(array('response' => $response, '_serialize' => array('response')));
	}


	/**
	 ** @purpose mark the venue as favorite for the user
	 ** @input auth_type, auth_key, venue_id, status (y/n)
	 ** @output
	 */
	public function mark_favorite() {

		$this->UserFavoriteVenue = ClassRegistry::init('UserFavoriteVenue');

		$auth_type = $this->getParams('auth_type');
		$auth_key = $this->getParams('auth_key');
		$venue_id = $this->getParams('venue_id');
		$status = $this->getParams('status');

		$validationErrors = array();
		if(empty($auth_key))
			$validationErrors['auth_key'] = 'Auth key is blank';
		else {
			$user_id = $this->User->get_user_id($auth_key);
			if(empty($user_id))
				$validationErrors['auth_key'] = 'Invalid auth key';
		}

		if(empty($venue_id))
			$validationErrors['venue_id'] = 'Venue is not selected';
		else {
			// Check if the venue is available or not
			$venueExists = $this->Venue->findByid($venue_id);
			if(empty($venueExists))
				$validationErrors['venue_id'] = 'Venue does not exists';
		}

		if(empty($status) || !in_array($status, array('y', 'n')))
			$validationErrors['status'] = 'Status is invalid';



		if(empty($validationErrors) && $status == 'y') {
			// Check if the venue is already favorite of the user
			$sql = "select * from user_favorite_venues where user_id = '".$user_id."' and venue_id = '".$venue_id."'";
			$alreadyFavorite = $this->UserFavoriteVenue->query($sql);
			//$alreadyFavorite = $this->UserFavoriteVenue->find('first', array('user_id' => $user_id, 'venue_id' => $venue_id));
			if(!empty($alreadyFavorite))
				$validationErrors['status'] = 'Venue is already in your favorite list';
		}

		if(!empty($validationErrors)) {
			$response = array('status' => 'error', 'operation' => 'mark_favorite', 'validationErrors' => $validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}


		if($status == 'y') {

			$favorite_input = array('user_id' => $user_id, 'venue_id' => $venue_id);
			if(!$this->UserFavoriteVenue->save($favorite_input)) {
				$response = array('status' => 'error', 'operation' => 'mark_favorite', 'dataErrors' => $this->UserFavoriteVenue->validationErrors);
				$this->set(array('response' => $response, '_serialize' => array('response')));
				return;
			}
			$msg = "Venue has been added to your favorite list";

		} else {

			if(!$this->UserFavoriteVenue->deleteAll($conditions = array('user_id' => $user_id, 'venue_id' => $venue_id))) {
				$response = array('status' => 'error', 'operation' => 'mark_favorite', 'dataErrors' => $this->UserFavoriteVenue->validationErrors);
				$this->set(array('response' => $response, '_serialize' => array('response')));
				return;
			}
			$msg = "Venue has been removed from your favorite list";

		}

		$response = array(
			'status' => 'success',
			'operation' => 'mark_favorite',
			'data' => array('msg' => $msg, 'favorite_id' => $this->UserFavoriteVenue->id),
			);
		$this->set(array('response' => $response, '_serialize' => array('response')));
	}

	/**
	 ** @purpose get the list of favorite venues
	 ** @input auth_type, auth_key
	 ** @output
	 */
	public function favorite_list() {
		$auth_type = $this->getParams('auth_type', 'web');
		$auth_key = $this->getParams('auth_key');

		$sort = $this->getParams('sort', 'Venue.updated');
		$direction = $this->getParams('direction', 'DESC');
		$page = $this->getParams('page', 1);
		$limit = $this->getParams('limit', 15);

		$validationErrors = array();
		if(empty($auth_key))
			$validationErrors['auth_key'] = 'Auth key is blank';
		else {
			 $user_id = $this->User->get_user_id($auth_key);

			if(empty($user_id))
				$validationErrors['auth_key'] = 'Invalid auth key';
		}

		if(!empty($validationErrors)) {
			$response = array('status' => 'error', 'operation' => 'favorite_list', 'validationErrors' => $validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}



		// Get the total count of matching venues
		$options = array(
			'recursive' => '-1',
			'joins' => array(
				array(
					'table' => 'user_favorite_venues',
					'alias' => 'Favorite',
					'conditions' => 'Favorite.venue_id = Venue.id',
					),
				),
			'conditions' => array('Favorite.user_id' => $user_id, 'Venue.venue_status' => 1),
			);

		$total_venues = $this->Venue->find('count', $options);


		// Get pagination related information
		$paginate_cond = array(
			'total_records'	=> $total_venues,
			'page_num'	=> $page,
			'per_page'	=> $limit,
			);
		$pagination = get_pagination_stats($paginate_cond);


		// Get the list of venues
		$options = array(
			'recursive' => '-1',
			'page'	=> $page,
			'limit'	=> $limit,
			'order'	=> array($sort => $direction),
			'fields' => array('Venue.id', 'Venue.name', 'Venue.address', 'Venue.state', 'Venue.city', 'Venue.zip', 'Venue.phone1 AS phone', 'Venue.logo as image', 'Venue.avg_rating'),
			'joins' => array(
				array(
					'table' => 'user_favorite_venues',
					'alias' => 'Favorite',
					'conditions' => 'Favorite.venue_id = Venue.id',
					),
				),
			'conditions' => array('Favorite.user_id' => $user_id, 'Venue.venue_status' => 1),
			);
		$result = $this->Venue->find('all', $options);



		$venues = array();
		if(!empty($result)) {
			foreach($result as $row) {

				$venue_id = $row['Venue']['id'];
				$avg_ratings = $row['Venue']['avg_rating'];
				$total_reviews = $this->VenueReview->get_total_reviews_count($venue_id);

				//$row['Venue']['image'] = '';
				if($row['Venue']['image'] == NULL || $row['Venue']['image'] == null)
					$row['Venue']['image'] = "";
				else {
					$thumb_imageName 	= Router::url('/', true).$row['Venue']['image'];
					if(!file_exists(WWW_ROOT.$row['Venue']['image'])){
						$thumb_imageName 	= Router::url('/', true)."img/default/NAthumb.png";
					}
					$row['Venue']['image'] = $thumb_imageName;
				}


				$category_alias = '';
				$category_alias = $this->get_parent_category_alias($venue_id);



				$row['Venue']['category_alias'] = $category_alias;
				$row['Venue']['avg_ratings'] = $avg_ratings;
				$row['Venue']['total_reviews'] = $total_reviews;
				$venues[] = $row['Venue'];
			}
		}


		$response = array(
			'status' => 'success',
			'operation' => 'favorite_list',
			'pagination' => $pagination,
			'venues' => $venues,
			);
		$this->set(array('response' => $response, '_serialize' => array('response')));
	}


	/**
	 ** @purpose rate the vneue
	 ** @input auth_type, auth_key, venue_id, rating, review
	 ** @output
	 */
	public function rate_venue() {
		$auth_type = $this->getParams('auth_type', 'web');
		$auth_key = $this->getParams('auth_key');
		$venue_id = $this->getParams('venue_id');
		$rating = $this->getParams('rating');
		$review = $this->getParams('review');

		$validationErrors = array();

		if(empty($auth_key))
			$validationErrors['auth_key'] = 'Auth key is blank';
		else {
			$user_id = $this->User->get_user_id($auth_key);
			if(empty($user_id))
				$validationErrors['auth_key'] = 'Invalid auth key';
		}
		if(empty($venue_id))
			$validationErrors['venue_id'] = 'Venue is not selected';
		else {
			$venueExists = $this->Venue->findByid($venue_id);
			if(empty($venueExists))
				$validationErrors['venue_id'] = 'Venue does not exists';
		}
		if(empty($rating) || !is_numeric($rating) || $rating < 1 || $rating > 5)
			$validationErrors['rating'] = 'User rating is incorrect. Allowed value is between 1 to 5.';
		/*
		if(empty($review))
			$validationErrors['review'] = 'User review is not provided';
		*/
		if(!empty($validationErrors)) {
			$response = array('status' => 'error', 'operation' => 'rate_venue', 'validationErrors' => $validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}

		$input = array(
			'user_id'	=> $user_id,
			'venue_id'	=> $venue_id,
			'comment'	=> $review,
			'rating'	=> $rating,
			'created'	=> date('Y-m-d H:i:s'),
			);

		// Check if the venue rating is already done by the user
		$options = array('conditions' => array('VenueReview.user_id' => $user_id, 'VenueReview.venue_id' => $venue_id));
		$ratingExists = $this->VenueReview->find('first', $options);
		if(!empty($ratingExists)) {
			$rate_id = $ratingExists['VenueReview']['id'];
			$input['id'] = $rate_id;
		}

		// Begin transaction
		$this->Venue->begin();

		if(!$this->VenueReview->save($input)) {
			// Rollback transaction
			$this->Venue->rollback();
			$response = array('status' => 'error', 'operation' => 'rate_venue', 'validationErrors' => $this->VenueReview->validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}

		// Get the average rating for a venue
		$avg_rating = $this->VenueReview->calculate_avg_rating($venue_id);

		// Update the new average rating value of the venue
		$input_venue = array('id' => $venue_id, 'avg_rating' => $avg_rating);
		if(!$this->Venue->save($input_venue, $validate = false)) {
			// Rollback transaction
			$this->Venue->rollback();
			$response = array('status' => 'error', 'operation' => 'rate_venue', 'validationErrors' => $this->Venue->validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}


		if(!isset($rate_id))
			$rate_id = $this->VenueReview->getLastInsertId();

		// Commit transaction
		$this->Venue->commit();

		$response = array(
			'status' => 'success',
			'operation' => 'rate_venue',
			'data' => array('msg' => 'Rating has been saved', 'rate_id' => $rate_id, 'data' => $this->data),
			);
		$this->set(array('response' => $response, '_serialize' => array('response')));
	}


	/**
	 ** @purpose Get Review of Venues
	 ** @input auth_type, auth_key, venue_id
	 ** @output
	 **/
	// @09/09/2014 by 037,
	public function get_review() {
		$auth_type = $this->getParams('auth_type', 'web');
		$auth_key = $this->getParams('auth_key');
		$venue_id = $this->getParams('venue_id');

		$validationErrors = array();
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
				$validationErrors['venue_id'] = "Venue does not exist";
		}

		if(!empty($validationErrors)) {
			$response = array('status' => 'error', 'operation' => 'get_review', 'validationErrors' => $validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}

		$options = array('conditions' => array('VenueReview.user_id' => $user_id, 'VenueReview.venue_id' => $venue_id));

		$reviewData = $this->VenueReview->find('first', $options);


		$data = array();
		if(!empty($reviewData)) {
			$data['review_id'] = $reviewData['VenueReview']['id'];
			$data['rating'] = $reviewData['VenueReview']['rating'];
			$data['comment'] = $reviewData['VenueReview']['comment'];
		}
		else {
			$data['rating'] = 0;
			$data['comment'] = '';
		}

		$response = array(
			'status' => 'success',
			'operation' => 'get_review',
			'data' => $data,
			);
		$this->set(array('response' => $response, '_serialize' => array('response')));
	}
/****************************12-12-2016**********************************************************************/
public function capture_rating() {	
	$auth_type = $this->getParams('auth_type', 'web');
		$auth_key = $this->getParams('auth_key');
		$venue_id = $this->getParams('venue_id');
		$rate_cat_id = $this->getParams('rate_cat_id');
		//$rating_cat = $this->getParams('rating_cat');
		$review = $this->getParams('review');
		
		$validationErrors = array();

		if(empty($auth_key))
			$validationErrors['auth_key'] = 'Auth key is blank';
		else {
			$user_id = $this->User->get_user_id($auth_key);
			if(empty($user_id))
				$validationErrors['auth_key'] = 'Invalid auth key';
		}
		if(empty($venue_id))
			$validationErrors['venue_id'] = 'Venue is not selected';
		else {
			$venueExists = $this->Venue->findByid($venue_id);
			if(empty($venueExists))
				$validationErrors['venue_id'] = 'Venue does not exists';
		}
		
		if(!empty($validationErrors)) {
			$response = array('status' => 'error', 'operation' => 'capture_rating', 'validationErrors' => $validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}
		
		if(count($_REQUEST['rate_cat_id']) == 0) {
			$response = array('status' => 'You did not rated anything?', 'operation' => 'capture_rating');
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}
		$date = date('Y-m-d H:i:s');
		//print_r($_REQUEST['rate_cat_id']);return
		$experience_rate = 0;
	    $service_rate = 0;
	    $atmosphere_rate = 0;
	    $crowd_rate = 0;
	    $profile_info_rate = 0;
	    $food_rate = 0;
		# get overall rating
		$total = 0;
		$total_rating = 0;
		foreach($_REQUEST['rate_cat_id'] as $category_id => $rating):
			if($rating > 0) {
				$total_rating = $total_rating + $rating;
				$total++;
			}
		endforeach;
		if($total_rating > 0) {
			$total_rating = floor($total_rating/$total);
			if($total_rating > 5) $total_rating = 5;
		}
		$sql = "DELETE FROM venue_reviews WHERE user_id='$user_id' AND venue_id='$venue_id'";
		$this->VenueReview->query($sql);
		//print_r($_REQUEST['rate_cat_id']);
		foreach($_REQUEST['rate_cat_id'] as $category_id => $rating):
		
			if(empty($category_id)) continue;
				# Get category_id
				$sql = "SELECT rate_cat_id FROM rating_categories WHERE internal_name='".trim($category_id)."'";

				$array_ids = $this->VenueReview->query($sql);
				$rate_cat_id = isset($array_ids[0]['rating_categories']['rate_cat_id'])?$array_ids[0]['rating_categories']['rate_cat_id']:'';
				if(empty($rate_cat_id)) continue;
				#
				$sql = "INSERT INTO `venue_reviews`(`user_id`,`venue_id`, `comment`, `rating`, `rate_cat_id`,`created`, `rating_cat`) VALUES ('".$user_id."','".$venue_id."','".$review."','".$total_rating."','".$rate_cat_id."','".$date."','".$rating."')";
		        	
			    if($this->VenueReview->query($sql)) { } 
				
				$avg_rating = $this->VenueReview->calculate_avg_rating_new($venue_id, $rate_cat_id);
				if($category_id == 'experience_rate') $experience_rate = $avg_rating;
				if($category_id == 'service_rate') $service_rate = $avg_rating;
				if($category_id == 'atmosphere_rate') $atmosphere_rate = $avg_rating;
				if($category_id == 'crowd_rate') $crowd_rate = $avg_rating;	
				if($category_id == 'profile_info_rate') $profile_info_rate = $avg_rating;
				if($category_id == 'food_rate') $food_rate = $avg_rating;			
				 
		endforeach;
		
		// Get the average rating for a venue
		  
		   //$sql3 = "SELECT SUM(rating_cat) AS total_rating FROM venue_reviews WHERE venue_id = '".$venue_id."'";
          //$avg_rating = $this->VenueReview->calculate_avg_rating($venue_id); 

		  
		  
		
		 $avg_rating = ($experience_rate + $service_rate + $atmosphere_rate + $crowd_rate + $profile_info_rate + $food_rate)/6;
				
		 
		$sql1 = "UPDATE venues SET 
        user_id = '".$user_id."',
        experience_rate = '".$experience_rate."',
        service_rate= '".$service_rate."',
        atmosphere_rate='".$atmosphere_rate."',
        crowd_rate= '".$crowd_rate."',
        profile_info_rate= '".$profile_info_rate."',
        food_rate= '".$food_rate."',
        avg_rating= '".$avg_rating."',
        updated= '".$date."' 
        WHERE `id` = '".$venue_id."'";
        if($this->Venue->query($sql1)) {
					   
		  $rate_id = $this->Venue->getLastInsertId();
		}
		//return;
		
		$response = array(
			'status' => 'success',
			'operation' => 'capture_rating',
			'data' => array('msg' => 'Rating has been saved','data' => $this->data),
			);
		$this->set(array('response' => $response, '_serialize' => array('response')));
	
}	
/**
	 ** @purpose Get Review of Venues
	 ** @input auth_type, auth_key, venue_id
	 ** @output
	 **/
	// @12/12/2016 by 079,
	public function get_venue_rating() {
		$auth_type = $this->getParams('auth_type', 'web');
		$auth_key = $this->getParams('auth_key');
		$venue_id = $this->getParams('venue_id');

		$validationErrors = array();
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
				$validationErrors['venue_id'] = "Venue does not exist";
		}

		if(!empty($validationErrors)) {
			$response = array('status' => 'error', 'operation' => 'get_venue_rating', 'validationErrors' => $validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}
       
 
        $options = array('conditions' => array('Venue.id' => $venue_id),
						 'fields'=> array('Venue.*'));

		$venue_Data = $this->Venue->find('first',$options);
		//print_r($venue_Data); exit;
		$data = array();
		if(!empty($venue_Data)) {
			$data['id'] = $venue_Data['Venue']['id'];
			$data['experience_rate'] = round($venue_Data['Venue']['experience_rate']);
			$data['service_rate'] = round($venue_Data['Venue']['service_rate']);
			$data['atmosphere_rate'] = round($venue_Data['Venue']['atmosphere_rate']);
			$data['crowd_rate'] = round($venue_Data['Venue']['crowd_rate']);
			$data['profile_info_rate'] = round($venue_Data['Venue']['profile_info_rate']);
			$data['food_rate'] = round($venue_Data['Venue']['food_rate']);
		}
		else {
			$data['experience_rate'] = '0';
			$data['service_rate'] = '0';
			$data['atmosphere_rate'] = '0';
			$data['crowd_rate'] = '0';
			$data['profile_info_rate'] = '0';
			$data['food_rate'] = '0';
		}
		$response = array(
			'status' => 'success',
			'operation' => 'get_venue_rating',
			'data' => $data,
			);
		$this->set(array('response' => $response, '_serialize' => array('response')));
	}
/*****************************12-12-2016*********************************************************************/
	/**
	 ** @purpose get the venue photos
	 ** @input auth_type (android/iphone/web), venue_id
	 ** @output Array of venue photos
	 */
	public function get_venue_photos($venue_id) {
		//$venue_id = $this->getParams('venue_id');

		$validationErrors = array();
		if(empty($venue_id))
			$validationErrors['venue_id'] = 'Venue is not selected';
		else {
			// Check if the venue is available or not
			$options = array(
				'fields' => array('Venue.id', 'Venue.name', 'Venue.address', 'Venue.state', 'Venue.city', 'Venue.zip', 'Venue.phone1 AS phone', 'Venue.phone2 AS mobile', 'Venue.avg_rating', 'Venue.prefered', 'Venue.description', 'Venue.logo', 'Venue.details', 'Venue.promotions'),
				'conditions' => array('Venue.id' => $venue_id),
				);
			$venueExists = $this->Venue->find('first', $options);
			if(empty($venueExists))
				$validationErrors['venue_id'] = 'Venue does not exists';
		}

		if(!empty($validationErrors)) {
			$response = array('status' => 'error', 'operation' => 'get_venue_photos', 'validationErrors' => $validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}

		// Parse the venue photos
		$photos = isset($venueExists['VenuePhoto']) ? $venueExists['VenuePhoto'] : array();
		$venue_photos = array();
		if(!empty($photos)) {
			$base_url = Router::url('/', true);
			foreach($photos as $row) {
				$imageName 	= $base_url . $row['photo'];
				$path_parts = pathinfo($imageName);

				// **************** for thumb and large image **********************
				$thumb_imageName 	= $base_url."img/default/NAlarge.png";
				$large_imageName 	= $base_url."img/default/NAthumb.png";

				if(file_exists(WWW_ROOT."uploads/venues/".$path_parts['filename']."_thumb.".$path_parts['extension'])){
					$thumb_imageName 	= $path_parts['dirname']."/".$path_parts['filename']."_thumb.".$path_parts['extension'];
				}
				if(file_exists(WWW_ROOT."uploads/venues/".$path_parts['filename']."_large.".$path_parts['extension'])){
					$large_imageName 	= $path_parts['dirname']."/".$path_parts['filename']."_large.".$path_parts['extension'];
				}


				$row_data = array(
					'id'	=> $row['id'],
					'photo'	=> $base_url . $row['photo'],
					'thumb_image'	=> $thumb_imageName,
					'large_image'	=> $large_imageName
					);
				$venue_photos[] = $row_data;
			}
		}
		return $venue_photos;


	}


	/**
	 ** @purpose get the venue promos
	 ** @input auth_type (android/iphone/web), venue_id
	 ** @output Array of venue promos
	 */
	public function get_venue_promos($venue_id) {
		//$venue_id = $this->getParams('venue_id');

		$validationErrors = array();
		if(empty($venue_id))
			$validationErrors['venue_id'] = 'Venue is not selected';
		else {
			// Check if the venue is available or not
			$options = array(
				'fields' => array('Venue.id', 'Venue.name', 'Venue.address', 'Venue.state', 'Venue.city', 'Venue.zip', 'Venue.phone1 AS phone', 'Venue.phone2 AS mobile', 'Venue.avg_rating', 'Venue.prefered', 'Venue.description', 'Venue.logo', 'Venue.details', 'Venue.promotions'),
				'conditions' => array('Venue.id' => $venue_id),
				);
			$venueExists = $this->Venue->find('first', $options);
			if(empty($venueExists))
				$validationErrors['venue_id'] = 'Venue does not exists';
		}

		if(!empty($validationErrors)) {
			$response = array('status' => 'error', 'operation' => 'get_venue_promos', 'validationErrors' => $validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}

		// Parse the venue promos
		$promos = isset($venueExists['VenuePromotion']) ? $venueExists['VenuePromotion'] : array();
		$venue_promos = array();
		if(!empty($promos)) {
			$base_url = Router::url('/', true);
			foreach($promos as $row) {
				$imageName 	= $base_url . $row['promotion'];
				$path_parts = pathinfo($imageName);

				// **************** for thumb and large image **********************
				$thumb_imageName 	= $base_url."img/default/NAlarge.png";
				$large_imageName 	= $base_url."img/default/NAthumb.png";

				if(file_exists(WWW_ROOT."uploads/venues/".$path_parts['filename']."_thumb.".$path_parts['extension'])){
					$thumb_imageName 	= $path_parts['dirname']."/".$path_parts['filename']."_thumb.".$path_parts['extension'];
				}
				if(file_exists(WWW_ROOT."uploads/venues/".$path_parts['filename']."_large.".$path_parts['extension'])){
					$large_imageName 	= $path_parts['dirname']."/".$path_parts['filename']."_large.".$path_parts['extension'];
				}


				$row_data = array(
					'id'	=> $row['id'],
					'promotion'	=> $base_url . $row['promotion'],
					'thumb_image'	=> $thumb_imageName,
					'large_image'	=> $large_imageName
					);
				$venue_promos[] = $row_data;
			}
		}
		return $venue_promos;

	}


	/**
	 ** @purpose get the venue floor_plans
	 ** @input auth_type (android/iphone/web), venue_id
	 ** @output Array of venue floor_plans
	 */
	public function get_venue_floor_plans($venue_id) {
		//$venue_id = $this->getParams('venue_id');

		$validationErrors = array();
		if(empty($venue_id))
			$validationErrors['venue_id'] = 'Venue is not selected';
		else {
			// Check if the venue is available or not
			$options = array(
				'fields' => array('Venue.id', 'Venue.name', 'Venue.address', 'Venue.state', 'Venue.city', 'Venue.zip', 'Venue.phone1 AS phone', 'Venue.phone2 AS mobile', 'Venue.avg_rating', 'Venue.prefered', 'Venue.description', 'Venue.logo', 'Venue.details', 'Venue.promotions'),
				'conditions' => array('Venue.id' => $venue_id),
				);
			$venueExists = $this->Venue->find('first', $options);
			if(empty($venueExists))
				$validationErrors['venue_id'] = 'Venue does not exists';
		}

		if(!empty($validationErrors)) {
			$response = array('status' => 'error', 'operation' => 'get_venue_floor_plans', 'validationErrors' => $validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}

		// Parse the venue floor_plans
		$floor_plans = isset($venueExists['VenueFloorPlan']) ? $venueExists['VenueFloorPlan'] : array();
		//print_r($floor_plans); exit;
		$venue_floor_plans = array();
		if(!empty($floor_plans)) {
			$base_url = Router::url('/', true);
			foreach($floor_plans as $row) {
				$imageName 	= $base_url . $row['floor_plan'];
				$path_parts = pathinfo($imageName);

				// **************** for thumb and large image **********************
				$thumb_imageName 	= $base_url."img/default/NAlarge.png";
				$large_imageName 	= $base_url."img/default/NAthumb.png";

				if(file_exists(WWW_ROOT."uploads/venues/".$path_parts['filename']."_thumb.".$path_parts['extension'])){
					$thumb_imageName 	= $path_parts['dirname']."/".$path_parts['filename']."_thumb.".$path_parts['extension'];
				}
				if(file_exists(WWW_ROOT."uploads/venues/".$path_parts['filename']."_large.".$path_parts['extension'])){
					$large_imageName 	= $path_parts['dirname']."/".$path_parts['filename']."_large.".$path_parts['extension'];
				}


				$row_data = array(
					'id'	=> $row['id'],
					'floor_plan'	=> $base_url . $row['floor_plan'],
					'thumb_image'	=> $thumb_imageName,
					'large_image'	=> $large_imageName
					);
				$venue_floor_plans[] = $row_data;
			}
		}
		return $venue_floor_plans;

	}



	public function test_photo_api(){
		$file_name = "1452_604151131_0.376114001410446927.jpg";
		$file_loc = 'uploads'.DS.'venues'.DS;;
		list($width, $height, $type, $attr) = getimagesize("/var/www/AIMS/shoutoutcity/www/api/v1.0/webroot/uploads/venues/1452_604151131_0.376114001410446927.jpg");
					$source_image = $file_loc . $file_name;
					$destination_image = $file_loc . $file_name;

					$new_compressed_image = $this->Image->generateCompressImagePhoto($source_image, $destination_image);
					$attachmentSize = array('width' => $width, 'height' => $height);

					$new_image = array();
					$new_image['Image']['name']['name'] 	= $file_name;
					$new_image['Image']['name']['type'] 	= "image/jpg";
					$new_image['Image']['name']['tmp_name'] = $source_image;
					$new_image['Image']['name']['error'] 	= "";
					$new_image['Image']['name']['size'] 	= $attachmentSize;
					print_r($new_image);

					$image_path_large = $this->Image->upload_image_and_thumbnail($new_image,"name",500,600,true, "_large");
					$image_path_thumb = $this->Image->upload_image_and_thumbnail($new_image,"name",161,161,true, "_thumb");
					echo $image_path_large;

		exit;
	}

	/**
	 ** @purpose save the venue photos
	 ** @input auth_type (android/iphone/web), venue_id
	 ** @output Array of venue photos
	 */
	public function save_venue_photos($venue_id, $user_id, $photos) {
		$photo_ids = array();

		foreach($photos as $key => $photo) {

			$file_loc = 'uploads'.DS.'venues'.DS;
			$file_name = $venue_id.'_' .$user_id.'_' . time() . '.jpg';
			$file_dir = WWW_ROOT . $file_loc;
			$file_url = Router::url('/', true) . $file_loc . $file_name;
			$fname = $file_dir . $file_name;

			if(!is_dir($file_dir))
				mkdir($file_dir, 0755, true);

			$raw_file = base64_decode($photo);
			$handle = fopen($fname, "w+");
			fwrite($handle, $raw_file);
			fclose($handle);

			if(!file_exists($fname) || filesize($fname) <= 0) {
				$validationErrors['photos'][$key + 1] = 'File could not be uploaded';
				continue;
			}

			$venue_photo_input = array(
				'venue_id' 	=> $venue_id,
				'user_id' 	=> $user_id,
				'photo' 	=> $file_loc . $file_name,
				'created' 	=> date('Y-m-d H:i:s')
			);

			$this->VenuePhoto->create();
			if(!$this->VenuePhoto->save($venue_photo_input)) {
				$validationErrors['photos'][$key + 1] = $this->VenuePhoto->validationErrors;
				continue;
			}
			else {
				//$upload_dir = $file_loc . $file_name;
				//chmod($upload_dir, 0755);
				//-------------- image compression start -----------------
				// here we are replacing the compressed image with the same orignal image
				/*$source_image = $file_dir.$file_name;
				$destination_image = $file_dir.$file_name;
				$new_compressed_image = $this->Image->generateCompressImagePhoto($source_image, $destination_image);*/
				//-------------- image compression end -----------------

				//-------------- make the thumbnail start -----------------
				/*$new_image = array();
				$new_image['Image']['name']['name'] 	= $file_name;
				$new_image['Image']['name']['type'] 	= $attachmentType;
				$new_image['Image']['name']['tmp_name'] = $file_dir.$file_name;
				$new_image['Image']['name']['error'] 	= $attachmentError;
				$new_image['Image']['name']['size'] 	= $attachmentSize;

				$image_path_large = $this->Image->upload_image_and_thumbnail_test($new_image,"name",500,600,true, "_large");
				$image_path_thumb = $this->Image->upload_image_and_thumbnail_test($new_image,"name",161,161,true, "_thumb");*/
				//-------------- make the thumbnail end -----------------
			}

			$photo_ids[] = array(
				'photo_id' => $this->VenuePhoto->getLastInsertId(),
				'photo_url' => $file_url,
				);

		}
 		return $photo_ids;
	}

	/**
	 ** @purpose set the venue table type "C" for "Completed Venue", "D" for "Draft Venue"
	 ** @input
	 ** @output
	 */
	public function set_venue_draft_script() {
		$this->Venue = ClassRegistry::init('Venue');
		$this->CategoriesVenue = ClassRegistry::init('CategoriesVenue');

		//$venues = $this->Venue->find('all', array('limit' => 4));
		$venues = $this->Venue->find('all');
		foreach($venues as $venues_val){
			$venue_id = $venues_val['Venue']['id'];
			$categories = $this->CategoriesVenue->find('first', array('conditions' => array('CategoriesVenue.venue_id' => $venue_id)));

			if(isset($categories['CategoriesVenue']) && !empty($categories['CategoriesVenue']))
				$type = "C";
			else
				$type = "D";

			$venue_input = array(
				'id'		=> $venue_id,
				'venue_type'	=> $type,
			);

			$this->Venue->id = $venue_id;
			if(!$this->Venue->saveField('venue_type', $type)) {
				echo "no";
			} else {
				echo "yes";
			}

			//pr($this->Venue->getDataSource()->getLog());
		}
		exit;
	}


	/**
	 ** @purpose upload venue promos
	 ** @input auth_type, auth_key, venue_id, promos (array)
	 ** @output Array of data
	 */
	public function upload_promos() {
		$auth_type = $this->getParams('auth_type', 'web');
		$auth_key = $this->getParams('auth_key');
		$venue_id = $this->getParams('venue_id');
		$promos = $this->getParams('promos', array());


		// Check If Empty
		$validationErrors = array();
		if(empty($auth_key))
			$validationErrors['auth_key'] = 'Auth key is blank';
		else {
			$user_id = $this->User->get_user_id($auth_key);
			if(empty($user_id))
				$validationErrors['auth_key'] = 'Invalid auth key';
		}
		if(empty($venue_id))
			$validationErrors['venue_id'] = 'Venue is not selected';

		if(empty($validationErrors)) {
			// Check if the venue is available or not
			$venueExists = $this->Venue->findByid($venue_id);
			if(empty($venueExists))
				$validationErrors['venue_id'] = 'Venue does not exists';
		}

		if(empty($promos))
			$validationErrors['promos'] = 'Promotions are not selected';
		else {
			foreach($promos as $key => $promo) {
				if(empty($promo)) {
					$validationErrors['promos'][$key + 1] = 'Promotion is not selected';
				}
			}
		}

		if(!empty($validationErrors)) {
			$response = array('status' => 'error', 'operation' => 'upload_promos', 'validationErrors' => $validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}

		$promo_ids = array();
		foreach($promos as $key => $promo) {
			$file_loc = 'uploads'.DS.'venues'.DS;
			$file_name = $venue_id.'_' . rand()."_".microtime() . '.jpg';
			$file_name 		= str_replace(" ", '', $file_name);

			$file_dir = WWW_ROOT . $file_loc;
			$file_url = Router::url('/', true) . $file_loc . $file_name;
			$fname = $file_dir . $file_name;

			if(!is_dir($file_dir))
				mkdir($file_dir, 0755, true);

			$raw_file = base64_decode($promo);
			$handle = fopen($fname, "w+");
			fwrite($handle, $raw_file);
			fclose($handle);

			if(!file_exists($fname) || filesize($fname) <= 0) {
				$validationErrors['promos'][$key + 1] = 'File could not be uploaded';
				continue;
			}

			############### Pause #########
			$venue_promo_input = array(
				'venue_id' 	=> $venue_id,
				'user_id' 	=> $user_id,
				'promotion' 	=> $file_loc . $file_name,
				'created' 	=> date('Y-m-d H:i:s')
			);
			$this->VenuePromotion->create();
			if(!$this->VenuePromotion->save($venue_promo_input)) {
				$validationErrors['promos'][$key + 1] = $this->VenuePromotion->validationErrors;
				continue;
			}
			else{
					list($width, $height, $type, $attr) = getimagesize($file_dir . $file_name);
					$source_image = $file_loc . $file_name;
					$destination_image = $file_loc . $file_name;

					$new_compressed_image = $this->Image->generateCompressImagePhoto($source_image, $destination_image);
					$attachmentSize = array('width' => $width, 'height' => $height);

					$new_image = array();
					$new_image['Image']['name']['name'] 	= $file_name;
					$new_image['Image']['name']['type'] 	= "image/jpg";
					$new_image['Image']['name']['tmp_name'] = $source_image;
					$new_image['Image']['name']['error'] 	= "";
					$new_image['Image']['name']['size'] 	= $attachmentSize;

					$image_path_large = $this->Image->upload_image_and_thumbnail($new_image,"name",500,600,true, "_large");
					$image_path_thumb = $this->Image->upload_image_and_thumbnail($new_image,"name",161,161,true, "_thumb");

				}

			$promo_ids[] = array(
				'promo_id' => $this->VenuePromotion->getLastInsertId(),
				'promo_url' => $file_url,
				);
		}

		if(!empty($validationErrors)) {
			$response = array('status' => 'error', 'operation' => 'upload_promos', 'dataErrors' => $validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}


		// SuccessResponse
		$response = array(
			'status' => 'success',
			'operation' => 'upload_promos',
			'data' => array('msg' => 'Promotions uploaded successfully', 'promo_ids' => $promo_ids),
			);
		$this->set(array(
			'response' => $response,
			'_serialize' => array('response'),
		));
	}


	/**
	 ** @purpose delete the venue promo
	 ** @input auth_type, venue_id, promo_id
	 ** @output
	 */
	public function delete_promos() {
		$venue_id = $this->getParams('venue_id');
		$promo_id = $this->getParams('promo_id');

		$validationErrors = array();
		if(empty($venue_id))
			$validationErrors['venue_id'] = 'Venue is not selected';
		else {
			// Check if the venue is available or not
			$venueExists = $this->Venue->findByid($venue_id);
			if(empty($venueExists))
				$validationErrors['venue_id'] = 'Venue does not exists';
		}
		if(empty($promo_id))
			$validationErrors['promo_id'] = 'Venue promotion is not selected';
		else {
			// Check if the venue promo is available or not
			$options = array('conditions' => array('VenuePromotion.venue_id' => $venue_id, 'VenuePromotion.id' => $promo_id));
			$venuePromoExists = $this->VenuePromotion->find('first', $options);
			if(empty($venuePromoExists))
				$validationErrors['promo_id'] = 'Venue promotion does not exists';
		}

		if(!empty($validationErrors)) {
			$response = array('status' => 'error', 'operation' => 'delete_promos', 'validationErrors' => $validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}


		if(!$this->VenuePromotion->delete($promo_id)) {
			$response = array('status' => 'error', 'operation' => 'delete_promos', 'dataErrors' => $this->VenuePromotion->validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}


		// Unlink the venue promo
		$venue_promo = $venuePromoExists['VenuePromotion']['promotion'];
		$venue_path = WWW_ROOT . DS . $venue_promo;

		$path_parts 			= pathinfo($venue_promo);
		$venuePromoLarge 		= WWW_ROOT . DS . $path_parts['dirname'].'/'.$path_parts['filename'].'_large.'.$path_parts['extension'];
		$venuePromoThumb 		= WWW_ROOT . DS . $path_parts['dirname'].'/'.$path_parts['filename'].'_thumb.'.$path_parts['extension'];
		@unlink($venuePromoLarge);
		@unlink($venuePromoThumb);
		@unlink($venue_path);


		// SuccessResponse
		$response = array(
			'status' => 'success',
			'operation' => 'delete_promos',
			'data' => array('msg' => 'Venue promotion has been deleted successfully', 'data' => $this->data),
			);
		$this->set(array('response' => $response, '_serialize' => array('response')));
	}


	/**
	 ** @purpose upload venue floor plans
	 ** @input auth_type, auth_key, venue_id, floor_plans (array)
	 ** @output Array of data
	 */
	public function upload_floor_plans() {
		$auth_type = $this->getParams('auth_type', 'web');
		$auth_key = $this->getParams('auth_key');
		$venue_id = $this->getParams('venue_id');
		$floor_plans = $this->getParams('floor_plans', array());


		// Check If Empty
		$validationErrors = array();
		if(empty($auth_key))
			$validationErrors['auth_key'] = 'Auth key is blank';
		else {
			$user_id = $this->User->get_user_id($auth_key);
			if(empty($user_id))
				$validationErrors['auth_key'] = 'Invalid auth key';
		}
		if(empty($venue_id))
			$validationErrors['venue_id'] = 'Venue is not selected';

		if(empty($validationErrors)) {
			// Check if the venue is available or not
			$venueExists = $this->Venue->findByid($venue_id);
			if(empty($venueExists))
				$validationErrors['venue_id'] = 'Venue does not exists';
		}

		if(empty($floor_plans))
			$validationErrors['floor_plans'] = 'Floor Plan are not selected';
		else {
			foreach($floor_plans as $key => $floor_plan) {
				if(empty($floor_plan)) {
					$validationErrors['floor_plans'][$key + 1] = 'Floor Plan is not selected';
				}
			}
		}

		if(!empty($validationErrors)) {
			$response = array('status' => 'error', 'operation' => 'upload_floor_plans', 'validationErrors' => $validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}

		$floor_plan_ids = array();
		foreach($floor_plans as $key => $floor_plan) {
			$file_loc = 'uploads'.DS.'venues'.DS;
			$file_name = $venue_id.'_' . rand()."_".microtime() . '.jpg';
			$file_name 		= str_replace(" ", '', $file_name);

			$file_dir = WWW_ROOT . $file_loc;
			$file_url = Router::url('/', true) . $file_loc . $file_name;
			$fname = $file_dir . $file_name;

			if(!is_dir($file_dir))
				mkdir($file_dir, 0755, true);

			$raw_file = base64_decode($floor_plan);
			$handle = fopen($fname, "w+");
			fwrite($handle, $raw_file);
			fclose($handle);

			if(!file_exists($fname) || filesize($fname) <= 0) {
				$validationErrors['floor_plans'][$key + 1] = 'File could not be uploaded';
				continue;
			}

			$venue_floor_plan_input = array(
				'venue_id' 	=> $venue_id,
				'user_id' 	=> $user_id,
				'floor_plan' 	=> $file_loc . $file_name,
				'created' 	=> date('Y-m-d H:i:s')
			);
			$this->VenueFloorPlan->create();
			if(!$this->VenueFloorPlan->save($venue_floor_plan_input)) {
				$validationErrors['floor_plans'][$key + 1] = $this->VenueFloorPlan->validationErrors;
				continue;
			}
			else{
					list($width, $height, $type, $attr) = getimagesize($file_dir . $file_name);
					$source_image = $file_loc . $file_name;
					$destination_image = $file_loc . $file_name;

					$new_compressed_image = $this->Image->generateCompressImagePhoto($source_image, $destination_image);
					$attachmentSize = array('width' => $width, 'height' => $height);

					$new_image = array();
					$new_image['Image']['name']['name'] 	= $file_name;
					$new_image['Image']['name']['type'] 	= "image/jpg";
					$new_image['Image']['name']['tmp_name'] = $source_image;
					$new_image['Image']['name']['error'] 	= "";
					$new_image['Image']['name']['size'] 	= $attachmentSize;

					$image_path_large = $this->Image->upload_image_and_thumbnail($new_image,"name",500,600,true, "_large");
					$image_path_thumb = $this->Image->upload_image_and_thumbnail($new_image,"name",161,161,true, "_thumb");

				}

			$floor_plan_ids[] = array(
				'floor_plan_id' => $this->VenueFloorPlan->getLastInsertId(),
				'floor_plan_url' => $file_url,
				);
		}

		if(!empty($validationErrors)) {
			$response = array('status' => 'error', 'operation' => 'upload_floor_plans', 'dataErrors' => $validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}


		// SuccessResponse
		$response = array(
			'status' => 'success',
			'operation' => 'upload_floor_plans',
			'data' => array('msg' => 'Floor Plans uploaded successfully', 'floor_plan_ids' => $floor_plan_ids),
			);
		$this->set(array(
			'response' => $response,
			'_serialize' => array('response'),
		));
	}


	/**
	 ** @purpose delete the venue floor plans
	 ** @input auth_type, venue_id, floor_plan_id
	 ** @output
	 */
	public function delete_floor_plans() {
		$venue_id = $this->getParams('venue_id');
		$floor_plan_id = $this->getParams('floor_plan_id');

		$validationErrors = array();
		if(empty($venue_id))
			$validationErrors['venue_id'] = 'Venue is not selected';
		else {
			// Check if the venue is available or not
			$venueExists = $this->Venue->findByid($venue_id);
			if(empty($venueExists))
				$validationErrors['venue_id'] = 'Venue does not exists';
		}
		if(empty($floor_plan_id))
			$validationErrors['floor_plan_id'] = 'Venue Floor Plan is not selected';
		else {
			// Check if the venue floor_plan is available or not
			$options = array('conditions' => array('VenueFloorPlan.venue_id' => $venue_id, 'VenueFloorPlan.id' => $floor_plan_id));
			$venueFloorPlanExists = $this->VenueFloorPlan->find('first', $options);
			if(empty($venueFloorPlanExists))
				$validationErrors['floor_plan_id'] = 'Venue Floor Plan does not exists';
		}

		if(!empty($validationErrors)) {
			$response = array('status' => 'error', 'operation' => 'delete_floor_plans', 'validationErrors' => $validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}


		if(!$this->VenueFloorPlan->delete($floor_plan_id)) {
			$response = array('status' => 'error', 'operation' => 'delete_floor_plans', 'dataErrors' => $this->VenueFloorPlan->validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}


		// Unlink the venue floor_plan
		$venue_floor_plan = $venueFloorPlanExists['VenueFloorPlan']['floor_plan'];
		$venue_path = WWW_ROOT . DS . $venue_floor_plan;

		$path_parts 			= pathinfo($venue_floor_plan);
		$venueFloorPlanLarge 		= WWW_ROOT . DS . $path_parts['dirname'].'/'.$path_parts['filename'].'_large.'.$path_parts['extension'];
		$venueFloorPlanThumb 		= WWW_ROOT . DS . $path_parts['dirname'].'/'.$path_parts['filename'].'_thumb.'.$path_parts['extension'];
		@unlink($venueFloorPlanLarge);
		@unlink($venueFloorPlanThumb);
		@unlink($venue_path);


		// SuccessResponse
		$response = array(
			'status' => 'success',
			'operation' => 'delete_floor_plans',
			'data' => array('msg' => 'Venue Floor Plan has been deleted successfully', 'data' => $this->data),
			);
		$this->set(array('response' => $response, '_serialize' => array('response')));
	}

	/**
	 ** @purpose Return all Bottles data
	 ** @input auth_type, venue_id, bottle_type_id(optional)
	 ** @output Array of data
	 **
	 **/
	//@07/10/2014 by 037, Get Bottles data
	public function get_bottle_boxes(){
		$this->BottleServiceBox = ClassRegistry::init('BottleServiceBox');
		$auth_type = $this->getParams('auth_type', 'web');
		$venue_id = $this->getParams('venue_id');
		$bottle_type_id = $this->getParams('bottle_type_id');

		//Validations
		$validationErrors = array();
		if(empty($venue_id))
			$validationErrors['venue_id'] = 'Venue is not selected';
		else {
			$venueExists = $this->Venue->findByid($venue_id);
			if(empty($venueExists))
				$validationErrors['venue_id'] = 'Venue does not exists';
		}
		//Return Validations
		if(!empty($validationErrors)) {
			$response = array('status' => 'error', 'operation' => 'get_bottle_boxes', 'validationErrors' => $validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}

		$options = array(
				'joins' => array(
					array(
						'table' => 'bottle_types',
						'alias' => 'BottleType',
						'conditions' => 'BottleServiceBox.bottle_type_id = BottleType.id',
						),
					),
				'fields' => array('BottleServiceBox.*', 'BottleType.*'),
		);

		$conditions = array('BottleServiceBox.venue_id' => $venue_id);

		if(!empty($bottle_type_id))
			$conditions['BottleServiceBox.bottle_type_id'] = $bottle_type_id;

		$options['conditions'] = $conditions;

		$total = $this->BottleServiceBox->find('count', $options);

		//If Bottle Types are not found return all records
		if($total == 0) {
			$conditions = array('BottleServiceBox.venue_id' => $venue_id);
			$options['conditions'] = $conditions;
		}

		$result = $this->BottleServiceBox->find('all', $options);

		$bottleBoxes = array();
		if(!empty($result)) {
				foreach($result as $row) {
					$row_data = array(
						'id'	=> $row['BottleServiceBox']['id'],
						'brand' => $row['BottleServiceBox']['brand'],
						'bottle_type_id' => $row['BottleServiceBox']['bottle_type_id'],
						'bottle_type_name' => $row['BottleType']['name'],
						'price'	=> $row['BottleServiceBox']['price'],
						);
					$bottleBoxes[] = $row_data;
				}
			}

		//Return Response
		$response = array(
			'status' => 'success',
			'operation' => 'get_bottle_boxes',
			'data' => $bottleBoxes,
			);
		$this->set(array('response' => $response, '_serialize' => array('response')));
	}

	/**
	 ** @url v1.0/venues/get_bottle_types.json
	 ** @purpose Return all Bottle Tyoes
	 ** @input auth_type, venue_id, bottle_type_id
	 ** @output Array of data
	 **
	 **/
	//@07/10/2014 by 037, Get Bottle Types
	public function get_bottle_types(){
		$this->BottleType = ClassRegistry::init('BottleType');
		$this->BottleServiceBox = ClassRegistry::init('BottleServiceBox');

		$auth_type = $this->getParams('auth_type', 'web');
		$venue_id = $this->getParams('venue_id');
		$bottle_type_id = $this->getParams('bottle_type_id');

		//Validations
		$validationErrors = array();
		if(empty($venue_id))
			$validationErrors['venue_id'] = 'Venue is not selected';
		else {
			$venueExists = $this->Venue->findById($venue_id);
			if(empty($venueExists))
				$validationErrors['venue_id'] = 'Venue does not exists';
		}

		//Return Validations
		if(!empty($validationErrors)) {
			$response = array('status' => 'error', 'operation' => 'get_bottle_types', 'validationErrors' => $validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}

		//$conditions = array('BottleServiceBox.venue_id' => $venue_id, 'BottleServiceBox.bottle_type_id !=' => 8);
		$options = array(
				'conditions' => array('BottleServiceBox.venue_id' => $venue_id, 'BottleServiceBox.bottle_type_id !=' => 8),
				'joins' => array(
					array(
						'table' => 'bottle_types',
						'alias' => 'BottleType',
						'conditions' => 'BottleServiceBox.bottle_type_id = BottleType.id',
						),
					),
				'fields' => array('DISTINCT BottleServiceBox.bottle_type_id', 'BottleServiceBox.other_bottle_type', 'BottleType.name', 'BottleType.alias'),
				'group' => array('BottleServiceBox.bottle_type_id')
		);

		/*
		if(!empty($bottle_type_id))
			$conditions['BottleServiceBox.bottle_type_id'] = $bottle_type_id;
		*/
		/*
		if(!empty($conditions))
			$options['conditions'] = $conditions;
		*/
		$result = $this->BottleServiceBox->find('all', $options);

		$options2 = array(
				'conditions' => array('BottleServiceBox.venue_id' => $venue_id, 'BottleServiceBox.bottle_type_id' => 8),
				'fields' => array('BottleServiceBox.id', 'BottleServiceBox.bottle_type_id', 'BottleServiceBox.other_bottle_type'),
				'group' => array('BottleServiceBox.id')
		);

		$result2 = $this->BottleServiceBox->find('all', $options2);
		//print_r($result2); exit;

		$bottleTypes = array();
		$bottleTypes_other = array();

		if(!empty($result)) {
			foreach($result as $row) {
				$row_data = array(
					'id'	=> $row['BottleServiceBox']['bottle_type_id'],
					'name' => $row['BottleType']['name'],
					'alias' => $row['BottleType']['alias'],
					);
				$bottleTypes[] = $row_data;
			}
		}
		if(!empty($result2)) {
			foreach($result2 as $row2) {
				$row_data2 = array(
					'id'	=> $row2['BottleServiceBox']['bottle_type_id'],
					'name' => $row2['BottleServiceBox']['other_bottle_type'],
					'alias' => 'other',
					);
				$bottleTypes_other[] = $row_data2;
			}
			$bottleTypes = array_merge($bottleTypes, $bottleTypes_other);
		}

		//Return Response
		$response = array(
			'status' => 'success',
			'operation' => 'get_bottle_types',
			'data' => $bottleTypes,
			);
		$this->set(array('response' => $response, '_serialize' => array('response')));
	}


	/**
	 ** @purpose Return all Mixer Tyoes
	 ** @input auth_type, mixer_type_id, keyword
	 ** @output Array of data
	 **
	 **/
	//@07/10/2014 by 037, Get Mixer Types
	public function get_mixer_types(){
		$this->MixerType = ClassRegistry::init('MixerType');
		$auth_type = $this->getParams('auth_type', 'web');
		$keyword = $this->getParams('keyword');
		$mixer_type_id = $this->getParams('mixer_type_id');

		$conditions = array();
		$options = array(
				'fields' => array('MixerType.*'),
		);

		if(!empty($mixer_type_id))
			$conditions['MixerType.id'] = $mixer_type_id;

		if(!empty($keyword))
			$conditions['MixerType.name LIKE'] = '%'.$keyword.'%';

		if(!empty($conditions))
			$options['conditions'] = $conditions;

		$result = $this->MixerType->find('all', $options);

		$mixerTypes= array();

		if(!empty($result)) {
				foreach($result as $row) {
					$row_data = array(
						'id'	=> $row['MixerType']['id'],
						'name' => $row['MixerType']['name'],
						'alias' => $row['MixerType']['alias'],
						);
					$mixerTypes[] = $row_data;
				}
			}

		//Return Response
		$response = array(
			'status' => 'success',
			'operation' => 'get_mixer_types',
			'data' => $mixerTypes,
			);
		$this->set(array('response' => $response, '_serialize' => array('response')));
	}


		/**
	 ** @purpose add mixer service box
	 ** @input auth_type (android/iphone/web), venue_id, mixer, price
	 ** @output
	 */
	public function add_mixer_service_box() {
		$this->Venue = ClassRegistry::init('Venue');
		$this->MixerServiceBox = ClassRegistry::init('MixerServiceBox');

		$venue_id = $this->getParams('venue_id');
		$mixer = $this->getParams('mixer');
		$price = $this->getParams('price', 0.00);

		//Validations
		$validationErrors = array();
		if(empty($venue_id))
			$validationErrors['venue_id'] = 'Venue is not selected';

		if(empty($mixer))
			$validationErrors['mixer'] = 'Mixer name is not set';
		/*
		if($price == '' || !is_numeric($price))
			$validationErrors['price'] = 'Invalid mixer price value';
		*/
		if(empty($validationErrors)) {
			// Check if the selected venue is available or not
			$venueExists = $this->Venue->findByid($venue_id);
			if(empty($venueExists))
				$validationErrors['venue_id'] = 'Venue does not exists';
		}

		if(!empty($validationErrors)) {
			$response = array(
				'status' => 'error',
				'operation' => 'add_mixer_service_box',
				'validationErrors' => $validationErrors,
				);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}

		$today = date('Y-m-d H:i:s');
		$bs_input = array(
			'venue_id'	=> $venue_id,
			'mixer'	=> $mixer,
			'price'	=> $price,
			'created'	=> $today,
			);
		$this->MixerServiceBox->save($bs_input);
		$mixer_service_box_id = $this->MixerServiceBox->getLastInsertId();

		$data = array('msg'	=> 'Changes saved successfully', 'mixer_service_box_id' => $mixer_service_box_id, 'data' => $this->data);
		$response = array(
			'status' => 'success',
			'operation' => 'add_mixer_service_box',
			'data' => $data,
			);

		$this->set(array(
            'response' => $response,
            '_serialize' => array('response'),
        ));
	}


	/**
	 ** @purpose delete mixer service box
	 ** @input auth_type, venue_id, mixer_service_box_id
	 ** @output
	 */
	public function delete_mixer_service_box() {
		$this->MixerServiceBox = ClassRegistry::init('MixerServiceBox');

		$auth_type = $this->getParams('auth_type');
		$venue_id = $this->getParams('venue_id');
		$mixer_service_box_id = $this->getParams('mixer_service_box_id');

		$validationErrors = array();
		if(empty($venue_id))
			$validationErrors['venue_id'] = 'Venue is not selected';
		else {
			// Check if the selected venue is available or not
			$venueExists = $this->Venue->findByid($venue_id);
			if(empty($venueExists))
				$validationErrors['venue_id'] = 'Venue does not exists';
		}
		if(empty($mixer_service_box_id))
			$validationErrors['mixer_service_box_id'] = 'Mixer service box is not selected';
		else {
			// Check if the selected mixer service is correct or not
			$options = array('conditions' => array('venue_id' => $venue_id, 'id' => $mixer_service_box_id));
			$mixerServiceBoxExists = $this->MixerServiceBox->find('first', $options);
			if(empty($mixerServiceBoxExists))
				$validationErrors['mixer_service_box_id'] = 'Invalid mixer service box';
		}

		if(!empty($validationErrors)) {
			$response = array('status' => 'error', 'operation' => 'delete_mixer_service_box', 'validationErrors' => $validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}


		if(!$this->MixerServiceBox->delete($mixer_service_box_id)) {
			$response = array('status' => 'error', 'operation' => 'delete_mixer_service_box', 'dataErrors' => $this->MixerServiceBox->validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}


		$response = array(
			'status' => 'success',
			'operation' => 'delete_mixer_service_box',
			'data' => array('msg' => 'Mixer service box has been deleted successfully', 'data' => $this->data),
			);
		$this->set(array('response' => $response, '_serialize' => array('response')));
	}


	/**
	 ** @purpose Return all Mixers data
	 ** @input auth_type, venue_id, mixer_type_id(optional)
	 ** @output Array of data
	 **
	 **/
	//@07/10/2014 by 037, Get Mixers data
	public function get_mixer_boxes(){
		$this->MixerServiceBox = ClassRegistry::init('MixerServiceBox');
		$auth_type = $this->getParams('auth_type', 'web');
		$venue_id = $this->getParams('venue_id');

		//Validations
		$validationErrors = array();
		if(empty($venue_id))
			$validationErrors['venue_id'] = 'Venue is not selected';
		else {
			$venueExists = $this->Venue->findByid($venue_id);
			if(empty($venueExists))
				$validationErrors['venue_id'] = 'Venue does not exists';
		}
		//Return Validations
		if(!empty($validationErrors)) {
			$response = array('status' => 'error', 'operation' => 'get_mixer_boxes', 'validationErrors' => $validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}

		$options = array(
			'conditions' => array('MixerServiceBox.venue_id' => $venue_id),
			'fields' => array('MixerServiceBox.*'),
		);

		$result = $this->MixerServiceBox->find('all', $options);

		$mixerBoxes = array();
		if(!empty($result)) {
				foreach($result as $row) {
					$row_data = array(
						'id'	=> $row['MixerServiceBox']['id'],
						'mixer' => $row['MixerServiceBox']['mixer'],
						'price'	=> $row['MixerServiceBox']['price'],
						);
					$mixerBoxes[] = $row_data;
				}
			}

		//Return Response
		$response = array(
			'status' => 'success',
			'operation' => 'get_mixer_boxes',
			'data' => $mixerBoxes,
			);
		$this->set(array('response' => $response, '_serialize' => array('response')));
	}

	/**
	 **	@url  v1.0/venues/process_cc_payment.json
	 ** @purpose Process payment details
	 ** @input auth_type, auth_key, payment_details
	 ** payment_details is encrypted string using credit card details json
	 ** encrypted version of {"amount":"100","cc_num":"4111111111111111","exp_date":"0517","cvv":"123"}
	 **	@output status = success/error, transaction_no, response_code
	 **
	 **/
	public function process_cc_payment() {

		try {

			$auth_type 		 = $this->getParams('auth_type', 'web');
			$auth_key 		 = $this->getParams('auth_key');
			$payment_details = $this->getParams('payment_details');

			// Validations
			$validationErrors = array();
			if(empty($auth_key))
				$validationErrors['auth_key'] = 'Auth key is blank';
			else {
				$user_id = $this->User->get_user_id($auth_key);
				$this->User->recursive = -1;
				$user_data = $this->User->findById($user_id);
				if(!isset($user_data['User']['id']))
					$validationErrors['auth_key'] = 'Invalid auth key';
			}

			// Import RNCryptor to decrypt payment details
			App::import('Vendor', 'RNCryptor', array('file' =>'RNCryptor'.DS.'autoload.php'));
			$cryptor = new \RNCryptor\Decryptor();

			// Decrypt payment details
			$decrypted_payment_details = $cryptor->decrypt($payment_details, SECRET_ENCRYPTION_KEY);

			$decrypted_payment_details = json_decode($decrypted_payment_details, true);

			if(empty($decrypted_payment_details) || !is_array($decrypted_payment_details)) {
				$validationErrors['payment_details'] = 'Payment details is blank or invalid';

			} else {
				if(!isset($decrypted_payment_details['amount']) || empty($decrypted_payment_details['amount']))
					$validationErrors['amount'] = 'Invalid amount';

				if(!isset($decrypted_payment_details['cc_num']) || empty($decrypted_payment_details['cc_num']))
					$validationErrors['cc_num'] = 'Credit card number is required field';

				if(!isset($decrypted_payment_details['exp_date']) || empty($decrypted_payment_details['exp_date']))
					$validationErrors['exp_date'] = 'Expiry date is required field';

				if(!isset($decrypted_payment_details['cvv']) || empty($decrypted_payment_details['cvv']))
					$validationErrors['cvv'] = 'CVV is required field';
			}

			if(!empty($validationErrors)) {
				$response = array('status' => 'error', 'operation' => 'process_cc_payment', 'validationErrors' => $validationErrors);
				$this->set(array('response' => $response, '_serialize' => array('response')));
				return;
			}

			App::import('Vendor', 'anet_php_sdk', array('file' =>'anet_php_sdk'.DS.'AuthorizeNet.php'));
			$transaction = new AuthorizeNetAIM;
			$transaction->setSandbox(AUTHORIZENET_SANDBOX);
			$transaction->setFields(
		        array(
			        'amount' => $decrypted_payment_details['amount'],
			        'card_num' => $decrypted_payment_details['cc_num'],
			        'exp_date' => $decrypted_payment_details['exp_date'],
			        'first_name' => $user_data['User']['name'],
			        'last_name' => '',
			        'address' => $user_data['User']['address'],
			        'city' => '',
			        'state' => '',
			        'country' => '',
			        'zip' => '',
			        'email' => $user_data['User']['email'],
			        'card_code' => $decrypted_payment_details['cvv'],
		        )
		    );

			$response = array(
				'operation' => 'process_cc_payment',
			);

			$result = $transaction->authorizeAndCapture();
			if ($result->approved) {
				$transaction_id = $result->transaction_id;

				$response['status'] = 'success';
				$response['data']['transaction_id'] = $transaction_id;
				$response['data']['response_reason_code'] = $result->response_reason_code;
			} else {
				if($result->response_reason_code == '253' || $result->response_reason_code == '252'){
					$response['status'] = 'success';
				} else {
					$response['status'] = 'error';
				}
				$response['data']['response_reason_code'] = $result->response_reason_code;
				$response['data']['message'] = $result->response_reason_text;
			}



		} catch (Exception $e) {

			$response['status'] = 'error';
			$response['data']['message'] = "Invlid payment details";
		}

		$this->set(array(
            'response' => $response,
            '_serialize' => array('response'),
        ));

	}


	/**
	 **	@url  v1.0/venues/place_order.json
	 ** @purpose Save Table Reservation Order
	 ** @input auth_type, auth_key, venue_id, order_id(int/optional) order_date, table_area_id, bottle_data, mixer_data, no_of_males, no_of_females, grand_total, promocode, discount(optional), discounted_price(optional), reservation_fee, gross_total, card_no(optional), transaction_no(optional), response_code(optional)
	 **	@output
	 **
	 **/
	//@07/10/2014 by 037, Place Table Reservation Order
	public function place_order() {
		$this->Order = ClassRegistry::init('Order');
		$this->OrderLine = ClassRegistry::init('OrderLine');
		$this->BottleService = ClassRegistry::init('BottleService');
		$this->TableArea = ClassRegistry::init('TableArea');

		$order_id = $this->getParams('order_id', 0);

		$auth_type = $this->getParams('auth_type', 'web');
		$auth_key = $this->getParams('auth_key');
		$venue_id = $this->getParams('venue_id');
		$order_date = $this->getParams('order_date');
		$no_of_males = $this->getParams('no_of_males', 0);
		$no_of_females = $this->getParams('no_of_females', 0);
		$grand_total = $this->getParams('grand_total');
		$transportation = $this->getParams('transportation', 'n');

		$discount = $this->getParams('discount', 0);
		$discounted_price = $this->getParams('discounted_price', 0);
		$reservation_fee = $this->getParams('reservation_fee');
		$gross_total = $this->getParams('gross_total');
		$promocode = $this->getParams('promocode', 'NA');

		$card_no = $this->getParams('card_no');
		$transaction_no = $this->getParams('transaction_no');
		$response_code =  $this->getParams('response_code');

		$table_area_id = $this->getParams('table_area_id');

		//$limo_service_name = $this->getParams('limo_service_name');
		//$limo_service_price = $this->getParams('limo_service_price');

		$bottle_data = $this->getParams('bottle_data', array());
		$mixer_data = $this->getParams('mixer_data', array());

		// Validations
		$validationErrors = array();
		if(empty($auth_key))
			$validationErrors['auth_key'] = 'Auth key is blank';
		else {
			$user_id = $this->User->get_user_id($auth_key);
			if(empty($user_id))
				$validationErrors['auth_key'] = 'Invalid auth key';
		}

		if(empty($venue_id))
			$validationErrors['venue_id'] = 'Venue is not selected';
		else {
			$venueExists = $this->Venue->findById($venue_id);
			if(empty($venueExists))
				$validationErrors['venue_id'] = 'Venue does not exists';
			else{
				$assigned_user_id = isset($venueExists['Venue']['assigned_user_id']) ? $venueExists['Venue']['assigned_user_id'] : 0;
				$userExists = $this->User->findById($assigned_user_id);
				//print_r($userExists); exit;
				$venue_admin_email = isset($userExists['User']['email']) ? $userExists['User']['email'] : '';
			}
		}

		if(empty($order_date))
			$validationErrors['order_date'] = 'Order Date is not selected';

		if(!is_numeric($no_of_males))
			$validationErrors['no_of_males'] = 'Invalid No of males';

		if(!is_numeric($no_of_females))
			$validationErrors['no_of_females'] = 'Invalid No of Females';

		if(empty($table_area_id))
			$validationErrors['table_area_id'] = 'Table Area is not selected';
		else {
			$tableAreaExists = $this->TableArea->findById($table_area_id);
			if(empty($tableAreaExists))
				$validationErrors['table_area_id'] = 'Invalid Table Area';
			else {
				$valid_count = isset($tableAreaExists['TableArea']['no_of_persons']) ? $tableAreaExists['TableArea']['no_of_persons'] : 0;
				$total_count = $no_of_males + $no_of_females;
				if($total_count > $valid_count)
					$validationErrors['no_of_males'] = 'Total Male Female Count is exceeded';
			}
		}
		/*
		if(empty($table_area))
			$validationErrors['table_area'] = 'Table Area is not selected';

		if(empty($table_area_price) || !is_numeric($table_area_price))
			$validationErrors['table_area_price'] = 'Invalid Table Area Price';

		if(!is_numeric($limo_service_price))
			$validationErrors['limo_service_price'] = 'Invalid Limo Service Price';


		if(is_numeric($no_of_males) && is_numeric($no_of_females)) {
			$valid_count = isset($tableAreaExists['TableArea']['no_of_persons']) ? $tableAreaExists['TableArea']['no_of_persons'] : 0;
			$total_count = $no_of_males + $no_of_females;
			if($total_count > $valid_count)
				$validationErrors['no_of_males'] = 'Total Male Female Count is exceeded';
		}
		*/
		if(empty($grand_total) || !is_numeric($grand_total))
			$validationErrors['grand_total'] = 'Invalid Grand Total';

		if(!is_numeric($discount))
			$validationErrors['discount'] = 'Invalid Discount';

		if(!is_numeric($discounted_price))
			$validationErrors['discounted_price'] = 'Invalid Discounted Price';

		if(empty($reservation_fee) || !is_numeric($reservation_fee))
			$validationErrors['reservation_fee'] = 'Invalid Reservation Price';

		if(empty($gross_total) || !is_numeric($gross_total))
			$validationErrors['gross_total'] = 'Invalid Gross Total';

		if(empty($transportation) || !in_array($transportation, array('y', 'n')))
			$validationErrors['transportation'] = 'Transportation is not selected';

		//Bottle Data Validations
		if(empty($bottle_data))
			$validationErrors['bottle_data'] = 'Bottle Data is not selected' ;
		else {
			if(!is_array($bottle_data))
				$bottle_data  = json_decode($bottle_data, true);

			// User Can not select More than 2 Bottels
			if(count($bottle_data) > 2)
				$validationErrors['bottle_data'] = 'You can not select More than 2 Bottles' ;
			else {
				//Check If Price and Total Price are Set
				foreach($bottle_data as $key => $bottle) {
					if(isset($bottle['price']) && !is_numeric($bottle['price']))
						$validationErrors['bottle_data'][$key+1] = 'Invalid Bottle Price';

					if(isset($bottle['total_price']) && !is_numeric($bottle['total_price']))
						$validationErrors['bottle_data'][$key+1] = 'Invalid Total Bottle Price';
				}
			}
		}

		//Mixer Data Validations
		if(empty($mixer_data))
			$validationErrors['mixer_data'] = 'Mixer Data is not selected' ;
		else {
			if(!is_array($mixer_data))
				$mixer_data  = json_decode($mixer_data, true);

			// User can not select More than 2 Mixers
			if(count($mixer_data) > 2)
				$validationErrors['mixer_data'] = 'You can not select More than 2 Mixers' ;
			else {
				//Check If Price and Total Price are Set
				foreach($mixer_data as $key => $mixer) {
					if(isset($mixer['price']) && !is_numeric($mixer['price']))
						$validationErrors['mixer_data'][$key+1] = 'Invalid Mixer Price';

					if(isset($mixer['total_price']) && !is_numeric($mixer['total_price']))
						$validationErrors['mixer_data'][$key+1] = 'Invalid Total Mixer Price';
				}
			}
		}

		if(!empty($validationErrors)) {
			$response = array('status' => 'error', 'operation' => 'place_order', 'validationErrors' => $validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}

		if($transportation == 'y') $transport_status = 1 ;
		if($transportation == 'n') $transport_status = 0 ;

		$today = date('Y-m-d H:i:s');


		##firstly check if order is approve then do nothing
		$orderExists = $this->Order->findById($order_id);
		if(!empty($orderExists)) {
			if($orderExists['Order']['status'] == 1){
				$data = array('msg'	=> 'Your order is confirmed.', 'order_id' => $orderExists['Order']['id'], 'orderno' => $orderExists['Order']['order_no'],'order_status' => $orderExists['Order']['status']);

				$response = array(
					'status' => 'success',
					'operation' => 'place_order',
					'data' => $data,
					);

				$this->set(array(
					'response' => $response,
					'_serialize' => array('response'),
				));
				return;
			}
		}
		##
		$result = $this->get_pay_status($response_code);

		//if($discount == 0) $discounted_price = $grand_total;

		switch($result['pay_status']) {
			case 'failure':
				$status = 0;
			break;

			case 'success':
				$status = 1;
			break;
		}


		$orderExists = $this->Order->findById($order_id);



		$input = array(
			'venue_id'		=> $venue_id,
			'user_id'		=> $user_id,
		//	'order_no'		=> 'SHO'.mt_rand(),
			'order_date'	=> $order_date,
			'table_area_id'	=> $table_area_id,
			'no_of_males'	=> $no_of_males,
			'no_of_females'	=> $no_of_females,
			'grand_total'	=> $grand_total,
			'promocode'	=> $promocode,
			'discount'	=> $discount,
			'discounted_price'	=> $discounted_price,
			'reservation_fee'	=> $reservation_fee,
			'gross_total'	=> $gross_total,
			'transportation'	=> $transport_status,
			'card_no' => $card_no,
			'transaction_no' => $transaction_no,
			'pay_status' => $result['pay_status'],
			'comment' => $result['comment'],
			'status'	=> $status,
		//	'created'	=> $today
		);


		if(!empty($orderExists)) {
			$input['id'] = $order_id;
			$input['modified'] = $today;
		}
		else {
			$input['order_no'] = 'SHO'.mt_rand();
			$input['created'] = $today;
			$input['modified'] = $today;
		}

		// Begin transaction
		$this->Order->begin();

		if(!$this->Order->save($input)) {
			// Rollback transaction
			$this->Order->rollback();

			$response = array('status' => 'error', 'operation' => 'place_order', 'dataErrors' => $this->Order->validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;

		}

		if(empty($orderExists))
			$order_id = $this->Order->getLastInsertId();
		else
			$this->OrderLine->deleteAll(array('OrderLine.order_id' => $order_id));

		$i = 0;
		$order_line_ids = array();

		//If Table Area exists
		if(!empty($tableAreaExists)) {

			//Add Table Area Item
			$table_data = array(
					'order_id' => $order_id,
					'item' => $tableAreaExists['TableArea']['table_area'],
					'item_type' => 'Table Area',
					'quantity' => 1,
					'price' => $tableAreaExists['TableArea']['price'],
					'total_price' => $tableAreaExists['TableArea']['price'],
			);



			if(!$this->OrderLine->save($table_data)) {
				// Rollback transaction
				$this->Order->rollback();

				$response = array('status' => 'error', 'operation' => 'place_order', 'dataErrors' => $this->OrderLine->validationErrors);
				$this->set(array('response' => $response, '_serialize' => array('response')));
				return;

			}

			$order_line_ids[$i] = $this->OrderLine->getLastInsertId();
			$i++;
		}

		//Add Bottles Item
		if(!empty($bottle_data)) {
			foreach($bottle_data as $bottle) {
				$item = isset($bottle['name']) ? $bottle['name'] : '';
				$item_type = isset($bottle['type']) ? $bottle['type'] : 'Bottle';
				$quantity = isset($bottle['quantity']) ? $bottle['quantity'] : 0;
				$price = isset($bottle['price']) ? $bottle['price'] : 0;
				$total_price = isset($bottle['totalprice']) ? $bottle['totalprice'] : 0;

				$order_line = array(
					'order_id' => $order_id,
					'item' => $item,
					'item_type' => $item_type,
					'quantity' => $quantity,
					'price' => $price,
					'total_price' => $total_price,
				);

				$this->OrderLine->create();

				if(!$this->OrderLine->save($order_line)) {
					// Rollback transaction
					$this->Order->rollback();

					$validationErrors['bottle_data'][$i + 1] = $this->OrderLine->validationErrors;
					continue;
				}
				$order_line_ids[$i] = $this->OrderLine->getLastInsertId();
				$i++;
			}
		}


		if(!empty($validationErrors)) {
			$response = array('status' => 'error', 'operation' => 'place_order', 'validationErrors' => $validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}

		//Add Mixer Items
		if(!empty($mixer_data)) {
			foreach($mixer_data as $mixer) {
				$item = isset($mixer['name']) ? $mixer['name'] : '';
				$item_type = isset($mixer['type']) ? $mixer['type'] : 'Mixer';
				$quantity = isset($mixer['quantity']) ? $mixer['quantity'] : 0;
				$price = isset($mixer['price']) ? $mixer['price'] : 0;
				$total_price = isset($mixer['totalprice']) ? $mixer['totalprice'] : 0;

				$order_line = array(
					'order_id' => $order_id,
					'item' => $item,
					'item_type' => $item_type,
					'quantity' => $quantity,
					'price' => $price,
					'total_price' => $total_price,
				);

				$this->OrderLine->create();

				if(!$this->OrderLine->save($order_line)) {
					// Rollback transaction
					$this->Order->rollback();

					$validationErrors['mixer_data'][$i + 1] = $this->OrderLine->validationErrors;
					continue;
				}
				$order_line_ids[$i] = $this->OrderLine->getLastInsertId();
				$i++;
			}
		}


		if(!empty($validationErrors)) {
			$response = array('status' => 'error', 'operation' => 'place_order', 'validationErrors' => $validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}

		/*
		//Check If Limo Services are present
		if(!empty($limo_service_name) && !empty($limo_service_price)) {
			//Add Limo Service Item
			$limo_service_data = array(
					'order_id' => $order_id,
					'item' => $limo_service_name,
					'item_type' => 'Limo Service',
					'quantity' => 1,
					'price' => $limo_service_price,
					'total_price' => $limo_service_price,
			);

			$this->OrderLine->create();

			if(!$this->OrderLine->save($limo_service_data)) {
				// Rollback transaction
				$this->Order->rollback();

				$response = array('status' => 'error', 'operation' => 'place_order', 'dataErrors' => $this->OrderLine->validationErrors);
				$this->set(array('response' => $response, '_serialize' => array('response')));
				return;

			}
			$order_line_ids[$i] = $this->OrderLine->getLastInsertId();
			$i++;
		}
		*/
		// Commit transaction
		$this->Order->commit();

		// Send Achnowledgement to Ordering User
		$userData = $this->User->findById($user_id);
		$orderData = $this->Order->findById($order_id);
		$itemData = $this->OrderLine->find('all', array('conditions' => array('OrderLine.order_id' => $order_id)));


		if(isset($userData['User']) && !empty($userData['User'])) {			
			//Send Notification mail to user			
			$cc = array();			
			$to = array();
			$name = $userData['User']['name'];
			$email = $userData['User']['email'];
			array_push($to, $email);            #Add user in "to"	
			
			$options = array('conditions' => array('BottleService.venue_id' => $venue_id));
			$bottleServiceExists = $this->BottleService->find('first', $options);
			     
			$from = array(SITE_EMAIL => SITE_TITLE);
			if($result['pay_status'] == 'failure') {
				$subject = 'Shoutoutcity - Order failed';
				$template = 'order_failure';				
			}
			else {
				$subject = 'Shoutoutcity - Order received';
				$template = 'order';

				if(isset($bottleServiceExists['BottleService']) && !empty($bottleServiceExists['BottleService'])) {
					$bottle_email = $bottleServiceExists['BottleService']['email'];
					$bottle_email2 = $bottleServiceExists['BottleService']['email2'];				

					if(!empty($bottle_email))
						array_push($cc, $bottle_email);   #Add email1 in "cc"

					if(!empty($bottle_email2))
						array_push($cc, $bottle_email2);   #Add email2 in "cc"
				}				
			}
			$variables = array(
				'name' => $name,
				'orderData' => $orderData,
				'itemData' => $itemData
				);
			if(!$this->User->send_mail_cc($from, $to, $subject, $template, $variables, $cc)) {
				$response = array('status' => 'error', 'operation' => 'place_order', 'validationErrors' => $this->User->validationErrors);
				$this->set(array('response' => $response, '_serialize' => array('response')));
				return;
			}			
			
			#If order faile then send full sescription in cc to "email1" and "email2"
			if($template == 'order_failure'){
				$cc = array();	
				$to = array();
				$options = array('conditions' => array('BottleService.venue_id' => $venue_id));
				$bottleServiceExists = $this->BottleService->find('first', $options);				
				if(isset($bottleServiceExists['BottleService']) && !empty($bottleServiceExists['BottleService'])) {
					$bottle_email = $bottleServiceExists['BottleService']['email'];
					$bottle_email2 = $bottleServiceExists['BottleService']['email2'];	

					if(!empty($bottle_email))
						array_push($to, $bottle_email);   #Add email1 in "to"

					if(!empty($bottle_email2))
						array_push($to, $bottle_email2);   #Add email2 in "to"

				}			
					 
				$from = array(SITE_EMAIL => SITE_TITLE);
				if($result['pay_status'] == 'failure') {
					$subject = 'Shoutoutcity - Order failed';
					$template = 'order_failure_cc';
				}			
				$variables = array(
					'name' => $name,
					'orderData' => $orderData,
					'itemData' => $itemData
					);
				if(!$this->User->send_mail_cc($from, $to, $subject, $template, $variables, $cc)) {
					$response = array('status' => 'error', 'operation' => 'place_order', 'validationErrors' => $this->User->validationErrors);
					$this->set(array('response' => $response, '_serialize' => array('response')));
					return;
				}
			}
			##
			
/////////////////////////////
			##SMS integration			
			if(isset($bottleServiceExists['BottleService']) && !empty($bottleServiceExists['BottleService'])) {				
				$bottle_phone1 = $bottleServiceExists['BottleService']['phone'];
				$bottle_phone2 = $bottleServiceExists['BottleService']['phone2'];
				
				$remaining = $orderData['Order']['grand_total'] - $orderData['Order']['reservation_fee'];

				$table_area = '';
				$bottle = '';
				$mixture = '';				
				if(!empty($itemData)){
					foreach($itemData as $order_line){
						
						if($order_line['OrderLine']['item_type'] == 'Table Area')
							$table_area = $order_line['OrderLine']['item']." ($".$order_line['OrderLine']['price'].") ";
						elseif($order_line['OrderLine']['item_type'] == 'mixer')
							$mixture = $order_line['OrderLine']['item']." ";
						else
							$bottle = $order_line['OrderLine']['quantity']." - ".$order_line['OrderLine']['item_type']." ".$order_line['OrderLine']['item']." ($".$order_line['OrderLine']['price'].") ";		
					}
				
				
				if($orderData['Order']['status'] == 1) $stat = 'Confirmed';
				elseif($orderData['Order']['status'] == 2) $stat = 'Rejected';
				else $stat = 'Pending';

				$reserve_dt = date("d/m/Y", strtotime($orderData['Order']['order_date']));


$text = "Nightclub - ".$orderData['Venue']['name']."

Guest: ".$orderData['User']['name']."
Reservation Date: ".$$reserve_dt."
Table Area: ".$table_area."
Bottles: ".$bottle."
Mixers: ".$mixture."
Guests: ".$orderData['Order']['no_of_males']." men
".$orderData['Order']['no_of_females']." women
Sub Total: $".number_format($orderData['Order']['grand_total'], 2)."
Reservation Fee: $".number_format($orderData['Order']['reservation_fee'], 2)."
Total Upon Aarival: $".number_format($remaining, 2)."
Status: ".$stat."";
$text = urlencode($text);

				if(!empty($bottle_phone1)){		
					## for first phone 
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, "https://rest.nexmo.com/sms/json?api_key=214a6246&api_secret=38ef268c&from=NEXMO&to=$bottle_phone1&text=$text");
					curl_setopt($ch, CURLOPT_HEADER, 0);
					curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);	
					curl_exec($ch);
					curl_close($ch);
					##
				}			

				if(!empty($bottle_phone2)){		
					## for first phone 
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, "https://rest.nexmo.com/sms/json?api_key=214a6246&api_secret=38ef268c&from=NEXMO&to=$bottle_phone2&text=$text");
					curl_setopt($ch, CURLOPT_HEADER, 0);
					curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);	
					curl_exec($ch);
					curl_close($ch);
					##
				}	
			}		
		}			
		##

/////////////////////////////		

			//Send Notification mail to Bottle Service Mail
			/*$options = array('conditions' => array('BottleService.venue_id' => $venue_id));
			$bottleServiceExists = $this->BottleService->find('first', $options);

			if(isset($bottleServiceExists['BottleService']) && !empty($bottleServiceExists['BottleService'])) {
				$bottle_email = $bottleServiceExists['BottleService']['email'];
				$bottle_email2 = $bottleServiceExists['BottleService']['email2'];
				$to = array();
				$cc = array();

				if(!empty($bottle_email))
					array_push($cc, $bottle_email);

				if(!empty($bottle_email2))
					array_push($cc, $bottle_email2);

				if(!empty($venue_admin_email))
					array_push($cc, $venue_admin_email);

				$from = array(SITE_EMAIL => SITE_TITLE);
				$subject = 'Shoutoutcity - New Table Reservation Request';
				$template = 'order_admin';
				$variables = array(
					'name' => $name,
					'email' => $email,
					'orderData' => $orderData,
					'itemData' => $itemData
				);
				if(!$this->User->send_mail_cc($from, $to, $subject, $template, $variables, $cc)) {
					$response = array('status' => 'error', 'operation' => 'place_order', 'validationErrors' => $this->User->validationErrors);
					$this->set(array('response' => $response, '_serialize' => array('response')));
					return;
				}
			}*/
		}


		$data = array('msg'	=> 'Changes saved successfully', 'order_id' => $orderData['Order']['id'], 'orderno' => $orderData['Order']['order_no']);
		$data = array_merge($data, $result);
		$response = array(
			'status' => 'success',
			'operation' => 'place_order',
			'data' => $data,
			);

		$this->set(array(
            'response' => $response,
            '_serialize' => array('response'),
        ));

	}


	/**
	 **	@url  v1.0/venues/place_order.json
	 ** @purpose Save Table Reservation Order
	 ** @input auth_type, auth_key, venue_id, order_date, no_of_males, no_of_females, grand_total, transportation(y/n), table_area, table_area_price,  limo_service_name(optional), limo_service_price(optional), bottle_data, mixer_data
	 **	@output
	 **
	 **/
	//@07/10/2014 by 037, Place Table Reservation Order
	public function place_order_bkp() {
		$this->Order = ClassRegistry::init('Order');
		$this->OrderLine = ClassRegistry::init('OrderLine');
		$this->BottleService = ClassRegistry::init('BottleService');
		$this->TableArea = ClassRegistry::init('TableArea');

		$auth_type = $this->getParams('auth_type', 'web');
		$auth_key = $this->getParams('auth_key');
		$venue_id = $this->getParams('venue_id');
		$order_date = $this->getParams('order_date');
		$no_of_males = $this->getParams('no_of_males', 0);
		$no_of_females = $this->getParams('no_of_females', 0);
		$grand_total = $this->getParams('grand_total');
		$transportation = $this->getParams('transportation', 'n');

		$discount = $this->getParams('discount', 0);
		$discounted_price = $this->getParams('discounted_price', 0);
		$reservation_fee = $this->getParams('reservation_fee');
		$gross_total = $this->getParams('gross_total');
		$promocode = $this->getParams('promocode', 'NA');

		$card_no = $this->getParams('card_no');
		$transaction_no = $this->getParams('transaction_no');
		$response_code =  $this->getParams('response_code');

		$table_area_id = $this->getParams('table_area_id');

		$limo_service_name = $this->getParams('limo_service_name');
		$limo_service_price = $this->getParams('limo_service_price');

		$bottle_data = $this->getParams('bottle_data', array());
		$mixer_data = $this->getParams('mixer_data', array());

		// Validations
		$validationErrors = array();
		if(empty($auth_key))
			$validationErrors['auth_key'] = 'Auth key is blank';
		else {
			$user_id = $this->User->get_user_id($auth_key);
			if(empty($user_id))
				$validationErrors['auth_key'] = 'Invalid auth key';
		}

		if(empty($venue_id))
			$validationErrors['venue_id'] = 'Venue is not selected';
		else {
			$venueExists = $this->Venue->findById($venue_id);
			if(empty($venueExists))
				$validationErrors['venue_id'] = 'Venue does not exists';
			else{
				$assigned_user_id = isset($venueExists['Venue']['assigned_user_id']) ? $venueExists['Venue']['assigned_user_id'] : 0;
				$userExists = $this->User->findById($assigned_user_id);
				//print_r($userExists); exit;
				$venue_admin_email = isset($userExists['User']['email']) ? $userExists['User']['email'] : '';
			}
		}

		if(empty($order_date))
			$validationErrors['order_date'] = 'Order Date is not selected';

		if(!is_numeric($no_of_males))
			$validationErrors['no_of_males'] = 'Invalid No of males';

		if(!is_numeric($no_of_females))
			$validationErrors['no_of_females'] = 'Invalid No of Females';

		if(empty($table_area_id))
			$validationErrors['table_area_id'] = 'Table Area is not selected';
		else {
			$tableAreaExists = $this->TableArea->findById($table_area_id);
			if(empty($tableAreaExists))
				$validationErrors['table_area_id'] = 'Invalid Table Area';
			else {
				$valid_count = isset($tableAreaExists['TableArea']['no_of_persons']) ? $tableAreaExists['TableArea']['no_of_persons'] : 0;
				$total_count = $no_of_males + $no_of_females;
				if($total_count > $valid_count)
					$validationErrors['no_of_males'] = 'Total Male Female Count is exceeded';
			}
		}
		/*
		if(empty($table_area))
			$validationErrors['table_area'] = 'Table Area is not selected';

		if(empty($table_area_price) || !is_numeric($table_area_price))
			$validationErrors['table_area_price'] = 'Invalid Table Area Price';

		if(!is_numeric($limo_service_price))
			$validationErrors['limo_service_price'] = 'Invalid Limo Service Price';


		if(is_numeric($no_of_males) && is_numeric($no_of_females)) {
			$valid_count = isset($tableAreaExists['TableArea']['no_of_persons']) ? $tableAreaExists['TableArea']['no_of_persons'] : 0;
			$total_count = $no_of_males + $no_of_females;
			if($total_count > $valid_count)
				$validationErrors['no_of_males'] = 'Total Male Female Count is exceeded';
		}
		*/
		if(empty($grand_total) || !is_numeric($grand_total))
			$validationErrors['grand_total'] = 'Invalid Grand Total';

		if(!is_numeric($discount))
			$validationErrors['discount'] = 'Invalid Discount';

		if(!is_numeric($discounted_price))
			$validationErrors['discounted_price'] = 'Invalid Discounted Price';

		if(empty($reservation_fee) || !is_numeric($reservation_fee))
			$validationErrors['reservation_fee'] = 'Invalid Reservation Price';

		if(empty($gross_total) || !is_numeric($gross_total))
			$validationErrors['gross_total'] = 'Invalid Gross Total';

		if(empty($transportation) || !in_array($transportation, array('y', 'n')))
			$validationErrors['transportation'] = 'Transportation is not selected';

		//Bottle Data Validations
		if(empty($bottle_data))
			$validationErrors['bottle_data'] = 'Bottle Data is not selected' ;
		else {
			if(!is_array($bottle_data))
				$bottle_data  = json_decode($bottle_data, true);

			// User Can not select More than 2 Bottels
			if(count($bottle_data) > 2)
				$validationErrors['bottle_data'] = 'You can not select More than 2 Bottles' ;
			else {
				//Check If Price and Total Price are Set
				foreach($bottle_data as $key => $bottle) {
					if(isset($bottle['price']) && !is_numeric($bottle['price']))
						$validationErrors['bottle_data'][$key+1] = 'Invalid Bottle Price';

					if(isset($bottle['total_price']) && !is_numeric($bottle['total_price']))
						$validationErrors['bottle_data'][$key+1] = 'Invalid Total Bottle Price';
				}
			}
		}

		//Mixer Data Validations
		if(empty($mixer_data))
			$validationErrors['mixer_data'] = 'Mixer Data is not selected' ;
		else {
			if(!is_array($mixer_data))
				$mixer_data  = json_decode($mixer_data, true);

			// User can not select More than 2 Mixers
			if(count($mixer_data) > 2)
				$validationErrors['mixer_data'] = 'You can not select More than 2 Mixers' ;
			else {
				//Check If Price and Total Price are Set
				foreach($mixer_data as $key => $mixer) {
					if(isset($mixer['price']) && !is_numeric($mixer['price']))
						$validationErrors['mixer_data'][$key+1] = 'Invalid Mixer Price';

					if(isset($mixer['total_price']) && !is_numeric($mixer['total_price']))
						$validationErrors['mixer_data'][$key+1] = 'Invalid Total Mixer Price';
				}
			}
		}

		if(!empty($validationErrors)) {
			$response = array('status' => 'error', 'operation' => 'place_order', 'validationErrors' => $validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}

		if($transportation == 'y') $transport_status = 1 ;
		if($transportation == 'n') $transport_status = 0 ;

		$today = date('Y-m-d H:i:s');

		$result = $this->get_pay_status($response_code);

		//if($discount == 0) $discounted_price = $grand_total;

		switch($result['pay_status']) {
			case 'failure':
				$status = 0;
			break;

			case 'success':
				$status = 1;
			break;
		}

		$input = array(
			'venue_id'		=> $venue_id,
			'user_id'		=> $user_id,
			'order_no'		=> 'SHO'.mt_rand(),
			'order_date'	=> $order_date,
			'table_area_id'	=> $table_area_id,
			'no_of_males'	=> $no_of_males,
			'no_of_females'	=> $no_of_females,
			'grand_total'	=> $grand_total,
			'promocode'	=> $promocode,
			'discount'	=> $discount,
			'discounted_price'	=> $discounted_price,
			'reservation_fee'	=> $reservation_fee,
			'gross_total'	=> $gross_total,
			'transportation'	=> $transport_status,
			'card_no' => $card_no,
			'transaction_no' => $transaction_no,
			'pay_status' => $result['pay_status'],
			'comment' => $result['comment'],
			'status'	=> $status,
			'created'	=> $today
		);


		// Begin transaction
		$this->Order->begin();

		if(!$this->Order->save($input)) {
			// Rollback transaction
			$this->Order->rollback();

			$response = array('status' => 'error', 'operation' => 'place_order', 'dataErrors' => $this->Order->validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;

		}

		$order_id = $this->Order->getLastInsertId();

		$i = 0;
		$order_line_ids = array();

		//If Table Area exists
		if(!empty($tableAreaExists)) {

			//Add Table Area Item
			$table_data = array(
					'order_id' => $order_id,
					'item' => $tableAreaExists['TableArea']['table_area'],
					'item_type' => 'Table Area',
					'quantity' => 1,
					'price' => $tableAreaExists['TableArea']['price'],
					'total_price' => $tableAreaExists['TableArea']['price'],
			);



			if(!$this->OrderLine->save($table_data)) {
				// Rollback transaction
				$this->Order->rollback();

				$response = array('status' => 'error', 'operation' => 'place_order', 'dataErrors' => $this->OrderLine->validationErrors);
				$this->set(array('response' => $response, '_serialize' => array('response')));
				return;

			}

			$order_line_ids[$i] = $this->OrderLine->getLastInsertId();
			$i++;
		}

		//Add Bottles Item
		if(!empty($bottle_data)) {
			foreach($bottle_data as $bottle) {
				$item = isset($bottle['name']) ? $bottle['name'] : '';
				$item_type = isset($bottle['type']) ? $bottle['type'] : 'Bottle';
				$quantity = isset($bottle['quantity']) ? $bottle['quantity'] : 0;
				$price = isset($bottle['price']) ? $bottle['price'] : 0;
				$total_price = isset($bottle['totalprice']) ? $bottle['totalprice'] : 0;

				$order_line = array(
					'order_id' => $order_id,
					'item' => $item,
					'item_type' => $item_type,
					'quantity' => $quantity,
					'price' => $price,
					'total_price' => $total_price,
				);

				$this->OrderLine->create();

				if(!$this->OrderLine->save($order_line)) {
					// Rollback transaction
					$this->Order->rollback();

					$validationErrors['bottle_data'][$i + 1] = $this->OrderLine->validationErrors;
					continue;
				}
				$order_line_ids[$i] = $this->OrderLine->getLastInsertId();
				$i++;
			}
		}


		if(!empty($validationErrors)) {
			$response = array('status' => 'error', 'operation' => 'place_order', 'validationErrors' => $validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}

		//Add Mixer Items
		if(!empty($mixer_data)) {
			foreach($mixer_data as $mixer) {
				$item = isset($mixer['name']) ? $mixer['name'] : '';
				$item_type = isset($mixer['type']) ? $mixer['type'] : 'Mixer';
				$quantity = isset($mixer['quantity']) ? $mixer['quantity'] : 0;
				$price = isset($mixer['price']) ? $mixer['price'] : 0;
				$total_price = isset($mixer['totalprice']) ? $mixer['totalprice'] : 0;

				$order_line = array(
					'order_id' => $order_id,
					'item' => $item,
					'item_type' => $item_type,
					'quantity' => $quantity,
					'price' => $price,
					'total_price' => $total_price,
				);

				$this->OrderLine->create();

				if(!$this->OrderLine->save($order_line)) {
					// Rollback transaction
					$this->Order->rollback();

					$validationErrors['mixer_data'][$i + 1] = $this->OrderLine->validationErrors;
					continue;
				}
				$order_line_ids[$i] = $this->OrderLine->getLastInsertId();
				$i++;
			}
		}


		if(!empty($validationErrors)) {
			$response = array('status' => 'error', 'operation' => 'place_order', 'validationErrors' => $validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}

		//Check If Limo Services are present
		if(!empty($limo_service_name) && !empty($limo_service_price)) {
			//Add Limo Service Item
			$limo_service_data = array(
					'order_id' => $order_id,
					'item' => $limo_service_name,
					'item_type' => 'Limo Service',
					'quantity' => 1,
					'price' => $limo_service_price,
					'total_price' => $limo_service_price,
			);

			$this->OrderLine->create();

			if(!$this->OrderLine->save($limo_service_data)) {
				// Rollback transaction
				$this->Order->rollback();

				$response = array('status' => 'error', 'operation' => 'place_order', 'dataErrors' => $this->OrderLine->validationErrors);
				$this->set(array('response' => $response, '_serialize' => array('response')));
				return;

			}
			$order_line_ids[$i] = $this->OrderLine->getLastInsertId();
			$i++;
		}

		// Commit transaction
		$this->Order->commit();

		// Send Achnowledgement to Ordering User
		$userData = $this->User->findById($user_id);
		$orderData = $this->Order->findById($order_id);
		$itemData = $this->OrderLine->find('all', array('conditions' => array('OrderLine.order_id' => $order_id)));

		if(isset($userData['User']) && !empty($userData['User'])) {
			$name = $userData['User']['name'];
			$email = $userData['User']['email'];
			$to = array($email => $name);
			$from = array(SITE_EMAIL => SITE_TITLE);
			if($result['pay_status'] == 'failure') {
				$subject = 'Shoutoutcity - Order failed';
				$template = 'order_failure';
			}
			else {
				$subject = 'Shoutoutcity - Order received';
				$template = 'order';
			}
			$variables = array(
				'name' => $name,
				'orderData' => $orderData,
				'itemData' => $itemData
				);
			if(!$this->User->send_mail($from, $to, $subject, $template, $variables)) {
				$response = array('status' => 'error', 'operation' => 'place_order', 'validationErrors' => $this->User->validationErrors);
				$this->set(array('response' => $response, '_serialize' => array('response')));
				return;
			}

			//Send Notification mail to Bottle Service Mail
			$options = array('conditions' => array('BottleService.venue_id' => $venue_id));
			$bottleServiceExists = $this->BottleService->find('first', $options);

			if(isset($bottleServiceExists['BottleService']) && !empty($bottleServiceExists['BottleService'])) {
				$bottle_email = $bottleServiceExists['BottleService']['email'];
				$bottle_email2 = $bottleServiceExists['BottleService']['email2'];
				$to = array();

				if(!empty($bottle_email))
					array_push($to, $bottle_email);

				if(!empty($bottle_email2))
					array_push($to, $bottle_email2);

				if(!empty($venue_admin_email))
					array_push($to, $venue_admin_email);

				$from = array(SITE_EMAIL => SITE_TITLE);
				$subject = 'Shoutoutcity - New Table Reservation Request';
				$template = 'order_admin';
				$variables = array(
					'name' => $name,
					'email' => $email,
					'orderData' => $orderData,
					'itemData' => $itemData
				);
				if(!$this->User->send_mail($from, $to, $subject, $template, $variables)) {
					$response = array('status' => 'error', 'operation' => 'place_order', 'validationErrors' => $this->User->validationErrors);
					$this->set(array('response' => $response, '_serialize' => array('response')));
					return;
				}
			}
		}


		$data = array('msg'	=> 'Changes saved successfully', 'order_id' => $orderData['Order']['id'], 'orderno' => $orderData['Order']['order_no']);
		$data = array_merge($data, $result);
		$response = array(
			'status' => 'success',
			'operation' => 'place_order',
			'data' => $data,
			);

		$this->set(array(
            'response' => $response,
            '_serialize' => array('response'),
        ));

	}


	/**
	 ** @purpose Get list of Orders
	 ** @url  /v1.0/venues/get_orders_list.json
	 ** @input auth_type, auth_key, sort, direction, page, limit
	 ** @output
	 **
	 **/
	public function get_orders_list() {
		$this->Order = ClassRegistry::init('Order');
		$this->OrderLine = ClassRegistry::init('OrderLine');

		$auth_type = $this->getParams('auth_type', 'web');
		$auth_key = $this->getParams('auth_key');
		$status_val = $this->getParams('status_val');
		$keyword = $this->getParams('keyword');
		$sort = $this->getParams('sort', 'Order.id');
		$direction = $this->getParams('direction', 'DESC');
		$page = $this->getParams('page', 1);
		$limit = $this->getParams('limit', 15);


		// Validations
		$validationErrors = array();
		if(empty($auth_key))
			$validationErrors['auth_key'] = 'Auth key is blank';
		else {
			$user_id = $this->User->get_user_id($auth_key);
			if(empty($user_id))
				$validationErrors['auth_key'] = 'Invalid auth key';
		}

		if(!empty($validationErrors)) {
			$response = array('status' => 'error', 'operation' => 'get_orders_list', 'validationErrors' => $validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}


		$options = array(
			'recursive' => '-1',
			'fields' => array('Order.*', 'Venue.id', 'Venue.name', 'User.id', 'User.name', 'User.email'),
			'group' => array('Order.id'),
		);
		$conditions = array();

		if(!empty($keyword)) {
			$searchCond['OR'] = array(
				'Order.order_no LIKE ' => "%".$keyword."%",
				'Venue.name LIKE ' => "%".$keyword."%",
				'User.name LIKE ' => "%".$keyword."%",
				'Order.order_date LIKE ' => "%".$keyword."%",
				'Order.grand_total LIKE ' => "%".$keyword."%",
				);
		}

		if($status_val != '') {
			if(!empty($keyword)) {
				$conditions = array('Order.status' => $status_val, $searchCond);
				$options['conditions'] = array('Order.status' => $status_val, $searchCond);
			}
			else{
				$conditions = array('Order.status' => $status_val);
				$options['conditions'] = array('Order.status' => $status_val);
			}
		}
		else
		{
			$conditions = array();
			$options['conditions'] = array();
		}

		$joins = array(
			array(
				'table' => 'venues',
				'alias' => 'Venue',
				'type' => 'LEFT',
				'conditions' => 'Order.venue_id = Venue.id',
				),
			array(
				'table' => 'users',
				'alias' => 'User',
				'type' => 'LEFT',
				'conditions' => 'Order.user_id = User.id',
				),
		);

		$options['joins'] = $joins;
		if(!empty($conditions))
			$options['conditions'] = $conditions;

		$total_orders = $this->Order->find('count', $options);

		$paginate_cond = array(
			'total_records'	=> $total_orders,
			'page_num'	=> $page,
			'per_page'	=> $limit,
			);
		$pagination = get_pagination_stats($paginate_cond);

		$options = array(
			'recursive' => '-1',
			'fields' => array('Order.*', 'Venue.id', 'Venue.name', 'User.id', 'User.username', 'User.name', 'User.email'),
			'group' => array('Order.id'),
			'page'	=> $page,
			'limit'	=> $limit,
			'order'	=> array($sort => $direction),
		);

		$options['joins'] = $joins;
		if(!empty($conditions))
			$options['conditions'] = $conditions;

		$orders = $this->Order->find('all', $options);


		//print_r($result); exit;
		$response = array(
			'status' => 'success',
			'operation' => 'get_orders_list',
			'pagination' => $pagination,
			'data' => $orders,
			);

		$this->set(array(
            'response' => $response,
            '_serialize' => array('response'),
        ));
	}




	/**
	 ** @purpose Get Details of Orders
	 ** @url  /v1.0/venues/get_order_details.json
	 ** @input auth_type, auth_key, order_id
	 ** @output
	 **
	 **/
	public function get_order_details() {
		$this->Order = ClassRegistry::init('Order');
		$this->OrderLine = ClassRegistry::init('OrderLine');

		$auth_type = $this->getParams('auth_type', 'web');
		$auth_key = $this->getParams('auth_key');
		$order_id = $this->getParams('order_id');

		// Validations
		$validationErrors = array();
		if(empty($auth_key))
			$validationErrors['auth_key'] = 'Auth key is blank';
		else {
			$userExists = $this->User->get_user_id($auth_key);
			if(empty($userExists))
				$validationErrors['auth_key'] = 'Invalid auth key';
		}


		if(empty($order_id))
			$validationErrors['order_id'] = 'Order ID is blank';
		else {
			$orderExists = $this->Order->find('first', array('conditions' => array('Order.id' => $order_id)));
			//print_r($orderExists); exit;
			if(empty($orderExists) || !isset($orderExists['Order']))
				$validationErrors['order_id'] = 'Invalid Order ID';
		}

		if(!empty($validationErrors)) {
			$response = array('status' => 'error', 'operation' => 'get_order_details', 'validationErrors' => $validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}

		$user_id = isset($orderExists['Order']['user_id']) ? $orderExists['Order']['user_id'] : '';
		$venue_id = isset($orderExists['Order']['venue_id']) ? $orderExists['Order']['venue_id'] : '';

		$venue_data = $this->Venue->find('first', array('conditions' => array('Venue.id' => $venue_id)));
		$user_data = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));

		if(!empty($venue_data))
			$orderExists['Venue'] = $venue_data['Venue'];

		if(!empty($user_data))
			$orderExists['User'] = $user_data['User'];

		//print_r($result); exit;
		$response = array(
			'status' => 'success',
			'operation' => 'get_order_details',
			'data' => $orderExists,
			);

		$this->set(array(
            'response' => $response,
            '_serialize' => array('response'),
        ));
	}




	/**
	 ** @purpose add limo service box
	 ** @input auth_type (android/iphone/web), venue_id, limo_service, price
	 ** @output
	 */
	//@08/10/2014 by 037, Add Limo Service
	public function add_limo_service_box() {
		$this->Venue = ClassRegistry::init('Venue');
		$this->LimoServiceBox = ClassRegistry::init('LimoServiceBox');

		$venue_id = $this->getParams('venue_id');
		$limo_service = $this->getParams('limo_service');
		$price = $this->getParams('price');

		//Validations
		$validationErrors = array();
		if(empty($venue_id))
			$validationErrors['venue_id'] = 'Venue is not selected';

		if(empty($limo_service))
			$validationErrors['limo_service'] = 'Limo Service is not set';

		if($price == '' || !is_numeric($price))
			$validationErrors['price'] = 'Invalid Limo Service price value';

		if(empty($validationErrors)) {
			// Check if the selected venue is available or not
			$venueExists = $this->Venue->findByid($venue_id);
			if(empty($venueExists))
				$validationErrors['venue_id'] = 'Venue does not exists';
		}

		if(!empty($validationErrors)) {
			$response = array(
				'status' => 'error',
				'operation' => 'add_limo_service_box',
				'validationErrors' => $validationErrors,
				);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}

		$today = date('Y-m-d H:i:s');
		$bs_input = array(
			'venue_id'	=> $venue_id,
			'limo_service'	=> $limo_service,
			'price'	=> $price,
			'created'	=> $today,
			);
		$this->LimoServiceBox->save($bs_input);
		$limo_service_box_id = $this->LimoServiceBox->getLastInsertId();

		$data = array('msg'	=> 'Changes saved successfully', 'limo_service_box_id' => $limo_service_box_id, 'data' => $this->data);
		$response = array(
			'status' => 'success',
			'operation' => 'add_limo_service_box',
			'data' => $data,
			);

		$this->set(array(
            'response' => $response,
            '_serialize' => array('response'),
        ));
	}


	/**
	 ** @purpose delete limo service box
	 ** @input auth_type, venue_id, limo_service_box_id
	 ** @output
	 */
	//@08/10/2014 by 037, delete limo service box
	public function delete_limo_service_box() {
		$this->LimoServiceBox = ClassRegistry::init('LimoServiceBox');

		$auth_type = $this->getParams('auth_type');
		$venue_id = $this->getParams('venue_id');
		$limo_service_box_id = $this->getParams('limo_service_box_id');

		$validationErrors = array();
		if(empty($venue_id))
			$validationErrors['venue_id'] = 'Venue is not selected';
		else {
			// Check if the selected venue is available or not
			$venueExists = $this->Venue->findByid($venue_id);
			if(empty($venueExists))
				$validationErrors['venue_id'] = 'Venue does not exists';
		}
		if(empty($limo_service_box_id))
			$validationErrors['limo_service_box_id'] = 'Limo service box is not selected';
		else {
			// Check if the selected limo service is correct or not
			$options = array('conditions' => array('LimoServiceBox.venue_id' => $venue_id, 'LimoServiceBox.id' => $limo_service_box_id));
			$limoServiceBoxExists = $this->LimoServiceBox->find('first', $options);
			if(empty($limoServiceBoxExists))
				$validationErrors['limo_service_box_id'] = 'Invalid limo service box';
		}

		if(!empty($validationErrors)) {
			$response = array('status' => 'error', 'operation' => 'delete_limo_service_box', 'validationErrors' => $validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}


		if(!$this->LimoServiceBox->delete($limo_service_box_id)) {
			$response = array('status' => 'error', 'operation' => 'delete_limo_service_box', 'dataErrors' => $this->LimoServiceBox->validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}


		$response = array(
			'status' => 'success',
			'operation' => 'delete_limo_service_box',
			'data' => array('msg' => 'Limo service box has been deleted successfully', 'data' => $this->data),
			);
		$this->set(array('response' => $response, '_serialize' => array('response')));
	}


	/**
	 ** @purpose Return all Limo Service Box data
	 ** @input auth_type, venue_id
	 ** @output Array of data
	 **
	 **/
	//@08/10/2014 by 037, Get Limo Service Box data
	public function get_limo_boxes(){
		$this->LimoServiceBox = ClassRegistry::init('LimoServiceBox');
		$auth_type = $this->getParams('auth_type', 'web');
		$venue_id = $this->getParams('venue_id');

		//Validations
		$validationErrors = array();
		if(empty($venue_id))
			$validationErrors['venue_id'] = 'Venue is not selected';
		else {
			$venueExists = $this->Venue->findByid($venue_id);
			if(empty($venueExists))
				$validationErrors['venue_id'] = 'Venue does not exists';
		}
		//Return Validations
		if(!empty($validationErrors)) {
			$response = array('status' => 'error', 'operation' => 'get_limo_boxes', 'validationErrors' => $validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}

		$options = array(
			'conditions' => array('LimoServiceBox.venue_id' => $venue_id),
			'fields' => array('LimoServiceBox.*'),
		);

		$result = $this->LimoServiceBox->find('all', $options);

		$limoBoxes = array();
		if(!empty($result)) {
				foreach($result as $row) {
					$row_data = array(
						'id'	=> $row['LimoServiceBox']['id'],
						'limo_service' => $row['LimoServiceBox']['limo_service'],
						'price'	=> $row['LimoServiceBox']['price'],
						);
					$limoBoxes[] = $row_data;
				}
			}

		//Return Response
		$response = array(
			'status' => 'success',
			'operation' => 'get_limo_boxes',
			'data' => $limoBoxes,
			);
		$this->set(array('response' => $response, '_serialize' => array('response')));
	}


	/**
	 ** @purpose unlink the venue [ie make the assigned_user_id = 0 in venue table ]
	 ** @input auth_type (android/iphone/web), venue_id
	 ** @output message
	 */
	public function unlink_venueadmin() {
		$auth_type = $this->getParams('auth_type', 'web');
		$venue_id = $this->getParams('venue_id');
		$auth_key = $this->getParams('auth_key');

		$validationErrors = array();
		if(empty($venue_id))
			$validationErrors['venue_id'] = 'Venue is not selected';
		else {
			$venueExists = $this->Venue->findById($venue_id);
			if(empty($venueExists))
				$validationErrors['venue_id'] = 'Venue does not exists';
			else{
				$assigned_user_id = isset($venueExists['Venue']['assigned_user_id']) ? $venueExists['Venue']['assigned_user_id'] : 0;
				$contact_person = isset($venueExists['Venue']['contact_person']) ? $venueExists['Venue']['contact_person'] : '';
				$venuename = isset($venueExists['Venue']['name']) ? $venueExists['Venue']['name'] : '';
				$userExists = $this->User->findById($assigned_user_id);
				//print_r($userExists); exit;
				$venue_admin_email = isset($userExists['User']['email']) ? $userExists['User']['email'] : '';
			}
		}


		if(!empty($validationErrors)) {
			$response = array('status' => 'error', 'operation' => 'unlink_venueadmin', 'validationErrors' => $validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}

        $this->Venue->begin();
		$venue_input = array(
			'id'	=> $venue_id,
			'assigned_user_id'	=> 0,
			'is_new_user'	=> 0,
			'is_new_assignment'	=> 0,
			);

        // Make entry in the venues table
		if(!$this->Venue->save($venue_input, $validate = false)) {
			// Rollback transaction
			$this->Venue->rollback();

			$response = array(
				'status' => 'error',
				'operation' => 'unlink_venueadmin',
				'dataErrors' => 'Venue information could not be saved',
				);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}

		// commit transaction
		$this->Venue->commit();
		/*
		if(!empty($venue_admin_email) && !empty($contact_person)) {
			//@16/10/2014 by 037, Send Notification mail of unlink to Venue Admin
			$to = array($venue_admin_email => $contact_person);
			$from = array(SITE_EMAIL => SITE_TITLE);
			$subject = 'Shoutoutcity - Unlink Notification';
			$template = 'unlink_venue_admin';
			$variables = array(
				'name' => $contact_person,
				'venuename' => $venuename
			);
			if(!$this->User->send_mail($from, $to, $subject, $template, $variables)) {
				$response = array('status' => 'error', 'operation' => 'unlink_venueadmin', 'validationErrors' => $this->User->validationErrors);
				$this->set(array('response' => $response, '_serialize' => array('response')));
				return;
			}
		}
		*/
		$data = array('msg'	=> 'unlinked successfully', 'data' => $this->data);
		$response = array(
			'status' => 'success',
			'operation' => 'unlink_venueadmin',
			'data' => $data,
			);

		$this->set(array(
            'response' => $response,
            '_serialize' => array('response'),
        ));
	}


	/** @url v1.0/venues/update_order_status.json
	 ** @purpose update Order Status in Table Reservation
	 ** @input auth_type, auth_key, order_id, status(confirmed/rejected/pending)
	 ** @output
	 **
	 */
	//@13/10/2014 by 037, Update Order status
	public function update_order_status() {
		$this->Order = ClassRegistry::init('Order');

		$auth_type = $this->getParams('auth_type', 'web');
		$auth_key = $this->getParams('auth_key');
		$order_id = $this->getParams('order_id');
		$status = $this->getParams('status');
		$order_notify_user = $this->getParams('order_notify_user', 0);

		// Validations
		$validationErrors = array();
		if(empty($auth_key))
			$validationErrors['auth_key'] = 'Auth key is blank';
		else {
			$user_id = $this->User->get_user_id($auth_key);
			if(empty($user_id))
				$validationErrors['auth_key'] = 'Invalid auth key';
		}

		if(empty($order_id))
			$validationErrors['order_id'] = 'Order ID is blank';
		else {
			$orderExists = $this->Order->findById($order_id);
			//print_r($orderExists); exit;
			if(empty($orderExists))
				$validationErrors['order_id'] = 'Invalid Order ID';
		}

		if(empty($status) || !in_array($status, array('confirmed', 'rejected', 'pending')))
			$validationErrors['status'] = 'Invalid Status';

		if(!empty($validationErrors)) {
			$response = array('status' => 'error', 'operation' => 'update_order_status', 'validationErrors' => $validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}

		$value = 0;
		if($status == 'confirmed')
			$value = 1;
		if($status == 'rejected')
			$value = 2;

		$data = array(
			'id' => $order_id,
			'status' => $value
		);

		if(!$this->Order->save($data)) {

			$response = array('status' => 'error', 'operation' => 'update_order_status', 'dataErrors' => $this->Order->validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;

		}

		$orderExists = $this->Order->findById($order_id);
		$to = array();
		if(isset($orderExists['User']) && !empty($orderExists['User']) && $order_notify_user == 1) {
			$name = $orderExists['User']['name'];
			$email = $orderExists['User']['email'];
			array_push($to, $email);     #Add user in "to"
			
			$from = array(SITE_EMAIL => SITE_TITLE);
			$subject = 'Shoutoutcity - Order status';
			$template = 'order_confirm';						
			
			//Send Notification mail to Bottle Service Mail
			$venue_id = $orderExists['Order']['venue_id'];
			$this->BottleService = ClassRegistry::init('BottleService');
			$options = array('conditions' => array('BottleService.venue_id' => $venue_id));
			$bottleServiceExists = $this->BottleService->find('first', $options);

			$cc = array();
			if(isset($bottleServiceExists['BottleService']) && !empty($bottleServiceExists['BottleService'])) {
				$bottle_email = $bottleServiceExists['BottleService']['email'];
				$bottle_email2 = $bottleServiceExists['BottleService']['email2'];

				if(!empty($bottle_email))
					array_push($cc, $bottle_email);    #Add email1 in "cc"			

				if(!empty($bottle_email2))
					array_push($cc, $bottle_email2);   #Add email2 in "cc"			
			}				
				
			//echo '<pre>'; print_r($to); echo '</pre>';	
			//echo '<pre>'; print_r($cc); echo '</pre>'; exit;
			
			$variables = array(
				'name' => $name,
				'orderData' => $orderExists['Order'],
				'itemData' => $orderExists['OrderLine']
				);
			if(!$this->User->send_mail_cc($from, $to, $subject, $template, $variables, $cc)) {
				$response = array('status' => 'error', 'operation' => 'update_order_status', 'validationErrors' => $this->User->validationErrors);
				$this->set(array('response' => $response, '_serialize' => array('response')));
				return;
			}
			


			##SMS integration			
			if(isset($bottleServiceExists['BottleService']) && !empty($bottleServiceExists['BottleService'])) {				
				$bottle_phone1 = $bottleServiceExists['BottleService']['phone'];
				$bottle_phone2 = $bottleServiceExists['BottleService']['phone2'];
				
				$remaining = $orderExists['Order']['grand_total'] - $orderExists['Order']['reservation_fee'];

				$table_area = '';
				$bottle = '';
				$mixture = '';				
				if(!empty($orderExists['OrderLine'])){
					foreach($orderExists['OrderLine'] as $order_line){
						
						if($order_line['item_type'] == 'Table Area')
							$table_area = $order_line['item']." ($".$order_line['price'].") ";
						elseif($order_line['item_type'] == 'mixer')
							$mixture = $order_line['item']." ";
						else
							$bottle = $order_line['quantity']." - ".$order_line['item_type']." ".$order_line['item']." ($".$order_line['price'].") ";		
					}
			$reserve_dt = date("d/m/Y", strtotime($orderExists['Order']['order_date']));	

$text = "Nightclub - ".$orderExists['Venue']['name']."

Guest: ".$orderExists['User']['name']."
Reservation Date: ".$reserve_dt."
Table Area: ".$table_area."
Bottles: ".$bottle."
Mixers: ".$mixture."
Guests: ".$orderExists['Order']['no_of_males']." men
".$orderExists['Order']['no_of_females']." women
Sub Total: $".number_format($orderExists['Order']['grand_total'], 2)."
Reservation Fee: $".number_format($orderExists['Order']['reservation_fee'], 2)."
Total Upon Aarival: $".number_format($remaining, 2)."
Status: ".$status."";
$text = urlencode($text);

				if(!empty($bottle_phone1)){		
					## for first phone 
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, "https://rest.nexmo.com/sms/json?api_key=214a6246&api_secret=38ef268c&from=NEXMO&to=$bottle_phone1&text=$text");
					curl_setopt($ch, CURLOPT_HEADER, 0);
					curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);	
					curl_exec($ch);
					curl_close($ch);
					##
				}			
				if(!empty($bottle_phone2)){		
					## for first phone 
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, "https://rest.nexmo.com/sms/json?api_key=214a6246&api_secret=38ef268c&from=NEXMO&to=$bottle_phone2&text=$text");
					curl_setopt($ch, CURLOPT_HEADER, 0);
					curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);	
					curl_exec($ch);
					curl_close($ch);
					##
				}			
			}
		
		}			
		##
	
			
		}

		$response = array(
			'status' => 'success',
			'operation' => 'update_order_status',
			'data' => array('msg' => 'Order status has been changed successfully', 'data' => $this->data),
			);
		$this->set(array('response' => $response, '_serialize' => array('response')));

	}


	/**
	 ** @url v1.0/venues/get_table_area_details.json
	 ** @purpose Get Table Area Details
	 ** @input auth_type, auth_key, table_area_id
	 ** @output
	 */
	//14/10/2014 by 037, Get Table Area  Details
	public function get_table_area_details() {
		$this->TableArea = ClassRegistry::init('TableArea');

		$auth_type = $this->getParams('auth_type', 'web');
		$auth_key = $this->getParams('auth_key');
		$table_area_id = $this->getParams('table_area_id');

		//Validations
		$validationErrors = array();
		if(empty($auth_key))
			$validationErrors['auth_key'] = 'Auth Key is not selected';
		else{
			$user_id = $this->User->get_user_id($auth_key);
			if(empty($user_id))
				$validationErrors['auth_key'] = 'Invalid Auth Key';
		}
		if(empty($table_area_id))
			$validationErrors['table_area_id'] = 'Table Area ID is not selected';
		else {
			$tableAreaExists = $this->TableArea->findById($table_area_id);
			if(empty($tableAreaExists) || !isset($tableAreaExists['TableArea']))
				$validationErrors['table_area_id'] = 'Inavlid Table Area ID';
		}

		if(!empty($validationErrors)) {
			$response = array('status' => 'error', 'operation' => 'get_table_area_details', 'validationErrors' => $validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}

		$response = array('status' => 'success', 'operation' => 'get_table_area_details', 'data' => $tableAreaExists['TableArea']);
		$this->set(array('response' => $response, '_serialize' => array('response')));

	}

	/**
	 ** @url v1.0/venues/get_reviews_list.json
	 ** @purpose Get Reviews List
	 ** @input auth_type, venue_id
	 ** @output
	 */
	//14/10/2014 by 037, Get Reviews List
	public function get_reviews_list() {
		$auth_type = $this->getParams('auth_type');
		$venue_id = $this->getParams('venue_id');


		$sort = $this->getParams('sort', 'VenueReview.created');
		$direction = $this->getParams('direction', 'DESC');
		$page = $this->getParams('page', 1);
		$limit = $this->getParams('limit', 10);

		//Validations
		$validationErrors = array();
		if(empty($venue_id))
			$validationErrors['venue_id'] = 'Venue is not selected';
		else {
			$venueExists = $this->Venue->findByid($venue_id);
			if(empty($venueExists))
				$validationErrors['venue_id'] = 'Venue does not exists';
		}

		if(!empty($validationErrors)) {
			$response = array('status' => 'error', 'operation' => 'get_reviews_list', 'validationErrors' => $validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}

		$options = array();
		$options['conditions'] = array('VenueReview.venue_id' => $venue_id);
		$total_reviews = $this->VenueReview->find('count', $options);

		$paginate_cond = array(
			'total_records'	=> $total_reviews,
			'page_num'	=> $page,
			'per_page'	=> $limit,
			);
		$pagination = get_pagination_stats($paginate_cond);

		$options = array(
			'joins' => array(
				array(
					'table'	=> 'users',
					'alias'	=> 'User',
					'conditions' => array(
							'User.id = VenueReview.user_id',
					),
				),
			),
			'fields' => array('VenueReview.id', 'VenueReview.rating', 'VenueReview.comment', 'VenueReview.created', 'User.username'),
			'page'	=> $page,
			'limit'	=> $limit,
			'order'	=> array($sort => $direction),

		);

		$options['conditions'] = array('VenueReview.venue_id' => $venue_id);
		$result = $this->VenueReview->find('all', $options);

		$response = array('status' => 'success', 'operation' => 'get_reviews_list', 'pagination' => $pagination, 'data' => $result);
		$this->set(array('response' => $response, '_serialize' => array('response')));

	}

	/**
	 ** @purpose save venue other  features
	 ** @input auth_type (android/iphone/web), venue_id, custom_feature
	 ** @output
	 */
	public function add_categoryfeature_other() {
		$this->Venue = ClassRegistry::init('Venue');

		$venue_id = $this->getParams('venue_id');
		$custom_feature = $this->getParams('custom_feature');
		$custom_feature_val = $this->getParams('custom_feature_val');


		$validationErrors = array();
		if(empty($venue_id))
			$validationErrors['venue_id'] = 'Venue is not selected';

		if(empty($validationErrors)) {
			// Check if the selected venue is available or not
			$venueExists = $this->Venue->findByid($venue_id);
			if(empty($venueExists))
				$validationErrors['venue_id'] = 'Venue does not exists';
		}

		if(!empty($validationErrors)) {
			$response = array(
				'status' => 'error',
				'operation' => 'add_categoryfeature_other',
				'validationErrors' => $validationErrors,
				);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}

		// Begin transaction
		$this->Venue->begin();

		if($custom_feature_val != 'y'){
			if($venueExists['Venue']['custom_feature'] != "")
				$custom_feature_new = $venueExists['Venue']['custom_feature'].",".$custom_feature;

			else
			$custom_feature_new = $custom_feature;
		}
		else
			$custom_feature_new = $custom_feature;




		$venue_input = array(
			'id'	=> $venue_id,
			'custom_feature' => $custom_feature_new
			);
		if(!$this->Venue->save($venue_input, $validate = false)) {
			// Rollback transaction
			$this->Venue->rollback();

			$response = array(
				'status' => 'error',
				'operation' => 'add_categoryfeature_other',
				'dataErrors' => 'Venue information could not be saved',
				);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}

		// commit transaction
		$this->Venue->commit();


		$data = array('msg'	=> 'Changes saved successfully', 'data' => $this->data);
		$response = array(
			'status' => 'success',
			'operation' => 'add_categoryfeature_other',
			'data' => $data,
			);

		$this->set(array(
            'response' => $response,
            '_serialize' => array('response'),
        ));
	}

	/**
	 ** @purpose delete venue other  features
	 ** @input auth_type (android/iphone/web), venue_id
	 ** @output
	 */
	public function delete_categoryfeature_other() {
		$this->Venue = ClassRegistry::init('Venue');

		$venue_id = $this->getParams('venue_id');

		$validationErrors = array();
		if(empty($venue_id))
			$validationErrors['venue_id'] = 'Venue is not selected';

		if(empty($validationErrors)) {
			// Check if the selected venue is available or not
			$venueExists = $this->Venue->findByid($venue_id);
			if(empty($venueExists))
				$validationErrors['venue_id'] = 'Venue does not exists';
		}

		if(!empty($validationErrors)) {
			$response = array(
				'status' => 'error',
				'operation' => 'delete_categoryfeature_other',
				'validationErrors' => $validationErrors,
				);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}

		// Begin transaction
		$this->Venue->begin();



		$venue_input = array(
			'id'	=> $venue_id,
			'custom_feature' => "",
			);
		if(!$this->Venue->save($venue_input, $validate = false)) {
			// Rollback transaction
			$this->Venue->rollback();

			$response = array(
				'status' => 'error',
				'operation' => 'delete_categoryfeature_other',
				'dataErrors' => 'Venue information could not be deleted',
				);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}

		// commit transaction
		$this->Venue->commit();


		$data = array('msg'	=> 'Changes deleted successfully', 'data' => $this->data);
		$response = array(
			'status' => 'success',
			'operation' => 'delete_categoryfeature_other',
			'data' => $data,
			);

		$this->set(array(
			'response' => $response,
			'_serialize' => array('response'),
		));
	}


	/**
	 ** @url v1.0/venues/check_table_area_availability.json
	 ** @purpose To Check whether Table Area is available or not for i/p date and no of males and females entered
	 ** @input auth_type, auth_key, venue_id(int), order_date(yyyy-mm-dd), table_area_id(int), no_of_males(int), no_of_females(int)
	 ** @output
	 **
	 */
	//@ 16/10/2014 by 037, Check Table Area Availability
	public function check_table_area_availability() {
		$this->Order = ClassRegistry::init('Order');
		$this->TableArea = ClassRegistry::init('TableArea');

		$auth_type = $this->getParams('auth_type','web');
		$auth_key = $this->getParams('auth_key');
		$venue_id = $this->getParams('venue_id');
		$order_date = $this->getParams('order_date');
		$table_area_id = $this->getParams('table_area_id');
		$no_of_males = $this->getParams('no_of_males', 0);
		$no_of_females = $this->getParams('no_of_females', 0);

		//Validations
		$validationErrors = array();

		if(empty($auth_key))
			$validationErrors['auth_key'] = 'Auth Key is not selected';
		else {
			$user_id = $this->User->get_user_id($auth_key);
			if(empty($user_id))
				$validationErrors['auth_key'] = 'Invalid Auth Key';
		}


		if(empty($venue_id))
			$validationErrors['venue_id'] = 'Venue is not selected';
		else {
			$venueExists = $this->Venue->findById($venue_id);
			if(empty($venueExists))
				$validationErrors['venue_id'] = 'Venue does not exists';
		}

		if(empty($order_date))
			$validationErrors['order_date'] = 'Order Date is not selected';
		else
			$date = date('Y-m-d H:i:s', strtotime($order_date));



		if(!preg_match('/^[0-9]*$/', $no_of_males))
			$validationErrors['no_of_males'] = 'Invalid No of males';

		if(!preg_match('/^[0-9]*$/', $no_of_females))
			$validationErrors['no_of_females'] = 'Invalid No of Females';

		if(empty($table_area_id))
			$validationErrors['table_area_id'] = 'Table Area is not selected';
		else {
			$tableAreaExists = $this->TableArea->findById($table_area_id);
			//print_r($tableAreaExists); exit;
			if(empty($tableAreaExists))
				$validationErrors['table_area_id'] = 'Invalid Table Area';
			else{
				$valid_count = isset($tableAreaExists['TableArea']['no_of_persons']) ? $tableAreaExists['TableArea']['no_of_persons'] : 0;
				$total_count = $no_of_males + $no_of_females;
				if($total_count > $valid_count)
					$validationErrors['no_of_males'] = 'Total Male Female Count is exceeded';
			}
		}

		if(!empty($validationErrors)) {
			$response = array('status' => 'error', 'operation' => 'check_table_area_availability', 'validationErrors' => $validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}


		$result = $this->Order->findAllByVenueIdAndOrderDateAndTableAreaIdAndStatus($venue_id, $date, $table_area_id, 1);
		//print_r($result); exit;
		$remaing_seats = 0;
		$message = array();
		if(!empty($result)){
			$reserved_people = 0 ;

			foreach($result as $res) {
				$a = $b = 0;

				$a = $res['Order']['no_of_males'];
				$b = $res['Order']['no_of_females'];

				$total_persons = $a + $b;
				$reserved_people = $reserved_people + $total_persons;
			}

			if($reserved_people >= $valid_count)
				$message = array(
					'msg' => 'Table Area is already reserved',
					'available_seats' => 0
				);
			else {
				$remaing_seats = $valid_count - $reserved_people ;
				$message = array(
					'msg' => 'You can reserve this Table Area for '.$remaing_seats.' persons',
					'available_seats' => $remaing_seats
				);
			}
		}
		else
			$message = array(
				'msg' => 'Table Area is available',
				'available_seats' => $valid_count
			);

		$response = array(
			'status' => 'success',
			'operation' => 'check_table_area_availability',
			'data' => $message,
		);
		$this->set(array('response' => $response, '_serialize' => array('response')));
	}


	/** @name delete_venue_multiple_photos
	 ** @purpose delete the venue photo
	 ** @input auth_type, venue_id, photo_ids
	 ** @output
	 */
	public function delete_venue_multiple_photos() {
		$venue_id = $this->getParams('venue_id');
		$photo_ids = $this->getParams('photo_ids', $default = array());

		$validationErrors = array();
		if(empty($venue_id))
			$validationErrors['venue_id'] = 'Venue is not selected';
		else {
			// Check if the venue is available or not
			$venueExists = $this->Venue->findByid($venue_id);
			if(empty($venueExists))
				$validationErrors['venue_id'] = 'Venue does not exists';
		}
		if(empty($photo_ids))
			$validationErrors['photo_id'] = 'Venue photo is not selected';
		else {
			// Check if the venue photo is available or not
			foreach($photo_ids as $key => $value){
				$options = array('conditions' => array('VenuePhoto.venue_id' => $venue_id, 'VenuePhoto.id' => $value));
				$venuePhotoExists = $this->VenuePhoto->find('first', $options);
				if(empty($venuePhotoExists))
					$validationErrors['photo_id'] = 'Venue photo does not exists';
			}
		}

		if(!empty($validationErrors)) {
			$response = array('status' => 'error', 'operation' => 'delete_venue_multiple_photos', 'validationErrors' => $validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}

		foreach($photo_ids as $key => $value){
			$photo_id = $value;
			if(!$this->VenuePhoto->delete($photo_id)) {
				$response = array('status' => 'error', 'operation' => 'delete_venue_multiple_photos', 'dataErrors' => $this->VenuePhoto->validationErrors);
				$this->set(array('response' => $response, '_serialize' => array('response')));
				return;
			}


			// Unlink the venue logo
			$venue_photo = $venuePhotoExists['VenuePhoto']['photo'];
			$venue_path = WWW_ROOT . DS . $venue_photo;

			$path_parts 			= pathinfo($venue_photo);
			$venuePhotoLarge 		= WWW_ROOT . DS . $path_parts['dirname'].'/'.$path_parts['filename'].'_large.'.$path_parts['extension'];
			$venuePhotoThumb 		= WWW_ROOT . DS . $path_parts['dirname'].'/'.$path_parts['filename'].'_thumb.'.$path_parts['extension'];
			@unlink($venuePhotoLarge);
			@unlink($venuePhotoThumb);
			@unlink($venue_path);
		}

		// SuccessResponse
		$response = array(
			'status' => 'success',
			'operation' => 'delete_venue_multiple_photos',
			'data' => array('msg' => 'Venue photo has been deleted successfully', 'data' => $this->data),
			);
		$this->set(array('response' => $response, '_serialize' => array('response')));
	}


	/** @name delete_venue_multiple_promotion_photos
	 ** @purpose delete the venue promo
	 ** @input auth_type, venue_id, promo_ids
	 ** @output
	 */
	public function delete_venue_multiple_promotion_photos() {
		$venue_id = $this->getParams('venue_id');
		$promo_ids = $this->getParams('promo_ids', $default = array());

		$validationErrors = array();
		if(empty($venue_id))
			$validationErrors['venue_id'] = 'Venue is not selected';
		else {
			// Check if the venue is available or not
			$venueExists = $this->Venue->findByid($venue_id);
			if(empty($venueExists))
				$validationErrors['venue_id'] = 'Venue does not exists';
		}
		if(empty($promo_ids))
			$validationErrors['promo_id'] = 'Venue promotion is not selected';
		else {
			// Check if the venue promo is available or not
			foreach($photo_ids as $key => $value){
				$options = array('conditions' => array('VenuePromotion.venue_id' => $venue_id, 'VenuePromotion.id' => $value));
				$venuePromoExists = $this->VenuePromotion->find('first', $options);
				if(empty($venuePromoExists))
					$validationErrors['promo_id'] = 'Venue promotion does not exists';
			}
		}

		if(!empty($validationErrors)) {
			$response = array('status' => 'error', 'operation' => 'delete_venue_multiple_promotion_photos', 'validationErrors' => $validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}

		foreach($promo_ids as $key => $value){
			$promo_id = $value;

			if(!$this->VenuePromotion->delete($promo_id)) {
				$response = array('status' => 'error', 'operation' => 'delete_venue_multiple_promotion_photos', 'dataErrors' => $this->VenuePromotion->validationErrors);
				$this->set(array('response' => $response, '_serialize' => array('response')));
				return;
			}

			// Unlink the venue promo
			$venue_promo = $venuePromoExists['VenuePromotion']['promotion'];
			$venue_path = WWW_ROOT . DS . $venue_promo;

			$path_parts 			= pathinfo($venue_promo);
			$venuePromoLarge 		= WWW_ROOT . DS . $path_parts['dirname'].'/'.$path_parts['filename'].'_large.'.$path_parts['extension'];
			$venuePromoThumb 		= WWW_ROOT . DS . $path_parts['dirname'].'/'.$path_parts['filename'].'_thumb.'.$path_parts['extension'];
			@unlink($venuePromoLarge);
			@unlink($venuePromoThumb);
			@unlink($venue_path);
		}

		// SuccessResponse
		$response = array(
			'status' => 'success',
			'operation' => 'delete_venue_multiple_promotion_photos',
			'data' => array('msg' => 'Venue promotion has been deleted successfully', 'data' => $this->data),
			);
		$this->set(array('response' => $response, '_serialize' => array('response')));
	}


	/** @name delete_venue_multiple_floorplan_photos
	 ** @purpose delete the venue floor plans
	 ** @input auth_type, venue_id, floor_plan_ids
	 ** @output
	 */
	public function delete_venue_multiple_floorplan_photos() {
		$venue_id = $this->getParams('venue_id');
		$floorplan_ids = $this->getParams('floorplan_ids', $default = array());

		$validationErrors = array();
		if(empty($venue_id))
			$validationErrors['venue_id'] = 'Venue is not selected';
		else {
			// Check if the venue is available or not
			$venueExists = $this->Venue->findByid($venue_id);
			if(empty($venueExists))
				$validationErrors['venue_id'] = 'Venue does not exists';
		}
		if(empty($floorplan_ids))
			$validationErrors['floor_plan_id'] = 'Venue Floor Plan is not selected';
		else {
			// Check if the venue floor_plan is available or not
			foreach($floorplan_ids as $key => $value){
				$options = array('conditions' => array('VenueFloorPlan.venue_id' => $venue_id, 'VenueFloorPlan.id' => $value));
				$venueFloorPlanExists = $this->VenueFloorPlan->find('first', $options);
				if(empty($venueFloorPlanExists))
					$validationErrors['floor_plan_id'] = 'Venue Floor Plan does not exists';
			}
		}

		if(!empty($validationErrors)) {
			$response = array('status' => 'error', 'operation' => 'delete_venue_multiple_floorplan_photos', 'validationErrors' => $validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}

		foreach($floorplan_ids as $key => $value){
			$floor_plan_id = $value;
			if(!$this->VenueFloorPlan->delete($floor_plan_id)) {
				$response = array('status' => 'error', 'operation' => 'delete_venue_multiple_floorplan_photos', 'dataErrors' => $this->VenueFloorPlan->validationErrors);
				$this->set(array('response' => $response, '_serialize' => array('response')));
				return;
			}


			// Unlink the venue floor_plan
			$venue_floor_plan = $venueFloorPlanExists['VenueFloorPlan']['floor_plan'];
			$venue_path = WWW_ROOT . DS . $venue_floor_plan;

			$path_parts 			= pathinfo($venue_floor_plan);
			$venueFloorPlanLarge 		= WWW_ROOT . DS . $path_parts['dirname'].'/'.$path_parts['filename'].'_large.'.$path_parts['extension'];
			$venueFloorPlanThumb 		= WWW_ROOT . DS . $path_parts['dirname'].'/'.$path_parts['filename'].'_thumb.'.$path_parts['extension'];
			@unlink($venueFloorPlanLarge);
			@unlink($venueFloorPlanThumb);
			@unlink($venue_path);
		}

		// SuccessResponse
		$response = array(
			'status' => 'success',
			'operation' => 'delete_floor_plans',
			'data' => array('msg' => 'Venue Floor Plan has been deleted successfully', 'data' => $this->data),
			);
		$this->set(array('response' => $response, '_serialize' => array('response')));
	}



	/**
	 ** @url v1.0/venues/update_pay_status.json
	 ** @purpose
	 ** @input auth_type, auth_key, order_id(int), response_code(varchar),
	 ** @output array of data
	 */

	// @03/11/2014 by 062, To update Order Pay status
	public function update_pay_status() {
		$this->Order = ClassRegistry::init('Order');
		$this->OrderLine = ClassRegistry::init('OrderLine');
		$auth_type = $this->getParams('auth_type', 'web');
		$auth_key = $this->getParams('auth_key');
		$order_id =  $this->getParams('order_id');
		$response_code =  $this->getParams('response_code');
		$card_no = $this->getParams('card_no');
		$transaction_no = $this->getParams('transaction_no');

		// Validations
		$validationErrors = array();
		if(empty($auth_key))
			$validationErrors['auth_key'] = 'Auth key is blank';
		else {
			$user_id = $this->User->get_user_id($auth_key);

			if(empty($user_id))
				$validationErrors['auth_key'] = 'Invalid auth key';
		}
		if(empty($order_id))
			$validationErrors['order_id'] = 'Order ID is not selected';
		else{
			$orderExists = $this->Order->findById($order_id);
			if(empty($orderExists))
				$validationErrors['order_id'] = 'Order does not exists';
		}

		if(empty($response_code))
			$validationErrors['response_code'] = 'Response code is not selected';


		if(!empty($validationErrors)) {
			$response = array('status' => 'error', 'operation' => 'update_pay_status', 'validationErrors' => $validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}

		$result = $this->get_pay_status($response_code);

		switch($result['pay_status']) {
			case 'failure':
				$status = 0;
			break;

			case 'success':
				$status = 1;
			break;
		}

		$input = array(
			'id' => $order_id,
			'card_no' => $card_no,
			'transaction_no' => $transaction_no,
			'pay_status' => $result['pay_status'],
			'comment' => $result['comment'],
			'status'	=> $status,
		);

		if(!$this->Order->save($input)){
			$response = array('status' => 'error', 'operation' => 'update_pay_status', 'dataErrors' => $this->Order->validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}

		// Send Achnowledgement to Ordering User
		$userData = $this->User->findById($user_id);
		$orderData = $this->Order->findById($order_id);
		$itemData = $this->OrderLine->find('all', array('conditions' => array('OrderLine.order_id' => $order_id)));

		if(isset($userData['User']) && !empty($userData['User'])) {
			$name = $userData['User']['name'];
			$email = $userData['User']['email'];
			$to = array($email => $name);
			$from = array(SITE_EMAIL => SITE_TITLE);
			if($result['pay_status'] == 'failure') {
				$subject = 'Shoutoutcity - Order failed';
				$template = 'order_failure';
			}
			else {
				$subject = 'Shoutoutcity - Order received';
				$template = 'order';
			}
			$variables = array(
				'name' => $name,
				'orderData' => $orderData,
				'itemData' => $itemData
				);
			if(!$this->User->send_mail($from, $to, $subject, $template, $variables)) {
				$response = array('status' => 'error', 'operation' => 'place_order', 'validationErrors' => $this->User->validationErrors);
				$this->set(array('response' => $response, '_serialize' => array('response')));
				return;
			}
		}


		// SuccessResponse
		$response = array(
			'status' => 'success',
			'operation' => 'update_pay_status',
			'data' => $result,
			);
		$this->set(array('response' => $response, '_serialize' => array('response')));
	}

	//@04/11/2014 by 037, Get Order's Pay Status
	public function get_pay_status($response_code) {

		switch ($response_code) {
			// for success
			case 'I00001':
				$pay_status = 'success';
				$comment = 	'Successful';
			break;

			case 'I00003':
				$pay_status = 'success';
				$comment = 	'The record has already been deleted';
			break;

			case 'I00005':
				$pay_status = 'success';
				$comment = 	'The mobile device has been submitted for approval by the account administrator.';
			break;

			case 'I00006':
				$pay_status = 'success';
				$comment = 	'The mobile device is approved and ready for use.';
			break;

			// failure
			case 'E00000':
				$pay_status = 'failure';
				$comment = 	'Unknown';
			break;

			case 'E00001':
				$pay_status = 'failure';
				$comment = 	'An error occurred during processing. Please try again.';
			break;
				case 'E00002':
				$pay_status = 'failure';
				$comment = 	'The content-type specified is not supported.';
			break;

			case 'E00003':
				$pay_status = 'failure';
				$comment = 	'An error occurred while parsing the XML request.';
			break;

			case 'E00004':
				$pay_status = 'failure';
				$comment = 	'The name of the requested API method is invalid';
			break;

			case 'E00005':
				$pay_status = 'failure';
				$comment = 	'The merchantAuthentication.transactionKey is invalid or not present.';
			break;

			case 'E00006':
				$pay_status = 'failure';
				$comment = 	'The merchantAuthentication.name is invalid or not present.';
			break;

			case 'E00007':
				$pay_status = 'failure';
				$comment = 	'User authentication failed due to invalid authentication values.';
			break;

			case 'E00008':
				$pay_status = 'failure';
				$comment = 	'User authentication failed. The payment gateway account or user is inactive.';
			break;

			case 'E00009':
				$pay_status = 'failure';
				$comment = 	'The payment gateway account is in Test Mode. The request cannot be processed.';
			break;

			case 'E00010':
				$pay_status = 'failure';
				$comment = 	'User authentication failed. You do not have the appropriate permissions.';
			break;

			case 'E000011':
				$pay_status = 'failure';
				$comment = 	'Access denied. You do not have the appropriate permissions.';
			break;

			case 'E00012':
				$pay_status = 'failure';
				$comment = 	'A duplicate subscription already exists.';
			break;

			case 'E00013':
				$pay_status = 'failure';
				$comment = 	'The field is invalid.';
			break;

			case 'E00014':
				$pay_status = 'failure';
				$comment = 	'A required field is not present.';
			break;

			case 'E00015':
				$pay_status = 'failure';
				$comment = 	'The field length is invalid.';
			break;

			case 'E00016':
				$pay_status = 'failure';
				$comment = 	'The field type is invalid.';
			break;

			case 'E00017':
				$pay_status = 'failure';
				$comment = 	'The startDate cannot occur in the past.';
			break;

			case 'E00018':
				$pay_status = 'failure';
				$comment = 	'The credit card expires before the subscription startDate.';
			break;

			case 'E00019':
				$pay_status = 'failure';
				$comment = 	'The customer taxId or driversLicense information is required.';
			break;

			case 'E00020':
				$pay_status = 'failure';
				$comment = 	'The payment gateway account is not enabled for eCheck.Net subscriptions.';
			break;

			case 'E000021':
				$pay_status = 'failure';
				$comment = 	'The payment gateway account is not enabled for credit card subscriptions.';
			break;

			case 'E00022':
				$pay_status = 'failure';
				$comment = 	'The interval length cannot exceed 365 days or 12 months.';
			break;

			case 'E00023':
				$pay_status = 'failure';
				$comment = 	'Unknown';
			break;

			case 'E00024':
				$pay_status = 'failure';
				$comment = 	'The trialOccurrences is required when trialAmount is specified.';
			break;

			case 'E00025':
				$pay_status = 'failure';
				$comment = 	'Automated Recurring Billing is not enabled.';
			break;
			case 'E00026':
				$pay_status = 'failure';
				$comment = 	'Both trialAmount and trialOccurrences are required.';
			break;
			case 'E00027':
				$pay_status = 'failure';
				$comment = 	'The test transaction was unsuccessful."';
			break;
			case 'E00028':
				$pay_status = 'failure';
				$comment = 	'The trialOccurrences must be less than totalOccurrences.';
			break;
			case 'E00029':
				$pay_status = 'failure';
				$comment = 	'Payment information is required.';
			break;
			case 'E00030':
				$pay_status = 'failure';
				$comment = 	'A paymentSchedule is required.';
			break;
			case 'E00031':
				$pay_status = 'failure';
				$comment = 	'The amount is required.';
			break;

			case 'E00032':
				$pay_status = 'failure';
				$comment = 	'The startDate is required.';
			break;
			case 'E00033':
				$pay_status = 'failure';
				$comment = 	'The subscription Start Date cannot be changed.';
			break;

			case 'E00034':
				$pay_status = 'failure';
				$comment = 	'The interval information cannot be changed.';
			break;
			case 'E00035':
				$pay_status = 'failure';
				$comment = 	'The subscription cannot be found.';
			break;

			case 'E00036':
				$pay_status = 'failure';
				$comment = 	'The payment type cannot be changed.';
			break;
			case 'E00037':
				$pay_status = 'failure';
				$comment = 	'The subscription cannot be updated.';
			break;

			case 'E00038':
				$pay_status = 'failure';
				$comment = 	'The subscription cannot be canceled.';
			break;

			case 'E00045':
				$pay_status = 'failure';
				$comment = 	'The root node does not reference a valid XML namespace.';
			break;

			case 'E00054':
				$pay_status = 'failure';
				$comment = 	'The mobile device is not registered with this merchant account.';
			break;
			case 'E00055':
				$pay_status = 'failure';
				$comment = 	'The mobile device is pending approval by the account administrator.';
			break;
			case 'E00056':
				$pay_status = 'failure';
				$comment = 	'The mobile device has been disabled for use with this account.';
			break;
			case 'E00057':
				$pay_status = 'failure';
				$comment = 	'The user does not have permissions to submit requests from a mobile device.';
			break;
			case 'E00058':
				$pay_status = 'failure';
				$comment = 	'The merchant has met or exceeded the number of pending mobile devices permitted for this account.';
			break;
			case 'E00059':
				$pay_status = 'failure';
				$comment = 	'The authentication type is not allowed for this method call.';
			break;

			default:
				$pay_status = 'failure';
				$comment = 	'NA';
		}

		$response = array(
			'pay_status' => $pay_status,
			'comment' => $comment
		);

		return $response;
	}


	/**
	 ** @url v1.0/venues/get_parent_category_alias.json
	 ** @purpose Get parent category alias
	 ** @input category_id
	 ** @output
	 */
	//16/02/2015 by 044, Get parent category alias
	public function get_parent_category_alias($venue_id) {
		//$venue_id = $this->getParams('venue_id');
		$this->Category = ClassRegistry::init('Category');

		//Validations
		$validationErrors = array();

		if(empty($venue_id))
			$validationErrors['venue_id'] = "Venue ID is not selected";
		else {
			$venueExists = $this->Venue->findByid($venue_id);
			if(empty($venueExists) || !isset($venueExists))
				$validationErrors['venue_id'] = "Venue does not exist";
		}


		if(!empty($validationErrors)) {
			$response = array('status' => 'error', 'operation' => 'get_parent_category_alias', 'validationErrors' => $validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}
		$result = array();
		$result = $this->Venue->find('all', array('conditions' => array('Venue.id' => $venue_id)));
		return $result[0]['Category'][0]['alias'];


		/*$response = array(
			'status' => 'success',
			'operation' => 'update_pay_status',
			'data' => $result[0]['Category'][0]['alias'],
			);
		$this->set(array('response' => $response, '_serialize' => array('response')));
		return;*/




	}


	/**
	 * this is for email notification when new promotion image is added or edited
	 */
	public function sendCronEmailForPushNotiForImage() {
     		$this->autoRender = false;	
	
		//$site_title  				= Configure::read('site_title');
		//$site_email  				= Congifure::read('site_email');
		$site_title  				= "Shoutout";
		$site_email  				= SITE_EMAIL;
		//$photo_selected_venue_id = $this->Session->read('photo_selected_venue_id');	
		$params['conditions'] = array(
		    'AND' => array(
		        array('VenueMessageQueue.push_noti_image_status = ' => 0),		       
		    )
		);
		$params['limit'] 		= "100";		
		$VenueMessageQueueDetail = $this->VenueMessageQueue->find('all', $params);	
		
		// ---------------email send code start	-----------------------------		
		if(!empty($VenueMessageQueueDetail) ){													
			$android_give = "";
			$iphone_give = "";
			foreach($VenueMessageQueueDetail as $VenueMessageQueue){			
				$to = "";
				$email_status = false;
				$message_id = $VenueMessageQueue['VenueMessageQueue']['message_id'];
				$id 		= $VenueMessageQueue['VenueMessageQueue']['id'];
				
				$VenueAdminMessageDetail = $this->VenueAdminMessage->find('first', array('conditions' => array('VenueAdminMessage.id' => $message_id)));
				
				$user_id 	= $VenueAdminMessageDetail['VenueAdminMessage']['user_id'];
				$UserDetail = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));	
				$userName 	= $UserDetail['User']['name'];
				$VenueDetail = $this->Venue->find('first', array('conditions' => array('Venue.id' => $VenueMessageQueue['VenueMessageQueue']['venue_id'])));	
				$VenueName 	= $VenueDetail['Venue']['name'];
				
				$from 		= $site_email;
				$to 		= $VenueMessageQueue['VenueMessageQueue']['email'];
			
				//$message 	= $VenueAdminMessageDetail['VenueAdminMessage']['message'];
				//$message 	= "New promotion images are uploaded. Please login to your account to view promotion images.";
				//$message .= "\n\n".$site_title;
				$message 	= "Check out ".$VenueName."'s latest update!";
				$subject = "";
				//$subject = $userName.' - Promotion Detail - '.$site_title;
				/*$Email = new CakeEmail();
				$Email->from($from)
					->to($to)
					->subject($subject)
					->emailFormat('html');		
				$email_status = $Email->send($message);*/
				
				/*if($email_status)
				{*/
					//--------------if email send the make message_status as 1 -------------------
					$this->VenueMessageQueue->id = $VenueMessageQueue['VenueMessageQueue']['id'];
					$VenueMessageQueueInput = array(			
						'push_noti_image_status' 		=> "1",
						
					);			
					$this->VenueMessageQueue->save($VenueMessageQueueInput);
					
					//Send Push Notification
					$userExists = $this->User->findByEmail($to);


					$category_alias = $this->get_only_parent_category_alias($VenueMessageQueue['VenueMessageQueue']['venue_id']);
					$params = array('category_alias' => $category_alias, 'venue_id' => $VenueMessageQueue['VenueMessageQueue']['venue_id'], 'action' => "promotion");
					
					if($iphone_give == ""){
						if(isset($userExists['User']['ios_device_token']) && !empty($userExists['User']['ios_device_token'])){						
							//$this->send_push_notification($userExists['ios_device_token'], $message);
							$iphone_give = "done";
							$this->sendIphonePushNotification($userExists['User']['ios_device_token'], $message, $params);						
						}
					}
						
					if($android_give == ""){	
						if(isset($userExists['User']['android_device_token']) && !empty($userExists['User']['android_device_token'])){						
							$android_give = "done";
							$this->sendAndroidPushNotification($userExists['User']['android_device_token'], $message, $params);										
						}
					}
												
					/*//--------uncomment below line when cron will set on online
					#$this->VenueMessageQueue->delete($id);								
					$responce['status'] = "success";
					$responce['message'] = "Email send successfully\n".$to."----".$from;
					//print_r($responce);

			
					 $this->set(array(
					    'venues' 		=> $responce,
					    '_serialize' 	=> array('venues'),
					));*/
				
  
				//}	
					
			}
		
			//$this->sendIphonePushNotification($userExists['User']['ios_device_token'], $message, $params);	
			//$this->sendAndroidPushNotification($userExists['User']['android_device_token'], $message, $params);	
			//--------uncomment below line when cron will set on online
			#$this->VenueMessageQueue->delete($id);								
			$responce['status'] = "success";
			$responce['message'] = "Email send successfully\n".$to."----".$from;
			//print_r($responce);

	
			 $this->set(array(
				'venues' 		=> $responce,
				'_serialize' 	=> array('venues'),
			));
			
			
		}			
		
	}


	/**
	 ** @url v1.0/venues/get_only_parent_category_alias.json
	 ** @purpose Get parent category alias
	 ** @input category_id
	 ** @output
	 */
	//16/02/2015 by 044, Get parent category alias
	public function get_only_parent_category_alias($venue_id) {
		//$venue_id = $this->getParams('venue_id');
		$this->Category = ClassRegistry::init('Category');

		//Validations
		$validationErrors = array();

		if(empty($venue_id))
			$validationErrors['venue_id'] = "Venue ID is not selected";
		else {
			$venueExists = $this->Venue->findById($venue_id);
			if(empty($venueExists) || !isset($venueExists))
				$validationErrors['venue_id'] = "Venue does not exist";
		}


		if(!empty($validationErrors)) {
			$response = array('status' => 'error', 'operation' => 'get_parent_category_alias', 'validationErrors' => $validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}
		$result = array();
		$result = $this->Venue->find('all', array('conditions' => array('Venue.id' => $venue_id)));
		
		if($result[0]['Category'][0]['parent'] != 0){
			$sql = "SELECT * FROM categories where id = ".$result[0]['Category'][0]['parent'];
			$CategoryExists = $this->Category->query($sql);

			return $CategoryExists[0]['categories']['alias'];

			/*$response = array(
				'status' => 'success1',
				'operation' => 'update_pay_status',
				'data' => $CategoryExists[0]['categories']['alias'],
				);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;*/

		}

		return $result[0]['Category'][0]['alias'];
		/*$response = array(
			'status' => 'success2',
			'operation' => 'update_pay_status',
			'data' => $result[0]['Category'][0]['alias'],
			);
		$this->set(array('response' => $response, '_serialize' => array('response')));
		return;*/
	}


	public function send_mail_to_all($venue_id){
		$venueData = $this->Venue->findByid($venue_id);			
					
					
					

		#send update mail to all 4 (shoutoutadmin, venueadmin, notificationemail1, notificationemail2)
		$cc = array();			
		$to = array();			
		
		#shoutoutadmin
		/*$shoutoutadmin_id = $venueData['Venue']['user_id'];          
		if(!empty($shoutoutadmin_id)){
			$shoutoutadminExists = $this->User->findByid($shoutoutadmin_id);
			if(!empty($shoutoutadminExists)){			
				$shoutoutadmin_name = $shoutoutadminExists['User']['name'];
				$shoutoutadmin_email = $shoutoutadminExists['User']['email'];
				if(!empty($shoutoutadmin_email))
					array_push($to, $shoutoutadmin_email); 
			}
		}*/
		##
		
		#venueadmin
		$venueadmin_id = $venueData['Venue']['assigned_user_id'];  
		if(!empty($venueadmin_id)){
			$venueadminExists = $this->User->findByid($venueadmin_id);
			if(!empty($venueadminExists)){			
				$venueadmin_name = $venueadminExists['User']['name'];
				$venueadmin_email = $venueadminExists['User']['email'];
				if(!empty($venueadmin_email))
					array_push($to, $venueadmin_email); 
			}
		}
		##
		
		#bottle mail1 and mail2
		$this->BottleService = ClassRegistry::init('BottleService');
		$options = array('conditions' => array('BottleService.venue_id' => $venue_id));
		$bottleServiceExists = $this->BottleService->find('first', $options);
		if(!empty($bottleServiceExists)){
			$notification_mail1 = $bottleServiceExists['BottleService']['email'];
			if(!empty($notification_mail1))
					array_push($cc, $notification_mail1); 
					
			$notification_mail2 = $bottleServiceExists['BottleService']['email2'];
			if(!empty($notification_mail2))
					array_push($cc, $notification_mail2); 
			
		}
		##
		
		$from = array(SITE_EMAIL => SITE_TITLE);
		$subject = 'Shoutoutcity - Venue updated';
		$template = 'venue_updated';
		$variables = array(				
			'venuename' => $venueData['Venue']['name']				
			);
			
		if(!empty($to))	{
			if(!$this->User->send_mail_cc($from, $to, $subject, $template, $variables, $cc)) {
				//$response = array('status' => 'error', 'operation' => 'save_venue_profile');
				//$this->set(array('response' => $response, '_serialize' => array('response')));
				//return;
			}
		}
	}
#79 ->14-12-2016 to add new category
     public function add_new_category() {
        $auth_type = $this->getParams('auth_type', 'web');
        //$auth_key = $this->getParams('auth_key');
        $category_name = $this->getParams('cat_name');
        
        $today = date('Y-m-d H:i:s');
        $validationErrors = array();
      
        if(empty($category_name)){
            $validationErrors['cat_name'] = 'Please Enter the category.';
        }
        //$this->loadModel('State');
        //return $cityExists = $this->City->find('all');
      
        if(!empty($category_name))  {
			 $stateExists = $this->Category->find('first', array('conditions' => array('name' => $category_name)));
             if(!empty($stateExists) || isset($stateExists['Category']))
                $validationErrors['cat_name'] = 'Category already exist';
             /*$sql = "select name FROM categories WHERE name='".$category_name."'";
			 $cat_result = $this->Category->query($sql);
			 if(isset($cat_result) && $cat_result!= '') {
				$validationErrors['cat_name'] = 'Category already exist';
			 }*/
        }
        if(!empty($validationErrors)) {
            $response = array('status' => 'error', 'operation' => 'add_new_category', 'validationErrors' => $validationErrors);
            $this->set(array('response' => $response, '_serialize' => array('response')));
            return;
        }
        
            $input = array(
                //'city_id' => $city_id,
                'parent' => '0',
                'name' => $category_name,
                'display_name' => $category_name, 
                'alias' => 'new'.$category_name, 
                'status' => 'a', 
                'created' => $today
                );
              
            $this->Category->create();
            if(!$this->Category->save($input)) {
                /*$response = array('status' => 'error', 'operation' => 'add_new_category', 'validationErrors' => $this->Category->validationErrors);
                $this->set(array('response' => $response, '_serialize' => array('response')));
                return;*/
                $response = array(
				'status' => 'error',
				'operation' => 'add_new_category',
				'data' => array('msg' => 'Category cannot be added.'),
				);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
            }
            
                
        $new_category_id = $this->Category->getLastInsertId();
        
        $response = array(
            'status' => 'success',
            'operation' => 'add_new_category',
            'data' => array('msg' => 'Category has been added successfully.Please Refresh the Page.', 'id' => $new_category_id),
        );
        
        $this->set(array('response' => $response, '_serialize' => array('response')));
    } 
    
     public function add_new_subcategory() {
        $auth_type = $this->getParams('auth_type', 'web');
        //$auth_key = $this->getParams('auth_key');
        $category_id = $this->getParams('cat_name');
        $subcategory = $this->getParams('sub_cat_name');
        $today = date('Y-m-d H:i:s');
        $validationErrors = array();
      
        if(empty($category_id)){
            $validationErrors['cat_name'] = 'Please select the category.';
        }
        if(empty($subcategory)){
            $validationErrors['sub_cat_name'] = 'Please Enter the subcategory.';
        }
        //$this->loadModel('State');
        //return $cityExists = $this->City->find('all');
      
        if(!empty($subcategory))  {
            $stateExists = $this->Category->find('first', array('conditions' => array('name' => $category_name)));
            if(!empty($stateExists) || isset($stateExists['Category']))
                $validationErrors['cat_name'] = 'Category already exist';
             /*$sql = "select name FROM categories WHERE name='".$category_name."'";
			 $cat_result = $this->Category->query($sql);
			 if(isset($cat_result) && $cat_result!= '') {
				$validationErrors['cat_name'] = 'Category already exist';
			 }*/    
        }
        if(!empty($validationErrors)) {
            $response = array('status' => 'error', 'operation' => 'add_new_category', 'validationErrors' => $validationErrors);
            $this->set(array('response' => $response, '_serialize' => array('response')));
            return;
        }
        
            $input = array(
                //'city_id' => $city_id,
                'parent' => $category_id ,
                'name' => $subcategory,
                'display_name' => $subcategory, 
                'alias' => 'new'.$subcategory, 
                'status' => 'a', 
                'created' => $today
                );
              
            $this->Category->create();
            if(!$this->Category->save($input)) {
                $response = array('status' => 'error', 'operation' => 'add_new_category');
                $this->set(array('response' => $response, '_serialize' => array('response')));
                return;
            }
            
                
        $new_category_id = $this->Category->getLastInsertId();
        
        $response = array(
            'status' => 'success',
            'operation' => 'add_new_category',
            'data' => array('msg' => 'Subcategory has been added successfully.Please Refresh the Page.', 'id' => $new_category_id),
        );
        
        $this->set(array('response' => $response, '_serialize' => array('response')));
    } 
    /**
	 ** @purpose delete the venue logo
	 ** @input auth_type, venue_id
	 ** @output
	 */
	public function delete_venue_video() {
		
		$auth_type = $this->getParams('auth_type', 'web');
		$auth_key = $this->getParams('auth_key');
		$venue_id = $this->getParams('venue_id');
       $validationErrors = array();
		if(empty($venue_id))
			$validationErrors['venue_id'] = 'Venue is not selected';
		else {
			// Check if the venue is available or not
			$venueExists = $this->Venue->findByid($venue_id);
			if(empty($venueExists))
				$validationErrors['venue_id'] = 'Venue does not exists';
		}

		if(!empty($validationErrors)) {
			$response = array('status' => 'error', 'operation' => 'delete_venue_video', 'validationErrors' => $validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}
        
        

		$venue_input = array(
			'id' 	=> $venue_id,
			'video'	=> '',
			);
		if(!$this->Venue->save($venue_input, $validate = false)) {
			$response = array('status' => 'error', 'operation' => 'delete_venue_video', 'dataErrors' => $this->Venue->validationErrors);
			$this->set(array('response' => $response, '_serialize' => array('response')));
			return;
		}


		 //Unlink the venue video
		$venue_video = $venueExists['Venue']['video'];
		$var = substr($venue_video, 26, 0);  // returns "cde"
		//$venue_path = WWW_ROOT . DS . $venue_video;
		@unlink($var);


		 //SuccessResponse
		$response = array(
			'status' => 'success',
			'operation' => 'delete_venue_video',
			'data' => array('msg' => 'Venue video has been deleted successfully.Please Refresh the Page.', 'data' => $this->data,'var' => $var),
			);
		$this->set(array('response' => $response, '_serialize' => array('response')));
		
	}
    public function save_venue_admin_video() {
        $auth_type = $this->getParams('auth_type', 'web');
        $venue_id = $this->getParams('venue_id');
        $assigned_user_id = $this->getParams('assigned_user_id');
        $video_url = $this->getParams('video_url');
        $validationErrors = array();
      
        if(empty($venue_id)){
            $validationErrors['venue_id'] = 'Please select the venue_id.';
        }
        if(empty($assigned_user_id)){
            $validationErrors['assigned_user_id'] = 'Please Enter the assigned_user_id.';
        }
        if(empty($video_url)){
            $validationErrors['video_url'] = 'Please select the video_url.';
        }
        
        if(!empty($validationErrors)) {
            $response = array('status' => 'error', 'operation' => 'save_venue_admin_video', 'validationErrors' => $validationErrors);
            $this->set(array('response' => $response, '_serialize' => array('response')));
            return;
        }
        
           /* $input = array(
                'id' => $venue_id,
                'assigned_user_id' => $assigned_user_id,
                'video' => $video_url, 
               );*/
              
            //$this->Venue->create();
            $this->Venue->id = $venue_id;		
			if(!$this->Venue->saveField("video",$video_url)) {
				$response = array('status' => 'error', 'operation' => 'save_venue_admin_video');
                $this->set(array('response' => $response, '_serialize' => array('response')));
                return;
			}	

         $response = array(
            'status' => 'success',
            'operation' => 'save_venue_admin_video',
            'data' => array('msg' => 'Video  has been uploaded successfully.'),
        );
        
        $this->set(array('response' => $response, '_serialize' => array('response')));
    } 
    #79 ->14-12-2016 to add new category

}
