<?php
//required file
require_once("db_connection.php");
require_once("functions.php");
list($type,$uid,$mode_no) = logged_in();

if( isset($_POST["image"]) && $_POST["image"] == "Upload" ) {
    // check if file was uploaded
    if( !isset($_FILES["file_image"]["tmp_name"]) || empty($_FILES["file_image"]["tmp_name"]) ){
        $err = "No file selected.";
        header("Location: ../file.php?err=$err");
        exit();
    }else{

        //file detail
        $target_dir = "../uploads/images/";
        $target_file = $target_dir . basename($_FILES["file_image"]["name"]);
        $file_tmp_name = $_FILES["file_image"]["tmp_name"];
        $filesize = $_FILES["file_image"]["size"];
        $filetype = pathinfo($target_file,PATHINFO_EXTENSION);
        
        // target detail
        if (isset($file_image)){
            $file = $file_image;
        }else{
            $file = "MS_IMG_" . rand(10000,99999) .".". $filetype;
        }
        $target_file = $target_dir . $file;

        //validations

        // Check file size
        if ($filesize > 3145728) { 
            $err = "File too large; Max 3mb";
            header("Location: ../file.php?err=$err");
            exit();
        }

        // Allow certain file formats
        if($filetype != "jpg" && $filetype != "png" && $filetype != "jpeg" && $filetype != "gif" ) {
            $err = "Only JPG, JPEG, PNG  GIF files are allowed.";
            header("Location: ../file.php?err=$err");
            exit();
        }

        //check mode
        if($mode_no == 0) {
            $err = "Trial Period Over.";
            header("Location: ../file.php?err=$err");
            exit();
        }

        // if everything is ok, try to upload file
        else {
            // Check if file already exists
           /*  if (file_exists($target_file)) {
                unlink($target_file);
            } */

            if (move_uploaded_file($file_tmp_name, $target_file)) {
                //resize image
                /* include_once("image_resize.php");
                $old = $target_file;
                $new = $target_file;
                $wid = 320;
                $hgt = 157;
                $ext = $filetype;
                img_resize($old, $new, $wid, $hgt, $ext); */
                
                reduce($uid,$mode_no);

                $ans = save_file($uid,$file,"image");
                if ($ans != ""){
                    header("Location: ../file.php?err=$ans");
                }
                header("Location: ../file.php");
                exit();
            } else {
                $err = "Error uploading file.";
                header("Location: ../file.php?err=$err");
                exit();
            }
        }    
    }
} elseif( isset($_POST["audio"]) && $_POST["audio"] == "Upload" ) {
    // check if file was uploaded
    if( isset($_FILES) && !empty($_FILES["file_audio"]["name"] ) ){
    
        //file detail
        $target_dir = "../uploads/audios/";
        $target_file = $target_dir . basename($_FILES["file_audio"]["name"]);
        $file_tmp_name = $_FILES["file_audio"]["tmp_name"];
        $filesize = $_FILES['file_audio']['size'];
        $filetype = pathinfo($target_file,PATHINFO_EXTENSION);
        //echo $_FILES['file_audio']['size'];
        // target detail
        if (isset($file_audio)){
            $file = $file_audio;
        }else{
            $file = "MS_AUD_" . rand(10000,99999) .".". $filetype;
        }
        $target_file = $target_dir . $file;

        //validations

        // Check file size
        if ($filesize > 5242880) {
            $err = "File too large; Max 5mb";
            header("Location: ../file.php?err=$err");
            exit();
        }

        // Allow certain file formats
        if($filetype != "mp3" && $filetype != "flac" && $filetype != "ogg"){
            $err = "Only MP3, OGG and FLAC files are allowed.";
            header("Location: ../file.php?err=$err");
            exit();
        }

        if($mode_no == 0) {
            $err = "Trial Period Over.";
            header("Location: ../file.php?err=$err");
            exit();
        }

        // if everything is ok, try to upload file
        else {
            // Check if file already exists
            /* if (file_exists($target_file)) {
                unlink($target_file);
            } */

            if (move_uploaded_file($file_tmp_name, $target_file)) {

                reduce($uid,$mode_no);

                $ans = save_file($uid,$file,"audio");
                if ($ans != ""){
                    header("Location: ../file.php?err=$ans");
                }else{
                    header("Location: ../file.php");
                }
                exit();
            } else {
                $err = "Error uploading file.";
                header("Location: ../file.php?err=$err");
                exit();
            }
        }     
    }else{
        $err = "No file selected.";
        header("Location: ../file.php?err=$err");
        exit();
    }
} elseif( isset($_POST["video"]) && $_POST["video"] == "Upload" ) {
    // check if file was uploaded
    if( !isset($_FILES["file_video"]["tmp_name"]) || empty($_FILES["file_video"]["tmp_name"]) ){
        $err = "No file selected.";
        header("Location: ../file.php?err=$err");
        exit();
    }else{

        //file detail
        $target_dir = "../uploads/videos/";
        $target_file = $target_dir . basename($_FILES["file_video"]["name"]);
        $file_tmp_name = $_FILES["file_video"]["tmp_name"];
        $filesize = $_FILES["file_video"]["size"];
        $filetype = pathinfo($target_file,PATHINFO_EXTENSION);
        
        // target detail
        if (isset($file_video)){
            $file = $file_video;
        }else{
            $file = "MS_VID_" . rand(10000,99999) .".". $filetype;
        }
        $target_file = $target_dir . $file;

        //validations

        // Check file size
        if ($filesize > 10485760) { 
            $err = "File too large; Max 10mb";
            header("Location: ../file.php?err=$err");
            exit();
        }

        // Allow certain file formats
        if($filetype != "mp4" && $filetype != "3gp" && $filetype != "avi" ) {
            $err = "Only MP4, AVI, 3PG files are allowed.";
            header("Location: ../file.php?err=$err");
            exit();
        }

        if($mode_no == 0) {
            $err = "Trial Period Over.";
            header("Location: ../file.php?err=$err");
            exit();
        }

        // if everything is ok, try to upload file
        else {
            // Check if file already exists
            /* if (file_exists($target_file)) {
                unlink($target_file);
            } */

            if (move_uploaded_file($file_tmp_name, $target_file)) {

                reduce($uid,$mode_no);

                $ans = save_file($uid,$file,"video");
                if ($ans != ""){
                    header("Location: ../file.php?err=$ans");
                }
                header("Location: ../file.php");
                exit();
            } else {
                $err = "Error uploading file.";
                header("Location: ../file.php?err=$err");
                exit();
            }
        }    
    }
}elseif( isset($_POST["pdf"]) && $_POST["pdf"] == "Upload" ) {
    
    // check if file was uploaded
    if( !isset($_FILES["file_pdf"]["tmp_name"]) || empty($_FILES["file_pdf"]["tmp_name"]) ){
        $err = "No file selected.";
        header("Location: ../file.php?err=$err");
        exit();
    }else{

        //file detail
        $target_dir = "../uploads/pdfs/";
        $target_file = $target_dir . basename($_FILES["file_pdf"]["name"]);
        $file_tmp_name = $_FILES["file_pdf"]["tmp_name"];
        $filesize = $_FILES["file_pdf"]["size"];
        $filetype = pathinfo($target_file,PATHINFO_EXTENSION);
        
        // target detail
        if (isset($file_pdf)){
            $file = $file_pdf;
        }else{
            $file = "MS_PDF_" . rand(10000,99999) .".". $filetype;
        }
        $target_file = $target_dir . $file;

        //validations

        // Check file size
        if ($filesize > 3145728) { 
            $err = "File too large; Max 3mb";
            header("Location: ../file.php?err=$err");
            exit();
        }

        // Allow certain file formats
        if($filetype != "pdf" ) {
            $err = "Only PDF files are allowed.";
            header("Location: ../file.php?err=$err");
            exit();
        }

        if($mode_no == 0) {
            $err = "Trial Period Over.";
            header("Location: ../file.php?err=$err");
            exit();
        }

        // if everything is ok, try to upload file
        else {
            // Check if file already exists
            /* if (file_exists($target_file)) {
                unlink($target_file);
            } */

            if (move_uploaded_file($file_tmp_name, $target_file)) {

                reduce($uid,$mode_no);

                $ans = save_file($uid,$file,"pdf");
                if ($ans != ""){
                    header("Location: ../file.php?err=$ans");
                }
                header("Location: ../file.php");
                exit();
            } else {
                $err = "Error uploading file.";
                header("Location: ../file.php?err=$err");
                exit();
            }
        }    
    }
} 

function save_file($uid,$name,$type){
    global $conn;

    //user's id to be gotten from session
    //$uid = 1;

    $sql = " INSERT into files(user_id,sysname,filename,filetype) values (?,?,?,?) ";
    $res = $conn->prepare($sql);
    $res->bind_param('isss', $uid,$name,$name,$type);
    
    $res->execute();
    if (!$res) {
        $Err = "File not Saved.";
        return $Err;
    }

}

function reduce($id,$no){
    global $conn;
    $no -= 1;
    $sql = " UPDATE users set mode_no = $no where id = $id ";
    $res = $conn->query($sql);
}

/* 
if( isset($_POST["submit"])){
    $path = "../uploads/videos/";echo "enter";
    $valid_formats1 = array("mp4","3gp","avi");
    if(isset($_POST) and $_SERVER['REQUEST_METHOD'] == "POST"){
        $file1 = $_FILES["file_video"]["name"];
        $size = $_FILES["file_video"]["size"];
        if(strlen($file1)){
            list($txt,$ext) = explode(".",$file1);
            if(in_array($ext,$valid_formats1)){
                $imgname = $txt.".".$ext;
                $tmp = $_FILES["file_video"]["tmp_name"];
                if (move_uploaded_file($tmp, $path.$imgname)) {
                    echo "success";
                }else{
                    echo "fail";
                }
            }
        }
    }
} */

?>