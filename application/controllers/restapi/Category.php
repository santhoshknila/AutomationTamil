<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Category extends CI_Controller {
	private $error = array();
	function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->model("category_model");
        $this->load->model("auth_model");		
        $this->load->library('session');
    }
	
	public function index(){
		$userauth = array();
		$userauth['users_id']  = $this->input->get_request_header('User-ID', TRUE);
        $userauth['token']     = $this->input->get_request_header('Auth-Key', TRUE);
		if(checkForUserSession($userauth) == 1){			
			$post = $this->input->post();			
			$data = array();
			$data['title'] = "Manage Category";
			$select=array();
			$where=array();
			$whereSub=array();
			$orderby = array();
			$join = array();
			$groupby =array();
			$like = array();
			$or_like = array();
			$or_where = array();
			$where_in = array();
			$where_not = array();
			
			$where['cat.status'] = 1;
			$where['cat.parentId'] = 0;

			if (isset($post['page'])) {
				$page = $post['page'];
			} else {
				$page = 1;
			}
			$limit = 0;
			$offset = 0;
			
			$orderby['cat.name'] = 'ASC';
			$allCategory = $this->category_model->get($where,$orderby,$select,$join,$groupby,$limit,$offset,"",$like,$or_like,$or_where,$where_in);			
			$totalCategory = count($this->category_model->get($where,$orderby));
			
			$response['category'] = array();		
			if (!empty($allCategory)) {
				foreach($allCategory as $catrs){
					$subcatarr = array();
					$whereSub['cat.status'] = 1;
					$whereSub['cat.parentId'] = $catrs->categoryId;
					$subcategory = $this->category_model->get($whereSub);
					if(!empty($subcategory)){
						foreach($subcategory as $subcat){
							$thirdcatarr = array();
							$whereThird['cat.status'] = 1;
							$whereThird['cat.parentId'] = $subcat->categoryId;
							$thirdCategory = $this->category_model->get($whereThird);
							if(!empty($thirdCategory)){
								foreach($thirdCategory as $thirdcat){
									$thirdcatarr[] = array(
										'thirdCatId' => $thirdcat->categoryId,
										'thirdCatName' => $thirdcat->name,
										'thirdCatImg' => 'right.png',
									);
								}
							}
							$subcatarr[] = array(
								'subcatId' => $subcat->categoryId,
								'subcatName' => $subcat->name,
								'subcatImg' => 'right.png',
								'thirdCat' => $thirdcatarr,
							);
						}
					}
					$response['category'][] = array(
						'catId' => $catrs->categoryId,
						'catName' => $catrs->name,
						'catImg' => $catrs->image,
						'subCat' => $subcatarr,
					);
				}
				
				//$response['current_page'] = (int)$page;
				//$response['total_page'] = ceil($totalCategory / $limit);
				$response['status'] = '200';
				$response['messages'] = 'Category listings';					
				json_output($response['status'],$response);
			} else {
				$response['current_page'] = (int)0;
				$response['total_page'] = 0;
				$response['status'] = '200';
				$response['messages'] = 'No records found';					
				json_output($response['status'],$response);
			}
			
		} else {
			$response['status'] = '401';
			$response['messages'] = 'Your Authorization details are incorrect.';					
			json_output($response['status'],$response);
		}
	}
	
}
