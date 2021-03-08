<?php
class Sitepages_model extends CI_Model{

    function __construct()
    {
        parent::__construct();
    }
    public function get($where = array(), $orderby = array(), $select = "sp.*", $join = array(), $group_by = "", $limit ="", $offset = 0, $row = false, $like = array(),$or_like=array(),$or_where =array(),$where_in=array()) {
        if($select == "") $select = "sp.*";
        $this->db->select($select);
        $this->db->from("sitepages as sp");
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

        $sitepages = $this->db->get();
        if($sitepages->num_rows() > 0){
            if(!$row) return $sitepages->result();
            return $sitepages->row();
        }
        return array();
    }
	
	public function save_update_sitepages($data = array()){
		if(!empty($data['page']['pageId'])){
			unset($data['submit']);
			$data['pageVal']['pagename'] = trim($data['page']['pagename']);
            $data['pageVal']['description'] = trim($data['page']['description']);
			$data['pageVal']['updatedDate'] = date('Y-m-d H:i:s');
			$data['pageVal']['updatedBy '] = $this->session->userid;
			$this->db->where("pageId", $data['page']['pageId']);
			$this->db->update('sitepages', $data['pageVal']);
			return $data['page']['pageId'];
        }else{
			unset($data['submit']);
            $data['pageVal']['pagename'] = trim($data['page']['pagename']);
            $data['pageVal']['description'] = trim($data['page']['description']);
            $data['pageVal']['status'] = 1;
            $data['pageVal']['createdDate'] = date('Y-m-d H:i:s');
            $data['pageVal']['createdBy'] = $this->session->userid;
            $data['pageVal']['updatedDate'] = date('Y-m-d H:i:s');
            $data['pageVal']['updatedBy'] = $this->session->userid;
            $this->db->insert('sitepages', $data['pageVal']);
            $affected_rows = $this->db->insert_id();
            return $affected_rows;
        }
	}	
}
