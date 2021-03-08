<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Notifications extends CI_Controller {
	private $error = array();
	function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->model("notifications_model");
        $this->load->model("users_model");
		$this->load->model("auth_model");
        $this->load->library('session');
        $this->load->library('email');
		$this->load->library('form_validation');
		$this->load->library('phpass');
		$this->load->helper('date');
    }
	
	public function getNotifications(){
		$userauth = array();
		$userauth['users_id']  = $this->input->get_request_header('User-ID', TRUE);
        $userauth['token']     = $this->input->get_request_header('Auth-Key', TRUE);
		if(checkForUserSession($userauth) == 1){
			$post = $this->input->post();
			if (isset($post['page'])) {
				$page = $post['page'];
			} else {
				$page = 1;
			}
			$limit = 12;			
			$input_data = array(
				'start'          => ($page - 1) * $limit,
				'limit'          => $limit,
				'loggedUser'	 => $userauth['users_id'],
				'readUpdate' => 1,
			);
			
			$notifyUserarray = array();
			$allNotify = $this->notifications_model->getNotify($input_data);
			$totalNotifycnt = $this->notifications_model->getNotifyTotal($input_data);			
			if (!empty($allNotify)) {
				foreach($allNotify as $notify){
									
					$userdetails = $this->users_model->getUseriddetails($notify['createdBy']);
					if(!empty($userdetails)){
						$userID = $userdetails->userid;
						if($userdetails->userid == 1){
							$firstName = 'Tamil Ethos';
						} else {
							$firstName = $userdetails->firstName;
						}
						$profileimg = $userdetails->profileimg;
					} else {
						$userID = '';
						$firstName = '';
						$profileimg = '';
					}
					if($userID !=0){
						$notifyUserarray[] = array(
							'notifylogId' => $notify['notifylogId'],
							'userId' => $userID,
							'userName' => $firstName,
							'userImg' => $profileimg,
							'notifyType' => $notify['notifyType'],
							'notifyId' => $notify['notifyId'],
							'message' => $notify['notifyReason'],
							'readStatus' => (int)$notify['readStatus'],
							'updatedDate' => date_time_ago($notify['createdDate']),
						);
					}
				}
				$response['notifyList'] = $notifyUserarray;
				$response['current_page'] = (int)$page;
				$response['total_page'] = ceil(count($totalNotifycnt) / $limit);
				$response['status'] = '200';
				$response['messages'] = 'Notifications listings';					
				json_output($response['status'],$response);
			} else {
				$response['notifyList'] = $notifyUserarray;
				$response['current_page'] = (int)0;
				$response['total_page'] = 0;
				$response['status'] = '200';
				$response['messages'] = 'No records found';					
				json_output($response['status'],$response);
			}			
		} else {
			$response['status'] = '401';
			$response['messages'] = 'Your Authorization details are incorrect.';					
			json_output($response['status'],$response);
		}
	}
	
	public function updateReadstatus(){
		$userauth = array();
		$userauth['users_id']  = $this->input->get_request_header('User-ID', TRUE);
        $userauth['token']     = $this->input->get_request_header('Auth-Key', TRUE);
		if(checkForUserSession($userauth) == 1){
			$post = $this->input->post();
			$input_data = array(
				'notifylogId' => $post['notifylogId'],
				'readStatus' => $post['readStatus'],
			);
			$readStatus = $this->notifications_model->updateReadstatus($input_data);
			if (!empty($readStatus)){				
				$readSts = $this->notifications_model->getNotifystatus($post['notifylogId']);
				if(isset($readSts[0])){
					$readstatus = (int)$readSts[0]['readStatus'];
				} else {
					$readstatus = 0;
				}
				$response['requestStatus'] = $readstatus;
				$response['status'] = '200';			
				$response['messages'] = 'Status Updated successfully.';
				json_output($response['status'],$response);
			} else {
				$response['requestStatus'] = '';
				$response['status'] = '200';
				$response['messages'] = 'No records found';					
				json_output($response['status'],$response);
			}			
		} else {
			$response['status'] = '401';
			$response['messages'] = 'Your Authorization details are incorrect.';					
			json_output($response['status'],$response);
		}
	}
	
	public function getUnreadNotify(){
		$userauth = array();
		$userauth['users_id']  = $this->input->get_request_header('User-ID', TRUE);
        $userauth['token']     = $this->input->get_request_header('Auth-Key', TRUE);
		if(checkForUserSession($userauth) == 1){					
			$input_data = array(				
				'loggedUser'	 => $userauth['users_id'],
			);
			$totalunreadNotifycnt = count($this->notifications_model->getunreadNotifyTotal($input_data));
			$response['notifyCnt'] = (string) $totalunreadNotifycnt;
			$response['status'] = '200';
			$response['messages'] = 'Total unread notifications count';					
			json_output($response['status'],$response);		
		} else {
			$response['status'] = '401';
			$response['messages'] = 'Your Authorization details are incorrect.';					
			json_output($response['status'],$response);
		}
	}	
}
