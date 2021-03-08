<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tamilethosregister extends CI_Controller {
	private $error = array();
	function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->model("users_model");		
        $this->load->model("newsfeeds_model");		
        $this->load->model("friends_model");		
        $this->load->model("packages_model");		
        $this->load->model("ads_model");		
        $this->load->model("products_model");		
        $this->load->model("locations_model");		
        $this->load->model("category_model");		
        $this->load->model("auth_model");		
        $this->load->library('session');
        $this->load->library('email');
		$this->load->library('form_validation');
		$this->load->library('phpass');
    }
	
	public function index(){
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			$response['status'] = '401';
			$response['messages'] = 'Bad request.';
			json_output($response['status'],$response);	
			return;			
		} else {
			$data = array( 'client_service' => $_SERVER['HTTP_CLIENT_SERVICE'], 'auth_key'=> $_SERVER['HTTP_AUTH_KEY']);
			$check_auth_client = $this->auth_model->check_auth_client($data);
			if($check_auth_client == true){
				$post = $this->input->post();
				$error = array();
				if($post){
					if (empty($this->input->post('firstName'))){
						$message = "First Name is required";
					} else {
						$name = $this->check_input($this->input->post('firstName'));
						if (!preg_match("/^[a-zA-Z ]*$/",$name)){
							$message = "Only letters and white space allowed. Numbers not allowed.";
						}
					}
					
					if (empty($this->input->post('surName'))) {
						$message = "surName is required";
					} else {
						$surName = $this->check_input($this->input->post('surName'));
						if (!preg_match("/^[a-zA-Z ]*$/",$surName)) {
							$message = "Only letters and white space allowed. Numbers not allowed.";
						}
					}
					
					if (empty($this->input->post('gender'))) {
						$message = "Gender is required";
					}
					
					if (empty($this->input->post('dob'))){
						$message = "Date of birth is required";
					} else {
						if(!preg_match('/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/',$this->input->post('dob'))){
							$message = "Invalid date format.";
						} 
					}
					
					if (empty($this->input->post('countryId'))){
						$message = "Country is required";
					} else {
						if (!preg_match('/^([0-9]*)$/',$this->input->post('countryId'))) {
							$message = "Only country numeric allowed. Not allowed letters.";
						} 
					}
					
					if (empty($this->input->post('provinceId'))){
						$message = "Province is required";
					} else {
						if (!preg_match('/^([0-9]*)$/',$this->input->post('provinceId'))) {
							$message = "Only province numeric allowed. Not allowed letters.";
						} 
					}
					
					/*if (empty($this->input->post('districtId'))){
						$message = "Suburb is required";
					} else {
						if (!preg_match('/^([0-9]*)$/',$this->input->post('districtId'))) {
							$message = "Only Suburb numeric allowed. Not allowed letters.";
						} 
					}
					
					if (empty($this->input->post('cityId'))){
						$message = "City is required";
					} else {
						if (!preg_match('/^([0-9]*)$/',$this->input->post('cityId'))) {
							$message = "Only city numeric allowed. Not allowed letters.";
						} 
					}*/
					
					if (empty($this->input->post('emailid'))){
						$message = "Email ID is required";
					} else {
						if (!preg_match('/^([\w-\.]+)@[a-zA-Z0-9-]{1,2}((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/',$this->input->post('emailid'))){
							$message = "Enter valid email id.";
						} else if($this->input->post('emailid')){
							$existEmail = $this->users_model->getUseremail($this->input->post('emailid'));
							if($existEmail == 0){
								$message = "Email ID already exist";
							}
						}
					}
					
					if (empty($this->input->post('password'))){
						$message = "Password is required";
					}
					
					if (empty($this->input->post('conpassword'))){
						$message = "Confirm password is required";
					} else if ($this->input->post('password') != $this->input->post('conpassword')) {
						$message = "Your passwords do not match. Please type carefully.";
					}
					
					if (empty($this->input->post('mothertongueId'))){
						$message = "Mother tongue is required";
					} else {
						if (!preg_match('/^([0-9]*)$/',$this->input->post('mothertongueId'))) {
							$message = "Only numeric allowed. Not allowed letters.";
						} 
					}
					
					if (empty($this->input->post('religionId'))){
						$message = "Religion is required";
					} else {
						if (!preg_match('/^([0-9]*)$/',$this->input->post('religionId'))) {
							$message = "Only numeric allowed. Not allowed letters.";
						} 
					}

					if (empty($this->input->post('jobTitle'))){
						$message = "Please select your Job Title!";
					} else {
						if (!preg_match('/^([0-9]*)$/',$this->input->post('jobTitle'))) {
							$message = "Only numeric allowed. Not allowed letters.";
						} 
					}
					
					if (strlen($this->input->post('mobileno')) < 5 || strlen($this->input->post('mobileno')) > 20) {
						$message = "Phone number must be between 5-20 characters";
					} else {
						if (!preg_match('/^([0-9]*)$/',$this->input->post('mobileno'))){
							$message = "Phone number allowed only numbers";
						}
					}
					
					if(empty($message)){
						$result = $this->users_model->save_insert_users($post);		
						if($result == 0){
							$response['status'] = '401';
							$response['messages'] = 'Registeration Faild. Please try again!';
							json_output($response['status'],$response);
						} else {
							$userdetails = $this->users_model->getUserdetails($this->session->userdata('userid'));
							$userdetails = array(
								'userid' => $userdetails->userid,
								'firstName' => $userdetails->firstName,
								'surName' => $userdetails->surName,
								'gender' => $userdetails->genderId,
								'dob' => $userdetails->dob,
								'jobTitle' => $userdetails->jobTitle,
								'jobTitletxt' => $userdetails->jobTitletxt,
								'company' => $userdetails->company,
								'mothertongueId' => $userdetails->mothertongueId,
								'religionId' => $userdetails->religionId,
								'emailid' => $userdetails->emailid,
								'mobileno' => $userdetails->mobileno,
								'altermobileno' => $userdetails->altermobileno,
								'countryId' => $userdetails->countryId,
								'provinceId' => $userdetails->provinceId,
								'districtId' => $userdetails->districtId,
								'cityId' => $userdetails->cityId,
								'profileimg' => $userdetails->profileimg,
								'userrole' => $userdetails->userrole,
								'token' => $userdetails->token,
								'settings' => array(
									'pushNotification' => $userdetails->pushNotification,
									'pollNotification' => $userdetails->pollNotification,
									'dobPrivacy' => $userdetails->dobPrivacy,
									'regionPrivacy' => $userdetails->regionPrivacy,
									'mothertonguePrivacy' => $userdetails->mothertonguePrivacy,
									'mobilenoPrivacy' => $userdetails->mobilenoPrivacy,
								)
							);
							$response['status'] = '200';
							$response['messages'] = 'Your account has been created successfully.';
							$response['users'] = $userdetails;
							json_output($response['status'],$response);
							return;
						}
					} else {
						$response['status'] = '401';
						$response['messages'] = $message;
						json_output($response['status'],$response);
						return;
					}
				}
			}  else {
				$response['status'] = '401';
				$response['messages'] = 'Authentication failed try again...';
				json_output($response['status'],$response);
				return;
			}
		}
	}
	
	function check_input($data){
		$data = trim($data);
		$data = stripslashes($data);
		$data = htmlspecialchars($data);
		return $data;
	}
}