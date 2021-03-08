<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Products extends CI_Controller {
	private $error = array();
	function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->model("products_model");
        $this->load->model("category_model");
		$this->load->model("general_model");
        $this->load->model("locations_model");
        $this->load->model("notifications_model");
        $this->load->model("packages_model");
        $this->load->model("ads_model");
        $this->load->model("users_model");
        $this->load->model("settings_model");
        $this->load->model("ads_model");
        $this->load->model("auth_model");		
        $this->load->library('session');
        $this->load->library('email');
		$this->load->library('form_validation');
		$this->load->library('phpass');
		$this->load->helper('date');
		
    }
	
	public function index(){
		$userauth = array();
		$userauth['users_id']  = $this->input->get_request_header('User-ID', TRUE);
        $userauth['token']     = $this->input->get_request_header('Auth-Key', TRUE);
		if(checkForUserSession($userauth) == 1){
			$data = array();
			$data['title'] = "Manage Products";
			$select=array();
			$where=array();
			$orderby = array();
			$join = array();
			$groupby =array();
			$like = array();
			$or_like = array();
			$or_where = array();
			$where_in = array();
			$where_not = array();
			
			$post = $this->input->post();			
			if (isset($post['page'])) {
				$page = $post['page'];
			} else {
				$page = 1;
			}
			$limit = 4;
			
			/*User total products*/
			$myTotal_data = array(
				'userId' => $userauth['users_id'],
				'proType' => 1,
			);
			$myProducts = $this->products_model->getProductstotal($myTotal_data);
			$response['mytotalProducts'] = count($myProducts);
			
			/*My Subscription total count*/
			$mySelectpack = $this->users_model->getmySubscription($userauth['users_id']);
			if (!empty($mySelectpack)) {
				foreach($mySelectpack as $myselpack){
					$packproTotal[] = $this->products_model->getpackagePrototal($myselpack['packId']);
				}
				$packproTotalval = 0;
				foreach($packproTotal as $packproTot){
					//echo $packproTot[0]->packageProTotal;
					$packproTotalval += $packproTot[0]->packageProTotal;
					
				}
				$mypackTotal_data = array(
					'userId' => $userauth['users_id'],
					'proType' => 1,
					'packageId' => 1,
				);
				$mytotProducts = $this->products_model->getProductstotal($mypackTotal_data);
				$response['mypackageCount'] = (int)$packproTotalval;
				$response['mypackremainCount'] = (int)$packproTotalval - count($mytotProducts);
			} else {
				$response['mypackageCount'] = 0;
				$response['mypackremainCount'] = 0;
			}
			
			/*Listing Category*/
			$whereCat=array();
			$whereCat['cat.status'] = 1;
			$whereCat['cat.parentId'] = 0;
			$orderbycat['cat.name'] = 'ASC';
			$allCategory = $this->category_model->get($whereCat,$orderbycat);			
			$totalCategory = count($this->category_model->get($whereCat,$orderbycat));
			$response['category'] = array();		
			if (!empty($allCategory)) {
				foreach($allCategory as $catrs){
					/*$subcatarr = array();
					$whereSub['cat.status'] = 1;
					$whereSub['cat.parentId'] = $catrs->categoryId;
					$subcategory = $this->category_model->get($whereSub);
					if(!empty($subcategory)){
						foreach($subcategory as $subcat){
							$subcatarr[] = array(
								'subcatId' => $subcat->categoryId,
								'subcatName' => $subcat->name,
								'subcatImg' => $subcat->image,
							);
						}
					}*/
					$response['category'][] = array(
						'catId' => $catrs->categoryId,
						'catName' => $catrs->name,
						'catImg' => $catrs->image,
						//'subCat' => $subcatarr,
					);
				}
			}
			
			/*Listing Ads 1-home, 3-Both*/
			$ads_data = array(
				'proDisplayOnly' => 1,						
				'proDisplayBoth' => 3,
				'userid' => $userauth['users_id'],
				'range' => $post['range'],
				'mainlocation' => $post['mainlocation'],
				'sublocation' => $post['sublocation'],
				'category' => $post['category'],
				'subcategory' => $post['subcategory'],
				'thirdcategory' => $post['thirdcategory'],
				'minPrice' => $post['minPrice'],
				'maxPrice' => $post['maxPrice'],
				'start'  => ($page - 1) * $limit,
				'limit'  => $limit
			);
			$allAds = $this->products_model->getallAds($ads_data);			
			$response['adsProduct'] = array();
			if (!empty($allAds)){
				foreach($allAds as $ads){
					$adsimg_data = array(
						'productId' => $ads['productId'],						
						'limit'  => 1
					);
					$adsimage = $this->products_model->getProductimage($adsimg_data);
					$response['adsProduct'][] = array(
						'adsId' => $ads['productId'],
						'adsName' => $ads['proName'],
						'adsImage' => $adsimage[0]['imageurl'],
					);
				}			
			}
			/*Listing Products */
			$filter_data = array(
				'userid' => $userauth['users_id'],
				'range' => $post['range'],
				'mainlocation' => $post['mainlocation'],
				'sublocation' => $post['sublocation'],
				'category' => $post['category'],
				'subcategory' => $post['subcategory'],
				'thirdcategory' => $post['thirdcategory'],
				'minPrice' => $post['minPrice'],
				'maxPrice' => $post['maxPrice'],
				'start'  => ($page - 1) * $limit,
				'limit'  => $limit
			);			
			
			$allProducts = $this->products_model->getallProducts($filter_data);			
			$totalProducts = count($this->products_model->getProductstotal($filter_data));
			$response['productsTotal'] = $totalProducts;
			$response['products'] = array();
			if (!empty($allProducts)) {
				foreach($allProducts as $product){
					$proimg_data = array(
						'productId' => $product['productId'],						
						'limit'  => 1
					);
					$proimage = $this->products_model->getProductimage($proimg_data);	
					if($product['proPrice'] != '0.00' && $product['proCountryId'] != '0'){
						$proCurrency = $this->general_model->getCountries($product['proCountryId']);
						$proPrice = $proCurrency[0]->currency.' '.$product['proPrice'];						
					} else {
						$proPrice = '';	
					}
					$response['products'][] = array(
						'proId' => $product['productId'],
						'proName' => $product['proName'],
						'proPrice' => $proPrice,
						'proImage' => $proimage[0]['imageurl'],
					);
				}
				$response['current_page'] = (int)$page;
				$response['total_page'] = ceil($totalProducts / $limit);
				$response['status'] = '200';
				$response['messages'] = 'Product listings';	
			} else {
				$response['current_page'] = (int)0;
				$response['total_page'] = 0;
				$response['status'] = '200';
				$response['messages'] = 'No records found';					
			}
			json_output($response['status'],$response);
		} else {
			$response['status'] = '401';
			$response['messages'] = 'Your Authorization details are incorrect.';					
			json_output($response['status'],$response);
		}
	}
	
	public function save(){
		$userauth = array();
		$userauth['users_id']  = $this->input->get_request_header('User-ID', TRUE);
        $userauth['token']     = $this->input->get_request_header('Auth-Key', TRUE);
		if(checkForUserSession($userauth) == 1){
			$post = $this->input->post();			
			if(isset($post) && !empty($post)){
				/*Try product ID releated details delete*/
				if(!empty($post['removeproId'])){
					$post1['pro']['productId'] = $post['removeproId'];
					$resultDel = $this->products_model->remove_cancelProduct($post1);
				}
				
				/*Get user current paln package ID*/
				$myTotal_data = array(
					'userId' => $userauth['users_id'],
					'proType' => 1,
				);
				$myProducts = $this->products_model->getProductstotal($myTotal_data);
				$response['mytotalProducts'] = count($myProducts);
				/* 
					Dynamic package Unique ID generation format
					First - 'PACK',
					Second - 'packID',
					Thrid - 'date',
					Fourth - 'randVal',
					Ex : PACK120181120ABC123
				*/
				if(count($myProducts) >= 3 ){
					$mySubscripe = $this->users_model->getmySubscription($userauth['users_id']);
					if(!empty($post['packageId'])){
						$packageID = $post['packageId'];
						$packageUnique = 'PACK'.$post['packageId'].''.date('Ymd').''.generateRandomString(5);
						$prostatus = 0;
					} else if(!empty($post['packageId']) && empty($mySubscripe[0]['packId'])){
						$packageID = $post['packageId'];
						$packageUnique = 'PACK'.$post['packageId'].''.date('Ymd').''.generateRandomString(5);
						$prostatus = 0;
					} else if(empty($post['packageId']) && !empty($mySubscripe[0]['packId'])){
						$packageID = $mySubscripe[0]['packId'];
						$packageUnique = $mySubscripe[0]['packageUnique'];
						$prostatus = 1;
					}
					if(!empty($post['adsId']) && !empty($mySubscripe[0]['packId'])){
						$prostatus = 0;
					}
				} else if(!empty($post['adsId'])){
					$packageID = 0;
					$packageUnique = 'PACK0'.date('Ymd').''.generateRandomString(5);
					$prostatus = 0;
				} else {
					$packageID = 0;
					$packageUnique = 'PACK0'.date('Ymd').''.generateRandomString(5);
					$prostatus = 1;
				}				
				
				$post['pro']['productId'] = $post['productId'];
				$post['pro']['proName'] = $post['proName'];
				$post['pro']['procatId'] = $post['procatId'];
				$post['pro']['prosubcatId'] = $post['prosubcatId'];
				$post['pro']['prothirdcatId'] = $post['prothirdcatId'];
				$post['pro']['prolocId'] = $post['prolocId'];
				$post['pro']['prosublocId'] = $post['prosublocId'];
				$post['pro']['proDescription'] = $post['proDescription'];
				$post['pro']['proCountryId'] = $post['proCountryId'];
				$post['pro']['proPrice'] = $post['proPrice'];	
				$post['pro']['packageId'] = $post['userPack']['packId'] = $packageID;
				$post['pro']['packageUnique'] = $post['userPack']['packageUnique'] = $packageUnique;
				$post['pro']['adsId'] = $post['userPack']['adsId'] = $post['adsId'];				
				$post['pro']['status'] = $post['userPack']['status'] = $prostatus; 				
				if($post['adsId'] != '' && $post['proDisplaytype'] != ''){
					$post['pro']['proType'] = $post['userPack']['packType'] = 2;
					$post['pro']['status'] = 0;
				} else {
					$post['pro']['proType'] = $post['userPack']['packType'] = 1;
				}
				$post['pro']['proDisplaytype'] = $post['proDisplaytype'];
				$post['pro']['createdBy'] = $post['userPack']['createdBy'] = $post['userPack']['userid'] = $userauth['users_id'];
				$post['pro']['updatedBy'] = $post['userPack']['updatedBy'] = $userauth['users_id'];				
				
				if(isset($_FILES['proImages']['name']) && !empty($_FILES['proImages']['name'])){
					$files = $_FILES;
					$count = count($_FILES['proImages']['name']);
					$config = array(
						'upload_path' => "./uploads/product/",
						'allowed_types' => "jpg|png|jpeg|gif",
						'overwrite' => TRUE,
						'remove_spaces' => TRUE,
					);
					
					for($i = 0; $i < $count; $i++){
						$_FILES['userfile']['name']     = uniqid().'_'.time().'_'.$files['proImages']['name'][$i];
						$_FILES['userfile']['type']     = $files['proImages']['type'][$i];
						$_FILES['userfile']['tmp_name'] = $files['proImages']['tmp_name'][$i];
						$_FILES['userfile']['error']    = $files['proImages']['error'][$i];
						$_FILES['userfile']['size']     = $files['proImages']['size'][$i]; 
	   
						$this->load->library('upload', $config);

						if (!$this->upload->do_upload('userfile')){
							$error = array('error' => $this->upload->display_errors());
						} else {
							$fileData = $this->upload->data();							
							$post['proImg']['imageurl'][] = $fileData['file_name'];
							
							$file = $fileData['file_name'];
							$path = $fileData['full_path'];
							$config_resize['image_library'] = 'gd2';  
							$config_resize['source_image'] = $path;
							$config_resize['create_thumb'] = false;
							$config_resize['maintain_ratio'] = TRUE;
							$config_resize['width'] = 250;
							$config_resize['height'] = 250;
							$config_resize['new_image'] = './uploads/product/thumb/'.$file;
							$this->load->library('image_lib',$config_resize);
							$this->image_lib->clear();
							$this->image_lib->initialize($config_resize);
							$this->image_lib->resize();
						}
					} 
				}
				
				$result = $this->products_model->save_update_products($post);	
				
				$response['status'] = '200';
				/*Payment payfast gateway intergation*/
				/* check with product count more than 3 use this otherwise else.
				1. Check with product count
				2. package & Ads
				3. no package & ads only
				4. package only & no ads
				5. Already package purchase
				6. No package & no ads
				*/
				/*Check with product count*/
				if(!empty($post['packageAmt'])){
					$package = $post['packageAmt'];
				} else {
					$package = 0;
				}
				
				if(!empty($post['adsAmt'])){
					$ads = $post['adsAmt'];
				} else {
					$ads = 0;
				}
				$proID = $result;
				if(count($myProducts) >= 3 && (!empty($post['adsId']) || !empty($post['packageId']))){
					//echo "packages and ads";
					$response['productId'] = (string)$result;
					$response['paymentURL'] = base_url().'restapi/products/paySummary/?proID='.$proID.'&package='.$package.'&ads='.$ads.'&adstype=';
					$response['successURL'] = base_url().'restapi/products/paySuccess';
					$response['cancelURL'] = base_url().'restapi/products/payCancel';
					$response['messages'] = 'Page redirect to payment process.';
					json_output($response['status'],$response);
				} else if(!empty($post['adsId'])){
					//echo "Coming to ads";					
					$response['productId'] = (string)$result;
					$response['paymentURL'] = base_url().'restapi/products/paySummary/?proID='.$proID.'&package='.$package.'&ads='.$ads.'&adstype=';
					$response['successURL'] = base_url().'restapi/products/paySuccess';
					$response['cancelURL'] = base_url().'restapi/products/payCancel';
					$response['messages'] = 'Page redirect to payment process.';
					json_output($response['status'],$response);					
				} else {
					$response['productId'] = '';
					$response['paymentURL'] = '';
					$response['successURL'] = '';
					$response['cancelURL'] = '';
					$response['messages'] = 'Products has been created successfully';
					json_output($response['status'],$response);	
				}
			}
		} else {
			$response['status'] = '401';
			$response['messages'] = 'Your Authorization details are incorrect.';					
			json_output($response['status'],$response);
		}
	}
	
	public function paySummary(){
		$data = array();
		$data["title"] = "Payment Summary";
		$data['settings'] = $this->settings_model->getSettings(1);
		//$data["paymentDetails"] = 
		$data["payVal"] = $_REQUEST;		
		load_paysummary_template('',$data,$this);
		$this->template->render();
	}
	
	public function paySuccess($data = array()){
		$data = array();
		$data["title"] = "Payment Success";		
		//load_paysuccess_template('',$data,$this);
		//$this->template->render();
	}
	
	public function payCancel(){
		$data = array();
		$data["title"] = "Payment Cancel";
		$proID = $_REQUEST['productId'];
		/* 2 means user cancel payment */
		$post['pro']['status'] = $post['userPack']['status'] = 2; 
		$post['pro']['productId'] = $proID;
		$result = $this->products_model->save_update_products($post);
		$response['status'] = '200';
		$response['messages'] = 'Your have cancel the payment transactions.';					
		json_output($response['status'],$response);
	}
	
	public function paymentCancel(){
		$data = array();
		$data["title"] = "Payment Cancel";
		$proID = $_REQUEST['productId'];
		/* 2 means payment cancel */
		$post['pro']['status'] = $post['userPack']['status'] = 3; 
		$post['pro']['productId'] = $proID;
		$result = $this->products_model->save_update_products($post);
		$response['status'] = '200';
		$response['messages'] = 'Your payment process has been cancelled.';					
		json_output($response['status'],$response);
	}
	
	public function payNotify(){
		$data = array();
		$data["title"] = "Payment Notify";
		if($_POST['payment_status'] == 'COMPLETE'){
			/*Get the product details based on payment product ID */
			$proID_data = array(
				'productId' => $_POST['m_payment_id'],
			);		
			$getProducts = $this->products_model->getProductsdetailspay($proID_data);
			if (!empty($getProducts)){
				/*Update paid details to related product*/
				$post['pro']['productId'] = $_POST['m_payment_id'];
				$post['pro']['pf_payment_id'] = $_POST['pf_payment_id'];
				$post['pro']['payment_status'] = $_POST['payment_status'].' '.$_POST['custom_int1'];
				$post['pro']['amount_gross'] = $_POST['amount_gross'];
				$post['pro']['amount_fee'] = $_POST['amount_fee'];
				$post['pro']['amount_net'] = $_POST['amount_net'];
				$post['pro']['status'] = $post['userPack']['status'] = 1; 	
				$result = $this->products_model->save_update_products($post);
				/* Create user details and get the device token */
				$userdetails = $this->users_model->getUseriddetails($getProducts[0]['createdBy']);
				/* Get packages details */
				if($getProducts[0]['packageId'] != 0){
					$wherepack['pack.packageId'] = $getProducts[0]['packageId'];
					$packagedetails = $this->packages_model->get($wherepack);
					$packagetitle = $packagedetails[0]->title; 
				} else {
					$packagetitle = '';
				}			
				if($getProducts[0]['adsId'] != 0){
					$whereads['ad.adsId'] = $getProducts[0]['adsId'];
					$adsdetails = $this->ads_model->get($whereads);
					$adstitle = $adsdetails[0]->title; 
				} else {
					$adstitle = '';
				}
				if(!empty($packagetitle) && !empty($adstitle)){
					$title = $packagetitle.' & '.$adstitle;
				} else if(!empty($packagetitle) && empty($adstitle)){
					$title = $packagetitle;
				} else if(!empty($adstitle) && empty($packagetitle)){
					$title = $adstitle;
				}
				/* Send notifications with package or ads name */
				$subject = 'Product packages or ads purchase details';			
				$message = 'Transactions ID - '.$_POST['pf_payment_id'].'. Details - '.$title.' has been purchased successfully!';			
				$created_date = date('Y-m-d h:i');				
				$tokens[] = $userdetails->deviceToken;
				$senddetails = array("message"=>$message,"title"=> $subject,"timestamp" => $created_date);
				$message_status = send_notification($tokens, $senddetails);
				if(!empty($message_status)){					
					$notify_data = array(
						'fromUserId'    => 1,
						'toUserId'      => $getProducts[0]['createdBy'],
						'notifyType'	=> 'Product',
						'notifyReason'	=> $message,
						'createdBy'		=> 1,
						'updatedBy'		=> 1,
					);
					$receiveRequest = $this->notifications_model->notificationsLog($notify_data);					
				}
			}
		} else if($_POST['payment_status'] == 'CANCEL'){
			/* 3 means user cancel payment */
			$post['pro']['status'] = $post['userPack']['status'] = 3; 
			$post['pro']['productId'] = $proID;
			$result = $this->products_model->save_update_products($post);
		} else {
			/* 4 means user cancel payment */
			$post['pro']['status'] = $post['userPack']['status'] = 4; 
			$post['pro']['productId'] = $proID;
			$result = $this->products_model->save_update_products($post);
		}
	}
	
	public function getproductDetails($proId){
		$userauth = array();
		$userauth['users_id']  = $this->input->get_request_header('User-ID', TRUE);
        $userauth['token']     = $this->input->get_request_header('Auth-Key', TRUE);
		if(checkForUserSession($userauth) == 1){
			if(isset($proId)){
				$proID_data = array(
					'productId' => $proId,					
				);
				$getProducts = $this->products_model->getProductsdetails($proID_data);			
				$response['products'] = array();
				if (!empty($getProducts)) {
					foreach($getProducts as $product){
						/*Post user details*/
						$userdetails = $this->users_model->getUseriddetails($product['createdBy']);
						
						$proimg_data = array(
							'productId' => $product['productId'],						
							'limit'  => '',
						);
						$proimage = $this->products_model->getProductimage($proimg_data);
						$mediaarray = array();
						if(!empty($proimage)){
							foreach($proimage as $media){
								$mediaarray[] = array(
									'imageId' => $media['proimageId'],
									'fileType' => 'image',
									'path' => $media['imageurl'],
								);
							}
						}
						
						/*Category Details*/
						$wherecat['cat.parentId'] = 0;
						$wherecat['cat.categoryId'] = $product['procatId'];
						$categorymain = $this->category_model->get($wherecat);
						
						$wheresubcat['cat.parentId'] = $product['procatId'];
						$wheresubcat['cat.categoryId'] = $product['prosubcatId'];
						$categorysub = $this->category_model->get($wheresubcat);
						
						if(!empty($categorysub)){
							$category = $categorymain[0]->name.' >> '.$categorysub[0]->name;
							$categorySubname = $categorysub[0]->name;
						} else {
							$category = $categorymain[0]->name;
							$categorySubname = '';
						}
						
						/*Third level cat name */
						$wherethirdcat['cat.parentId'] = $product['prosubcatId'];
						$wherethirdcat['cat.categoryId'] = $product['prothirdcatId'];
						$categoryThird = $this->category_model->get($wherethirdcat);
						if(!empty($categoryThird)){
							$thirdCategory = $categoryThird[0]->name;
						} else {
							$thirdCategory = '';
						}
						
						/*Locations details*/
						$whereloc['loc.parentId'] = 0;
						$whereloc['loc.locationsId'] = $product['prolocId'];
						$locationmain = $this->locations_model->get($whereloc);
						
						$wheresubloc['loc.parentId'] = $product['prolocId'];
						$wheresubloc['loc.locationsId'] = $product['prosublocId'];
						$locationsub = $this->locations_model->get($wheresubloc);
						if(!empty($locationsub)){
							$location = $locationmain[0]->name.' >> '.$locationsub[0]->name;
							$locationsubname = $locationsub[0]->name;
						} else {
							$location = $locationmain[0]->name;
							$locationsubname = '';
						}
						
						$reportAbusests = $this->products_model->getuserreportAbusests($userauth['users_id'], $product['productId']);
						//$reportAbusests = 0;
						if($product['proPrice'] != '0.00'){
							$proCurrency = $this->general_model->getCountries($product['proCountryId']);
							$proPrice = $proCurrency[0]->currency.' '.$product['proPrice'];						
						} else {
							$proPrice = '';	
						}
						$response['products'] = array(
							'userId' => $userdetails->userid,
							'userName' => $userdetails->firstName,
							'userImg' => $userdetails->profileimg,
							'userPhone' => $userdetails->mobileno,
							'firebaseId' => $userdetails->firebaseId,
							'proId' => $product['productId'],
							'proName' => $product['proName'],
							'proPrice' => $proPrice,
							'proDescription' => $product['proDescription'],
							'procatId' => $product['procatId'],
							'procatName' => $categorymain[0]->name,
							'prosubcatId' => $product['prosubcatId'],
							'prosubcatName' => $categorySubname,
							'prothirdcatId' => $product['prothirdcatId'],
							'prothirdcatName' => $thirdCategory,
							'prolocId' => $product['prolocId'],
							'prolocName' => $locationmain[0]->name,
							'prosublocId' => $product['prosublocId'],
							'prosublocName' => $locationsubname,
							'proLocation' => $location,
							'reportAbuse' => $reportAbusests,
							'postedDate' => date_time_ago($product['updatedDate']),
							'media' => $mediaarray,
						);
					}					
					$response['status'] = '200';
					$response['messages'] = 'Product details listings successfully';	
				} else {					
					$response['status'] = '200';
					$response['messages'] = 'No records found';					
				}
			} else {
				$response['status'] = '200';
				$response['messages'] = 'Product Not matched';	
			}
			json_output($response['status'],$response);
		} else {
			$response['status'] = '401';
			$response['messages'] = 'Your Authorization details are incorrect.';					
			json_output($response['status'],$response);
		}
	}
	
	public function getadsProduct(){
		$userauth = array();
		$userauth['users_id']  = $this->input->get_request_header('User-ID', TRUE);
        $userauth['token']     = $this->input->get_request_header('Auth-Key', TRUE);
		if(checkForUserSession($userauth) == 1){
			/*Listing Ads 1-home, 3-Both*/
			$ads_data = array(
				'proDisplayOnly' => 2,						
				'proDisplayBoth' => 3,
			);
			$allAds = $this->products_model->getallAds($ads_data);
			$response['adsProduct'] = array();
			if (!empty($allAds)){
				foreach($allAds as $ads){
					$adsimg_data = array(
						'productId' => $ads['productId'],						
						'limit'  => 1
					);
					$adsimage = $this->products_model->getProductimage($adsimg_data);
					$response['adsProduct'][] = array(
						'adsId' => $ads['productId'],
						'adsName' => $ads['proName'],
						'adsImage' => $adsimage[0]['imageurl'],
					);
				}
				$response['status'] = '200';
				$response['messages'] = 'Ads Product listing category';	
			} else {
				$response['status'] = '200';
				$response['messages'] = 'No records found';					
			}			
			json_output($response['status'],$response);			
		} else {
			$response['status'] = '401';
			$response['messages'] = 'Your Authorization details are incorrect.';					
			json_output($response['status'],$response);
		}
	}
	
	public function addreportAbuse(){
		$userauth = array();
		$userauth['users_id']  = $this->input->get_request_header('User-ID', TRUE);
        $userauth['token']     = $this->input->get_request_header('Auth-Key', TRUE);
		if(checkForUserSession($userauth) == 1){
			$post = $this->input->post();
			if(isset($post) && !empty($post)){
				$data['reAbuse']['productID'] = $post['productID'];
				$data['reAbuse']['comments'] = $post['comments'];
				$data['reAbuse']['createdBy'] = $data['reAbuse']['updatedBy'] = $userauth['users_id'];
				$result = $this->products_model->save_update_reportAbuseproduct($data);
				$response['status'] = '200';
				if($result == ""){
					$response['messages'] = 'Report abuse details not added. Kindly try again!';
				} else if($result){					
					$response['messages'] = 'Report abuse details added successfully.';
				}
				json_output($response['status'],$response);
			} else {
				$response['status'] = '401';
				$response['messages'] = 'Report abuse details not added. Kindly try again!';					
				json_output($response['status'],$response);
			}
		} else {
			$response['status'] = '401';
			$response['messages'] = 'Your Authorization details are incorrect.';					
			json_output($response['status'],$response);
		}
	}
	
	/* Products Delete */
	public function productDelete(){
		$userauth = array();
		$userauth['users_id']  = $this->input->get_request_header('User-ID', TRUE);
        $userauth['token']     = $this->input->get_request_header('Auth-Key', TRUE);
		if(checkForUserSession($userauth) == 1){
			$post = $this->input->post();
			if(isset($post) && !empty($post)){
				$data['del']['productID'] = $post['productID'];				
				$result = $this->products_model->save_update_productDelete($data);
				$response['status'] = '200';
				if($result == ""){
					$response['messages'] = 'Product not deleted. Kindly try again!';
				} else if($result){					
					$response['messages'] = 'Product deleted successfully.';
				}
				json_output($response['status'],$response);
			} else {
				$response['status'] = '401';
				$response['messages'] = 'Product not deleted. Kindly try again!';					
				json_output($response['status'],$response);
			}
		} else {
			$response['status'] = '401';
			$response['messages'] = 'Your Authorization details are incorrect.';					
			json_output($response['status'],$response);
		}
	}
	
	/*Ads separte payment method*/
	public function productConvertads(){
		$userauth = array();
		$userauth['users_id']  = $this->input->get_request_header('User-ID', TRUE);
        $userauth['token']     = $this->input->get_request_header('Auth-Key', TRUE);
		if(checkForUserSession($userauth) == 1){
			$post = $this->input->post();
			if(isset($post) && !empty($post)){				
				$package = 0;				
				$ads = $post['adsAmt'];
				$proID = $post['productID'];
				if(!empty($post['adsId'])){
					
					$post['pro']['productId'] = $post['productID'];
					$post['pro']['adsId'] = $post['adsId'];
					$post['pro']['proType '] = 2;	
					$post['pro']['status '] = 0;	
					$post['pro']['proDisplaytype'] = $post['proDisplaytype'];	
					
					$post['userPack']['adsId'] = $post['adsId'];
					$post['userPack']['packType'] = 2;
					
					$result = $this->products_model->save_update_products($post);
					
					$response['status'] = '200';
					$response['productId'] = (string)$proID;
					$response['paymentURL'] = base_url().'restapi/products/paySummary/?proID='.$proID.'&package='.$package.'&ads='.$ads.'&adstype=adsOnly';
					$response['successURL'] = base_url().'restapi/products/payadsSuccess';
					$response['cancelURL'] = base_url().'restapi/products/payadsCancel';
					$response['messages'] = 'Page redirect to payment process.';
					json_output($response['status'],$response);					
				}
			} else {
				$response['status'] = '401';
				$response['messages'] = 'Ads details not valid. Kindly try again!';					
				json_output($response['status'],$response);
			}
		} else {
			$response['status'] = '401';
			$response['messages'] = 'Your Authorization details are incorrect.';					
			json_output($response['status'],$response);
		}
	}
	
	public function payadsSuccess($data = array()){
		$data = array();
		$data["title"] = "Payment Success";		
		//load_paysuccess_template('',$data,$this);
		//$this->template->render();
	}
	
	public function payadsCancel(){
	    //echo'<pre>';print_r($_REQUEST);exit;
		$data = array();
		$data["title"] = "Payment Cancel";
		$proID = $_REQUEST['productId'];
		
		$post['pro']['productId'] = $proID;
		$post['pro']['adsId'] = 0;
		$post['pro']['proType '] = 1;	
		$post['pro']['proDisplaytype'] = 0;	
		
		$post['userPack']['adsId'] = 0;
		$post['userPack']['packType'] = 2;
		$post['pro']['status'] = $post['userPack']['status'] = 1; 
		
		$result = $this->products_model->save_update_products($post);
		$response['status'] = '200';
		$response['messages'] = 'You have cancel the payment transactions.';					
		json_output($response['status'],$response);
	}
	
	public function paymentadsCancel(){
		$data = array();
		$data["title"] = "Payment Cancel";
		$proID = $_REQUEST['productId'];
		
		$post['pro']['productId'] = $proID;
		$post['pro']['adsId'] = 0;
		$post['pro']['proType '] = 1;	
		$post['pro']['proDisplaytype'] = 0;	
		
		$post['userPack']['adsId'] = 0;
		$post['userPack']['packType'] = 2;
		$post['pro']['status'] = $post['userPack']['status'] = 1; 
		$result = $this->products_model->save_update_products($post);
		$response['status'] = '200';
		$response['messages'] = 'Your payment process has been cancelled.';					
		json_output($response['status'],$response);
	}
	
	public function payadsNotify(){
		$data = array();
		$data["title"] = "Payment Ads Notify";
		if($_POST['payment_status'] == 'COMPLETE'){
			/*Get the product details based on payment product ID */
			$proID_data = array(
				'productId' => $_POST['m_payment_id'],
			);		
			$getProducts = $this->products_model->getProductsdetailspay($proID_data);
			if (!empty($getProducts)){
				/*Update paid details to related product*/
				$post['pro']['productId'] = $_POST['m_payment_id'];
				$post['pro']['pf_payment_id'] = $_POST['pf_payment_id'];
				$post['pro']['payment_status'] = $_POST['payment_status'];
				$post['pro']['amount_gross'] = $_POST['amount_gross'];
				$post['pro']['amount_fee'] = $_POST['amount_fee'];
				$post['pro']['amount_net'] = $_POST['amount_net'];
				$post['pro']['packageId'] = 0;
				$post['pro']['status'] = $post['userPack']['status'] = 1;
				$result = $this->products_model->save_update_products($post);
				/* Create user details and get the device token */
				$userdetails = $this->users_model->getUseriddetails($getProducts[0]['createdBy']);
				/* Get packages details */						
				if($getProducts[0]['adsId'] != 0){
					$whereads['ad.adsId'] = $getProducts[0]['adsId'];
					$adsdetails = $this->ads_model->get($whereads);
					$adstitle = $adsdetails[0]->title; 
				} else {
					$adstitle = '';
				}
				if(!empty($adstitle)){
					$title = $adstitle;
				} else {
					$title = '';
				}
				/* Send notifications with package or ads name */
				$subject = 'Product convert details';			
				$message = 'Transactions ID - '.$_POST['pf_payment_id'].'. Details - '.$title.' product converts to ads successfully!';			
				$created_date = date('Y-m-d h:i');				
				$tokens[] = $userdetails->deviceToken;
				$senddetails = array("message"=>$message,"title"=> $subject,"timestamp" => $created_date);
				$message_status = send_notification($tokens, $senddetails);
				if(!empty($message_status)){					
					$notify_data = array(
						'fromUserId'    => 1,
						'toUserId'      => $getProducts[0]['createdBy'],
						'notifyType'	=> 'Ads',
						'notifyReason'	=> $message,
						'createdBy'		=> 1,
						'updatedBy'		=> 1,
					);
					$receiveRequest = $this->notifications_model->notificationsLog($notify_data);					
				}
			}
		} else if($_POST['payment_status'] == 'CANCEL'){
			$proID_data = array(
				'productId' => $_POST['m_payment_id'],
			);	
			$getProducts = $this->products_model->getProductsdetailspay($proID_data);
			if (!empty($getProducts)){
				/*Update paid details to related product*/
				$post['pro']['productId'] = $_POST['m_payment_id'];
				$post['pro']['adsId'] = 0;
				$post['pro']['proType '] = 1;	
				$post['pro']['proDisplaytype'] = 0;	
				
				$post['userPack']['adsId'] = 0;
				$post['userPack']['packType'] = 2;
				$post['pro']['status'] = $post['userPack']['status'] = 1; 
				$result = $this->products_model->save_update_products($post);
			}
		} else {
			/* 4 means user cancel payment */
			$post['pro']['productId'] = $_POST['m_payment_id'];
			$post['pro']['adsId'] = 0;
			$post['pro']['proType '] = 1;	
			$post['pro']['proDisplaytype'] = 0;	
			
			$post['userPack']['adsId'] = 0;
			$post['userPack']['packType'] = 2;
			$post['pro']['status'] = $post['userPack']['status'] = 1; 
			$result = $this->products_model->save_update_products($post);
		}
	}
}