<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
function load_default_template($view,$data,&$CI) {
    $CI->load->library("Template");
    /** CSS Files **/	
    $CI->template->add_css("skin/default/css/style.css");
    $CI->template->add_css("skin/default/css/font-awesome/css/font-awesome.css");
	$CI->template->add_css("skin/default/css/Ionicons/css/ionicons.css");
	$CI->template->add_css("skin/default/css/perfect-scrollbar/css/perfect-scrollbar.css");
	$CI->template->add_css("skin/default/css/jquery-switchbutton/jquery.switchButton.css");
	$CI->template->add_css("skin/default/css/rickshaw/rickshaw.min.css");
	$CI->template->add_css("skin/default/css/chartist/chartist.css");
	$CI->template->add_css("skin/default/css/jquery.datetimepicker.css");
	$CI->template->add_css("skin/default/css/emojionearea.min.css");
	
    /** JS Files **/
    $CI->template->add_js("skin/default/js/jquery.js");
    $CI->template->add_js("skin/default/js/popper.js");
    $CI->template->add_js("skin/default/js/bootstrap.js");
    $CI->template->add_js("skin/default/js/moment.js");
    $CI->template->add_js("skin/default/js/jquery-ui.js");
    $CI->template->add_js("skin/default/js/jquery.switchButton.js");
    $CI->template->add_js("skin/default/js/jquery.peity.js");
	$CI->template->add_js("skin/default/css/chartist/chartist.js");
    $CI->template->add_js("skin/default/css/perfect-scrollbar/js/perfect-scrollbar.jquery.js");
    $CI->template->add_js("skin/default/js/jquery.sparkline.min.js");
    $CI->template->add_js("skin/default/js/d3.js");
    $CI->template->add_js("skin/default/css/rickshaw/rickshaw.js");
	$CI->template->add_js("skin/default/js/bracket.js");
	$CI->template->add_js("skin/default/js/ResizeSensor.js");
	$CI->template->add_js("skin/default/js/dashboard.js");
	$CI->template->add_js("skin/default/js/jquery.validate.js");
    $CI->template->add_js("skin/default/js/form-validation.js");
	$CI->template->add_js("skin/default/js/parsley.js");	
	$CI->template->add_js("skin/default/js/jquery.datetimepicker.full.js");	
	$CI->template->add_js("skin/default/js/jscolor.js");	
	$CI->template->add_js("skin/default/js/emojionearea.js");	
    
    $CI->template->write_view("header","header",$data);
    $CI->template->write_view("menu","menu",$data);
    $CI->template->write_view("content",$view,$data);
    $CI->template->write_view("footer","footer",$data);
}
function load_admin_login_template($view,$data,&$CI) {
    $CI->load->library("Template");
    $CI->template->add_css("skin/default/css/style.css");
    $CI->template->add_css("skin/default/css/font-awesome/css/font-awesome.css");
    $CI->template->add_css("skin/default/css/Ionicons/css/ionicons.css");
    /** JS Files **/
	
    $CI->template->add_js("skin/default/js/jquery.js");
	$CI->template->add_js("skin/default/js/popper.js");
	$CI->template->add_js("skin/default/js/bootstrap.js" );
    $CI->template->add_js("skin/default/js/jquery.validate.js");
    $CI->template->add_js("skin/default/js/form-validation.js");
    $CI->template->add_js("skin/default/js/parsley.js");
	
    $CI->template->set_master_template("admin/login");
    $CI->template->write_view("content", "admin/login", $data);
}
function load_admin_forgot_template($view,$data,&$CI) {
    $CI->load->library("Template");
     $CI->template->add_css("skin/default/css/style.css");
    $CI->template->add_css("skin/default/css/font-awesome/css/font-awesome.css");
    $CI->template->add_css("skin/default/css/Ionicons/css/ionicons.css");
    /** JS Files **/
	
    $CI->template->add_js("skin/default/js/jquery.js");
	$CI->template->add_js("skin/default/js/popper.js");
	$CI->template->add_js("skin/default/js/bootstrap.js" );
    $CI->template->add_js("skin/default/js/jquery.validate.js");
    $CI->template->add_js("skin/default/js/form-validation.js");
    $CI->template->set_master_template("admin/forgot");
    $CI->template->write_view("content", "admin/forgot", $data);
}

function load_resetpassword_template($view,$data,&$CI) {
    $CI->load->library("Template");
    $CI->template->add_css("skin/default/css/style.css");
    $CI->template->add_css("skin/default/css/font-awesome/css/font-awesome.css");
    $CI->template->add_css("skin/default/css/Ionicons/css/ionicons.css");
    /** JS Files **/
	
    $CI->template->add_js("skin/default/js/jquery.js");
	$CI->template->add_js("skin/default/js/popper.js");
	$CI->template->add_js("skin/default/js/bootstrap.js" );
    $CI->template->add_js("skin/default/js/jquery.validate.js");
    $CI->template->add_js("skin/default/js/form-validation.js");
    $CI->template->add_js("skin/default/js/parsley.js");
	
    $CI->template->set_master_template("users/reset");
    $CI->template->write_view("content", "users/reset", $data);
}

function load_paysummary_template($view,$data,&$CI) {
    $CI->load->library("Template");
    $CI->template->add_css("skin/default/css/style.css");
    $CI->template->add_css("skin/default/css/font-awesome/css/font-awesome.css");
    $CI->template->add_css("skin/default/css/Ionicons/css/ionicons.css");
    /** JS Files **/
	
    $CI->template->add_js("skin/default/js/jquery.js");
	$CI->template->add_js("skin/default/js/popper.js");
	$CI->template->add_js("skin/default/js/bootstrap.js" );
    $CI->template->add_js("skin/default/js/jquery.validate.js");
    $CI->template->add_js("skin/default/js/form-validation.js");
    $CI->template->add_js("skin/default/js/parsley.js");
	
    $CI->template->set_master_template("products/paySummary");
    $CI->template->write_view("content", "products/paySummary", $data);
}

function load_paysuccess_template($view,$data,&$CI) {
    $CI->load->library("Template");
    $CI->template->add_css("skin/default/css/style.css");
    $CI->template->add_css("skin/default/css/font-awesome/css/font-awesome.css");
    $CI->template->add_css("skin/default/css/Ionicons/css/ionicons.css");
    /** JS Files **/
	
    $CI->template->add_js("skin/default/js/jquery.js");
	$CI->template->add_js("skin/default/js/popper.js");
	$CI->template->add_js("skin/default/js/bootstrap.js" );
    $CI->template->add_js("skin/default/js/jquery.validate.js");
    $CI->template->add_js("skin/default/js/form-validation.js");
    $CI->template->add_js("skin/default/js/parsley.js");
	
    $CI->template->set_master_template("products/paySuccess");
    $CI->template->write_view("content", "products/paySuccess", $data);
}

function load_paycancel_template($view,$data,&$CI) {
    $CI->load->library("Template");
    $CI->template->add_css("skin/default/css/style.css");
    $CI->template->add_css("skin/default/css/font-awesome/css/font-awesome.css");
    $CI->template->add_css("skin/default/css/Ionicons/css/ionicons.css");
    /** JS Files **/
	
    $CI->template->add_js("skin/default/js/jquery.js");
	$CI->template->add_js("skin/default/js/popper.js");
	$CI->template->add_js("skin/default/js/bootstrap.js" );
    $CI->template->add_js("skin/default/js/jquery.validate.js");
    $CI->template->add_js("skin/default/js/form-validation.js");
    $CI->template->add_js("skin/default/js/parsley.js");
	
    $CI->template->set_master_template("products/payCancel");
    $CI->template->write_view("content", "products/payCancel", $data);
}

function load_paynotify_template($view,$data,&$CI) {
    $CI->load->library("Template");
    $CI->template->add_css("skin/default/css/style.css");
    $CI->template->add_css("skin/default/css/font-awesome/css/font-awesome.css");
    $CI->template->add_css("skin/default/css/Ionicons/css/ionicons.css");
    /** JS Files **/
	
    $CI->template->add_js("skin/default/js/jquery.js");
	$CI->template->add_js("skin/default/js/popper.js");
	$CI->template->add_js("skin/default/js/bootstrap.js" );
    $CI->template->add_js("skin/default/js/jquery.validate.js");
    $CI->template->add_js("skin/default/js/form-validation.js");
    $CI->template->add_js("skin/default/js/parsley.js");
	
    $CI->template->set_master_template("products/payNotify");
    $CI->template->write_view("content", "products/payNotify", $data);
}


function default_theme_skin_path($path) {
    return base_url("skin/default/".$path);
}
?>