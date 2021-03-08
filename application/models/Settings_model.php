<?php
class Settings_model extends CI_Model{

    function __construct()
    {
        parent::__construct();
    }
	
	/*District functionality here*/
    public function getdistrict($where = array(), $orderby = array(), $select = "dt.*", $join = array(), $group_by = "", $limit ="", $offset = 0, $row = false, $like = array(),$or_like=array(),$or_where =array(),$where_in=array()) {
        if($select == "") $select = "dt.*";
        $this->db->select($select);
        $this->db->from("district as dt");
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
	
	public function save_insert_district($data = array()){
		if(!empty($data['districtId'])){
			$data['dt']['districtName'] = $data['districtName'];
			$data['dt']['provinceId'] = $data['provinceName'];
			$data['dt']['status'] = 1;
			$data['dt']['createdDate'] = date('Y-m-d H:i:s');
			$data['dt']['creadtedBy'] = $this->session->userid;
			$data['dt']['updatedDate'] = date('Y-m-d H:i:s');
			$data['dt']['updatedBy'] = $this->session->userid;
			$this->db->where("districtId", $data['districtId']);
			$this->db->update('district', $data['dt']);
			return $data['districtId'];
		} else {
			$data['dt']['districtName'] = $data['districtName'];
			$data['dt']['provinceId'] = $data['provinceName'];
			$data['dt']['status'] = 1;
			$data['dt']['createdDate'] = date('Y-m-d H:i:s');
			$data['dt']['creadtedBy'] = $this->session->userid;
			$data['dt']['updatedDate'] = date('Y-m-d H:i:s');
			$data['dt']['updatedBy'] = $this->session->userid;
			$this->db->insert('district', $data['dt']);
			$affected_rows = $this->db->affected_rows();
			$inserted_id = $this->db->insert_id();
			return $inserted_id;
		}
	}	
		
	public function getdistrictCheck($data = array()){
		$this->db->select('*');		
		$this->db->where('provinceId', $data['province']);		
		$this->db->where('districtName', $data['distName']);		
		$get =  $this->db->get('district');
		$resVal = $get->result();
		if($resVal) {           
            return true;
        } else {
			return false;
		}
	}
	
	/*City functionality here*/
	public function getCity($where = array(), $orderby = array(), $select = "ct.*", $join = array(), $group_by = "", $limit ="", $offset = 0, $row = false, $like = array(),$or_like=array(),$or_where =array(),$where_in=array()) {
        if($select == "") $select = "ct.*";
        $this->db->select($select);
        $this->db->from("city as ct");
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
	
	public function save_insert_city($data = array()){
		if(!empty($data['cityId'])){
			$data['ct']['cityName'] = $data['cityName'];
			$data['ct']['districtId'] = $data['districtId'];
			$data['ct']['status'] = 1;
			$data['ct']['createdDate'] = date('Y-m-d H:i:s');
			$data['ct']['creadtedBy'] = $this->session->userid;
			$data['ct']['updatedDate'] = date('Y-m-d H:i:s');
			$data['ct']['updatedBy'] = $this->session->userid;
			$this->db->where("cityId", $data['cityId']);
			$this->db->update('city', $data['ct']);
			return $data['cityId'];
		} else {
			$data['ct']['cityName'] = $data['cityName'];
			$data['ct']['districtId'] = $data['districtId'];
			$data['ct']['status'] = 1;
			$data['ct']['createdDate'] = date('Y-m-d H:i:s');
			$data['ct']['creadtedBy'] = $this->session->userid;
			$data['ct']['updatedDate'] = date('Y-m-d H:i:s');
			$data['ct']['updatedBy'] = $this->session->userid;
			$this->db->insert('city', $data['ct']);
			$affected_rows = $this->db->affected_rows();
			$inserted_id = $this->db->insert_id();
			return $inserted_id;
		}
	}	
		
	public function getcityCheck($data = array()){
		$this->db->select('*');		
		$this->db->where('districtId', $data['districtId']);		
		$this->db->where('cityName', $data['cityName']);		
		$get =  $this->db->get('city');
		$resVal = $get->result();
		if($resVal) {           
            return true;
        } else {
			return false;
		}
	}
	
	/*General Helper functions*/
	public function getProvincename($id){
		$this->db->select("provinceId, provinceName");
        $this->db->from('province');
		$this->db->where('provinceId',$id);
        $query = $this->db->get();
        return $query->result();
	}
	
	public function getDistrictname($id){
		$this->db->select("districtId, districtName");
        $this->db->from('district');
		$this->db->where('districtId',$id);
		$this->db->where('status', 1);
        $query = $this->db->get();
        return $query->result();
	}
	
	/*Religion functionality */
	public function getReligion($where = array(), $orderby = array(), $select = "re.*", $join = array(), $group_by = "", $limit ="", $offset = 0, $row = false, $like = array(),$or_like=array(),$or_where =array(),$where_in=array()) {
        if($select == "") $select = "re.*";
        $this->db->select($select);
        $this->db->from("religion as re");
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

        $religion = $this->db->get();
        if($religion->num_rows() > 0){
            if(!$row) return $religion->result();
            return $religion->row();
        }
        return array();
    }
	
	public function save_insert_religion($data = array()){
		if(!empty($data['religionId'])){
			$data['re']['religionName'] = $data['religionName'];
			$data['re']['religionColor'] = $data['religionColor'];
			$data['re']['status'] = 1;
			$data['re']['updatedDate'] = date('Y-m-d H:i:s');
			$data['re']['updatedBy'] = $this->session->userid;
			$this->db->where("religionId", $data['religionId']);
			$this->db->update('religion', $data['re']);
			return $data['religionId'];
		} else {
			$data['re']['religionName'] = $data['religionName'];
			$data['re']['religionColor'] = $data['religionColor'];
			$data['re']['status'] = 1;
			$data['re']['createdDate'] = date('Y-m-d H:i:s');
			$data['re']['createdBy'] = $this->session->userid;
			$data['re']['updatedDate'] = date('Y-m-d H:i:s');
			$data['re']['updatedBy'] = $this->session->userid;
			$this->db->insert('religion', $data['re']);
			$affected_rows = $this->db->affected_rows();
			$inserted_id = $this->db->insert_id();
			return $inserted_id;
		}
	}

	public function getreligionCheck($data = array()){
		$this->db->select('*');		
		$this->db->where('religionName', $data['religionName']);
		if(isset($data['religionId'])){
			$this->db->where('religionId !=', $data['religionId']);
		}	
		$get =  $this->db->get('religion');
		$resVal = $get->result();
		if($resVal) {           
            return true;
        } else {
			return false;
		}
	}	
	
	/*Mothertongue functionality */
	public function getMothertongue($where = array(), $orderby = array(), $select = "mt.*", $join = array(), $group_by = "", $limit ="", $offset = 0, $row = false, $like = array(),$or_like=array(),$or_where =array(),$where_in=array()) {
        if($select == "") $select = "mt.*";
        $this->db->select($select);
        $this->db->from("mothertongue as mt");
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

        $mothertongue = $this->db->get();
        if($mothertongue->num_rows() > 0){
            if(!$row) return $mothertongue->result();
            return $mothertongue->row();
        }
        return array();
    }	
	
	public function save_insert_mothertongue($data = array()){
		if(!empty($data['mothertongueId'])){
			$data['re']['mothertongueName'] = $data['mothertongueName'];
			$data['re']['mothertongueColor'] = $data['mothertongueColor'];
			$data['re']['status'] = 1;
			$data['re']['updatedDate'] = date('Y-m-d H:i:s');
			$data['re']['updatedBy'] = $this->session->userid;
			$this->db->where("mothertongueId", $data['mothertongueId']);
			$this->db->update('mothertongue', $data['re']);
			return $data['mothertongueId'];
		} else {
			$data['re']['mothertongueName'] = $data['mothertongueName'];
			$data['re']['mothertongueColor'] = $data['mothertongueColor'];
			$data['re']['status'] = 1;
			$data['re']['createdDate'] = date('Y-m-d H:i:s');
			$data['re']['createdBy'] = $this->session->userid;
			$data['re']['updatedDate'] = date('Y-m-d H:i:s');
			$data['re']['updatedBy'] = $this->session->userid;
			$this->db->insert('mothertongue', $data['re']);
			$affected_rows = $this->db->affected_rows();
			$inserted_id = $this->db->insert_id();
			return $inserted_id;
		}
	}

	public function getmothertongueCheck($data = array()){
		$this->db->select('*');		
		$this->db->where('mothertongueName', $data['mothertongueName']);
		if(isset($data['mothertongueId'])){
			$this->db->where('mothertongueId !=', $data['mothertongueId']);
		}		
		$get =  $this->db->get('mothertongue');
		$resVal = $get->result();
		if($resVal) {           
            return true;
        } else {
			return false;
		}
	}
	
	/*Mothertongue functionality */
	public function getGender($where = array(), $orderby = array(), $select = "ge.*", $join = array(), $group_by = "", $limit ="", $offset = 0, $row = false, $like = array(),$or_like=array(),$or_where =array(),$where_in=array()) {
        if($select == "") $select = "ge.*";
        $this->db->select($select);
        $this->db->from("gender as ge");
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

        $mothertongue = $this->db->get();
        if($mothertongue->num_rows() > 0){
            if(!$row) return $mothertongue->result();
            return $mothertongue->row();
        }
        return array();
    }	
	
	public function save_insert_gender($data = array()){
		if(!empty($data['genderId'])){
			$data['ge']['genderName'] = $data['genderName'];
			$data['ge']['genderColor'] = $data['genderColor'];
			$data['ge']['status'] = 1;
			$data['ge']['updatedDate'] = date('Y-m-d H:i:s');
			$data['ge']['updatedBy'] = $this->session->userid;
			$this->db->where("genderId", $data['genderId']);
			$this->db->update('gender', $data['ge']);
			return $data['genderId'];
		} else {
			$data['ge']['genderName'] = $data['genderName'];
			$data['ge']['genderColor'] = $data['genderColor'];
			$data['ge']['status'] = 1;
			$data['ge']['createdDate'] = date('Y-m-d H:i:s');
			$data['ge']['createdBy'] = $this->session->userid;
			$data['ge']['updatedDate'] = date('Y-m-d H:i:s');
			$data['ge']['updatedBy'] = $this->session->userid;
			$this->db->insert('gender', $data['ge']);
			$affected_rows = $this->db->affected_rows();
			$inserted_id = $this->db->insert_id();
			return $inserted_id;
		}
	}

	public function getgenderCheck($data = array()){
		$this->db->select('*');		
		$this->db->where('genderName', $data['genderName']);
		if(isset($data['genderId'])){
			$this->db->where('genderId !=', $data['genderId']);
		}		
		$get =  $this->db->get('gender');
		$resVal = $get->result();
		if($resVal) {           
            return true;
        } else {
			return false;
		}
	}

	/*Job Title functionality */
	public function getJobtitle($where = array(), $orderby = array(), $select = "jt.*", $join = array(), $group_by = "", $limit ="", $offset = 0, $row = false, $like = array(),$or_like=array(),$or_where =array(),$where_in=array()) {
        if($select == "") $select = "jt.*";
        $this->db->select($select);
        $this->db->from("jobtitle as jt");
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

        $jobtitle = $this->db->get();
        if($jobtitle->num_rows() > 0){
            if(!$row) return $jobtitle->result();
            return $jobtitle->row();
        }
        return array();
    }
	
	public function save_insert_jobTitle($data = array()){
		if(!empty($data['jobtitleId'])){
			$data['jt']['jobtitleName'] = $data['jobtitleName'];
			$data['jt']['jobtitleColor'] = $data['jobtitleColor'];
			$data['jt']['status'] = 1;
			$data['jt']['updatedDate'] = date('Y-m-d H:i:s');
			$data['jt']['updatedBy'] = $this->session->userid;
			$this->db->where("jobtitleId", $data['jobtitleId']);
			$this->db->update('jobtitle', $data['jt']);
			return $data['jobtitleId'];
		} else {
			$data['jt']['jobtitleName'] = $data['jobtitleName'];
			$data['jt']['jobtitleColor'] = $data['jobtitleColor'];
			$data['jt']['status'] = 1;
			$data['jt']['createdDate'] = date('Y-m-d H:i:s');
			$data['jt']['createdBy'] = $this->session->userid;
			$data['jt']['updatedDate'] = date('Y-m-d H:i:s');
			$data['jt']['updatedBy'] = $this->session->userid;
			$this->db->insert('jobtitle', $data['jt']);
			$affected_rows = $this->db->affected_rows();
			$inserted_id = $this->db->insert_id();
			return $inserted_id;
		}
	}

	public function getjobtitleCheck($data = array()){
		$this->db->select('*');		
		$this->db->where('jobtitleName', $data['jobtitleName']);
		if(isset($data['jobtitleId'])){
			$this->db->where('jobtitleId !=', $data['jobtitleId']);
		}			
		$get =  $this->db->get('jobtitle');
		$resVal = $get->result();
		if($resVal) {           
            return true;
        } else {
			return false;
		}
	}
	
	/*Privacy functionality */
	public function getPrivacy($where = array(), $orderby = array(), $select = "py.*", $join = array(), $group_by = "", $limit ="", $offset = 0, $row = false, $like = array(),$or_like=array(),$or_where =array(),$where_in=array()) {
        if($select == "") $select = "py.*";
        $this->db->select($select);
        $this->db->from("privacy as py");
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

        $privacy = $this->db->get();
        if($privacy->num_rows() > 0){
            if(!$row) return $privacy->result();
            return $privacy->row();
        }
        return array();
    }
	
	public function save_insert_privacy($data = array()){
		if(!empty($data['privacyId'])){
			$data['updatedDate'] = date('Y-m-d H:i:s');
			$data['updatedBy'] = $this->session->userid;
			$this->db->where("privacyId", $data['privacyId']);
			$this->db->update('privacy', $data);
			return $data['privacyId'];
		} else {
			$data['status'] = 1;
			$data['createdDate'] = date('Y-m-d H:i:s');
			$data['createdBy'] = $this->session->userid;
			$data['updatedDate'] = date('Y-m-d H:i:s');
			$data['updatedBy'] = $this->session->userid;
			$this->db->insert('privacy', $data);
			$affected_rows = $this->db->affected_rows();
			$inserted_id = $this->db->insert_id();
			return $inserted_id;
		}
	}

	public function getprivacyCheck($data = array()){
		$this->db->select('*');		
		$this->db->where('privacyName', $data['privacyName']);		
		$get =  $this->db->get('privacy');
		$resVal = $get->result();
		if($resVal) {           
            return true;
        } else {
			return false;
		}
	}
	
	public function did_get_dist_data($districtId){
        $this->db->select('*');
        $this->db->from('district');
        $this->db->join('province', 'district.provinceId = province.provinceId', 'left');
        $this->db->join('country', 'country.countryId = province.countryId', 'left');
        $this->db->where('districtId', $districtId);
        $query = $this->db->get();
        if ($query->num_rows() > 0){
            return $query->result();
        } else {
            return false;
        }
    }
    public function did_get_city_data($cityId){
        $this->db->select('*');
        $this->db->from('city');
        $this->db->join('district','city.districtId = district.districtId','left');
        $this->db->join('province', 'district.provinceId = province.provinceId', 'left');
        $this->db->join('country', 'country.countryId = province.countryId', 'left');
        $this->db->where('cityId', $cityId);
        $query = $this->db->get();

        if ($query->num_rows() > 0){
            return $query->result();
        } else {
            return false;
        }
    }
	public function did_get_religion_data($religionId){
		$this->db->select('*');
		$this->db->from('religion');
		$this->db->where('religionid',$religionId);
		$query = $this->db->get();
		if ($query->num_rows() > 0){
			return $query->result();
		} else {
			return false;
		}
    }
    public function did_get_moth_data($mothertongueId){
		$this->db->select('*');
		$this->db->from('mothertongue');
		$this->db->where('mothertongueId',$mothertongueId);
		$query = $this->db->get();

        if ($query->num_rows() > 0){
            return $query->result();
        } else {
            return false;
        }
    }
	public function did_get_gender_data($genderId){
		$this->db->select('*');
		$this->db->from('gender');
		$this->db->where('genderId',$genderId);
		$query = $this->db->get();

        if ($query->num_rows() > 0){
            return $query->result();
        } else {
            return false;
        }
    }
	
    public function did_get_job_data($jobtitleId){
		$this->db->select('*');
		$this->db->from('jobtitle');
		$this->db->where('jobtitleId',$jobtitleId);
		$query = $this->db->get();
        if ($query->num_rows() > 0){
            return $query->result();
        } else {
            return false;
        }
    }
	
	public function getSettings($id){
		$this->db->select('*');		
		$this->db->where('siteId', $id);		
		$get =  $this->db->get('sitesettings');
		return $get->result();
	}
	
	public function updateSitesetting($data = array()){
		if(!empty($data['siteId'])){
			$this->db->where("siteId", $data['siteId']);
			$this->db->update('sitesettings', $data);
			return $data['siteId'];
		}
	}
	
	public function getMothertongueCount(){
		$query = $this->db->query("SELECT mo.*, 
		(SELECT count(us.userid) FROM users AS us WHERE us.mothertongueId = mo.mothertongueId AND us.userrole != 'admin') as mocount
		FROM mothertongue AS mo ORDER BY mothertongueId ASC");	
		$getMother = $query->result();		
		return $getMother;
	}
	
	public function getReligionCount(){
		$query = $this->db->query("SELECT re.*, 
		(SELECT count(us.userid) FROM users AS us WHERE us.religionId = re.religionId  AND us.userrole != 'admin') as recount
		FROM religion AS re
		ORDER BY religionId ASC");	
		$getReligion = $query->result();		
		return $getReligion;
	}
	
	public function getjotitleCount(){
		$query = $this->db->query("SELECT job.*, 
		(SELECT count(us.userid) FROM users AS us WHERE us.jobTitle = job.jobtitleId  AND us.userrole != 'admin') as jobcount
		FROM jobtitle AS job
		ORDER BY jobtitleId ASC");	
		$getJob = $query->result();		
		return $getJob;
	}
	
	public function getgenderCount(){
		$query = $this->db->query("SELECT gen.*, 
		(SELECT count(us.userid) FROM users AS us WHERE us.genderId = gen.genderId  AND us.userrole != 'admin') as gendercount
		FROM gender AS gen
		ORDER BY genderId ASC");	
		$getGender = $query->result();		
		return $getGender;
	}
	
	public function getAgeCount(){			
		$query = $this->db->query("select concat(20*floor(age/20), '-', 20*floor(age/20) + 20) as `range`, count(*) as count from ( select *, TIMESTAMPDIFF(YEAR,DOB,CURDATE()) AS age from users ) as t WHERE userid NOT IN ('1') group by `range`");	
		$getAge = $query->result();	
		return $getAge;
	}
	
	public function getCountryCount(){
		$query = $this->db->query("SELECT coun.countryId, coun.countryName, count(coun.countryId) AS councount FROM country AS coun LEFT JOIN users AS us ON coun.countryId = us.countryId WHERE us.userrole != 'admin' GROUP BY coun.countryId ");	
		$getCountry = $query->result();		
		return $getCountry;
	}
	
	public function getProvinceCount($countryID){
		$query = $this->db->query("SELECT pro.provinceId, pro.provinceName, count(pro.provinceId) AS councount FROM province AS pro LEFT JOIN users AS us ON pro.provinceId = us.provinceId WHERE us.userrole != 'admin' AND us.countryId = '".$countryID."' GROUP BY pro.provinceId");	
		$getProvince = $query->result();		
		return $getProvince;
	}
}
