<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Category extends CI_Controller {
    function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->model("category_model");
		$this->load->model("users_model");				
        $this->load->library('session');
        $this->load->library('email');
    }
	
	public function index($page = 1){
		if(checkForAdminSession(1, true))redirect('admin/login');
		$data = array();
		$data['title'] = "Manage Category";
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
		
		//$where['cat.parentId'] = 0;

		/**Pagination **/
		$select1 = ('count(distinct cat.categoryId) as count');
		$all = $this->category_model->get($where,$orderby,$select1,$join,"","","","",$like,$or_like,$or_where, $where_in, $where_not);
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
		$config['base_url'] = base_url().'category/index';
		$config["cur_page"] = $page;
		if(isset($get['sel']) && !empty($get['sel'])){
		$config["per_page"] = $get['sel'];
		} else{
		$config["per_page"] = $this->config->item('per_page');
		}
		
		$this->pagination->initialize($config);
		$offset = $limit * ($page - 1);
		
		$orderby['cat.categoryId'] = 'DESC';
		$data["category"] = $this->category_model->get($where,$orderby,$select,$join,$groupby,$limit,$offset,"",$like,$or_like,$or_where, $where_in, $where_not);
		load_default_template('category/view',$data,$this);
		echo $this->template->render("", true);
	}
	
	public function addedit(){
		if(checkForAdminSession(1, true))redirect('admin/login');
		$data = array();
		$where=array();
		//$where['cat.parentId'] = 0;
		$where['cat.status'] = 1;
		$orderby['cat.name'] = 'ASC';
		$data["title"] = "Add New Category";
		$data["category"] = $this->category_model->get($where,$orderby);
		load_default_template('category/addedit',$data,$this);
		echo $this->template->render("", true);
	}
	
	public function editcat($id){
		if(checkForAdminSession(1, true))redirect('admin/login');
		$data['title'] = "Edit Category";
		$where1=array();
		$orderby1=array();
		//$where1['cat.parentId'] = 0;
		$where1['cat.status'] = 1;
		$orderby1['cat.name'] = 'ASC';
		$data["category"] = $this->category_model->get($where1,$orderby1);
		$select=array();
		$where['cat.categoryId'] = $id;
		$orderby = array();
		$data["editcat"] = $this->category_model->get($where,$orderby,$select);
		load_default_template('category/addedit',$data,$this);
		echo $this->template->render("", true);
	}
	
	public function save(){
		$post = $this->input->post();		
		if(isset($post) && !empty($post)){
			if(isset($_FILES['filePathimages'])){				
				$new_name = uniqid().'_'.time().'_'.$_FILES['filePathimages']['name'];
				$_FILES['file']['name']     = $_FILES['filePathimages']['name'];
				$_FILES['file']['type']     = $_FILES['filePathimages']['type'];
				$_FILES['file']['tmp_name'] = $_FILES['filePathimages']['tmp_name'];
				$_FILES['file']['error']    = $_FILES['filePathimages']['error'];
				$_FILES['file']['size']     = $_FILES['filePathimages']['size'];
				
				$config = array(
					'upload_path' => "./uploads/category/",
					'allowed_types' => "jpg|png|jpeg|gif",
					'overwrite' => TRUE,
					'max_size' => "2048000",
					'file_name' => $new_name,
					'remove_spaces' => TRUE,
				);						
				// Load and initialize upload library
				$this->load->library('upload', $config);
				$this->upload->initialize($config);
				
				if($this->upload->do_upload('file')){
					// Uploaded file data
					$fileData = $this->upload->data();
					if(empty($fileData['file_name'])){
						$post['category']['image'] = 'no_image.png';
					} else {
						$post['category']['image'] = $fileData['file_name'];
					}
				}
				
			}
			
			$result = $this->category_model->save_update_category($post);
			if($result == ""){
				$this->session->set_flashdata('message', '<div class="fail_msg">Category not Added. Kindly try again!</div>');
			} else if($result){
				if(!empty($post['category']['id'])){
					$this->session->set_flashdata('message', '<div class="success_msg">Category has been updated successfully.</div>');
				} else {
					$this->session->set_flashdata('message', '<div class="success_msg">Category has been added successfully.</div>');
				}
			} 
			redirect("/category");
		}
	}

	public function categoryCheck(){
		$data = $this->input->post();
		// print_r($data);
		// exit;
		$result = $this->category_model->getcategoryCheck($data);
		if($result == 1){
			echo TRUE;	
		} else if($result == 0){
			echo 0;
		} 
	}
}
