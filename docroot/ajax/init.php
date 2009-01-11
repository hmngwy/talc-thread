<?php 
session_start();
$DB = new PDO('sqlite:../../model/talk.sqlite');

$object = new stdClass();
$object->site = new stdClass();

$statement = $DB->query('SELECT * FROM `thread` WHERE `thread`.`ID`=1');
$thread = $statement->fetch(PDO::FETCH_ASSOC);

$_SESSION['site-status'] = $object->site->status = $thread['site-status'];
$_SESSION['site-notice'] = $object->site->notice = $thread['site-notice'];


if(isset($_SESSION['u']))
	$object->username = $_SESSION['u'];

$statement = $DB->query('SELECT `topic`.`ID`, `topic`.`text` FROM `topic` ORDER BY `ID` DESC LIMIT 1');
$topic = $statement->fetch(PDO::FETCH_ASSOC);

$_SESSION['topic_ID'] = $topic['ID'];
$object->topic = $topic['text'];

$statement = $DB->query('SELECT `message`.`ID`, `message`.`name`, `message`.`text` FROM `message` ORDER BY `created_dt` DESC LIMIT 10');
$comments = array_reverse($statement->fetchAll(PDO::FETCH_ASSOC));

$_SESSION['comment_ID'] = 0;

foreach($comments as $row)
{
	$comment = new stdClass();
	$comment->name = $row['name'];
	$comment->text = $row['text'];
	$object->comments[] = $comment;
	
	$_SESSION['comment_ID'] = $row['ID'];
}


header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', FALSE); 
header('Pragma: no-cache'); 
header('Content-type: application/json; charset=utf-8');

echo json_encode($object);
?>