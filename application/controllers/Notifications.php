<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Notifications extends CI_Controller {
    function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->model("notifications_model");
        $this->load->library('session');
        $this->load->library('email');
    }
	
	public function index($page = 1){
		if(checkForAdminSession(1, true))redirect('admin/login');
		$data = array();
		$data['title'] = "Manage Notifications";
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
		$select1 = ('count(distinct notify.notifyId) as count');
		$all = $this->notifications_model->get($where,$orderby,$select1,$join,"","","","",$like,$or_like,$or_where, $where_in, $where_not);
		if(!empty($all)){
			$config['total_rows'] = count($all);
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
		$config['base_url'] = base_url().'notifications/index';
		$config["cur_page"] = $page;
		if(isset($get['sel']) && !empty($get['sel'])){
		$config["per_page"] = $get['sel'];
		} else{
		$config["per_page"] = $this->config->item('per_page');
		}
		$this->pagination->initialize($config);
		$offset = $limit * ($page - 1);
		
		$orderby['notify.notifyId'] = 'DESC';
		$data["notify"] = $this->notifications_model->get($where,$orderby,$select,$join,$groupby,$limit,$offset,"",$like,$or_like,$or_where, $where_in, $where_not);
		load_default_template('notifications/view',$data,$this);
		echo $this->template->render("", true);
	}
	
	public function addnew(){
		if(checkForAdminSession(1, true))redirect('admin/login');
		$data = array();
		$data['title'] = "Add Notifications";
		load_default_template('notifications/addnew',$data,$this);
		echo $this->template->render("", true);
	}
	
	public function save(){
		$post = $this->input->post();		
		if(isset($post) && !empty($post)){
			$notifyToken = $this->notifications_model->getTokens();
			/*echo "<pre>";
			print_r($notifyToken);
			echo "</pre>";
			exit;*/
			if(!empty($notifyToken)){
				/*Image Upload process*/				
				if(isset($_FILES['imageurl']['name']) && !empty($_FILES['imageurl']['name'])){
					$files = $_FILES;						
					$config = array(
						'upload_path' => "./uploads/notify/",
						'allowed_types' => "jpg|png|jpeg|gif",
						'overwrite' => TRUE,
						'max_size' => "2048000",
						'remove_spaces' => TRUE,
					);
					$_FILES['userfile']['name']     = uniqid().'_'.time().'_'.$files['imageurl']['name'];
					$_FILES['userfile']['type']     = $files['imageurl']['type'];
					$_FILES['userfile']['tmp_name'] = $files['imageurl']['tmp_name'];
					$_FILES['userfile']['error']    = $files['imageurl']['error'];
					$_FILES['userfile']['size']     = $files['imageurl']['size']; 
   
					$this->load->library('upload', $config);

					if (!$this->upload->do_upload('userfile')){
						$error = array('error' => $this->upload->display_errors());
					} else {
						$fileData = $this->upload->data();							
						$post['notify']['imageurl'] = $fileData['file_name'];
						
						$file = $fileData['file_name'];
						$path = $fileData['full_path'];
						$config_resize['image_library'] = 'gd2';  
						$config_resize['source_image'] = $path;
						$config_resize['create_thumb'] = false;
						$config_resize['maintain_ratio'] = TRUE;
						$config_resize['width'] = 250;
						$config_resize['height'] = 250;
						$config_resize['new_image'] = './uploads/notify/thumb/'.$file;
						$this->load->library('image_lib',$config_resize);
						$this->image_lib->clear();
						$this->image_lib->initialize($config_resize);
						$this->image_lib->resize();
					}
				} else {
					$post['notify']['imageurl'] = '';
				}
				
				$subject = $post['notify']['subject'];			
				$message = $post['notify']['messages'];			
				$created_date = date('Y-m-d h:i');	
				foreach($notifyToken as $ntoken){
					if($ntoken["deviceType"] == 1){
						$tokensAndroid[] = $ntoken["deviceToken"];
					} else if($ntoken["deviceType"] == 2){
						$tokensIOS[] = $ntoken["deviceToken"];
					}
				}
				//'https://static.pexels.com/photos/4825/red-love-romantic-flowers.jpg',
				/*Android Only*/
				if(!empty($post['notify']['imageurl'])){
					$senddetailsAndroid = array(
						"title"=> $subject,
						"message"=>$message,					
						"image" => base_url().'uploads/notify/'.$post['notify']['imageurl'],
						"notifyType" => "common",
						"notifyId" => '',
						"timestamp" => $created_date
					);
				} else {
					$senddetailsAndroid = array(
						"title"=> $subject,
						"message"=>$message,
						"notifyType" => "common",
						"notifyId" => '',
						"timestamp" => $created_date
					);
				}				
				
				if(!empty($post['notify']['imageurl'])){
					$senddetailsIOS = array(
						"attachment" => base_url().'uploads/notify/'.$post['notify']['imageurl'],
						"media_type" => 'image',
						"notifyType" => "common",
						"notifyId" => ''
					);
					$messageIOS = array(
						'title' => $subject,
						'body' => $message,
						'sound' => 'default',
						"notifyType" => "common",
						"notifyId" => ''
					);
				} else {
					$senddetailsIOS = array(
						"attachment" => '',
						"media_type" => '',
						"notifyType" => "common",
						"notifyId" => ''
					);
					$messageIOS = array(
						'title' => $subject,
						'body' => $message,
						'sound' => 'default',
						"notifyType" => "common",
						"notifyId" => ''
					);
				}
				
				if(!empty($tokensAndroid)){
					$message_statusan = send_notification($tokensAndroid, $senddetailsAndroid);
				}
				
				if(!empty($tokensIOS)){
					$message_status = send_notificationIOS($tokensIOS, $senddetailsIOS, $messageIOS);
				}
				
				$result = $this->notifications_model->save_update_notify($post);
				if($result == ""){
					$this->session->set_flashdata('message', '<div class="fail_msg">Notifications messages not added & not sent to users. Kindly try again!</div>');
				} else if($result){
					if(!empty($post['notify']['id'])){
						$this->session->set_flashdata('message', '<div class="success_msg">Notifications messages has been updated successfully.</div>');
					} else {
						foreach($notifyToken as $ntoken){
							$notify_data = array( 
								array(
									'fromUserId'    => 1,
									'toUserId'      => $ntoken['userid'],
									'notifyType'	=> 'common',
									'notifyId'		=> 0,
									'notifyReason'	=> $message,
									'createdBy'		=> 1,							
									'updatedBy'		=> 1,
								)
							);
							$receiveRequest = $this->notifications_model->notificationsLog($notify_data);
						}
						$this->session->set_flashdata('message', '<div class="success_msg">Notifications messages has been added & sent to users successfully.</div>');
					}
				}
			} else {
				$this->session->set_flashdata('message', '<div class="success_msg">There are no tokens found. The notifications messages has been not sent.</div>');
			}
			redirect("/notifications");
		}
	}

	public function deletenotify($id){
		if((int)$id != ''){
            $delete = $this->notifications_model->delete($id);
            if($delete){
				$this->session->set_flashdata('message', '<div class="success_msg">Notifications messages has been Deleted Successfully!</div>');
                redirect('/notifications');
            }
        }
	}	
}
