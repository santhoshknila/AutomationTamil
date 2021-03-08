<?php
class Groups_model extends CI_Model{

    function __construct()
    {
        parent::__construct();
    }
	
	public function get($where = array(), $orderby = array(), $select = "gp.*", $join = array(), $group_by = "", $limit ="", $offset = 0, $row = false, $like = array(),$or_like=array(),$or_where =array(),$where_in=array()) {
        if($select == "") $select = "gp.*";
        $this->db->select($select);
        $this->db->from("groups as gp");
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

        $newsfeeds = $this->db->get();
        if($newsfeeds->num_rows() > 0){
            if(!$row) return $newsfeeds->result();
            return $newsfeeds->row();
        }
        return array();
    }
	
	public function save_createGroup($data){
		if(isset($data['groupName'])){
			$groupExistchk = $this->db->select('*')->from('groups')->where('groupName',$data['groupName'])->where('createdBy',$data['createdBy'])->where('status', 1)->get()->num_rows();
			if($groupExistchk == 0) {
				$data['gp']['groupName'] = $data['groupName'];
				$data['gp']['groupImage'] = $data['groupImage'];
				$data['gp']['groupDescription'] = $data['groupDescription'];
				$data['gp']['groupPrivacyId'] = $data['groupPrivacyId'];
				$data['gp']['status'] = 1;			
				$data['gp']['createdBy'] = $data['createdBy'];
				$data['gp']['updatedBy'] = $data['updatedBy'];
				$data['gp']['createdDate'] = date('Y-m-d H:i:s');
				$data['gp']['updatedDate'] = date('Y-m-d H:i:s');
				$this->db->insert('groups', $data['gp']);
				$lastId = $this->db->insert_id();
				$affected_rows = $this->db->affected_rows();
				if($affected_rows == 1){
					$uploadVal = $data['groupMember'];
					foreach($uploadVal as $key=>$up ){
						$member['gpm']['groupid'] = $lastId;
						$member['gpm']['sendRequestUserId'] = $data['createdBy'];
						$member['gpm']['receiveRequestId'] = $data['groupMember'][$key];
						if($data['groupMember'][$key] == $data['createdBy']){
							$member['gpm']['groupMemisAdmin'] = 1;
						} else {
							$member['gpm']['groupMemisAdmin'] = 0;
						}
						$member['gpm']['status'] = 1;
						$member['gpm']['createdDate'] = date('Y-m-d H:i:s');
						$member['gpm']['createdBy'] = $data['createdBy'];
						$member['gpm']['updatedDate'] = date('Y-m-d H:i:s');
						$member['gpm']['updatedBy'] = $data['createdBy'];
						$this->db->insert('groupmembers', $member['gpm']);
					}
					return $lastId;
				} else {
					return false;
				}
			} else {
				return 3;
			}
		} else {
			return false;
		}
	}
	
	public function save_editGroup($data = array()){
		if(isset($data['groupName'])){
			$groupExistchk = $this->db->select('*')->from('groups')->where('groupName',$data['groupName'])->where('createdBy',$data['createdBy'])->where_not_in('groupId',$data['groupId'])->get()->num_rows();			
			if($groupExistchk == 0) {
				$data['gp']['groupName'] = $data['groupName'];
				if(!empty($data['groupImage'])){
					$data['gp']['groupImage'] = $data['groupImage'];
				}
				$data['gp']['groupDescription'] = $data['groupDescription'];
				$data['gp']['groupPrivacyId'] = $data['groupPrivacyId'];
				$data['gp']['updatedBy'] = $data['updatedBy'];
				$data['gp']['updatedDate'] = date('Y-m-d H:i:s');
				$this->db->where("groupId", $data['groupId']);
				$this->db->update('groups', $data['gp']);				
				return $data['groupId'];				
			} else {
				return 3;
			}
		} else {
			return false;
		}
	}
	
	
	public function getgroupListing($data = array()){
		$sql = '';		
		/* Low to high and High to low */
		if(isset($data['range']) && !empty($data['range']) && $data['range'] == "atoz"){
			$sql .= " ORDER BY groups.groupName ASC";
		} else if(isset($data['range']) && !empty($data['range']) && $data['range'] == "ztoa"){
			$sql .= " ORDER BY groups.groupName DESC";
		} else {
			$sql .= " ORDER BY groups.updatedDate DESC";
		}
		
		$groupList = $this->db->query("SELECT DISTINCT groups.groupId, groups.groupName, groups.groupImage, groups.groupDescription, groups.groupPrivacyId, groups.updatedDate FROM groupmembers LEFT JOIN groups ON groups.groupId = groupmembers.groupid WHERE (groupmembers.receiveRequestId = '".$data['userId']."' AND groupmembers.status <> 5 AND groupmembers.status <> 4 AND groupmembers.status <> 3) AND (groupmembers.status = 1 OR groupmembers.status = 2 OR groupmembers.status = 6 OR groupmembers.status <> 5 OR groupmembers.status <> 4 AND groupmembers.status <> 3) AND groups.status = 1 GROUP BY groupid $sql limit ".(int)$data['limit']." offset ".(int)$data['start']);
		//echo $sql = $this->db->last_query();
		//exit;
		return $groupList = $groupList->result_array();
	}
	
	public function getgrouptotListing($data = array()){
		$sql = '';		
		
		/* Low to high and High to low */
		if(isset($data['range']) && !empty($data['range']) && $data['range'] == "atoz"){
			$sql .= " ORDER BY groups.groupName ASC";
		} else if(isset($data['range']) && !empty($data['range']) && $data['range'] == "ztoa"){
			$sql .= " ORDER BY groups.groupName DESC";
		} else {
			$sql .= " ORDER BY groups.updatedDate DESC";
		}
		
		$groupList = $this->db->query("SELECT DISTINCT groups.groupId, groups.groupName, groups.groupImage, groups.groupDescription, groups.groupPrivacyId, groups.updatedDate FROM groupmembers LEFT JOIN groups ON groups.groupId = groupmembers.groupid WHERE (groupmembers.receiveRequestId = '".$data['userId']."' AND groupmembers.status <> 5 AND groupmembers.status <> 4 AND groupmembers.status <> 3) AND (groupmembers.status = 1 OR groupmembers.status = 2 OR groupmembers.status = 6 OR groupmembers.status <> 5 OR groupmembers.status <> 4 AND groupmembers.status <> 3) AND groups.status = 1 GROUP BY groupid $sql");
		//echo $sql = $this->db->last_query();
		//exit;
		return $groupList = $groupList->result_array();
	}
	
	public function getgroupListingSearch($data = array()){
		$sql = '';
		$groupList = $this->db->query("SELECT DISTINCT groups.groupId, groups.groupName, groups.groupImage, groups.groupDescription, groups.groupPrivacyId FROM ((SELECT groups.groupId, groups.groupName, groups.groupImage, groups.groupDescription, groups.groupPrivacyId FROM groupmembers LEFT JOIN groups ON groups.groupId = groupmembers.groupid WHERE (groupmembers.sendRequestUserId = '".$data['userId']."' OR groupmembers.receiveRequestId = '".$data['userId']."') AND groupmembers.status = 1 AND groups.groupName LIKE '%".$data['search']."%') UNION ALL (SELECT groups.groupId, groups.groupName, groups.groupImage, groups.groupDescription, groups.groupPrivacyId FROM groupmembers LEFT JOIN groups ON groups.groupId = groupmembers.groupid WHERE (groups.groupPrivacyId = 1 OR groups.groupPrivacyId = 2) AND groups.groupName LIKE '%".$data['search']."%' AND (groups.status = 1 AND (groups.status <> 2 OR groups.status <> 3) ))) AS groups ORDER BY groups.groupId limit ".(int)$data['limit']." offset ".(int)$data['start']);
		
		//echo $sql = $this->db->last_query();
		//exit;
		return $groupList = $groupList->result_array();
	}
	
	public function getgrouptotListingSearch($data = array()){
		$sql = '';	
		
		$groupList = $this->db->query("SELECT DISTINCT groups.groupId, groups.groupName, groups.groupImage, groups.groupDescription, groups.groupPrivacyId FROM ((SELECT groups.groupId, groups.groupName, groups.groupImage, groups.groupDescription, groups.groupPrivacyId FROM groupmembers LEFT JOIN groups ON groups.groupId = groupmembers.groupid WHERE (groupmembers.sendRequestUserId = '".$data['userId']."' OR groupmembers.receiveRequestId = '".$data['userId']."') AND groupmembers.status = 1 AND groups.groupName LIKE '%".$data['search']."%') UNION ALL (SELECT groups.groupId, groups.groupName, groups.groupImage, groups.groupDescription, groups.groupPrivacyId FROM groupmembers LEFT JOIN groups ON groups.groupId = groupmembers.groupid WHERE (groups.groupPrivacyId = 1 OR groups.groupPrivacyId = 2) AND groups.groupName LIKE '%".$data['search']."%' AND (groups.status = 1 AND (groups.status <> 2 OR groups.status <> 3)))) AS groups ORDER BY groups.groupId");
		
		return $groupList = $groupList->result_array();
	}
	
	public function getgroupMemberListing($data = array()){
		$sql = '';
		if(isset($data['limit'])){
			$groupList = $this->db->query("SELECT * FROM groupmembers WHERE groupid = '".$data['groupId']."' AND (status = 1 OR status = 6) ORDER BY groupMemberId DESC limit ".(int)$data['limit']." offset ".(int)$data['start']);
		} else {
			$groupList = $this->db->query("SELECT * FROM groupmembers WHERE groupid = '".$data['groupId']."' AND (status = 1 OR status = 6) ORDER BY groupMemberId DESC");
		}
		//echo $sql = $this->db->last_query();
		//exit;
		return $groupList = $groupList->result_array();
	}
	
	public function getgroupMembertotListing($data = array()){
		$groupList = $this->db->query("SELECT * FROM groupmembers WHERE groupid = '".$data['groupId']."' AND (status = 1 OR status = 6)");
		return $groupList = $groupList->result_array();
	}
	
	
	public function getMembercnt($gId){
		$this->db->select('*');
		$this->db->from('groupmembers AS gpm');
		$this->db->where('gpm.groupid',$gId);
		$this->db->where('gpm.status',1);
		$query = $this->db->get();
		//echo $sql = $this->db->last_query();
		//exit;
		return $query->result();
	}
	
	public function getgroupDetails($groupID, $userid){
		$groupDetails = $this->db->select('*')->from('groups')->where('groupId',$groupID)->get()->result();
		return $groupDetails[0];
	}	
	
	/*getMemIsAdmin details*/
	public function getMemIsAdmin($groupID, $userid){
		$memberAdmin = $this->db->select('*')->from('groupmembers')->where('receiveRequestId',$userid)->where('groupId',$groupID)->get()->result();
		//echo $sql = $this->db->last_query();
		//exit;
		return $memberAdmin;
	}
	
	/*Group post with based on groupId*/	
	public function getgroupPostdetails($data = array()){
		$query = $this->db->query("SELECT * FROM groupmembers WHERE groupid = '".$data['groupId']."' AND receiveRequestId = '".$data['userId']."' AND status = 1");
		if($query->num_rows() == 1){
			$query = $this->db->query("SELECT newsFeedsId as feedID,title as feedTitle,description as feedDesc,isActive as active,newsType as feedType, groupId, createdBy as user, createdDate as crdate, updatedDate as updaDate, endDate as exDate, privacyId as pID, reportAbuse as report, isActive as status FROM newsfeeds WHERE reportAbuse = 1 AND status = 1 AND groupid = '".$data['groupId']."' ORDER BY updaDate DESC limit ".(int)$data['limit']." offset ".(int)$data['start']);
			return $newsfeeds = $query->result_array();	
		} else {
			return;
		}		
	}
	
	public function getgroupTotalPostdetails($data = array()){
		$query = $this->db->query("SELECT * FROM groupmembers WHERE groupid = '".$data['groupId']."' AND receiveRequestId = '".$data['userId']."' AND status = 1");		
		if($query->num_rows() == 1){
			$query = $this->db->query("SELECT newsFeedsId as feedID,title as feedTitle,description as feedDesc,isActive as active,newsType as feedType, groupId, createdBy as user, createdDate as crdate, updatedDate as updaDate, endDate as exDate, privacyId as pID, reportAbuse as report, isActive as status FROM newsfeeds WHERE reportAbuse = 1 AND status = 1 AND groupid = '".$data['groupId']."' ORDER BY updaDate DESC ");
			return $newsfeeds = $query->result_array();	
		} else {			
			return;
		}
	}
		
	
	public function getinviteMemberlist($groupID, $userID){
		$gmInvite = $this->db->select('*')->from('groupmembers')->where('groupId',$groupID)->where('receiveRequestId',$userID)->get()->result_array();
		return $gmInvite;
	}
	
	/* Accept/Cancel Request details update*/
	public function updateRequest($data=array()){		
		if(isset($data['groupMemberId'])){
			$accept['sendRequestUserId'] = $data['loggedUser'];
			$accept['status'] = $data['updateStatus'];
			$accept['updatedDate'] = date('Y-m-d H:i:s');
			$accept['updatedBy'] = $data['loggedUser'];
			$this->db->where('groupMemberId', $data['groupMemberId']);
			$this->db->update('groupmembers', $accept);
			return true;
		} else {
			return false;
		}
	}
	
	public function getMemberstatus($groupMemberId){
		$memberStatus = $this->db->select('status')->from('groupmembers')->where("groupMemberId", $groupMemberId)->get()->result_array();
		return $memberStatus;
	}	
	
	/*Invite Member listing with feiends*/
	public function getMemberinvitelist($data=array()){
		
		//$query = $this->db->query("SELECT users.userid, users.firstName, users.surName, users.profileimg FROM friends LEFT JOIN groupmembers ON (groupmembers.receiveRequestId = (CASE WHEN friends.sendRequestUserId = '".$data['userId']."' THEN friends.receiveRequestId ELSE friends.sendRequestUserId END)) AND groupmembers.groupId = '".$data['groupId']."' LEFT JOIN users ON users.userid = (CASE WHEN friends.sendRequestUserId = '".$data['userId']."' THEN friends.receiveRequestId ELSE friends.sendRequestUserId END) WHERE (friends.sendRequestUserId = '".$data['userId']."' OR friends.receiveRequestId = '".$data['userId']."') AND (groupmembers.groupMemberId IS NULL OR groupmembers.status = 4) limit ".(int)$data['limit']." offset ".(int)$data['start']);
		
		$query = $this->db->query("SELECT * FROM friends LEFT JOIN groupmembers ON (groupmembers.receiveRequestId = (CASE WHEN friends.sendRequestUserId = '".$data['userId']."' THEN friends.receiveRequestId ELSE friends.sendRequestUserId END)) AND groupmembers.groupId = '".$data['groupId']."' LEFT JOIN users ON users.userid = (CASE WHEN friends.sendRequestUserId = '".$data['userId']."' THEN friends.receiveRequestId ELSE friends.sendRequestUserId END) WHERE (friends.sendRequestUserId = '".$data['userId']."' OR friends.receiveRequestId = '".$data['userId']."') AND friends.status=1 AND (users.firstName LIKE '%".$data['searchVal']."%' OR users.surName LIKE '%".$data['searchVal']."%' OR CONCAT(users.firstName, ' ', users.surName) LIKE '%".$data['searchVal']."%') AND (groupmembers.groupMemberId IS NULL OR groupmembers.status = 4 OR groupmembers.status = 5 ) limit ".(int)$data['limit']." offset ".(int)$data['start']);
		//echo $sql = $this->db->last_query();
		//exit;
		return $Memberinvite = $query->result_array();	
	}	
	
	public function getMemberinvitelisttot($data=array()){
		$query = $this->db->query("SELECT * FROM friends LEFT JOIN groupmembers ON (groupmembers.receiveRequestId = (CASE WHEN friends.sendRequestUserId = '".$data['userId']."' THEN friends.receiveRequestId ELSE friends.sendRequestUserId END)) AND groupmembers.groupId = '".$data['groupId']."' LEFT JOIN users ON users.userid = (CASE WHEN friends.sendRequestUserId = '".$data['userId']."' THEN friends.receiveRequestId ELSE friends.sendRequestUserId END) WHERE (friends.sendRequestUserId = '".$data['userId']."' OR friends.receiveRequestId = '".$data['userId']."') AND friends.status=1 AND (users.firstName LIKE '%".$data['searchVal']."%' OR users.surName LIKE '%".$data['searchVal']."%' OR CONCAT(users.firstName, ' ', users.surName) LIKE '%".$data['searchVal']."%') AND (groupmembers.groupMemberId IS NULL OR groupmembers.status = 4 OR groupmembers.status = 5 ) ");
		return $Memberinvite = $query->result_array();	
	}
	
	/*Leave Group*/
	public function leaveGroup($data=array()){
		$checkMemadmin = $this->db->select('*')->from('groupmembers')->where("groupId", $data['groupId'])->where("groupMemisAdmin", 1)->where("status", 1)->where("groupMemberId", $data['groupMemberId'])->get()->result();	
		if(!empty($checkMemadmin)){
			//checkLeaveGroup($data);
			if(isset($data['groupId'])){
				$leave['status'] = 5;
				$leave['updatedDate'] = date('Y-m-d H:i:s');
				$leave['updatedBy'] = $data['loggedUser'];
				$this->db->where('groupId', $data['groupId']);
				$this->db->where('groupMemberId', $data['groupMemberId']);
				$this->db->update('groupmembers', $leave);
				$afftectedRows = $this->db->affected_rows();
				if($afftectedRows == 1){
					$checkMemadmin1 = $this->db->select('*')->from('groupmembers')->where("groupId", $data['groupId'])->where("groupMemisAdmin", 1)->where("status", 1)->get()->result();
					if(empty($checkMemadmin1)){
						$query = $this->db->query("SELECT * FROM groupmembers WHERE groupid = '".$data['groupId']."' AND status = 1  ORDER BY groupMemberId ASC limit 1");
						$nextUseradmin = $query->result();						
						if(!empty($nextUseradmin)){
							$makeadmin['groupMemisAdmin'] = 1;							
							$this->db->where('groupId', $nextUseradmin[0]->groupid);
							$this->db->where('groupMemberId', $nextUseradmin[0]->groupMemberId);
							$this->db->update('groupmembers', $makeadmin);
							
							$gpUpdate['createdBy'] = $gpUpdate['updatedBy'] = $nextUseradmin[0]->receiveRequestId;
							$this->db->where('groupId', $data['groupId']);
							$this->db->update('groups', $gpUpdate);							
							return true;
						} else {
							$del['status'] = 3;
							$del['updatedDate'] = date('Y-m-d H:i:s');
							$del['updatedBy'] = $data['loggedUser'];
							$this->db->where('groupId', $data['groupId']);
							$this->db->update('groups', $del);
							$afftectedRows = $this->db->affected_rows();
							if($afftectedRows == 1){
								$delMem['status'] = 3;
								$this->db->where('groupId', $data['groupId']);
								$this->db->update('groupmembers', $delMem);
								
								$delPost['status'] = 0;
								$delPost['isActive'] = 0;
								$this->db->where('groupId', $data['groupId']);
								$this->db->update('newsfeeds', $delPost);
								
								$gpUpdate['status'] = 3;
								$gpUpdate['updatedBy'] = $data['loggedUser'];
								$this->db->where('groupId', $data['groupId']);
								$this->db->update('groups', $gpUpdate);
								return true;								
							}
							return true;
						}
					}
				}
				return true;
			} else {
				return true;
			}
		} else {
			$checkMemstatus = $this->db->select('*')->from('groupmembers')->where("groupId", $data['groupId'])->where("status", 1)->where("groupMemberId", $data['groupMemberId'])->get()->result();
			if(!empty($checkMemstatus)){
				//checkLeaveGroup($data);
				if(isset($data['groupId'])){
					$leave['status'] = 5;
					$leave['updatedDate'] = date('Y-m-d H:i:s');
					$leave['updatedBy'] = $data['loggedUser'];
					$this->db->where('groupId', $data['groupId']);
					$this->db->where('groupMemberId', $data['groupMemberId']);
					$this->db->update('groupmembers', $leave);
					$afftectedRows = $this->db->affected_rows();
					if($afftectedRows == 1){
						$checkMemadmin1 = $this->db->select('*')->from('groupmembers')->where("groupId", $data['groupId'])->where("groupMemisAdmin", 1)->where("status", 1)->get()->result();
						if(empty($checkMemadmin1)){
							$query = $this->db->query("SELECT * FROM groupmembers WHERE groupid = '".$data['groupId']."' AND status = 1  ORDER BY groupMemberId ASC limit 1");
							$nextUseradmin = $query->result();						
							if(!empty($nextUseradmin)){
								$makeadmin['groupMemisAdmin'] = 1;							
								$this->db->where('groupId', $nextUseradmin[0]->groupid);
								$this->db->where('groupMemberId', $nextUseradmin[0]->groupMemberId);
								$this->db->update('groupmembers', $makeadmin);
								
								$gpUpdate['createdBy'] = $gpUpdate['updatedBy'] = $nextUseradmin[0]->receiveRequestId;
								$this->db->where('groupId', $data['groupId']);
								$this->db->update('groups', $gpUpdate);							
								return true;
							} else {
								$del['status'] = 3;
								$del['updatedDate'] = date('Y-m-d H:i:s');
								$del['updatedBy'] = $data['loggedUser'];
								$this->db->where('groupId', $data['groupId']);
								$this->db->update('groups', $del);
								$afftectedRows = $this->db->affected_rows();
								if($afftectedRows == 1){
									$delMem['status'] = 3;
									$this->db->where('groupId', $data['groupId']);
									$this->db->update('groupmembers', $delMem);
									
									$delPost['status'] = 0;
									$delPost['isActive'] = 0;
									$this->db->where('groupId', $data['groupId']);
									$this->db->update('newsfeeds', $delPost);
									
									$gpUpdate['status'] = 3;
									$gpUpdate['updatedBy'] = $data['loggedUser'];
									$this->db->where('groupId', $data['groupId']);
									$this->db->update('groups', $gpUpdate);
									return true;
								}
								return true;
							}
						}
					}
					return true;
				} else {
					return false;
				}
			} else {
				return false;
			}
		}
	}
	
	/*Leave Group*/
	public function checkLeaveGroup($data=array()){
		if(isset($data['groupId'])){
			$leave['status'] = 5;
			$leave['updatedDate'] = date('Y-m-d H:i:s');
			$leave['updatedBy'] = $data['loggedUser'];
			$this->db->where('groupId', $data['groupId']);
			$this->db->where('groupMemberId', $data['groupMemberId']);
			$this->db->update('groupmembers', $leave);
			$afftectedRows = $this->db->affected_rows();
			if($afftectedRows == 1){
				$checkMemadmin = $this->db->select('*')->from('groupmembers')->where("groupId", $data['groupId'])->where("groupMemisAdmin", 1)->where("status", 1)->get()->result();
				if(empty($checkMemadmin)){
					$query = $this->db->query("SELECT * FROM groupmembers WHERE groupid = '".$data['groupId']."' AND status = 1  ORDER BY groupMemberId ASC limit 1");
					$nextUseradmin = $query->result();
					$makeadmin['groupMemisAdmin'] = 1;
					$this->db->where('groupId', $nextUseradmin[0]->groupid);
					$this->db->where('groupMemberId', $nextUseradmin[0]->groupMemberId);
					$this->db->update('groupmembers', $makeadmin);
					return true;
				}
			}
			return true;
		} else {
			return false;
		}
	}
	
	public function deleteGroups($data=array()){
		$query = $this->db->query("SELECT * FROM groupmembers WHERE groupid = '".$data['groupId']."' AND groupMemisAdmin = 1 AND status = 1 AND (sendRequestUserId  = '".$data['loggedUser']."' OR receiveRequestId = '".$data['loggedUser']."' )");
		$checkMemadmin = $query->result();		
		if(!empty($checkMemadmin)){
			if(isset($data['groupId'])){
				$del['status'] = 3;
				$del['updatedDate'] = date('Y-m-d H:i:s');
				$del['updatedBy'] = $data['loggedUser'];
				$this->db->where('groupId', $data['groupId']);
				$this->db->update('groups', $del);
				$afftectedRows = $this->db->affected_rows();
				if($afftectedRows == 1){
					$delMem['status'] = 3;
					$this->db->where('groupId', $data['groupId']);
					$this->db->update('groupmembers', $delMem);
					
					$delPost['status'] = 0;
					$delPost['isActive'] = 0;
					$this->db->where('groupId', $data['groupId']);
					$this->db->update('newsfeeds', $delPost);
					return true;
				}
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	
	/*Make Admin or not*/
	public function makegroupAdmin($data=array()){	
		if(isset($data['groupId'])){
			$up['groupMemisAdmin'] = $data['groupMemisAdmin'];
			$up['updatedDate'] = date('Y-m-d H:i:s');
			$up['updatedBy'] = $data['loggedUser'];
			$this->db->where('groupMemberId', $data['groupMemberId']);
			$this->db->where('groupId', $data['groupId']);
			$this->db->update('groupmembers', $up);
			$afftectedRows = $this->db->affected_rows();
			return true;
		} else {
			return false;
		}	
	}
	
	/*group Join request */
	public function groupJoinrequest($data=array()){
		if(isset($data['groupId'])){
			$query =  $this->db->query("SELECT * FROM groupmembers WHERE groupid = '".$data['groupId']."' AND receiveRequestId = '".$data['loggedUser']."' AND (status = 4 OR status = 5)");
			$checkJoinrequest = $query->result_array();			
			if(empty($checkJoinrequest)){
				$data['join']['groupid'] = $data['groupId'];
				$data['join']['sendRequestUserId'] = $data['loggedUser'];
				$data['join']['receiveRequestId'] = $data['loggedUser'];
				$data['join']['groupMemisAdmin'] = 0;
				$data['join']['status'] = 6;			
				$data['join']['createdBy'] = $data['loggedUser'];
				$data['join']['updatedBy'] = $data['loggedUser'];
				$data['join']['createdDate'] = date('Y-m-d H:i:s');
				$data['join']['updatedDate'] = date('Y-m-d H:i:s');
				$this->db->insert('groupmembers', $data['join']);
				return true;
			} else {
				$data['join']['groupid'] = $data['groupId'];
				$data['join']['sendRequestUserId'] = $data['loggedUser'];
				$data['join']['receiveRequestId'] = $data['loggedUser'];
				$data['join']['groupMemisAdmin'] = 0;
				$data['join']['status'] = 6;			
				$data['join']['createdBy'] = $data['loggedUser'];
				$data['join']['updatedBy'] = $data['loggedUser'];
				$data['join']['createdDate'] = date('Y-m-d H:i:s');
				$data['join']['updatedDate'] = date('Y-m-d H:i:s');
				$this->db->where('receiveRequestId', $data['loggedUser']);
				$this->db->where('groupId', $data['groupId']);
				$this->db->update('groupmembers', $data['join']);
				return true;
			}
		} else {
			return false;
		}
	}
	
	/*Invite request*/
	public function groupaddmemberInvite($data=array()){
		
		if(isset($data['groupId'])){
			$query = $this->db->query("SELECT * FROM groupmembers WHERE groupid = '".$data['groupId']."' AND receiveRequestId = '".$data['addMemberId']."' AND (status = 4 OR status = 5)");
			//echo $sql = $this->db->last_query();
			//exit;
			$Memberinvite = $query->result_array();
			if(empty($Memberinvite)){
				$data['join']['groupid'] = $data['groupId'];
				$data['join']['sendRequestUserId'] = $data['loggedUser'];
				$data['join']['receiveRequestId'] = $data['addMemberId'];
				$data['join']['groupMemisAdmin'] = 0;
				$data['join']['status'] = 2;			
				$data['join']['createdBy'] = $data['loggedUser'];
				$data['join']['updatedBy'] = $data['loggedUser'];
				$data['join']['createdDate'] = date('Y-m-d H:i:s');
				$data['join']['updatedDate'] = date('Y-m-d H:i:s');
				$this->db->insert('groupmembers', $data['join']);
				return true;
			} else {
				$data['join']['groupid'] = $data['groupId'];
				$data['join']['sendRequestUserId'] = $data['loggedUser'];
				$data['join']['receiveRequestId'] = $data['addMemberId'];
				$data['join']['groupMemisAdmin'] = 0;
				$data['join']['status'] = 2;			
				$data['join']['createdBy'] = $data['loggedUser'];
				$data['join']['updatedBy'] = $data['loggedUser'];
				$data['join']['createdDate'] = date('Y-m-d H:i:s');
				$data['join']['updatedDate'] = date('Y-m-d H:i:s');
				$this->db->where('receiveRequestId', $data['addMemberId']);
				$this->db->where('groupId', $data['groupId']);
				$this->db->update('groupmembers', $data['join']);
				return true;
			}
		} else {
			return false;
		}
	}
	
	/*Remove Member*/
	public function groupRemovemember($data=array()){		
		if(isset($data['groupId'])){
			$remove['status'] = 4;
			$remove['updatedBy'] = $data['loggedUser'];
			$remove['updatedDate'] = date('Y-m-d H:i:s');
			$this->db->where('groupMemberId', $data['removeMemberId']);
			$this->db->where('groupId', $data['groupId']);
			$this->db->update('groupmembers', $remove);
			
			$query = $this->db->query("SELECT * FROM groupmembers WHERE groupMemberId = '".$data['removeMemberId']."'");
			$getMebID = $query->result();
			
			$afftectedRows = $this->db->affected_rows();
			return $getMebID[0]->receiveRequestId;
		} else {
			return false;
		}
	}
	
	/*Get the group members for notifications*/
	public function getgroupdeleteMember($gID){		
		$groupList = $this->db->query("SELECT * FROM groupmembers WHERE groupid = '".$gID."'");
		//echo $sql = $this->db->last_query();
		//exit;
		return $groupList = $groupList->result_array();
	}
	
	/* Report abuse add details */
	public function save_group_reportAbuse($data = array()){
		$data['reAbuse']['status'] = 1;			
		$data['reAbuse']['createdDate'] = date('Y-m-d H:i:s');
		$data['reAbuse']['updatedDate'] = date('Y-m-d H:i:s');
		$this->db->insert('groupsreport', $data['reAbuse']);
		$lastId = $this->db->insert_id();
		$affected_rows = $this->db->affected_rows();
		return $affected_rows;
	}
	
	public function getuserreportAbusests($userId, $gId){
		$getuserreportAbusecnt = $this->db->select('*')->from('groupsreport')->where('createdBy',$userId)->where('groupId',$gId)->get()->num_rows();
		return $getuserreportAbusecnt;
	}
	
}