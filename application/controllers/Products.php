<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Products extends CI_Controller {
    function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->model("products_model");	
        $this->load->library('session');
        $this->load->library('email');
    }
	
	public function index($page = 1){
		if(checkForAdminSession(1, true))redirect('admin/login');
		$data = array();
		$data['title'] = "Manage Products";
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
		
		//$where['nf.status'] = 1;
		$where['pro.procatId !='] = 0;

		/**Pagination **/
		$select1 = ('count(distinct pro.productId) as count');
		$all = $this->products_model->get($where,$orderby,$select1,$join,"","","","",$like,$or_like,$or_where, $where_in, $where_not);
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
		$config['base_url'] = base_url().'products/index';
		$config["cur_page"] = $page;
		if(isset($get['sel']) && !empty($get['sel'])){
			$config["per_page"] = $get['sel'];
		} else{
			$config["per_page"] = $this->config->item('per_page');
		}
		$this->pagination->initialize($config);
		$offset = $limit * ($page - 1);
		
		$orderby['pro.productId'] = 'DESC';
		$data["products"] = $this->products_model->get($where,$orderby,$select,$join,$groupby,$limit,$offset,"",$like,$or_like,$or_where, $where_in, $where_not);
		load_default_template('products/list',$data,$this);
		echo $this->template->render("", true);
	}
	
	/*Status update the product*/
	function updatestatusProduct(){
		if(empty($_POST['pro_id'])) return false;
		$data['pro']['status'] = (int) $_POST['relval'];
		$this->db->where("productId", $_POST['pro_id']);
		$this->db->update('products', $data['pro']);
		$affected_rows = $this->db->affected_rows();
	
		if($affected_rows == 1){
			return $affected_rows;
		} else {
			return false;
		}
	}
	
	/*Block the Product*/
	function blockstatusNews(){
		if(empty($_POST['pro_id'])) return false;
		$data['pro']['reportAbuse'] = (int) $_POST['relval'];
		$this->db->where("productId", $_POST['pro_id']);
		$this->db->update('products', $data['pro']);
		$affected_rows = $this->db->affected_rows();
		if($affected_rows == 1){
			return $affected_rows;
		} else {
			return false;
		}
	}
	
	/*Report Abuse the Product*/	
	function reportAbuseblocksts(){
		if(empty($_POST['pro_id'])) return false;
		$data['pro']['reportAbuse'] = 0;
		$this->db->where("productId", $_POST['pro_id']);
		$this->db->update('products', $data['pro']);
		$affected_rows = $this->db->affected_rows();
		if($affected_rows == 1){
			$data['proreset']['status'] = 0;
			$this->db->where("productId", $_POST['pro_id']);
			$this->db->update('productsreport', $data['proreset']);
			
			/*$data['prorepack']['status'] = 0;
			$this->db->where("productId", $_POST['pro_id']);
			$this->db->update('userpackages', $data['prorepack']);*/
			
			return $affected_rows;
		} else {
			return false;
		}
	}
}