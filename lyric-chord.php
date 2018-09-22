<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="../styles/w3.css">
</head>
<body class="w3-white" style="font-family:;">
<?php
$words = $_GET["word_chord"];
$chord = $_GET["chord"];
$words = explode("-", $words );
$chord = explode(",", $chord );

echo "
<input type=\"hidden\" id=\"word\" value=\" ".$_GET["word_chord"]." \" >
<input type=\"hidden\" id=\"chord\" value=\" ".$_GET["chord"]." \" >";
?>
<canvas id="canvas" class="w3-light-grey" width="500px" height="10000px" style="width:100%;height:100%;"></canvas>

<script>
    var vspace = 20;
    var hspace = 10;
    var xcur = 10;
    var ycur = 20;

    function text(wrd,slf){
        //document.getElementById("canvas").height = ycur + 80;
        var canvas = document.getElementById("canvas");
        var ctx = canvas.getContext("2d");
        ctx.font = "20px times new roman";
        ctx.fillStyle = "black";

        //for empty strings
        if( wrd.length == 0 ){
            wrd = "    "; 
        }

        var wid = ctx.measureText(wrd).width;
        var hgt = ctx.measureText(wrd).height;
        var wids = ctx.measureText(slf).width;

        //entering a newline
        if( (xcur + wid + hspace) > canvas.width){
            ycur = ycur + 40 + vspace;
            xcur = 10;
        }

        ctx.fillText(wrd,xcur,ycur);//writing out word

        //adjusting the chord to be exactly at the middle of the word
        var wmid = wid/2 + xcur;
        var smid = wids/2 + xcur;
        var middif = smid - xcur;
        if(wmid > smid){
            xcur = wmid - middif;
        }
        if(wmid < smid){
            xcur = smid - middif;
        }

        //changing the color and size for easier recognition
        ctx.fillStyle = "blue";
        ctx.font = "15px times new roman";
        ctx.fillText(slf, xcur, ycur + vspace /* + vspace/2 */);//writing out chord
        xcur = xcur + wid + hspace //updating the value of the x-coordinate
    }
</script>

<script>
    var w = document.getElementById("word").value;
    var s = document.getElementById("chord").value;
    var word = w.split("-");
    var chord = s.split(",");

    for(var a = 0; a < word.length; a++){
        text(word[a],chord[a]);
    }
</script>

</body>
</html>