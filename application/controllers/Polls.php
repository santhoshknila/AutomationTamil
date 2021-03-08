<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Polls extends CI_Controller {
    function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->model("polls_model");		
        $this->load->model("notifications_model");
        $this->load->model("users_model");
        $this->load->library('session');
        $this->load->library('email');
    }
	
	public function index($page = 1){
		if(checkForAdminSession(1, true))redirect('admin/login');
		$data = array();
		$data['title'] = "Manage Polls";
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
		
		//$where['poll.status'] = 1;
		/**Pagination **/
		$select1 = ('count(distinct poll.pollingId) as count');
		$all = $this->polls_model->get($where,$orderby,$select1,$join,"","","","",$like,$or_like,$or_where, $where_in, $where_not);
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
		$config['base_url'] = base_url().'polls/index';
		$config["cur_page"] = $page;
		if(isset($get['sel']) && !empty($get['sel'])){
			$config["per_page"] = $get['sel'];
		} else{
			$config["per_page"] = $this->config->item('per_page');
		}
		$this->pagination->initialize($config);
		$offset = $limit * ($page - 1);
		
		$orderby['poll.pollingId'] = 'DESC';
		$data["polls"] = $this->polls_model->get($where,$orderby,$select,$join,$groupby,$limit,$offset,"",$like,$or_like,$or_where, $where_in, $where_not);
		load_default_template('polls/list',$data,$this);
		echo $this->template->render("", true);
	}
	
	public function addedit(){
		if(checkForAdminSession(1, true))redirect('admin/login');
		$data = array();
		$data["title"] = "Add polling";
		load_default_template('polls/addedit',$data,$this);
		echo $this->template->render("", true);
	}
	
	public function editpolls($id){
		if(checkForAdminSession(1, true))redirect('admin/login');
		$data['title'] = "Edit Polling";
		$select=array();
		$where['poll.pollingId'] = $id;
		$orderby = array();
		$data["editpolls"] = $this->polls_model->get($where,$orderby,$select);
		load_default_template('polls/addedit',$data,$this);
		echo $this->template->render("", true);
	}
	
	public function save(){
		$post = $this->input->post();
		if(isset($post) && !empty($post)){
			$result = $this->polls_model->save_update_polling($post);
			if($result == ""){
				$this->session->set_flashdata('message', '<div class="fail_msg">Polling not Added. Kindly try again!</div>');
			} else if($result){
				if(!empty($post['polls']['pollingId'])){
					$this->session->set_flashdata('message', '<div class="success_msg">Polling has been updated successfully.</div>');
				} else {
					$notifyToken = $this->notifications_model->getPollTokens();
					if(!empty($notifyToken)){
						$subject = "Polls Notifications on Tamil Ethos";			
						$message = 'Admin posted "'.$post['polls']['pollingQuestion'].'" in Polls';	
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
							"notifyType" => "polls",
							"notifyId" => $result,
							"timestamp" => $created_date
						);
						$senddetailsIOS = array(
							"attachment" => '',
							"media_type" => '',
							"notifyType" => "polls",
							"notifyId" => $result
						);
						$messageIOS = array(
							'title' => $subject,
							'body' => $message,
							'sound' => 'default',
							"notifyType" => "polls",
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
									'notifyType'	=> 'polls',
									'notifyId'		=> $result,
									'notifyReason'	=> 'Admin posted <b>'.$post['polls']['pollingQuestion'].'<b> in <b>Polls<b>',
									'createdBy'		=> 1,							
									'updatedBy'		=> 1,
								)
							);
							$receiveRequest = $this->notifications_model->notificationsLog($notify_data);
						}
					}
					$this->session->set_flashdata('message', '<div class="success_msg">Polling has been added successfully.</div>');
				}
			} 
			redirect("/polls");
		}
	}
	
	/*Status update the polls*/
	function updatestatusPolls(){
		if(empty($_POST['pollingId'])) return false;
		$data['poll']['isActive'] = (int) $_POST['relval'];
		$data['poll']['status'] = (int) $_POST['relval'];
		$this->db->where("pollingId", $_POST['pollingId']);
		$this->db->update('polling', $data['poll']);
		$affected_rows = $this->db->affected_rows();
		if($affected_rows == 1){
			return $affected_rows;
		} else {
			return false;
		}
	}
}