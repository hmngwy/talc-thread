<?php
$DB = new PDO('sqlite:../model/talk.sqlite');

$statement = $DB->query('SELECT * FROM `topic` WHERE ID='.$_GET['i'].' LIMIT 1');
$topic = $statement->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>

<title>Talk Transcript: <?php echo $topic['text']; ?></title>
<link rel="stylesheet" type="text/css"	href="/stylesheets/transcript.css"/>


</head>

<body id="home">
<span class="msg-count">
<?php
	$msgs = $DB->query('SELECT count(*) AS count FROM `message` WHERE `message`.`created_dt`>=\''.$topic['created_dt'].'\' AND `message`.`created_dt`<=\''.$topic['end_dt'].'\'');	
	$count = $msgs->fetch(PDO::FETCH_ASSOC);
	echo $count['count'];
?>
</span>
<h2><?php echo $topic['text']; ?></h2><br />
<span class="topic-dates"><strong>FROM</strong> <?php echo $topic['created_dt']; ?> <strong>TO</strong> <?php echo $topic['end_dt']; ?></span>

<?php $statement = $DB->query('SELECT * FROM `message` WHERE `created_dt`>=\''.$topic['created_dt'].'\' AND `created_dt`<=\''.$topic['end_dt'].'\' ORDER BY `created_dt` ASC'); ?>

<ol>
	<?php while($message = $statement->fetch(PDO::FETCH_ASSOC)) : ?>
	<li>
		<span class="msg-name"><?php echo $message['name']; ?></span>:&nbsp;<span class="msg-text"><?php echo $message['text']; ?></span>
		<!--<span class="topic-date"><?php echo $message['created_dt']; ?></span>-->
	</li>
	<?php endwhile; ?>
</ol>
</body>

</html>