<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Events extends CI_Controller {
    function __construct() {
        parent::__construct();
        $this->load->database();
		$this->load->model("events_model");		
        $this->load->model("users_model");		
        $this->load->model("notifications_model");	
		$this->load->model('general_model');
        $this->load->library('session');
        $this->load->library('email');
    }
	
	public function index($page = 1){
		if(checkForAdminSession(1, true))redirect('admin/login');
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
		

		/**Pagination **/
		$select1 = ('count(distinct event.eventId) as count');
		$all = $this->events_model->get($where,$orderby,$select1,$join,"","","","",$like,$or_like,$or_where, $where_in, $where_not);
		if(!empty($all)){
			$config['total_rows'] = $all[0]->count;
		} else {
			$config['total_rows'] = 0;
		}
		$config["uri_segment"] = "3";

		if(isset($get['sel']) && !empty($get['sel'])){
		$limit = $get['sel'];
		} else{
		$limit = $this->config->item('per_page');
		}
		
		$choice = $config["total_rows"] / $limit;
		$config["num_links"] = floor($choice);
		$config['base_url'] = base_url().'events/index';
		$config["cur_page"] = $page;
		if(isset($get['sel']) && !empty($get['sel'])){
			$config["per_page"] = $get['sel'];
		} else{
			$config["per_page"] = $this->config->item('per_page');
		}
		$this->pagination->initialize($config);
		$offset = $limit * ($page - 1);
		
		$orderby['event.eventId'] = 'DESC';
		$data["events"] = $this->events_model->get($where,$orderby,$select,$join,$groupby,$limit,$offset,"",$like,$or_like,$or_where, $where_in, $where_not);
		load_default_template('events/list',$data,$this);
		echo $this->template->render("", true);
	}
	
	public function addedit(){
		if(checkForAdminSession(1, true))redirect('admin/login');
		$data = array();
		$data["title"] = "Add Events";
		$data["country"] = $this->general_model->getCountries(0);
		load_default_template('events/addedit',$data,$this);
		echo $this->template->render("", true);
	}
	
	public function editEvents($id){
		if(checkForAdminSession(1, true))redirect('admin/login');
		$data['title'] = "Edit Events";
		$select=array();
		$where['event.eventId'] = $id;
		$orderby = array();
		$data["country"] = $this->general_model->getCountries(0);
		$data["editevents"] = $this->events_model->get($where,$orderby,$select);
		load_default_template('events/addedit',$data,$this);
		echo $this->template->render("", true);
	}
	
	public function save(){
		$post = $this->input->post();
		if(isset($post) && !empty($post)){
			if(isset($post['events']['fileType'])){
				foreach($post['events']['fileType'] as $key=>$data){
					if($post['events']['fileType'][$key] == 'image'.$key){
						//$post['newsimg']['fileTypeval'][] = $post['news']['fileType'][$key];
						$post['eventimg']['fileTypeval'][] = 'image';
						//upload code here					
						if(isset($_FILES['filePathimages']['name'][$key]) && !empty($_FILES['filePathimages']['name'][$key])){
							$new_name = uniqid().'_'.time().'_'.$_FILES['filePathimages']['name'][$key];
							$getTotalcnt = $_FILES['filePathimages']['name'][$key];
							$countfiles = count($getTotalcnt);
							for($i=0; $i < $countfiles; $i++){
								$_FILES['file']['name']     = $_FILES['filePathimages']['name'][$key];
								$_FILES['file']['type']     = $_FILES['filePathimages']['type'][$key];
								$_FILES['file']['tmp_name'] = $_FILES['filePathimages']['tmp_name'][$key];
								$_FILES['file']['error']    = $_FILES['filePathimages']['error'][$key];
								$_FILES['file']['size']     = $_FILES['filePathimages']['size'][$key];
								
								$config = array(
									'upload_path' => "./uploads/events/",
									'allowed_types' => "jpg|png|jpeg|gif",
									'overwrite' => TRUE,
									'max_size' => "2048000",
									'file_name' => $new_name,
									'remove_spaces' => TRUE,
								);						
								// Load and initialize upload library
								$this->load->library('upload', $config);
								$this->upload->initialize($config);
								
								// Upload file to server
								if($this->upload->do_upload('file')){
									// Uploaded file data
									$fileData = $this->upload->data();
									$post['eventimg']['imagevideo_url'][] = $fileData['file_name'];
									
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
					} else if($post['events']['fileType'][$key] == 'url'.$key){
						$post['eventimg']['fileTypeval'][] = 'url';
						$post['eventimg']['imagevideo_url'][] = $post['events']["filePathURl"][$key];
					}
				}
			}
			unset($post['events']['fileType']);
			unset($post['events']['filePathimages']);
			unset($post['events']['filePathURl']);
			unset($post['submit']);
			$post['event']['updatedBy'] = $this->session->userid;
			$result = $this->events_model->save_update_events($post);
			if($result == ""){
				$this->session->set_flashdata('message', '<div class="fail_msg">Events not Added. Kindly try again!</div>');
			} else if($result){
				if(!empty($post['event']['eventId'])){
					$eventDetails = $this->events_model->getEvent($post['event']['eventId']);
					if(!empty($eventDetails)){
						foreach($eventDetails as $evtDetail){
							if($evtDetail->status == 1){
								$message = 'Your event has been approved <b>'.$post['event']['title'].'<b>';
								$resultVal = $result;
							} else if($evtDetail->status == 4){
								$message = 'Your event has been rejected <b>'.$post['event']['title'].'<b>';
								$resultVal = 0; 
							}
							$notify_data = array( 
								array(
									'fromUserId'    => 1,
									'toUserId'      => $evtDetail->createdBy,
									'notifyType'	=> 'events',
									'notifyId'		=> $resultVal,
									'notifyReason'	=> $message,
									'createdBy'		=> 1,							
									'updatedBy'		=> 1,
								)
							);
							$receiveRequest = $this->notifications_model->notificationsLog($notify_data);
						}
					}
					$this->session->set_flashdata('message', '<div class="success_msg">Events has been updated successfully.</div>');
				} else {
					$notifyToken = $this->notifications_model->getTokens();
					if(!empty($notifyToken)){
						$subject = "Events Notifications on Tamil Ethos";			
						$message = 'Admin posted "'.$post['event']['title'].'" in Events.';	
						$created_date = date('Y-m-d h:i');
						foreach($notifyToken as $ntoken){
							if($ntoken["deviceType"] == 1){
								$tokensAndroid[] = $ntoken["deviceToken"];
							} else if($ntoken["deviceType"] == 2){
								$tokensIOS[] = $ntoken["deviceToken"];
							}
						}
						$senddetailsAndroid = array(
							"title"=> $subject,
							"message"=>$message,
							"notifyType" => "events",
							"notifyId" => $result,
							"timestamp" => $created_date
						);
						$senddetailsIOS = array(
							"attachment" => '',
							"media_type" => '',
							"notifyType" => "events",
							"notifyId" => $result
						);
						$messageIOS = array(
							'title' => $subject,
							'body' => $message,
							'sound' => 'default',
							"notifyType" => "events",
							"notifyId" => $result
						);
						
						if(!empty($tokensAndroid)){
							$message_statusan = send_notification($tokensAndroid, $senddetailsAndroid);
						}
						
						if(!empty($tokensIOS)){
							$message_status = send_notificationIOS($tokensIOS, $senddetailsIOS, $messageIOS);
						}						
						
						foreach($notifyToken as $ntoken1){
							$notify_data = array( 
								array(
									'fromUserId'    => 1,
									'toUserId'      => $ntoken1['userid'],
									'notifyType'	=> 'events',
									'notifyId'		=> $result,
									'notifyReason'	=> 'Admin posted <b>'.$post['event']['title'].'<b> in <b>Events<b>.',
									'createdBy'		=> 1,							
									'updatedBy'		=> 1,
								)
							);
							$receiveRequest = $this->notifications_model->notificationsLog($notify_data);
						}
					}		
					$this->session->set_flashdata('message', '<div class="success_msg">Events has been added successfully.</div>');
				}
			} 
			redirect("/events");
		}
	}	
	
	/*Status update the polls*/
	function updatestatusEvents(){
		if(empty($_POST['eventId'])) return false;
		$data['event']['isActive'] = (int) $_POST['relval'];
		$data['event']['status'] = (int) $_POST['relval'];
		$this->db->where("eventId", $_POST['eventId']);
		$this->db->update('events', $data['event']);
		$affected_rows = $this->db->affected_rows();
		if($affected_rows == 1){
			$eventDetails = $this->events_model->getEvent($_POST['eventId']);
			$userdetails = $this->users_model->getUseriddetails($eventDetails[0]->createdBy);
			if(!empty($userdetails)){
				$subject = "Events on Tamil Ethos Smart app";			
				$message = "Your event has been approved.";	
				$created_date = date('Y-m-d h:i');
				$tokens[] = $userdetails->deviceToken;
				$senddetails = array("message"=>$message,"title"=> $subject,"timestamp" => $created_date);
				$message_status = send_notification($tokens, $senddetails);
				if(!empty($message_status)){
					$notify_data = array( 
						array(
							'fromUserId'    => 1,
							'toUserId'      => $eventDetails[0]->createdBy,
							'notifyType'	=> 'events',
							'notifyId'		=> $_POST['eventId'],
							'notifyReason'	=> 'Your '.$eventDetails[0]->title.' event has been approved by Admin',
							'createdBy'		=> 1,							
							'updatedBy'		=> 1,
						)
					);
					$receiveRequest = $this->notifications_model->notificationsLog($notify_data);
				}
			}
			return $affected_rows;
		} else {
			return false;
		}
	}
	
	/*Avtive update the polls*/
	function updateactiveEvents(){
		if(empty($_POST['eventId'])) return false;
		$data['event']['isActive'] = (int) $_POST['relval'];
		$this->db->where("eventId", $_POST['eventId']);
		$this->db->update('events', $data['event']);
		$affected_rows = $this->db->affected_rows();
		if($affected_rows == 1){
			return $affected_rows;
		} else {
			return false;
		}
	}
	
	function removeUploadfiles(){
		if(empty($_POST['uploadRowID'])) return false;
		$this->db->where('eventImageId', $_POST['uploadRowID']);
		$this->db->delete('eventimages');
		return true;
	}
	
}