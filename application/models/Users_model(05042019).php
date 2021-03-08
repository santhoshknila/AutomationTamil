<?php
class Users_model extends CI_Model{

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
	
    public function check_usersLogin($data = array(), $set_session = true){
		
       $user = $this->db->get_where("users",array('emailid' =>trim($data["username"]), 'status' => 1));
       if($user->num_rows()==0) return false;
       if($user->num_rows()>0){
            $data_ret = $user->result_array();
            $password = $data['password'];
            $right_password = $data_ret[0]['password'];
            $validate_password = $this->phpass->check($password, $right_password);
			
			if($validate_password){
				$last_login = date('Y-m-d H:i:s');
				$token = md5(rand());				
				//$expired_at = date("Y-m-d H:i:s", strtotime('+6 hours'));
				$this->db->where('userid',$data_ret[0]['userid'])->update('users',array('lastlogin' => $last_login, 'deviceToken' => $data["deviceToken"], 'deviceType' => $data["deviceType"]));
				$selexisttoken = $this->db->select('*')->from('users_authentication')->where('userid',$data_ret[0]['userid'])->get()->row();
				if($selexisttoken == ""){
					$this->db->insert('users_authentication',array('userid' => $data_ret[0]['userid'],'token' => $token));
				} else {
					$this->db->where('userid',$data_ret[0]['userid'])->update('users_authentication',array('token' => $token));
				}
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
	
	public function save_insert_users($data = array(), $set_session = true){
		if(!empty($data['userid'])){
			$data['genderId'] = $data['gender'];
			unset($data['gender']);
			$this->db->where("userid", $data['userid']);
			unset($data['userid']);
			$this->db->update('users', $data);
			$affected_rows = $this->db->affected_rows();
			if($affected_rows == 1){
				return true; 
			} else {
				return false;
			}
		} else {
			unset($data['conpassword']);
			$data['genderId'] = $data['gender'];
			unset($data['gender']);
			$data['dummypass'] = $data['password'];
			$data['password'] = $this->phpass->hash($data['password']);
			$data['profileimg'] = 'default.png';
			$data['userrole'] = 'user';
			$data['status'] = 1;
			$data['pollNotification'] = 1;
			$data['pushNotification'] = 1;
			$data['dobPrivacy'] = 1;
			$data['regionPrivacy'] = 1;
			$data['mothertonguePrivacy'] = 1;
			$data['mobilenoPrivacy'] = 3;
			$data['createdDate'] = date('Y-m-d H:i:s');
			$data['updatedDate'] = date('Y-m-d H:i:s');
			$this->db->insert('users', $data);
			$lastid = $this->db->insert_id();
			
			$data1['createdBy'] = $lastid;
			$data1['updatedBy'] = $lastid;
			$this->db->where("userid", $lastid);
			$this->db->update('users', $data1);
			
			$affected_rows = $this->db->affected_rows();
			if($affected_rows == 1){
				$user = $this->db->get_where("users",array('emailid' =>trim($data["emailid"]), 'status' => 1));
				if($user->num_rows()==0) return;
				if($user->num_rows()>0){
					$data_ret = $user->result_array();
					$password = $data['password'];
					$right_password = $data_ret[0]['password'];
					$validate_password = $this->phpass->check($password, $right_password);
					
					if($password === $right_password){
						$last_login = date('Y-m-d H:i:s');
						$token = md5(rand());				
						$expired_at = date("Y-m-d H:i:s", strtotime('+6 hours'));
						$this->db->where('userid',$data_ret[0]['userid'])->update('users',array('lastlogin' => $last_login));				
						$selexisttoken = $this->db->select('*')->from('users_authentication')->where('userid',$data_ret[0]['userid'])->get()->row();
						if($selexisttoken == ""){
							$this->db->insert('users_authentication',array('userid' => $data_ret[0]['userid'],'token' => $token,'expired_at' => $expired_at));
						} else {
							$this->db->where('userid',$data_ret[0]['userid'])->update('users_authentication',array('token' => $token, 'expired_at' => $expired_at));
						}
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
						return;
					}
				}
			} else {
				return;
			}
		}
	}
	
	public function getUseremail($data = array()){
		$user = $this->db->get_where("users",array('emailid' =>$data));
		 if($user->num_rows() > 0) {
            return 0;
        } else{
            return 1;
        }
	}
	
	public function getUserdetailsbyemail($data = array()){		
		$this->db->select("*");
		$this->db->from('users');	
		$this->db->where('emailid',$data);
		$query = $this->db->get();
		return $query->row();
	}
	
	public function getUserdetails($id){
		//$this->db->select("us.userid, us.firstName, us.surName, us.dob, us.jobTitle, us.company, us.mothertongueId, us.religionId, us.emailid, us.mobileno, us.altermobileno, us.countryId, us.provinceId, us.districtId, us.cityId, us.profileimg, us.userrole, ua.token");
		$this->db->select("*");
		$this->db->from('users as us');
		$this->db->join('users_authentication as ua', 'us.userid = ua.userid', 'left');		
		$this->db->where('us.userid',$id);
		$query = $this->db->get();
		return $query->row();
    }
	
	public function getUserdetailsPrivacy($logId, $aId){
		$query = $this->db->query("SELECT userid, firstName, surName, genderId, jobTitle, jobTitletxt, company, emailid, altermobileno, countryId, provinceId, districtId, cityId, profileimg, userrole, firebaseId,
		CASE WHEN users.mothertonguePrivacy = 1 THEN mothertongue.mothertongueId WHEN(
		users.mothertonguePrivacy = 2 AND friends1.friendcont = 1
		) THEN mothertongue.mothertongueId WHEN(
		users.mothertonguePrivacy = 3 AND users.userid = '".(int)$logId."'
		) THEN mothertongue.mothertongueId ELSE ''
		END AS mothertongueId,
		
		CASE WHEN users.regionPrivacy = 1 THEN religion.religionId WHEN(
		users.regionPrivacy = 2 AND friends1.friendcont = 1
		) THEN religion.religionId WHEN(
		users.regionPrivacy = 3 AND users.userid = '".(int)$logId."'
		) THEN religion.religionId ELSE ''
		END AS religionId,
		
		CASE WHEN users.dobPrivacy = 1 THEN users.dob WHEN(
		users.dobPrivacy = 2 AND friends1.friendcont = 1
		) THEN users.dob WHEN(
		users.dobPrivacy = 3 AND users.userid = '".(int)$logId."'
		) THEN users.dob ELSE ''
		END AS dob,
		
		CASE WHEN users.mobilenoPrivacy = 1 THEN users.mobileno WHEN(
		users.mobilenoPrivacy = 2 AND friends1.friendcont = 1
		) THEN users.mobileno WHEN(
		users.mobilenoPrivacy = 3 AND users.userid = '".(int)$logId."'
		) THEN users.mobileno ELSE ''
		END AS mobileno
		
		FROM
		users
		LEFT JOIN mothertongue ON mothertongue.mothertongueId = users.mothertongueId
		LEFT JOIN religion ON religion.religionId = users.religionId
		
		JOIN(
		SELECT
		COUNT(*) AS friendcont,
		receiveRequestId,
		sendRequestUserId
		FROM
		friends
		WHERE
		STATUS
		= 1 AND(
		friends.sendRequestUserId = '".(int)$logId."' AND friends.receiveRequestId = '".(int)$aId."'
		) OR(
		friends.sendRequestUserId = '".(int)$aId."' AND friends.receiveRequestId = '".(int)$logId."'
		)
		) AS friends1
		WHERE
		users.userid = '".(int)$aId."'");
		//echo $sql = $this->db->last_query();
		//exit;
		return $query->row();	
    }

    public function getUseriddetails($id){
		$this->db->select("*");
		$this->db->from('users as us');	
		$this->db->where('us.userid',$id);
		$query = $this->db->get();
		return $query->row();
    }
	
	public function getmySubscription($userid){
		$query = $this->db->query("SELECT * FROM userpackages AS userpack WHERE (userpack.packType=1 OR userpack.packType=2) AND userpack.packId != 0 AND status = 1 AND userpack.userid = '".(int)$userid."' GROUP BY packageUnique ORDER BY startDate DESC");
		//echo $sql = $this->db->last_query();
		//exit;
		return $mySubscripe = $query->result_array();	
	}
	
	public function getmyProducts($data = array()){
		$sql = '';
		if(isset($data['userid'])){
			$sql = 'AND pro.createdBy = "'.$data['userid'].'" AND pro.proType = "'.$data['proType'].'"';
		} else {
			$sql = '';
		}
		$query = $this->db->query("SELECT * FROM products AS pro WHERE pro.status=1 AND pro.reportAbuse=1 $sql ORDER BY updatedDate DESC limit ".(int)$data['limit']." offset ".(int)$data['start']);
		//echo $sql = $this->db->last_query();
		//exit;
		return $mypro = $query->result_array();
	}
	
	public function getmyProductsTot($data = array()){
		$sql = '';
		if(isset($data['userid'])){
			$sql .= 'AND pro.createdBy = "'.$data['userid'].'" AND pro.proType = "'.$data['proType'].'"';
		} else {
			$sql .= '';
		}
		
		/*if(isset($data['packageUnique'])){
			$sql .= 'AND pro.packageUnique = "'.$data['packageUnique'].'"';
		} else {
			$sql .= '';
		}*/
		
		$query = $this->db->query("SELECT * FROM products AS pro WHERE pro.status=1 AND pro.reportAbuse=1 $sql ORDER BY updatedDate DESC");
		return $myproCnt = $query->result_array();
	}
	
	public function save_update_password($data = array()) {
        if(!empty($data)){
			unset($data['conpassword']);
			$data["pass"]['password'] = $this->phpass->hash($data['password']);
			$data["pass"]['dummypass'] = $data['password'];
			if(isset($data['emailid'])){
				$this->db->where("emailid", $data['emailid']);
			}
			if(isset($data['userid'])){
				$this->db->where("userid", $data['userid']);
			}
			$this->db->update('users', $data['pass']);
			return true;
		} else {
			return false;
		}
    }
	
	public function updateUsertoken($data = array()) {
        if(!empty($data)){
			$up["token"]['deviceToken'] = $data['deviceToken'];			
			if(isset($data['userid'])){
				$this->db->where("userid", $data['userid']);
			}
			$this->db->update('users', $up['token']);
			return true;
		} else {
			return false;
		}
    }
	
	public function versionUpdatebyuser($data = array()) {
        if(!empty($data)){
			$up["ver"]['versionType'] = $data['versionType'];	
			$this->db->where("userid", $data['userid']);
			$this->db->update('users', $up['ver']);
			return true;
		} else {
			return false;
		}
    }
	
	public function updatefireBase($data = array()) {
        if(!empty($data)){
			$up["fire"]['firebaseId'] = $data['firebaseId'];			
			if(isset($data['userid'])){
				$this->db->where("userid", $data['userid']);
			}
			$this->db->update('users', $up['fire']);
			return true;
		} else {
			return false;
		}
    }
	
	public function getfireBaseChat($data = array()) {
		if(!empty($data['firebaseId'])){
			$query = $this->db->query("SELECT userid, CONCAT(firstName,' ',surName) as name, profileimg, firebaseId FROM users WHERE firebaseId IN(".$data['firebaseId'].")");
			//$query = $this->db->query("SELECT userid, CONCAT(firstName,' ',surName) as name, profileimg FROM users WHERE firebaseId IN('XyAXaNNkVnS5U1WXCCK24iVoz613','997hr9r3JmgKiKZmouccIJAO8kC2')");
			return $result = $query->result_array();
		} else {
			return false;
		}
    }
	
	public function check_usersoldpassword($oldpass, $userid){
		$user = $this->db->get_where("users",array('userid' => $userid, 'status' => 1));
		if($user->num_rows()==0) return false;
		if($user->num_rows()>0){
            $data_ret = $user->result_array();
            $right_password = $data_ret[0]['password'];
            $validate_password = $this->phpass->check($oldpass, $right_password);
			if($validate_password){
				return true;
			} else {
				return false;
			}
		}  else {
           return false;
        }
	}
	
	public function logout($data = array()){
		$selexisttoken = $this->db->select('*')->from('users_authentication')->where('userid',$data['users_id'])->where('token',$data['token'])->get()->row();
		if(isset($selexisttoken)){
			$updatetoken = md5(rand());
			$this->db->where('userid',$data['users_id'])->update('users_authentication',array('token' => $updatetoken));
			return true;
		} else {
			return false;
		}
    }
	
}
