<?php
$PERMISSIONS=1;
$PAGE="Game Info";
include 'includes/common.inc';
include 'includes/builds.inc';
include 'includes/headers.inc';

if(isset($_REQUEST['which'])) {
  $which=$_REQUEST['which'];
} else { $which=0; }

//outputs the information for the game
//this is all basic into, and should not be confused with game management, or the game list

if($which>0) {
	$sql="SELECT games.*, games.current_males+games.current_females AS total,
					games.max_players-(games.current_males+games.current_females) AS remaining,
					users.user_id, users.username FROM games JOIN users ON games.owner_id=users.user_id WHERE games.id=?";
	$result=get_row($db,$sql,0,$which);
	if(!empty($result)) {
		$result['description']=nl2br($result['description']);

		$result['creation_time']=date("F j, G:i:s",$result['creation_time']);
		$result['last_activity']=date("F j, G:i:s",$result['last_activity']);
		
		$page->page=$page->loadPage('game_info',PAGE);
		$page->replace_tags($pageArray,$page->page);
		$page->replace_tags($result,$page->page);
		$page->outputPage();

	} else { error('Game does not exist'); }
} else { error('Game ID not found.  Please provide an id.'); }
?>