<?php
if($_SERVER['REQUEST_URI'] == $_SERVER["SCRIPT_NAME"])
{
	header('HTTP/1.1 301 Moved Permanently');
	header('Location: /archive');
}
$DB = new PDO('sqlite:../model/talk.sqlite');
$statement = $DB->query('SELECT * FROM `thread` WHERE ID=1 LIMIT 1');
$thread = $statement->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/> 

<title><?php echo $thread['name']; ?> Archive</title>
<link rel="stylesheet" type="text/css"	href="/stylesheets/archive.css"/>


</head>

<body id="home">
<h2><?php echo $thread['name']; ?> Archive</h2>
<?php $statement = $DB->query('SELECT * FROM `topic` WHERE `topic`.`end_dt`!=\'0000-00-00 00:00:00\' ORDER BY `created_dt` DESC'); ?>

<ol>
	<?php while($topic = $statement->fetch(PDO::FETCH_ASSOC)) : ?>
	<li>
		<a href="transcript.php?i=<?php echo $topic['ID']; ?>" class="topic-msgs">
		<?php
			$msgs = $DB->query('SELECT count(*) AS count FROM `message` WHERE `message`.`created_dt`>=\''.$topic['created_dt'].'\' AND `message`.`created_dt`<=\''.$topic['end_dt'].'\'');
				
			$count = $msgs->fetch(PDO::FETCH_ASSOC);
			
			echo $count['count'];
		?><!--COMMENT<?php echo ($count>1) ? 'S' : '' ; ?>--></a>&nbsp;
		<a href="transcript/<?php echo $topic['ID']; ?>" class="topic-text"><?php echo $topic['text']; ?></a>
		<br /><span class="topic-date"><strong>FROM</strong> <?php echo $topic['created_dt']; ?> <strong>TO</strong> <?php echo $topic['end_dt']; ?></span>
	</li>
	<?php endwhile; /*END TOPIC LOOPING*/?>
</ol>

</body>

</html>