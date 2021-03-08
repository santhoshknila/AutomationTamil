<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Newsfeeds extends CI_Controller {
	private $error = array();
	function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->model("newsfeeds_model");
        $this->load->model("notifications_model");
        $this->load->model("polls_model");
        $this->load->model("users_model");
        $this->load->model("auth_model");		
        $this->load->model("groups_model");		
        $this->load->library('session');
        $this->load->library('email');
		$this->load->library('form_validation');
		$this->load->library('phpass');
		$this->load->helper('date');
    }
	
	public function index(){
		$userauth = array();
		$userauth['users_id']  = $this->input->get_request_header('User-ID', TRUE);
        $userauth['token']     = $this->input->get_request_header('Auth-Key', TRUE);
		if(checkForUserSession($userauth) == 1){
			$data = array();
			$data['title'] = "Manage Posts";
			$select=array();
			$where=array();
			$orderby = array();
			$join = array();
			$groupby =array();
			$like = array();
			$or_like = array();
			$or_where = array();
			$where_in = array();
			$where_not = array();
			
			$post = $this->input->post();
			
			if (isset($post['page'])) {
				$page = $post['page'];
			} else {
				$page = 1;
			}
			$limit = 3;
			$filter_data = array(
				'userid' => $userauth['users_id'],
				'start'  => ($page - 1) * $limit,
				'limit'  => $limit
			);
			
			/* Update version user */
			$version_data = array(
				'userid' => $userauth['users_id'],
				'versionType' => '1.2'
			);
			$this->users_model->versionUpdatebyuser($version_data);
			
			
			$allFeeds = $this->newsfeeds_model->getPostdetails($filter_data);			
			$totalFeeds = count($this->newsfeeds_model->getTotalPostdetails($filter_data));
			
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
						//$sharelink = 'http://tamilchamber.org.za/share?data=news&id='.$feeds['feedID'];
						//$sharelink = base_url().'news?id='.$feeds['feedID'];
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
				
				//$response['results'] = sprintf('Showing', ($totalFeeds) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($totalFeeds - $limit)) ? $totalFeeds : ((($page - 1) * $limit) + $limit), $totalFeeds, ceil($totalFeeds / $limit));
				//$response['total_post'] = $totalFeeds;
				$response['current_page'] = (int)$page;
				$response['total_page'] = ceil($totalFeeds / $limit);
				$response['status'] = '200';
				$response['messages'] = 'Newsfeed listings';					
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
	
	public function save(){
		$userauth = array();
		$userauth['users_id']  = $this->input->get_request_header('User-ID', TRUE);
        $userauth['token']     = $this->input->get_request_header('Auth-Key', TRUE);
		if(checkForUserSession($userauth) == 1){
			$post = $this->input->post();
			if(isset($post) && !empty($post)){
				$post['news']['newsFeedsId'] = $post['newsFeedsId'];
				$post['news']['title'] = $post['title'];
				$post['news']['description'] = $post['description'];
				$post['news']['privacyId'] = $post['privacyId'];
				$post['news']['groupId'] = $post['groupId'];
				$post['news']['createdBy'] = $post['userid'];
				$post['news']['updatedBy'] = $post['userid'];
				if(isset($_FILES['userfile']['name']) && !empty($_FILES['userfile']['name'])){
					$files = $_FILES;
					$count = count($_FILES['userfile']['name']);
					$config = array(
						'upload_path' => "./uploads/newsfeeds/",
						'allowed_types' => "jpg|png|jpeg|gif",
						'overwrite' => TRUE,
						'max_size' => "2048000",
						'remove_spaces' => TRUE,
					);
					
					for($i = 0; $i < $count; $i++) {
						$_FILES['userfile']['name']     = uniqid().'_'.time().'_'.$files['userfile']['name'][$i];
						$_FILES['userfile']['type']     = $files['userfile']['type'][$i];
						$_FILES['userfile']['tmp_name'] = $files['userfile']['tmp_name'][$i];
						$_FILES['userfile']['error']    = $files['userfile']['error'][$i];
						$_FILES['userfile']['size']     = $files['userfile']['size'][$i]; 
	   
						$this->load->library('upload', $config);

						if (!$this->upload->do_upload('userfile')){
							$error = array('error' => $this->upload->display_errors());
						} else {
							$fileData = $this->upload->data();							
							$post['newsimg']['imagevideo_url'][] = $fileData['file_name'];
							$post['newsimg']['fileTypeval'][] = 'image';
							
							$file = $fileData['file_name'];
							$path = $fileData['full_path'];
							$config_resize['image_library'] = 'gd2';  
							$config_resize['source_image'] = $path;
							$config_resize['create_thumb'] = false;
							$config_resize['maintain_ratio'] = TRUE;
							$config_resize['width'] = 250;
							$config_resize['height'] = 250;
							$config_resize['new_image'] = './uploads/newsfeeds/thumb/'.$file;
							$this->load->library('image_lib',$config_resize);
							$this->image_lib->clear();
							$this->image_lib->initialize($config_resize);
							$this->image_lib->resize();
						}
					} 
				}
				
				if(isset($post['filePathURl']) && !empty($post['filePathURl'])){
					foreach($post['filePathURl'] as $resUrl){
						$post['newsimg']['fileTypeval'][] = 'url';
						$post['newsimg']['imagevideo_url'][] = $resUrl;
					}
				}
				
				unset($post['userfile']);
				unset($post['filePathURl']);
				unset($post['title']);
				unset($post['description']);
				unset($post['privacyId']);	
				unset($post['userid']);	
				
				$result = $this->newsfeeds_model->save_update_newsfeeds($post);
				$response['status'] = '200';
				if($result == ""){
					$response['messages'] = 'Post not Added. Kindly try again!';
				} else if($result){
					if(!empty($post['news']['newsFeedsId'])){
						$response['messages'] = 'Post has been updated successfully.';
					} else {
						if(!empty($post['groupId'])){
							$groupmemlist = $this->groups_model->getgroupdeleteMember($post['groupId']);
							if(!empty($groupmemlist)){
								$userdetailsSend = $this->users_model->getUseriddetails($userauth['users_id']);
								$groupDetails = $this->groups_model->getgroupDetails($post['news']['groupId'], $userauth['users_id']);
								
								$notify_dataself = array(
									array(
										'fromUserId'    => $userauth['users_id'],
										'toUserId'      => $userauth['users_id'],
										'notifyType'	=> 'news',
										'notifyId'		=> $result,
										'notifyReason'	=> 'You posted <b>'.$post['news']['title'].'<b> in <b>'.$groupDetails->groupName.'<b>',
										'createdBy'		=> $userauth['users_id'],							
										'updatedBy'		=> $userauth['users_id'],
									)
								);
								$receiveRequest = $this->notifications_model->notificationsLog($notify_dataself);
								
								foreach($groupmemlist as $gpmemList){
									if($userauth['users_id'] != $gpmemList['receiveRequestId']){
										$notify_data = array(
											array(
												'fromUserId'    => $userauth['users_id'],
												'toUserId'      => $gpmemList['receiveRequestId'],
												'notifyType'	=> 'news',
												'notifyId'		=> $result,
												'notifyReason'	=> '<b>'.$userdetailsSend->firstName.' '.$userdetailsSend->surName.'<b> posted <b>'.$post['news']['title'].'<b> in <b>'.$groupDetails->groupName.'<b>',
												'createdBy'		=> $userauth['users_id'],							
												'updatedBy'		=> $userauth['users_id'],
											)
										);
										$receiveRequest = $this->notifications_model->notificationsLog($notify_data);
										if($gpmemList['receiveRequestId'] != $userauth['users_id']){
											$userdetails = $this->users_model->getUseriddetails($gpmemList['receiveRequestId']);
											if($userdetails->deviceType == 1){
												$tokensAndroid[] = $userdetails->deviceToken;
											} else if($userdetails->deviceType == 2){
												$tokensIOS[] = $userdetails->deviceToken;
											}
										}
									}
								}
								
								$subject = "Group Notifications on Tamil Ethos";			
								$message = $userdetailsSend->firstName.' '.$userdetailsSend->surName.' posted '.$post['news']['title'].' in '.$groupDetails->groupName.' : '.$post['news']['description'];
								$created_date = date('Y-m-d h:i');
								$senddetailsAndroid = array(
									"title"=> $subject,
									"message"=>$message,
									"notifyType" => "news",
									"notifyId" => $result,
									"timestamp" => $created_date
								);
								$senddetailsIOS = array(
									"attachment" => '',
									"media_type" => '',
									"notifyType" => "news",
									"notifyId" => $result
								);
								$messageIOS = array(
									'title' => $subject,
									'body' => $message,
									'sound' => 'default',
									"notifyType" => "news",
									"notifyId" => $result
								);
								
								if(!empty($tokensAndroid)){
									$message_statusan = send_notification($tokensAndroid, $senddetailsAndroid);
								}
								
								if(!empty($tokensIOS)){
									$message_status = send_notificationIOS($tokensIOS, $senddetailsIOS, $messageIOS);
								}
							}
						}
						$response['messages'] = 'Post has been added successfully.';
					}
				}
				json_output($response['status'],$response);
			}
		} else {
			$response['status'] = '401';
			$response['messages'] = 'Your Authorization details are incorrect.';					
			json_output($response['status'],$response);
		}
	}
	
	public function newsDelete(){
		$userauth = array();
		$userauth['users_id']  = $this->input->get_request_header('User-ID', TRUE);
        $userauth['token']     = $this->input->get_request_header('Auth-Key', TRUE);
		if(checkForUserSession($userauth) == 1){
			$post = $this->input->post();
			if(isset($post) && !empty($post)){
				$post['userId'] = $userauth['users_id'];
				$result = $this->newsfeeds_model->news_Delete($post);
				if($result == ''){
					$response['status'] = '200';
					$response['messages'] = 'Send the valid details.';					
					json_output($response['status'],$response);
				} else {
					$response['status'] = '200';
					$response['messages'] = 'Post has been deleted successfully.';					
					json_output($response['status'],$response);
				}
			} else {
				$response['status'] = '401';
				$response['messages'] = 'Send the valid details.';					
				json_output($response['status'],$response);
			}
		} else {
			$response['status'] = '401';
			$response['messages'] = 'Your Authorization details are incorrect.';					
			json_output($response['status'],$response);
		}
	}
	
	
	public function likePost(){
		$userauth = array();
		$userauth['users_id']  = $this->input->get_request_header('User-ID', TRUE);
        $userauth['token']     = $this->input->get_request_header('Auth-Key', TRUE);
		if(checkForUserSession($userauth) == 1){
			$post = $this->input->post();
			if(isset($post) && !empty($post)){
				$data['like']['newsFeedsId'] = $post['feedID'];
				$data['like']['status'] = $post['likeval'];
				$data['like']['createdBy'] = $post['userid'];
				$data['like']['updatedBy'] = $post['userid'];
				$result = $this->newsfeeds_model->save_update_likefeeds($data);
				$response['status'] = '200';
				if($result == ""){
					$response['messages'] = 'Request action not updated. Kindly try again!';
				} else if($result){	
					/*Notifications send to request friend */
					if($post['likeval'] == 1){
						/* Send the push notification switch on/off like notifications */						
						$select=array();
						$where=array();		
						$where['nf.newsFeedsId'] = $post['feedID'];
						$newsfeeds = $this->newsfeeds_model->get($where,$select);
						foreach($newsfeeds as $feeds){
							if($feeds->createdBy != $userauth['users_id']){
								$userdetails = $this->users_model->getUseriddetails($feeds->createdBy);
								$userdetailsSent = $this->users_model->getUseriddetails($userauth['users_id']);
								$subject = $feeds->title.' like notifications';			
								$message = $userdetailsSent->firstName.' '.$userdetailsSent->surName.' likes your post '.$feeds->title;
								$created_date = date('Y-m-d h:i');
								if($userdetails->likeNotification == 1){
									if($userdetails->deviceType == 1){
										$tokensAndroid[] = $userdetails->deviceToken;
									} else if($userdetails->deviceType == 2){
										$tokensIOS[] = $userdetails->deviceToken;
									}
									$senddetailsAndroid = array(
										"title"=> $subject,
										"message"=>$message,
										"notifyType" => "newsLike",
										"notifyId" => $post['feedID'],
										"timestamp" => $created_date
									);
									$senddetailsIOS = array(
										"attachment" => '',
										"media_type" => '',
										"notifyType" => "newsLike",
										"notifyId" => $post['feedID']
									);
									$messageIOS = array(
										'title' => $subject,
										'body' => $message,
										'sound' => 'default',
										"notifyType" => "newsLike",
										"notifyId" => $post['feedID'],
										"timestamp" => $created_date
									);
									
									if(!empty($tokensAndroid)){
										$message_statusan = send_notification($tokensAndroid, $senddetailsAndroid);
									} 
									
									if(!empty($tokensIOS)){
										$message_status = send_notificationIOS($tokensIOS, $senddetailsIOS, $messageIOS);
									}
								}
								$notify_data = array(
									array(
										'fromUserId'    => $userauth['users_id'],
										'toUserId'      => $feeds->createdBy,
										'notifyType'	=> 'newsLike',
										'notifyId'		=> $post['feedID'],
										'notifyReason'	=> '<b>'.$userdetailsSent->firstName.' '.$userdetailsSent->surName.'<b> likes your post <b>'.$feeds->title.'<b>',
										'createdBy'		=> $userauth['users_id'],							
										'updatedBy'		=> $userauth['users_id'],
									),

									array(
										'fromUserId'    => $userauth['users_id'],
										'toUserId'      => $feeds->createdBy,
										'notifyType'	=> 'newsOther',
										'notifyId'		=> $post['feedID'],
										'notifyReason'	=> '<b>'.$userdetailsSent->firstName.' '.$userdetailsSent->surName.'<b> likes post <b>'.$feeds->title.'<b> posted by <b>'.$userdetails->firstName.' '.$userdetails->surName.'<b>',
										'createdBy'		=> $userauth['users_id'],							
										'updatedBy'		=> $userauth['users_id'],
									),

									array(
										'fromUserId'    => $userauth['users_id'],
										'toUserId'      => $userauth['users_id'],
										'notifyType'	=> 'newsLike',
										'notifyId'		=> $post['feedID'],
										'notifyReason'	=> 'You liked the post <b>'.$feeds->title.'<b> posted by <b>'.$userdetails->firstName.' '.$userdetails->surName.'<b>',
										'createdBy'		=> $userauth['users_id'],							
										'updatedBy'		=> $userauth['users_id'],
									)
								);							
								$receiveRequest = $this->notifications_model->notificationsLog($notify_data);
							}
						}
					}
					$response['messages'] = 'Request action updated successfully.';					
				}
				json_output($response['status'],$response);
			}
		}  else {
			$response['status'] = '401';
			$response['messages'] = 'Your Authorization details are incorrect.';					
			json_output($response['status'],$response);
		}
	}
	
public function likeUser($feedID, $page){
		$userauth = array();
		$userauth['users_id']  = $this->input->get_request_header('User-ID', TRUE);
        $userauth['token']     = $this->input->get_request_header('Auth-Key', TRUE);
		if(checkForUserSession($userauth) == 1){
			$likeUserarray = array();
			$limit = 10;
			$filter_data = array(
				'newsFeedsId'    => $feedID,
				'start'          => ($page - 1) * $limit,
				'limit'          => $limit
			);
			
			$likeUserlist = $this->newsfeeds_model->getlikeuserListing($filter_data);
			$likeTotalUserlist = $this->newsfeeds_model->getTotallikeuserListing($feedID);
			$response['likeuserCnt'] = count($likeTotalUserlist);
			if(!empty($likeUserlist)){
				foreach($likeUserlist as $luList){
					$userdetails = $this->users_model->getUseriddetails($luList['createdBy']);
					$likeUserarray[] = array(
						'userId' => $userdetails->userid,
						'userName' => $userdetails->firstName,
						'userImg' => $userdetails->profileimg,
					);
				}
			}
			$response['likeuserList'] = $likeUserarray;
			$response['current_page'] = (int)$page;
			$response['total_page'] = ceil(count($likeTotalUserlist) / $limit);
			$response['status'] = '200';			
			$response['messages'] = 'Post like users listing successfully!';			
			json_output($response['status'],$response);
		} else {
			$response['status'] = '401';
			$response['messages'] = 'Your Authorization details are incorrect.';					
			json_output($response['status'],$response);
		}
	}
	
	public function comments($cid, $page, $pageType){
		$userauth = array();
		$userauth['users_id']  = $this->input->get_request_header('User-ID', TRUE);
        $userauth['token']     = $this->input->get_request_header('Auth-Key', TRUE);		
		if(checkForUserSession($userauth) == 1){		
			$data = array();		
			$select=array();
			$where=array();		
			$where['nf.newsFeedsId'] = $cid;
			$newsfeeds = $this->newsfeeds_model->get($where,$select);
			$response['newsfeeds'] = array();
			if (!empty($newsfeeds)) {
				foreach($newsfeeds as $feeds){
					$userdetails = $this->users_model->getUseriddetails($feeds->createdBy);
					if($feeds->createdBy == 1){
						$createUser = 'Tamil Ethos Smart App';
						$isAdmin ='1';
					} else {
						$createUser = $userdetails->firstName;
						$isAdmin ='0';
					}
					$attributearray = array(
						'likeCnt' => getTotallikecnt($feeds->newsFeedsId),
						'commentCnt' => getTotalcommentscnt($feeds->newsFeedsId),
						'myLike' => getMylikes($userauth['users_id'], $feeds->newsFeedsId),
					);					
					$mediaFiles = getMedia($feeds->newsFeedsId);
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
					//Total Comments
					if($pageType == 'details'){
						$limit = 3;
					} else if($pageType == 'view'){
						$limit = 10;
					}
					$filter_data = array(
						'newsFeedsId'    => $feeds->newsFeedsId,
						'start'          => ($page - 1) * $limit,
						'limit'          => $limit
					);
					$comments = getTotalcomments($filter_data);					
					$commentsCnt = count(getTotalCntcomments($feeds->newsFeedsId));					
					$commentsarray = array();
					if(!empty($comments)){
						foreach($comments as $cmt){
							$cmtuserdetails = $this->users_model->getUseriddetails($cmt['createdBy']);
							if($cmt['createdBy'] == 1){
								$cmtUser = 'Tamil Ethos Smart App';
							} else {
								$cmtUser = $cmtuserdetails->firstName;
							}
							$commentsarray[] = array(
								'userId' => $cmtuserdetails->userid,
								'userName' => $cmtUser,
								'userImg' => $cmtuserdetails->profileimg,
								'commentId' => $cmt['commentId'],
								'commentdesc' => $cmt['comments'],
								'commentDate' => date_time_ago($cmt['createdDate']),
							);
						}						
					}
					
					/*Get the report abuse status*/
					if($feeds->newsType == 'news'){
						$reportAbusests = $this->newsfeeds_model->getuserreportAbusests($userauth['users_id'], $feeds->newsFeedsId);
						//$sharelink = base_url().'share?data=news&id='.$feeds->newsFeedsId;
						//$sharelink = base_url().'news?id='.$feeds->newsFeedsId;
						//$sharelink = 'http://tamilchamber.org.za/share?data=news&id='.$feeds['feedID'];
						$sharelink = $this->config->item('shareAddress').".page.link/?link=http://www.tamilchamber.org.news/news?id=".$feeds->newsFeedsId."&apn=".$this->config->item('shareAndriodapn')."&amv=10&ibi=".$this->config->item('shareIOSibi')."&isi=".$this->config->item('shareIOSisi')."&ius=tamilethos";
					} else {
						$reportAbusests = 0;
						//$sharelink = 'http://www.tamilchamber.org.za/share?data=poll&id='.$feeds['feedID'];
						//$sharelink = base_url().'share?data=poll&id='.$feeds->newsFeedsId;
						//$sharelink = base_url().'poll?id='.$feeds->newsFeedsId;
						$sharelink = $this->config->item('shareAddress').".page.link/?link=http://www.tamilchamber.org.poll/poll?id=".$feeds->newsFeedsId."&apn=".$this->config->item('shareAndriodapn')."&amv=10&ibi=".$this->config->item('shareIOSibi')."&isi=".$this->config->item('shareIOSisi')."&ius=tamilethos";
					}
					
					if(!empty($feeds->groupId)){
						$groupDetails = $this->groups_model->getgroupDetails($feeds->groupId, '');
						$groupId = $feeds->groupId;
						$groupName = $groupDetails->groupName;
					} else {
						$groupId = '';
						$groupName = '';
					}
					
					$response['newsfeeds'][] = array(
						'userId' => $userdetails->userid,
						'userName' => $createUser,
						'userImg' => $userdetails->profileimg,
						'feedID' => $feeds->newsFeedsId,
						'feedTitle' => $feeds->title,
						'feedDescription' => $feeds->description,
						'feedType' => $feeds->newsType,
						'isAdmin' => $isAdmin,
						'privacyId' => $feeds->privacyId,
						'media' => $mediaarray,
						'attribute' => $attributearray,
						'groupId' => $groupId,
						'groupName' => $groupName,
						'feedcreateAt' => date_time_ago($feeds->createdDate),
						'comments' => $commentsarray,
						'shareLink' => $sharelink,
						'reportAbuse' => $reportAbusests,
					);
				}
				$response['current_page'] = (int)$page;
				$response['total_page'] = ceil($commentsCnt / $limit);
				$response['status'] = '200';
				$response['messages'] = 'Comments listings';					
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
	
	public function addcomments(){
		$userauth = array();
		$userauth['users_id']  = $this->input->get_request_header('User-ID', TRUE);
        $userauth['token']     = $this->input->get_request_header('Auth-Key', TRUE);
		if(checkForUserSession($userauth) == 1){
			$post = $this->input->post();
			if(isset($post) && !empty($post)){
				$data['cmt']['newsFeedsId'] = $post['feedID'];
				$data['cmt']['comments'] = $post['comments'];
				$data['cmt']['createdBy'] = $post['userid'];
				$data['cmt']['updatedBy'] = $post['userid'];
				$result = $this->newsfeeds_model->save_update_commentsfeeds($data);
				$response['status'] = '200';
				if($result == ""){
					$response['messages'] = 'Comments not added. Kindly try again!';
				} else if($result){
					/*Notifications send to request friend */
					$select=array();
					$where=array();		
					$where['nf.newsFeedsId'] = $post['feedID'];
					$newsfeeds = $this->newsfeeds_model->get($where,$select);
					foreach($newsfeeds as $feeds){
						if($feeds->createdBy != $userauth['users_id']){
							$userdetails = $this->users_model->getUseriddetails($feeds->createdBy);
							$userdetailsSent = $this->users_model->getUseriddetails($userauth['users_id']);
							$subject = $feeds->title.' comments notifications';			
							$message = $userdetails->firstName.' '.$userdetails->surName.' commented on your post '.$feeds->title;
							$created_date = date('Y-m-d h:i');
							if($userdetails->commentNotification == 1){
								if($userdetails->deviceType == 1){
									$tokensAndroid[] = $userdetails->deviceToken;
								} else if($userdetails->deviceType == 2){
									$tokensIOS[] = $userdetails->deviceToken;
								}
								$senddetailsAndroid = array(
									"title"=> $subject,
									"message"=>$message,
									"notifyType" => "newsCmt",
									"notifyId" => $post['feedID'],
									"timestamp" => $created_date
								);
								$senddetailsIOS = array(
									"attachment" => '',
									"media_type" => '',
									"notifyType" => "newsCmt",
									"notifyId" => $post['feedID']
								);
								$messageIOS = array(
									'title' => $subject,
									'body' => $message,
									'sound' => 'default',
									"notifyType" => "newsCmt",
									"notifyId" => $post['feedID'],
									"timestamp" => $created_date
								);
								
								if(!empty($tokensAndroid)){
									$message_statusan = send_notification($tokensAndroid, $senddetailsAndroid);
								} 
								
								if(!empty($tokensIOS)){
									$message_status = send_notificationIOS($tokensIOS, $senddetailsIOS, $messageIOS);
								}
							}
							
							$notify_data = array(
								array(
									'fromUserId'    => $userauth['users_id'],
									'toUserId'      => $feeds->createdBy,
									'notifyType'	=> 'newsCmt',
									'notifyId'		=> $post['feedID'],
									'notifyReason'	=> '<b>'.$userdetailsSent->firstName.' '.$userdetailsSent->surName.'<b> commented on your post <b>'.$feeds->title.'<b>',
									'createdBy'		=> $userauth['users_id'],							
									'updatedBy'		=> $userauth['users_id'],
								),
								array(
									'fromUserId'    => $userauth['users_id'],
									'toUserId'      => $userauth['users_id'],
									'notifyType'	=> 'newsCmt',
									'notifyId'		=> $post['feedID'],
									'notifyReason'	=> 'You commented on the post <b>'.$feeds->title.'<b> posted by <b>'.$userdetails->firstName.' '.$userdetails->surName.'<b>',
									'createdBy'		=> $userauth['users_id'],							
									'updatedBy'		=> $userauth['users_id'],
								)
							);
							$receiveRequest = $this->notifications_model->notificationsLog($notify_data);
						}
					}
					$response['commentId'] = $result;
					$response['messages'] = 'Comments added successfully.';					
				}
				json_output($response['status'],$response);
			} else {
				$response['status'] = '401';
				$response['messages'] = 'Comments not added.';					
				json_output($response['status'],$response);
			}
		} else {
			$response['status'] = '401';
			$response['messages'] = 'Your Authorization details are incorrect.';					
			json_output($response['status'],$response);
		}
	}
	
	public function deletecomments($cid){
		$userauth = array();
		$userauth['users_id']  = $this->input->get_request_header('User-ID', TRUE);
        $userauth['token']     = $this->input->get_request_header('Auth-Key', TRUE);
		if(checkForUserSession($userauth) == 1){
			$delCmt = $this->newsfeeds_model->delComments($cid);			
			$response['status'] = '200';
			if($delCmt == ""){
				$response['messages'] = 'Comment not deleted. Try again';
			} else if($delCmt){					
				$response['messages'] = 'Comment deleted successfully.';
			}
			json_output($response['status'],$response);
		} else {
			$response['status'] = '401';
			$response['messages'] = 'Your Authorization details are incorrect.';					
			json_output($response['status'],$response);
		}
	}
	
	public function addreportAbuse(){
		$userauth = array();
		$userauth['users_id']  = $this->input->get_request_header('User-ID', TRUE);
        $userauth['token']     = $this->input->get_request_header('Auth-Key', TRUE);
		if(checkForUserSession($userauth) == 1){
			$post = $this->input->post();
			if(isset($post) && !empty($post)){
				$data['reAbuse']['newsFeedsId'] = $post['feedID'];
				$data['reAbuse']['comments'] = $post['comments'];
				$data['reAbuse']['createdBy'] = $data['reAbuse']['updatedBy'] = $userauth['users_id'];
				$result = $this->newsfeeds_model->save_update_reportAbusefeeds($data);
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
	
	function removeUploadfiles(){
		$userauth = array();
		$userauth['users_id']  = $this->input->get_request_header('User-ID', TRUE);
        $userauth['token']     = $this->input->get_request_header('Auth-Key', TRUE);
		if(checkForUserSession($userauth) == 1){
			$post = $this->input->post();
			if(isset($post) && !empty($post)){
				$data['del']['uploadRowID'] = $post['uploadRowID'];
				$result = $this->newsfeeds_model->del_removeUploadfiles($data);
				$response['status'] = '200';
				if($result == ""){
					$response['messages'] = 'Upload files not removed. Kindly try again!';
				} else if($result){					
					$response['messages'] = 'Upload files removed successfully.';
				}
				json_output($response['status'],$response);
			} else {
				$response['status'] = '401';
				$response['messages'] = 'Upload files not removed. Kindly try again!';					
				json_output($response['status'],$response);
			}
		} else {
			$response['status'] = '401';
			$response['messages'] = 'Your Authorization details are incorrect.';					
			json_output($response['status'],$response);
		}
	}
}