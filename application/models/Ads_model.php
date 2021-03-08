<?php
class Ads_model extends CI_Model{

    function __construct()
    {
        parent::__construct();
    }
    public function get($where = array(), $orderby = array(), $select = "ad.*", $join = array(), $group_by = "", $limit ="", $offset = 0, $row = false, $like = array(),$or_like=array(),$or_where =array(),$where_in=array()) {
        if($select == "") $select = "ad.*";
        $this->db->select($select);
        $this->db->from("ads as ad");
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
	
	public function save_update_ads($data = array()){
		if(!empty($data['ads']['adsId'])){			
			$data['ads']['updatedDate'] = date('Y-m-d H:i:s');
			$this->db->where("adsId", $data['ads']['adsId']);
			$this->db->update('ads', $data['ads']);
			return $data['ads']['adsId'];
        } else {
			$data['ads']['status'] = 1;
            $data['ads']['createdDate'] = date('Y-m-d H:i:s');
            $data['ads']['updatedDate'] = date('Y-m-d H:i:s');
            $this->db->insert('ads', $data['ads']);
            $affected_rows = $this->db->insert_id();
            return $affected_rows;
        }
	}
	
	public function getadsCheck($data = array()){
        $this->db->select('*'); 
        $this->db->where('title', trim($data['title']));
        if(isset($data['adsId'])){
            $this->db->where('adsId !=', $data['adsId']);
        }           
        $get =  $this->db->get('ads');
        $resVal = $get->result();
        if($resVal) {           
            return true;
        } else {
            return false;
        }
    }
	
}
