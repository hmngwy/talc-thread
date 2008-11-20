<?php
$DB = new PDO('sqlite:../model/talk.sqlite');
$statement = $DB->query('SELECT * FROM `thread` WHERE ID=1 LIMIT 1');
$thread = $statement->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>

<title><?php echo $thread['name']; ?> Archive</title>
<link rel="stylesheet" type="text/css"	href="/stylesheets/archive.css"/>


</head>

<body id="home">
<h2><?php echo $thread['name']; ?> Archive</h2>
<?php $statement = $DB->query('SELECT * FROM `topic` ORDER BY `created_dt` DESC LIMIT -1 OFFSET 1'); ?>

<ol>
	<?php while($topic = $statement->fetch(PDO::FETCH_ASSOC)) : ?>
	<li>
		<a href="transcript.php?i=<?php echo $topic['ID']; ?>" class="topic-msgs">
		<?php
			if($topic['end_dt'] == '0000-00-00 00:00:00')
				$msgs = $DB->query('SELECT count(*) AS count FROM `message` WHERE `message`.`created_dt`>=\''.$topic['created_dt'].'\'');
			else
				$msgs = $DB->query('SELECT count(*) AS count FROM `message` WHERE `message`.`created_dt`>=\''.$topic['created_dt'].'\' AND `message`.`created_dt`<=\''.$topic['end_dt'].'\'');
				
			$count = $msgs->fetch(PDO::FETCH_ASSOC);
			
			echo $count['count'];
		?><!--COMMENT<?php echo ($count>1) ? 'S' : '' ; ?>--></a>&nbsp;
		<a href="transcript/<?php echo $topic['ID']; ?>" class="topic-text"><?php echo $topic['text']; ?></a>
		<br /><span class="topic-date">from <?php echo $topic['created_dt']; ?> to <?php echo $topic['end_dt']; ?></span>
	</li>
	<?php endwhile; /*END TOPIC LOOPING*/?>
</ol>

</body>

</html>