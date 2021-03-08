<?php
class Newsfeeds_model extends CI_Model{

    function __construct()
    {
        parent::__construct();
    }
    public function get($where = array(), $orderby = array(), $select = "nf.*", $join = array(), $group_by = "", $limit ="", $offset = 0, $row = false, $like = array(),$or_like=array(),$or_where =array(),$where_in=array()) {
        if($select == "") $select = "nf.*";
        $this->db->select($select);
        $this->db->from("newsfeeds as nf");
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
	
	public function save_update_newsfeeds($data = array()){		
		if(!empty($data['news']['newsFeedsId'])){
			$data['news']['updatedDate'] = date('Y-m-d H:i:s');
			$this->db->where("newsFeedsId", $data['news']['newsFeedsId']);
			$this->db->update('newsfeeds', $data['news']);
			$affected_rows = $this->db->affected_rows();
			if($affected_rows == 1){
				if(isset($data['newsimg']['fileTypeval'])){
					$uploadVal = $data['newsimg']['fileTypeval'];
					foreach($uploadVal as $key=>$up ){
						$newimg['newsimg']['newsFeedsId'] = $data['news']['newsFeedsId'];
						$newimg['newsimg']['fileType'] = $data['newsimg']['fileTypeval'][$key];
						$newimg['newsimg']['imagevideo_url'] = $data['newsimg']['imagevideo_url'][$key];
						$newimg['newsimg']['createdDate'] = date('Y-m-d H:i:s');
						$newimg['newsimg']['createdBy'] = $data['news']['createdBy'];
						$newimg['newsimg']['updatedDate'] = date('Y-m-d H:i:s');
						$newimg['newsimg']['updatedBy'] = $data['news']['createdBy'];
						$this->db->insert('newsfeedimages', $newimg['newsimg']);
					}
					return $data['news']['newsFeedsId'];
				}
			} else {
				return false;
			}
			return $data['news']['newsFeedsId'];
        } else {
			if(isset($data['news'])){
				$data['news']['status'] = 1;
				$data['news']['isActive'] = 1;
				$data['news']['newsType'] = 'news';				
				$data['news']['createdDate'] = date('Y-m-d H:i:s');
				$data['news']['updatedDate'] = date('Y-m-d H:i:s');
				$this->db->insert('newsfeeds', $data['news']);
				$lastId = $this->db->insert_id();
				$affected_rows = $this->db->affected_rows();
				if($affected_rows == 1){
					if(isset($data['newsimg']['fileTypeval'])){
						$uploadVal = $data['newsimg']['fileTypeval'];
						foreach($uploadVal as $key=>$up ){
							$newimg['newsimg']['newsFeedsId'] = $lastId;
							$newimg['newsimg']['fileType'] = $data['newsimg']['fileTypeval'][$key];
							$newimg['newsimg']['imagevideo_url'] = $data['newsimg']['imagevideo_url'][$key];
							$newimg['newsimg']['createdDate'] = date('Y-m-d H:i:s');
							$newimg['newsimg']['createdBy'] = $data['news']['createdBy'];
							$newimg['newsimg']['updatedDate'] = date('Y-m-d H:i:s');
							$newimg['newsimg']['updatedBy'] = $data['news']['createdBy'];
							$this->db->insert('newsfeedimages', $newimg['newsimg']);
						}						
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
	
	public function news_Delete($data=array()){
		$deleteRow = $this->db->select('*')->from('newsfeeds')->where('newsFeedsId',$data['feedID'])->where('createdBy',$data['userId'])->get()->num_rows();		
		if($deleteRow == 0) {
			return false;
		} else {
			$data['isActive'] = 0;
			$data['status'] = 0;
			$data['updatedDate'] = date('Y-m-d H:i:s');
			$data['updatedBy'] = $data['userId'];
			$this->db->where('newsFeedsId', $data['feedID']);
			$this->db->where('createdBy', $data['userId']);
			unset($data['feedID']);
			unset($data['userId']);
			$this->db->update('newsfeeds', $data);
			return true;
		}
	}

    public function getNewslisting(){
        $this->db->select('*');
        $this->db->from('newsfeeds as nf');
        $this->db->join('newsfeedlikes as nflike', 'nflike.newsFeedsId = nf.newsFeedsId', 'left');
        $this->db->join('newsfeedimages as nfimg', 'nfimg.newsFeedsId = nf.newsFeedsId', 'left');
        $this->db->join('newsfeedcomments as nfcmt', 'nfcmt.newsFeedsId = nf.newsFeedsId', 'left');
        $this->db->where('nf.isActive ', 1);
        $this->db->where('nf.reportAbuse ', 1);
        $res = $this->db->get();
        return $res->result();
    }

    public function getlikeCnt($id){
        
    }
	
	public function save_update_likefeeds($data){
		$likerow = $this->db->select('*')->from('newsfeedlikes')->where('newsFeedsId',$data['like']['newsFeedsId'])->where('createdBy',$data['like']['createdBy'])->get()->num_rows();		
		if($likerow == 0) {
			$this->db->query("INSERT INTO newsfeedlikes SET newsFeedsId = '" . (int)$data['like']['newsFeedsId'] . "', status = '" .(int)$data['like']['status'] . "', createdDate = NOW(), createdBy = '" . (int)$data['like']['createdBy'] . "', updatedDate = NOW(),  updatedBy = '" . (int)$data['like']['updatedBy'] . "'");
			return true;
		} else {
			$this->db->query("UPDATE newsfeedlikes SET status = '" .(int)$data['like']['status']. "', updatedDate = NOW() WHERE newsFeedsId = '".(int)$data['like']['newsFeedsId']."' AND createdBy = '" . (int)$data['like']['createdBy'] . "'");
			return true;
		}
	}
	
	public function getPostdetails($data = array()){
		//updatedDate as update,
		//$query = $this->db->query("SELECT * FROM (SELECT newsFeedsId as feedID,title as feedTitle,description as feedDesc,isActive as active,newsType as feedType, createdBy as user, createdDate as crdate, updatedDate as updaDate, endDate as exDate, privacyId as pID, reportAbuse as report, isActive as status FROM newsfeeds WHERE reportAbuse = 1 AND status = 1 AND isActive = 1 AND (((privacyId = 1 OR privacyId = 2) AND createdBy IN (SELECT CASE WHEN sendRequestUserId = '".$data['userid']."' THEN receiveRequestId ELSE sendRequestUserId END AS myFriends FROM friends WHERE (sendRequestUserId = '".$data['userid']."' OR receiveRequestId = '".$data['userid']."') AND status = 1 OR createdBy=1 )) OR (createdBy = '".$data['userid']."' OR privacyId = 1 ) OR (createdBy IN (SELECT userid FROM users WHERE userrole = 'admin'))) UNION ALL SELECT pollingId as feedID, pollingQuestion as feedTitle, description as feedDesc, isActive as active, pollType as feedType, createdBy as user, createdDate as crdate, updatedDate as updaDate, CONCAT(endDate, endTime) as exDate, 0 as pID, 1 as report, isActive as status FROM polling WHERE startDate <= CURRENT_DATE() AND endDate >= CURRENT_DATE()) AS post WHERE post.status=1 AND post.report = 1 ORDER BY updaDate DESC limit ".(int)$data['limit']." offset ".(int)$data['start']);
		
		$query = $this->db->query("SELECT * FROM (SELECT newsFeedsId as feedID, title as feedTitle,description as feedDesc, isActive as active, newsType as feedType, createdBy as user, createdDate as crdate, updatedDate as updaDate, endDate as exDate, privacyId as pID, reportAbuse as report, isActive as status, groupId as gId FROM newsfeeds WHERE reportAbuse = 1 AND status = 1 AND isActive = 1 AND groupId = 0 AND (((privacyId = 1 OR privacyId = 2) AND createdBy IN (SELECT CASE WHEN sendRequestUserId = '".$data['userid']."' THEN receiveRequestId ELSE sendRequestUserId END AS myFriends FROM friends WHERE (sendRequestUserId = '".$data['userid']."' OR receiveRequestId = '".$data['userid']."') AND status = 1 )) OR (createdBy = '".$data['userid']."' ) OR (createdBy IN (SELECT userid FROM users WHERE userrole = 'admin'))) UNION ALL SELECT pollingId as feedID, pollingQuestion as feedTitle, description as feedDesc, isActive as active, pollType as feedType, createdBy as user, createdDate as crdate, updatedDate as updaDate, CONCAT(endDate, endTime) as exDate, 0 as pID, 1 as report, isActive as status, '' as gId FROM polling WHERE startDate <= CURRENT_DATE() AND endDate >= CURRENT_DATE()) AS post WHERE post.status=1 AND post.report = 1 UNION ALL (SELECT fd.newsFeedsId as feedID, fd.title as feedTitle, fd.description as feedDesc, fd.isActive as active, fd.newsType as feedType, fd.createdBy as user, fd.createdDate as crdate, fd.updatedDate as updaDate, fd.endDate as exDate, fd.privacyId as pID, fd.reportAbuse as report, fd.isActive as status, fd.groupId as gId FROM newsfeeds AS fd LEFT JOIN groupmembers ON groupmembers.groupid = fd.groupId AND groupmembers.status = 1 WHERE fd.isActive = 1 AND groupmembers.receiveRequestId = '".$data['userid']."' AND fd.groupId != 0) ORDER BY updaDate DESC limit ".(int)$data['limit']." offset ".(int)$data['start']);
		//echo $this->db->last_query();
		//exit;
		return $newsfeeds = $query->result_array();	
	}
	
	public function getTotalPostdetails($data = array()){
		//$query = $this->db->query("SELECT * FROM (SELECT newsFeedsId as feedID,title as feedTitle,description as feedDesc,isActive as active,newsType as feedType, createdBy as user, createdDate as crdate, updatedDate as updaDate, endDate as exDate, privacyId as pID, reportAbuse as report, isActive as status FROM newsfeeds WHERE reportAbuse = 1 AND status = 1 AND isActive = 1 AND (((privacyId = 1 OR privacyId = 2) AND createdBy IN (SELECT CASE WHEN sendRequestUserId = '".$data['userid']."' THEN receiveRequestId ELSE sendRequestUserId END AS myFriends FROM friends WHERE (sendRequestUserId = '".$data['userid']."' OR receiveRequestId = '".$data['userid']."') AND status = 1 OR createdBy=1 )) OR (createdBy = '".$data['userid']."' OR privacyId = 1 ) OR (createdBy IN (SELECT userid FROM users WHERE userrole = 'admin'))) UNION ALL SELECT pollingId as feedID, pollingQuestion as feedTitle, description as feedDesc, isActive as active, pollType as feedType, createdBy as user, createdDate as crdate, updatedDate as updaDate, CONCAT(endDate, endTime) as exDate, 0 as pID, 1 as report, isActive as status FROM polling WHERE startDate <= CURRENT_DATE() AND endDate >= CURRENT_DATE()) AS post WHERE post.status=1 AND post.report = 1 ORDER BY updaDate DESC");
		
		$query = $this->db->query("SELECT * FROM (SELECT newsFeedsId as feedID, title as feedTitle,description as feedDesc, isActive as active, newsType as feedType, createdBy as user, createdDate as crdate, updatedDate as updaDate, endDate as exDate, privacyId as pID, reportAbuse as report, isActive as status, groupId as gId FROM newsfeeds WHERE reportAbuse = 1 AND status = 1 AND isActive = 1 AND groupId = 0 AND (((privacyId = 1 OR privacyId = 2) AND createdBy IN (SELECT CASE WHEN sendRequestUserId = '".$data['userid']."' THEN receiveRequestId ELSE sendRequestUserId END AS myFriends FROM friends WHERE (sendRequestUserId = '".$data['userid']."' OR receiveRequestId = '".$data['userid']."') AND status = 1 )) OR (createdBy = '".$data['userid']."' ) OR (createdBy IN (SELECT userid FROM users WHERE userrole = 'admin'))) UNION ALL SELECT pollingId as feedID, pollingQuestion as feedTitle, description as feedDesc, isActive as active, pollType as feedType, createdBy as user, createdDate as crdate, updatedDate as updaDate, CONCAT(endDate, endTime) as exDate, 0 as pID, 1 as report, isActive as status, '' as gId FROM polling WHERE startDate <= CURRENT_DATE() AND endDate >= CURRENT_DATE()) AS post WHERE post.status=1 AND post.report = 1 UNION ALL (SELECT fd.newsFeedsId as feedID, fd.title as feedTitle, fd.description as feedDesc, fd.isActive as active, fd.newsType as feedType, fd.createdBy as user, fd.createdDate as crdate, fd.updatedDate as updaDate, fd.endDate as exDate, fd.privacyId as pID, fd.reportAbuse as report, fd.isActive as status, fd.groupId as gId FROM newsfeeds AS fd LEFT JOIN groupmembers ON groupmembers.groupid = fd.groupId AND groupmembers.status = 1 WHERE fd.isActive = 1 AND groupmembers.receiveRequestId = '".$data['userid']."' AND fd.groupId != 0) ORDER BY updaDate DESC");
		return $newsfeeds = $query->result_array();	
	}
	
	
	public function getProfilePostdetails($data = array()){
		if(isset($data['useridPro'])){
			$sql = 'AND post.user = "'.$data['useridPro'].'"';
		} else {
			$sql = '';
		}
		//updatedDate as update,
		$query = $this->db->query("SELECT * FROM (SELECT newsFeedsId as feedID,title as feedTitle,description as feedDesc,isActive as active,newsType as feedType, createdBy as user, createdDate as crdate, updatedDate as updaDate, endDate as exDate, privacyId as pID, reportAbuse as report, isActive as status, groupId as gId FROM newsfeeds WHERE reportAbuse = 1 AND groupId = 0 AND status = 1 AND (((privacyId = 1 OR privacyId = 2) AND createdBy IN (SELECT CASE WHEN sendRequestUserId = '".$data['useridPro']."' THEN receiveRequestId ELSE sendRequestUserId END AS myFriends FROM friends WHERE (sendRequestUserId = '".$data['useridPro']."' OR receiveRequestId = '".$data['useridPro']."') AND status = 1 )) OR (createdBy = '".$data['useridPro']."' OR privacyId = 1 ) OR (createdBy IN (SELECT userid FROM users WHERE userrole = 'admin'))) UNION ALL SELECT pollingId as feedID, pollingQuestion as feedTitle, description as feedDesc, isActive as active, pollType as feedType, createdBy as user, createdDate as crdate, updatedDate as updaDate, CONCAT(endDate, endTime) as exDate, 0 as pID, 1 as report, isActive as status, '' as gId FROM polling WHERE endDate >= CURRENT_DATE()) AS post WHERE post.status=1 AND post.report = 1 $sql UNION ALL (SELECT fd.newsFeedsId as feedID, fd.title as feedTitle, fd.description as feedDesc, fd.isActive as active, fd.newsType as feedType, fd.createdBy as user, fd.createdDate as crdate, fd.updatedDate as updaDate, fd.endDate as exDate, fd.privacyId as pID, fd.reportAbuse as report, fd.isActive as status, fd.groupId as gId FROM newsfeeds AS fd LEFT JOIN groupmembers ON groupmembers.groupid = fd.groupId AND groupmembers.status = 1 WHERE fd.isActive = 1 AND fd.createdBy = '".$data['useridPro']."' AND groupmembers.receiverequestid = '".$data['useridlog']."' AND fd.groupid != 0) ORDER BY updaDate DESC limit ".(int)$data['limit']." offset ".(int)$data['start']."");
		//echo $this->db->last_query();
		//exit;
		
		//$query = $this->db->query("SELECT * FROM (SELECT newsFeedsId as feedID,title as feedTitle,description as feedDesc,isActive as active,newsType as feedType, createdBy as user, createdDate as crdate, updatedDate as updaDate, endDate as exDate, privacyId as pID, reportAbuse as report, isActive as status FROM newsfeeds WHERE reportAbuse = 1 AND status = 1 AND privacyId = 1 UNION ALL SELECT newsFeedsId as feedID,title as feedTitle,description as feedDesc,isActive as active,newsType as feedType, createdBy as user, createdDate as crdate, updatedDate as updaDate, endDate as exDate, privacyId as pID, reportAbuse as report, isActive as status FROM newsfeeds WHERE reportAbuse = 1 AND status = 1 AND ((privacyId = 2) AND createdBy IN (SELECT CASE WHEN sendRequestUserId = '".$data['useridPro']."' THEN receiveRequestId ELSE sendRequestUserId END AS myFriends FROM friends WHERE (sendRequestUserId = '".$data['useridPro']."' OR receiveRequestId = '".$data['useridPro']."') AND status = 1  )) OR (createdBy = '".$data['useridPro']."' AND privacyId = 3 ) ) AS post WHERE post.status=1 AND post.report = 1 AND post.user = '".$data['useridPro']."' ORDER BY updaDate DESC limit ".(int)$data['limit']." offset ".(int)$data['start']."");
		
		return $newsfeeds = $query->result_array();	
	}
	
	public function getProfileTotalPostdetails($data = array()){
		if(isset($data['useridPro'])){
			$sql = 'AND post.user = "'.$data['useridPro'].'"';
		} else {
			$sql = '';
		}
		$query = $this->db->query("SELECT * FROM (SELECT newsFeedsId as feedID,title as feedTitle,description as feedDesc,isActive as active,newsType as feedType, createdBy as user, createdDate as crdate, updatedDate as updaDate, endDate as exDate, privacyId as pID, reportAbuse as report, isActive as status, groupId as gId FROM newsfeeds WHERE reportAbuse = 1 AND groupId = 0 AND status = 1 AND (((privacyId = 1 OR privacyId = 2) AND createdBy IN (SELECT CASE WHEN sendRequestUserId = '".$data['useridPro']."' THEN receiveRequestId ELSE sendRequestUserId END AS myFriends FROM friends WHERE (sendRequestUserId = '".$data['useridPro']."' OR receiveRequestId = '".$data['useridPro']."') AND status = 1 )) OR (createdBy = '".$data['useridPro']."' OR privacyId = 1 ) OR (createdBy IN (SELECT userid FROM users WHERE userrole = 'admin'))) UNION ALL SELECT pollingId as feedID, pollingQuestion as feedTitle, description as feedDesc, isActive as active, pollType as feedType, createdBy as user, createdDate as crdate, updatedDate as updaDate, CONCAT(endDate, endTime) as exDate, 0 as pID, 1 as report, isActive as status, '' as gId FROM polling WHERE endDate >= CURRENT_DATE()) AS post WHERE post.status=1 AND post.report = 1 $sql UNION ALL (SELECT fd.newsFeedsId as feedID, fd.title as feedTitle, fd.description as feedDesc, fd.isActive as active, fd.newsType as feedType, fd.createdBy as user, fd.createdDate as crdate, fd.updatedDate as updaDate, fd.endDate as exDate, fd.privacyId as pID, fd.reportAbuse as report, fd.isActive as status, fd.groupId as gId FROM newsfeeds AS fd LEFT JOIN groupmembers ON groupmembers.groupid = fd.groupId AND groupmembers.status = 1 WHERE fd.isActive = 1 AND fd.createdBy = '".$data['useridPro']."' AND groupmembers.receiverequestid = '".$data['useridlog']."' AND fd.groupid != 0) ORDER BY updaDate DESC");
		return $newsfeeds = $query->result_array();	
	}
	
	
	public function save_update_commentsfeeds($data){
		$data['cmt']['status'] = 1;			
		$data['cmt']['createdDate'] = date('Y-m-d H:i:s');
		$data['cmt']['updatedDate'] = date('Y-m-d H:i:s');
		$this->db->insert('newsfeedcomments', $data['cmt']);
		$lastId = $this->db->insert_id();
		$affected_rows = $this->db->affected_rows();
		return $lastId;
	}
	
	public function save_createGroup($data){
		if(isset($data['groupName'])){
			$data['gp']['groupName'] = $data['groupName'];
			$data['gp']['groupImage'] = $data['groupImage'];
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
					$member['gpm']['userid'] = $data['groupMember'][$key];
					$member['gpm']['status'] = 1;
					$member['gpm']['createdDate'] = date('Y-m-d H:i:s');
					$member['gpm']['createdBy'] = $data['createdBy'];
					$member['gpm']['updatedDate'] = date('Y-m-d H:i:s');
					$member['gpm']['updatedBy'] = $data['createdBy'];
					$this->db->insert('groupmembers', $member['gpm']);
				}
				return $affected_rows;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	
	public function getgroupListing($userid){
		$groupList = $this->db->select('*')->from('groups')->where('createdBy',$userid)->order_by('createdDate', 'DESC')->get()->result_array();
		return $groupList;
	}
	
	public function getlikeuserListing($data = array()){
		//$postComments = $CI->db->select('*')->from('newsfeedcomments')->where('newsFeedsId',$data['newsFeedsId'])->limit($data['limit'])->offset($data['start'])->order_by('createdDate', 'DESC')->get()->result_array();
		
		$likeuserList = $this->db->select('createdBy')->from('newsfeedlikes')->where('newsFeedsId',$data['newsFeedsId'])->where('status', 1)->get()->result_array();
		return $likeuserList;
	}
	
	public function getTotallikeuserListing($feedid){
		$likeuserList = $this->db->select('createdBy')->from('newsfeedlikes')->where('newsFeedsId',$feedid)->where('status', 1)->get()->result_array();
		return $likeuserList;
	}
	
	public function save_update_reportAbusefeeds($data = array()){
		$data['reAbuse']['status'] = 1;			
		$data['reAbuse']['createdDate'] = date('Y-m-d H:i:s');
		$data['reAbuse']['updatedDate'] = date('Y-m-d H:i:s');
		$this->db->insert('newsfeedreport', $data['reAbuse']);
		$lastId = $this->db->insert_id();
		$affected_rows = $this->db->affected_rows();
		return $affected_rows;
	}
	
	public function getuserreportAbusests($userId, $feedId){
		$getuserreportAbusecnt = $this->db->select('*')->from('newsfeedreport')->where('createdBy',$userId)->where('newsFeedsId',$feedId)->get()->num_rows();
		//echo $this->db->last_query();
		//exit;
		return $getuserreportAbusecnt;
	}
	
	public function del_removeUploadfiles($data = array()){
		$this->db->where('newsfeedImageId', $data['del']['uploadRowID']);
		$this->db->delete('newsfeedimages');
		return true;
	}
	
	public function delComments($cid){
		$del['status'] = 0;
		$this->db->where('commentId', $cid);
		$this->db->update('newsfeedcomments', $del);
		$afftectedRows = $this->db->affected_rows();
		if($afftectedRows == 1){
			return true;
		} else {
			return false;
		}
	}
	
}