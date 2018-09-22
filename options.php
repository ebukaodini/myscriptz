<?php
//required files
require_once("db_connection.php");

//action category page socials message

//query
$res = $conn->query(" SELECT * from options where opt_group = 'action' order by id asc");
if ( $res->num_rows ){
    $rows = $res->num_rows;
    $action = array();
    // looping through database
    for($i = 0 ; $i < $rows ; $i++){
        $res->data_seek($i);
        $row = $res->fetch_array(MYSQLI_ASSOC);
        $action[$i][0] = $row['name'];
        $action[$i][1] = $row['value'];
    }
}

//query
$res = $conn->query(" SELECT * from options where opt_group = 'category' order by id asc");
if ( $res->num_rows ){
    $rows = $res->num_rows;
    $category = array();
    // looping through database
    for($i = 0 ; $i < $rows ; $i++){
        $res->data_seek($i);
        $row = $res->fetch_array(MYSQLI_ASSOC);
        $category[$i][0] = $row['name'];
        $category[$i][1] = $row['value'];
    }
}

//query
$res = $conn->query(" SELECT * from options where opt_group = 'page' order by id asc");
if ( $res->num_rows ){
    $rows = $res->num_rows;
    $page = array();
    // looping through database
    for($i = 0 ; $i < $rows ; $i++){
        $res->data_seek($i);
        $row = $res->fetch_array(MYSQLI_ASSOC);
        $page[$i][0] = $row['name'];
        $page[$i][1] = $row['value'];
    }
}

//query
$res = $conn->query(" SELECT * from options where opt_group = 'socials' order by id asc");
if ( $res->num_rows ){
    $rows = $res->num_rows;
    $socials = array();
    // looping through database
    for($i = 0 ; $i < $rows ; $i++){
        $res->data_seek($i);
        $row = $res->fetch_array(MYSQLI_ASSOC);
        $socials[$i][0] = $row['name'];
        $socials[$i][1] = $row['value'];
    }
}

//query
$res = $conn->query(" SELECT * from options where opt_group = 'message' order by id asc");
if ( $res->num_rows ){
    $rows = $res->num_rows;
    $message = array();
    // looping through database
    for($i = 0 ; $i < $rows ; $i++){
        $res->data_seek($i);
        $row = $res->fetch_array(MYSQLI_ASSOC);
        $message[$i][0] = $row['name'];
        $message[$i][1] = $row['value'];
    }
}
?>
