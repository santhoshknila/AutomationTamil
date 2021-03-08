<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Groups extends CI_Controller {
    function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->model("Groups_model");
		$this->load->model("notifications_model");
        $this->load->model("users_model");		
        $this->load->library('session');
        $this->load->library('email');
    }
	
	public function index($page = 1){
		if(checkForAdminSession(1, true))redirect('admin/login');
		$data = array();
		$data['title'] = "Manage Groups";
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
		
		//$where['gp.status'] = 1;

		/**Pagination **/
		$select1 = ('count(distinct gp.groupId) as count');
		$all = $this->Groups_model->get($where,$orderby,$select1,$join,"","","","",$like,$or_like,$or_where, $where_in, $where_not);
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
		$config['base_url'] = base_url().'groups/index/';
		$config["cur_page"] = $page;
		if(isset($get['sel']) && !empty($get['sel'])){
			$config["per_page"] = $get['sel'];
		} else{
			$config["per_page"] = $this->config->item('per_page');
		}
		$this->pagination->initialize($config);
		$offset = $limit * ($page - 1);
		
		$orderby['gp.groupId'] = 'DESC';
		$data["groups"] = $this->Groups_model->get($where,$orderby,$select,$join,$groupby,$limit,$offset,"",$like,$or_like,$or_where, $where_in, $where_not);
		load_default_template('groups/list',$data,$this);
		echo $this->template->render("", true);
	}
	
	/*Status update the news*/
	function updatestatusGroup(){
		if(empty($_POST['groupId'])) return false;
		$data['gp']['status'] = (int)$_POST['relval'];
		$this->db->where("groupId", $_POST['groupId']);
		$this->db->update('groups', $data['gp']);
		$affected_rows = $this->db->affected_rows();
		if($affected_rows == 1){
			return $affected_rows;
		} else {
			return false;
		}
	}
	
	function reportAbuseblocksts(){
		if(empty($_POST['gid'])) return false;
		$data['gp']['reportAbuse'] = 0;
		$data['gp']['status'] = 2;
		$this->db->where("groupId", $_POST['gid']);
		$this->db->update('groups', $data['gp']);
		$affected_rows = $this->db->affected_rows();
		if($affected_rows == 1){
			$data['groupreset']['status'] = 0;
			$this->db->where("groupId", $_POST['gid']);
			$this->db->update('groupsreport', $data['groupreset']);
			return $affected_rows;
		} else {
			return false;
		}
	}
}