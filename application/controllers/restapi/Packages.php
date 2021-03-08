<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Packages extends CI_Controller {
	private $error = array();
	function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->model("packages_model");
        $this->load->model("products_model");
        $this->load->model("auth_model");		
        $this->load->library('session');
    }
	
	public function index(){
		$userauth = array();
		$userauth['users_id']  = $this->input->get_request_header('User-ID', TRUE);
        $userauth['token']     = $this->input->get_request_header('Auth-Key', TRUE);
		if(checkForUserSession($userauth) == 1){			
			$post = $this->input->post();			
			$data = array();
			$data['title'] = "Manage Packages";
			$select=array();
			$where=array();
			$whereSub=array();
			$orderby = array();
			$join = array();
			$groupby =array();
			$like = array();
			$or_like = array();
			$or_where = array();
			$where_in = array();
			$where_not = array();
			
			$where['pack.status'] = 1;

			if (isset($post['page'])) {
				$page = $post['page'];
			} else {
				$page = 1;
			}
			$limit = 0;
			$offset = 0;
			
			$orderby['pack.packageId'] = 'ASC';
			$allPackage = $this->packages_model->get($where,$orderby,$select,$join,$groupby,$limit,$offset,"",$like,$or_like,$or_where,$where_in);			
			$totalPackage = count($this->packages_model->get($where,$orderby));
			
			$response['packages'] = array();		
			if (!empty($allPackage)) {
				foreach($allPackage as $packrs){
					if($packrs->priceType == 1){ $priceType = "Month"; } else if($packrs->priceType == 2){ $priceType = "Year"; }
					$response['packages'][] = array(
						'packId' => $packrs->packageId,
						'packTitle' => $packrs->title,
						'packDesc' => $packrs->description,
						'packPrice' => $packrs->price,
						'packPricetype' => $priceType,
					);
				}
				$response['status'] = '200';
				$response['messages'] = 'Packages listings';
				json_output($response['status'],$response);
			} else {				
				$response['status'] = '200';
				$response['messages'] = 'No records found';					
				json_output($response['status'],$response);
			}
			
		} else {
			$response['status'] = '401';
			$response['messages'] = 'Your Authorization details are incorrect.';					
			json_output($response['status'],$response);
		}
	}
}
