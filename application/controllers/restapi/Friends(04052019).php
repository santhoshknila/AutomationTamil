<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Friends extends CI_Controller {
	private $error = array();
	function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->model("friends_model");		
        $this->load->model("users_model");		
        $this->load->model("notifications_model");		
        $this->load->model("auth_model");		
        $this->load->library('session');
        $this->load->library('email');
		$this->load->library('form_validation');
		$this->load->library('phpass');
		$this->load->helper('date');
    }
	
	/*Search based show the friend list*/
	public function getfriendSearch(){
		$userauth = array();
		$userauth['users_id']  = $this->input->get_request_header('User-ID', TRUE);
        $userauth['token']     = $this->input->get_request_header('Auth-Key', TRUE);
		if(checkForUserSession($userauth) == 1){
			$post = $this->input->post();
			if(isset($post['page'])) {
				$page = $post['page'];
			} else {
				$page = 1;
			}
			
			$limit = 10;
			$filter_data = array(
				'start'          => ($page - 1) * $limit,
				'limit'          => $limit,
				'search'		 => $post['search'],
			);
			
			$listUserarray = array();
			$allUserlist = $this->friends_model->getFriends($filter_data);
			$totalUserlist = $this->friends_model->getTotalFriends($filter_data);
			$response['userCnt'] = count($totalUserlist);
			
			if(!empty($post['search']) && !empty($allUserlist)){
				foreach($allUserlist as $userList){
					if($userList['userid'] != $userauth['users_id']){
						$userdetails = $this->users_model->getUseriddetails($userList['userid']);
						$feiendSts = $this->friends_model->getFriendstatus($userList['userid'], $userauth['users_id']);
						if(isset($feiendSts[0])){
							$fristatus = (int)$feiendSts[0]['status'];
							$friendID = (string)$feiendSts[0]['friendId'];
							$requestSenderID = (string)$feiendSts[0]['sendRequestUserId'];
						} else {
							$fristatus = 0;
							$friendID = '';
							$requestSenderID = '';
						}
						$mutualFriend = $this->friends_model->getMutualfriends($userauth['users_id'], $userdetails->userid);
						$listUserarray[] = array(
							'friendID' => $friendID,
							'requestSenderID' => $requestSenderID,
							'userId' => $userdetails->userid,
							'userName' => $userdetails->firstName.' '.$userdetails->surName,
							'userImg' => $userdetails->profileimg,
							'firebaseId' => $userdetails->firebaseId,
							'mutualFri' => (string)$mutualFriend." Mutual friends",
							'friendStatus' => $fristatus,
						);
					}
				}
				$response['userList'] = $listUserarray;
				$response['current_page'] = (int)$page;
				$response['total_page'] = ceil(count($totalUserlist) / $limit);
				$response['status'] = '200';			
				$response['messages'] = 'Search Listing successfully!';
				json_output($response['status'],$response);
			} else {
				$response['userList'] = $listUserarray;
				$response['current_page'] = (int)0;
				$response['total_page'] = (int)0;
				$response['status'] = '200';			
				$response['messages'] = 'No results found!';
				json_output($response['status'],$response);
			}
		} else {
			$response['status'] = '401';
			$response['messages'] = 'Your Authorization details are incorrect.';					
			json_output($response['status'],$response);
		}
	}
	
	/*Send the friends request*/
	public function friendSendrequest(){
		$userauth = array();
		$userauth['users_id']  = $this->input->get_request_header('User-ID', TRUE);
        $userauth['token']     = $this->input->get_request_header('Auth-Key', TRUE);
		if(checkForUserSession($userauth) == 1){
			$post = $this->input->post();
			$input_data = array(
				'loggedUser'     => $userauth['users_id'],
				'requestUser'		 => $post['sendRequestUserId'],
			);
			$friRequest = $this->friends_model->sendRequest($input_data);
			if(!empty($friRequest)){
				$feiendSts = $this->friends_model->getFriendstatus($post['sendRequestUserId'], $userauth['users_id']);
				if(isset($feiendSts[0]['status'])){
					$fristatus = (int)$feiendSts[0]['status'];
				} else {
					$fristatus = 0;
				}
				
				/*Notifications send to request friend */
				$userdetails = $this->users_model->getUseriddetails($post['sendRequestUserId']);
				$userdetailsSent = $this->users_model->getUseriddetails($userauth['users_id']);
				$subject = 'Friend Request';			
				$message = $userdetailsSent->firstName.' '.$userdetailsSent->surName.' sent you a friend request';
				$created_date = date('Y-m-d h:i');	
				if($userdetails->deviceType == 1){
					$tokensAndroid[] = $userdetails->deviceToken;
				} else if($userdetails->deviceType == 2){
					$tokensIOS[] = $userdetails->deviceToken;
				}
				$senddetailsAndroid = array(
					"title"=> $subject,
					"message"=>$message,
					"notifyType" => "friendRequest",
					"notifyId" => '',
					"timestamp" => $created_date
				);
				$senddetailsIOS = array(
					"attachment" => '',
					"media_type" => '',
					"notifyType" => "friendRequest",
					"notifyId" => ''
				);
				$messageIOS = array(
					'title' => $subject,
					'body' => $message,
					'sound' => 'default',
					"notifyType" => "friendRequest",
					"notifyId" => '',
					"timestamp" => $created_date
				);
				
				if(!empty($tokensAndroid)){
					$message_statusan = send_notification($tokensAndroid, $senddetailsAndroid);
				} 
				
				if(!empty($tokensIOS)){
					$message_status = send_notificationIOS($tokensIOS, $senddetailsIOS, $messageIOS);					
				}
				
				
				if(!empty($message_status) || !empty($message_statusan)){
					$notify_data = array(
						array(
							'fromUserId'    => $userauth['users_id'],
							'toUserId'      => $post['sendRequestUserId'],
							'notifyType'	=> 'friendRequest',
							'notifyId'		=> $userauth['users_id'],
							'notifyReason'	=> '<b>'.$userdetailsSent->firstName.' '.$userdetailsSent->surName.'<b> sent you a friend request',
							'createdBy'		=> $userauth['users_id'],							
							'updatedBy'		=> $userauth['users_id'],
						)/*,
						array(
							'fromUserId'    => $userauth['users_id'],
							'toUserId'      => $userauth['users_id'],
							'notifyType'	=> 'Friend',
							'notifyReason'	=> 'You have sent friend request to <b>'.$userdetails->firstName.' '.$userdetails->surName.'<b>',
							'createdBy'		=> $userauth['users_id'],							
							'updatedBy'		=> $userauth['users_id'],
						)*/
					);
					$receiveRequest = $this->notifications_model->notificationsLog($notify_data);
				}
				$response['requestStatus'] = $fristatus;
				$response['status'] = '200';			
				$response['messages'] = 'Friend request send successfully.';
				json_output($response['status'],$response);
			} else {
				$response['requestStatus'] = '';
				$response['status'] = '200';			
				$response['messages'] = 'Friend request not send!';
				json_output($response['status'],$response);
			}
		} else {
			$response['status'] = '401';
			$response['messages'] = 'Your Authorization details are incorrect.';					
			json_output($response['status'],$response);
		}
	}
	
	/*Get the receive request*/
	public function friendReceiverequest(){
		$userauth = array();
		$userauth['users_id']  = $this->input->get_request_header('User-ID', TRUE);
        $userauth['token']     = $this->input->get_request_header('Auth-Key', TRUE);
		if(checkForUserSession($userauth) == 1){
			$post = $this->input->post();
			if(isset($post['page'])) {
				$page = $post['page'];
			} else {
				$page = 1;
			}
			
			$limit = 10;
			$filter_data = array(
				'start'          => ($page - 1) * $limit,
				'limit'          => $limit,
				'receiveID'		 => $userauth['users_id'],
			);
			
			$listUserarray = array();			
			$receiveRequest = $this->friends_model->receiveRequest($filter_data);
			$totalReceive = $this->friends_model->receiveTotalRequest($filter_data);
			$response['userCnt'] = count($totalReceive);
			if(!empty($receiveRequest)){
				foreach($receiveRequest as $userList){
					$userdetails = $this->users_model->getUseriddetails($userList['sendRequestUserId']);
					$feiendSts = $this->friends_model->getfristatus($userList['friendId']);
					if(isset($feiendSts[0])){
						$fristatus = (int)$feiendSts[0]['status'];
					} else {
						$fristatus = 0;
					}
					$mutualFriend = $this->friends_model->getMutualfriends($userauth['users_id'], $userdetails->userid);
					$listUserarray[] = array(
						'friendID' => $userList['friendId'],
						'userId' => $userdetails->userid,
						'userName' => $userdetails->firstName,
						'userImg' => $userdetails->profileimg,
						'mutualFri' => (string)$mutualFriend." Mutual friends",
						'friendStatus' => $fristatus,
					);
				}
				$response['userList'] = $listUserarray;
				$response['current_page'] = (int)$page;
				$response['total_page'] = ceil(count($totalReceive) / $limit);
				$response['status'] = '200';
				$response['messages'] = 'Request Friend Listing.';
				json_output($response['status'],$response);
			} else {
				$response['userList'] = $listUserarray;
				$response['current_page'] = (int)$page;
				$response['total_page'] = ceil(count($totalReceive) / $limit);
				$response['status'] = '200';			
				$response['messages'] = 'No request friends list!';
				json_output($response['status'],$response);
			}
		} else {
			$response['status'] = '401';
			$response['messages'] = 'Your Authorization details are incorrect.';					
			json_output($response['status'],$response);
		}
	}
	
	/* Accept/Unfriend/Cancel Request details update*/
	public function updateRequest(){
		$userauth = array();
		$userauth['users_id']  = $this->input->get_request_header('User-ID', TRUE);
        $userauth['token']     = $this->input->get_request_header('Auth-Key', TRUE);
		if(checkForUserSession($userauth) == 1){
			$post = $this->input->post();
			$input_data = array(
				'loggedUser'     => $userauth['users_id'],
				'friendID' => $post['friendID'],
				'updateStatus' => $post['updateStatus'],
			);
			$friRequest = $this->friends_model->updateRequest($input_data);			
			if(!empty($friRequest)){				
				$feiendSts = $this->friends_model->getfristatus($post['friendID']);
				if(!empty($feiendSts)){				 
					if(isset($feiendSts[0])){
						$fristatus = (int)$feiendSts[0]['status'];
					} else {
						$fristatus = 0;
					}
					/*Notifications send to request friend */
					if($post['updateStatus'] == 1){
						$notifyReason = 'became friends.'; 
					} /*else if($post['updateStatus'] == 3){
						$notifyReason = "You are unfriend"; 
					} else if($post['updateStatus'] == 4){
						$notifyReason = "Request Canceled"; 
					}*/
					
					/*Notifications send to request friend */
					$userdetails = $this->users_model->getUseriddetails($feiendSts[0]['sendRequestUserId']);
					$userdetailsSent = $this->users_model->getUseriddetails($userauth['users_id']);
					$subject = 'Friend Request';			
					$message = $userdetailsSent->firstName.' '.$userdetailsSent->surName.' accepted your friend request';
					$created_date = date('Y-m-d h:i');	
					if($userdetails->deviceType == 1){
						$tokensAndroid[] = $userdetails->deviceToken;
					} else if($userdetails->deviceType == 2){
						$tokensIOS[] = $userdetails->deviceToken;
					}
					$senddetailsAndroid = array(
						"title"=> $subject,
						"message"=>$message,
						"notifyType" => "friendRequest",
						"notifyId" => '',
						"timestamp" => $created_date
					);
					$senddetailsIOS = array(
						"attachment" => '',
						"media_type" => '',
						"notifyType" => "friendRequest",
						"notifyId" => ''
					);
					$messageIOS = array(
						'title' => $subject,
						'body' => $message,
						'sound' => 'default',
						"notifyType" => "friendRequest",
						"notifyId" => '',
						"timestamp" => $created_date
					);
					
					if(!empty($tokensAndroid)){
						$message_statusan = send_notification($tokensAndroid, $senddetailsAndroid);
					} 
					
					if(!empty($tokensIOS)){
						$message_status = send_notificationIOS($tokensIOS, $senddetailsIOS, $messageIOS);				
					}					
					
					$notify_data = array(
						array(
							'fromUserId'    => $userauth['users_id'],
							'toUserId'      => $feiendSts[0]['sendRequestUserId'],
							'notifyType'	=> 'friendAccept',
							'notifyId' 		=> $userauth['users_id'],
							'notifyReason'	=> '<b> '.$userdetailsSent->firstName.' '.$userdetailsSent->surName.'<b> accepted your friend request',
							'createdBy'		=> $userauth['users_id'],							
							'updatedBy'		=> $userauth['users_id'],
						),
						array(
							'fromUserId'    => $userauth['users_id'],
							'toUserId'      => $feiendSts[0]['sendRequestUserId'],
							'notifyType'	=> 'friendAccept',
							'notifyId' 		=> $userauth['users_id'],
							'notifyReason'	=> 'You and <b>'.$userdetailsSent->firstName.' '.$userdetailsSent->surName.'<b> became friends',
							'createdBy'		=> $userauth['users_id'],							
							'updatedBy'		=> $userauth['users_id'],
						),
						array(
							'fromUserId'    => $userauth['users_id'],
							'toUserId'      => $userauth['users_id'],
							'notifyType'	=> 'friendAccept',
							'notifyId' 		=> $feiendSts[0]['sendRequestUserId'],
							'notifyReason'	=> 'You and <b>'.$userdetails->firstName.' '.$userdetails->surName.'<b> became friends',
							'createdBy'		=> $userauth['users_id'],							
							'updatedBy'		=> $userauth['users_id'],
						)
					);
					$receiveRequest = $this->notifications_model->notificationsLog($notify_data);
				} else {
					$fristatus = 0;
				}
				$response['requestStatus'] = $fristatus;
				$response['status'] = '200';			
				$response['messages'] = 'Request Updated successfully.';
				json_output($response['status'],$response);
			} else {
				$response['requestStatus'] = '';
				$response['status'] = '200';			
				$response['messages'] = 'Request Not Updated!';
				json_output($response['status'],$response);
			}
		} else {
			$response['status'] = '401';
			$response['messages'] = 'Your Authorization details are incorrect.';					
			json_output($response['status'],$response);
		}
	}
	
	/*My connections*/
	public function myConnections(){
		$userauth = array();
		$userauth['users_id']  = $this->input->get_request_header('User-ID', TRUE);
        $userauth['token']     = $this->input->get_request_header('Auth-Key', TRUE);
		if(checkForUserSession($userauth) == 1){	
			$post = $this->input->post();
			if(isset($post['page'])) {
				$page = $post['page'];
			} else {
				$page = 1;
			}
			
			$limit = 10;
			$filter_data = array(
				'start'          => ($page - 1) * $limit,
				'limit'          => $limit,
				'loggedUser'     => $post['friendId'],
			);
			
			$listUserarray = array();
			$friRequest = $this->friends_model->myConnections($filter_data);			
			$friRequesttot = $this->friends_model->myConnectionsTotal($filter_data);
			$response['userCnt'] = count($friRequesttot);
			if(!empty($friRequest)){
				foreach($friRequest as $userList){	
					//print_r($userList);
					//exit;
					if($post['friendId'] != $userList['receiveRequestId']){
						$userID = $userList['receiveRequestId'];						
					} else if($post['friendId'] != $userList['sendRequestUserId']){
						$userID = $userList['sendRequestUserId'];
					} else {
						$userID = 0;
					}					
					$userdetails = $this->users_model->getUseriddetails($userID);
					if(!empty($userdetails)){
						$userID = $userdetails->userid;
						$firstName = $userdetails->firstName;
						$profileimg = $userdetails->profileimg;
						$firebaseId = $userdetails->firebaseId;
					} else {
						$userID = '';
						$firstName = '';
						$profileimg = '';
						$firebaseId = '';
					}
					$mutualFriend = $this->friends_model->getMutualfriends($userauth['users_id'], $userdetails->userid);
					$listUserarray[] = array(
						'friendID' => $userList['friendId'],
						'userId' => $userID,
						'userName' => $firstName,
						'userImg' => $profileimg,
						'firebaseId' => $firebaseId,
						'mutualFri' => (string)$mutualFriend." Mutual friends",
						'friendStatus' => (int)$userList['status'],
					);					
				}
				$response['userList'] = $listUserarray;
				$response['current_page'] = (int)$page;
				$response['total_page'] = ceil(count($friRequesttot) / $limit);
				$response['status'] = '200';			
				$response['messages'] = 'My connections Listing successfully!';
				json_output($response['status'],$response);
			} else {
				$response['userList'] = $listUserarray;
				$response['current_page'] = (int)$page;
				$response['total_page'] = ceil(count($friRequesttot) / $limit);
				$response['status'] = '200';			
				$response['messages'] = 'No results found!';
				json_output($response['status'],$response);
			}
			
		} else {
			$response['status'] = '401';
			$response['messages'] = 'Your Authorization details are incorrect.';					
			json_output($response['status'],$response);
		}
	}
	
	/*My connection friends search*/
	public function myConnectionFriends(){
		$userauth = array();
		$userauth['users_id']  = $this->input->get_request_header('User-ID', TRUE);
        $userauth['token']     = $this->input->get_request_header('Auth-Key', TRUE);
		if(checkForUserSession($userauth) == 1){	
			$post = $this->input->post();
			if(isset($post['page'])) {
				$page = $post['page'];
			} else {
				$page = 1;
			}
			
			if(!empty($post['searchVal'])){
				$searchVal = $post['searchVal'];  
			} else {
				$searchVal = ''; 
			}
			
			$limit = 10;
			$filter_data = array(
				'start'          => ($page - 1) * $limit,
				'limit'          => $limit,
				'loggedUser'     => $userauth['users_id'],
				'searchVal'     => $searchVal,
			);
			
			$listUserarray = array();
			$friRequest = $this->friends_model->myConnectionsfri($filter_data);			
			$friRequesttot = $this->friends_model->myConnectionsfriTotal($filter_data);			
			$response['userCnt'] = $friRequesttot;
			if(!empty($friRequest)){
				foreach($friRequest as $userList){	
					//print_r($userList);
					//exit;
					if($userauth['users_id'] != $userList['receiveRequestId']){
						$userID = $userList['receiveRequestId'];						
					} else if($userauth['users_id'] != $userList['sendRequestUserId']){
						$userID = $userList['sendRequestUserId'];
					} else {
						$userID = 0;
					}	
					$userdetails = $this->users_model->getUseriddetails($userID);
					if(!empty($userdetails)){
						$userID = $userdetails->userid;
						$firstName = $userdetails->firstName.' '.$userdetails->surName;
						$profileimg = $userdetails->profileimg;
						$firebaseId = $userdetails->firebaseId;
					} else {
						$userID = '';
						$firstName = '';
						$profileimg = '';
						$firebaseId = '';
					}
					$mutualFriend = $this->friends_model->getMutualfriends($userauth['users_id'], $userdetails->userid);
					$listUserarray[] = array(
						'friendID' => $userList['friendId'],
						'userId' => $userID,
						'userName' => $firstName,
						'userImg' => $profileimg,
						'firebaseId' => $firebaseId,
						'mutualFri' => (string)$mutualFriend." Mutual friends",
						'friendStatus' => (int)$userList['status'],
					);					
				}
				$response['userList'] = $listUserarray;
				$response['current_page'] = (int)$page;
				$response['total_page'] = ceil($friRequesttot / $limit);
				$response['status'] = '200';			
				$response['messages'] = 'My connections Listing successfully!';
				json_output($response['status'],$response);
			} else {
				$response['userList'] = $listUserarray;
				$response['current_page'] = (int)$page;
				$response['total_page'] = ceil($friRequesttot / $limit);
				$response['status'] = '200';			
				$response['messages'] = 'No results found!';
				json_output($response['status'],$response);
			}			
		} else {
			$response['status'] = '401';
			$response['messages'] = 'Your Authorization details are incorrect.';					
			json_output($response['status'],$response);
		}
	}
	
	public function aboutFriendConnect(){
		$userauth = array();
		$userauth['users_id']  = $this->input->get_request_header('User-ID', TRUE);
        $userauth['token']     = $this->input->get_request_header('Auth-Key', TRUE);
		if(checkForUserSession($userauth) == 1){	
			$post = $this->input->post();
			if(isset($post['page'])) {
				$page = $post['page'];
			} else {
				$page = 1;
			}
			
			$limit = 10;
			$filter_data = array(
				'start'          => ($page - 1) * $limit,
				'limit'          => $limit,
				'loggedUser'     => $post['friendId'],
			);
			
			$listUserarray = array();
			$friRequest = $this->friends_model->myConnections($filter_data);			
			$friRequesttot = $this->friends_model->myConnectionsTotal($filter_data);
			$response['userCnt'] = count($friRequesttot);
			if(!empty($friRequest)){
				foreach($friRequest as $userList){	
					//print_r($userList);
					//exit;
					if($post['friendId'] != $userList['receiveRequestId']){
						$userID = $userList['receiveRequestId'];						
					} else if($post['friendId'] != $userList['sendRequestUserId']){
						$userID = $userList['sendRequestUserId'];
					} else {
						$userID = 0;
					}					
					$userdetails = $this->users_model->getUseriddetails($userID);
					if(!empty($userdetails)){
						$userID = $userdetails->userid;
						$firstName = $userdetails->firstName;
						$profileimg = $userdetails->profileimg;
						$firebaseId = $userdetails->firebaseId;
					} else {
						$userID = '';
						$firstName = '';
						$profileimg = '';
						$firebaseId = '';
					}
					$mutualFriend = $this->friends_model->getMutualfriendsabout($userauth['users_id'], $userID);
					$listUserarray[] = array(
						'friendID' => $userList['friendId'],
						'userId' => $userID,
						'userName' => $firstName,
						'userImg' => $profileimg,
						'firebaseId' => $firebaseId,
						'mutualFri' => (string)$mutualFriend." Mutual friends",
						'friendStatus' => (int)$userList['status'],
					);					
				}
				$response['userList'] = $listUserarray;
				$response['current_page'] = (int)$page;
				$response['total_page'] = ceil(count($friRequesttot) / $limit);
				$response['status'] = '200';			
				$response['messages'] = 'My Friends Listing successfully!';
				json_output($response['status'],$response);
			} else {
				$response['userList'] = $listUserarray;
				$response['current_page'] = (int)$page;
				$response['total_page'] = ceil(count($friRequesttot) / $limit);
				$response['status'] = '200';			
				$response['messages'] = 'No results found!';
				json_output($response['status'],$response);
			}
			
		} else {
			$response['status'] = '401';
			$response['messages'] = 'Your Authorization details are incorrect.';					
			json_output($response['status'],$response);
		}
	}
	
	/*Suggest friends*/
	public function suggestFriends(){
		$userauth = array();
		$userauth['users_id']  = $this->input->get_request_header('User-ID', TRUE);
        $userauth['token']     = $this->input->get_request_header('Auth-Key', TRUE);
		if(checkForUserSession($userauth) == 1){
			$post = $this->input->post();
			if(isset($post['page'])) {
				$page = $post['page'];
			} else {
				$page = 1;
			}
			if(!empty($post['logUserCountry'])){
				$userCountry = $post['logUserCountry'];
			} else {
				$userCountry = 202;
			}
			
			if(!empty($post['logUserProvince'])){
				$userCity = $post['logUserProvince'];
			} else {
				$userCity = 3238;
			}
			
			$limit = 30;
			$page = 1;
			$filter_data = array(
				'loggedUser' => $userauth['users_id'],
				'logUserCountry' => $userCountry,
				'logUserProvince' => $userCity,
				'start'      => ($page - 1) * $limit,
				'limit'      => $limit,				
			);
			
			$listUserarray = array();
			$allUserlist = $this->friends_model->getsuggestFriends($filter_data);
			//$totalUserlist = $this->friends_model->getsuggestTotalFriends($filter_data);
			$response['userCnt'] = count($allUserlist);
			
			if(!empty($allUserlist)){				
				foreach($allUserlist as $userList){
					if($userList['userid'] != $userauth['users_id']){
						$userdetails = $this->users_model->getUseriddetails($userList['userid']);
						$feiendSts = $this->friends_model->getFriendstatus($userList['userid'], $userauth['users_id']);
						if(isset($feiendSts[0])){
							$fristatus = (int)$feiendSts[0]['status'];
							$friendID = (string)$feiendSts[0]['friendId'];
							$requestSenderID = (string)$feiendSts[0]['sendRequestUserId'];
						} else {
							$fristatus = 0;
							$friendID = '';
							$requestSenderID = '';
						}
						//$mutualFriend = $this->friends_model->getMutualfriends($userauth['users_id'], $userdetails->userid);
						
						$listUserarray[] = array(
							'friendID' => $friendID,
							'requestSenderID' => $requestSenderID,
							'userId' => $userdetails->userid,
							'userName' => $userdetails->firstName.' '.$userdetails->surName,
							'userImg' => $userdetails->profileimg,
							'firebaseId' => $userdetails->firebaseId,
							'mutualFri' => (string)$userList['TotalMutualFriends']." Mutual friends",
							'friendStatus' => $fristatus,
						);
					}
				}				
				$response['userList'] = $listUserarray;
				$response['current_page'] = (int)$page;
				$response['total_page'] = ceil(count($allUserlist) / $limit);
				$response['status'] = '200';			
				$response['messages'] = 'Find friends listing successfully!';
				json_output($response['status'],$response);
			} else {
				$response['userList'] = $listUserarray;
				$response['current_page'] = (int)0;
				$response['total_page'] = (int)0;
				$response['status'] = '200';			
				$response['messages'] = 'No Friend Suggestion Found!';
				json_output($response['status'],$response);
			}
		} else {
			$response['status'] = '401';
			$response['messages'] = 'Your Authorization details are incorrect.';					
			json_output($response['status'],$response);
		}
	}
	
	/* Getting Overall Fiends */
	public function myTotalFriends(){
		$userauth = array();
		$userauth['users_id']  = $this->input->get_request_header('User-ID', TRUE);
        $userauth['token']     = $this->input->get_request_header('Auth-Key', TRUE);
		if(checkForUserSession($userauth) == 1){
			$filter_data = array(
				'loggedUser'     => $userauth['users_id'],
			);
			
			$listUserarray = array();
			$friRequest = $this->friends_model->myTotalfriends($filter_data);
			if(!empty($friRequest)){
				foreach($friRequest as $userList){	
					//print_r($userList);
					//exit;
					if($userauth['users_id'] != $userList['receiveRequestId']){
						$userID = $userList['receiveRequestId'];						
					} else if($userauth['users_id'] != $userList['sendRequestUserId']){
						$userID = $userList['sendRequestUserId'];
					} else {
						$userID = 0;
					}	
					$userdetails = $this->users_model->getUseriddetails($userID);
					if(!empty($userdetails)){
						$userID = $userdetails->userid;
						$firstName = $userdetails->firstName.' '.$userdetails->surName;
						$profileimg = $userdetails->profileimg;						
					} else {
						$userID = '';
						$firstName = '';
						$profileimg = '';						
					}					
					$listUserarray[] = array(						
						'userId' => $userID,
						'userName' => $firstName,
						'userImg' => $profileimg,						
					);					
				}
				$response['userList'] = $listUserarray;				
				$response['status'] = '200';			
				$response['messages'] = 'My friends Listing successfully!';
				json_output($response['status'],$response);
			} else {
				$response['userList'] = $listUserarray;				
				$response['status'] = '200';			
				$response['messages'] = 'No results found!';
				json_output($response['status'],$response);
			}			
		} else {
			$response['status'] = '401';
			$response['messages'] = 'Your Authorization details are incorrect.';					
			json_output($response['status'],$response);
		}
	}
	
}