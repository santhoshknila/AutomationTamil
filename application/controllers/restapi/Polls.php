<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Polls extends CI_Controller {
	private $error = array();
	function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->model("polls_model");
        $this->load->model("users_model");
        $this->load->model("notifications_model");
        $this->load->model("auth_model");		
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
			$post = $this->input->post();			
			$data = array();
			$data['title'] = "Manage Polls";
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
			
			$where['poll.status'] = 1;
			$where['poll.startDate <='] = date("Y-m-d");
			$where['poll.endDate >='] = date("Y-m-d");	
			//$where['poll.startTime <='] = date("H:i");
			//$where['poll.endTime >='] = date("H:i");	
			
			if (isset($post['page'])) {
				$page = $post['page'];
			} else {
				$page = 1;
			}
			$limit = 3;
			$offset = $limit * ($page - 1);
			
			$orderby['poll.pollingId'] = 'DESC';
			$allPolls = $this->polls_model->get($where,$orderby,$select,$join,$groupby,$limit,$offset,"",$like,$or_like,$or_where,$where_in);			
			$totalPolls = count($this->polls_model->get($where,$orderby));
			
			$response['newsfeeds'] = array();		
			if (!empty($allPolls)) {
				foreach($allPolls as $feeds){
					$userdetails = $this->users_model->getUseriddetails($feeds->createdBy);
					if($feeds->createdBy == 1){
						$createUser = 'Tamil Ethos';
					} else {
						$createUser = $userdetails->firstName;
					}					
					if($feeds->pollType == 'polls'){
						$mediaarray = array();
						$pollsarray = array();
						$attributearray = array(
							'likeCnt' => 0,
							'commentCnt' => 0,
							'myLike' => 0,
						);	
						
						$pollAnswer = getPollsanswer($feeds->pollingId);
						if(!empty($pollAnswer)){
							foreach($pollAnswer as $pans){
								$pollsarray[] = array(
									'answerId' => $pans['pollingAnswerId'],
									'answer' => $pans['answer'],
									'likecnt' => getTotalpollcnt($pans['pollingAnswerId'], $feeds->pollingId).'%',
									'selected' => getMyanswerId($userauth['users_id'], $feeds->pollingId, $pans['pollingAnswerId']),
								);
							}
						}
					}

					$response['newsfeeds'][] = array(
						'userId' => $userdetails->userid,
						'userName' => $createUser,
						'userImg' => $userdetails->profileimg,
						'feedID' => $feeds->pollingId,
						'feedTitle' => $feeds->pollingQuestion,
						'feedDescription' => $feeds->description,
						'feedType' => $feeds->pollType,
						'media' => $mediaarray,
						'attribute' => $attributearray,
						'polls' => $pollsarray,
						'feedcreateAt' => date_time_ago($feeds->createdDate),
						'feedexpireAt' => date_time_expire($feeds->endDate.''.$feeds->endTime),
						/*'shareLink' => 'http://www.tamilchamber.org.za/poll?id='.$feeds->pollingId,*/
						'shareLink' => $this->config->item('shareAddress').".page.link/?link=http://www.tamilchamber.org.poll/poll?id=".$feeds->pollingId."&apn=".$this->config->item('shareAndriodapn')."&amv=10&ibi=".$this->config->item('shareIOSibi')."&isi=".$this->config->item('shareIOSisi')."&ius=tamilethos",
					);
				}
				
				//$response['results'] = sprintf('Showing', ($totalPolls) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($totalPolls - $limit)) ? $totalPolls : ((($page - 1) * $limit) + $limit), $totalPolls, ceil($totalPolls / $limit));
				//$response['total_post'] = $totalPolls;
				$response['current_page'] = (int)$page;
				$response['total_page'] = ceil($totalPolls / $limit);
				$response['status'] = '200';
				$response['messages'] = 'Polls listings';					
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
	
	public function likepoll(){
		$userauth = array();
		$userauth['users_id']  = $this->input->get_request_header('User-ID', TRUE);
        $userauth['token']     = $this->input->get_request_header('Auth-Key', TRUE);
		if(checkForUserSession($userauth) == 1){
			$post = $this->input->post();
			if(isset($post) && !empty($post)){
				$data['poll']['pollingId'] = $post['pollID'];
				$data['poll']['pollingAnswerId'] = $post['answerID'];
				$data['poll']['status'] = $post['likeval'];
				$data['poll']['createdBy'] = $post['userid'];
				$data['poll']['updatedBy'] = $post['userid'];
				$result = $this->polls_model->update_votepolls($data);
				
				$pollAnswer = getPollsanswer($post['pollID']);
				if(!empty($pollAnswer)){
					foreach($pollAnswer as $pans){
						$pollsarray[] = array(
							'answerId' => $pans['pollingAnswerId'],
							'answer' => $pans['answer'],
							'likecnt' => getTotalpollcnt($pans['pollingAnswerId'], $post['pollID']).'%',
							'selected' => getMyanswerId($userauth['users_id'], $post['pollID'], $pans['pollingAnswerId']),
						);
					}
				}
				$response['polls'] = $pollsarray;
				$response['status'] = '200';
				if($result == ""){
					$response['messages'] = 'Poll answered successfully.';
				} else if($result){
					if($post['likeval'] == 1){
						$select=array();
						$where=array();		
						$where['poll.pollingId'] = $post['pollID'];
						$newsfeeds = $this->polls_model->get($where,$select);
						foreach($newsfeeds as $feeds){	
							$userdetails = $this->users_model->getUseriddetails($feeds->createdBy);
							$userdetailsSent = $this->users_model->getUseriddetails($userauth['users_id']);
							
							$notify_data = array(
								array(
									'fromUserId'    => $userauth['users_id'],
									'toUserId'      => $userauth['users_id'],
									'notifyType'	=> 'polls',
									'notifyId' 		=> $post['pollID'],
									'notifyReason'	=> 'You answered <b>'.$feeds->pollingQuestion.'<b> posted by <b>Admin<b>',
									'createdBy'		=> $userauth['users_id'],							
									'updatedBy'		=> $userauth['users_id'],
								)
							);
							$receiveRequest = $this->notifications_model->notificationsLog($notify_data);
						}
						$response['messages'] = 'Poll answered successfully.';	
					}
				}
				json_output($response['status'],$response);
			} else {
				$response['status'] = '401';
				$response['messages'] = 'Poll answer not added. Try again.';					
				json_output($response['status'],$response);
			}
		} else {
			$response['status'] = '401';
			$response['messages'] = 'Your Authorization details are incorrect.';					
			json_output($response['status'],$response);
		}
	}
	
	public function getPolldetails($pid){
		$userauth = array();
		$userauth['users_id']  = $this->input->get_request_header('User-ID', TRUE);
        $userauth['token']     = $this->input->get_request_header('Auth-Key', TRUE);
		if(checkForUserSession($userauth) == 1){
			$data = array();
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
			
			$where['poll.status'] = 1;	
			$where['poll.endDate >='] = date("Y-m-d");	
			$where['poll.pollingId'] = $pid;			
			$orderby['poll.pollingId'] = 'DESC';
			$allPolls = $this->polls_model->get($where);	
			
			$response['newsfeeds'] = array();		
			if (!empty($allPolls)) {
				foreach($allPolls as $feeds){
					$userdetails = $this->users_model->getUseriddetails($feeds->createdBy);
					if($feeds->createdBy == 1){
						$createUser = 'Tamil Ethos';
					} else {
						$createUser = $userdetails->firstName;
					}					
					if($feeds->pollType == 'polls'){
						$mediaarray = array();
						$pollsarray = array();
						$attributearray = array(
							'likeCnt' => 0,
							'commentCnt' => 0,
							'myLike' => 0,
						);	
						
						$pollAnswer = getPollsanswer($feeds->pollingId);
						if(!empty($pollAnswer)){
							foreach($pollAnswer as $pans){
								$pollsarray[] = array(
									'answerId' => $pans['pollingAnswerId'],
									'answer' => $pans['answer'],
									'likecnt' => getTotalpollcnt($pans['pollingAnswerId'], $feeds->pollingId).'%',
									'selected' => getMyanswerId($userauth['users_id'], $feeds->pollingId, $pans['pollingAnswerId']),
								);
							}
						}
					}

					$response['newsfeeds'][] = array(
						'userId' => $userdetails->userid,
						'userName' => $createUser,
						'userImg' => $userdetails->profileimg,
						'feedID' => $feeds->pollingId,
						'feedTitle' => $feeds->pollingQuestion,
						'feedDescription' => $feeds->description,
						'feedType' => $feeds->pollType,
						'media' => $mediaarray,
						'attribute' => $attributearray,
						'polls' => $pollsarray,
						'feedcreateAt' => date_time_ago($feeds->createdDate),
						'feedexpireAt' => date_time_expire($feeds->endDate.''.$feeds->endTime),
						/*'shareLink' => 'http://www.tamilchamber.org.za/poll?id='.$feeds->pollingId,*/
						'shareLink' => $this->config->item('shareAddress').".page.link/?link=http://www.tamilchamber.org.poll/poll?id=".$feeds->pollingId."&apn=".$this->config->item('shareAndriodapn')."&amv=10&ibi=".$this->config->item('shareIOSibi')."&isi=".$this->config->item('shareIOSisi')."&ius=tamilethos",
					);
				}
				$response['status'] = '200';
				$response['messages'] = 'Polls listings';					
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
