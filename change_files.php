<?php
//required file
require_once("db_connection.php");
require_once("functions.php");
list($type,$uid) = logged_in();

if( isset($_POST["edit"]) && $_POST["edit"] == "Submit" ){
    $type = $_POST["type"];
    $id = $_POST["id"];
    $name = $_POST["name"];
    $file_tmp_name = $_FILES["file_edit"]["tmp_name"];
    $filesize = $_FILES["file_edit"]["size"];
    switch($type){
        case "image":
            $target_dir = "../uploads/images/";
            $file = $target_dir . $name;
            $target_file = $target_dir . basename($_FILES["file_edit"]["name"]);
            $filetype = pathinfo($target_file,PATHINFO_EXTENSION);
            if($filetype == "jpg" || $filetype == "png" || $filetype == "jpeg" || $filetype == "gif" ) {
                if ($filesize < 3145728) {
                    if (file_exists($file)) {
                        unlink($file);
                        $file = "MS_IMG_" . rand(10000,99999) .".". $filetype;
                        $target_file = $target_dir . $file;
                        if (move_uploaded_file($file_tmp_name, $target_file)) {
                            $ans = update_file($file,$id,"image");
                            if ($ans == ""){
                                header("Location: ../file.php");
                            }else{
                                header("Location: ../file.php?err=$ans");
                            }
                        } else {
                            $err = "Error uploading file.";
                            header("Location: ../file.php?err=$err");
                        }
                    }else{
                        $err = "File to Edit don't Exist.";
                        header("Location: ../file.php?err=$err");
                    }
                }else{
                    $err = "File too large; Max 3mb";
                    header("Location: ../file.php?err=$err");
                }
            }else{
                $err = "Only JPG, JPEG, PNG  GIF files are allowed.";
                header("Location: ../file.php?err=$err");
            }
        break;
        case "audio":
            $target_dir = "../uploads/audios/";
            $file = $target_dir . $name;
            $target_file = $target_dir . basename($_FILES["file_edit"]["name"]);
            $filetype = pathinfo($target_file,PATHINFO_EXTENSION);
            if($filetype == "mp3" || $filetype == "flac" || $filetype == "ogg"){
                if ($filesize < 5242880) {
                    if (file_exists($file)) {
                        unlink($file);
                        $file = "MS_AUD_" . rand(10000,99999) .".". $filetype;
                        $target_file = $target_dir.$file;
                        if (move_uploaded_file($file_tmp_name, $target_file)) {
                            $ans = update_file($file,$id,"audio");
                            if ($ans == ""){
                                header("Location: ../file.php");
                            }else{
                                header("Location: ../file.php?err=$ans");
                            }
                        } else {
                            $err = "Error uploading file.";
                            header("Location: ../file.php?err=$err");
                        }
                    }else{
                        $err = "File to Edit don't Exist.";
                        header("Location: ../file.php?err=$err");
                    }
                }else{
                    $err = "File too large; Max 5mb";
                    header("Location: ../file.php?err=$err");
                }
            }else{
                $err = "Only MP3, OGG and FLAC files are allowed.";
                header("Location: ../file.php?err=$err");
            }
        break;
        case "video":
            $target_dir = "../uploads/videos/";
            $file = $target_dir . $name;
            $target_file = $target_dir . basename($_FILES["file_edit"]["name"]);
            $filetype = pathinfo($target_file,PATHINFO_EXTENSION);
            if($filetype == "mp4" || $filetype == "3gp" || $filetype == "avi" ) {
                if ($filesize < 10485760) { 
                    if (file_exists($file)) {
                        unlink($file);
                        $file = "MS_VID_" . rand(10000,99999) .".". $filetype;
                        $target_file = $target_dir . $file;
                        if (move_uploaded_file($file_tmp_name, $target_file)) {
                            $ans = update_file($file,$id,"video");
                            if ($ans == ""){
                                header("Location: ../file.php");
                            }else{
                                header("Location: ../file.php?err=$ans");
                            }
                        } else {
                            $err = "Error uploading file.";
                            header("Location: ../file.php?err=$err");
                        }
                    }else{
                        $err = "File to Edit don't Exist.";
                        header("Location: ../file.php?err=$err");
                    }
                }else{
                    $err = "File too large; Max 10mb";
                    header("Location: ../file.php?err=$err");
                }
            }else{
                $err = "Only MP4, AVI, 3PG files are allowed.";
                header("Location: ../file.php?err=$err");
            }
        break;
        case "pdf":
            $target_dir = "../uploads/pdfs/";
            $file = $target_dir . $name;
            $target_file = $target_dir . basename($_FILES["file_edit"]["name"]);
            $filetype = pathinfo($target_file,PATHINFO_EXTENSION);
            if($filetype == "pdf" ) {
                if ($filesize < 3145728) { 
                    if (file_exists($file)) {
                        unlink($file);
                        $file = "MS_PDF_" . rand(10000,99999) .".". $filetype;
                        $target_file = $target_dir . $file;
                        if (move_uploaded_file($file_tmp_name, $target_file)) {
                            $ans = update_file($file,$id,"pdf");
                            if ($ans == ""){
                                header("Location: ../file.php");
                            }else{
                                header("Location: ../file.php?err=$ans");
                            }
                        } else {
                            $err = "Error uploading file.";
                            header("Location: ../file.php?err=$err");
                        }
                    }else{
                        $err = "File to Edit don't Exist.";
                        header("Location: ../file.php?err=$err");
                    }
                }else{
                    $err = "File too large; Max 3mb";
                    header("Location: ../file.php?err=$err");
                }
            }else{
                $err = "Only PDF files are allowed.";
                header("Location: ../file.php?err=$err");
            }
        break;
    }
}

function update_file($name,$id,$type){
    global $conn;

    //user's id to be gotten from session'
    //$uid = 1;

    $sql = " UPDATE files set filename = '$name', sysname = '$name', filetype = '$type' where id = $id ";
    $res = $conn->query($sql);
    if ($conn->query($sql) !== TRUE) {
        $err = "File not Updated.";
        return $err;
    }
}

?>