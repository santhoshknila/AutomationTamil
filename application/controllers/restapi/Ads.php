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
	
	public function index(){
		$userauth = array();
		$userauth['users_id']  = $this->input->get_request_header('User-ID', TRUE);
        $userauth['token']     = $this->input->get_request_header('Auth-Key', TRUE);
		if(checkForUserSession($userauth) == 1){			
			$post = $this->input->post();			
			$data = array();
			$data['title'] = "Manage Ads";
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
			
			$where['ad.status'] = 1;

			if (isset($post['page'])) {
				$page = $post['page'];
			} else {
				$page = 1;
			}
			$limit = 0;
			$offset = 0;
			
			$orderby['ad.adsId'] = 'ASC';
			$allads = $this->ads_model->get($where,$orderby,$select,$join,$groupby,$limit,$offset,"",$like,$or_like,$or_where,$where_in);			
			$totalads = count($this->ads_model->get($where,$orderby));
			
			$response['ads'] = array();		
			if (!empty($allads)) {
				foreach($allads as $adsrs){
					if($adsrs->priceType == 1){ $priceType = "Month"; } else if($adsrs->priceType == 2){ $priceType = "Year"; }
					if($adsrs->displayType == 1){ $displayType = "Home"; } else if($adsrs->displayType == 2){ $displayType = "Category"; } else if($adsrs->displayType == 3){ $displayType = "Both"; }
					$response['ads'][$displayType][] = array(
						'adsId' => $adsrs->adsId,
						'adsTitle' => $adsrs->title,
						'adsDesc' => $adsrs->description,
						'adsPrice' => $adsrs->price,
						'adsPricetype' => $priceType,
					);
				}
				$response['status'] = '200';
				$response['messages'] = 'Ads listings';
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
