<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Packages extends CI_Controller {
	private $error = array();
	function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->model("packages_model");
        $this->load->model("auth_model");		
        $this->load->library('session');
    }
	
	public function index($page = 1){
		if(checkForAdminSession(1, true))redirect('admin/login');
		$data = array();
		$data['title'] = "Manage Packages";
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
		$select1 = ('count(distinct pack.packageId) as count');
		$all = $this->packages_model->get($where,$orderby,$select1,$join,"","","","",$like,$or_like,$or_where, $where_in, $where_not);
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
		$config['base_url'] = base_url().'packages/index';
		$config["cur_page"] = $page;
		if(isset($get['sel']) && !empty($get['sel'])){
		$config["per_page"] = $get['sel'];
		} else{
		$config["per_page"] = $this->config->item('per_page');
		}
		$this->pagination->initialize($config);
		$offset = $limit * ($page - 1);
		
		$orderby['pack.packageId'] = 'DESC';
		$data["packages"] = $this->packages_model->get($where,$orderby,$select,$join,$groupby,$limit,$offset,"",$like,$or_like,$or_where, $where_in, $where_not);
		load_default_template('packages/view',$data,$this);
		echo $this->template->render("", true);
	}
	
	public function addedit(){
		if(checkForAdminSession(1, true))redirect('admin/login');
		$data = array();
		$data['title'] = "Add Packages";
		$where=array();
		load_default_template('packages/addedit',$data,$this);
		echo $this->template->render("", true);
	}
	
	public function editpack($id){
		if(checkForAdminSession(1, true))redirect('admin/login');
		$data['title'] = "Edit Packages";
		$select=array();
		$where['pack.packageId'] = $id;
		$orderby = array();
		$data["editpack"] = $this->packages_model->get($where,$orderby,$select);
		load_default_template('packages/addedit',$data,$this);
		echo $this->template->render("", true);
	}
	
	public function save(){
		$post = $this->input->post();		
		if(isset($post) && !empty($post)){						
			$result = $this->packages_model->save_update_packages($post);
			if($result == ""){
				$this->session->set_flashdata('message', '<div class="fail_msg">Packages not Added. Kindly try again!</div>');
			} else if($result){
				if(!empty($post['package']['packageId'])){
					$this->session->set_flashdata('message', '<div class="success_msg">Packages has been updated successfully.</div>');
				} else {
					$this->session->set_flashdata('message', '<div class="success_msg">Packages has been added successfully.</div>');
				}
			} 
			redirect("/packages");
		}
	}
	
	public function productCheck(){
		$data = $this->input->post();
		// print_r($data);
		// exit;
		$result = $this->packages_model->getpackageCheck($data);
		if($result == 1){
			echo TRUE;	
		} else if($result == 0){
			echo 0;
		} 
	}
	
}
