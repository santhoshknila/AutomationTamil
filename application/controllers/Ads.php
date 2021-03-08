<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ads extends CI_Controller {
	private $error = array();
	function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->model("ads_model");
        $this->load->model("auth_model");		
        $this->load->library('session');
    }
	
	public function index($page = 1){
		if(checkForAdminSession(1, true))redirect('admin/login');
		$data = array();
		$data['title'] = "Manage Ads";
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
		$select1 = ('count(distinct ad.adsId) as count');
		$all = $this->ads_model->get($where,$orderby,$select1,$join,"","","","",$like,$or_like,$or_where, $where_in, $where_not);
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
		$config['base_url'] = base_url().'ads/index';
		$config["cur_page"] = $page;
		if(isset($get['sel']) && !empty($get['sel'])){
		$config["per_page"] = $get['sel'];
		} else{
		$config["per_page"] = $this->config->item('per_page');
		}
		$this->pagination->initialize($config);
		$offset = $limit * ($page - 1);
		
		$orderby['ad.adsId'] = 'DESC';
		$data["ads"] = $this->ads_model->get($where,$orderby,$select,$join,$groupby,$limit,$offset,"",$like,$or_like,$or_where, $where_in, $where_not);
		load_default_template('ads/view',$data,$this);
		echo $this->template->render("", true);
	}
	
	public function addedit(){
		if(checkForAdminSession(1, true))redirect('admin/login');
		$data = array();
		$data['title'] = "Add Ads";
		$where=array();
		load_default_template('ads/addedit',$data,$this);
		echo $this->template->render("", true);
	}
	
	public function editads($id){
		if(checkForAdminSession(1, true))redirect('admin/login');
		$data['title'] = "Edit Ads";
		$select=array();
		$where['ad.adsId'] = $id;
		$orderby = array();
		$data["editads"] = $this->ads_model->get($where,$orderby,$select);
		load_default_template('ads/addedit',$data,$this);
		echo $this->template->render("", true);
	}
	
	public function save(){
		$post = $this->input->post();		
		if(isset($post) && !empty($post)){						
			$result = $this->ads_model->save_update_ads($post);
			if($result == ""){
				$this->session->set_flashdata('message', '<div class="fail_msg">Ads not Added. Kindly try again!</div>');
			} else if($result){
				if(!empty($post['package']['packageId'])){
					$this->session->set_flashdata('message', '<div class="success_msg">Ads has been updated successfully.</div>');
				} else {
					$this->session->set_flashdata('message', '<div class="success_msg">Ads has been added successfully.</div>');
				}
			} 
			redirect("/ads");
		}
	}
	public function adsCheck(){
		$data = $this->input->post();		
		$result = $this->ads_model->getadsCheck($data);
		if($result == 1){
			echo TRUE;	
		} else if($result == 0){
			echo 0;
		} 
	}
}
