<?php
$DB = new PDO('sqlite:../model/talk.sqlite');

$statement = $DB->query('SELECT * FROM `thread` WHERE ID=1 LIMIT 1');
$thread = $statement->fetch(PDO::FETCH_ASSOC);

$statement = $DB->query('SELECT * FROM `topic` WHERE ID='.$_GET['i'].' LIMIT 1');
$topic = $statement->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>

<title><?php echo $topic['text']; ?> &laquo; <?php echo $thread['name'] ?> Archive</title>
<link rel="stylesheet" type="text/css"	href="/stylesheets/archive.css"/>


</head>

<body id="home">
<h2><?php echo $topic['text']; ?></h2>
<?php $statement = $DB->query('SELECT * FROM `message` WHERE `created_dt`>=\''.$topic['created_dt'].'\' AND `created_dt`<=\''.$topic['end_dt'].'\' ORDER BY `created_dt` ASC'); ?>

<ol>
	<?php while($message = $statement->fetch(PDO::FETCH_ASSOC)) : ?>
	<li>
		
		<span class="topic-text"><?php echo $message['name']; ?></span>&nbsp;&#8594;&nbsp;<span class="topic-text"><?php echo $message['text']; ?></span>
		
		<br /><span class="topic-date"><?php echo $message['created_dt']; ?></span>
	</li>
	<?php endwhile; /*END TOPIC LOOPING*/?>
</ol>

</body>

</html>