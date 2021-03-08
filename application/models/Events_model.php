<?php
class Events_model extends CI_Model{

    function __construct(){
        parent::__construct();
    }
	
	public function get($where = array(), $orderby = array(), $select = "event.*", $join = array(), $group_by = "", $limit ="", $offset = 0, $row = false, $like = array(),$or_like=array(),$or_where =array(),$where_in=array()) {
        if($select == "") $select = "event.*";
        $this->db->select($select);
        $this->db->from("events as event");
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

        $poll = $this->db->get();
		//echo $sql = $this->db->last_query();
		//exit;
        if($poll->num_rows() > 0){
            if(!$row) return $poll->result();
            return $poll->row();
        }
        return array();
    }
	
	public function save_update_events($data = array()){
		if(!empty($data['event']['eventId'])){
			$data['event']['isActive'] = $data['event']['status'];
			$data['event']['updatedDate'] = date('Y-m-d');
			$this->db->where("eventId", $data['event']['eventId']);
			$this->db->update('events', $data['event']);
			$affected_rows = $this->db->affected_rows();
			if(isset($data['eventimg']['fileTypeval'])){
				$uploadVal = $data['eventimg']['fileTypeval'];
				foreach($uploadVal as $key=>$up ){
					$newimg['eventimg']['eventId'] = $data['event']['eventId'];
					$newimg['eventimg']['fileType'] = $data['eventimg']['fileTypeval'][$key];
					$newimg['eventimg']['imagevideo_url'] = $data['eventimg']['imagevideo_url'][$key];
					$newimg['eventimg']['createdDate'] = date('Y-m-d H:i:s');
					$newimg['eventimg']['createdBy'] = $data['event']['createdBy'];
					$newimg['eventimg']['updatedDate'] = date('Y-m-d H:i:s');
					$newimg['eventimg']['updatedBy'] = $data['event']['createdBy'];
					$this->db->insert('eventimages', $newimg['eventimg']);
				}
				return $data['event']['eventId'];
			}
			return $data['event']['eventId'];
        } else {
			if(isset($data['event'])){
				$data['event']['isActive'] = $data['event']['status'];
				$data['event']['createdDate'] = date('Y-m-d H:i:s');
				$data['event']['updatedDate'] = date('Y-m-d H:i:s');				
				$this->db->insert('events', $data['event']);
				$lastId = $this->db->insert_id();
				$affected_rows = $this->db->affected_rows();
				if($affected_rows == 1){
					if(isset($data['eventimg']['fileTypeval'])){
						$uploadVal = $data['eventimg']['fileTypeval'];
						foreach($uploadVal as $key=>$up ){
							$newimg['eventimg']['eventId'] = $lastId;
							$newimg['eventimg']['fileType'] = $data['eventimg']['fileTypeval'][$key];
							$newimg['eventimg']['imagevideo_url'] = $data['eventimg']['imagevideo_url'][$key];
							$newimg['eventimg']['createdDate'] = date('Y-m-d H:i:s');
							$newimg['eventimg']['createdBy'] = $data['event']['createdBy'];
							$newimg['eventimg']['updatedDate'] = date('Y-m-d H:i:s');
							$newimg['eventimg']['updatedBy'] = $data['event']['createdBy'];
							$this->db->insert('eventimages', $newimg['eventimg']);
						}
						return $lastId;
					}
					return $lastId;
				} else {
					return false;
				}
			} else {
				return false;
			}
        }
	}
	
	public function getallEventdetails($data = array()){
		$startTime = date("H:i");
		$endTime = date("H:i");	
		
		$this->db->select('*');
		$this->db->from('events');
		$this->db->where('status',1);
		$this->db->where('isActive',1);
		if($data['type'] == 'today' || $data['type'] == 'tomorrow'){			
			$this->db->where('(startDate <= date("'.$data['startDate'].'") AND endDate >= date("'.$data['endDate'].'"))');
		} else if($data['type'] == 'week'){
			$this->db->where('((startDate BETWEEN date("'.$data['startDate'].'") AND date("'.$data['endDate'].'") ) OR ( endDate BETWEEN date("'.$data['startDate'].'") AND date("'.$data['endDate'].'")))');
		}else if($data['type'] == 'month')
		{
		    $this->db->where(' (( YEAR(`startdate`)='.$data['startDate'].' AND  MONTH(`startdate`)='.$data['endDate'].' ) OR ( YEAR(`endDate`)='.$data['startDate'].' AND MONTH(`endDate`)='.$data['endDate'].' ) )');
		}
		else if($data['type'] == 'year')
		{
		    $this->db->where(' (YEAR(`startdate`)='.$data['startDate'].' OR YEAR(`endDate`)='.$data['startDate'].' )');
		}
		$this->db->order_by('startDate', 'DESC');
		$this->db->limit($data['limit']);
		$this->db->offset($data['start']);
		$query = $this->db->get();
		
		$eventDetails = $query->result_array();		
		return $eventDetails;
	}
	
	public function getalltotalEventdetails($data = array()){		
		$this->db->select('*');
		$this->db->from('events');
		$this->db->where('status',1);
		$this->db->where('isActive',1);
		if($data['type'] == 'today' || $data['type'] == 'tomorrow'){			
			$this->db->where('(startDate <= date("'.$data['startDate'].'") AND endDate >= date("'.$data['endDate'].'"))');
		} else if($data['type'] == 'week'){
			$this->db->where('((startDate BETWEEN date("'.$data['startDate'].'") AND date("'.$data['endDate'].'") ) OR ( endDate BETWEEN date("'.$data['startDate'].'") AND date("'.$data['endDate'].'")))');
		}else if($data['type'] == 'month')
		{
		    $this->db->where(' (( YEAR(`startdate`)='.$data['startDate'].' AND  MONTH(`startdate`)='.$data['endDate'].' ) OR (YEAR(`startdate`)='.$data['startDate'].' AND  MONTH(`endDate`)='.$data['endDate'].' )) ');
		}
		else if($data['type'] == 'year')
		{
		    $this->db->where('( YEAR(`startdate`)='.$data['startDate'].' OR YEAR(`endDate`)='.$data['startDate'].' )');
		}
		$this->db->order_by('startDate', 'DESC');
		$query = $this->db->get();
		$eventDetails = $query->result_array();
		//echo $sql = $this->db->last_query();
		//exit;
		return $eventDetails;
	}
	
	public function getallEventDates(){
		$this->db->select('*');
		$this->db->from('events');
		$this->db->where('status',1);
		$this->db->where('isActive',1);
		//$this->db->where('startDate <=', date("Y-m-d"));
		$query = $this->db->get();
		$eventallDates = $query->result_array();
		//echo $sql = $this->db->last_query();
		//exit;
		return $eventallDates;
	}
	
	public function getEventdetails($evtId){
		$this->db->select('*');
		$this->db->from('events');
		$this->db->where('status',1);
		$this->db->where('eventId', $evtId);
		$query = $this->db->get();
		$event = $query->result();
		return $event;
	}
	
	public function getEvent($evtId){
		$this->db->select('*');
		$this->db->from('events');
		$this->db->where('eventId', $evtId);
		$query = $this->db->get();
		$event = $query->result();
		return $event;
	}
	
	public function getEventmedia($evtId){
		$this->db->select('*');
		$this->db->from('eventimages');
		$this->db->where('eventId', $evtId);
		$query = $this->db->get();
		$eventMedia = $query->result_array();
		return $eventMedia;
	}
	
	public function goingStatus($userID, $evtId){
		$this->db->select('*');
		$this->db->from('eventsgoing');
		$this->db->where('createdBy', $userID);
		$this->db->where('eventId', $evtId);
		$this->db->where('status', 1);
		$query = $this->db->get();
		$goingSts = $query->result();
		return $goingSts;
	}
	
	public function interestStatus($userID, $evtId){
		$this->db->select('*');
		$this->db->from('eventsinterest');
		$this->db->where('createdBy', $userID);
		$this->db->where('eventId', $evtId);
		$this->db->where('status', 1);
		$query = $this->db->get();
		$goingSts = $query->result();
		return $goingSts;
	}
	
	public function save_update_goingstaus($data = array()){
		$goingrow = $this->db->select('*')->from('eventsgoing')->where('eventId',$data['goSts']['eventId'])->where('createdBy',$data['goSts']['createdBy'])->get()->num_rows();	
		if($goingrow == 0) {
			$this->db->query("INSERT INTO eventsgoing SET eventId = '".(int)$data['goSts']['eventId']."', status = '" .(int)$data['goSts']['status']."', createdDate = NOW(), createdBy = '" . (int)$data['goSts']['createdBy'] . "', updatedDate = NOW(),  updatedBy = '" . (int)$data['goSts']['updatedBy'] . "'");
			return true;
		} else {
			$this->db->query("UPDATE eventsgoing SET status = '" .(int)$data['goSts']['status']. "', updatedDate = NOW() WHERE eventId = '".(int)$data['goSts']['eventId']."' AND createdBy = '" . (int)$data['goSts']['createdBy'] . "'");
			return true;
		}
	}
	
	public function save_update_intereststaus($data = array()){
		$goingrow = $this->db->select('*')->from('eventsinterest')->where('eventId',$data['interSts']['eventId'])->where('createdBy',$data['interSts']['createdBy'])->get()->num_rows();		
		if($goingrow == 0) {
			$this->db->query("INSERT INTO eventsinterest SET eventId = '".(int)$data['interSts']['eventId']."', status = '" .(int)$data['interSts']['status']."', createdDate = NOW(), createdBy = '" . (int)$data['interSts']['createdBy'] . "', updatedDate = NOW(),  updatedBy = '" . (int)$data['interSts']['updatedBy'] . "'");
			return true;
		} else {
			$this->db->query("UPDATE eventsinterest SET status = '" .(int)$data['interSts']['status']. "', updatedDate = NOW() WHERE eventId = '".(int)$data['interSts']['eventId']."' AND createdBy = '" . (int)$data['interSts']['createdBy'] . "'");
			return true;
		}
	}
	
	public function getEventcomments($data=array()){
		$eventComments = $this->db->select('*')->from('eventscomments')->where('eventId',$data['eventId'])->limit($data['limit'])->offset($data['start'])->order_by('createdDate', 'DESC')->get()->result_array();
		return $eventComments;
	}
	
	public function getTotaleventCntcomments($evtID){
		$eventTotalComments = $this->db->select('*')->from('eventscomments')->where('eventId',$evtID)->order_by('createdDate', 'DESC')->get()->result_array();
		return $eventTotalComments;
	}
	
	public function save_update_commentsevents($data){
		$data['cmt']['status'] = 1;			
		$data['cmt']['createdDate'] = date('Y-m-d H:i:s');
		$data['cmt']['updatedDate'] = date('Y-m-d H:i:s');
		$this->db->insert('eventscomments', $data['cmt']);
		$lastId = $this->db->insert_id();
		$affected_rows = $this->db->affected_rows();
		return $affected_rows;		
	}
	
	public function goingStatuscnt($evtID){
		$eventTotalGoingstscnt = $this->db->select('*')->from('eventsgoing')->where('eventId',$evtID)->where('status',1)->get()->num_rows();
		return $eventTotalGoingstscnt;
	}
	
	public function interestStatuscnt($evtID){
		$eventTotalinterstscnt = $this->db->select('*')->from('eventsinterest')->where('eventId',$evtID)->where('status',1)->get()->num_rows();
		return $eventTotalinterstscnt;
	}
	
	public function getEventmedialist($evtId){
		$this->db->select('*');
		$this->db->from('eventimages');
		$this->db->where('eventId', $evtId);
		$this->db->where('fileType', 'image');
		$this->db->order_by('createdDate', 'DESC');
		$this->db->limit(1);
		$query = $this->db->get();
		$eventMedialist = $query->result_array();
		return $eventMedialist;
	}
	
	public function del_removeUploadfiles($data = array()){
		$this->db->where('eventImageId', $data['del']['uploadRowID']);
		$this->db->delete('eventimages');
		return true;
	}
	
	public function save_update_eventDelete($data = array()){
		$data['del']['status'] = 3;
		$data['del']['updatedDate'] = date('Y-m-d H:i:s');
		$this->db->where("eventId", $data['del']['eventId']);
		unset($data['del']['eventId']);
		$this->db->update('events', $data['del']);
		//echo $this->db->last_query();
		//exit;
		$affected_rows = $this->db->affected_rows();
		return $affected_rows;
	}
}
