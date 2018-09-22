<?php
//required files
require_once("db_connection.php");
require_once("functions.php");
list($type,$uid) = logged_in();

//get details
$field = !empty($_GET["field"])?trim($_GET["field"]):"";
$value1 = !empty($_GET["value1"])?trim($_GET["value1"]):"";
$value2 = !empty($_GET["value2"])?trim($_GET["value2"]):"";

//sterilize datas
$value1 = sanitizeMySQL($conn, $value1);
$value2 = sanitizeMySQL($conn, $value2);

//variables
//$uid = 12;//user's id to be gotten from session

switch($field){
    case "name":
        $ans = validate_name($value1);
        echo ($ans == "")?"ok":$ans;
    break;
    case "comm":
        $ans = validate_comm($value1);
        echo ($ans == "")?"ok":$ans;
    break;
    case "firstname":
        $ans = validate_fname($value1);
        echo ($ans == "")?"ok":$ans;
    break;
    case "lastname":
        $ans = validate_lname($value1);
        echo ($ans == "")?"ok":$ans;
    break;
    case "email":
        $ans = validate_email($value1);
        echo ($ans == "")?"ok":$ans;
    break;
    case "password":
        $ans = validate_password($value1);
        echo ($ans == "")?"ok":$ans;
    break;
    case "cpassword":
        $ans = validate_cpassword($value1, $value2);
        echo ($ans == "")?"ok":$ans;
    break;
    case "verifycode":
        $ans = verify_code($value1,$value2);
        echo ($ans == "")?"ok":$ans;
    break;
    case "email_edit":
        $ans = validate_email_edit($uid,$value1,$type);
        echo ($ans == "")?"ok":$ans;
    break;
}

//validation functions
function validate_name($value){
    // check fname presence
    if (empty($value)) {
        $Err = "Name is required.";
        return $Err;
    } else {
        // check fname characters
        if (!preg_match("/^[a-zA-Z ]*$/",$value)) {
            $Err = "Only letters and white space allowed.";
            return $Err;
        }   
    }
}

function validate_comm($value){
    // check fname presence
    if (empty($value)) {
        $Err = "Comment/Suggestion is required.";
        return $Err;
    }
}

function validate_fname($value){
    // check fname presence
    if (empty($value)) {
        $Err = "FirstName is required.";
        return $Err;
    } else {
        // check fname characters
        if (!preg_match("/^[a-zA-Z ]*$/",$value)) {
            $Err = "Only letters and white space allowed.";
            return $Err;
        }   
    }
}

function validate_lname($value){
    // check lname presence
    if (empty($value)) {
        $Err = "LastName is required";
        return $Err;
    } else {
        // check lname characters
        if (!preg_match("/^[a-zA-Z ]*$/",$value)) {
            $Err = "Only letters and white space allowed.";
            return $Err;
        }
    }
}

function validate_password($value){
    // check password presence
    if (empty($value)) {
        $Err = "Password is required";
        return $Err;
    } else {
        // check password characters
        //if (preg_match("/^[a-zA-Z0-9]/", $value)){
        if (!preg_match("/[a-z]/", $value) || !preg_match("/[A-Z]/", $value) || !preg_match("/[0-9]/", $value)){
            $Err = "Password require 1 each of a-z, A-Z and 0-9.";
            return $Err;
        }
        // check password length
        if (strlen($value) < 8 ){
            $Err = "Password must be at least 8 characters.";
            return $Err;
        }
    }
}

function validate_cpassword($value,$pass){
    // check password presence
    if (empty($value)) {
        $Err = "Confirm Password is required";
        return $Err;
    } else {
        // check password characters
        if ($value != $pass) {
            $Err = "Confirm Password is invalid.";
            return $Err;
        }
    }   
}

function validate_email($value){
    // check password presence
    if (empty($value) ){
        $Err = "Email is required.";
        return $Err;
    } else {
        // check email characters
        if (!preg_match("/[@]/", $value) || !preg_match("/[.]/", $value)){
            $Err = "Email is invalid.";
            return $Err;
        }
        // check if email exists
        // admin 
        $ans = userExists("admin", $value);
        // users
        $ans = userExists("users", $value);
        if ($ans > 0) {
            $Err = "Email already exists."; return $Err;
        }
    }
}

function validate_email_edit($uid,$value,$type){
    // check password presence
    if (empty($value) ){
        $Err = "Email is required.";
        return $Err;
    } else {
        // check email characters
        if (!preg_match("/[@]/", $value) || !preg_match("/[.]/", $value)){
            $Err = "Email is invalid.";
            return $Err;
        }

        // check if email exists
        // admin 
        $ans = userExists("admin", $value);
        if ($ans > 0) {
            $Err = "Email already exists."; return $Err;
        }else{
            // users
            $ans = userExists("users", $value);
            if ($ans > 0) {
                $ans = userExists_edit($uid,$value,$type);
                if ($ans > 0) {
                    $Err = "Email already exists."; return $Err;
                }
            }
        }
    }
}

function verify_code($value1,$value2){
    global $conn;
    $sql = " SELECT verification_code from users where email = '$value1' and verification_code = '$value2' ";
    $res = $conn->query($sql);
    if (!$res) {
        $Err = "Verification code is incorrect.";
        return $Err;
    }elseif ($res){
        $sql = " UPDATE users set status = 'activated' where email = '$value1' and verification_code = '$value2' ";
        $res = $conn->query($sql);
    }
}

//close database
$conn->close();

?>