<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Settings extends CI_Controller {
    function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->model("settings_model");	
		$this->load->model("general_model");			
        $this->load->library('session');
        $this->load->library('email');
    }
	
	/*District functionality here*/	
    public function district($page = 1){
		if(checkForAdminSession(1, true))redirect('admin/login');
		$data = array();
		$data['title'] = "Manage City";
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
		$select1 = ('count(distinct dt.districtId) as count');
		$all = $this->settings_model->getdistrict($where,$orderby,$select1,$join,"","","","",$like,$or_like,$or_where, $where_in, $where_not);
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
		$config['base_url'] = base_url().'settings/district';
		$config["cur_page"] = $page;
		if(isset($get['sel']) && !empty($get['sel'])){
			$config["per_page"] = $get['sel'];
		} else{
			$config["per_page"] = $this->config->item('per_page');
		}
		
		$this->pagination->initialize($config);
		$offset = $limit * ($page - 1);	
		$orderby['dt.districtId'] = 'DESC';
		$data['countries'] = $this->general_model->getCountries(0);
		$data["district"] = $this->settings_model->getdistrict($where,$orderby,$select,$join,$groupby,"","","",$like,$or_like,$or_where, $where_in, $where_not);
		load_default_template('settings/district',$data,$this);
		echo $this->template->render("", true);
    }
	
	public function getProvinceval($id){
		$result = $this->db->where("countryId",$id)->get("province")->result();
		echo json_encode($result);
	}
		
    public function districtadd(){
		$post = $this->input->post();
		if(isset($post) && !empty($post)){			
			$result = $this->settings_model->save_insert_district($post);			
			if($result == 0){
				$this->session->set_flashdata('message', '<div class="fail_msg">District not Added. Kindly try again!</div>');
				redirect("/settings/district");
			} else if($result == 1){
				if(!empty($post['districtID'])){
					$this->session->set_flashdata('message', '<div class="success_msg">District has been updated successfully.</div>');
					redirect("/settings/district");
				} else {
					$this->session->set_flashdata('message', '<div class="success_msg">District has been added successfully.</div>');
					redirect("/settings/district");
				}
			} else {
				redirect("/settings/district");
			}
		}
	}
	
	public function districtCheck(){
		$data = $this->input->post();
		$result = $this->settings_model->getdistrictCheck($data);
		if($result == 1){
			echo TRUE;	
		} else if($result == 0){
			echo 0;
		} 
	}
	
	/*City functionality here*/
	public function city($page = 1){
		if(checkForAdminSession(1, true))redirect('admin/login');
		$data = array();
		$data['title'] = "Manage Suburb";
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
		$select1 = ('count(distinct ct.cityId) as count');
		$all = $this->settings_model->getCity($where,$orderby,$select1,$join,"","","","",$like,$or_like,$or_where, $where_in, $where_not);
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
		$config['base_url'] = base_url().'settings/city';
		$config["cur_page"] = $page;
		if(isset($get['sel']) && !empty($get['sel'])){
			$config["per_page"] = $get['sel'];
		} else{
			$config["per_page"] = $this->config->item('per_page');
		}
		
		$this->pagination->initialize($config);
		$offset = $limit * ($page - 1);	
		$orderby['ct.cityId'] = 'DESC';
		$data['province'] = $this->general_model->getProvince(0);
		$data["city"] = $this->settings_model->getCity($where,$orderby,$select,$join,$groupby,"","","",$like,$or_like,$or_where, $where_in, $where_not);
		load_default_template('settings/city',$data,$this);
		echo $this->template->render("", true);
    }
	
	public function getDistrictval($id){
		$result = $this->db->where("provinceId",$id)->order_by("districtName", "ASC")->get("district")->result();
		echo json_encode($result);
	}
		
    public function cityadd(){
		$post = $this->input->post();
		if(isset($post) && !empty($post)){			
			$result = $this->settings_model->save_insert_city($post);			
			if($result == 0){
				$this->session->set_flashdata('message', '<div class="fail_msg">City not Added. Kindly try again!</div>');
				redirect("/settings/city");
			} else if($result == 1){
				if(!empty($post['cityID'])){
					$this->session->set_flashdata('message', '<div class="success_msg">City has been updated successfully.</div>');
					redirect("/settings/city");
				} else {
					$this->session->set_flashdata('message', '<div class="success_msg">City has been added successfully.</div>');
					redirect("/settings/city");
				}
			} else {
				redirect("/settings/city");
			}
		}
	}
	
	public function cityCheck(){
		$data = $this->input->post();
		$result = $this->settings_model->getcityCheck($data);
		if($result == 1){
			echo TRUE;	
		} else if($result == 0){
			echo 0;
		} 
	}
	
	/* Religion functionality here */
	public function religion($page = 1){
		if(checkForAdminSession(1, true))redirect('admin/login');
		$data = array();
		$data['title'] = "Manage Religion";
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
		$select1 = ('count(distinct re.religionId) as count');
		$all = $this->settings_model->getReligion($where,$orderby,$select1,$join,"","","","",$like,$or_like,$or_where, $where_in, $where_not);
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
		$config['base_url'] = base_url().'settings/religion';
		$config["cur_page"] = $page;
		if(isset($get['sel']) && !empty($get['sel'])){
			$config["per_page"] = $get['sel'];
		} else{
			$config["per_page"] = $this->config->item('per_page');
		}
		
		$this->pagination->initialize($config);
		$offset = $limit * ($page - 1);	
		$orderby['re.religionId'] = 'DESC';
		$data["religion"] = $this->settings_model->getReligion($where,$orderby,$select,$join,$groupby,"","","",$like,$or_like,$or_where, $where_in, $where_not);
		load_default_template('settings/religion',$data,$this);
		echo $this->template->render("", true);
    }

	public function religionAdd(){
		$post = $this->input->post();
		if(isset($post) && !empty($post)){			
			$result = $this->settings_model->save_insert_religion($post);			
			if($result == 0){
				$this->session->set_flashdata('message', '<div class="fail_msg">Religion not Added. Kindly try again!</div>');
				redirect("/settings/religion");
			} else if($result){
				if(!empty($post['religionId'])){
					$this->session->set_flashdata('message', '<div class="success_msg">Religion has been updated successfully.</div>');
					redirect("/settings/religion");
				} else {
					$this->session->set_flashdata('message', '<div class="success_msg">Religion has been added successfully.</div>');
					redirect("/settings/religion");
				}
			} else {
				redirect("/settings/religion");
			}
		}
	}
	
	public function religionCheck(){
		$data = $this->input->post();
		$result = $this->settings_model->getreligionCheck($data);
		if($result == 1){
			echo TRUE;	
		} else if($result == 0){
			echo 0;
		} 
	}
		
	
	/* Mothertongue functionality here */
	public function mothertongue($page = 1){
		if(checkForAdminSession(1, true))redirect('admin/login');
		$data = array();
		$data['title'] = "Manage Mothertongue";
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
		$select1 = ('count(distinct mt.mothertongueId) as count');
		$all = $this->settings_model->getMothertongue($where,$orderby,$select1,$join,"","","","",$like,$or_like,$or_where, $where_in, $where_not);
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
		$config['base_url'] = base_url().'settings/mothertongue';
		$config["cur_page"] = $page;
		if(isset($get['sel']) && !empty($get['sel'])){
			$config["per_page"] = $get['sel'];
		} else{
			$config["per_page"] = $this->config->item('per_page');
		}
		
		$this->pagination->initialize($config);
		$offset = $limit * ($page - 1);	
		$orderby['mt.mothertongueId'] = 'DESC';
		$data["mothertongue"] = $this->settings_model->getMothertongue($where,$orderby,$select,$join,$groupby,"","","",$like,$or_like,$or_where, $where_in, $where_not);
		load_default_template('settings/mothertongue',$data,$this);
		echo $this->template->render("", true);
    }
	
	public function mothertongueAdd(){
		$post = $this->input->post();
		if(isset($post) && !empty($post)){			
			$result = $this->settings_model->save_insert_mothertongue($post);			
			if($result == 0){
				$this->session->set_flashdata('message', '<div class="fail_msg">Mothertongue not Added. Kindly try again!</div>');
				redirect("/settings/mothertongue");
			} else if($result){
				if(!empty($post['mothertongueId'])){
					$this->session->set_flashdata('message', '<div class="success_msg">Mothertongue has been updated successfully.</div>');
					redirect("/settings/mothertongue");
				} else {
					$this->session->set_flashdata('message', '<div class="success_msg">Mothertongue has been added successfully.</div>');
					redirect("/settings/mothertongue");
				}
			} else {
				redirect("/settings/mothertongue");
			}
		}
	}
	
	public function mothertongueCheck(){
		$data = $this->input->post();
		$result = $this->settings_model->getmothertongueCheck($data);
		if($result == 1){
			echo TRUE;	
		} else if($result == 0){
			echo 0;
		} 
	}
	
	/* Gender functionality here */
	public function gender($page = 1){
		if(checkForAdminSession(1, true))redirect('admin/login');
		$data = array();
		$data['title'] = "Manage Gender";
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
		$select1 = ('count(distinct ge.genderId) as count');
		$all = $this->settings_model->getGender($where,$orderby,$select1,$join,"","","","",$like,$or_like,$or_where, $where_in, $where_not);
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
		$config['base_url'] = base_url().'settings/gender';
		$config["cur_page"] = $page;
		if(isset($get['sel']) && !empty($get['sel'])){
			$config["per_page"] = $get['sel'];
		} else{
			$config["per_page"] = $this->config->item('per_page');
		}
		
		$this->pagination->initialize($config);
		$offset = $limit * ($page - 1);	
		$orderby['ge.genderId'] = 'DESC';
		$data["gender"] = $this->settings_model->getGender($where,$orderby,$select,$join,$groupby,"","","",$like,$or_like,$or_where, $where_in, $where_not);
		load_default_template('settings/gender',$data,$this);
		echo $this->template->render("", true);
    }
	
	public function genderAdd(){
		$post = $this->input->post();
		if(isset($post) && !empty($post)){			
			$result = $this->settings_model->save_insert_gender($post);			
			if($result == 0){
				$this->session->set_flashdata('message', '<div class="fail_msg">Gender not Added. Kindly try again!</div>');
				redirect("/settings/gender");
			} else if($result){
				if(!empty($post['genderId'])){
					$this->session->set_flashdata('message', '<div class="success_msg">Gender has been updated successfully.</div>');
					redirect("/settings/gender");
				} else {
					$this->session->set_flashdata('message', '<div class="success_msg">Gender has been added successfully.</div>');
					redirect("/settings/gender");
				}
			} else {
				redirect("/settings/gender");
			}
		}
	}
	
	public function genderCheck(){
		$data = $this->input->post();
		$result = $this->settings_model->getgenderCheck($data);
		if($result == 1){
			echo TRUE;	
		} else if($result == 0){
			echo 0;
		} 
	}
	
	/* Jobtitle functionality here */
	public function jobtitle($page = 1){
		if(checkForAdminSession(1, true))redirect('admin/login');
		$data = array();
		$data['title'] = "Manage Job Sector";
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
		$select1 = ('count(distinct jt.jobtitleId) as count');
		$all = $this->settings_model->getJobtitle($where,$orderby,$select1,$join,"","","","",$like,$or_like,$or_where, $where_in, $where_not);
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
		$config['base_url'] = base_url().'settings/jobtitle';
		$config["cur_page"] = $page;
		if(isset($get['sel']) && !empty($get['sel'])){
			$config["per_page"] = $get['sel'];
		} else{
			$config["per_page"] = $this->config->item('per_page');
		}
		
		$this->pagination->initialize($config);
		$offset = $limit * ($page - 1);	
		$orderby['jt.jobtitleId'] = 'DESC';
		$data["jobtitle"] = $this->settings_model->getJobtitle($where,$orderby,$select,$join,$groupby,"","","",$like,$or_like,$or_where, $where_in, $where_not);
		load_default_template('settings/jobtitle',$data,$this);
		echo $this->template->render("", true);
    }
	
	public function jobtitleAdd(){
		$post = $this->input->post();
		if(isset($post) && !empty($post)){			
			$result = $this->settings_model->save_insert_jobTitle($post);			
			if($result == 0){
				$this->session->set_flashdata('message', '<div class="fail_msg">Jobsector not Added. Kindly try again!</div>');
				redirect("/settings/jobtitle");
			} else if($result){
				if(!empty($post['mothertongueId'])){
					$this->session->set_flashdata('message', '<div class="success_msg">Jobsector has been updated successfully.</div>');
					redirect("/settings/jobtitle");
				} else {
					$this->session->set_flashdata('message', '<div class="success_msg">Jobsector has been added successfully.</div>');
					redirect("/settings/jobtitle");
				}
			} else {
				redirect("/settings/jobtitle");
			}
		}
	}
	
	public function jobtitleCheck(){
		$data = $this->input->post();
		$result = $this->settings_model->getjobtitleCheck($data);
		if($result == 1){
			echo TRUE;	
		} else if($result == 0){
			echo 0;
		} 
	}
	
	/* Privacy functionality here */
	public function privacy($page = 1){
		if(checkForAdminSession(1, true))redirect('admin/login');
		$data = array();
		$data['title'] = "Manage Privacy";
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
		
		$where['py.status'] = 1;
		
		/**Pagination **/
		$select1 = ('count(distinct py.privacyId) as count');
		$all = $this->settings_model->getPrivacy($where,$orderby,$select1,$join,"","","","",$like,$or_like,$or_where, $where_in, $where_not);
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
		$config['base_url'] = base_url().'settings/privacy';
		$config["cur_page"] = $page;
		if(isset($get['sel']) && !empty($get['sel'])){
			$config["per_page"] = $get['sel'];
		} else{
			$config["per_page"] = $this->config->item('per_page');
		}
		
		$this->pagination->initialize($config);
		$offset = $limit * ($page - 1);	
		$orderby['py.privacyId'] = 'DESC';
		$data["privacy"] = $this->settings_model->getPrivacy($where,$orderby,$select,$join,$groupby,"","","",$like,$or_like,$or_where, $where_in, $where_not);
		load_default_template('settings/privacy',$data,$this);
		echo $this->template->render("", true);
    }
	
	public function privacyAdd(){
		$post = $this->input->post();
		if(isset($post) && !empty($post)){			
			$result = $this->settings_model->save_insert_privacy($post);			
			if($result == 0){
				$this->session->set_flashdata('message', '<div class="fail_msg">Privacy not Added. Kindly try again!</div>');
				redirect("/settings/privacy");
			} else if($result == 1){
				if(!empty($post['mothertongueId'])){
					$this->session->set_flashdata('message', '<div class="success_msg">Privacy has been updated successfully.</div>');
					redirect("/settings/privacy");
				} else {
					$this->session->set_flashdata('message', '<div class="success_msg">Privacy has been added successfully.</div>');
					redirect("/settings/privacy");
				}
			} else {
				redirect("/settings/privacy");
			}
		}
	}
	
	public function privacyCheck(){
		$data = $this->input->post();
		$result = $this->settings_model->getprivacyCheck($data);
		if($result == 1){
			echo TRUE;	
		} else if($result == 0){
			echo 0;
		} 
	}
	
	public function editdistrict(){
		$districtId = $this->input->post('districtId');
		$result = $this->settings_model->did_get_dist_data($districtId);
		echo json_encode($result);
	} 
	public function editreli(){
		$religionId = $this->input->post('religionId');
		$result = $this->settings_model->did_get_religion_data($religionId);
		echo json_encode($result);
	} 
	public function editmothert(){
		$mothertongueId = $this->input->post('mothertongueId');
		$result = $this->settings_model->did_get_moth_data($mothertongueId);
		echo json_encode($result);
	}
	
	public function editgender(){
		$genderId = $this->input->post('genderId');
		$result = $this->settings_model->did_get_gender_data($genderId);
		echo json_encode($result);
	} 
    
    public function editcity(){
		$cityId = $this->input->post('cityId');
		$result = $this->settings_model->did_get_city_data($cityId);
		echo json_encode($result);
	}
	public function editjobtitle(){
		$jobtitleId = $this->input->post('jobtitleId');
		$result = $this->settings_model->did_get_job_data($jobtitleId);
		echo json_encode($result);
	}
	
	public function sitesetting(){
		if(checkForAdminSession(1, true))redirect('admin/login');
		$data = array();
		$data['title'] = "Manage Site Settings";
		$data['settings'] = $this->settings_model->getSettings(1);
		load_default_template('settings/sitesetting',$data, $this);
		echo $this->template->render("", true);
    }
	
	public function sitesettingSave(){
		$post = $this->input->post();
		if(isset($post) && !empty($post)){			
			$result = $this->settings_model->updateSitesetting($post);			
			if($result == 0){
				$this->session->set_flashdata('message', '<div class="fail_msg">Settings not Added. Kindly try again!</div>');
				redirect("/settings/sitesetting");
			} else if($result == 1){
				if(!empty($post['siteId'])){
					$this->session->set_flashdata('message', '<div class="success_msg">Settings has been updated successfully.</div>');
					redirect("/settings/sitesetting");
				} else {
					$this->session->set_flashdata('message', '<div class="success_msg">Settings has been added successfully.</div>');
					redirect("/settings/sitesetting");
				}
			} else {
				redirect("/settings/sitesetting");
			}
		}
	}
}
