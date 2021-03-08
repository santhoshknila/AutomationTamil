<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sitepages extends CI_Controller {
    function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->model("sitepages_model");		
        $this->load->library('session');
        $this->load->library('email');
    }
	
	public function index($page = 1){
		if(checkForAdminSession(1, true))redirect('admin/login');
		$data = array();
		$data['title'] = "Manage Sitepages";
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
		$select1 = ('count(distinct sp.pageId) as count');
		$all = $this->sitepages_model->get($where,$orderby,$select1,$join,"","","","",$like,$or_like,$or_where, $where_in, $where_not);
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
		$config['base_url'] = base_url().'sitepages';
		$config["cur_page"] = $page;
		if(isset($get['sel']) && !empty($get['sel'])){
			$config["per_page"] = $get['sel'];
		} else{
			$config["per_page"] = $this->config->item('per_page');
		}
		$this->pagination->initialize($config);
		$offset = $limit * ($page - 1);
		
		$orderby['sp.pageId'] = 'DESC';
		$data["sitepages"] = $this->sitepages_model->get($where,$orderby,$select,$join,$groupby,$limit,$offset,"",$like,$or_like,$or_where, $where_in, $where_not);
		load_default_template('sitepages/list',$data,$this);
		echo $this->template->render("", true);
	}
	
	public function addedit(){
		if(checkForAdminSession(1, true))redirect('admin/login');
		$data = array();
		$data["title"] = "Add New Sitepages";
		load_default_template('sitepages/addedit',$data,$this);
		echo $this->template->render("", true);
	}
	
	public function editpage($id){
		if(checkForAdminSession(1, true))redirect('admin/login');
		$data['title'] = "Edit Sitepages";
		$select=array();
		$where['sp.pageId'] = $id;
		$orderby = array();
		$data["editpage"] = $this->sitepages_model->get($where,$orderby,$select);
		load_default_template('sitepages/addedit',$data,$this);
		echo $this->template->render("", true);
	}
	
	public function save(){
		$post = $this->input->post();		
		if(isset($post) && !empty($post)){
			$result = $this->sitepages_model->save_update_sitepages($post);
			if($result == ""){
				$this->session->set_flashdata('message', '<div class="fail_msg">Pages not Added. Kindly try again!</div>');
			} else if($result){
				if(!empty($post['page']['pageId'])){
					$this->session->set_flashdata('message', '<div class="success_msg">Pages has been updated successfully.</div>');
				} else {
					$this->session->set_flashdata('message', '<div class="success_msg">Pages has been added successfully.</div>');
				}
			} 
			redirect("/sitepages");
		}
	}	
}