<?php

class topic_model
{
	public $ID;
	pulic $text;
	
	function __construct()
	{
		$this->DB = new PDO('sqlite:talk.sqlite');
		$this->DB->setAttribute(PDO::ERRMODE_EXCEPTION, PDO::ERRMODE_WARNING);
	}
	
	function load()
	{
		$statement = $this->DB->prepare('SELECT * FROM topic WHERE ID=?');
		$statement->bindColumn('ID', $this->ID);
		$statement->bindColumn('text', $this->text);
		$statement->execute(array($this->ID));
		$statement->fetch();
	}
	
}

?>