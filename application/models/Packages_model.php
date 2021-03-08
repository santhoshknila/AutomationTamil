<?php
class Packages_model extends CI_Model{

    function __construct()
    {
        parent::__construct();
    }
    public function get($where = array(), $orderby = array(), $select = "pack.*", $join = array(), $group_by = "", $limit ="", $offset = 0, $row = false, $like = array(),$or_like=array(),$or_where =array(),$where_in=array()) {
        if($select == "") $select = "pack.*";
        $this->db->select($select);
        $this->db->from("packages as pack");
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
	
	public function save_update_packages($data = array()){
		if(!empty($data['package']['packageId'])){			
			$data['package']['updatedDate'] = date('Y-m-d H:i:s');
			$this->db->where("packageId", $data['package']['packageId']);
			$this->db->update('packages', $data['package']);
			return $data['package']['packageId'];
        } else {
			$data['package']['status'] = 1;
            $data['package']['createdDate'] = date('Y-m-d H:i:s');
            $data['package']['updatedDate'] = date('Y-m-d H:i:s');
            $this->db->insert('packages', $data['package']);
            $affected_rows = $this->db->insert_id();
            return $affected_rows;
        }
	}
	
	public function getpackageCheck($data = array()){
        $this->db->select('*'); 
        $this->db->where('title', trim($data['title']));
        if(isset($data['packageId'])){
            $this->db->where('packageId !=', $data['packageId']);
        }           
        $get =  $this->db->get('packages');
        $resVal = $get->result();
        if($resVal) {           
            return true;
        } else {
            return false;
        }
    }
	
}
