<?php
session_start();
$DB = new PDO('sqlite:../../model/talk.sqlite');

$interval = 2;
$loops = 5;

$count = 0;

$object = new stdClass();
$object->site = new stdClass();

while($count<$loops)
{
	$count++;
	$statement = $DB->query('SELECT * FROM `thread` WHERE `thread`.`ID`=1');
	$thread = $statement->fetch(PDO::FETCH_ASSOC);
	
	
	if($_SESSION['site-status'] != $thread['site-status'] ||
		$_SESSION['site-notice'] != $thread['site-notice'])
	{
		$object->site->status = $thread['site-status'];
		$object->site->notice = $thread['site-notice'];
		$_SESSION['site-status'] = $thread['site-status'];
		$_SESSION['site-notice'] = $thread['site-notice'];
		$changed = true;
	}
	
	
	$statement = $DB->query('SELECT `topic`.`ID`, `topic`.`text` FROM `topic` ORDER BY `ID` DESC LIMIT 1');
	$topic = $statement->fetch(PDO::FETCH_ASSOC);
	
	if($topic['ID'] != $_SESSION['topic_ID'])
	{
		$_SESSION['topic_ID'] = $topic['ID'];
		$object->topic = $topic['text'];
		$changed = true;
	}
	
	$statement = $DB->query('SELECT `message`.`ID`, `message`.`name`, `message`.`text` FROM `message` WHERE ID>\''.$_SESSION['comment_ID'].'\' ORDER BY `created_dt` DESC');
	$comments = array_reverse($statement->fetchAll(PDO::FETCH_ASSOC));
	
	foreach($comments as $row)
	{
		$comment = new stdClass();
		$comment->name = $row['name'];
		$comment->text = $row['text'];
		$object->comments[] = $comment;
		
		$_SESSION['comment_ID'] = $row['ID'];
		$changed = true;
	}

	if($changed==true) break;
	else
	{
		sleep($interval);
	}
}
 
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', FALSE); 
header('Pragma: no-cache'); 
header('Content-type: application/json');

echo json_encode($object);
?>