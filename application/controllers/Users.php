<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends CI_Controller {
    function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->model("users_model");		
        $this->load->library('session');
        $this->load->library('email');
    }
    public function index($page = 1){
    	 
		if(checkForAdminSession(1, true))redirect('admin/login');
		$data = array();
		$data['title'] = "Manage Users";
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
		
		$where['us.userrole'] = 'user';
		
		/**Pagination **/
		$select1 = ('count(distinct us.userid) as count');
		$all = $this->users_model->get($where,$orderby,$select1,$join,"","","","",$like,$or_like,$or_where, $where_in, $where_not);
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
		$config['base_url'] = base_url().'users/index';
		$config["cur_page"] = $page;
		if(isset($get['sel']) && !empty($get['sel'])){
			$config["per_page"] = $get['sel'];
		} else{
			$config["per_page"] = $this->config->item('per_page');
		}
		
		$this->pagination->initialize($config);
		$offset = $limit * ($page - 1);	
		$orderby['us.userid'] = 'DESC';
		$data["users"] = $this->users_model->get($where,$orderby,$select,$join,$groupby,$limit,$offset,"",$like,$or_like,$or_where, $where_in, $where_not);
		load_default_template('users/list',$data,$this);
		echo $this->template->render("", true);
    }

    /*User Status update */
	function updatestatusUser(){
		if(empty($_POST['user_id'])) return false;

		$data['users']['status'] = (int) $_POST['relval'];
		$this->db->where("userid", $_POST['user_id']);
		$this->db->update('users', $data['users']);

		$data['users_delete']['token'] = "";
		$this->db->where("userid", $_POST['user_id']);
		$this->db->update('users_authentication', $data['users_delete']);

		$affected_rows = $this->db->affected_rows();
		if($affected_rows == 1){
			return $affected_rows;
		} else {
			return false;
		}
	}
	
	public function resetpassword(){
		$token = $this->input->get('token', TRUE);
		$post = $this->input->post();
		if(isset($post) && !empty($post)){
			if (empty($this->input->post('password'))){
				$message = "Password is required";
			}
			if (empty($this->input->post('conpassword'))){
				$message = "Confirm password is required";
			} else if ($this->input->post('password') != $this->input->post('conpassword')) {
				$message = "Your passwords do not match. Please type carefully.";
			}
			if(empty($message)){
				$result = $this->users_model->save_update_password($post);			
				if($result){
					$this->session->set_flashdata('message', '<div class="success_msg">Password Updated successfully.</div>');
				} else {
					$this->session->set_flashdata('message', '<div class="success_msg">Password Not Updated. Try again.</div>');
				}
			} else {
				$this->session->set_flashdata('message', '<div class="error_msg">'.$message.'</div>');
			}
			redirect('users/resetpassword/?token='.$this->input->post('token'));
		} else {
		    $data = array();
		    $data["title"] = "Reset Password";
			$useremail = dec_enc('decrypt', $token);
			$data['token'] = $token;
			$data['emailid'] = $useremail;
    		load_resetpassword_template('',$data,$this);
    		$this->template->render();
		}
	}       
}