<?php
//startgame.php
//only accessible by game creators
$PERMISSIONS=2;
$PAGE="Starting a Game";
include 'includes/common.inc';

if(isset($_REQUEST['which'])) {
  $which=$_REQUEST['which'];
} else { $which=0; }

if(isset($_REQUEST['action'])) {
  $action=$_REQUEST['action'];
} else { $action=""; }

$game=get_row($db,"SELECT owner_id FROM games WHERE id=?",0,$which);

if((empty($game))||($game['owner_id']!=$user)) {
	error('Game ID does not match.<br/>Either you provided a bad game ID, or this game is not yours.');
}

if($action=="start") {
	if(isset($_POST['confirm'])) {
		include 'Page.php';
		$page=new Page('template');
		
		$page->page=$page->loadPage('start_game_notice',PAGE);
		
		//gets the starting location of the characters
		$sql="SELECT id, pos_x, pos_y FROM maps WHERE game_id=? AND options | 8";
		$startLoc=get_row($db,$sql,0,$which);
		
		//get the character id and weapon id, to form a list of both for the game
		$sql="SELECT id, weapon_id FROM characters WHERE game_id=?";
		$query=query($db,$sql,$which);
		$total=how_many($query);
		
		$result=next_row($query);
		//load the weapon and character list
		for($i=0;$i<$total;$i++) {
			$weapons[$i]=$result['weapon_id'];
			$characters[$i]=$result['id'];
			$result=next_row($query);
		}
		//shuffle the weapon list
		shuffle($weapons);
		//put the new weapon ids into the characters, effectively randomizing the new weapon list
		for($i=0;$i<$total;$i++) {
			$sql="SELECT ammo FROM weapons WHERE id=?";
			$weapon=get_row($db,$sql,0,$weapons[$i]);
			$sql="UPDATE characters SET weapon_id=".$weapons[$i].", weapon_ammo=".$weapon['ammo'].",
						pos_x=".$startLoc['pos_x'].", pos_y=".$startLoc['pos_x'].", health=20, max_health=20 WHERE id=?";
			query($db,$sql,$characters[$i]);
		}
		//update the map start position to show the amount of players there
		$sql="UPDATE maps SET players=$total WHERE id=".$startLoc['id'];
		query($db,$sql);
		//update the game stat info
		$sql="UPDATE games SET start_time=".time().", last_activity=".time()." WHERE id=?";
		query($db,$sql,$which);
		
		$pageArray['which']=$which;
		$page->replace_tags($pageArray,$page->page);
		echo $page->page;
		
		exit();
	}
}


include 'includes/builds.inc';
include 'includes/headers.inc';

switch($action) {
	default:
		$sql="SELECT COUNT(id) FROM characters WHERE weapon_id=0 AND game_id=?";
		$weaponless=get_row($db,$sql,1,$which);
		//make sure all characters have a weapon assignment
		if($weaponless[0]==0) {
			$page->page=$page->loadPage('start_game',PAGE);
		} else {
			$page->page=$page->loadPage('start_game_weaponless',PAGE);
		}
}

$pageArray['which']=$which;
$page->replace_tags($pageArray,$page->page);
$page->outputPage();
?>