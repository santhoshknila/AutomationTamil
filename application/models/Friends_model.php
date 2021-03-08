<?php
class Friends_model extends CI_Model{

    function __construct()
    {
        parent::__construct();
    }
   
	/*Listing friends list based on search parameter*/
	public function getFriends($data = array()){
		$userList = $this->db->select('*')->from('users as us')->where('userrole', 'user')->where("(firstName LIKE '%".$data['search']."%' OR surName LIKE '%".$data['search']."%' OR emailid LIKE '%".$data['search']."%' OR CONCAT(firstName, ' ', surName) LIKE '%".$data['search']."%' OR CONCAT(firstName, '', surName) LIKE '%".$data['search']."%')", NULL, FALSE)->where('status', 1)->order_by('userid', 'DESC')->limit($data['limit'])->offset($data['start'])->get()->result_array();
		return $userList;
	}
	
	public function getTotalFriends($data = array()){
		$userListall = $this->db->select('*')->from('users as us')->where('userrole', 'user')->where("(firstName LIKE '%".$data['search']."%' OR surName LIKE '%".$data['search']."%' OR emailid LIKE '%".$data['search']."%' OR CONCAT(firstName, ' ', surName) LIKE '%".$data['search']."%' OR CONCAT(firstName, '', surName) LIKE '%".$data['search']."%')", NULL, FALSE)->where('status', 1)->order_by('userid', 'DESC')->get()->result_array();
		return $userListall;
	}
	
	/*Friends status*/
	public function getFriendstatus($requestID, $userID){
		$userFriend = $this->db->select('*')->from('friends')->where("(sendRequestUserId = '".$requestID."' AND receiveRequestId = '".$userID."')", NULL, FALSE)->or_where("(sendRequestUserId = '".$userID."' AND receiveRequestId = '".$requestID."')", NULL, FALSE)->get()->result_array();
		//echo $sql = $this->db->last_query();
		//exit;
		return $userFriend;
	}
	
	public function getfristatus($friendID){
		$userFriend = $this->db->select('*')->from('friends')->where("friendId", $friendID)->get()->result_array();
		return $userFriend;
	}
	
	/*Request send*/
	public function sendRequest($data=array()){		
		if(isset($data['requestUser'])){			
			$userFriend = $this->db->select('*')->from('friends')->where("(sendRequestUserId = '".$data['loggedUser']."' AND receiveRequestId = '".$data['requestUser']."')", NULL, FALSE)->or_where("(sendRequestUserId = '".$data['requestUser']."' AND receiveRequestId = '".$data['loggedUser']."')", NULL, FALSE)->get()->result_array();
			if(count($userFriend) == 1){
				$accept['status'] = 2;
				$accept['updatedDate'] = date('Y-m-d H:i:s');
				$accept['updatedBy'] = $data['loggedUser'];
				$this->db->where('friendId', $userFriend[0]['friendId']);
				$this->db->update('friends', $accept);
				return true;
			} else {
				$data['sent']['sendRequestUserId'] = $data['loggedUser'];
				$data['sent']['receiveRequestId'] = $data['requestUser'];
				$data['sent']['status'] = 2;			
				$data['sent']['createdBy'] = $data['loggedUser'];
				$data['sent']['updatedBy'] = $data['loggedUser'];
				$data['sent']['createdDate'] = date('Y-m-d H:i:s');
				$data['sent']['updatedDate'] = date('Y-m-d H:i:s');
				$this->db->insert('friends', $data['sent']);
				return true;
			}
		} else {
			return false;
		}
	}
	
	/* Accept/Unfriend/Cancel Request details update*/
	public function updateRequest($data=array()){		
		if(isset($data['friendID'])){
			if($data['updateStatus'] == 3 || $data['updateStatus'] == 4){
				$this->db->where('friendId', $data['friendID']);
				$this->db->delete('friends'); 
				return true;
			} else {
				$accept['status'] = $data['updateStatus'];
				$accept['updatedDate'] = date('Y-m-d H:i:s');
				$accept['updatedBy'] = $data['loggedUser'];
				$this->db->where('friendId', $data['friendID']);
				$this->db->update('friends', $accept);
				return true;
			}
		} else {
			return false;
		}
	}
	
	public function receiveRequest($data=array()){	
		$receiveUser = $this->db->select('*')->from('friends')->where('receiveRequestId', $data['receiveID'])->where('status', 2)->limit($data['limit'])->offset($data['start'])->get()->result_array();
		return $receiveUser;
	}	
	
	public function receiveTotalRequest($data=array()){	
		$receiveUsertot = $this->db->select('*')->from('friends')->where('receiveRequestId', $data['receiveID'])->where('status', 2)->get()->result_array();
		return $receiveUsertot;
	}
	
	public function myConnections($data=array()){	
		$myconnect = $this->db->select('*')->from('friends')->where("(sendRequestUserId = '".$data['loggedUser']."' OR receiveRequestId = '".$data['loggedUser']."')", NULL, FALSE)->where('status', 1)->limit($data['limit'])->offset($data['start'])->order_by('friendId', 'DESC')->get()->result_array();
		//echo $sql = $this->db->last_query();
		//exit;
		return $myconnect;
	}
	
	public function myConnectionsTotal($data=array()){	
		$myconnecttot = $this->db->select('*')->from('friends')->where("(sendRequestUserId = '".$data['loggedUser']."' OR receiveRequestId = '".$data['loggedUser']."')", NULL, FALSE)->where('status',1)->get()->result_array();
		return $myconnecttot;
	}
	
	/*My connections friends search*/
	public function myConnectionsfri($data=array()){
		
		$query = $this->db->query("SELECT * FROM friends LEFT JOIN users ON users.userid = (CASE WHEN friends.sendRequestUserId != '".$data['loggedUser']."' THEN friends.sendRequestUserId ELSE friends.receiveRequestId END) WHERE (sendRequestUserId = '".$data['loggedUser']."' OR receiveRequestId = '".$data['loggedUser']."') AND friends.status = 1 AND (users.firstName LIKE '%".$data['searchVal']."%' OR users.surName LIKE '%".$data['searchVal']."%' OR CONCAT(users.firstName, ' ', users.surName) LIKE '%".$data['searchVal']."%') ORDER BY users.userid DESC limit ".(int)$data['limit']." offset ".(int)$data['start']);
		//echo $sql = $this->db->last_query();
		//exit;
		return $query->result_array();
	}
	
	public function myConnectionsfriTotal($data=array()){	
		$query = $this->db->query("SELECT * FROM friends LEFT JOIN users ON users.userid = (CASE WHEN friends.sendRequestUserId != '".$data['loggedUser']."' THEN friends.sendRequestUserId ELSE friends.receiveRequestId END) WHERE (sendRequestUserId = '".$data['loggedUser']."' OR receiveRequestId = '".$data['loggedUser']."') AND friends.status = 1 AND (users.firstName LIKE '%".$data['searchVal']."%' OR users.surName LIKE '%".$data['searchVal']."%' OR CONCAT(users.firstName, ' ', users.surName) LIKE '%".$data['searchVal']."%')");
		//echo $query->num_rows();
		//exit;
		return $query->num_rows();
	}
	
	public function getMutualfriends($loguserID, $viewFriID){	
		
		/*$query = $this->db->query("SELECT CASE WHEN (sendRequestUserId = '".$loguserID."') THEN receiveRequestId ELSE sendRequestUserId END AS MyFriendID FROM `friends` as myID INNER JOIN (SELECT CASE WHEN (sendRequestUserId = '".$viewFriID."') THEN receiveRequestId ELSE sendRequestUserId END AS MyFriendID FROM `friends` WHERE (`sendRequestUserId` = '".$viewFriID."' OR `receiveRequestId` = '".$viewFriID."')) AS viewFriend ON viewFriend.MyFriendID = MyFriendID AND viewFriend.MyFriendID != '".$loguserID."' WHERE (`sendRequestUserId` = '".$loguserID."' OR `receiveRequestId` = '".$loguserID."') AND (CASE WHEN (sendRequestUserId = '".$loguserID."') THEN receiveRequestId ELSE sendRequestUserId END != '".$viewFriID."') AND (CASE WHEN (sendRequestUserId = '".$loguserID."') THEN receiveRequestId ELSE sendRequestUserId END = viewFriend.MyFriendID)");

		//echo $sql = $this->db->last_query();
		//exit;		
		$mutualFriCnt = $query->num_rows();		
		return $mutualFriCnt;*/

		$logUser = $this->db->query("SELECT sendRequestUserId,receiveRequestId FROM `friends` WHERE (`sendRequestUserId`='".$loguserID."' or  `receiveRequestId`= '".$loguserID."') and status=1")->result_array();

		$logRes = array();
		foreach ($logUser as $key => $log) 
		{
			array_push($logRes,$log['receiveRequestId'],$log['sendRequestUserId']);
		}

		$viewUser = $this->db->query("SELECT sendRequestUserId,receiveRequestId FROM `friends` WHERE (`sendRequestUserId`='".$viewFriID."' or  `receiveRequestId`= '".$viewFriID."') and status=1")->result_array();
		
		$viewRes = array();
		foreach ($viewUser as $key => $view) 
		{
			array_push($viewRes,$view['receiveRequestId'],$view['sendRequestUserId']);
		}

		$logRes = array_unique($logRes);
		$viewRes = array_unique($viewRes);

		$del_val = array($loguserID, $viewFriID);

		$logRes = $this->remove_element($logRes,$del_val);
		$viewRes = $this->remove_element($viewRes,$del_val);

		$mutualFriCnt = count(array_intersect($logRes, $viewRes));

		return $mutualFriCnt;
		
	}

	public function remove_element($array,$value) {
  		return array_diff($array, (is_array($value) ? $value : array($value)));
	}
	
	public function getMutualfriendsabout($loguserID, $viewFriID){	
		$query = $this->db->query("SELECT CASE WHEN (sendRequestUserId = '".$loguserID."') THEN receiveRequestId ELSE sendRequestUserId END AS MyFriendID FROM `friends` as myID INNER JOIN (SELECT CASE WHEN (sendRequestUserId = '".$viewFriID."') THEN receiveRequestId ELSE sendRequestUserId END AS MyFriendID FROM `friends` WHERE (`sendRequestUserId` = '".$viewFriID."' OR `receiveRequestId` = '".$viewFriID."')) AS viewFriend ON viewFriend.MyFriendID = MyFriendID AND viewFriend.MyFriendID != '".$loguserID."' WHERE (`sendRequestUserId` = '".$loguserID."' OR `receiveRequestId` = '".$loguserID."') AND (CASE WHEN (sendRequestUserId = '".$loguserID."') THEN receiveRequestId ELSE sendRequestUserId END != '".$viewFriID."') AND (CASE WHEN (sendRequestUserId = '".$loguserID."') THEN receiveRequestId ELSE sendRequestUserId END = viewFriend.MyFriendID)");
		//echo $sql = $this->db->last_query();
		//exit;		
		$mutualabtFriCnt = $query->num_rows();		
		return $mutualabtFriCnt;
	}
	
	/* Suggest friends */
	public function getsuggestFriends($data = array()){
		$query = $this->db->query("SELECT * FROM users where userid NOT IN (SELECT CASE WHEN (friends.sendRequestUserId != '".$data['loggedUser']."') THEN friends.sendRequestUserId ELSE friends.receiveRequestId END AS MyFriendID FROM `friends` WHERE (sendRequestUserId = '".$data['loggedUser']."' OR receiveRequestId = '".$data['loggedUser']."') AND (friends.status = 1 )) AND userid NOT IN('".$data['loggedUser']."') AND userid NOT IN(1)");
	//	print_r($data);exit;
		// OR friends.status = 2
		/*Removed  status=2 on 22/07/2019 - K000118*/				
 
		 
		$myMutualusers = $query->result_array();
		if(!empty($myMutualusers)){
		    
			foreach($myMutualusers AS $cityUser){

			/*Added status=1 on 22/07/2019 - K000118*/				

				$query1 = $this->db->query("SELECT *, COUNT(MyFriendID) AS TotalMutualFriends, '".$cityUser['userid']."' AS userid FROM (SELECT CASE WHEN (sendRequestUserId = '".$data['loggedUser']."') THEN receiveRequestId ELSE sendRequestUserId END AS MyFriendID FROM `friends` as myID INNER JOIN (SELECT CASE WHEN (sendRequestUserId = '".$cityUser['userid']."') THEN receiveRequestId ELSE sendRequestUserId END AS MyFriendID FROM `friends` WHERE (`sendRequestUserId` = '".$cityUser['userid']."' OR `receiveRequestId` = '".$cityUser['userid']."') and status=1 ) AS viewFriend ON viewFriend.MyFriendID = MyFriendID AND viewFriend.MyFriendID != '".$cityUser['userid']."' and status=1 WHERE (`sendRequestUserId` = '".$data['loggedUser']."' OR `receiveRequestId` = '".$data['loggedUser']."') AND (CASE WHEN (sendRequestUserId = '".$data['loggedUser']."') THEN receiveRequestId ELSE sendRequestUserId END != '".$cityUser['userid']."') AND (CASE WHEN (sendRequestUserId = '".$data['loggedUser']."') THEN receiveRequestId ELSE sendRequestUserId END = viewFriend.MyFriendID)) AS totals");
			
				$mutualUsers[] = $query1->result_array();
				
			}


			
			$arr  = $mutualUsers;
			$sort = array();
			$arrMutual = array();
			$arrCity = array();
			$arrMutualIDs = array();
			foreach($arr as $k=>$v) {
			//	print_r($v[0]);
				if($v[0]['TotalMutualFriends'] >= 1 ){
					$arrMutual[] = $v[0];
					$arrMutualIDs[] = $v[0]['userid'];
					$sort['TotalMutualFriends'][$k] = $v[0]['TotalMutualFriends'];
				}
			}			
			if(count($arrMutualIDs) < 30 ){
				if(!empty($arrMutualIDs)){
					$string_version = implode(',', $arrMutualIDs);
					$sql = "AND userid NOT IN($string_version)";
				} else {
					$sql = "";
				}
				$queryCity = $this->db->query("SELECT '' AS MyFriendID, 0 AS TotalMutualFriends, userid FROM users where provinceId = '".$data['logUserProvince']."' AND userid NOT IN (SELECT CASE WHEN (friends.sendRequestUserId != '".$data['loggedUser']."') THEN friends.sendRequestUserId ELSE friends.receiveRequestId END AS MyFriendID FROM `friends` WHERE (sendRequestUserId = '".$data['loggedUser']."' OR receiveRequestId = '".$data['loggedUser']."') AND (friends.status = 1 OR friends.status = 2)) AND userid NOT IN('".$data['loggedUser']."') AND userid NOT IN(1) $sql");
				$arrCityqry = $queryCity->result_array();
				foreach($arrCityqry as $k=>$v) {				
					//print_r($v);
					$arrCity[] = $v;
					$sort1['TotalMutualFriends'][$k] = $v['TotalMutualFriends'];
				}
			}		
			$mergeCityMutual = array_merge($arrMutual, $arrCity);
			$TotalMutualFriends = array();
			foreach ($mergeCityMutual as $key => $row){
				$TotalMutualFriends[$key] = $row['TotalMutualFriends'];
			}
			array_multisort($TotalMutualFriends, SORT_DESC, $mergeCityMutual);
			return array_slice($mergeCityMutual, 0,30);
		} else {
			return $arr = array();
		}
	}
	
	public function getsuggestTotalFriends($data = array()){
		$query = $this->db->query("SELECT * FROM users where countryId = '".$data['logUserCountry']."' AND userid NOT IN (SELECT CASE WHEN (friends.sendRequestUserId != '".$data['loggedUser']."') THEN friends.sendRequestUserId ELSE friends.receiveRequestId END AS MyFriendID FROM `friends` WHERE (sendRequestUserId = '".$data['loggedUser']."' OR receiveRequestId = '".$data['loggedUser']."') AND (friends.status = 1 OR friends.status = 2)) AND userid NOT IN('".$data['loggedUser']."') AND userid NOT IN(1)");
		$myCityusers = $query->result_array();
		if(!empty($myCityusers)){
			foreach($myCityusers AS $cityUser){				
				$query1 = $this->db->query("SELECT *, COUNT(MyFriendID) AS TotalMutualFriends, '".$cityUser['userid']."' AS userid FROM (SELECT CASE WHEN (sendRequestUserId = '".$data['loggedUser']."') THEN receiveRequestId ELSE sendRequestUserId END AS MyFriendID FROM `friends` as myID INNER JOIN (SELECT CASE WHEN (sendRequestUserId = '".$cityUser['userid']."') THEN receiveRequestId ELSE sendRequestUserId END AS MyFriendID FROM `friends` WHERE (`sendRequestUserId` = '".$cityUser['userid']."' OR `receiveRequestId` = '".$cityUser['userid']."')) AS viewFriend ON viewFriend.MyFriendID = MyFriendID AND viewFriend.MyFriendID != '".$cityUser['userid']."' WHERE (`sendRequestUserId` = '".$data['loggedUser']."' OR `receiveRequestId` = '".$data['loggedUser']."') AND (CASE WHEN (sendRequestUserId = '".$data['loggedUser']."') THEN receiveRequestId ELSE sendRequestUserId END != '".$cityUser['userid']."') AND (CASE WHEN (sendRequestUserId = '".$data['loggedUser']."') THEN receiveRequestId ELSE sendRequestUserId END = viewFriend.MyFriendID)) AS totals ");
				$mutualUsers[] = $query1->result_array();
			}
			//$mutualUsers = $query->result_array();
			//print_r($mutualUsers);
			//exit;
			$arr  = $mutualUsers;
		} else {
			$arr = array();
		}
		return $arr;
	}
	
	public function myTotalfriends($data=array()){	
		$query = $this->db->query("SELECT * FROM friends LEFT JOIN users ON users.userid = (CASE WHEN friends.sendRequestUserId != '".$data['loggedUser']."' THEN friends.sendRequestUserId ELSE friends.receiveRequestId END) WHERE (sendRequestUserId = '".$data['loggedUser']."' OR receiveRequestId = '".$data['loggedUser']."') AND friends.status = 1");
		//echo $sql = $this->db->last_query();
		//exit;	
		return $query->result_array();
	}
	
}
