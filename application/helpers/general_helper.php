<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function getProvincename($id){
    $CI = & get_instance();
    $CI->load->model("settings_model");
    $meta = $CI->settings_model->getProvincename($id);
    return $meta;
}

function getDistrictname($id){
    $CI = & get_instance();
    $CI->load->model("settings_model");
    $meta = $CI->settings_model->getDistrictname($id);
    return $meta;
}

function getCity($id){
    $CI = & get_instance();
    $CI->load->model("settings_model");
    $meta = $CI->settings_model->getCity($id);
    return $meta;
}

function send_notification($tokens, $message){
    $url = 'http://fcm.googleapis.com/fcm/send';
    $fields = array(
		'registration_ids' => $tokens,
		'data' => $message,
		//'notification' => $message
		/*
		"data" => array(
			'media-url' => "http://www.alphansotech.com/wp-content/uploads/2015/12/Push-notification-1.jpg",
			'mediaType' => "png"
		),
		'mediaurl' => "http://www.alphansotech.com/wp-content/uploads/2015/12/Push-notification-1.jpg",	
		'notification' => array(
			"body" => "body test",
			"message" => "Testsetset",
			"title" => "KAvi test val",
			"subtitle" => "sub title value",
			"sound" => 1,
			"vibrate" => 1,
			"badge" => 1,
			"category" => "newCategory",
			"mutable-content" => 1,
			

		)*/
	);
	
    $headers = array(
		//'Authorization: key= AAAAriuzWmo:APA91bH_SXkq9c1oeHXelHF_Hmxku6svwa162soydwWoMBvK1UaKgLXtPsFe3aF0cITakan6LT8FjTd0rnhyyGgTcdk3zCe5MWZBjYtdQwsPJN7LnBKIzEZ4CYMLgY8rSLdBKC3wmUHU',
		'Authorization: key= AAAA7lWEzHQ:APA91bFQj4rYWZdaPF6n8G7LofAv_lOCoOhwQoVuq61pGFxZgs296tdgnzHSyEaV9YIk38SPRWh9eGg3XRQmo5GnnEiB0zj8kAsCCqfHVPKPXJNYHcNSuOBGsaEiPJUfltAchKescuhi',
		'Content-Type: application/json'
	);

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
	$result = curl_exec($ch);

	if ($result === FALSE) {
		die('Curl failed: ' . curl_error($ch));
	}
	curl_close($ch);
	return $result;
}

/*Only IOS push notifications*/
function send_notificationIOS($tokens, $messagedata, $message){
    $url = 'http://fcm.googleapis.com/fcm/send';
    $fields = array(
		'registration_ids' => $tokens,
		'content_available' => true,
		'mutable_content'=> true,
		'priority' => 'high',
		'data' => $messagedata,
		'notification' => $message		
	);
	
    $headers = array(
		//'Authorization: key= AAAAriuzWmo:APA91bH_SXkq9c1oeHXelHF_Hmxku6svwa162soydwWoMBvK1UaKgLXtPsFe3aF0cITakan6LT8FjTd0rnhyyGgTcdk3zCe5MWZBjYtdQwsPJN7LnBKIzEZ4CYMLgY8rSLdBKC3wmUHU',
		'Authorization: key= AAAA7lWEzHQ:APA91bFQj4rYWZdaPF6n8G7LofAv_lOCoOhwQoVuq61pGFxZgs296tdgnzHSyEaV9YIk38SPRWh9eGg3XRQmo5GnnEiB0zj8kAsCCqfHVPKPXJNYHcNSuOBGsaEiPJUfltAchKescuhi',
		'Content-Type: application/json'
	);

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
	$result = curl_exec($ch);

	if ($result === FALSE) {
		die('Curl failed: ' . curl_error($ch));
	}
	curl_close($ch);
	return $result;
}



function sendMail($from, $name, $to, $sub, $cont, $cc="", $bcc="", $attachments=array()){
	$CI = & get_instance();
	$CI->load->library('email');
	$CI->email->initialize(array(
	  'protocol' => 'smtp',
	  'smtp_host' => 'ssl://smtp.gmail.com',
	  'smtp_user' => 'knilaitsolution@gmail.com',
	  'smtp_pass' => '123@Knila',
	  'smtp_port' => 587,
	  'crlf' => "\r\n",
	  'newline' => "\r\n"
	));
	$contextOptions = array(
    'ssl' => array(
        'verify_peer' => false,
        'verify_peer_name' => false
    )
);
	$CI->email->clear();
	$CI->email->mailtype = "html";
	$CI->email->from($from, $name);
	$CI->email->to($to);
	if($cc!="") $CI->email->cc($cc);
	if($bcc!="") $CI->email->bcc($bcc);
	if(!empty($attachments)){
		foreach($attachments as $attachment){
			$CI->email->attach($attachment);
		}
	}
	$CI->email->subject($sub);
	$CI->email->message($cont);
	if($CI->email->send())
		return true;
	else
		return false;
}

    
        
function checkForAdminSession($level,$red=true,$controller=''){
    $CI = & get_instance();
    if($CI->session->userdata("username")!=""){
        if(($controller == 'Admin') && $CI->session->userdata("user_type") == $level ){
            return true;
        } else { return false; }
    } else{
        if($red==true){
            $CI->session->sess_destroy();
            $CI->session->set_flashdata("msg","Session expired");
            return true;
        }else{ return false; }
    }
}

/*function checkForUserSession($data = array()){
    $CI = & get_instance();
    $selvalidttoken = $CI->db->select('*')->from('users_authentication')->where('userid',$data['users_id'])->where('token',$data['token'])->get()->row();
    if(isset($selvalidttoken)){
        return true;
    } else {
        return false;
    }
}*/

function checkForUserSession($data = array())
{
    $CI = & get_instance();
    $selinvalidtuser = $CI->db->select('*')->from('users')->where('userid',$data['users_id'])->where('status',0)->get()->row();
    if(isset($selinvalidtuser))
    {
      $response['status'] = '401';
      $response['messages'] = 'Contact Administrator';  
      header('Content-Type: application/json');
      echo json_encode($response,true);
      exit;
    }else{
      $selvalidttoken = $CI->db->select('*')->from('users_authentication')->where('userid',$data['users_id'])->where('token',$data['token'])->get()->row();
      if(isset($selvalidttoken)){
          return true;
      } else {
          return false;
      }
    }
}

function getUserRole(){
    $CI = & get_instance();
    $admin_user_det = array("userid" => $CI->session->userdata("userid"),"UserRole" => $CI->session->userdata("userrole"),"username" => $CI->session->userdata("username"));
    return $admin_user_det;
}

function shorten_text($text, $max_length = 140, $cut_off = '...', $keep_word = false){
    if(strlen($text) <= $max_length) {
        return $text;
    }

    if(strlen($text) > $max_length) {
        if($keep_word) {
            $text = substr($text, 0, $max_length + 1);

            if($last_space = strrpos($text, ' ')) {
                $text = substr($text, 0, $last_space);
                $text = rtrim($text);
                $text .=  $cut_off;
            }
        } else {
            $text = substr($text, 0, $max_length);
            $text = rtrim($text);
            $text .=  $cut_off;
        }
    }
    return $text;
}

function getMenu(){
	$CI = & get_instance();
	$user_data = getUser();
    print_r($user_data);
}

function date_time_ago($timestamp){  
	
     $time_ago = strtotime($timestamp);  
      $current_time = time();  
      $time_difference = $current_time - $time_ago;  
      $seconds = $time_difference;  
      $minutes      = round($seconds / 60 );           // value 60 is seconds  
      $hours           = round($seconds / 3600);           //value 3600 is 60 minutes * 60 sec  
      $days          = round($seconds / 86400);          //86400 = 24 * 60 * 60;  
      $weeks          = round($seconds / 604800);          // 7*24*60*60;  
      $months          = round($seconds / 2629440);     //((365+365+365+365+366)/5/12)*24*60*60  
      $years          = round($seconds / 31553280);     //(365+365+365+365+366)/5 * 24 * 60 * 60  
      if($seconds <= 60)  
      {  
     return "Just Now";  
   }  
      else if($minutes <=60)  
      {  
     if($minutes==1)  
           {  
       return "one minute ago";  
     }  
     else  
           {  
       return "$minutes minutes ago";  
     }  
   }  
      else if($hours <=24)  
      {  
     if($hours==1)  
           {  
       return "an hour ago";  
     }  
           else  
           {  
       return "$hours hrs ago";  
     }  
   }  
      else if($days <= 7)  
      {  
     if($days==1)  
           {  
       return "yesterday";  
     }  
           else  
           {  
       return "$days days ago";  
     }  
   }  
      else if($weeks <= 4.3) //4.3 == 52/12  
      {  
     if($weeks==1)  
           {  
       return "a week ago";  
     }  
           else  
           {  
       return "$weeks weeks ago";  
     }  
   }  
       else if($months <=12)  
      {  
     if($months==1)  
           {  
       return "a month ago";  
     }  
           else  
           {  
       return "$months months ago";  
     }  
   }  
      else  
      {  
     if($years==1)  
           {  
       return "one year ago";  
     }  
           else  
           {  
       return "$years years ago";  
     }  
   } 
	/*$now = new DateTime;
    $ago = new DateTime(strtotime($timestamp));
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = array(
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    );
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' ago' : 'just now';*/

   
 } 
 
function date_time_expire($timestamp)  
{  
      $time_ago = strtotime($timestamp);  
      $current_time = time();  
      $time_difference = $time_ago-$current_time;  
      $seconds = $time_difference;  
      $minutes      = round($seconds / 60 );           // value 60 is seconds  
      $hours           = round($seconds / 3600);           //value 3600 is 60 minutes * 60 sec  
      $days          = round($seconds / 86400);          //86400 = 24 * 60 * 60;  
      $weeks          = round($seconds / 604800);          // 7*24*60*60;  
      $months          = round($seconds / 2629440);     //((365+365+365+365+366)/5/12)*24*60*60  
      $years          = round($seconds / 31553280);     //(365+365+365+365+366)/5 * 24 * 60 * 60  
      if($seconds <= 60)  
      {  
     return "Expired";  
   }  
      else if($minutes <=60)  
      {  
     if($minutes==1)  
           {  
       return "one minute left";  
     }  
     else  
           {  
       return "$minutes minutes left";  
     }  
   }  
      else if($hours <=24)  
      {  
     if($hours==1)  
           {  
       return "an hour left";  
     }  
           else  
           {  
       return "$hours hrs left";  
     }  
   }  
      else if($days <= 7)  
      {  
     if($days==1)  
           {  
       return "One day left";  
     }  
           else  
           {  
       return "$days days left";  
     }  
   }  
      else if($weeks <= 4.3) //4.3 == 52/12  
      {  
     if($weeks==1)  
           {  
       return "a week left";  
     }  
           else  
           {  
       return "$weeks weeks left";  
     }  
   }  
       else if($months <=12)  
      {  
     if($months==1)  
           {  
       return "a month left";  
     }  
           else  
           {  
       return "$months months left";  
     }  
   }  
      else  
      {  
     if($years==1)  
           {  
       return "one year left";  
     }  
           else  
           {  
       return "$years years left";  
     }  
   }  
 } 

function getTotallikecnt($id){
    $CI = & get_instance();
    $likeCount = $CI->db->select('*')->from('newsfeedlikes')->where('newsFeedsId',$id)->where('status',1)->get()->num_rows();
    if(isset($likeCount)){
        return $likeCount;
    } else {
        return 0;
    }
}

function getTotalpollcnt($id, $feedID){
    $CI = & get_instance();
	$pollqusCount = $CI->db->select('*')->from('pollinganswerbyuser')->where('pollingId',$feedID)->where('status',1)->get()->num_rows();
    $pollansCount = $CI->db->select('*')->from('pollinganswerbyuser')->where('pollingAnswerId',$id)->where('status',1)->get()->num_rows();
    if(isset($pollansCount) && isset($pollqusCount) && $pollqusCount!=0){
        return round($pollansCount / $pollqusCount *100);
    } else {
        return 0;
    }
}

function getTotalcommentscnt($id){
    $CI = & get_instance();
    $cmtCount = $CI->db->select('*')->from('newsfeedcomments')->where('newsFeedsId',$id)->where('status',1)->get()->num_rows();
    if(isset($cmtCount)){
        return $cmtCount;
    } else {
        return 0;
    }
}

function getReportabuseStatus($nId){
    $CI = & get_instance();
    $rAres = $CI->db->select('*')->from('newsfeedreport')->where('newsFeedsId',$nId)->where('status',1)->get()->result_array();
    if(isset($rAres)){
        return $rAres;
    } else {
        return 0;
    }
}

function getReportabuseStatuspro($pId){
    $CI = & get_instance();
    $rAres = $CI->db->select('*')->from('productsreport')->where('productId',$pId)->where('status',1)->get()->result_array();
    if(isset($rAres)){
        return $rAres;
    } else {
        return 0;
    }
}

function getTotalmemberscnt($id){
    $CI = & get_instance();
    $groupmemCount = $CI->db->select('*')->from('groupmembers')->where('groupid',$id)->where('status',1)->get()->num_rows();
    if(isset($groupmemCount)){
        return $groupmemCount;
    } else {
        return 0;
    }
}

function getTotalmempostcnt($id){
    $CI = & get_instance();
    $groupmemCount = $CI->db->select('*')->from('newsfeeds')->where('groupId',$id)->where('status',1)->get()->num_rows();
    if(isset($groupmemCount)){
        return $groupmemCount;
    } else {
        return 0;
    }
}

function getReportabusememStatus($gId){
    $CI = & get_instance();
    $rAres = $CI->db->select('*')->from('groupsreport')->where('groupId',$gId)->where('status',1)->get()->result_array();
    if(isset($rAres)){
        return $rAres;
    } else {
        return 0;
    }
}

function getMedia($id){
    $CI = & get_instance();
    return $mediaImage = $CI->db->select('*')->from('newsfeedimages')->where('newsFeedsId',$id)->get()->result_array();
}

function getPollsanswer($id){
    $CI = & get_instance();
    return $pollans = $CI->db->select('*')->from('pollinganswers')->where('pollingId',$id)->get()->result_array();
}

function getEventMedia($id){
    $CI = & get_instance();
    return $mediaImage = $CI->db->select('*')->from('eventimages')->where('eventId',$id)->get()->result_array();
}

function dec_enc($action, $string) {
    $output = false;
 
    $encrypt_method = "AES-256-CBC";
    $secret_key = 'Tamilethos';
    $secret_iv = 'Smartapp';
 
    // hash
    $key = hash('sha256', $secret_key);
    
    // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
    $iv = substr(hash('sha256', $secret_iv), 0, 16);
 
    if( $action == 'encrypt' ) {
        $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
        $output = base64_encode($output);
    }
    else if( $action == 'decrypt' ){
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
    }
 
    return $output;
}

function getMylikes($userId, $newsId){
	$CI = & get_instance();
    $myLikecnt = $CI->db->select('*')->from('newsfeedlikes')->where('newsFeedsId',$newsId)->where('createdBy', $userId)->where('status', 1)->get()->row();
	if(isset($myLikecnt)){
        return 1;
    } else {
        return 0;
    }
}

function getMyPolls($userId, $feedId){
	$CI = & get_instance();
    $myPollcnt = $CI->db->select('*')->from('pollinganswerbyuser')->where('pollingId',$feedId)->where('createdBy', $userId)->get()->row();
	if(isset($myPollcnt)){
        return 1;
    } else {
        return 0;
    }
}

function getMyanswerId($userId, $feedId, $ansID){
	$CI = & get_instance();
    $myPollans = $CI->db->select('*')->from('pollinganswerbyuser')->where('pollingId',$feedId)->where('pollingAnswerId', $ansID)->where('createdBy', $userId)->get()->row();
	if(isset($myPollans)){
        return "1";
    } else {
        return '0';
    }
}

function getTotalcomments($data=array()){
	$CI = & get_instance();
    $postComments = $CI->db->select('*')->from('newsfeedcomments')->where('newsFeedsId',$data['newsFeedsId'])->where('status',1)->limit($data['limit'])->offset($data['start'])->order_by('createdDate', 'DESC')->get()->result_array();
	return $postComments;
}

function getTotalCntcomments($cid){
	$CI = & get_instance();
    $postCommentscnt = $CI->db->select('*')->from('newsfeedcomments')->where('newsFeedsId',$cid)->where('status',1)->order_by('createdDate', 'DESC')->get()->result_array();
	return $postCommentscnt;
}

function group_by($key, $data) {
    $result = array();
    foreach($data as $val) {
        if(array_key_exists($key, $val)){
            $result[$val[$key]][] = $val;
        }else{
            $result[""][] = $val;
        }
    }
    return $result;
}

function generateRandomString($length) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}


?>
