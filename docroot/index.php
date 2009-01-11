<?php
$DB = new PDO('sqlite:../model/talk.sqlite');
$statement = $DB->query('SELECT * FROM `thread` WHERE ID=1 LIMIT 1');
$thread = $statement->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>

<title><?php echo $thread['name']; ?></title>
<link rel="stylesheet" type="text/css"	href="/stylesheets/main.css"/>

</head>

<body id="home">

<div id="main">
	<div id="topic-text"></div>
	<div id="comments-list">
	</div>
	<div id="comment-editor">
		<form id="comment-form" action="#" method="post">
			<input id="u" type="text" name="u" maxlength="8" /><div id="sep">&#8594;</div>
			<input id="t" type="text" name="t" maxlength="140"/>
		</form>
	</div>
	<div id="footer">
		<p><?php echo $thread['name']; ?>, superpowered by <a href="http://code.google.com/p/talc-thread">Talc Thread</a>. <a href="/archive">Archive</a>.</p>
	</div>
</div>

<script src="/javascript/mootools-1.2/core.js"></script>
<script src="/javascript/mootools-1.2/more.js"></script>
<script src="/javascript/main.js"></script>

<!-- Google Analytics -->

</body>

</html>