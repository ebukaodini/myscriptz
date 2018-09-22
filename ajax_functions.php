<?php
//required files
require_once("db_connection.php");
require_once("functions.php");
require_once("options.php");
require_once("mailing.php");
list($type,$uid) = logged_in();

//get details
$func = !empty($_GET["func"])?trim($_GET["func"]):"";
$value1 = !empty($_GET["value1"])?trim($_GET["value1"]):"";
$value2 = !empty($_GET["value2"])?trim($_GET["value2"]):"";
$value3 = !empty($_GET["value3"])?trim($_GET["value3"]):"";

//sterilize datas
$value1 = sanitizeMySQL($conn, $value1);
$value2 = sanitizeMySQL($conn, $value2);
$value3 = sanitizeMySQL($conn, $value3);

//variables <span class="w3-medium w3-center w3-text-green" id="ok" style="display:none"></span>
//$uid = 1;//user's id to be gotten from session

switch($func){
    case "del_compo":
        $ans = del_compo($value1);
        echo ($ans == "")?"ok":$ans;
    break;
    case "del_file":
        $ans = del_file($value1,$value2,$value3);
        echo ($ans == "")?"ok":$ans;
    break;
    case "updatefname":
        $ans = update_fname($uid,$value1);
        echo ($ans == "")?"ok":$ans;
    break;
    case "updatelname":
        $ans = update_lname($uid,$value1);
        echo ($ans == "")?"ok":$ans;
    break;
    case "updateemail":
        $ans = update_email($uid,$value1);
        echo ($ans == "")?"ok":$ans;
    break;
    case "updatepass":
        $ans = update_pass($uid,$value1);
        echo ($ans == "")?"ok":$ans;
    break;
    case "updateseen":
        $ans = update_seen($uid,$value1);
        echo ($ans == "")?"ok":$ans;
    break;
    case "updatetags":
        $ans = update_tags($uid,$value1);
        echo ($ans == "")?"ok":$ans;
    break;
    case "update_file":
        $ans = update_filename($uid,$value1,$value2);
        echo ($ans == "")?"ok":$ans;
    break;
    case "like":
        $ans = like($value1,$value2,$value3);
        echo ($ans == "")?"ok":$ans;
    break;
    case "unlike":
        $ans = unlike($value1,$value2,$value3);
        echo ($ans == "")?"ok":$ans;
    break;
    case "comment":
        $ans = comment($uid,$value1,$value2,$value3);
        echo ($ans == "")?"ok":$ans;
    break;
    case "add_subscriber":
        $ans = subscriber($value1);
        echo ($ans == "")?"ok":$ans;
    break;
    case "update_notify_stat":
        $ans = update_notify_stat($value1);
        echo ($ans == "")?"ok":$ans;
    break;
     case "article_comment":
        $ans = article_comment($value1,$value2);
        echo ($ans == "")?"ok":$ans;
    break;
}

//functions
function del_compo($id){
    global $conn;
    $sql = " DELETE from composition where id = $id ";
    $res = $conn->query($sql);
    if (!$res) {
        $Err = "Composition not deleted.";
        return $Err;
    }
}

function del_file($id,$filename,$filetype){
    global $conn;
    $path = "../uploads/". $filetype ."s/";
    $sql = " DELETE from files where id = $id ";
    $res = $conn->query($sql);
    if (!$res) {
        $Err = "File not deleted.";
        return $Err;
    }else{
        if(file_exists($path.$filename)){
            unlink($path.$filename);
        }
    }
}

function update_fname($uid,$value){
    global $conn;
    $sql = " UPDATE users set firstname = '$value' where id = $uid ";
    $res = $conn->query($sql);
    if (!$res) {
        $Err = "FirstName not updated.";
        return $Err;
    }
}

function update_lname($uid,$value){
    global $conn;
    $sql = " UPDATE users set lastname = '$value' where id = $uid ";
    $res = $conn->query($sql);
    if (!$res) {
        $Err = "LastName not updated.";
        return $Err;
    }
}

function update_email($uid,$value){
    global $conn;
    $sql = " UPDATE users set email = '$value' where id = $uid ";
    $res = $conn->query($sql);
    if (!$res) {
        $Err = "Email not updated.";
        return $Err;
    }
}

function update_pass($uid,$value){
    global $conn;

    //encrypting
    $value = cryptpass($value);

    // query
    $sql = " UPDATE users set password = '$value' where id = $uid ";
    $res = $conn->query($sql);
    if (!$res) {
        $Err = "Password not updated.";
        return $Err;
    }
}

function update_seen($uid,$value){
    global $conn;
    $sql = " UPDATE users set public_name = '$value' where id = $uid ";
    $res = $conn->query($sql);
    if (!$res) {
        $Err = "Name Shown not updated.";
        return $Err;
    }
}

function update_filename($uid,$id,$value){
    global $conn;
    $sql = " UPDATE files set filename = '$value' where user_id = $uid and id = $id ";
    $res = $conn->query($sql);
    if (!$res) {
        $Err = "Filename not updated.";
        return $Err;
    }
}

function like($compo,$viewer,$value){
    global $conn;
    // onlike increase the no-liked in composition by 1 
    // and add the viewer_user_id and compo_id to the impression db
    $value = $value + 1;
    $sql = " UPDATE composition set no_liked = '$value' where id = $compo ";
    $res = $conn->query($sql);
    if ( $res ) {
        $sql = " INSERT into impression(reviewer_user_id,compo_id) values($viewer,$compo)";
        $res = $conn->query($sql);
        //return $value;
        if (!$res) {
            return "Unknown Error";
        }
    }else{
        $Err = "Unknown Error";
        return $Err;
    }
}

function unlike($compo,$viewer,$value){
    global $conn;
    // on-unlike decrease the no-liked in composition by 1 
    // and add the viewer_user_id and compo_id to the impression db
    $value = $value - 1;
    $sql = " UPDATE composition set no_liked = '$value' where id = $compo ";
    $res = $conn->query($sql);
    if ( $res ) {
        $sql = " DELETE from impression where reviewer_user_id = $viewer and compo_id = $compo ";
        $res = $conn->query($sql);
        //return $value;
        if (!$res) {
            return "Unknown Error";
        }
    }else{
        $Err = "Unknown Error";
        return $Err;
    }
}

function comment($uid,$compo,$review,$value){
    global $conn;

    $value = $value + 1;
    $sql = " UPDATE composition set no_comment = '$value' where id = $compo ";
    $res = $conn->query($sql);
    if ( $res ) {
        $status = "unseen";
        $sql = " INSERT into reviews(reviewer_user_id,compo_id,review,status) values($uid,$compo,'$review','$status')";
        $res = $conn->query($sql);
        //return $value;
        if (!$res) {
            return "Unknown Error";
        }
    }else{
        $Err = "Unknown Error";
        return $Err;
    }
}

function subscriber($email){
    global $conn;
    global $xml;
    
    $sql = " INSERT into subscribers(email) values('$email')";
    $res = $conn->query($sql);
    //return $value;
    if (!$res) {
        return "Unknown Error";
    }else{
        $message = $xml->message[14]->value;
        $url = "subscribers.php";
        $ans = notify("Administrator",$message,$url);    

        //notify the subscribers of a new added compositions by the admin
        notifySubscribers_newbie($email);
        
        return "";
    }
}

function update_notify_stat($id){
	global $conn;
	$sql = " UPDATE notification set status = 'seen' where status = 'unseen' and id = $id ";
	$res = $conn->query($sql);
}

function update_tags($uid,$value){
    global $conn;

    //splitting tags and assigning real values
    $tag_array = explode(",", $value);
    $tags_real = array("vocal","keyboard","guitar","sax","violin","viola","cello","piano","recorder","flute","trumpet","drumpad");
    $new_tags = array();
    $rtags = "";
    for ($i = 0; $i < count($tag_array); $i++) {
        if ($tag_array[$i] == 1){
            $new_tags[] = $tags_real[$i];
        }
    }
    for ($i = 0; $i < count($new_tags); $i++) {
        if ($i == 0){
            $rtags = $new_tags[$i];
        } else {
            $rtags .= ",".$new_tags[$i];
        }
    }

    $sql = " UPDATE users set tags = '$rtags', itags = '$value' where id = $uid ";
    $res = $conn->query($sql);
    if (!$res) {
        $Err = "Name Shown not updated.";
        return $Err;
    }
}

function article_comment($id,$comment){
    global $conn;

    $sql = " INSERT into article_comment(article_id,comment) values($id,'$comment')";
    $res = $conn->query($sql);
    //return $value;
    if (!$res) {
        return "Unknown Error";
    }
}
/* function save_file($name,$type){
    global $conn;

    //user's id to be gotten from session
    $uid = 1;//echo $uid.",".$name.",".$type;

    $sql = " INSERT into files(user_id,sysname,filename,filetype) values (?,?,?,?) ";
    $res = $conn->prepare($sql);
    $res->bind_param('isss', $uid,$name,$name,$type);
    
    $res->execute();
    if (!$res) {
        $Err = "File not Saved.";
        return $Err;
    }

} */
//close database
$conn->close();

?>