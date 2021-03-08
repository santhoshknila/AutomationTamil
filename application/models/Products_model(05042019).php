<?php
class Products_model extends CI_Model{

    function __construct()
    {
        parent::__construct();
    }
    public function get($where = array(), $orderby = array(), $select = "pro.*", $join = array(), $group_by = "", $limit ="", $offset = 0, $row = false, $like = array(),$or_like=array(),$or_where =array(),$where_in=array()) {
        if($select == "") $select = "pro.*";
        $this->db->select($select);
        $this->db->from("products as pro");
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
		//echo $this->db->last_query();
		//exit;
        if($newsfeeds->num_rows() > 0){
            if(!$row) return $newsfeeds->result();
            return $newsfeeds->row();
        }
		
        return array();
    }
	
	public function save_update_products($data = array()){		
		if(!empty($data['pro']['productId'])){
			$data['pro']['updatedDate'] = date('Y-m-d H:i:s');
			$this->db->where("productId", $data['pro']['productId']);
			$this->db->update('products', $data['pro']);
			$affected_rows = $this->db->affected_rows();
			if($affected_rows == 1){
				$this->db->where("productId", $data['pro']['productId']);
				$this->db->update('userpackages', $data['userPack']);
				
				if(isset($data['proImg']['imageurl'])){
					$uploadVal = $data['proImg']['imageurl'];
					foreach($uploadVal as $valrs ){							
						$newimg['proImg']['productId'] = $lastId;
						$newimg['proImg']['imageurl'] = $valrs;
						$newimg['proImg']['createdDate'] = date('Y-m-d H:i:s');
						$newimg['proImg']['createdBy'] = $data['pro']['createdBy'];
						$newimg['proImg']['updatedDate'] = date('Y-m-d H:i:s');
						$newimg['proImg']['updatedBy'] = $data['pro']['createdBy'];
						$this->db->insert('productsimages', $newimg['proImg']);
					}
					return $data['pro']['productId'];
				}
			} else {
				return false;
			}
			return $data['pro']['productId'];
        } else {
			if(isset($data['pro'])){				
				$data['pro']['reportAbuse'] = 1;
				$data['pro']['startDate'] = $data['userPack']['startDate'] = date('Y-m-d H:i:s');
				$data['pro']['endDate'] = $data['userPack']['endDate']  = date('Y-m-d H:i:s', strtotime("+30 days"));
				$data['pro']['createdDate'] = $data['userPack']['createdDate'] = date('Y-m-d H:i:s');
				$data['pro']['updatedDate'] = $data['userPack']['updatedDate'] = date('Y-m-d H:i:s');				
				$this->db->insert('products', $data['pro']);
				$lastProId = $this->db->insert_id();
				$affected_rows = $this->db->affected_rows();
				if($affected_rows == 1){
					/*User packages add to userpackages table*/					
					$data['userPack']['productId'] = $lastProId;					
					$this->db->insert('userpackages', $data['userPack']);
					
					if(!empty($data['proImg']['imageurl'])){
						$uploadVal = $data['proImg']['imageurl'];
						foreach($uploadVal as $valrs ){							
							$newimg['proImg']['productId'] = $lastProId;
							$newimg['proImg']['imageurl'] = $valrs;
							$newimg['proImg']['createdDate'] = date('Y-m-d H:i:s');
							$newimg['proImg']['createdBy'] = $data['pro']['createdBy'];
							$newimg['proImg']['updatedDate'] = date('Y-m-d H:i:s');
							$newimg['proImg']['updatedBy'] = $data['pro']['createdBy'];
							$this->db->insert('productsimages', $newimg['proImg']);
						}
						return $lastProId;
					} else {
						$newimg['proImg']['productId'] = $lastProId;
						$newimg['proImg']['imageurl'] = 'no_image.png';
						$newimg['proImg']['createdDate'] = date('Y-m-d H:i:s');
						$newimg['proImg']['createdBy'] = $data['pro']['createdBy'];
						$newimg['proImg']['updatedDate'] = date('Y-m-d H:i:s');
						$newimg['proImg']['updatedBy'] = $data['pro']['createdBy'];
						$this->db->insert('productsimages', $newimg['proImg']);
						return $lastProId;
					}
					return $lastProId;
				} else {
					return false;
				}
			} else {
				return false;
			}
        }
	}
	
	public function getallAds($data = array()){
		$sql = '';
		/*Category and Subcategory*/
		if(!empty($data['category']) && empty($data['subcategory']) && empty($data['thirdcategory'])){
			$sql .= " AND procatId='".$data['category']."' AND (proDisplaytype = 2 OR proDisplaytype = 3)";
		} else if(!empty($data['category']) && !empty($data['subcategory']) && empty($data['thirdcategory'])){
			$sql .= " AND procatId='".$data['category']."' AND prosubcatId='".$data['subcategory']."' AND (proDisplaytype = 1 OR proDisplaytype = 3)";
		} else if(!empty($data['category']) && !empty($data['subcategory']) && !empty($data['thirdcategory'])){
			$sql .= " AND procatId='".$data['category']."' AND prosubcatId='".$data['subcategory']."' AND prothirdcatId='".$data['thirdcategory']."' AND (proDisplaytype = 1 OR proDisplaytype = 3)";
		} else {
			$sql .= " AND (proDisplaytype = 1 OR proDisplaytype = 3) ";
		}
		
		/*Location and Sublocation*/
		if(!empty($data['mainlocation']) && empty($data['sublocation'])){
			$sql .= " AND prolocId='".$data['mainlocation']."'";
		} else if(!empty($data['mainlocation']) && !empty($data['sublocation'])){
			$sql .= " AND prolocId='".$data['mainlocation']."' AND prosublocId='".$data['sublocation']."'";
		} else {
			$sql .= "";
		}		
		
		/*Min and Max price*/
		if(!empty($data['minPrice']) && !empty($data['maxPrice'])){
			$sql .= " AND proPrice >='".$data['minPrice']."' AND proPrice <='".$data['maxPrice']."'";
		} else {
			$sql .= "";
		}	
		
		/* Low to high and High to low */
		if(!empty($data['range']) && $data['range'] == "lowtohigh"){
			$sql .= " ORDER BY proPrice ASC";
		} else if(!empty($data['range']) && $data['range'] == "hightolow"){
			$sql .= " ORDER BY proPrice DESC";
		} else {
			$sql .= " ORDER BY updatedDate DESC";
		}
		
		$query = $this->db->query("SELECT * FROM products AS pro WHERE pro.proType=2 AND pro.status=1 AND pro.reportAbuse=1 AND pro.procatId!=0 $sql");
		//echo $sql = $this->db->last_query();
		//exit;
		return $adsResult = $query->result_array();	
	}
	
	public function getProductimage($data = array()){
		if(empty($data['limit'])){
			$query = $this->db->query("SELECT * FROM productsimages AS proImg WHERE proImg.productId='".(int)$data['productId']."' ORDER BY updatedDate ASC");			
		} else {
			$query = $this->db->query("SELECT * FROM productsimages AS proImg WHERE proImg.productId='".(int)$data['productId']."' ORDER BY updatedDate ASC limit ".(int)$data['limit']);
		}
		return $proImg = $query->result_array();	
	}
	
	public function getallProducts($data = array()){		
		$sql = '';
		/*Category and Subcategory and third level Category*/
		if(!empty($data['category']) && empty($data['subcategory']) && empty($data['thirdcategory'])){
			$sql .= " AND procatId='".$data['category']."'";
		} else if(!empty($data['category']) && !empty($data['subcategory']) && empty($data['thirdcategory'])){
			$sql .= " AND procatId='".$data['category']."' AND prosubcatId='".$data['subcategory']."'";
		} else if(!empty($data['category']) && !empty($data['subcategory']) && !empty($data['thirdcategory'])){
			$sql .= " AND procatId='".$data['category']."' AND prosubcatId='".$data['subcategory']."' AND prothirdcatId='".$data['thirdcategory']."'";
		} else {
			$sql .= "";
		}
		
		/*Location and Sublocation*/
		if(!empty($data['mainlocation']) && empty($data['sublocation'])){
			$sql .= " AND prolocId='".$data['mainlocation']."'";
		} else if(!empty($data['mainlocation']) && !empty($data['sublocation'])){
			$sql .= " AND prolocId='".$data['mainlocation']."' AND prosublocId='".$data['sublocation']."'";
		} else {
			$sql .= "";
		}		
		
		/*Min and Max price*/
		if(!empty($data['minPrice']) && !empty($data['maxPrice'])){
			$sql .= " AND proPrice >='".$data['minPrice']."' AND proPrice <='".$data['maxPrice']."'";
		} else {
			$sql .= "";
		}	
		
		/* Low to high and High to low */
		if(!empty($data['range']) && $data['range'] == "lowtohigh"){
			$sql .= " ORDER BY proPrice ASC";
		} else if(!empty($data['range']) && $data['range'] == "hightolow"){
			$sql .= " ORDER BY proPrice DESC";
		} else if(!empty($data['minPrice']) && !empty($data['maxPrice'])){
			$sql .= " ORDER BY proPrice ASC";
		} else {
			$sql .= " ORDER BY updatedDate DESC";
		}
			
		$query = $this->db->query("SELECT * FROM products AS pro WHERE pro.status=1 AND pro.reportAbuse=1 AND pro.procatId!=0 $sql limit ".(int)$data['limit']." offset ".(int)$data['start']);
		//echo $this->db->last_query();
		//exit;
		return $adsResult = $query->result_array();	
	}
	
	public function getProductstotal($data = array()){
		$sql = '';
		if(!empty($data['packageId']) && $data['packageId'] == 1){
			$sql .= " AND packageId != 0 ";
		} else {
			$sql .= '';
		}
		if(!empty($data['proType'])){
			$sql .= " AND proType = '".$data['proType']."'";
		} else {
			$sql .= '';
		}
		if(!empty($data['userId'])){
			$sql .= " AND createdBy = '".$data['userId']."'";
		} else {
			$sql .= '';
		}
		/*Category and Subcategory and third level Category*/
		if(!empty($data['category']) && empty($data['subcategory']) && empty($data['thirdcategory'])){
			$sql .= " AND procatId='".$data['category']."'";
		} else if(!empty($data['category']) && !empty($data['subcategory']) && empty($data['thirdcategory'])){
			$sql .= " AND procatId='".$data['category']."' AND prosubcatId='".$data['subcategory']."'";
		} else if(!empty($data['category']) && !empty($data['subcategory']) && !empty($data['thirdcategory'])){
			$sql .= " AND procatId='".$data['category']."' AND prosubcatId='".$data['subcategory']."' AND prothirdcatId='".$data['thirdcategory']."'";
		} else {
			$sql .= "";
		}
		
		/*Location and Sublocation*/
		if(!empty($data['mainlocation']) && empty($data['sublocation'])){
			$sql .= " AND prolocId='".$data['mainlocation']."'";
		} else if(!empty($data['mainlocation']) && !empty($data['sublocation'])){
			$sql .= " AND prolocId='".$data['mainlocation']."' AND prosublocId='".$data['sublocation']."'";
		} else {
			$sql .= "";
		}		
		
		/*Min and Max price*/
		if(!empty($data['minPrice']) && !empty($data['maxPrice'])){
			$sql .= " AND proPrice >='".$data['minPrice']."' AND proPrice <='".$data['maxPrice']."'";
		} else {
			$sql .= "";
		}
		
		/* Low to high and High to low */
		if(!empty($data['range']) && $data['range'] == "lowtohigh"){
			$sql .= " ORDER BY proPrice ASC";
		} else if(!empty($data['range']) && $data['range'] == "hightolow"){
			$sql .= " ORDER BY proPrice DESC";
		} else {
			$sql .= " ORDER BY updatedDate DESC";
		}
		
		$query = $this->db->query("SELECT * FROM products AS pro WHERE pro.status IN(1,5) AND pro.reportAbuse=1 AND pro.procatId!=0 $sql");
		//echo $sql = $this->db->last_query();
		//exit;
		return $adsResultCnt = $query->result_array();	
	}
	
	public function getProductsdetails($data = array()){
		$query = $this->db->query("SELECT * FROM products AS pro WHERE pro.status=1 AND pro.reportAbuse=1 AND pro.procatId!=0 AND pro.productId='".(int)$data['productId']."'");
		//echo $sql = $this->db->last_query();
		//exit;
		return $adsResult = $query->result_array();	
	}
	
	public function getProductsdetailspay($data = array()){
		$query = $this->db->query("SELECT * FROM products AS pro WHERE pro.status=0 AND pro.productId='".(int)$data['productId']."'");
		return $adsResult = $query->result_array();	
	}
	
	public function save_update_reportAbuseproduct($data = array()){
		$data['reAbuse']['status'] = 1;			
		$data['reAbuse']['createdDate'] = date('Y-m-d H:i:s');
		$data['reAbuse']['updatedDate'] = date('Y-m-d H:i:s');
		$this->db->insert('productsreport', $data['reAbuse']);
		$lastId = $this->db->insert_id();
		$affected_rows = $this->db->affected_rows();
		return $affected_rows;
	}
	
	public function save_update_productDelete($data = array()){
		$data['del']['status'] = 5;
		$data['del']['updatedDate'] = date('Y-m-d H:i:s');
		$this->db->where("productId", $data['del']['productID']);
		unset($data['del']['productID']);
		$this->db->update('products', $data['del']);
		//echo $this->db->last_query();
		//exit;
		$affected_rows = $this->db->affected_rows();
		return $affected_rows;
	}
	
	public function getuserreportAbusests($userId, $productId){
		$getuserreportAbusecnt = $this->db->select('*')->from('productsreport')->where('createdBy',$userId)->where('productId',$productId)->get()->num_rows();
		//echo $this->db->last_query();
		//exit;
		return $getuserreportAbusecnt;
	}
	
	public function getpackagePrototal($data){
		$query = $this->db->query("SELECT * FROM packages AS pack WHERE packageId = $data");
		//echo $this->db->last_query();
		//exit;
		return $cntResult = $query->result();
	}
	
	/*Payment notify log*/
	public function notifypayLogins($val){
		/*$data['log']['payLog'] = 1;			
		$data['log']['payStatus'] = $val;	
		$this->db->insert('notifypayLog', $data['log']);
		$lastId = $this->db->insert_id();*/
		
		
		$query = $this->db->query("INSERT INTO `notifypaylog` (`payLog`, `payStatus`) VALUES ('1', '".$val."')");
		$affected_rows = $this->db->affected_rows();
		if($affected_rows == 1){
			return true;
		} else {
			return false;
		}
	}
	
	public function remove_cancelProduct($data = array()){
		$this->db->where('productId', $data['pro']['productId']);
		$this->db->delete('products'); 
		
		$this->db->where('productId', $data['pro']['productId']);
		$this->db->delete('userpackages'); 
		
		$this->db->where('productId', $data['pro']['productId']);
		$this->db->delete('productsimages');
	}
}