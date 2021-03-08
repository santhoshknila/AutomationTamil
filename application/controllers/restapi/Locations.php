<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Locations extends CI_Controller {
	private $error = array();
	function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->model("locations_model");
        $this->load->model("general_model");
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
			$data['title'] = "Manage Locations";
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
			
			$where['loc.status'] = 1;
			$where['loc.parentId'] = 0;

			if (isset($post['page'])) {
				$page = $post['page'];
			} else {
				$page = 1;
			}
			$limit = 0;
			$offset = 0;
			
			$orderby['loc.name'] = 'ASC';

			$allCountry = $this->general_model->getCountries(0);

			//$allLocation = $this->locations_model->get($where,$orderby,$select,$join,$groupby,$limit,$offset,"",$like,$or_like,$or_where,$where_in);
			$totalLocation = count($this->general_model->getCountries(0));

			$response['location'] = array();		
			if (!empty($allCountry)) {
				foreach($allCountry as $locrs){
					$sublocarr = array();
					//$whereSub['loc.status'] = 1;
					//$whereSub['loc.parentId'] = $locrs->locationsId;
					//$sublocation = $this->locations_model->get($whereSub);
					$sublocation = $this->general_model->getProvince($locrs->id);
					if(!empty($sublocation)){
						foreach($sublocation as $subloc){
							$sublocarr[] = array(
								'sublocId' => $subloc->id,
								'sublocName' => $subloc->name,
								'sublocImg' => '',
							);
						}
					}
					$response['location'][] = array(
						'locId' => $locrs->id,
						'locName' => $locrs->name,
						'locImg' => '',
						'subloc' => $sublocarr,
					);
				}				
				//$response['current_page'] = (int)$page;
				//$response['total_page'] = ceil($totalCategory / $limit);
				$response['status'] = '200';
				$response['messages'] = 'Locations listings';					
				json_output($response['status'],$response);
			} else {
				$response['current_page'] = (int)0;
				$response['total_page'] = 0;
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
