<?php
	error_reporting(E_ERROR | E_WARNING | E_PARSE);

	require_once("db_connection.php");
	require_once("functions.php");
	require_once("options.php");
	
	/* $mail = isset($_GET['email'])?trim($_GET['email']):"";
	if(isset($mail)){
		list($email,$seen,$vcode) = get_vcode($mail);
		$ans = activation($seen,$email,$vcode);
		if($ans == "ok"){
			return "ok";
		}
	} */

	function signup_mail($firstname,$lastname,$email){
		$from = $xml->socials[2]->value;

		$subject = 'Welcome To MyScriptz, '.strtoupper($firstname).' '.strtoupper($lastname).'';

		$message = "<h1>Thank you for joining MyScriptz.</h1>";
		$message .= "<h3>At MyScriptz, you don't just love Music...</h3>";
		$message .= "Below are some the Benefits of joining MyScriptz";
		$message .= "<ul>
			<li>Easy access to scripts.</li>
			<li>Promote your composition.</li>
			<li>Make your Compositions known to the wider public.</li>
			<li>You also have access to educative post on music.</li>
			<li>Extra benefits + enjoyment of musical pieces.</li>
		</ul>";

		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		$headers .= 'From: '.$from."\r\n";
		mail($email, $subject, $message, $headers);
	}

	function activation($seen,$email,$vcode){
		$from = $xml->socials[2]->value;

		$subject = 'Activate Your Account '.ucfirst($seen).'';

		$message = "<h3>".ucfirst($seen).", Your Activation Code is</h3>";
		$message .= "<h3>".$vcode."</h3>";

		$headers = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		$headers .= 'From: '.$from."\r\n";
		if(mail($email, $subject, $message, $headers)){
			return "ok";
		}
	}

	function notifySubscribers_compo($email,$title,$link,$description){
		$from = $xml->socials[2]->value;

		$subject = "New Composition from MyScriptz";

		$message = "<h1>". $title ."</h1>";
		$message .= "<p>". $description ."</p>";
		$message .= "<h3>A new Composition have been Added, Follow the link below to view</h3>";
		$message .= "<h3>".$link."</h3>";

		$headers = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		$headers .= 'From: '.$from."\r\n";
		mail($email, $subject, $message, $headers);
	}

	function notifySubscribers_blog($email,$title,$link,$description){
		$from = $xml->socials[2]->value;

		$subject = "New Blog Post from MyScriptz";

		$message = "<h1>". $title ."</h1>";
		$message = "<p>". $description ."</p>";
		$message .= "<h3>A new Blog Post, Follow the link below to view</h3>";
		$message .= "<h3>".$link."</h3>";

		$headers = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		$headers .= 'From: '.$from."\r\n";
		mail($email, $subject, $message, $headers);
	}

	function notifySubscribers_newbie($email){
		$from = $xml->socials[2]->value;

		$subject = "Thank you for Subscribing to MyScriptz";

		$message = "<h1>Thank you for Subscribing to MyScriptz</h1>";
		$message = "<p>You would be receiving Messages of our recent Blog Post and Compositions.</p>";

		$headers = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		$headers .= 'From: '.$from."\r\n";
		mail($email, $subject, $message, $headers);
	}
?>