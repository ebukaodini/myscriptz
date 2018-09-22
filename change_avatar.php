<?php
//required files
require_once("db_connection.php");
require_once("functions.php");
list($type,$uid) = logged_in();

if( isset($_POST["upload"]) && $_POST["upload"] == "Upload" ) {
    // get data
    $avatar = !empty($_POST["aavatar"])?$_POST["aavatar"]:null;

    // check if file was uploaded
    if( !isset($_FILES["avatar"]["tmp_name"]) || empty($_FILES["avatar"]["tmp_name"]) ){
        $err = "No file selected.";
        header("Location: ../profile.php?err=$err");
        exit();
    }else{

        //file detail
        $target_dir = "../uploads/avatars/";
        $target_file = $target_dir . basename($_FILES["avatar"]["name"]);
        $file_tmp_name = $_FILES["avatar"]["tmp_name"];
        $filesize = $_FILES["avatar"]["size"];
        $filetype = pathinfo($target_file,PATHINFO_EXTENSION);
        
        // target detail
        if (isset($avatar)){
            $exists = true;
            $avatar_name = $avatar;
        }else{
            $exists = false;
            $avatar_name = "MS_" . rand(1000000000,9999999999) .".". $filetype;
        }
        $target_file = $target_dir . $avatar_name;

        //validations

        // Check file size
        if ($filesize > 1048576) { 
            $err = "File too large; Max 1mb";
            header("Location: ../profile.php?err=$err");
            exit();
        }

        // Allow certain file formats
        if($filetype != "jpg" && $filetype != "png" && $filetype != "jpeg" && $filetype != "gif" ) {
            $err = "Only JPG, JPEG, PNG  GIF files are allowed.";
            header("Location: ../profile.php?err=$err");
            exit();
        }
        // if everything is ok, try to upload file
        else {
            // Check if file already exists
            if (file_exists($target_file)) {
                unlink($target_file);
            }

            if (move_uploaded_file($_FILES["avatar"]["tmp_name"], $target_file)) {
                //resize image
                include_once("image_resize.php");
                $old = $target_file;
                $new = $target_file;
                $wid = 512;
                $hgt = 512;
                $ext = $filetype;
                img_resize($old, $new, $wid, $hgt, $ext);

                // if avatar already exists in the avatar db update else insert and update user
                if($exists == true){
                    $sql = "UPDATE avatar set sys_name = '$avatar' where sys_name = '$avatar_name' ";
                    $res = $conn->query($sql);
                    if ($conn->query($sql) !== TRUE) {
                        $err = "Unknown Error";
                        header("Location: ../profile.php?err=$err");
                    }
                }elseif($exists == false){
                    $sql = "INSERT into avatar(sys_name) values('$avatar_name')";
                    $res = $conn->query($sql);
                    if ($conn->query($sql) !== TRUE) {
                        $err = "Unknown Error";
                        header("Location: ../profile.php?err=$err");
                    }

                    $avatar_id = $conn->insert_id;
                    $sql = "UPDATE users set avatar_id = '$avatar_id' where id = '$uid' ";
                    $res = $conn->query($sql);
                    if ($conn->query($sql) !== TRUE) {
                        $err = "Unknown Error";
                        header("Location: ../profile.php?err=$err");
                    }
                }
                header("Location: ../profile.php");
                exit();
            } else {
                $err = "Error uploading file.";
                header("Location: ../profile.php?err=$err");
                exit();
            }
        }    
    }
} 

/* if ($_POST["submit"] == "Upload Image") {
    //get available datas from the submitted form
    $namefile = isset($_POST["namefile"])?$_POST["namefile"]: null;
    $passwordfile = isset($_POST["passwordfile"])?$_POST["passwordfile"]:null;
    $pinfile = isset($_POST["pinfile"])?$_POST["pinfile"]:null;
    $emailfile = isset($_POST["emailfile"])?$_POST["emailfile"]:null;
    $patternfile = isset($_POST["patternfile"])?$_POST["patternfile"]:null;
    $voicepassfile = isset($_POST["voicepassfile"])?$_POST["voicepassfile"]:null;
    $picfig = isset($_POST["picfig"])?$_POST["picfig"]:null;

    $target_dir = "../public/images/picpass/";
    $target_file = $target_dir . basename($_FILES["picpass"]["name"]);
    $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
    if ($imageFileType == "jpg"){
        $type = "jpg";
    } elseif ( $imageFileType == "png" ) {
        $type = "png";
    } elseif ( $imageFileType == "jpeg") {
        $type = "jpeg";
    } elseif ( $imageFileType == "gif") {
        $type = "gif";
    }
    else{
        exit;
    }
    $imgname = "vaultr" . rand(11111,99999) . "." . $type;
    $target_file = $target_dir . $imgname;
    $uploadOk = 1;

    //list($width,$height) = "";//getimagesize($file_tmp_name);

    // image dimension
    if($width < 10 || $height < 10){
        echo "file has no dimension";
        $uploadOk = 0;
    }
    //checking if the file is an image
    $check = getimagesize($_FILES["picpass"]["tmp_name"]);
    if ($check !== false) {
        //echo "File is an image - " . $check["mime"] . ".";
        $uploadOk = 1;
    } else {
    //echo "File is not an image.";
        $uploadOk = 0;
    }
    //checking file size
    if (file_exists($target_file)) {
        //echo "Sorry, file already exists.";
        $uploadOk = 0;
    }
     // Check file size
    if ($_FILES["picpass"]["size"] > 500000) {
        //echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }
    //if there is no error
    if ($uploadOk == 0) {
    //echo "Sorry, your file was not uploaded.";
    } else {
        $filename = basename($_FILES["picpass"]["name"]);
        if (move_uploaded_file($_FILES["picpass"]["tmp_name"], $target_file)) {
            //echo $namefile." this is  me here";
            //send avatar_name to the page to be added to the database
            
        } else {
            //echo "Sorry, there was an error uploading your file.";
        }
    }
}

//move to database
$sql = " INSERT into users (avatar) values($filename) where email = '$user' ";
$res = $conn->query($sql);
if( $res ){}
redirector("");
 */
?>