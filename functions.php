<?php
function sanitizeMySQL($conn, $var){
	$var = $conn->real_escape_string($var);
	$var = sanitizeString($var);
	return $var;
}

function sanitizeString($var){
	$var = (null !== (get_magic_quotes_gpc()))?stripslashes($var):null;
	$var = strip_tags($var);
	$var = htmlentities($var);
	return $var;
}

function userExists($user, $value){
	global $conn;
	if ($user == "admin"){
		$sql = " SELECT email from admin where email = '$value' ";
		$res = $conn->query($sql);
		return $res->num_rows;
	} elseif ($user == "users"){
		$sql = " SELECT email from users where email = '$value' ";
		$res = $conn->query($sql);
		return $res->num_rows;
	}
}

function userExists_edit($user, $value, $type){
	global $conn;
	$dbt = ($type == "admin")?"admin":"users";
	
	$sql = " SELECT email from users where email = '$value' and id != $user ";
	$res = $conn->query($sql);
	return $res->num_rows;
}

function get_vcode($email){
	global $conn;
	//$table = ($type == "user")?"users":"admin";
	$sql = " SELECT verification_code,seen from admin where email = $email ";
    $res = $conn->query($sql);
    if ( !$res->num_rows ){
		$sql = " SELECT verification_code,seen from users where email = $email ";
		$res = $conn->query($sql);
	}

	// getting datas from database
	$row = $res->fetch_array(MYSQLI_ASSOC);
	$seen = isset($row['seen'])?$row['seen']:"";
	$vcode = isset($row['verification_code'])?$row['verification_code']:"";
	return array($email,$seen,$vcode);
}

function cryptpass($var){
	$salt = "";
	$saltchars = array_merge(range('a','z'), range('A','Z'), range(0,9));
	for($i = 0; $i < 22; $i++){
		$salt .= $saltchars[array_rand($saltchars)];
	}
	$ans = crypt($var, "$2y$10$".$salt);
	return $ans;
}

function check_new($id){
	global $conn;
	//get the number of reviews that ststus is = unseen from reviews for a particular compo_id
	$sql = " SELECT * from reviews where status = 'unseen' and compo_id = $id ";
	$res = $conn->query($sql);
	return $res->num_rows;
}

function update_comm_stat($id){
	global $conn;
	//get the number of reviews that ststus is = unseen from reviews for a particular compo_id
	$sql = " UPDATE reviews set status = 'seen' where status = 'unseen' and id = $id ";
	$res = $conn->query($sql);
}

function compo_comment_update_notify($q){
	global $conn;
	global $xml;
	$sql = " SELECT distinct reviewer_user_id,reviewer_admin_id from reviews where compo_id = $q ";
	$res = $conn->query($sql);
	if ( !empty($res) ){
		$rows = $res->num_rows;
		for($i = 0; $i < $rows; $i++){
			$res->data_seek($i);
			$row = $res->fetch_array(MYSQLI_ASSOC);

			//compo comment updated
			if(isset($row['reviewer_user_id'])){
				$sql = " SELECT email from users where id = ". $row['reviewer_user_id'] ." ";
				$row = $conn->query($sql)->fetch_array(MYSQLI_ASSOC);
				$email = $row['email'];
				$message = $xml->message[3]->value;
				$url = "composition.php?q=$q";
				$ans = notify($email,$message,$url);
			}elseif(isset($row['reviewer_admin_id'])){
				$sql = " SELECT email from admin where id = ". $row['reviewer_admin_id'] ." ";
				$row = $conn->query($sql)->fetch_array(MYSQLI_ASSOC);
				$email = $row['email'];
				$message = $xml->message[3]->value;
				$url = "composition.php?q=$q";
				$ans = notify($email,$message,$url);
			}
		}
	}
}

function compo_like_update_notify($q){
	global $conn;
	global $xml;
	$sql = " SELECT distinct reviewer_user_id,reviewer_admin_id from impression where compo_id = $q ";
	$res = $conn->query($sql);
	if ( !empty($res) ){
		$rows = $res->num_rows;
		for($i = 0; $i < $rows; $i++){
			$res->data_seek($i);
			$row = $res->fetch_array(MYSQLI_ASSOC);

			//compo liked updated
			if(isset($row['reviewer_user_id'])){
				$sql = " SELECT email from users where id = ". $row['reviewer_user_id'] ." ";
				$row = $conn->query($sql)->fetch_array(MYSQLI_ASSOC);
				$email = $row['email'];
				$message = $xml->message[2]->value;
				$url = "composition.php?q=$q";
				$ans = notify($email,$message,$url);
			}elseif(isset($row['reviewer_admin_id'])){
				$sql = " SELECT email from admin where id = ". $row['reviewer_admin_id'] ." ";
				$row = $conn->query($sql)->fetch_array(MYSQLI_ASSOC);
				$email = $row['email'];
				$message = $xml->message[2]->value;
				$url = "composition.php?q=$q";
				$ans = notify($email,$message,$url);
			}
		}
	}
}

function logged_in(){
	global $conn;
	if( !empty($_COOKIE["type"]) && !empty($_COOKIE["u_id"]) ){
		$type = $_COOKIE["type"];
		$uid = $_COOKIE["u_id"];
		$url = $_SERVER["PHP_SELF"];
		$sql = " SELECT mode_no from users where id = $uid ";
		$res = $conn->query($sql);
		if ( $res ){ $row = $res->fetch_array(MYSQLI_ASSOC); $mode_no = $row['mode_no']; }

		if(strstr($url,$type)){
			return array($type,$uid,$mode_no);
		}else{
			if($type == "user"){
				$url = str_replace("admin",$type,$url);
				header("Location: ".$url);
			}elseif($type == "admin"){
				$url = str_replace("user",$type,$url);
				header("Location: ".$url);
			}
		}
		
	}else{
		header("Location: ../login.php");
	}
}

function logout(){
	setcookie("u_id",null,time() - 3600,"/");
	setcookie("type",null,time() - 3600,"/");
	header("Location: ../login.php");
}

function notify($email,$message,$url){
	global $conn;
	$status = "unseen";
	
	//query
	$sql = " INSERT into notification (email_to,message,msg_url,status) values ('$email','$message','$url','$status') ";
	$res = $conn->query($sql);
    //return $value;
    if (!$res) {
        return "Unknown Error";
    }
}

function get_user($type,$uid){
	global $conn;
	$table = ($type == "user")?"users":"admin";
	$sql = " SELECT firstname,lastname,email from $table where id = $uid ";
    $res = $conn->query($sql);
    if ( $res->num_rows ){
        // getting datas from database
        $row = $res->fetch_array(MYSQLI_ASSOC);
        $fname = isset($row['firstname'])?$row['firstname']:"";
        $lname = isset($row['lastname'])?$row['lastname']:"";
		$email = isset($row['email'])?$row['email']:"";
		return array($fname,$lname,$email);
    }else{
        return "<br>Author not found.";
    }
}

function sentdatify($db_date){
	$d=strtotime($db_date);
	//return time();	1516176299
	//return $d;		1515733883
	$d = date("D, d M Y", $d);
	if($d != "Thu, 01 Jan 1970"){
		return $d;
	}else{
		return "";
	}
}

function commdatify($db_date){
	//return $db_date;
	$d=strtotime($db_date);
	$diff = time() - $d;
	//return ceil((time() - $d) / 60 / 60 / 24 / 7 / 4 / 12 );
	//return date_sunset(time());
	$d = date("D, d M Y", $d);
	if($d != "Thu, 01 Jan 1970"){
		switch($diff){
			case ceil($diff / 60) == 1 ://now
				return "just now.";
			break;
			case ceil($diff / 60 ) == 2 ://minute ago
				return "a minute ago.";
			break;
			case ceil($diff / 60 ) > 1 && ceil($diff / 60 ) < 60://minutes
				return ceil($diff / 60 )." minutes ago.";
			break;
			case ceil($diff / 60 / 60) == 2://hour ago
				return "an hour ago.";
			break;
			case ceil($diff / 60 / 60) > 1 && ceil($diff / 60 / 60) < 24://hours
				return ceil($diff / 60 / 60 )." hours ago.";
			break;
			case ceil($diff / 60 / 60 / 24) == 2://yday
				return "yesterday.";
			break;
			case ceil($diff / 60 / 60 / 24) > 1 && ceil($diff / 60 / 60 / 24) < 7://days
				return ceil($diff / 60 / 60 / 24)." days ago.";
			break;
			case ceil($diff / 60 / 60 / 24 / 7) == 2://lastweek
				return "last week.";
			break;
			case ceil($diff / 60 / 60 / 24 / 7) > 1 && ceil($diff / 60 / 60 / 24 / 7) < 4://weeks
				return ceil($diff / 60 / 60 / 24 / 7)." weeks ago.";
			break;
			case ceil($diff / 60 / 60 / 24 / 7 / 4) == 2 ://month ago
				return "last month.";
			break;
			case ceil($diff / 60 / 60 / 24 / 7 / 4) > 1 && ceil($diff / 60 / 60 / 24 / 7 / 4) < 12 ://months
				return ceil($diff / 60 / 60 / 24 / 7 / 4)." months ago.";
			break;
			case ceil($diff / 60 / 60 / 24 / 7 / 4 / 12) == 2 ://year ago
				return "last year.";
			break;
			case ceil($diff / 60 / 60 / 24 / 7 / 4 / 12) > 1 ://years
				return ceil($diff / 60 / 60 / 24 / 7 / 4 / 12)." years ago.";
			break;
		}
	}else{
		return "";
	}
}
?>