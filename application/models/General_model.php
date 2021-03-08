<?php
class General_model extends CI_Model{

    function __construct()
    {
        parent::__construct();
    }
    
	public function getCountries($id){
		$this->db->select("countryId AS id, countryName AS name, phonecode AS code, currency_symbol AS symbol, currency_code AS currency");
        $this->db->from('country');
		if($id !=''){
			$this->db->where('countryId',$id);
		}
		$this->db->where('status', 1);		
        $query = $this->db->get();
        return $query->result();
	}
	
	public function getProvince($id){
		$this->db->select("provinceId AS id, provinceName AS name");
        $this->db->from('province');
		if($id !=0){
			$this->db->where('countryId',$id);
		}
		$this->db->where('status', 1);
		$this->db->order_by("provinceName","ASC");
        $query = $this->db->get();
        return $query->result();
	}

	public function getProvinceName($id){
		$this->db->select("provinceId AS id, provinceName AS name");
        $this->db->from('province');
		$this->db->where('provinceId',$id);
		$this->db->where('status', 1);
        $query = $this->db->get();
        return $query->result();
	}
	
	public function getDistrict($id){
		$this->db->select("districtId AS id, districtName AS name");
        $this->db->from('district');
		if($id != 0){
			$this->db->where('provinceId',$id);
		}
		$this->db->where('status', 1);
		$this->db->order_by("districtName","ASC");
        $query = $this->db->get();
        return $query->result();
	}
	
	public function getCity($id){
		$this->db->select("cityId AS id, cityName AS name");
        $this->db->from('city');
		if($id != 0){
			$this->db->where('districtId',$id);
		}
		$this->db->where('status', 1);
		$this->db->order_by("cityName","ASC");
        $query = $this->db->get();
        return $query->result();
	}
	
	public function getReligion(){
		$this->db->select("religionId AS id, religionName AS name");
        $this->db->from('religion');		
		$this->db->where('status', 1);
        $query = $this->db->get();
        return $query->result();
	}
	
	public function getGender(){
		$this->db->select("genderId AS id, genderName AS name");
        $this->db->from('gender');		
		$this->db->where('status', 1);
        $query = $this->db->get();
        return $query->result();
	}
	
	public function getGenderId($gId){
		$this->db->select("*");
        $this->db->from('gender');
		$this->db->where('status', 1);
		$this->db->where('genderId', $gId);
        $query = $this->db->get();
        return $query->result()[0];
	}
	
	public function getMothertongue(){
		$this->db->select("mothertongueId AS id, mothertongueName AS name");
        $this->db->from('mothertongue');
		$this->db->where('status', 1);
        $query = $this->db->get();
        return $query->result();
	}
	
	public function getJobtitle(){
		$this->db->select("jobtitleId AS id, jobtitleName AS name");
        $this->db->from('jobtitle');
		$this->db->where('status', 1);
		$this->db->order_by("jobtitleId","desc");
        $query = $this->db->get();
        return $query->result();
	}
	
	public function getPrivacy(){
		$this->db->select("privacyId AS id, privacyName AS name");
        $this->db->from('privacy');
		$this->db->where('status', 1);
		$this->db->order_by("privacyId","asc");
        $query = $this->db->get();
        return $query->result();
	}
	
	public function getgroupPrivacy(){
		$this->db->select("groupPrivacyId AS id, groupPrivacyName AS name");
        $this->db->from('groupprivacy');
		$this->db->where('status', 1);
		$this->db->order_by("groupPrivacyId","asc");
        $query = $this->db->get();
        return $query->result();
	}
	
	public function getpageDetails($pId){
		$this->db->select("*");
        $this->db->from('sitepages');
		$this->db->where('status', 1);
		$this->db->where('pageId', $pId);
        $query = $this->db->get();
        return $query->result()[0];
	}
	
	public function notifySettings_update($data = array()){
		if($data['notifyType'] == 'poll'){
			$data['pollNotification'] = $data['notifyStatus'];
		}		
		if($data['notifyType'] == 'push'){
			$data['pushNotification'] = $data['notifyStatus'];
		}
		if($data['notifyType'] == 'comment'){
			$data['commentNotification'] = $data['notifyStatus'];
		}
		if($data['notifyType'] == 'like'){
			$data['likeNotification'] = $data['notifyStatus'];
		}
		$this->db->where('userid', $data['userId']);
		unset($data['notifyStatus']);
		unset($data['notifyType']);
		$this->db->update('users', $data);
		return true;
	}
	
	public function privacySettings_update($data = array()){
		if($data['privacyType'] == 'dob'){
			$data['dobPrivacy'] = $data['privacyStatus'];
		}		
		if($data['privacyType'] == 'region'){
			$data['regionPrivacy'] = $data['privacyStatus'];
		}
		if($data['privacyType'] == 'mothertongue'){
			$data['mothertonguePrivacy'] = $data['privacyStatus'];
		}
		if($data['privacyType'] == 'mobileno'){
			$data['mobilenoPrivacy'] = $data['privacyStatus'];
		}
		$this->db->where('userid', $data['userId']);
		unset($data['privacyStatus']);
		unset($data['privacyType']);
		$this->db->update('users', $data);
		return true;
	}
	
}
