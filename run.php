<?php
if(isset($_REQUEST['action'])) {
  $action=$_REQUEST['action'];
} else { $action=""; }
if(isset($_REQUEST['which'])) {
  $which=$_REQUEST['which'];
} else { $which=0; }

$PERMISSIONS=2;
$PAGE="Run Game";
include 'includes/common.inc';
include 'includes/builds.inc';
include 'includes/headers.inc';

//verifies ownership of the game
$game=get_row($db,"SELECT owner_id, start_time FROM games WHERE id=?",0,$which);
if((empty($game))||($game['owner_id']!=$user)) {
	error('Game ID does not match. Either you provided a bad game ID, or this game is not yours.');
}

$pageArray['which']=$which;
$pageArray['msg']='';

switch($action) {
//code for listing the characters currently in the game
	case "list":
		$sql="SELECT SQL_CALC_FOUND_ROWS characters.*, users.username, weapons.name AS wname
					FROM characters JOIN users ON characters.user_id=users.user_id
					JOIN weapons ON characters.weapon_id=weapons.id
					WHERE game_id=?";
		$query=query($db, $sql,$which);
		$total=how_many($query);
		if($total==0) { error('No characters are in the game.'); }

		$page->table='';
		$result=next_row($query);

		for($i=0;$i<$total;$i++) {
			$page->table=$page->table.$page->loadPage('manage_list',TABLE);
			$result['gender']=($result['gender']==1) ? "Female" : "Male";
			
			$result['weapon_str']='<a href="{URL}weapons/view/'.$result['weapon_id'].'">'.$result['wname'].'</a>';
			$page->replace_tags($pageArray,$result['weapon_str']);
			$page->replace_tags($result,$page->table);
			$result= next_row($query);
		}
		$page->page=$page->loadPage('manage_list',PAGE);
		$page->replace_tags($pageArray,$page->page);
		$page->replace_table($page->page);
		$page->outputPage();
		break;
	case "nextround":
		$sql="UPDATE games SET round=round+1 WHERE id=?";
		query($db,$sql,$which);
	default:
		$sql="SELECT *, current_males+current_females AS total FROM games WHERE id=?";
		$game=get_row($db,$sql,0,$which);
		
		$game['description']=nl2br(stripslashes($game['description']));
		
		if($game['round']==0) {
			$pageArray['current_round']="Initial Round";
			$pageArray['next_round_button']="Start First Action Round";
		} elseif($game['round']%2==0) {
			$pageArray['current_round']="Movement Round (".($game['round']/2).")";
			$pageArray['next_round_button']="Start Next Action Round";
		} else {
			$pageArray['current_round']="Action Round (".ceil($game['round']/2).")";
			$pageArray['next_round_button']="Start Next Movement Round";
		}
		$page->page=$page->loadPage('run',PAGE);
		$page->replace_tags($game,$page->page);
		$page->replace_tags($pageArray,$page->page);
		$page->outputPage();
}
?>