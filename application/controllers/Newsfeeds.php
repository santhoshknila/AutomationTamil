<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Newsfeeds extends CI_Controller {
    function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->model("newsfeeds_model");
		$this->load->model("notifications_model");
        $this->load->model("users_model");		
        $this->load->library('session');
        $this->load->library('email');
    }
	
	public function index($page = 1){
		if(checkForAdminSession(1, true))redirect('admin/login');
		$data = array();
		$data['title'] = "Manage Newsfeeds";
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
		
		$where['nf.status'] = 1;

		/**Pagination **/
		$select1 = ('count(distinct nf.newsFeedsId) as count');
		$all = $this->newsfeeds_model->get($where,$orderby,$select1,$join,"","","","",$like,$or_like,$or_where, $where_in, $where_not);
		if(!empty($all)){
			$config['total_rows'] = $all[0]->count;
		} else {
			$config['total_rows'] = 0;
		}
		$config["uri_segment"] = "3";
		
		if(isset($get['sel']) && !empty($get['sel'])){
		$limit = $get['sel'];
		} else {
		$limit = $this->config->item('per_page');
		}
		
		$choice = $config["total_rows"] / $limit;
		$config["num_links"] = floor($choice);
		$config['base_url'] = base_url().'newsfeeds/index';
		$config["cur_page"] = $page;
		if(isset($get['sel']) && !empty($get['sel'])){
			$config["per_page"] = $get['sel'];
		} else{
			$config["per_page"] = $this->config->item('per_page');
		}
		$this->pagination->initialize($config);
		$offset = $limit * ($page - 1);
		
		$orderby['nf.newsFeedsId'] = 'DESC';
		$data["newsfeeds"] = $this->newsfeeds_model->get($where,$orderby,$select,$join,$groupby,$limit,$offset,"",$like,$or_like,$or_where, $where_in, $where_not);
		load_default_template('newsfeeds/list',$data,$this);
		echo $this->template->render("", true);
	}
	
	public function addedit(){
		if(checkForAdminSession(1, true))redirect('admin/login');
		$data = array();
		$data["title"] = "Add Newsfeeds";
		load_default_template('newsfeeds/addedit',$data,$this);
		echo $this->template->render("", true);
	}
	
	public function editnews($id){
		if(checkForAdminSession(1, true))redirect('admin/login');
		$data['title'] = "Edit Newsfeeds";
		$select=array();
		$where['nf.newsFeedsId'] = $id;
		$orderby = array();
		$data["editnews"] = $this->newsfeeds_model->get($where,$orderby,$select);
		load_default_template('newsfeeds/addedit',$data,$this);
		echo $this->template->render("", true);
	}
	
	public function save(){
		$post = $this->input->post();
		if(isset($post) && !empty($post)){
			if(isset($post['news']['fileType'])){
				foreach($post['news']['fileType'] as $key=>$data){
					if($post['news']['fileType'][$key] == 'image'.$key){
						//$post['newsimg']['fileTypeval'][] = $post['news']['fileType'][$key];
						$post['newsimg']['fileTypeval'][] = 'image';
						//upload code here
						if (isset($_FILES['filePathimages']['name'][$key]) && !empty($_FILES['filePathimages']['name'][$key])){
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
									'upload_path' => "./uploads/newsfeeds/",
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
									$post['newsimg']['imagevideo_url'][] = $fileData['file_name'];
									
									$file = $fileData['file_name'];
									$path = $fileData['full_path'];
									$config_resize['image_library'] = 'gd2';  
									$config_resize['source_image'] = $path;
									$config_resize['create_thumb'] = false;
									$config_resize['maintain_ratio'] = TRUE;
									$config_resize['width'] = 250;
									$config_resize['height'] = 250;
									$config_resize['new_image'] = './uploads/newsfeeds/thumb/'.$file;
									$this->load->library('image_lib',$config_resize);
									$this->image_lib->clear();
									$this->image_lib->initialize($config_resize);
									$this->image_lib->resize();
								}
							}
						}
					} else if($post['news']['fileType'][$key] == 'url'.$key){
						//$post['newsimg']['fileTypeval'][] = $post['news']['fileType'][$key];
						$post['newsimg']['fileTypeval'][] = 'url';
						$post['newsimg']['imagevideo_url'][] = $post['news']["filePathURl"][$key];
					}
				}
			}
			unset($post['news']['fileType']);
			unset($post['news']['filePathimages']);
			unset($post['news']['filePathURl']);
			unset($post['submit']);
			$result = $this->newsfeeds_model->save_update_newsfeeds($post);
			//print_r($result);
			//exit;
			if($result == ""){
				$this->session->set_flashdata('message', '<div class="fail_msg">News not Added. Kindly try again!</div>');
			} else if($result){
				if(!empty($post['news']['newsFeedsId'])){
					$this->session->set_flashdata('message', '<div class="success_msg">News has been updated successfully.</div>');
				} else {
					$notifyToken = $this->notifications_model->getTokens();
					if(!empty($notifyToken)){
						$subject = "Newfeeds Notifications on Tamil Ethos";			
						$message = 'Admin posted "'.$post['news']['title'].'" in the Newsfeed.';
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
							"notifyType" => "news",
							"notifyId" => $result,
							"timestamp" => $created_date
						);
						$senddetailsIOS = array(
							"attachment" => '',
							"media_type" => '',
							"notifyType" => "news",
							"notifyId" => $result
						);
						$messageIOS = array(
							'title' => $subject,
							'body' => $message,
							'sound' => 'default',
							"notifyType" => "news",
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
									'notifyType'	=> 'news',
									'notifyId'		=> $result,
									'notifyReason'	=> 'Admin posted <b>'.$post['news']['title'].'<b> in the <b>Newsfeed<b>.',
									'createdBy'		=> 1,							
									'updatedBy'		=> 1,
								)
							);
							$receiveRequest = $this->notifications_model->notificationsLog($notify_data);
						}						
					}
					$this->session->set_flashdata('message', '<div class="success_msg">News has been added successfully.</div>');
				}
			} 
			redirect("/newsfeeds");
		}
	}
	
	/*Delete the news*/
	public function deleteNews(){
		if(empty($_POST['news_id'])) return false;
		$data['news']['status'] = 0;
		$this->db->where("newsFeedsId", $_POST['news_id']);
		$this->db->update('newsfeeds', $data['news']);
		$affected_rows = $this->db->affected_rows();
		if($affected_rows == 1){
			return $affected_rows;
		} else {
			return false;
		}
	}
	
	/*Status update the news*/
	function updatestatusNews(){
		if(empty($_POST['news_id'])) return false;
		$data['news']['isActive'] = (int) $_POST['relval'];
		$this->db->where("newsFeedsId", $_POST['news_id']);
		$this->db->update('newsfeeds', $data['news']);
		$affected_rows = $this->db->affected_rows();
		if($affected_rows == 1){
			return $affected_rows;
		} else {
			return false;
		}
	}
	
	/*Block the news*/
	function blockstatusNews(){
		if(empty($_POST['news_id'])) return false;
		$data['news']['reportAbuse'] = (int) $_POST['relval'];
		$this->db->where("newsFeedsId", $_POST['news_id']);
		$this->db->update('newsfeeds', $data['news']);
		$affected_rows = $this->db->affected_rows();
		if($affected_rows == 1){
			return $affected_rows;
		} else {
			return false;
		}
	}
	
	function reportAbuseblocksts(){
		if(empty($_POST['news_id'])) return false;
		$data['news']['reportAbuse'] = 0;
		$this->db->where("newsFeedsId", $_POST['news_id']);
		$this->db->update('newsfeeds', $data['news']);
		$affected_rows = $this->db->affected_rows();
		if($affected_rows == 1){
			$data['newsreset']['status'] = 0;
			$this->db->where("newsFeedsId", $_POST['news_id']);
			$this->db->update('newsfeedreport', $data['newsreset']);
			return $affected_rows;
		} else {
			return false;
		}
	}
	
	function removeUploadfiles(){
		if(empty($_POST['uploadRowID'])) return false;
		$this->db->where('newsfeedImageId', $_POST['uploadRowID']);
		$this->db->delete('newsfeedimages');
		return true;
	}
}