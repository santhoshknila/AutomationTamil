<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class General extends CI_Controller {
	
	function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->model("general_model");		
        $this->load->model("auth_model");		
        $this->load->library('session');
        $this->load->library('email');
    }

	public function common(){
		$country = $this->general_model->getCountries(0);
		if(!empty($country)){
			$mothertongue = $this->general_model->getMothertongue();
			$religion = $this->general_model->getReligion();
			$jobtitle = $this->general_model->getJobtitle();
			$privacy = $this->general_model->getPrivacy();
			$groupPrivacy = $this->general_model->getgroupPrivacy();
			$gender = $this->general_model->getGender();
			$response['status'] = '200';
			$response['messages'] = 'Common listing.';
			$response['country'] = $country;
			$response['mothertongue'] = $mothertongue;
			$response['religion'] = $religion;
			$response['jobtitle'] = $jobtitle;
			$response['privacy'] = $privacy;
			$response['groupPrivacy'] = $groupPrivacy;
			$response['gender'] = $gender;
			$response['commonUrl'] = base_url();
			$response['profileImgurl'] = base_url().'uploads/users/';
			$response['profileImgthumburl'] = base_url().'uploads/users/thumb/';
			$response['newsfeedUrl'] = base_url().'uploads/newsfeeds/';
			$response['newsfeedthumbUrl'] = base_url().'uploads/newsfeeds/thumb/';
			$response['groupimgUrl'] = base_url().'uploads/groupImage/';
			$response['groupthumbimgUrl'] = base_url().'uploads/groupImage/thumb/';
			$response['eventimgUrl'] = base_url().'uploads/events/';
			$response['eventimgthumbUrl'] = base_url().'uploads/events/thumb/';
			$response['categoryimgUrl'] = base_url().'uploads/category/';
			$response['productimgUrl'] = base_url().'uploads/product/';
			$response['productthumbimgUrl'] = base_url().'uploads/product/thumb/';
			
		} else {
			$response['status'] = '401';
			$response['messages'] = 'No common listing.';
		}
		json_output($response['status'],$response);
	}
	
	public function province($cid){
		$provincers = $this->general_model->getProvince($cid);
		if(!empty($provincers)){
			$response['status'] = '200';
			$response['messages'] = 'Province listing based on country.';
			foreach($provincers as $pro) {
				$districtArray = array();
				$districtQuery = $this->general_model->getDistrict($pro->id);
				foreach($districtQuery as $dis) {
					$city = array();					
					$cityrs = $this->general_model->getCity($dis->id);
					foreach($cityrs as $cities) {
						$city[] = array( 
							'id' => $cities->id,
							'name' => $cities->name
						);
					}
					$districtArray[] = array(
						'id' => $dis->id,
						'name' => $dis->name,
						'city' => $city
					);
				} 
				$province[] = array( 
					'id' => $pro->id,
					'name' => $pro->name,
					'suburb' => $districtArray,
				);
			}
			$response['province'] = $province;
		} else {
			$response['status'] = '401';
			$response['messages'] = 'No Province based on this Countries.';
		}
		json_output($response['status'],$response);
	}
	
	public function district($pid){
		$district = $this->general_model->getDistrict($pid);
		if(!empty($district)){
			$response['status'] = '200';
			$response['messages'] = 'District listing based on province.';
			$response['lists'] = $district;
		} else {
			$response['status'] = '401';
			$response['messages'] = 'No District based on this Province.';
		}
		json_output($response['status'],$response);
	}
	
	public function city($did){
		$city = $this->general_model->getCity($did);
		if(!empty($city)){
			$response['status'] = '200';
			$response['messages'] = 'City listing based on district.';
			$response['lists'] = $city;
		} else {
			$response['status'] = '401';
			$response['messages'] = 'No city based on this district.';
		}
		json_output($response['status'],$response);
	}
	
	public function religion(){
		$religion = $this->general_model->getReligion();
		if(!empty($religion)){
			$response['status'] = '200';
			$response['messages'] = 'Religion listing.';
			$response['lists'] = $religion;
		} else {
			$response['status'] = '401';
			$response['messages'] = 'No Religion.';
		}
		json_output($response['status'],$response);
	}
	
	public function mothertongue(){
		$mothertongue = $this->general_model->getMothertongue();
		if(!empty($mothertongue)){
			$response['status'] = '200';
			$response['messages'] = 'Mothertongue listing.';
			$response['lists'] = $mothertongue;
		} else {
			$response['status'] = '401';
			$response['messages'] = 'No Mothertongue.';
		}
		json_output($response['status'],$response);
	}
	
	public function getPages($pId){
		$pages = $this->general_model->getpageDetails($pId);
		if(!empty($pages)){
			$response['pages'] = $pages;
			$response['status'] = '200';
			$response['messages'] = 'Page Details.';
		} else {
			$response['pages'] = '';
			$response['status'] = '401';
			$response['messages'] = 'No page details found.';
		}
		json_output($response['status'],$response);
	}
	
	public function notifySettings(){
		$userauth = array();
		$userauth['users_id']  = $this->input->get_request_header('User-ID', TRUE);
        $userauth['token']     = $this->input->get_request_header('Auth-Key', TRUE);
		if(checkForUserSession($userauth) == 1){
			$post = $this->input->post();
			if(isset($post) && !empty($post)){
				$post['userId'] = $userauth['users_id'];
				$result = $this->general_model->notifySettings_update($post);
				if($result == ''){
					$response['status'] = '200';
					$response['messages'] = 'Notifications settings not updated. Kindly try again!';					
					json_output($response['status'],$response);
				} else {
					$response['status'] = '200';
					$response['messages'] = 'Notifications settings updated successfully.';
					json_output($response['status'],$response);
				}
			} else {
				$response['status'] = '401';
				$response['messages'] = 'Kindly send the post details.';					
				json_output($response['status'],$response);
			}
		} else {
			$response['status'] = '401';
			$response['messages'] = 'Your Authorization details are incorrect.';					
			json_output($response['status'],$response);
		}
	}
	
	public function privacySettings(){
		$userauth = array();
		$userauth['users_id']  = $this->input->get_request_header('User-ID', TRUE);
        $userauth['token']     = $this->input->get_request_header('Auth-Key', TRUE);
		if(checkForUserSession($userauth) == 1){
			$post = $this->input->post();
			if(isset($post) && !empty($post)){
				$post['userId'] = $userauth['users_id'];
				$result = $this->general_model->privacySettings_update($post);
				if($result == ''){
					$response['status'] = '200';
					$response['messages'] = 'Privacy settings not updated. Kindly try again!';					
					json_output($response['status'],$response);
				} else {
					$response['status'] = '200';
					$response['messages'] = 'Privacy settings updated successfully.';
					json_output($response['status'],$response);
				}
			} else {
				$response['status'] = '401';
				$response['messages'] = 'Kindly send the post details.';					
				json_output($response['status'],$response);
			}
		} else {
			$response['status'] = '401';
			$response['messages'] = 'Your Authorization details are incorrect.';					
			json_output($response['status'],$response);
		}
	}
	
}
