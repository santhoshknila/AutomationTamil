<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Locations extends CI_Controller {
    function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->model("locations_model");
		$this->load->model("users_model");				
        $this->load->library('session');
        $this->load->library('email');
    }
	
	public function index($page = 1){
		if(checkForAdminSession(1, true))redirect('admin/login');
		$data = array();
		$data['title'] = "Manage Locations";
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
		$select1 = ('count(distinct loc.locationsId) as count');
		$all = $this->locations_model->get($where,$orderby,$select1,$join,"","","","",$like,$or_like,$or_where, $where_in, $where_not);
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
		$config['base_url'] = base_url().'locations/index';
		$config["cur_page"] = $page;
		if(isset($get['sel']) && !empty($get['sel'])){
		$config["per_page"] = $get['sel'];
		} else{
		$config["per_page"] = $this->config->item('per_page');
		}
		$this->pagination->initialize($config);
		$offset = $limit * ($page - 1);
		
		$orderby['loc.locationsId'] = 'DESC';
		$data["location"] = $this->locations_model->get($where,$orderby,$select,$join,$groupby,$limit,$offset,"",$like,$or_like,$or_where, $where_in, $where_not);
		load_default_template('locations/view',$data,$this);
		echo $this->template->render("", true);
	}
	
	public function addedit(){
		if(checkForAdminSession(1, true))redirect('admin/login');
		$data = array();
		$where=array();
		$where['loc.parentId'] = 0;
		$where['loc.status'] = 1;
		$data["title"] = "Add New Locations";
		$orderby['loc.locationsId'] = 'ASC';
		$data["location"] = $this->locations_model->get($where,$orderby);
		load_default_template('locations/addedit',$data,$this);
		echo $this->template->render("", true);
	}
	
	public function editcat($id){
		if(checkForAdminSession(1, true))redirect('admin/login');
		$data['title'] = "Edit Locations";
		$where1['loc.parentId'] = 0;
		$where1['loc.status'] = 1;
		$orderby1['loc.locationsId'] = 'ASC';
		$data["location"] = $this->locations_model->get($where1,$orderby1);
		
		$where['loc.locationsId'] = $id;
		$data["editloc"] = $this->locations_model->get($where);
		load_default_template('locations/addedit',$data,$this);
		echo $this->template->render("", true);
	}
	
	public function save(){
		$post = $this->input->post();		
		if(isset($post) && !empty($post)){
			$result = $this->locations_model->save_update_location($post);
			if($result == ""){
				$this->session->set_flashdata('message', '<div class="fail_msg">Locations not Added. Kindly try again!</div>');
			} else if($result){
				if(!empty($post['location']['locationsId'])){
					$this->session->set_flashdata('message', '<div class="success_msg">Locations has been updated successfully.</div>');
				} else {
					$this->session->set_flashdata('message', '<div class="success_msg">Locations has been added successfully.</div>');
				}
			} 
			redirect("/locations");
		}
	}
	public function locationCheck(){
		$data = $this->input->post();
		$result = $this->locations_model->getlocationCheck($data);
		if($result == 1){
			echo TRUE;	
		} else if($result == 0){
			echo 0;
		} 
	}
	
}
