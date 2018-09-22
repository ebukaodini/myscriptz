<?php
// required files
require_once("db_connection.php");

//variables
$fileid = isset($_GET["fileid"])?$_GET["fileid"]: null;

// file query
$sql = " SELECT sysname from files where id = $fileid and filetype = 'pdf' ";
$res = $conn->query($sql);
$path = "../uploads/pdfs/";
if ( $res->num_rows ){
    $row = $res->fetch_array(MYSQLI_ASSOC);
    $sysname = $row['sysname'];
    //header("Content-type:application/pdf");
    //header("Content-Disposition:inline;sysname =". $sysname ." ");
    //header("Content-Transfer-Encoding:binary");
    //header("Content-Length:". filesize($sysname) ."");
    //header("Accept-Ranges:bytes");
    //@readfile($path.$sysname);
    header("Location: ".$path . $sysname ."");
}else{
    // not found in the database
    echo "No Pdf File.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="../styles/w3.css">
</head>
<body>
</body>
</html>