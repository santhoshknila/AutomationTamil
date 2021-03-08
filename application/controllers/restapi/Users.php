<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends CI_Controller {
	private $error = array();
	function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->model("users_model");		
        $this->load->model("newsfeeds_model");	
		$this->load->model("general_model");		
        $this->load->model("friends_model");		
        $this->load->model("packages_model");		
        $this->load->model("ads_model");		
        $this->load->model("products_model");		
        $this->load->model("groups_model");		
        $this->load->model("locations_model");		
        $this->load->model("category_model");		
        $this->load->model("auth_model");		
        $this->load->library('session');
        $this->load->library('email');
		$this->load->library('form_validation');
		$this->load->library('phpass');
		$this->load->helper('date');
    }
	
	public function tamilRegister1(){
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

						if(isset($_FILES['userfile']['name']) && !empty($_FILES['userfile']['name']))
						{
							$files = $_FILES;						
							$config = array(
								'upload_path' => "./uploads/users/",
								'allowed_types' => "jpg|png|jpeg|gif",
								'overwrite' => TRUE,
								'max_size' => "2048000",
								'remove_spaces' => TRUE,
							);
							$_FILES['userfile']['name']     = time().'_'.$files['userfile']['name'];
							$_FILES['userfile']['type']     = $files['userfile']['type'];
							$_FILES['userfile']['tmp_name'] = $files['userfile']['tmp_name'];
							$_FILES['userfile']['error']    = $files['userfile']['error'];
							$_FILES['userfile']['size']     = $files['userfile']['size']; 
		   
							$this->load->library('upload', $config);

							if (!$this->upload->do_upload('userfile')){
								$error = array('error' => $this->upload->display_errors());
							} else {
								$fileData = $this->upload->data();							
								$post['profileimg'] = $fileData['file_name'];
								
								$file = $fileData['file_name'];
								$path = $fileData['full_path'];
								$config_resize['image_library'] = 'gd2';  
								$config_resize['source_image'] = $path;
								$config_resize['create_thumb'] = false;
								$config_resize['maintain_ratio'] = TRUE;
								$config_resize['width'] = 100;
								$config_resize['height'] = 100;
								$config_resize['new_image'] = './uploads/users/thumb/'.$file;
								$this->load->library('image_lib',$config_resize);
								$this->image_lib->clear();
								$this->image_lib->initialize($config_resize);
								$this->image_lib->resize();
							}
						}
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
									'commentNotification' => $userdetails->commentNotification,
									'likeNotification' => $userdetails->likeNotification,
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

	

	public function login(){
		$method = $_SERVER['REQUEST_METHOD'];		
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$data = array( 'client_service' => $_SERVER['HTTP_CLIENT_SERVICE'], 'auth_key'=> $_SERVER['HTTP_AUTH_KEY']);
			$check_auth_client = $this->auth_model->check_auth_client($data);
			if($check_auth_client == true){
				$post = $this->input->post();
				if(isset($post) && !empty($post)){
					if($this->users_model->check_usersLogin($post)){
						/*Get the session userid based on details with token*/
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
								'commentNotification' => $userdetails->commentNotification,
								'likeNotification' => $userdetails->likeNotification,
								'dobPrivacy' => $userdetails->dobPrivacy,
								'regionPrivacy' => $userdetails->regionPrivacy,
								'mothertonguePrivacy' => $userdetails->mothertonguePrivacy,
								'mobilenoPrivacy' => $userdetails->mobilenoPrivacy,
							)
						);
						$response['status'] = '200';
						$response['messages'] = 'You have logged in successfully.';
						$response['users'] = $userdetails;
						json_output($response['status'],$response);
					} else {
						if($this->users_model->check_usersdelete($post))
						{

						$response['status'] = '401';
						$response['messages'] = 'Contact Administrator.';						
						json_output($response['status'],$response);

						}else{

						$response['status'] = '401';
						$response['messages'] = 'Your login failed. Try again.';						
						json_output($response['status'],$response);

						}

					}
				} else {
					$response['status'] = '401';
					$response['messages'] = 'Your login failed. Try again.';					
					json_output($response['status'],$response);
				}
			}
		}
	}
	
	public function register(){
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			$response['status'] = '401';
			$response['messages'] = 'Bad request.';
			json_output($response['status'],$response);			
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
									'commentNotification' => $userdetails->commentNotification,
									'likeNotification' => $userdetails->likeNotification,
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
						}
					} else {
						$response['status'] = '401';
						$response['messages'] = $message;
						json_output($response['status'],$response);
					}
				}
			} else {
				$response['status'] = '401';
				$response['messages'] = 'Authentication failed try again...';
				json_output($response['status'],$response);
			}
		}
	}

	public function forgotpass(){
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			$response['status'] = '401';
			$response['messages'] = 'Bad request.';
			json_output($response['status'],$response);			
		} else {
			$post = $this->input->post();
			$error = array();
			if($post){
				if (empty($this->input->post('emailid'))){
					$message = "Email ID is required";
				} else {
					if (!preg_match('/^([\w-\.]+)@[a-zA-Z0-9-]{1,2}((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/',$this->input->post('emailid'))) {
						$message = "Enter valid email id.";
					} else if($this->input->post('emailid')){
						$existEmail = $this->users_model->getUseremail($this->input->post('emailid'));
						if($existEmail == 1){
							$message = "This email id not registered. Enter the registered email id";
						}
					}
				}
			
				
				if(empty($message)){
				    $emaildetails = $this->users_model->getUserdetailsbyemail($this->input->post('emailid'));
					$sercetdetails = $emaildetails->emailid;
					$token = dec_enc('encrypt', $sercetdetails);
					$from = "knilaitsolution@gmail.com";
				    $to = $this->input->post('emailid');
					$subject = "Tamil Ethos - Forget Password";
					$headers = "From:knilaitsolution@gmail.com" . "\r\n";
					$headers .= 'MIME-Version: 1.0' . "\r\n";
					$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
					$content = '<table cellspacing="0" cellpadding="0" border="0" width="100%">
						<tr>
							<td class="navbar navbar-inverse" align="center">
								<!-- This setup makes the nav background stretch the whole width of the screen. -->
								<table width="650px" cellspacing="0" cellpadding="3" class="container head" bgcolor="#ccc" style="padding:10px;">
									<tr class="navbar navbar-inverse">
										<td colspan="4"><a class="brand" href="'.base_url().'"><img src="'.base_url().'skin/default/images/logo.png" /></a></td>
										<td><p class="bc-title" style="color:#000;font-size:20px;float:right;">Tamil Ethos Forget Password Details</p></td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td bgcolor="#FFFFFF" align="center" >
								<table width="650px" cellspacing="0" cellpadding="3" class="container" style="padding:40px;background-color:#eee;">
									<tr>
										<td>
											<p>Hi '.$emaildetails->firstName.',</p>
											<p>You recently requested to reset your password for your account. Use the link below to reset it.</p>
											<p><b>URL: </b><a href="'.base_url().'users/resetpassword/?token='.$token.'">'.base_url().'users/resetpassword/?token='.$token.'</a></p>
											<p style="font-size:14px;line-height:20px;">If you have any questions, please contact admin at <a href="mailto:knilaitsolution@gmail.com">knilaitsolution@gmail.com</a></p>
										</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td align="center">
								<table width="650px" cellspacing="0" cellpadding="3" class="container" bgcolor="#04a1e0" style="padding:10px;">
									<tr>
										<td>
											<p style="text-align:center; color:#fff;">@ '.date('Y').' Copyrights <a href="'.base_url().'" style="color:#fff; text-decoration:none;">Tamil Ethos</a>. All rights reserved.</p>
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>';
					
					 $mail = sendMail($from,$to,$subject,$content,$headers);
				 print_r($mail);exit; 
					if($mail == 0){
						$response['status'] = '401';
						$response['messages'] = 'Password reset request not sent try again.';
						json_output($response['status'],$response);
					} else {
						$response['status'] = '200';
						$response['messages'] = 'Your password reset request link has been sent your registered email address. Check It.';
						json_output($response['status'],$response);
					}
				} else {
					$response['status'] = '401';
					$response['messages'] = $message;
					json_output($response['status'],$response);
				}
			}
		}
	}

	public function logout(){
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			$response['status'] = '401';
			$response['messages'] = 'Bad request.';
			json_output($response['status'],$response);
		} else {
			$data = array( 'client_service' => $_SERVER['HTTP_CLIENT_SERVICE'], 'auth_key'=> $_SERVER['HTTP_AUTH_KEY']);
			$check_auth_client = $this->auth_model->check_auth_client($data);
			
			if($check_auth_client == true){
				$user['users_id']  = $this->input->post('User-ID');
				$user['token']     = $this->input->post('Auth-Key');
				$check_user_auth =  $this->users_model->logout($user);
				if($check_user_auth == 1){
					$this->session->sess_destroy();
					$response['status'] = '200';
					$response['messages'] = 'You have logged out successfully.';
					json_output($response['status'],$response);
				} else {
					if($this->users_model->check_usersdeletelogout($user))
						{

						$this->session->sess_destroy();
					$response['status'] = '200';
					$response['messages'] = 'You have logged out successfully.';
					json_output($response['status'],$response);

						}else{

						$response['status'] = '401';
						$response['messages'] = 'Something went wrong. Try again after sometime...';
						json_output($response['status'],$response);

						}
					
				}
			} else {
				$response['status'] = '401';
				$response['messages'] = 'Authentication failed try again...';
				json_output($response['status'],$response);
			}
		}
	}
	
	public function getUserlist(){
		$userauth = array();
		$userauth['users_id']  = $this->input->get_request_header('User-ID', TRUE);
        $userauth['token']     = $this->input->get_request_header('Auth-Key', TRUE);
		if(checkForUserSession($userauth) == 1){
			$listUserarray = array();
			$where = array();			
			$where['us.userrole'] = 'user';
			$orderby['us.userid'] = 'DESC';
			$allUserlist = $this->users_model->get($where, $orderby);
			$response['userCnt'] = count($allUserlist);
			if(!empty($allUserlist)){
				foreach($allUserlist as $userList){
					if($userList->userid != $userauth['users_id']){
						$userdetails = $this->users_model->getUseriddetails($userList->userid);
						$listUserarray[] = array(
							'userId' => $userdetails->userid,
							'userName' => $userdetails->firstName,
							'userImg' => $userdetails->profileimg,
						);
					}
				}
			}
			$response['userList'] = $listUserarray;
			$response['status'] = '200';			
			$response['messages'] = 'User Listing successfully!';
			json_output($response['status'],$response);
		} else {
			$response['status'] = '401';
			$response['messages'] = 'Your Authorization details are incorrect.';					
			json_output($response['status'],$response);
		}
	}
	
	function check_input($data){
		$data = trim($data);
		$data = stripslashes($data);
		$data = htmlspecialchars($data);
		return $data;
	}
	
	public function myProfile(){
		$userauth = array();
		$userauth['users_id']  = $this->input->get_request_header('User-ID', TRUE);
        $userauth['token']     = $this->input->get_request_header('Auth-Key', TRUE);
		if(checkForUserSession($userauth) == 1){
			$userdetails = $this->users_model->getUserdetails($userauth['users_id']);
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
					'commentNotification' => $userdetails->commentNotification,
					'likeNotification' => $userdetails->likeNotification,
					'dobPrivacy' => $userdetails->dobPrivacy,
					'regionPrivacy' => $userdetails->regionPrivacy,
					'mothertonguePrivacy' => $userdetails->mothertonguePrivacy,
					'mobilenoPrivacy' => $userdetails->mobilenoPrivacy,
				)
			);
			$response['users'] = $userdetails;
			$response['status'] = '200';			
			$response['messages'] = 'User profile details...';
			json_output($response['status'],$response);
		} else {
			$response['status'] = '401';
			$response['messages'] = 'Your Authorization details are incorrect.';					
			json_output($response['status'],$response);
		}
	}
	
	public function changePassword(){
		$userauth = array();
		$userauth['users_id']  = $this->input->get_request_header('User-ID', TRUE);
        $userauth['token']     = $this->input->get_request_header('Auth-Key', TRUE);
		if(checkForUserSession($userauth) == 1){
			$post = $this->input->post();
			$post['userid'] = $userauth['users_id'];
			$message = array();
			if(isset($post) && !empty($post)){
				if (empty($this->input->post('oldpassword'))){
					$message = "Old Password is required";
				} else if($this->input->post('oldpassword')) {
					$useroldpassChk = $this->users_model->check_usersoldpassword($this->input->post('oldpassword'), $userauth['users_id']);
					if(empty($useroldpassChk)){
						$message = "Your old password not match. Please type carefully.";
					}
				} 
				if(empty($post['password'])){
					$message = "Password is required";
				} 
				if(empty($post['conpassword'])){
					$message = "Confirm password is required";
				} else if($this->input->post('password') != $this->input->post('conpassword')) {
					$message = "Your passwords do not match. Please type carefully.";
				}
				if(empty($message)){
					$result = $this->users_model->save_update_password($post);
					if($result == 0){
						$response['status'] = '401';
						$response['messages'] = 'Password Not updated. Try again..';
						json_output($response['status'],$response);
					} else {
						$response['status'] = '200';
						$response['messages'] = 'Your Password Updated successfully.';
						json_output($response['status'],$response);
					}
				} else {
					$response['status'] = '401';
					$response['messages'] = $message;
					json_output($response['status'],$response);
				}
				
				
			}	
		} else {
			$response['status'] = '401';
			$response['messages'] = 'Your Authorization details are incorrect.';					
			json_output($response['status'],$response);
		}
	}
	
	public function updateProfile(){
		$userauth = array();
		$userauth['users_id']  = $this->input->get_request_header('User-ID', TRUE);
        $userauth['token']     = $this->input->get_request_header('Auth-Key', TRUE);
		if(checkForUserSession($userauth) == 1){
			$post = $this->input->post();
			$post['userid'] = $userauth['users_id'];
			$error = array();
			if(isset($post) && !empty($post)){
				if (empty($this->input->post('firstName'))) {
					$message = "First Name is required";
				} else {
					$name = $this->check_input($this->input->post('firstName'));
					if (!preg_match("/^[a-zA-Z ]*$/",$name)) {
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
					if (!preg_match('/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/',$this->input->post('dob'))) {
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
				} else{
					if (!preg_match('/^([0-9]*)$/',$this->input->post('mobileno'))) {
						$message = "Phone number allowed only numbers";
					}
				}				
				
				if(empty($message)){
					if(isset($_FILES['userfile']['name']) && !empty($_FILES['userfile']['name'])){
						$files = $_FILES;						
						$config = array(
							'upload_path' => "./uploads/users/",
							'allowed_types' => "jpg|png|jpeg|gif",
							'overwrite' => TRUE,
							'max_size' => "2048000",
							'remove_spaces' => TRUE,
						);
						$_FILES['userfile']['name']     = time().'_'.$files['userfile']['name'];
						$_FILES['userfile']['type']     = $files['userfile']['type'];
						$_FILES['userfile']['tmp_name'] = $files['userfile']['tmp_name'];
						$_FILES['userfile']['error']    = $files['userfile']['error'];
						$_FILES['userfile']['size']     = $files['userfile']['size']; 
	   
						$this->load->library('upload', $config);

						if (!$this->upload->do_upload('userfile')){
							$error = array('error' => $this->upload->display_errors());
						} else {
							$fileData = $this->upload->data();							
							$post['profileimg'] = $fileData['file_name'];
							
							$file = $fileData['file_name'];
							$path = $fileData['full_path'];
							$config_resize['image_library'] = 'gd2';  
							$config_resize['source_image'] = $path;
							$config_resize['create_thumb'] = false;
							$config_resize['maintain_ratio'] = TRUE;
							$config_resize['width'] = 100;
							$config_resize['height'] = 100;
							$config_resize['new_image'] = './uploads/users/thumb/'.$file;
							$this->load->library('image_lib',$config_resize);
							$this->image_lib->clear();
							$this->image_lib->initialize($config_resize);
							$this->image_lib->resize();
						}
					}
					$result = $this->users_model->save_insert_users($post);			
					if($result == 0){
						$response['status'] = '401';
						$response['messages'] = 'No changes has been made on your profile.';
						json_output($response['status'],$response);
					} else {
						$userdetails = $this->users_model->getUserdetails($post['userid']);
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
								'commentNotification' => $userdetails->commentNotification,
								'likeNotification' => $userdetails->likeNotification,
								'dobPrivacy' => $userdetails->dobPrivacy,
								'regionPrivacy' => $userdetails->regionPrivacy,
								'mothertonguePrivacy' => $userdetails->mothertonguePrivacy,
								'mobilenoPrivacy' => $userdetails->mobilenoPrivacy,
							)
						);
						$response['users'] = $userdetails;
						$response['status'] = '200';
						$response['messages'] = 'Profile has been update successfully.';
						json_output($response['status'],$response);
					}
				} else {
					$response['status'] = '401';
					$response['messages'] = $message;
					json_output($response['status'],$response);
				}
			}
		} else {
			$response['status'] = '401';
			$response['messages'] = 'Your Authorization details are incorrect.';					
			json_output($response['status'],$response);
		}
	}
	
	/*Get user profile with post & polls*/
	public function getuserProfile($userid, $page){
		$userauth = array();
		$userauth['users_id']  = $this->input->get_request_header('User-ID', TRUE);
        $userauth['token']     = $this->input->get_request_header('Auth-Key', TRUE);
		if(checkForUserSession($userauth) == 1){
			$userdetails = $this->users_model->getUserdetails($userid);
			$response['users'] = $userdetails;
			$getfriendsCnt = array(
				'loggedUser'     => $userid,
			);
			$friRequesttot = $this->friends_model->myConnectionsTotal($getfriendsCnt);
			$response['totalFriend'] = (string)count($friRequesttot);
			$feiendSts = $this->friends_model->getFriendstatus($userid, $userauth['users_id']);
			
			if(isset($feiendSts[0])){
				$fristatus = (int)$feiendSts[0]['status'];
				$friendID = (string)$feiendSts[0]['friendId'];
				$requestSenderID = (string)$feiendSts[0]['sendRequestUserId'];
			} else {
				$fristatus = 0;
				$friendID = '';
				$requestSenderID = '';
			}
			
			$friendStatusarr = array(
				'friendID' => $friendID,
				'requestSenderID' => $requestSenderID,
				'friendStatus' => $fristatus,
			);
			$response['friendStatusarr'] = $friendStatusarr;
			$limit = 3;			
			$filter_data = array(				
				'useridPro' => $userid,
				'useridlog' => $userauth['users_id'],
				'start'  => ($page - 1) * $limit,
				'limit'  => $limit
			);
			
			$allFeeds = $this->newsfeeds_model->getProfilePostdetails($filter_data);			
			$totalFeeds = count($this->newsfeeds_model->getProfileTotalPostdetails($filter_data));
			$response['totalFeeds'] = (string)$totalFeeds;
			
			$response['newsfeeds'] = array();
			if (!empty($allFeeds)) {
				foreach($allFeeds as $feeds){
					$userdetails = $this->users_model->getUseriddetails($feeds['user']);
					if($feeds['user'] == 1){
						$createUser = 'Tamil Ethos';
						$isAdmin ='1';
					} else {
						$createUser = $userdetails->firstName;
						$isAdmin ='0';
					}
					
					if($feeds['feedType'] == 'news'){
						$pollsarray = array();											
						$attributearray = array(
							'likeCnt' => getTotallikecnt($feeds['feedID']),
							'commentCnt' => getTotalcommentscnt($feeds['feedID']),
							'myLike' => getMylikes($userauth['users_id'], $feeds['feedID']),
						);					
						$mediaFiles = getMedia($feeds['feedID']);
						$mediaarray = array();
						if(!empty($mediaFiles)){
							
							foreach($mediaFiles as $media){
								$mediaarray[] = array(
									'imageId' => $media['newsfeedImageId'],
									'fileType' => $media['fileType'],
									'path' => $media['imagevideo_url'],
								);
							}
						}
						
					} else if($feeds['feedType'] == 'polls'){
						$mediaarray = array();
						$pollsarray = array();
						$attributearray = array(
							'likeCnt' => 0,
							'commentCnt' => 0,
							'myLike' => 0,
						);	
						$pollAnswer = getPollsanswer($feeds['feedID']);
						if(!empty($pollAnswer)){
							foreach($pollAnswer as $pans){
								$pollsarray[] = array(
									'answerId' => $pans['pollingAnswerId'],
									'answer' => $pans['answer'],
									'likecnt' => getTotalpollcnt($pans['pollingAnswerId'], $feeds['feedID']).'%',
									'selected' => getMyanswerId($userauth['users_id'], $feeds['feedID'], $pans['pollingAnswerId']),
								);
							}
						}
					}
					
					/*Get the report abuse status*/					
					if($feeds['feedType'] == 'news'){
						$reportAbusests = $this->newsfeeds_model->getuserreportAbusests($userauth['users_id'], $feeds['feedID']);
						//$sharelink = base_url().'share?data=news&id='.$feeds['feedID'];
						//$sharelink = base_url().'news?id='.$feeds['feedID'];
						//$sharelink = 'http://tamilchamber.org.za/share?data=news&id='.$feeds['feedID'];
						$sharelink = $this->config->item('shareAddress').".page.link/?link=http://www.tamilchamber.org.news/news?id=".$feeds['feedID']."&apn=".$this->config->item('shareAndriodapn')."&amv=10&ibi=".$this->config->item('shareIOSibi')."&isi=".$this->config->item('shareIOSisi')."&ius=tamilethos";
					} else {
						$reportAbusests = 0;
						//$sharelink = 'http://www.tamilchamber.org.za/share?data=poll&id='.$feeds['feedID'];
						//$sharelink = base_url().'share?data=poll&id='.$feeds['feedID'];
						//$sharelink = base_url().'poll?id='.$feeds['feedID'];
						$sharelink = $this->config->item('shareAddress').".page.link/?link=http://www.tamilchamber.org.poll/poll?id=".$feeds['feedID']."&apn=".$this->config->item('shareAndriodapn')."&amv=10&ibi=".$this->config->item('shareIOSibi')."&isi=".$this->config->item('shareIOSisi')."&ius=tamilethos";
					}	

					if(!empty($feeds['gId'])){
						$groupDetails = $this->groups_model->getgroupDetails($feeds['gId'], '');
						$groupId = $feeds['gId'];
						$groupName = $groupDetails->groupName;
					} else {
						$groupId = '';
						$groupName = '';
					}			
					
					$response['newsfeeds'][] = array(
						'userId' => $userdetails->userid,
						'userName' => $createUser,
						'userImg' => $userdetails->profileimg,
						'firebaseId' => $userdetails->firebaseId,
						'feedID' => $feeds['feedID'],
						'feedTitle' => $feeds['feedTitle'],
						'feedDescription' => $feeds['feedDesc'],
						'feedType' => $feeds['feedType'],
						'isAdmin' => $isAdmin,
						'privacyId' => $feeds['pID'],
						'media' => $mediaarray,
						'attribute' => $attributearray,
						'groupId' => $groupId,
						'groupName' => $groupName,
						'polls' => $pollsarray,
						'feedcreateAt' => date_time_ago($feeds['crdate']),
						'feedexpireAt' => date_time_expire($feeds['exDate']),
						'shareLink' => $sharelink,
						'reportAbuse' => $reportAbusests,
					);
				}
			}
			$response['current_page'] = (int)$page;
			$response['total_page'] = ceil($totalFeeds / $limit);
			$response['status'] = '200';			
			$response['messages'] = 'User profile details show successfully!';
			json_output($response['status'],$response);
		} else {
			$response['status'] = '401';
			$response['messages'] = 'Your Authorization details are incorrect.';					
			json_output($response['status'],$response);
		}
	}
	
	public function aboutProfileInfo($aid){
		$userauth = array();
		$userauth['users_id']  = $this->input->get_request_header('User-ID', TRUE);
        $userauth['token']     = $this->input->get_request_header('Auth-Key', TRUE);
		if(checkForUserSession($userauth) == 1){
			$userdetails = $this->users_model->getUserdetailsPrivacy($userauth['users_id'], $aid);
			//print_r($userdetails);
			//exit;
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
				'firebaseId' => $userdetails->firebaseId,
			);
			$response['users'] = $userdetails;
			$response['status'] = '200';			
			$response['messages'] = 'User profile details show successfully!';
			json_output($response['status'],$response);			
		} else {
			$response['status'] = '401';
			$response['messages'] = 'Your Authorization details are incorrect.';					
			json_output($response['status'],$response);
		}
	}
	
	/*My Subscription details */
	public function mysubscription($data = array()){
		$userauth = array();
		$userauth['users_id']  = $this->input->get_request_header('User-ID', TRUE);
        $userauth['token']     = $this->input->get_request_header('Auth-Key', TRUE);
		if(checkForUserSession($userauth) == 1){
			$response['mySubscripe'] = array();
			$mySubscripe = $this->users_model->getmySubscription($userauth['users_id']);
			if(!empty($mySubscripe)) {
				foreach($mySubscripe as $subScripe){
					$where=array();
					$where['pack.packageId'] = $subScripe['packId'];
					$package = $this->packages_model->get($where);
					$packageTotalproduct = $package[0]->packageProTotal;
					
					/*Get my products count*/
					$groupby =array();
					$wheremy['pro.createdBy'] = $userauth['users_id'];
					$wheremy['pro.packageUnique'] = $subScripe['packageUnique'];
					$wheremy['pro.proType'] = 1;
					$wheremy['pro.reportAbuse'] = 1;
					$myproCnt = count($this->products_model->get($wheremy));
					$myPostedCnt = $packageTotalproduct - $myproCnt;
					
					
					/*$filter_data = array(
						'userid' => $userauth['users_id'],
						'proType' => 1,
						'packageUnique' => $subScripe['packageUnique'],
					);
					$myproCnt = count($this->users_model->getmyProductsTot($filter_data));
					$myPostedCnt = $packageTotalproduct - $myproCnt;*/
					
					if(date('Y-m-d h:m') >= date('Y-m-d h:m', strtotime($subScripe['endDate']))){
						$activeSts = 'Expired';
					} else {
						$activeSts = 'Active';
					}
					$response['mySubscripe'][] = array(
						'userPackId' => $subScripe['userPackId'],
						'status' => $subScripe['status'],
						'packID' => $subScripe['packId'],
						'packName' => $package[0]->title,
						'startDate' => date('Y-m-d', strtotime($subScripe['startDate'])),
						'endDate' => date('Y-m-d', strtotime($subScripe['endDate'])),
						'totalProposted' => $myproCnt,
						'remaingProduct' => $myPostedCnt,
						'activeStatus' => $activeSts,
					);
				}
				$response['status'] = '200';
				$response['messages'] = 'My Subscription Listings';
			} else {					
				$response['status'] = '200';
				$response['messages'] = 'No subscription records found';					
			}
			json_output($response['status'],$response);
		} else {
			$response['status'] = '401';
			$response['messages'] = 'Your Authorization details are incorrect.';					
			json_output($response['status'],$response);
		}
	}
		//mysubscriptiondelete:
//raju work 29.03.2019
	public function mysubscriptiondelete($data = array()){ 
        $userauth = array();
		$userauth['users_id']  = $this->input->get_request_header('User-ID', TRUE);
        $userauth['token']     = $this->input->get_request_header('Auth-Key', TRUE);    
  		if(checkForUserSession($userauth) == 1){
  			$post = $this->input->post();
   			$subdata = array(
				'userPackId' => $post['userPackId']
		    );	
		   	$mySubscripe = $this->users_model->deletesubscription($subdata); 
		  
		   	if($mySubscripe == true){
                $response['status'] = '200';
		   		$response['messages'] = 'subscription successfully remove';
		   	} else {
		   		$response['status'] = '400';
		   		$response['messages'] = 'subscription not removed please try again';
		   	}	   
        }else{
         	$response['status'] = '400';
			$response['messages'] = 'No subscription records found';					
		}
		json_output($response['status'],$response);
	}
	//raju work 29.03.2019



	/*User posted products & ads */
	public function myProducts($data = array()){
		$userauth = array();
		$userauth['users_id']  = $this->input->get_request_header('User-ID', TRUE);
        $userauth['token']     = $this->input->get_request_header('Auth-Key', TRUE);
		if(checkForUserSession($userauth) == 1){
			$post = $this->input->post();
			if (isset($post['page'])) {
				$page = $post['page'];
			} else {
				$page = 1;
			}
			$limit = 5;
			$filter_data = array(
				'userid' => $userauth['users_id'],
				'proType' => $post['proType'],
				'start'  => ($page - 1) * $limit,
				'limit'  => $limit
			);
			$response['myProducts'] = array();
			$myProducts = $this->users_model->getmyProducts($filter_data);
			$mySubscripetot = count($this->users_model->getmyProductsTot($filter_data));
			if(!empty($myProducts)) {
				foreach($myProducts as $product){
					$where=array();
					if($post['proType'] == 1){
						if($product['packageId'] != 0){
							$where['pack.packageId'] = $product['packageId'];
							$package = $this->packages_model->get($where);
							$packName = $package[0]->title;
							$type = '';
						} else {
							$packName = 'Free';
							$type = '';
						}
					} else if($post['proType'] == 2){
						$where['ad.adsId'] = $product['adsId'];
						$adsVal = $this->ads_model->get($where);
						$packName = $adsVal[0]->title;
						if($adsVal[0]->displayType == 1){ $type = "Home"; } else if($adsVal[0]->displayType == 2){ $type = "Category"; } else if($adsVal[0]->displayType == 3){ $type = "Both"; }
					}
					if(date('Y-m-d h:m') >= date('Y-m-d h:m', strtotime($product['endDate']))){
						$activeSts = 'Expired';
					} else {
						$activeSts = 'Active';
					}
					
					/*Category Details*/
					$wherecat['cat.parentId'] = 0;
					$wherecat['cat.categoryId'] = $product['procatId'];
					$categorymain = $this->category_model->get($wherecat);
					
					$wheresubcat['cat.parentId'] = $product['procatId'];
					$wheresubcat['cat.categoryId'] = $product['prosubcatId'];
					$categorysub = $this->category_model->get($wheresubcat);
					if(!empty($categorysub)){
						$category = $categorymain[0]->name.' >> '.$categorysub[0]->name;
					} else {
						if(!empty($categorymain))
						{
							$category = $categorymain[0]->name;
						}else{
							$category = "";
						}
					}

					 
					
					/*Locations details*/
					$whereloc['loc.parentId'] = 0;
					$whereloc['loc.locationsId'] = $product['prolocId'];
					$locationmain = $this->locations_model->get($whereloc);
					
					$wheresubloc['loc.parentId'] = $product['prolocId'];
					$wheresubloc['loc.locationsId'] = $product['prosublocId'];
					$locationsub = $this->locations_model->get($wheresubloc);
				
					if(!empty($locationsub)){
						$location = $locationmain[0]->name.' >> '.$locationsub[0]->name;
					} else if(!empty($locationmain[0]->name)){
						$location = $locationmain[0]->name;
					} else {
					    $location = '';
					}
					
					/*Image get only one*/
					$proimg_data = array(
						'productId' => $product['productId'],						
						'limit'  => 1
					);
					$proimage = $this->products_model->getProductimage($proimg_data);
					if($product['proPrice'] != '0.00'){
						$proCurrency = $this->general_model->getCountries($product['proCountryId']);
						$proPrice = $proCurrency[0]->currency.' '.$product['proPrice'];						
					} else {
						$proPrice = '';	
					}
					$response['myProducts'][] = array(
						'proId' => $product['productId'],
						'proName' => $product['proName'],
						'packName' => $packName,
						'adsType' => $type,
						'proPrice' => $proPrice,
						'procategory' => $category,
						'proLocations' => $location,
						'startDate' => date('Y-m-d', strtotime($product['startDate'])),
						'endDate' => date('Y-m-d', strtotime($product['endDate'])),
						'activeStatus' => $activeSts,
						'proImage' => $proimage[0]['imageurl'],
					);
				}
				$response['current_page'] = (int)$page;
				$response['total_page'] = ceil($mySubscripetot / $limit);
				$response['status'] = '200';
				$response['messages'] = 'My Products Listings';
			} else {
				$response['current_page'] = (int)0;
				$response['total_page'] = 0;				
				$response['status'] = '200';
				$response['messages'] = 'No Products records found';					
			}
			json_output($response['status'],$response);
		} else {
			$response['status'] = '401';
			$response['messages'] = 'Your Authorization details are incorrect.';					
			json_output($response['status'],$response);
		}
	}
	
	public function updateToken(){
		$userauth = array();
		$userauth['users_id']  = $this->input->get_request_header('User-ID', TRUE);
        $userauth['token']     = $this->input->get_request_header('Auth-Key', TRUE);
		if(checkForUserSession($userauth) == 1){
			$post = $this->input->post();
			if(isset($post) && !empty($post)){
				$update_data = array(
					'userid' => $userauth['users_id'],
					'deviceToken' => $post['deviceToken'],
				);
				$updateToken = $this->users_model->updateUsertoken($update_data);
				if($updateToken == ""){
					$response['status'] = '401';
					$response['messages'] = 'User device token not updated. Kindly try again!';
				} else if($updateToken){
					$response['status'] = '200';
					$response['messages'] = 'User device token updated successfully.';
				}
			} else {
				$response['status'] = '401';
				$response['messages'] = 'Send the device token values';	
			}
			json_output($response['status'],$response);
		} else {
			$response['status'] = '401';
			$response['messages'] = 'Your Authorization details are incorrect.';					
			json_output($response['status'],$response);
		}
	}
	
	public function updatefireBaseId(){
		$userauth = array();
		$userauth['users_id']  = $this->input->get_request_header('User-ID', TRUE);
        $userauth['token']     = $this->input->get_request_header('Auth-Key', TRUE);
		if(checkForUserSession($userauth) == 1){
			$post = $this->input->post();
			if(isset($post) && !empty($post)){
				$update_data = array(
					'userid' => $userauth['users_id'],
					'firebaseId' => $post['firebaseId'],
				);
				$updateToken = $this->users_model->updatefireBase($update_data);
				if($updateToken == ""){
					$response['status'] = '401';
					$response['messages'] = 'User firebase ID not updated. Kindly try again!';
				} else if($updateToken){
					$response['status'] = '200';
					$response['messages'] = 'User firebase ID updated successfully.';
				}
			} else {
				$response['status'] = '401';
				$response['messages'] = 'Send the firebase ID values';	
			}
			json_output($response['status'],$response);
		} else {
			$response['status'] = '401';
			$response['messages'] = 'Your Authorization details are incorrect.';					
			json_output($response['status'],$response);
		}
	}
	
	public function getfireBaseChat(){
		$userauth = array();
		$userauth['users_id']  = $this->input->get_request_header('User-ID', TRUE);
        $userauth['token']     = $this->input->get_request_header('Auth-Key', TRUE);
		if(checkForUserSession($userauth) == 1){
			$post = $this->input->post();
			if(isset($post) && !empty($post)){
				$update_data = array(
					'firebaseId' => $post['firebaseIds'],					
				);
				$fireChat = $this->users_model->getfireBaseChat($update_data);
				$listUserarray = array();
				if(!empty($fireChat)){
					foreach($fireChat as $userList){
						$listUserarray[] = array(							
							'userId' => $userList['userid'],
							'userName' => $userList['name'],
							'userImg' => $userList['profileimg'],
							'userFirebaseId' => $userList['firebaseId'],
						);					
					}
					$response['userList'] = $listUserarray;					
					$response['status'] = '200';			
					$response['messages'] = 'Firebase Listing successfully!';
					json_output($response['status'],$response);
				} else {	
					$response['userList'] = $listUserarray;			
					$response['status'] = '200';			
					$response['messages'] = 'No results found!';
					json_output($response['status'],$response);
				}
				
			} else {
				$response['status'] = '401';
				$response['messages'] = 'Send the firebase ID values';	
			}
			json_output($response['status'],$response);
		} else {
			$response['status'] = '401';
			$response['messages'] = 'Your Authorization details are incorrect.';					
			json_output($response['status'],$response);
		}
	}
}