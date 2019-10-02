<?
if(!isset($PAGE)) {
	die('Hacking Attempt.  Access is denied.');
}
/*
Page.php

Page class
	header-the header text
	menu-the main menu text
	side_menu-the side menu text
	content-the actual page content
	dirLocation-the directory where the template is stored
	
	function Page($loc='template')
		constructor method
		accepts a directory location, defaults to template
		also loads the header and menu information
	
	function loadPage($template=NULL)
		accepts a template file
		loads the template file into memory
	
	function replace_tags($tags = array(),&$file)
		accepts an array of tags, and a reference to the file (template) being replaced
		replaces tags in the template with the required infomation
	
	function setTable($input,$reset=false)
		accepts a string as input, and an optional boolean value
		resets the table variable if the value is TRUE otherwise
		appends the input string to the end of the table variable
	
	function outputAll()
		outputs all the page files
	
	function outputHeader()
		outputs just the header file
		
	function error($msg)
		accepts a message
		dies with "ERROR:" and the message
*/
define('BASE',0);
define('HEADER',1);
define('PAGE',2);
define('TABLE',3);

class Page
{
	var $header;
	var $menu;
	var $side_menu;
  var $content;
	var $page;
	var $table;
	var $dirLocation;

	function Page($loc='template') {
		$this->dirLocation=$loc.'/';
	}

	function loadHeaders() {
		$this->header=$this->loadPage('header/header');
		if($_SESSION['GameRank']==0)
			$this->menu=$this->loadPage('header/menu');
		else
			$this->menu=$this->loadPage('header/menu_loggedIn');
	}
	
	function loadPage($template=NULL,$type=BASE)	{
		if($template!=NULL) { //make sure a page was input
			switch($type) {
				case HEADER:
					$template='header/'.$template;
					break;
				case PAGE:
					$template='pages/'.$template;
					break;
				case TABLE:
					$template='pages/tables/'.$template;
					break;
				default:
			}
			$template=$this->dirLocation.$template.'.tpl'; //change template name to conform to the directory location
			if(file_exists($template))
				return file_get_contents($template);
			else
				$this->error("Template file $template not found.");
			} else {
				$this->error('No template file specified.');
			}
	}
	
	function replace_tags($tags = array(), &$file) {//	echo $file;
		global $siteLoc;
		if (sizeof($tags) > 0) { //as long as tags has an element
			$tags['URL']=$siteLoc;
			foreach ($tags as $tag => $data) { //loop through each tag, with data being the key (IE, the value being replaced)
				$file = eregi_replace("{" . $tag . "}", $data, $file); //replace the key CASE INSENSITIVE!!!
			}
		} else {	$this->error('No tags designated for replacement.'); }
	}

	function setTable($input,$reset=false)	{
		if($reset==true)
			$this->table='';
		$this->table.=$input;
	}

	function replace_table(&$file)	{
		$file=eregi_replace("{TABLE}", $this->table, $file); //replace the key CASE INSENSITIVE!!!
	}

	function generatePage()	{
		$this->page=eregi_replace("{CONTENT}", $this->page, $this->content); //replace the key CASE INSENSITIVE!!!
	}

	function outputPage()	{
		$this->generatePage();
		echo $this->page;
	}
	//function might not be used
	function outputAll() {
		$this->outputHeader();
		$this->outputMenu();
		$this->outputSideMenu();
		echo $this->content;
	}

	function outputHeader() {
		echo $this->header;
	}

	function outputMenu() {
		echo $this->menu;
	}

	function outputSideMenu() {
		echo $this->side_menu;
	}

	function error($msg) {
		die("ERROR: $msg");
	}
}
?>