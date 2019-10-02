<?php
$PERMISSIONS=1;
$PAGE="Game Map";
include 'includes/common.inc';
include 'includes/builds.inc';
include 'includes/headers.inc';

if(isset($_REQUEST['which'])) {
  $which=$_REQUEST['which'];
} else { $which=0; }

if(isset($_REQUEST['action'])) {
  $action=$_REQUEST['action'];
} else { $action=''; }

$game=get_row($db,"SELECT id, map_type, owner_id FROM games WHERE id=?",0,$which);

if((empty($game))||(empty($game['map_type']))) {
	error('Game ID or map not found.<br/>Either you provided a bad game ID, or this game does not have any map uploaded.');
}

define('IMPASSIBLE',1);
define('DANGER_ZONE',2);
define('NEW_DANGER_ZONE',4);
define('START_LOCATION',8);

$page->page=$page->loadPage('map',PAGE);
//generate the cache file name
$cache="includes/mapcache/".$game['id'].".cache";

if($action=="edit") {
	if($_SESSION['GameRank']>=2) {
		$options=0;
		$options+=(isset($_POST['danger'])) ? NEW_DANGER_ZONE : 0;
		$options+=(isset($_POST['impass'])) ? IMPASSIBLE : 0;
		$options+=(isset($_POST['start'])) ? START_LOCATION : 0;

		$sql="UPDATE maps SET options='$options' WHERE id=?";
		query($db,$sql,$_POST['map_id']);
		//delete the cached file
		unlink($cache);
	}
}
//if the cache exists, load into a string for use with the system
//otherwise, generate the cached file, and return the map string
if(file_exists($cache)) {
	$pageArray['map']=file_get_contents($cache);
} else {
	include 'includes/mapcache.inc';
	$pageArray['map']=generate_map_cache($game['id']);
}
//output page
$page->replace_tags($pageArray,$page->page);
$page->replace_tags($game,$page->page);
$page->outputPage();
?>