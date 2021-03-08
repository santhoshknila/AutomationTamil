<?php
class Notifications_model extends CI_Model{

    function __construct()
    {
        parent::__construct();
    }
    public function get($where = array(), $orderby = array(), $select = "notify.*", $join = array(), $group_by = "", $limit ="", $offset = 0, $row = false, $like = array(),$or_like=array(),$or_where =array(),$where_in=array()) {
        if($select == "") $select = "notify.*";
        $this->db->select($select);
        $this->db->from("notifications as notify");
		if(is_array($join) && !empty($join)) {
            foreach($join as $k=>$v){
                if(is_array($v)) $this->db->join($k, $v[0], $v[1]);
                else $this->db->join($k, $v);
            }
        }

        if(is_array($where_in) && !empty($where_in)) {
            foreach($where_in as $k=>$v){
                if(is_array($v)) $this->db->where_in($k, $v);
                else $this->db->where_in($k, $v);
            }
        }

		if(is_array($where)) {
            if(!empty($where)) $this->db->where($where);
        } elseif($where != "") {
            $this->db->where($where);
        }

		if(is_array($or_where)) {
            if(!empty($or_where)) $this->db->or_where($or_where);
        } elseif($or_where != "") {
            $this->db->where($or_where);
        }

        if(is_array($like)) {
            if(!empty($like)) {

                $this->db->like($like);
                $this->db->or_like($or_like);
            }
        } elseif($like != "") {
            $this->db->like($like);
        }

        if(is_array($orderby) && !empty($orderby)) {
            foreach($orderby as $k=>$v){
                $this->db->order_by($k, $v);
            }
        }

        if($group_by) $this->db->group_by($group_by);

        if((int)$limit != 0) $this->db->limit($limit, $offset);

        $users = $this->db->get();
        if($users->num_rows() > 0){
            if(!$row) return $users->result();
            return $users->row();
        }
        return array();
    }
	
	public function save_update_notify($data = array()){
		if(!empty($data['notify']['id'])){	
			$this->db->where("notifyId", $data['notify']['id']);
			$this->db->update('notifications', $data['notify']);
			return $data['notify']['id'];
        }else{
            $data['notify']['createdBy'] = $this->session->userid;
            $data['notify']['createdDate'] = date('Y-m-d H:i:s');
            $this->db->insert('notifications', $data['notify']);
            $affected_rows = $this->db->insert_id();
            return $affected_rows;
        }
	}
	
	public function getTokens(){
		$this->db->select('*');
		$this->db->where('deviceToken !=', '');
		$this->db->where('pushNotification', 1);
		$this->db->where('status', 1);
		$this->db->group_by('deviceToken');
		$this->db->from('users');
		$get =  $this->db->get();
		//echo $this->db->last_query();
		//exit;
     	return $get->result_array();
	}
	
	public function getPollTokens(){
		$this->db->select('*');
		$this->db->where('deviceToken !=', '');
		$this->db->where('pollNotification', 1);
		$this->db->where('status', 1);
		$this->db->group_by('deviceToken');
		$this->db->from('users');
		$get =  $this->db->get();
	    //echo $this->db->last_query();
	    //exit;
		return $get->result_array();
	}
	
	public function delete($id){
		if(empty($id)) return false;
        $result = $this->db->delete("notifications",array("notifyId" =>$id));		
        return $result;
	}
	
	public function notificationsLog($notify = array()){
		foreach($notify as $data ){
			$data['createdDate'] = date('Y-m-d H:i:s');
			$data['updatedDate'] = date('Y-m-d H:i:s');
			$this->db->insert('notificationslog', $data);
		}
		$affected_rows = $this->db->insert_id();
		return $affected_rows;
	}
	
	public function getNotify($data = array()){		
		/*$query = $this->db->query("SELECT nl.notifylogId, nl.fromUserId, nl.toUserId, nl.notifyType, nl.readStatus, nl.createdDate, nl.createdBy, nl.updatedDate, nl.updatedBy, 
		CASE WHEN (nl.fromUserId = '".$data['loggedUser']."' AND nl.notifyReason!='become friends.' AND nl.notifyType = 'Friend') THEN 'request sent' ELSE nl.notifyReason END AS notifyReason,
		CASE WHEN (nl.notifyType = 'GPCreate' AND nl.fromUserId = '".$data['loggedUser']."' ) THEN 'Group invite sent' ELSE nl.notifyReason END AS notifyReason
		FROM notificationslog AS nl WHERE (nl.fromUserId = '".$data['loggedUser']."' OR nl.toUserId = '".$data['loggedUser']."') ORDER BY nl.notifylogId DESC limit ".(int)$data['limit']." offset ".(int)$data['start']); */
		
		/*$query = $this->db->query("SELECT nl.notifylogId, nl.fromUserId, nl.toUserId, nl.notifyType, nl.readStatus, nl.createdDate, nl.createdBy, nl.updatedDate, nl.updatedBy, 
		CASE WHEN nl.notifyType = 'Friend' THEN (
			CASE WHEN (nl.fromUserId = '".$data['loggedUser']."' AND nl.notifyReason!='become friends.' AND nl.notifyType = 'Friend') THEN 'You have friend request to' ELSE nl.notifyReason END) 
		WHEN nl.notifyType = 'GPCreate' THEN ( 
			CASE WHEN (nl.notifyType = 'GPCreate' AND nl.fromUserId = '".$data['loggedUser']."' ) THEN 'Group invite sent' ELSE nl.notifyReason END ) 
		WHEN nl.notifyType = 'Group' THEN ( 
			CASE WHEN (nl.notifyType = 'Group' AND nl.fromUserId = '".$data['loggedUser']."' ) THEN 'Group invite sent to' ELSE nl.notifyReason END )
		WHEN nl.notifyType = 'admin' THEN ( 
			CASE WHEN (nl.notifyType = 'admin' AND nl.fromUserId = '1' ) THEN nl.notifyReason ELSE nl.notifyReason END )
		WHEN nl.notifyType = 'Event Notifications' THEN ( 
			CASE WHEN (nl.notifyType = 'Event Notifications' AND nl.fromUserId = '1' AND nl.toUserId = '".$data['loggedUser']."' ) THEN nl.notifyReason ELSE nl.notifyReason END )
		WHEN nl.notifyType = 'Newfedds' THEN ( 
			CASE WHEN (nl.notifyType = 'Newfedds' AND nl.fromUserId = '1' AND nl.toUserId = '".$data['loggedUser']."' ) THEN nl.notifyReason ELSE nl.notifyReason END )
		WHEN nl.notifyType = 'Newsfeeds' THEN ( 
			CASE WHEN (nl.notifyType = 'Newsfeeds' AND nl.fromUserId = '".$data['loggedUser']."' AND nl.toUserId = '1' ) THEN nl.notifyReason ELSE nl.notifyReason END )
		WHEN nl.notifyType = 'Polls' THEN ( 
			CASE WHEN (nl.notifyType = 'Polls' AND nl.fromUserId = '1' AND nl.toUserId = '".$data['loggedUser']."' ) THEN nl.notifyReason ELSE nl.notifyReason END )
		WHEN nl.notifyType = 'GPPost' THEN ( 
			CASE WHEN (nl.notifyType = 'Polls' AND nl.fromUserId = '1' AND nl.toUserId = '".$data['loggedUser']."' ) THEN nl.notifyReason ELSE nl.notifyReason END )
		WHEN nl.notifyType = 'Event' THEN ( 
			CASE WHEN (nl.notifyType = 'Event' AND nl.fromUserId = '1' AND nl.toUserId = '".$data['loggedUser']."' ) THEN nl.notifyReason ELSE nl.notifyReason END )
		END AS notifyReason 
		FROM notificationslog AS nl WHERE (nl.fromUserId = '".$data['loggedUser']."' OR nl.toUserId = '".$data['loggedUser']."') ORDER BY nl.notifylogId DESC limit ".(int)$data['limit']." offset ".(int)$data['start']);*/

		/*$query = $this->db->query("SELECT * FROM notificationslog WHERE toUserId = '".$data['loggedUser']."' and toUserId != fromUserId
union
SELECT * FROM notificationslog WHERE toUserId in (
SELECT receiveRequestId FROM `friends` where sendRequestUserId='".$data['loggedUser']."' and status=1) and fromUserId in (SELECT receiveRequestId FROM `friends` where sendRequestUserId='".$data['loggedUser']."' and status=1) and toUserId != fromUserId and notifyId=999
ORDER BY notifylogId DESC limit ".(int)$data['limit']." offset ".(int)$data['start']);
		*/
	
		/*$query = $this->db->query("SELECT * FROM notificationslog WHERE toUserId = '".$data['loggedUser']."' ORDER BY notifylogId DESC limit ".(int)$data['limit']." offset ".(int)$data['start']);*/

		
		if(isset($data['readUpdate'])){
			$read['readStatus'] = $data['readUpdate'];
			$read['updatedDate'] = date('Y-m-d H:i:s');
			$this->db->where('toUserId', $data['loggedUser']);
			$this->db->update('notificationslog', $read);		
		}
		
		
		
		$friends = $this->db->query("select * from friends where (sendRequestUserId='".$data['loggedUser']."' or receiveRequestId='".$data['loggedUser']."' ) and status=1 ")->result_array();

		$fData = array();
		foreach ($friends as $key => $frd) 
		{

			if( $frd['receiveRequestId'] != $data['loggedUser']) 
			{
			array_push($fData,$frd['receiveRequestId']);
			}

			if($frd['sendRequestUserId'] != $data['loggedUser'] ) 
			{
			array_push($fData,$frd['sendRequestUserId']);
			}
			 
		}
		 
		$fData=array_unique($fData);
		$fData = implode($fData, ',');
 
		
		if(!empty($fData))
		{

		$query = $this->db->query("SELECT * FROM notificationslog WHERE toUserId = '".$data['loggedUser']."' and toUserId != fromUserId and notifyType!='newsOther' union
		SELECT * FROM notificationslog WHERE toUserId in (".$fData.") and fromUserId in (".$fData.")    and notifyType='newsOther'
		ORDER BY notifylogId DESC limit ".(int)$data['limit']." offset ".(int)$data['start']);
		}else{
		$query = $this->db->query("SELECT * FROM notificationslog WHERE toUserId = '".$data['loggedUser']."' and toUserId != fromUserId  
		ORDER BY notifylogId DESC limit ".(int)$data['limit']." offset ".(int)$data['start']);
		}

		
		//echo $this->db->last_query();
		//exit;
		return $myNotify = $query->result_array();
	}
	
	public function getNotifyTotal($data=array()){	
		$myNotifytot = $this->db->select('*')->from('notificationslog')->where("toUserId = '".$data['loggedUser']."'", NULL, FALSE)->get()->result_array();
		return $myNotifytot;
	}
	
	public function getunreadNotifyTotal($data=array()){	
		$myunreadNotifytot = $this->db->select('*')->from('notificationslog')->where("toUserId != fromUserId")->where("toUserId = '".$data['loggedUser']."'", NULL, FALSE)->where('readStatus', 0)->get()->result_array();
		return $myunreadNotifytot;
	}
			
	public function updateReadstatus($data=array()){	
		if(isset($data['notifylogId'])){
			$read['readStatus'] = $data['readStatus'];
			$read['updatedDate'] = date('Y-m-d H:i:s');
			$this->db->where('notifylogId', $data['notifylogId']);
			$this->db->update('notificationslog', $read);
			return true;			
		} else {
			return false;
		}
	}
	
	public function getNotifystatus($notifyId){	
		$notify = $this->db->select('*')->from('notificationslog')->where("notifylogId", $notifyId)->get()->result_array();
		return $notify;
	}
	
	
}
