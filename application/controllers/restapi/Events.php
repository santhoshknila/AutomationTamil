<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Events extends CI_Controller {
	private $error = array();
	function __construct() {
        parent::__construct();
        $this->load->database();		
        $this->load->model("events_model");		
        $this->load->model("users_model");		
        $this->load->model("auth_model");		
        $this->load->library('session');
        $this->load->library('email');
		$this->load->library('form_validation');
		$this->load->library('phpass');
		$this->load->helper('date');
    }
	
	public function index(){
		$userauth = array();
		$userauth['users_id']  = $this->input->get_request_header('User-ID', TRUE);
        $userauth['token']     = $this->input->get_request_header('Auth-Key', TRUE);
		if(checkForUserSession($userauth) == 1){
			$data = array();
			$data['title'] = "Manage Events";
			$select=array();
			$where=array();
			$orderby = array();
			$join = array();
			$groupby =array();
			$like = array();
			$or_like = array();
			$or_where = array();
			$where_in = array();
			$where_not = array();
			
			$post = $this->input->post();
			if (isset($post['page'])) {
				$page = $post['page'];
			} else {
				$page = 1;
			}
			$limit = 3;
			$filter_data = array(
				'type' 	=> $post['type'],
				'startDate' => $post['startDate'],
				'endDate'   => $post['endDate'],
				'start'   => ($page - 1) * $limit,
				'limit'   => $limit
			);
			
			$eventsList = array();
			$allEvents = $this->events_model->getallEventdetails($filter_data);	
			$allTotalEvents = count($this->events_model->getalltotalEventdetails($filter_data));	
			$byGroupEvent = group_by("startDate", $allEvents);			
			if (!empty($allEvents)) {
				foreach($allEvents as $evtRes){					
					$userdetails = $this->users_model->getUseriddetails($evtRes['createdBy']);
					if($evtRes['createdBy'] == 1){
						$createUser = 'Tamil Ethos';
					} else {
						$createUser = $userdetails->firstName;
					}
					
					$goingStatuscnt = $this->events_model->goingStatuscnt($evtRes['eventId']);
					$interestStatuscnt = $this->events_model->interestStatuscnt($evtRes['eventId']);
					$commentCnt = count($this->events_model->getTotaleventCntcomments($evtRes['eventId']));
					$mediaFiles = $this->events_model->getEventmedialist($evtRes['eventId']);
					
					$mediaarray = array();
					if(!empty($mediaFiles)){					
						foreach($mediaFiles as $media){
							$mediaarray[] = array(
								'imageId' => $media['eventImageId'],
								'fileType' => $media['fileType'],
								'path' => $media['imagevideo_url'],
							);
						}
					}
					$eventsList[] = array(
						'eventId' => $evtRes['eventId'],
						'title' => $evtRes['title'],	
						'startDate' => $evtRes['startDate'],
						'endDate' => $evtRes['endDate'],
						'startTime' => $evtRes['startTime'],
						'endTime' => $evtRes['endTime'],
						'locationDetails' => $evtRes['locationDetails'],
						'venueDetails' => $evtRes['venueDetails'],						
						'createdBy' => $createUser,
						'goingStatuscnt' => (string)$goingStatuscnt,
						'interestStatuscnt' => (string)$interestStatuscnt,
						'commentCnt' => (string)$commentCnt,
						'media' => $mediaarray,
					);
				}
				$response['eventList'] = $eventsList;
				$response['current_page'] = (int)$page;
				$response['total_page'] = ceil($allTotalEvents / $limit);
				$response['status'] = '200';
				$response['messages'] = 	' event listings';					
				json_output($response['status'],$response);
			} else {
				$response['eventList'] = $eventsList;
				$response['current_page'] = (int)0;
				$response['total_page'] = 0;
				$response['status'] = '200';
				$response['messages'] = 'No events found';					
				json_output($response['status'],$response);
			}			
		} else {
			$response['status'] = '401';
			$response['messages'] = 'Your Authorization details are incorrect.';					
			json_output($response['status'],$response);
		}
	}
	
	public function eventsDates(){
		$userauth = array();
		$userauth['users_id']  = $this->input->get_request_header('User-ID', TRUE);
        $userauth['token']     = $this->input->get_request_header('Auth-Key', TRUE);
		if(checkForUserSession($userauth) == 1){
			$eventsList = array();
			$allEventsDates = $this->events_model->getallEventDates();
			if (!empty($allEventsDates)) {
				foreach($allEventsDates as $monthDate){
					$eventsList[]= array('date' => $monthDate['startDate']);
					$datediff = strtotime($monthDate['endDate']) -  strtotime($monthDate['startDate']);
					$datediffcnt = round($datediff / (60 * 60 * 24));
					
					for($i=1; $i<= $datediffcnt; $i++){						
						$eventsList[] = array('date' => date('Y-m-d', strtotime('+'.$i.' day', strtotime($monthDate['startDate']))));						
					}					
				}
				$response['eventList'] = array_values(array_unique( $eventsList, SORT_REGULAR ));
				$response['status'] = '200';
				$response['messages'] = ' event listings';					
				json_output($response['status'],$response);
			} else {
				$response['eventList'] = $eventsList;
				$response['status'] = '200';
				$response['messages'] = 'No events dates found';					
				json_output($response['status'],$response);
			}	
			
		} else {
			$response['status'] = '401';
			$response['messages'] = 'Your Authorization details are incorrect.';					
			json_output($response['status'],$response);
		}
	}
	
	public function eventDetails($evtID, $page){
		$userauth = array();
		$userauth['users_id']  = $this->input->get_request_header('User-ID', TRUE);
        $userauth['token']     = $this->input->get_request_header('Auth-Key', TRUE);
		if(checkForUserSession($userauth) == 1){
			$detailsEvents = $this->events_model->getEventdetails($evtID);			
			if(!empty($detailsEvents)){
				$eventMoredetails = $detailsEvents[0];
				$mediaFiles = $this->events_model->getEventmedia($eventMoredetails->eventId);
				$mediaarray = array();
				if(!empty($mediaFiles)){					
					foreach($mediaFiles as $media){
						$mediaarray[] = array(
							'imageId' => $media['eventImageId'],
							'fileType' => $media['fileType'],
							'path' => $media['imagevideo_url'],
						);
					}
				}
				$goingStatus = $this->events_model->goingStatus($userauth['users_id'], $eventMoredetails->eventId);
				if(isset($goingStatus[0])){
					$goingStatus = (int)$goingStatus[0]->status;
				} else {
					$goingStatus = 0;
				}
				$interestStatus = $this->events_model->interestStatus($userauth['users_id'], $eventMoredetails->eventId);
				if(isset($interestStatus[0])){
					$interestStatus = (int)$interestStatus[0]->status;
				} else {
					$interestStatus = 0;
				}
				
				$limit = 5;
				$filter_data = array(
					'eventId'    => $eventMoredetails->eventId,
					'start'      => ($page - 1) * $limit,
					'limit'      => $limit
				);
				
				$commentsarray = array();
				$comments = $this->events_model->getEventcomments($filter_data);
				$commentsCnt = count($this->events_model->getTotaleventCntcomments($eventMoredetails->eventId));		
				if(!empty($comments)){
					foreach($comments as $cmt){
						$cmtuserdetails = $this->users_model->getUseriddetails($cmt['createdBy']);
						if($cmt['createdBy'] == 1){
							$cmtUser = 'Tamil Ethos Smart App';
						} else {
							$cmtUser = $cmtuserdetails->firstName;
						}
						$commentsarray[] = array(
							'userId' => $cmtuserdetails->userid,
							'userName' => $cmtUser,
							'userImg' => $cmtuserdetails->profileimg,
							'commentdesc' => $cmt['comments'],
							'commentDate' => date_time_ago($cmt['createdDate']),
						);
					}						
				}
				
				$userdetails = $this->users_model->getUseriddetails($eventMoredetails->createdBy);
				if($eventMoredetails->createdBy == 1){
					$createUser = 'Tamil Ethos';
				} else {
					$createUser = $userdetails->firstName;
				}
				
				$response['events'] = array(
					'eventId' => $eventMoredetails->eventId,
					'title' => $eventMoredetails->title,
					'description' => $eventMoredetails->description,
					'startDate' => $eventMoredetails->startDate,
					'endDate' => $eventMoredetails->endDate,
					'startTime' => $eventMoredetails->startTime,
					'endTime' => $eventMoredetails->endTime,
					'locationDetails' => $eventMoredetails->locationDetails,
					'venueDetails' => $eventMoredetails->venueDetails,
					'ccode_1' => $eventMoredetails->ccode_1,
					'phone_1' => $eventMoredetails->phone_1,
					'ccode_2' => $eventMoredetails->ccode_2,
					'phone_2' => $eventMoredetails->phone_2,
					'hostOrganization' => $eventMoredetails->hostOrganization,
					'createdUser' => $eventMoredetails->createdBy,
					'createdBy' => $createUser,
					'media' => $mediaarray,
					'goingStatus' => $goingStatus,
					'interestStatus' => $interestStatus,
					'comments' => $commentsarray,
					/*'shareLink' => base_url().'events?id='.$eventMoredetails->eventId,*/
					'shareLink' => $this->config->item('shareAddress').".page.link/?link=http://www.tamilchamber.org.events/events?id=".$eventMoredetails->eventId."&apn=".$this->config->item('shareAndriodapn')."&amv=10&ibi=".$this->config->item('shareIOSibi')."&isi=".$this->config->item('shareIOSisi')."&ius=tamilethos",
				);
				$response['current_page'] = (int)$page;
				$response['total_page'] = ceil($commentsCnt / $limit);
				$response['status'] = '200';
				$response['messages'] = $eventMoredetails->title.' Details';					
				json_output($response['status'],$response);
			} else {
				$response['status'] = '200';
				$response['messages'] = 'No events found';					
				json_output($response['status'],$response);
			}
		} else {
			$response['status'] = '401';
			$response['messages'] = 'Your Authorization details are incorrect.';					
			json_output($response['status'],$response);
		}
	}
	
	
	public function save(){
		$userauth = array();
		$userauth['users_id']  = $this->input->get_request_header('User-ID', TRUE);
        $userauth['token']     = $this->input->get_request_header('Auth-Key', TRUE);
		if(checkForUserSession($userauth) == 1){
			$post = $this->input->post();
			if(isset($post) && !empty($post)){
				$dataEvent['event']['eventId'] = $post['eventId'];
				$dataEvent['event']['title'] = $post['title'];
				$dataEvent['event']['description'] = $post['description'];
				$dataEvent['event']['startDate'] = $post['startDate'];
				$dataEvent['event']['endDate'] = $post['endDate'];
				$dataEvent['event']['startTime'] = $post['startTime'];
				$dataEvent['event']['endTime'] = $post['endTime'];
				$dataEvent['event']['locationDetails'] = $post['locationDetails'];
				$dataEvent['event']['venueDetails'] = $post['venueDetails'];
				$dataEvent['event']['ccode_1'] = $post['ccode_1'];
				$dataEvent['event']['phone_1'] = $post['phone_1'];
				$dataEvent['event']['ccode_2'] = $post['ccode_2'];
				$dataEvent['event']['phone_2'] = $post['phone_2'];
				$dataEvent['event']['hostOrganization'] = $post['hostOrganization'];
				$dataEvent['event']['status'] = 2;
				$dataEvent['event']['createdBy'] = $post['userid'];
				$dataEvent['event']['updatedBy'] = $post['userid'];
				if(isset($_FILES['userfile']['name']) && !empty($_FILES['userfile']['name'])){
					$files = $_FILES;
					$count = count($_FILES['userfile']['name']);
					$config = array(
						'upload_path' => "./uploads/events/",
						'allowed_types' => "jpg|png|jpeg|gif",
						'overwrite' => TRUE,
						'max_size' => "2048000",
						'remove_spaces' => TRUE,
					);
					
					for($i = 0; $i < $count; $i++) {
						$_FILES['userfile']['name']     = uniqid().'_'.time().'_'.$files['userfile']['name'][$i];
						$_FILES['userfile']['type']     = $files['userfile']['type'][$i];
						$_FILES['userfile']['tmp_name'] = $files['userfile']['tmp_name'][$i];
						$_FILES['userfile']['error']    = $files['userfile']['error'][$i];
						$_FILES['userfile']['size']     = $files['userfile']['size'][$i]; 
	   
						$this->load->library('upload', $config);

						if (!$this->upload->do_upload('userfile')){
							$error = array('error' => $this->upload->display_errors());
						} else {
							$fileData = $this->upload->data();							
							$dataEvent['eventimg']['imagevideo_url'][] = $fileData['file_name'];
							$dataEvent['eventimg']['fileTypeval'][] = 'image';
							
							$file = $fileData['file_name'];
							$path = $fileData['full_path'];
							$config_resize['image_library'] = 'gd2';  
							$config_resize['source_image'] = $path;
							$config_resize['create_thumb'] = false;
							$config_resize['maintain_ratio'] = TRUE;
							$config_resize['width'] = 250;
							$config_resize['height'] = 250;
							$config_resize['new_image'] = './uploads/events/thumb/'.$file;
							$this->load->library('image_lib',$config_resize);
							$this->image_lib->clear();
							$this->image_lib->initialize($config_resize);
							$this->image_lib->resize();
						}
					} 
				}
				
				if(isset($post['filePathURl']) && !empty($post['filePathURl'])){
					foreach($post['filePathURl'] as $resUrl){
						$dataEvent['eventimg']['fileTypeval'][] = 'url';
						$dataEvent['eventimg']['imagevideo_url'][] = $resUrl;
					}
				}
				$result = $this->events_model->save_update_events($dataEvent);
				$response['status'] = '200';
				if($result == ""){
					$response['messages'] = 'Events not Added. Kindly try again!';
				} else if($result){
					if(!empty($post['eventId'])){						
						$response['messages'] = 'Events has been updated successfully. Kindly waiting for admin approval.';
					} else {
						$response['messages'] = 'Events has been added successfully. Kindly waiting for admin approval.';
					}
				}
				json_output($response['status'],$response);
			}
		} else {
			$response['status'] = '401';
			$response['messages'] = 'Your Authorization details are incorrect.';					
			json_output($response['status'],$response);
		}
	}
	
	public function eventGoingupstatus(){
		$userauth = array();
		$userauth['users_id']  = $this->input->get_request_header('User-ID', TRUE);
        $userauth['token']     = $this->input->get_request_header('Auth-Key', TRUE);
		if(checkForUserSession($userauth) == 1){
			$post = $this->input->post();
			if(isset($post) && !empty($post)){
				$data['goSts']['eventId'] = $post['eventId'];
				$data['goSts']['status'] = $post['status'];
				$data['goSts']['createdBy'] = $userauth['users_id'];
				$data['goSts']['updatedBy'] = $userauth['users_id'];
				$result = $this->events_model->save_update_goingstaus($data);
				$response['status'] = '200';
				if($result == ""){
					$response['messages'] = 'Action not updated. Kindly try again!';
				} else if($result){					
					$response['messages'] = 'Action updated successfully.';					
				}
				json_output($response['status'],$response);
			}
		} else {
			$response['status'] = '401';
			$response['messages'] = 'Your Authorization details are incorrect.';					
			json_output($response['status'],$response);
		}	
	}
	
	public function eventInterestupstatus(){
		$userauth = array();
		$userauth['users_id']  = $this->input->get_request_header('User-ID', TRUE);
        $userauth['token']     = $this->input->get_request_header('Auth-Key', TRUE);
		if(checkForUserSession($userauth) == 1){
			$post = $this->input->post();
			if(isset($post) && !empty($post)){
				$data['interSts']['eventId'] = $post['eventId'];
				$data['interSts']['status'] = $post['status'];
				$data['interSts']['createdBy'] = $userauth['users_id'];
				$data['interSts']['updatedBy'] = $userauth['users_id'];
				$result = $this->events_model->save_update_intereststaus($data);
				$response['status'] = '200';
				if($result == ""){
					$response['messages'] = 'Action not updated. Kindly try again!';
				} else if($result){					
					$response['messages'] = 'Action updated successfully.';					
				}
				json_output($response['status'],$response);
			}
		} else {
			$response['status'] = '401';
			$response['messages'] = 'Your Authorization details are incorrect.';					
			json_output($response['status'],$response);
		}
	}
	
	public function addcomments(){
		$userauth = array();
		$userauth['users_id']  = $this->input->get_request_header('User-ID', TRUE);
        $userauth['token']     = $this->input->get_request_header('Auth-Key', TRUE);
		if(checkForUserSession($userauth) == 1){
			$post = $this->input->post();
			if(isset($post) && !empty($post)){
				$data['cmt']['eventId'] = $post['eventId'];
				$data['cmt']['comments'] = $post['eventComments'];
				$data['cmt']['createdBy'] = $userauth['users_id'];
				$data['cmt']['updatedBy'] = $userauth['users_id'];
				$result = $this->events_model->save_update_commentsevents($data);
				$response['status'] = '200';
				if($result == ""){
					$response['messages'] = 'Events Comments not added. Kindly try again!';
				} else if($result){					
					$response['messages'] = 'Events Comments added successfully.';					
				}
				json_output($response['status'],$response);
			} else {
				$response['status'] = '401';
				$response['messages'] = 'Events Comments post not added.';					
				json_output($response['status'],$response);
			}
		} else {
			$response['status'] = '401';
			$response['messages'] = 'Your Authorization details are incorrect.';					
			json_output($response['status'],$response);
		}
	}
	
	function removeUploadfiles(){
		$userauth = array();
		$userauth['users_id']  = $this->input->get_request_header('User-ID', TRUE);
        $userauth['token']     = $this->input->get_request_header('Auth-Key', TRUE);
		if(checkForUserSession($userauth) == 1){
			$post = $this->input->post();
			if(isset($post) && !empty($post)){
				$data['del']['uploadRowID'] = $post['uploadRowID'];
				$result = $this->events_model->del_removeUploadfiles($data);
				$response['status'] = '200';
				if($result == ""){
					$response['messages'] = 'Upload files not removed. Kindly try again!';
				} else if($result){					
					$response['messages'] = 'Upload files removed successfully.';
				}
				json_output($response['status'],$response);
			} else {
				$response['status'] = '401';
				$response['messages'] = 'Report abuse not added. Kindly try again!';					
				json_output($response['status'],$response);
			}
		} else {
			$response['status'] = '401';
			$response['messages'] = 'Your Authorization details are incorrect.';					
			json_output($response['status'],$response);
		}
	}
	
	public function eventDetele(){
		$userauth = array();
		$userauth['users_id']  = $this->input->get_request_header('User-ID', TRUE);
        $userauth['token']     = $this->input->get_request_header('Auth-Key', TRUE);
		if(checkForUserSession($userauth) == 1){
			$post = $this->input->post();
			if(isset($post) && !empty($post)){
				$data['del']['eventId'] = $post['eventId'];
				$data['del']['updatedBy'] = $userauth['users_id'];
				$result = $this->events_model->save_update_eventDelete($data);
				$response['status'] = '200';
				if($result == ""){
					$response['messages'] = 'Event not deleted. Kindly try again!';
				} else if($result){					
					$response['messages'] = 'Event deleted successfully.';
				}
				json_output($response['status'],$response);
			} else {
				$response['status'] = '401';
				$response['messages'] = 'Event not deleted. Kindly try again!';					
				json_output($response['status'],$response);
			}
		} else {
			$response['status'] = '401';
			$response['messages'] = 'Your Authorization details are incorrect.';					
			json_output($response['status'],$response);
		}
	}
	
}
