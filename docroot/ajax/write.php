<?php 
include 'json_encode.fx.php';
session_start();
$DB = new PDO('sqlite:../../model/talk.sqlite');

$count = true;

if($_POST['comment'] == '#logout')
{#
	setcookie("PHPSESSID", "", time() - 3600, "/");
	session_destroy();
	
	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Cache-Control: no-cache, no-store, must-revalidate');
	header('Cache-Control: post-check=0, pre-check=0', FALSE); 
	header('Pragma: no-cache'); 
	header('Content-type: application/json');
	
	echo '{"status":"success", "comment":"'.$_POST['comment'].'"}';
	die();
}

if($_SESSION['site-status'] == 'ready')
{
	$_SESSION['u'] = $_POST['username'];
	
	$_POST['username'] = htmlentities($_POST['username'], ENT_QUOTES);
	$_POST['comment'] = htmlentities($_POST['comment'], ENT_QUOTES);

	try
	{	
		if (!empty($_SERVER['HTTP_CLIENT_IP'])){
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		}
		elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		else{
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		
		$DB->exec('BEGIN TRANSACTION');
		$DB->exec('INSERT INTO `identity` VALUES(NULL, \''.$ip.'\', \''.( (isset($_SERVER['REMOTE_HOST'])) ? $_SERVER['REMOTE_HOST'] : '' ).'\', \''.$_SERVER['REMOTE_PORT'].'\', \''.$_SERVER['REQUEST_TIME'].'\', \''.$_SERVER['HTTP_USER_AGENT'].'\', \''.$_SERVER['HTTP_REFERER'].'\')');
		$count = $DB->exec('INSERT INTO `message` VALUES(NULL, \''.$_POST['username'].'\', \''.$_POST['comment'].'\', '.$DB->lastInsertId().', datetime(\'now\'), NULL)');
		
		if($count===false)
			throw new Exception('Bad Input', 1);
						
		$DB->exec('COMMIT TRANSACTION');
	}
	catch(Exception $e)
	{
		$DB->exec('ROLLBACK TRANSACTION');
	}
}


header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', FALSE); 
header('Pragma: no-cache'); 
header('Content-type: application/json; charset=utf-8');

echo '{"status":"'.(($count===false) ? 'fail' : 'success').'", "comment":"'.$_POST['comment'].'"}';
?>