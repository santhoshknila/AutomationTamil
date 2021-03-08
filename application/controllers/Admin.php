<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {
    function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->model("admin_model");		
        $this->load->model("users_model");		
        $this->load->model("products_model");		
        $this->load->model("events_model");		
        $this->load->model("polls_model");		
        $this->load->library('session');
        $this->load->library('email');
    }
    public function index(){
        if(checkForAdminSession(1, true))redirect('admin/login');
        $data = array();
        $data['title'] = "Admin Login";
        $data['sessval'] = $this->session->userdata();
        if(empty($data['sessval']['user_role']) && $data['sessval']['user_role'] == ''){ redirect('admin/login'); }
    }
    public function dashboard(){
        if(checkForAdminSession(1, true))redirect('admin/login');
        $data = array();
        $data["title"] = "Dashboard";
		
		/*User Counts*/
		$whereusers['us.userrole'] = 'user';
		$allusers = $this->users_model->get($whereusers);
		$data["users"] = count($allusers);
		
		/*Total products and ads*/
		$whereproducts['pro.status'] = 1;
		$allproducts = $this->products_model->get($whereproducts);
		$data["products"] = count($allproducts);
		
		/*Total Active Events*/
		$whereevt['event.status'] = 1;
		$whereevt['event.startDate <='] = date("Y-m-d");
		$whereevt['event.endDate >='] = date("Y-m-d");	
		//$whereevt['poll.startTime <='] = date("H:i");
		//$whereevt['poll.endTime >='] = date("H:i");
		$data["events"] = count($this->events_model->get($whereevt));
		
		/*Total Active Polls*/
		$wherepoll['poll.status'] = 1;
		$wherepoll['poll.startDate <='] = date("Y-m-d");
		$wherepoll['poll.endDate >='] = date("Y-m-d");	
		//$wherepoll['poll.startTime <='] = date("H:i");
		//$wherepoll['poll.endTime >='] = date("H:i");
		$data["polls"] = count($this->polls_model->get($wherepoll));		
		
        load_default_template('admin/dashboard',$data,$this);
        echo $this->template->render("", true);
    }
    /*Admin Login */
    public function login(){		
		if(($this->session->userdata("userrole") == 'admin')){
			redirect('admin/dashboard');
		} else {			
			$data = array();
			$data["title"] = "Admin Login";
			$post = $this->input->post();
			if(isset($post) && !empty($post)){
				if($this->admin_model->check_AdminLogin($post)){
					redirect('admin/dashboard');
				} else {
					$this->session->set_flashdata('message', '<div class="error_msg">Please enter correct Emailaddress and Password.</div>');
				}
			}
			load_admin_login_template('',$data,$this);
			$this->template->render();
		}
    }
	
	public function changepass(){
		if(checkForAdminSession(1, true))redirect('admin/login');
		$data = array();
		$data['title'] = "Change Password";
		load_default_template('admin/changepass',$data,$this);
		echo $this->template->render("", true);
	}
	
	public function updatepass(){
		$post = $this->input->post();
		if(isset($post) && !empty($post)){
			$result = $this->admin_model->save_update_account($post);			
			if($result){
				$this->session->set_flashdata('message', '<div class="success_msg">Details has been Updated successfully.</div>');
			} else {
				$this->session->set_flashdata('message', '<div class="error_msg">Details has been not updated.</div>');
			}
		} else {
			$this->session->set_flashdata('message', '<div class="error_msg">Post value not Send. Try Again!</div>');
			redirect('admin/changepass');
		}
		redirect('admin/changepass');
	}
	
	
    /*Admin Forgot password */
    /*public function forgot(){
		$data = array();
		$data['title'] = "Forgot Password";
		load_admin_forgot_template('',$data,$this);
		$this->template->render();
    }
    public function forgotPass(){
		$data = array();
		$data['title'] = "Forgot Password";
		$post = $this->input->post();
		if(isset($post) && !empty($post)){
			$forgotpass_res = $this->admin_model->check_AdminForgotpass($post);
			if(isset($forgotpass_res) && !empty($forgotpass_res)){
				$from = 'info@bonfring.com';
				$to = $post['emailid'];
				$name = 'BONFRING';
				$subject = 'BONFRING Forget Password';
				$content = '<table cellpadding="0" cellspacing="0" width="600" style="background:#fff; font-family: sans-serif;border:1px solid #ddd;">
						<tr>
								<td align="center" style="padding:10px;border-bottom:1px solid #eeeeee;">
										<img src="'.base_url().'/skin/default/images/logo2.png" alt="Logo"/>
								</td>
						</tr>
						<tr><td align="center" bgcolor="#2171c6" style="padding:10px; color:#fff; ">Bonfring Forget Password Details </td></tr>
						<tr><td>
								<table cellpadding="5" cellspacing="5" border="0" style="font-family:Arial, Helvetica, sans-serif">
								 <tr>
										<td>
												<p>Hi '.$forgotpass_res['name'].',</p>
												<p>Your password has been updated successfully. Kindly login this password!</p>
												<p><b>Password: </b>'.$forgotpass_res['new_password'].'</p>
												<p style="font-size:14px;line-height:20px;">If you have any problems, please contact admin at <a href="mailto:info@bonfring.com"> Clickhere </a></p>
										</td>
								</tr>
								</table>
						</td></tr>
						<tr>
								<td style="background:#eee;font-size:13px;padding:5px;" align="center">
										Copyrights © 2018 <a href="http://bonfring.local/">Bonfring CRM</a> All Rights Reserved.
								</td>
						</tr>
				</table>';
				$mail = sendMail($from, $name, $to, $subject, $content);
				$this->session->set_flashdata('message', '<div class="success_msg">Password reset successfully. Kindly check your mail.</div>');
				redirect('admin/login');
			} else {
				$data["errors"][] = "Please enter the correct email address";
				load_admin_forgot_template('',$data,$this);
				$this->template->render();
			}
		}
    }    
	
	public function updateprofile(){
		$data["title"] = "Update Profile";
		$data['enquiry_data'] = $this->admin_model->get_enquiry_data();
		$data['leads_data'] = $this->admin_model->get_leads_data();
		$data['customer_data'] = $this->admin_model->get_customer_data();
		$data['executive_data'] = $this->admin_model->get_executive_data();
		$data['payment_data'] = $this->admin_model->get_payment_data();
		$data['paid_data'] = $this->admin_model->get_paid_data();
		$data['expense'] = $this->admin_model->get_account_expense();
		$data['invoice'] = $this->admin_model->get_invoice_amt();
		$data['enquiry'] = $this->admin_model->get_enquiry_list();
		$data['leads'] = $this->admin_model->get_leads_list();
		$data['executives'] = $this->admin_model->getexecutiveinfo();
		$select=array();
        $where['Ad.id'] = 1;
        $orderby = array();
        $data["edit_admin"] = $this->admin_model->get($where,$orderby,$select);
        load_default_template('admin/update-profile',$data,$this);
        echo $this->template->render("", true);
	}
	
	public function save(){
		$post = $this->input->post();
		if(!empty($post)){
			$config = array(
				'upload_path' => "./uploads/",
				'allowed_types' => "jpg|png|jpeg|doc|pdf",
				'overwrite' => TRUE,
				'max_size' => "2048000",
			);
			$this->load->library('upload', $config);
			
			if (!$this->upload->do_upload('profilephoto')){
				$error = array('error' => $this->upload->display_errors());
				if($post['admin']['profile_photo'] ==''){
					$post['admin']['profile_photo'] = '';
				}
			} else {
				$data = array('upload_data' => $this->upload->data());
				$post['admin']['profile_photo'] = $data['upload_data']['file_name'];
			}
			$data = $post['admin'];
			$updated_id = $this->admin_model->updateAdminUser($data, $where = array("id" =>1));
			if($updated_id){
				$this->session->set_flashdata('message', '<div class="success_msg">Profile has been updated Successfully!</div>');
			?>
				<script>
					alert('Profile has been updated Successfully!');
					//window.location.href="
				</script>
			<?php
				redirect('admin/updateprofile');
			}	
		}		
	}*/
    public function logout(){
        $this->session->sess_destroy();
        redirect('admin/login');
    }
}
