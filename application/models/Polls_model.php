<?php
class Polls_model extends CI_Model{

    function __construct()
    {
        parent::__construct();
    }
    public function get($where = array(), $orderby = array(), $select = "poll.*", $join = array(), $group_by = "", $limit ="", $offset = 0, $row = false, $like = array(),$or_like=array(),$or_where =array(),$where_in=array()) {
        if($select == "") $select = "poll.*";
        $this->db->select($select);
        $this->db->from("polling as poll");
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
	
	public function save_update_polling($data = array()){
		if(!empty($data['polls']['pollingId'])){
			$data['polls']['updatedDate'] = date('Y-m-d H:i:s');
			$data['polls']['updatedBy'] = $this->session->userid;
			$this->db->where("pollingId", $data['polls']['pollingId']);
			$this->db->update('polling', $data['polls']);
			$affected_rows = $this->db->affected_rows();
			if($affected_rows == 1){
				$uploadOption = $data['poll']['options'];
				if(!empty($uploadOption)){
					foreach($uploadOption as $key=>$up ){
						if(isset($data['poll']['pollingAnswerId'][$key])){
							$pollOpt['opt']['answer'] = $data['poll']['options'][$key];
							$pollOpt['opt']['updatedDate'] = date('Y-m-d H:i:s');
							$pollOpt['opt']['updatedBy'] = $this->session->userid;
							$this->db->where("pollingAnswerId", $data['poll']['pollingAnswerId'][$key]);
							$this->db->update('pollinganswers', $pollOpt['opt']);						
						} else {
							$pollOpt['opt']['pollingId'] = $data['polls']['pollingId'];
							$pollOpt['opt']['answer'] = $data['poll']['options'][$key];
							$pollOpt['opt']['status'] = 1;
							$pollOpt['opt']['createdDate'] = date('Y-m-d H:i:s');
							$pollOpt['opt']['createdBy'] = $this->session->userid;
							$pollOpt['opt']['updatedDate'] = date('Y-m-d H:i:s');
							$pollOpt['opt']['updatedBy'] = $this->session->userid;
							$this->db->insert('pollinganswers', $pollOpt['opt']);
						}
					}
					return $data['polls']['pollingId'];
				}
				return false;
			}
        }else{
			if(isset($data['polls'])){
				$data['polls']['pollType'] = 'polls';
				$data['polls']['status'] = 1;
				$data['polls']['isActive'] = 1;
				$data['polls']['createdDate'] = date('Y-m-d H:i:s');
				$data['polls']['createdBy'] = $this->session->userid;
				$data['polls']['updatedDate'] = date('Y-m-d H:i:s');
				$data['polls']['updatedBy'] = $this->session->userid;
				$this->db->insert('polling', $data['polls']);
				$lastId = $this->db->insert_id();
				$affected_rows = $this->db->affected_rows();
				if($affected_rows == 1){
					$uploadOption = $data['poll']['options'];
					foreach($uploadOption as $key=>$up ){
						$pollOpt['opt']['pollingId'] = $lastId;
						$pollOpt['opt']['answer'] = $data['poll']['options'][$key];
						$pollOpt['opt']['status'] = 1;
						$pollOpt['opt']['createdDate'] = date('Y-m-d H:i:s');
						$pollOpt['opt']['createdBy'] = $this->session->userid;
						$pollOpt['opt']['updatedDate'] = date('Y-m-d H:i:s');
						$pollOpt['opt']['updatedBy'] = $this->session->userid;
						$this->db->insert('pollinganswers', $pollOpt['opt']);
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
	
	public function update_votepolls($data = array()){
		if($data['poll']['status']== 0){
			$this->db->where('pollingId', $data['poll']['pollingId']);
			$this->db->where('createdBy', $data['poll']['createdBy']);
			$this->db->delete('pollinganswerbyuser');
			return true;
		} else {
			$pollrow = $this->db->select('*')->from('pollinganswerbyuser')->where('pollingId',$data['poll']['pollingId'])->where('createdBy',$data['poll']['createdBy'])->get()->num_rows();
			if($pollrow == 0){				
				$this->db->query("INSERT INTO pollinganswerbyuser SET pollingId = '".(int)$data['poll']['pollingId']."', pollingAnswerId = '".(int)$data['poll']['pollingAnswerId']."', status = '" .(int)$data['poll']['status'] . "', createdDate = NOW(), createdBy = '".(int)$data['poll']['createdBy']."', updatedDate = NOW(),  updatedBy = '".(int)$data['poll']['updatedBy']."'");
				return true;
			} else {
				$this->db->query("UPDATE pollinganswerbyuser SET status = '" .(int)$data['poll']['status']. "', pollingAnswerId = '" . (int)$data['poll']['pollingAnswerId'] . "', updatedDate = NOW() WHERE pollingId = '".(int)$data['poll']['pollingId']."' AND createdBy = '" . (int)$data['poll']['createdBy'] . "'");
				return true;
			}
		}
	}
	
	public function updatestatusPolls($data = array()){
		
	}
}
