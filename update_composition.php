<?php
// required files
require_once("db_connection.php");
require_once("functions.php");
require_once("options.php");
list($type,$uid) = logged_in();

// get datas
$lyric_solfa = !empty($_POST["word_solfa"])?trim($_POST["word_solfa"]):"";
$lyric_chord = !empty($_POST["word_chord"])?trim($_POST["word_chord"]):"";
$solfa = !empty($_POST["solfa"])?trim($_POST["solfa"]):"";
$chord = !empty($_POST["chord"])?trim($_POST["chord"]):"";
$title = !empty($_POST["title"])?trim($_POST["title"]):"";
$description = !empty($_POST["desc"])?trim($_POST["desc"]):"";
$genre = !empty($_POST["compo_genre"])?trim($_POST["compo_genre"]):"";
$nationality = !empty($_POST["compo_nation"])?trim($_POST["compo_nation"]):"";
$image_file_id = !empty($_POST["imagefile"])?trim($_POST["imagefile"]):0;
$video_file_id = !empty($_POST["videofile"])?trim($_POST["videofile"]):0;
$audio_file_id = !empty($_POST["audiofile"])?trim($_POST["audiofile"]):0;
$pdf_file_id = !empty($_POST["pdffile"])?trim($_POST["pdffile"]):0;
$htags = !empty($_POST["htags"])?trim($_POST["htags"]):"";
$id = !empty($_POST["id"])?trim($_POST["id"]):"";

// check for error
// it seems validation wud be done here
if ( (empty($lyric_solfa) && empty($solfa)) && (empty($lyric_chord) && empty($chord)) && (empty($image_file_id) && empty($video_file_id) && empty($audio_file_id) && empty($pdf_file_id)) ) {
    $Err = "No Field filled. Write a Script or Upload a file.";
}elseif(empty($title)) {
    $Err = "Title is required.";
}elseif(empty($description)) {
    $Err = "Description is required.";
}elseif($genre == "Select") {
    $Err = "Genre is required.";
}
if( !empty($Err) ) {
    header("Location: ../compose-edit.php?q=$id&err=$Err&lyric_solfa=$lyric_solfa&lyric_chord=$lyric_chord&solfa=$solfa&chord=$chord&title=$title&desc=$description&genre=$genre&nat=$nationality&imagefile=$image_file_id&audiofile=$audio_file_id&videofile=$video_file_id&pdffile=$pdf_file_id&tags=$htags");
}else{
    // sanitize data
    $lyric_solfa = sanitizeMySQL($conn, $lyric_solfa);
    $lyric_chord = sanitizeMySQL($conn, $lyric_chord);
    $solfa = sanitizeMySQL($conn, $solfa);
    $chord = sanitizeMySQL($conn, $chord);
    $title = sanitizeMySQL($conn, $title);
    $description = sanitizeMySQL($conn, $description);
    $genre = sanitizeMySQL($conn, $genre);
    $nationality = sanitizeMySQL($conn, $nationality);
    $image_file_id = sanitizeMySQL($conn, $image_file_id);
    $audio_file_id = sanitizeMySQL($conn, $audio_file_id);
    $video_file_id = sanitizeMySQL($conn, $video_file_id);
    $pdf_file_id = sanitizeMySQL($conn, $pdf_file_id);

    // other variables
    $status = "checked";//checked,blocked
    //$uid = 1;//use sessions

    //splitting tags and assigning real values
    $tag_array = explode(",", $htags);
    $tags_real = array("advent","ordinary","christmas","pentecost","lent","wedding","easter","competition","dedication","marian","latin","other");
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

    // add to database
    $sql = " UPDATE composition set lyric_solfa = ?,lyric_chord = ?,solfa = ?,chord = ?,image_file_id = ?,audio_file_id = ?,video_file_id = ?,pdf_file_id = ?,title = ?,description = ?,genre = ?,tags = ?,itags = ?,nationality = ?,status = ? where id = $id ";
    $res = $conn->prepare($sql);
    $res->bind_param('ssssiiiisssssss',$lyric_solfa,$lyric_chord,$solfa,$chord,$image_file_id,$audio_file_id,$video_file_id,$pdf_file_id,$title,$description,$genre,$rtags,$htags,$nationality,$status);
    $res->execute();
    if ( !empty($res) ){
        $ok = "Composition Updated.";

        //notify
        //admin notify compo updated
        $message = $xml->message[8]->value;
        $url = "manage-compositions.php";
        $ans = notify("Administrator",$message,$url);

        //compo liked updated
        $ans = compo_like_update_notify($id);

        //compo comment updated
        $ans = compo_comment_update_notify($id);

        header("Location: ../compose-edit.php?q=$id&ok=$ok");
    } else {
        $Err = "Unknown Error";
        header("Location: ../compose-edit.php?q=$id&err=$Err&lyric_solfa=$lyric_solfa&lyric_chord=$lyric_chord&solfa=$solfa&chord=$chord&title=$title&desc=$description&genre=$genre&nat=$nationality&imagefile=$image_file_id&audiofile=$audio_file_id&videofile=$video_file_id&pdffile=$pdf_file_id&tags=$htags");
    }
}

// close database
$conn->close();
?>