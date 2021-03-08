<?php
class Admin_model extends CI_Model{

    function __construct()
    {
        parent::__construct();
    }
    public function get($where = array(), $orderby = array(), $select = "us.*", $join = array(), $group_by = "", $limit ="", $offset = 0, $row = false, $like = array(),$or_like=array(),$or_where =array(),$where_in=array()) {
        if($select == "") $select = "us.*";
        $this->db->select($select);
        $this->db->from("users as us");
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
	
    public function check_AdminLogin($data = array(), $set_session = true){
       $user = $this->db->get_where("users",array('emailid' =>trim($data["username"]), 'userrole' => 'admin', 'status' => 1));
       if($user->num_rows()==0) return false;
       if($user->num_rows()>0){
            $data_ret = $user->result_array();
            $password = $data['password'];
            $right_password = $data_ret[0]['password'];
			$validate_password = $this->phpass->check($password, $right_password);
			
			if($validate_password){
				
                if($set_session){
					$userdata = $user->row();
                    $user_data = array( "userid" =>$userdata->userid, "username" => $userdata->firstName, "userrole" => $userdata->userrole);
                    $this->session->set_userdata($user_data);
                }
                if($set_session == false){
                    return $data_ret[0]['userid'];
                }
                return true; 
            } else{
                return false;
            }
        } else {
           return false;
        }
    }
	
	public function save_update_account($data = array()) {
        if(!empty($data)){
			unset($data['conpassword']);
			$data['pass']['userid'] = 1;
			$data["pass"]['password'] = $this->phpass->hash($data['password']);
			$data["pass"]['dummypass'] = $data['password'];
			$this->db->where("userid", $data['pass']['userid']);
			$this->db->update('users', $data['pass']);
			return $data['pass']['userid'];
		} else {
			return false;
		}
    } 

	
}
