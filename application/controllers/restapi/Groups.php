<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Groups extends CI_Controller {
	private $error = array();
	function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->model("groups_model");		
        $this->load->model("users_model");		
        $this->load->model("auth_model");		
        $this->load->model("newsfeeds_model");
        $this->load->model("notifications_model");
        $this->load->library('session');
        $this->load->library('email');
		$this->load->library('form_validation');
		$this->load->library('phpass');
		$this->load->helper('date');
    }
	
	public function createGroup(){
		$userauth = array();
		$userauth['users_id']  = $this->input->get_request_header('User-ID', TRUE);
        $userauth['token']     = $this->input->get_request_header('Auth-Key', TRUE);
		if(checkForUserSession($userauth) == 1){
			$post = $this->input->post();
			if(isset($post) && !empty($post)){
				if(empty($this->input->post('groupName'))){
					$message = "Group Name is required";
				} else if(empty($this->input->post('groupMember'))){
					$message = "Add atleast one members";
				} else if(empty($this->input->post('groupPrivacyId'))){
					$message = "Group PrivacyId is required";
				}
				
				if(empty($message)){
					if(isset($_FILES['groupImage']['name']) && !empty($_FILES['groupImage']['name'])){					
						$config = array(
							'upload_path' => "./uploads/groupImage/",
							'allowed_types' => "jpg|png|jpeg|gif",						
						);					
						$_FILES['userfile']['name']     = uniqid().'_'.time().'_'.$_FILES['groupImage']['name'];
						$_FILES['userfile']['type']     = $_FILES['groupImage']['type'];
						$_FILES['userfile']['tmp_name'] = $_FILES['groupImage']['tmp_name'];
						$_FILES['userfile']['error']    = $_FILES['groupImage']['error'];
						$_FILES['userfile']['size']     = $_FILES['groupImage']['size']; 
	   
						$this->load->library('upload', $config);
						if (!$this->upload->do_upload('userfile')){
							$error = array('error' => $this->upload->display_errors());
						} else {
							$fileData = $this->upload->data();							
							$post['groupImage'] = $fileData['file_name'];
							
							$file = $fileData['file_name'];
							$path = $fileData['full_path'];
							$config_resize['image_library'] = 'gd2';  
							$config_resize['source_image'] = $path;
							$config_resize['create_thumb'] = false;
							$config_resize['maintain_ratio'] = TRUE;
							$config_resize['width'] = 100;
							$config_resize['height'] = 100;
							$config_resize['new_image'] = './uploads/groupImage/thumb/'.$file;
							$this->load->library('image_lib',$config_resize);
							$this->image_lib->clear();
							$this->image_lib->initialize($config_resize);
							$this->image_lib->resize();
						}
					} else {
						$post['groupImage'] = "";
					}
					$post['createdBy'] = $post['updatedBy'] = $userauth['users_id'];				
					$result = $this->groups_model->save_createGroup($post);				
					
					if($result == ""){
						$response['status'] = '200';
						$response['messages'] = 'Your group not created. Kindly try again!';
					} else if($result == 3){
						$response['status'] = '401';
						$response['messages'] = 'Your group name already exist. Try to some other name.';
					} else if($result){
						$groupmemlist = $this->groups_model->getgroupdeleteMember($result);
						if(!empty($groupmemlist)){
							$userdetailsSend = $this->users_model->getUseriddetails($userauth['users_id']);
							$groupDetails = $this->groups_model->getgroupDetails($result, $userauth['users_id']);
							
							foreach($groupmemlist as $gpmemList){
								if($gpmemList['receiveRequestId'] != $userauth['users_id']){
									$notify_data = array(
										array(
											'fromUserId'    => $userauth['users_id'],
											'toUserId'      => $gpmemList['receiveRequestId'],
											'notifyType'	=> 'groupCreate',
											'notifyId'		=> $result,
											'notifyReason'	=> '<b>'.$userdetailsSend->firstName.' '.$userdetailsSend->surName.'<b> added you to the <b>'.$groupDetails->groupName.'<b> group.',
											'createdBy'		=> $userauth['users_id'],							
											'updatedBy'		=> $userauth['users_id'],
										)
									);
									$receiveRequest = $this->notifications_model->notificationsLog($notify_data);
									$userdetails = $this->users_model->getUseriddetails($gpmemList['receiveRequestId']);
									if($userdetails->deviceType == 1){
										$tokensAndroid[] = $userdetails->deviceToken;
									} else if($userdetails->deviceType == 2){
										$tokensIOS[] = $userdetails->deviceToken;
									}
								}
							}
							
							$notify_data1 = array(								
								array(
									'fromUserId'    => $userauth['users_id'],
									'toUserId'      => $userauth['users_id'],
									'notifyType'	=> 'groupCreate',
									'notifyId'		=> $result,
									'notifyReason'	=> 'You have created <b>'.$groupDetails->groupName.'<b> group.',
									'createdBy'		=> $userauth['users_id'],							
									'updatedBy'		=> $userauth['users_id'],
								)
							);
							$receiveRequest = $this->notifications_model->notificationsLog($notify_data1);
							
							$subject = "Group Notifications on Tamil Ethos";			
							$message = $userdetailsSend->firstName.' '.$userdetailsSend->surName.' added you to the '.$groupDetails->groupName.' group.';
							$created_date = date('Y-m-d h:i');
							$senddetailsAndroid = array(
								"title"=> $subject,
								"message"=>$message,
								"notifyType" => "groupCreate",
								"notifyId" => $result,
								"timestamp" => $created_date
							);
							$senddetailsIOS = array(
								"attachment" => '',
								"media_type" => '',
								"notifyType" => "groupCreate",
								"notifyId" => $result
							);
							$messageIOS = array(
								'title' => $subject,
								'body' => $message,
								'sound' => 'default',
								"notifyType" => "groupCreate",
								"notifyId" => $result
							);
							
							if(!empty($tokensAndroid)){
								$message_statusan = send_notification($tokensAndroid, $senddetailsAndroid);
							} 
							
							if(!empty($tokensIOS)){
								$message_status = send_notificationIOS($tokensIOS, $senddetailsIOS, $messageIOS);
							}
						}
						$response['status'] = '200';
						$response['messages'] = 'Your group has been created successfully';
					}
					json_output($response['status'],$response);
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
	
	public function editGroupdetails(){
		$userauth = array();
		$userauth['users_id']  = $this->input->get_request_header('User-ID', TRUE);
        $userauth['token']     = $this->input->get_request_header('Auth-Key', TRUE);
		if(checkForUserSession($userauth) == 1){
			$post = $this->input->post();
			if(isset($post) && !empty($post)){
				if(empty($this->input->post('groupId'))){
					$message = "Group ID is required";
				} else if(empty($this->input->post('groupName'))){
					$message = "Group Name is required";
				} else if(empty($this->input->post('groupPrivacyId'))){
					$message = "Group PrivacyId is required";
				}
				
				if(empty($message)){
					if(isset($_FILES['groupImage']['name']) && !empty($_FILES['groupImage']['name'])){					
						$config = array(
							'upload_path' => "./uploads/groupImage/",
							'allowed_types' => "jpg|png|jpeg|gif",						
						);					
						$_FILES['userfile']['name']     = uniqid().'_'.time().'_'.$_FILES['groupImage']['name'];
						$_FILES['userfile']['type']     = $_FILES['groupImage']['type'];
						$_FILES['userfile']['tmp_name'] = $_FILES['groupImage']['tmp_name'];
						$_FILES['userfile']['error']    = $_FILES['groupImage']['error'];
						$_FILES['userfile']['size']     = $_FILES['groupImage']['size']; 
	   
						$this->load->library('upload', $config);
						if (!$this->upload->do_upload('userfile')){
							$error = array('error' => $this->upload->display_errors());
						} else {
							$fileData = $this->upload->data();							
							$post['groupImage'] = $fileData['file_name'];
							
							$file = $fileData['file_name'];
							$path = $fileData['full_path'];
							$config_resize['image_library'] = 'gd2';  
							$config_resize['source_image'] = $path;
							$config_resize['create_thumb'] = false;
							$config_resize['maintain_ratio'] = TRUE;
							$config_resize['width'] = 250;
							$config_resize['height'] = 250;
							$config_resize['new_image'] = './uploads/groupImage/thumb/'.$file;
							$this->load->library('image_lib',$config_resize);
							$this->image_lib->clear();
							$this->image_lib->initialize($config_resize);
							$this->image_lib->resize();
						}
					} else {
						$post['groupImage'] = "";
					}
					$post['createdBy'] = $post['updatedBy'] = $userauth['users_id'];				
					$result = $this->groups_model->save_editGroup($post);
					$response['status'] = '200';
					if($result == ""){
						$response['messages'] = 'Your group not updated. Kindly try again!';
					} else if($result == 3){
						$response['messages'] = 'Your group name already exist. Try to some other name.';
					} else if($result == $post['groupId']){												
						$response['messages'] = 'Your group has been updated successfully.';
					} else {
						$response['messages'] = 'Your group not updated. Kindly try again!';
					}
					json_output($response['status'],$response);
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
	
	public function groupListing(){
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
			$limit = 12;
			
			if(!empty($post['orderVal'])){
				$orderVal = $post['orderVal'];  
			} else {
				$orderVal = ''; 
			}
			
			$myGroup = array(
				'userId' => $userauth['users_id'],				
				'range' => $orderVal,
				'start'  => ($page - 1) * $limit,
				'limit'  => $limit
			);
			
			$grouparray = array();
			$grouplist = $this->groups_model->getgroupListing($myGroup);			
			$totalgroups = count($this->groups_model->getgrouptotListing($myGroup));
			if(!empty($grouplist)){
				foreach($grouplist as $gpList){	
					$groupMemberCnt = count($this->groups_model->getMembercnt($gpList['groupId']));
					$grouparray[] = array(
						'groupID' => $gpList['groupId'],
						'groupName' => $gpList['groupName'],
						'groupImage' => $gpList['groupImage'],
						'groupMembers' => (string) $groupMemberCnt, // Need group members count.
						'groupDescription' => $gpList['groupDescription'],						
						'groupPrivacyId' => $gpList['groupPrivacyId'],						
					);
				}
			}
			$response['groupList'] = $grouparray;
			$response['totalGroupcnt'] = $totalgroups;
			$response['orderVal'] = $orderVal;
			$response['current_page'] = (int)$page;
			$response['total_page'] = ceil($totalgroups / $limit);
			$response['status'] = '200';			
			$response['messages'] = 'Group Listing successfully!';			
			json_output($response['status'],$response);
		} else {			
			$response['orderVal'] = "";
			$response['current_page'] = (int)0;
			$response['total_page'] = 0;
			$response['status'] = '401';
			$response['messages'] = 'Your Authorization details are incorrect.';					
			json_output($response['status'],$response);
		}
	}
	
	/*Group listing with search val*/
	public function groupListingSearch(){
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
			
			if(!empty($post['searchVal'])){
				$searchVal = $post['searchVal'];  
			} else {
				$searchVal = ''; 
			}
			
			$myGroup = array(
				'userId' => $userauth['users_id'],
				'search' => $searchVal,				
				'start'  => ($page - 1) * $limit,
				'limit'  => $limit
			);
			
			$grouparray = array();
			$grouplist = $this->groups_model->getgroupListingSearch($myGroup);		
			$totalgroups = count($this->groups_model->getgrouptotListingSearch($myGroup));
			if(!empty($grouplist)){
				foreach($grouplist as $gpList){	
					$groupMemberCnt = count($this->groups_model->getMembercnt($gpList['groupId']));
					$grouparray[] = array(
						'groupID' => $gpList['groupId'],
						'groupName' => $gpList['groupName'],
						'groupImage' => $gpList['groupImage'],
						'groupMembers' => (string) $groupMemberCnt, // Need group members count.
						'groupDescription' => $gpList['groupDescription'],						
						'groupPrivacyId' => $gpList['groupPrivacyId'],						
					);
				}
			}
			$response['groupList'] = $grouparray;
			$response['totalGroupcnt'] = $totalgroups;
			$response['searchVal'] = $searchVal;			
			$response['current_page'] = (int)$page;
			$response['total_page'] = ceil($totalgroups / $limit);
			$response['status'] = '200';			
			$response['messages'] = 'Group Listing successfully!';			
			json_output($response['status'],$response);
		} else {
			$response['searchVal'] = "";			
			$response['current_page'] = (int)0;
			$response['total_page'] = 0;
			$response['status'] = '401';
			$response['messages'] = 'Your Authorization details are incorrect.';
			json_output($response['status'],$response);
		}
	}
	
	/*Group Member listing*/
	public function groupMemberListing(){
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
			$limit = 12;
			
			$myMember = array(
				'userId' => $userauth['users_id'],
				'groupId' => $post['groupId'],
				'start'  => ($page - 1) * $limit,
				'limit'  => $limit
			);
			
			$groupmemarray = array();
			$groupmemlist = $this->groups_model->getgroupMemberListing($myMember);			
			$totalmemgroups = count($this->groups_model->getgroupMembertotListing($myMember));
			if(!empty($groupmemlist)){
				foreach($groupmemlist as $gpmemList){
					$userdetails = $this->users_model->getUseriddetails($gpmemList['receiveRequestId']);					
					$groupmemarray[] = array(
						'userId' => $userdetails->userid, 
						'userName' => $userdetails->firstName.' '.$userdetails->surName, 
						'userImg' => $userdetails->profileimg, 
						'groupMemberId' => $gpmemList['groupMemberId'],
						'groupMemisAdmin' => $gpmemList['groupMemisAdmin'],
						'groupMemStatus' => $gpmemList['status'],
					);
				}
			}
			$response['groupMemList'] = $groupmemarray;
			$response['totalGroupcnt'] = $totalmemgroups;			
			$response['current_page'] = (int)$page;
			$response['total_page'] = ceil($totalmemgroups / $limit);
			$response['status'] = '200';			
			$response['messages'] = 'Group Member Listing successfully!';			
			json_output($response['status'],$response);
		} else {	
			$response['groupMemList'] = '';
			$response['totalGroupcnt'] = '';	
			$response['current_page'] = (int)0;
			$response['total_page'] = 0;
			$response['status'] = '401';
			$response['messages'] = 'Your Authorization details are incorrect.';					
			json_output($response['status'],$response);
		}
	}	
	
	/*Group details view*/
	public function groupDetailsview(){
		$userauth = array();
		$userauth['users_id']  = $this->input->get_request_header('User-ID', TRUE);
        $userauth['token']     = $this->input->get_request_header('Auth-Key', TRUE);		
		if(checkForUserSession($userauth) == 1){
			$post = $this->input->post();
			$groupgetDetails = $this->groups_model->getgroupDetails($post['groupId'], $userauth['users_id']);
			$groupMemIsAdmin = $this->groups_model->getMemIsAdmin($post['groupId'], $userauth['users_id']);
			if(!empty($groupMemIsAdmin)){
				$groupMemId = $groupMemIsAdmin[0]->groupMemberId;
				$groupMemIsAdminval = $groupMemIsAdmin[0]->groupMemisAdmin;
				$groupMemStatus = $groupMemIsAdmin[0]->status;
				if($groupMemIsAdmin[0]->status == 1){
					$groupMember = "1";		
				} else {
					$groupMember = "0";		
				}
			} else {
				$groupMemId = '';
				$groupMemIsAdminval = '0';
				$groupMemStatus = '0';
				$groupMember = '0';				
			}
			$groupMemberCnt = count($this->groups_model->getMembercnt($post['groupId']));
			$groupdetails = array();			
			if(isset($groupgetDetails)){
				if($groupgetDetails->groupPrivacyId == 1){
					$groupPrivacyName = 'Public';
				} else if($groupgetDetails->groupPrivacyId == 2){
					$groupPrivacyName = 'Closed';
				} else if($groupgetDetails->groupPrivacyId == 3){
					$groupPrivacyName = 'Secret';
				}
				$reportAbusests = $this->groups_model->getuserreportAbusests($userauth['users_id'], $post['groupId']);
				$groupdetails = array(
					'groupID' => $groupgetDetails->groupId,
					'groupName' => $groupgetDetails->groupName,
					'groupImage' => $groupgetDetails->groupImage,
					'groupDescription' => $groupgetDetails->groupDescription,
					'groupPrivacyId' => (string) $groupgetDetails->groupPrivacyId,
					'groupPrivacyName' => $groupPrivacyName,
					'groupMembers' => (string) $groupMemberCnt, // Need group members count.
					'groupCreatedby' => $groupgetDetails->createdBy,
					'groupMemIsAdmin' => $groupMemIsAdminval, //isadmin 1 , notadmin 0 
					'groupMember' => $groupMember, //isMember 1, notmember 0 
					'groupMemStatus' => $groupMemStatus, //sent 2, Accept 1,  
					'groupMemId' => $groupMemId,
					'groupReportabuse' => (string)$reportAbusests,
				);				
				$response['groupDetails'] = $groupdetails;
				$response['status'] = '200';
				$response['messages'] = 'Group details Listing successfully!';
				json_output($response['status'],$response);
			} else {
				$response['groupDetails'] ='';
				$response['status'] = '200';
				$response['messages'] = 'Group ID not matched!';
				json_output($response['status'],$response);
			}
		} else {
			$response['status'] = '401';
			$response['messages'] = 'Your Authorization details are incorrect.';					
			json_output($response['status'],$response);
		}
	}
	
	/*Group ID based show the post details*/
	public function groupPostlisting(){
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
			$limit = 3;
			
			if(!empty($post['groupId'])){
				$groupIdVal = $post['groupId'];  
			} else {
				$groupIdVal = ''; 
			}
			
			$filter_data = array(
				'userId' => $userauth['users_id'],
				//'userId' => 46,
				'groupId' => $groupIdVal,
				'start'  => ($page - 1) * $limit,
				'limit'  => $limit
			);
			
			$groupgetDetails = $this->groups_model->getgroupDetails($groupIdVal, $userauth['users_id']);
			$allFeeds = $this->groups_model->getgroupPostdetails($filter_data);	
			$response['newsfeeds'] = array();			
			if (!empty($allFeeds)) {
				$totalFeeds = count($this->groups_model->getgroupTotalPostdetails($filter_data));
				$response['totalFeeds'] = (string)$totalFeeds;
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
					}
					
					/*Get the report abuse status*/					
					if($feeds['feedType'] == 'news'){
						$reportAbusests = $this->newsfeeds_model->getuserreportAbusests($userauth['users_id'], $feeds['feedID']);
						//$sharelink = base_url().'share?data=news&id='.$feeds['feedID'];
						//$sharelink = 'http://tamilchamber.org.za/share?data=news&id='.$feeds['feedID'];
						$sharelink = $this->config->item('shareAddress').".page.link/?link=http://www.tamilchamber.org.news/news?id=".$feeds['feedID']."&apn=".$this->config->item('shareAndriodapn')."&amv=10&ibi=".$this->config->item('shareIOSibi')."&isi=".$this->config->item('shareIOSisi')."&ius=tamilethos";
					} else {
						$reportAbusests = 0;
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
						'polls' => $pollsarray,
						'groupId' => '',
						'groupName' => '',
						'feedcreateAt' => date_time_ago($feeds['crdate']),
						'feedexpireAt' => date_time_expire($feeds['exDate']),
						'shareLink' => $sharelink,
						'reportAbuse' => $reportAbusests,
					);
				}
				$response['current_page'] = (int)$page;
				$response['total_page'] = ceil($totalFeeds / $limit);
			} else {
				$response['totalFeeds'] = (string)0;
				$response['current_page'] = (int)$page;
				$response['total_page'] = 0;
			}
			$response['status'] = '200';			
			$response['messages'] = 'Group details data fetch successfully!';
			json_output($response['status'],$response);
		} else {
			$response['status'] = '401';
			$response['messages'] = 'Your Authorization details are incorrect.';					
			json_output($response['status'],$response);
		}
	}
	
	/*Leave Group*/
	public function leaveGroup(){
		$userauth = array();
		$userauth['users_id']  = $this->input->get_request_header('User-ID', TRUE);
        $userauth['token']     = $this->input->get_request_header('Auth-Key', TRUE);
		if(checkForUserSession($userauth) == 1){
			$post = $this->input->post();
			$input_data = array(
				'loggedUser' => $userauth['users_id'],
				'groupId' => $post['groupId'],
				'groupMemberId' => $post['groupMemberId'],
			);
			$groupLeave = $this->groups_model->leaveGroup($input_data);	
			if(!empty($groupLeave)){
				if($groupLeave == 2){
					$response['status'] = '200';			
					$response['messages'] = "You're left the group successfully.";
					json_output($response['status'],$response);
				} else {	
					$response['status'] = '200';			
					$response['messages'] = "You're left the group successfully.";
					json_output($response['status'],$response);
				}
			} else {				
				$response['status'] = '200';			
				$response['messages'] = "You're not left the group. Try again!";
				json_output($response['status'],$response);
			}
		} else {
			$response['status'] = '401';
			$response['messages'] = 'Your Authorization details are incorrect.';					
			json_output($response['status'],$response);
		}
	}
	
	/*Delete Group*/
	public function deleteGroup(){
		$userauth = array();
		$userauth['users_id']  = $this->input->get_request_header('User-ID', TRUE);
        $userauth['token']     = $this->input->get_request_header('Auth-Key', TRUE);
		if(checkForUserSession($userauth) == 1){
			$post = $this->input->post();
			$input_data = array(
				'loggedUser'     => $userauth['users_id'],
				'groupId' => $post['groupId'],
			);
			$groupDelete = $this->groups_model->deleteGroups($input_data);
			if(!empty($groupDelete)){								
				$groupmemlist = $this->groups_model->getgroupdeleteMember($post['groupId']);
				if(!empty($groupmemlist)){
					foreach($groupmemlist as $gpmemList){
						$userdetails = $this->users_model->getUseriddetails($userauth['users_id']);
						$groupDetails = $this->groups_model->getgroupDetails($post['groupId'], $userauth['users_id']);	
						$notify_data = array(
							array(
								'fromUserId'    => $userauth['users_id'],
								'toUserId'      => $gpmemList['receiveRequestId'],
								'notifyType'	=> 'groupDelete',
								'notifyId'		=> 0,
								'notifyReason'	=> 'Group admin deleted the group <b>'.$groupDetails->groupName.'<b>',
								'createdBy'		=> $userauth['users_id'],							
								'updatedBy'		=> $userauth['users_id'],
							)
						);
						$receiveRequest = $this->notifications_model->notificationsLog($notify_data);
					}
				}
				$response['status'] = '200';			
				$response['messages'] = 'Group has been deleted successfully.';
				json_output($response['status'],$response);		
			} else {				
				$response['status'] = '200';			
				$response['messages'] = 'This group not deleted. You are not a admin.';
				json_output($response['status'],$response);
			}
		} else {
			$response['status'] = '401';
			$response['messages'] = 'Your Authorization details are incorrect.';					
			json_output($response['status'],$response);
		}
	}
	
	/*Make an admin & remove admin*/	
	public function makegroupAdmin(){
		$userauth = array();
		$userauth['users_id']  = $this->input->get_request_header('User-ID', TRUE);
        $userauth['token']     = $this->input->get_request_header('Auth-Key', TRUE);
		if(checkForUserSession($userauth) == 1){
			$post = $this->input->post();
			$input_data = array(
				'loggedUser' => $userauth['users_id'],
				'groupId' => $post['groupId'],
				'userId' => $post['userId'],
				'groupMemberId' => $post['groupMemberId'],
				'groupMemisAdmin' => $post['groupMemisAdmin'],
			);
			$groupAdmin = $this->groups_model->makegroupAdmin($input_data);	
			if(!empty($groupAdmin)){
				$response['status'] = '200';
				$userdetails = $this->users_model->getUseriddetails($userauth['users_id']);
				$actuserdetails = $this->users_model->getUseriddetails($post['userId']);
				$groupDetails = $this->groups_model->getgroupDetails($post['groupId'], $userauth['users_id']);
				if($post['groupMemisAdmin'] == 1){
					$notifyType = 'groupAdmin'; 
					$notifyMsg = "Congratulations you've been made as an admin for <b>".$groupDetails->groupName."<b> by the <b>".$userdetails->firstName.' '.$userdetails->surName."<b>";
					$response['messages'] = 'You have added '.$actuserdetails->firstName.' '.$actuserdetails->surName.' to be an admin';
					/*} else if($post['groupMemisAdmin'] == 0){
						$notifyType = 'GPaddRemove';
						$notifyMsg = ' remove a admin you from '.$groupDetails->groupName.' group.';
						$response['messages'] = 'Remove a admin successfully.';
					}*/
					$notify_data = array(
						array(
							'fromUserId'    => $userauth['users_id'],
							'toUserId'      => $actuserdetails->userid,
							'notifyType'	=> $notifyType,
							'notifyId'		=> $post['groupId'],
							'notifyReason'	=> $notifyMsg,
							'createdBy'		=> $userauth['users_id'],							
							'updatedBy'		=> $userauth['users_id'],
						)
					);
					$receiveRequest = $this->notifications_model->notificationsLog($notify_data);
					json_output($response['status'],$response);
				}
				
			} else {				
				$response['status'] = '200';			
				$response['messages'] = 'Request status not updated. Try again!';
				json_output($response['status'],$response);
			}
		} else {
			$response['status'] = '401';
			$response['messages'] = 'Your Authorization details are incorrect.';					
			json_output($response['status'],$response);
		}
	}
	
	
	/*Group Invite get the friends list */
	public function gmMemberinvite(){
		$userauth = array();
		$userauth['users_id']  = $this->input->get_request_header('User-ID', TRUE);
        $userauth['token']     = $this->input->get_request_header('Auth-Key', TRUE);		
		if(checkForUserSession($userauth) == 1){
			$post = $this->input->post();
			$limit = 12;
			$page = $post['page'];
			if(!empty($post['search'])){
				$searchVal = $post['search'];
			} else {
				$searchVal = '';
			}
			$myinvite = array(
				'userId' => $userauth['users_id'],
				'groupId' => $post['groupId'],
				'searchVal' => $searchVal,
				'start'  => ($page - 1) * $limit,
				'limit'  => $limit
			);
			
			if(isset($post) && !empty($post)){
				$listUserarray = array();
				$groupInvitelist = $this->groups_model->getMemberinvitelist($myinvite);
				$groupInvitelisttot = count($this->groups_model->getMemberinvitelisttot($myinvite));
				if(!empty($groupInvitelist)){
					foreach($groupInvitelist as $gpInvitelist){
						$listUserarray[] = array(
							'friendID' => '',
							'requestSenderID' => '',
							'userId' => $gpInvitelist['userid'],
							'userName' => $gpInvitelist['firstName'].' '.$gpInvitelist['surName'],
							'userImg' => $gpInvitelist['profileimg'],
							'firebaseId' => '',
							'mutualFri' => '',
							'friendStatus' => 0,
						);
					}
				}
				$response['userList'] = $listUserarray;
				$response['totalinvitecnt'] = $groupInvitelisttot;			
				$response['current_page'] = (int)$page;
				$response['total_page'] = ceil($groupInvitelisttot / $limit);
				$response['status'] = '200';			
				$response['messages'] = 'Invite Member Listing showing successfully!';			
				json_output($response['status'],$response);
			} else {	
				$response['userList'] = '';
				$response['totalinvitecnt'] = '';	
				$response['current_page'] = (int)0;
				$response['total_page'] = 0;
				$response['status'] = '401';
				$response['messages'] = 'Your Authorization details are incorrect.';					
				json_output($response['status'],$response);
			}
		}
	}
	
	
	/*Add member invite send */
	public function groupAddmemberInvite(){
		$userauth = array();
		$userauth['users_id']  = $this->input->get_request_header('User-ID', TRUE);
        $userauth['token']     = $this->input->get_request_header('Auth-Key', TRUE);
		if(checkForUserSession($userauth) == 1){
			$post = $this->input->post();
			$input_data = array(
				'loggedUser'  => $userauth['users_id'],
				'groupId' => $post['groupId'],
				'addMemberId' => $post['addUserId'],
			);
			$memberjoinRequest = $this->groups_model->groupaddmemberInvite($input_data);
			if(!empty($memberjoinRequest)){
				$userdetails = $this->users_model->getUseriddetails($userauth['users_id']);
				$groupDetails = $this->groups_model->getgroupDetails($post['groupId'], $userauth['users_id']);
				$notify_data = array( 
					array(
						'fromUserId'    => $userauth['users_id'],
						'toUserId'      => $post['addUserId'],
						'notifyType'	=> 'groupInvite',
						'notifyId'		=> $post['groupId'],
						'notifyReason'	=> '<b>'.$userdetails->firstName.' '.$userdetails->surName.'<b> invite you to the group <b>'.$groupDetails->groupName.'<b>',
						'createdBy'		=> $userauth['users_id'],							
						'updatedBy'		=> $userauth['users_id'],
					)
				);
				$receiveRequest = $this->notifications_model->notificationsLog($notify_data);
				
				/* Member invite push notifications */
				$memberdetails = $this->users_model->getUseriddetails($post['addUserId']);
				
				if($memberdetails->deviceType == 1){
					$tokensAndroid[] = $memberdetails->deviceToken;
				} else if($memberdetails->deviceType == 2){
					$tokensIOS[] = $memberdetails->deviceToken;
				}
				
				$subject = "Group Notifications on Tamil Ethos";			
				$message = $userdetails->firstName.' '.$userdetails->surName.' invite you to the group '.$groupDetails->groupName;
				$created_date = date('Y-m-d h:i');
				$senddetailsAndroid = array(
					"title"=> $subject,
					"message"=>$message,
					"notifyType" => "groupInvite",
					"notifyId" => $post['groupId'],
					"timestamp" => $created_date
				);
				$senddetailsIOS = array(
					"attachment" => '',
					"media_type" => '',
					"notifyType" => "groupInvite",
					"notifyId" => $post['groupId']
				);
				$messageIOS = array(
					'title' => $subject,
					'body' => $message,
					'sound' => 'default',
					"notifyType" => "groupInvite",
					"notifyId" => $post['groupId'],
					"timestamp" => $created_date
				);
				
				if(!empty($tokensAndroid)){
					$message_statusan = send_notification($tokensAndroid, $senddetailsAndroid);
				} 
				
				if(!empty($tokensIOS)){
					$message_status = send_notificationIOS($tokensIOS, $senddetailsIOS, $messageIOS);					
				}
				
				$response['status'] = '200';			
				$response['messages'] = "Your invite request send successfully. Kindly wait the invite user Approval.";
				json_output($response['status'],$response);
			} else {
				$response['requestStatus'] = '';
				$response['status'] = '200';			
				$response['messages'] = 'Your invite request not send. Kindly try again!';
				json_output($response['status'],$response);
			}
		} else {
			$response['status'] = '401';
			$response['messages'] = 'Your Authorization details are incorrect.';					
			json_output($response['status'],$response);
		}
	}
	
	/*Admin remove member */
	public function groupAdminremoveMem(){
		$userauth = array();
		$userauth['users_id']  = $this->input->get_request_header('User-ID', TRUE);
        $userauth['token']     = $this->input->get_request_header('Auth-Key', TRUE);
		if(checkForUserSession($userauth) == 1){
			$post = $this->input->post();
			$input_data = array(
				'loggedUser'  => $userauth['users_id'],
				'groupId' => $post['groupId'],
				'removeMemberId' => $post['removeMemberId'],
			);
			$memberRemoveRequest = $this->groups_model->groupRemovemember($input_data);			
			if(!empty($memberRemoveRequest)){
				/*Member remove notifications*/
				$userdetails = $this->users_model->getUseriddetails($userauth['users_id']);
				$groupDetails = $this->groups_model->getgroupDetails($post['groupId'], $userauth['users_id']);
				$notify_data = array(
					array(
						'fromUserId'    => $userauth['users_id'],
						'toUserId'      => $memberRemoveRequest,
						'notifyType'	=> 'groupRemove',
						'notifyId'		=> 0,
						'notifyReason'	=> 'Group admin <b>'.$userdetails->firstName.' '.$userdetails->surName.'<b> removed you form <b>'.$groupDetails->groupName.'<b>',
						'createdBy'		=> $userauth['users_id'],							
						'updatedBy'		=> $userauth['users_id'],
					)
				);
				$receiveRequest = $this->notifications_model->notificationsLog($notify_data);
				$groupMemberCnt = count($this->groups_model->getMembercnt($post['groupId']));
				$response['groupMemberCnt'] = (string)$groupMemberCnt;
				$response['status'] = '200';			
				$response['messages'] = "Member removed successfully.";
				json_output($response['status'],$response);
			} else {
				$groupMemberCnt = count($this->groups_model->getMembercnt($post['groupId']));
				$response['groupMemberCnt'] = (string)$groupMemberCnt;
				$response['requestStatus'] = '';
				$response['status'] = '200';			
				$response['messages'] = 'Member not removed. Kindly try again!';
				json_output($response['status'],$response);
			}
		} else {
			$response['status'] = '401';
			$response['messages'] = 'Your Authorization details are incorrect.';					
			json_output($response['status'],$response);
		}
	}
	
	/*Group invitation*/
	public function gmInvitelist(){
		$userauth = array();
		$userauth['users_id']  = $this->input->get_request_header('User-ID', TRUE);
        $userauth['token']     = $this->input->get_request_header('Auth-Key', TRUE);		
		if(checkForUserSession($userauth) == 1){
			$gmInvitearr = array();
			$post = $this->input->post();
			if(isset($post) && !empty($post)){
				$groupInvitelist = $this->groups_model->getinviteMemberlist($post['groupID'], $userauth['users_id']);
				if(!empty($groupInvitelist)){
					foreach($groupInvitelist as $gInviteList){					
						$gmInvitearr[] = array(
							'groupMemberId' => $gInviteList['groupMemberId'],
							'sendRequestUserId' => $gInviteList['sendRequestUserId'],
							'status' => $gInviteList['status'],
							'createdDate' => $gInviteList['createdDate'],						
						);
					}
				}
				$response['gmInvites'] = $gmInvitearr;
				$response['status'] = '200';
				$response['messages'] = 'Invite listing showing.';					
				json_output($response['status'],$response);
			} else {
				$response['gmInvites'] = $gmInvitearr;
				$response['status'] = '200';
				$response['messages'] = 'Send the valid details group ID.';					
				json_output($response['status'],$response);
			}
		} else {
			$response['status'] = '401';
			$response['messages'] = 'Your Authorization details are incorrect.';					
			json_output($response['status'],$response);
		}
	}
	
	/* Group Accept/Cancel Request details update*/
	public function gmInviteupdate(){
		$userauth = array();
		$userauth['users_id']  = $this->input->get_request_header('User-ID', TRUE);
        $userauth['token']     = $this->input->get_request_header('Auth-Key', TRUE);
		if(checkForUserSession($userauth) == 1){
			$post = $this->input->post();
			$input_data = array(
				'loggedUser'     => $userauth['users_id'],
				'groupMemberId' => $post['groupMemberId'],
				'updateStatus' => $post['updateStatus'],
			);
			if($post['updateStatus'] == 1){
				$sucMsg = 'Accepted successfully';
				$failMsg = 'Accepted request Not Updated!';
			} else if($post['updateStatus'] == 4){
				$sucMsg = 'Rejected successfully';
				$failMsg = 'Rejected request Not Updated!';
			}
			$memberRequest = $this->groups_model->updateRequest($input_data);
			if(!empty($memberRequest)){	
				$groupMemberCnt = count($this->groups_model->getMembercnt($post['groupId']));
				$response['groupMemberCnt'] = (string) $groupMemberCnt;
				$response['status'] = '200';			
				$response['messages'] = $sucMsg;
				json_output($response['status'],$response);
			} else {
				$groupMemberCnt = count($this->groups_model->getMembercnt($post['groupId']));
				$response['groupMemberCnt'] = (string) $groupMemberCnt;
				$response['status'] = '200';			
				$response['messages'] = $failMsg;
				json_output($response['status'],$response);
			}
		} else {
			$response['status'] = '401';
			$response['messages'] = 'Your Authorization details are incorrect.';					
			json_output($response['status'],$response);
		}
	}
	
	public function groupJoinrequest(){
		$userauth = array();
		$userauth['users_id']  = $this->input->get_request_header('User-ID', TRUE);
        $userauth['token']     = $this->input->get_request_header('Auth-Key', TRUE);
		if(checkForUserSession($userauth) == 1){
			$post = $this->input->post();
			$input_data = array(
				'loggedUser'  => $userauth['users_id'],
				'groupId' => $post['groupId'],
			);
			$memberjoinRequest = $this->groups_model->groupJoinrequest($input_data);
			if(!empty($memberjoinRequest)){
				$response['status'] = '200';			
				$response['messages'] = "Your join request send successfully. Kindly wait the group admin Approval.";
				json_output($response['status'],$response);
			} else {
				$response['requestStatus'] = '';
				$response['status'] = '200';			
				$response['messages'] = 'Your join request not send. Kindly try again!';
				json_output($response['status'],$response);
			}
		} else {
			$response['status'] = '401';
			$response['messages'] = 'Your Authorization details are incorrect.';					
			json_output($response['status'],$response);
		}
	}
	
	
	public function groupReportAbuse(){
		$userauth = array();
		$userauth['users_id']  = $this->input->get_request_header('User-ID', TRUE);
        $userauth['token']     = $this->input->get_request_header('Auth-Key', TRUE);
		if(checkForUserSession($userauth) == 1){
			$post = $this->input->post();
			if(isset($post) && !empty($post)){
				$data['reAbuse']['groupId'] = $post['groupId'];
				$data['reAbuse']['comments'] = $post['comments'];
				$data['reAbuse']['createdBy'] = $data['reAbuse']['updatedBy'] = $userauth['users_id'];
				$result = $this->groups_model->save_group_reportAbuse($data);
				$response['status'] = '200';
				if($result == ""){
					$response['messages'] = 'Report abuse details not added. Kindly try again!';
				} else if($result){					
					$response['messages'] = 'Report abuse details added successfully.';
				}
				json_output($response['status'],$response);
			} else {
				$response['status'] = '401';
				$response['messages'] = 'Report abuse details not added. Kindly try again!';					
				json_output($response['status'],$response);
			}
		} else {
			$response['status'] = '401';
			$response['messages'] = 'Your Authorization details are incorrect.';					
			json_output($response['status'],$response);
		}
	}
	
	/*Get all Group Members*/
	public function getAllgroupmember(){
		$userauth = array();
		$userauth['users_id']  = $this->input->get_request_header('User-ID', TRUE);
        $userauth['token']     = $this->input->get_request_header('Auth-Key', TRUE);		
		if(checkForUserSession($userauth) == 1){
			$post = $this->input->post();
			$myMember = array(
				'userId' => $userauth['users_id'],
				'groupId' => $post['groupId']
			);
			
			$groupmemarray = array();
			$groupmemlist = $this->groups_model->getgroupMemberListing($myMember);
			if(!empty($groupmemlist)){
				foreach($groupmemlist as $gpmemList){
					$userdetails = $this->users_model->getUseriddetails($gpmemList['receiveRequestId']);				
					if($userdetails->userid != $userauth['users_id']){
						$groupmemarray[] = array(
							'userId' => $userdetails->userid, 
							'userName' => $userdetails->firstName.' '.$userdetails->surName, 
							'userImg' => $userdetails->profileimg
						);
					}
				}
			}
			$response['groupMemList'] = $groupmemarray;			
			$response['status'] = '200';			
			$response['messages'] = 'Group Member Listing successfully!';			
			json_output($response['status'],$response);
		} else {	
			$response['groupMemList'] = '';			
			$response['status'] = '401';
			$response['messages'] = 'Your Authorization details are incorrect.';					
			json_output($response['status'],$response);
		}
	}
}