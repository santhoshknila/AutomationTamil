<?php
class Category_model extends CI_Model{

    function __construct()
    {
        parent::__construct();
    }
    public function get($where = array(), $orderby = array(), $select = "cat.*", $join = array(), $group_by = "", $limit ="", $offset = 0, $row = false, $like = array(),$or_like=array(),$or_where =array(),$where_in=array()) {
        if($select == "") $select = "cat.*";
        $this->db->select($select);
        $this->db->from("category as cat");
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
	
	public function save_update_category($data = array()){
		if(!empty($data['category']['categoryId'])){			
			$data['category']['updatedDate'] = date('Y-m-d H:i:s');
			$this->db->where("categoryId", $data['category']['categoryId']);
			$this->db->update('category', $data['category']);
			return $data['category']['categoryId'];
        } else {
			$data['category']['status'] = 1;
            $data['category']['createdDate'] = date('Y-m-d H:i:s');
            $data['category']['updatedDate'] = date('Y-m-d H:i:s');
            $this->db->insert('category', $data['category']);
            $affected_rows = $this->db->insert_id();
            return $affected_rows;
        }
	}
	
	public function getcategoryCheck($data = array()){
		if(isset($data['categoryId']) && !empty($data['categoryId'])){
			$sql = "AND categoryId IN ('".$data['categoryId']."')";
        } else {
			$sql = "";
		}
		
		if(isset($data['categoryIdval'])){
			$sql1 = "AND (parentId IN ('".$data['categoryIdval']."') AND parentId != 0)";
        } else {
			$sql1 = "";
		}
		$query = $this->db->query("SELECT * FROM category WHERE name='".trim($data['name'])."' $sql $sql1 LIMIT 1");
		$resVal = $query->result();
		//echo $sql = $this->db->last_query();
		//exit;
		if($resVal) {           
            return true;
        } else {
            return false;
        }
    }
	
}
