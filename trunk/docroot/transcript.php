<?php
$DB = new PDO('sqlite:../model/talk.sqlite');
$statement = $DB->query('SELECT * FROM `thread` WHERE ID=1 LIMIT 1');
$thread = $statement->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>

<title><?php echo $thread['name']; ?> Archives</title>


</head>

<body id="home">
<h2><?php echo $thread['name']; ?> Archives</h2>
<?php $statement = $DB->query('SELECT * FROM `topic` ORDER BY `created_dt` DESC'); ?>

<ol>
	<?php while($topic = $statement->fetch(PDO::FETCH_ASSOC)) : ?>
	<li>
		<span class="topic-text"><?php echo $topic['text']; ?></span>
		<span class="topic-date"><?php echo $topic['created_dt']; ?></span>
		<span class="topic-open"><?php echo ($topic['end_dt'] == '0000-00-00 00:00:00') ? 'open': ''; ?></span>
		<span class="topic-msgs">
		<?php
			if($topic['end_dt'] == '0000-00-00 00:00:00')
				$msgs = $DB->query('SELECT count(*) AS count FROM `message` WHERE `message`.`created_dt`>=\''.$topic['created_dt'].'\'');
			else
				$msgs = $DB->query('SELECT count(*) AS count FROM `message` WHERE `message`.`created_dt`>=\''.$topic['created_dt'].'\' AND `message`.`created_dt`<=\''.$topic['end_dt'].'\'');
				
			$count = $msgs->fetch(PDO::FETCH_ASSOC);
			
			echo $count['count'];
		?>
		comment
	</li>
	<?php endwhile; /*END TOPIC LOOPING*/?>
</ol>

</body>

</html>