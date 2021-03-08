<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$config = array(
        "add_admin_user" => array( 
                                array(
                                    'field' =>'first_name',
                                    'label' => 'First Name',
                                    'rules' => 'required'
                                    ),
                                array(
                                    'field' =>'last_name',
                                    'label' => 'Last Name',
                                    'rules' => 'required'
                                    ),
                                array(
                                    'field' =>'email_address',
                                    'label' => 'Email Address',
                                    'rules' => 'required|valid_email|is_unique[admin_users.email_address]'
                                ),
                                array(
                                    'field' =>'phone',
                                    'label' => 'Phone',
                                    'rules' => 'required'
                                ),
                                array(
                                    'field' =>'username',
                                    'label' => 'User Name',
                                    'rules' => 'required|is_unique[admin_users.username]'
                                    ),
                                array(
                                    'field' =>'password',
                                    'label' => 'Password',
                                    'rules' => 'required|matches[ConfirmPassword]'
                                ),
                                array(
                                    'field' =>'ConfirmPassword',
                                    'label' => 'ConfirmPassword',
                                    'rules' => 'required'
                                )  
                            ),
        "edit_admin_user" =>array( 
                                array(
                                    'field' =>'first_name',
                                    'label' => 'First Name',
                                    'rules' => 'required'
                                    ),
                                array(
                                    'field' =>'last_name',
                                    'label' => 'Last Name',
                                    'rules' => 'required'
                                    ),
                                array(
                                    'field' =>'email_address',
                                    'label' => 'Email Address',
                                    'rules' => 'required|valid_email'
                                ),
                                array(
                                    'field' =>'phone',
                                    'label' => 'Phone',
                                    'rules' => 'required'
                                ),
                                array(
                                    'field' =>'password',
                                    'label' => 'Password',
                                    'rules' => 'required|matches[ConfirmPassword]'
                                ),
                                array(
                                    'field' =>'ConfirmPassword',
                                    'label' => 'ConfirmPassword',
                                    'rules' => 'required'
                                )  
                            )      
        );
        
?>