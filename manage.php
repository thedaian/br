<?php
if(isset($_REQUEST['action'])) {
  $action=$_REQUEST['action'];
} else { $action=""; }
if(isset($_REQUEST['which'])) {
  $which=$_REQUEST['which'];
} else { $which=0; }

$PERMISSIONS=2;
$PAGE="Manage Game";
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
//code for assigning and removing weapons
//all character MUST have weapons for the game to start.
//weapons cannot be altered after the game has started
	case "assignWeapon":
	case "removeWeapon":
		if($game['start_time']==0) {
			if($action=="assignWeapon") {
				$weaponID=$_POST['weaponID'];
				$who=$_GET['who'];
				$sql="SELECT id, name, ammo FROM weapons WHERE id=?";
				$weapon=get_row($db,$sql,0,$weaponID);
				$sql="UPDATE characters SET weapon_id=?, weapon_ammo=".$weapon['ammo']." WHERE id=?";
				query($db,$sql,$weapon['id'],$who);
				$pageArray['msg']='Weapon assigned.';
			} elseif($action=="removeWeapon") {
				$who=$_GET['who'];
				$sql="UPDATE characters SET weapon_id=0, weapon_ammo=0 WHERE id=?";
				query($db,$sql,$who);
				$pageArray['msg']='Weapon removed.';
			}
		} else {
			error('Game has started, unable to change weapons at this point.');
		}
//code for listing the characters currently in the game
//also loads up a list of weapons, to be output if any character doesn't have weapons assigned to them
	case "list":
		$sql="SELECT SQL_CALC_FOUND_ROWS characters.*, users.username FROM characters JOIN users ON characters.user_id=users.user_id WHERE game_id=?";
		$query=query($db, $sql,$which);
		$total=how_many($query);
		if($total==0) { echo 'No characters are in the game.'; break; }
		
		$pageArray['weapon_list']='<option value="0">Select Weapon</option>';
		$sql="SELECT id, name FROM weapons ORDER BY name DESC";
		$wQuery=query($db,$sql);
		$wtotal=how_many($wQuery);
		for($i=0;$i<$wtotal;$i++) {
			$weapon=next_row($wQuery);
			$pageArray['weapon_list'].='<option value="'.$weapon['id'].'">'.$weapon['name'].'</option>';
		}
		$page->table='';
		$result=next_row($query);

		for($i=0;$i<$total;$i++) {
			$page->table=$page->table.$page->loadPage('manage_list',TABLE);
			$result['gender']=($result['gender']==1) ? "Female" : "Male";
			if($result['weapon_id']>0) {
				$weaponname=get_row($db,"SELECT name FROM weapons WHERE id=".$result['weapon_id'],1);
				$result['weapon_str']='<a href="{URL}weapons/view/{weapon_id}">'.$weaponname[0].'</a>';
				if($game['start_time']==0) {
					$result['weapon_str'].=' | <a href="{URL}manage/removeWeapon/{which}/{id}">Remove</a>';
				}
			} else {
				$result['weapon_str']=$page->loadPage('manage_weapon_form',TABLE);
			}
			$page->replace_tags($pageArray,$result['weapon_str']);
			$page->replace_tags($result,$result['weapon_str']);
			$page->replace_tags($result,$page->table);
			$result= next_row($query);
		}
		$page->page=$page->loadPage('manage_list',PAGE);
		$page->replace_tags($pageArray,$page->page);
		$page->replace_table($page->page);
		$page->outputPage();

		break;
		
//code for approving a character, edits stats of the game, and updates game activity
	case "approve":
		$sql="SELECT current_males, current_females, start_health FROM games WHERE id=?";
		$game=get_row($db,$sql,0,$which);
		$sql="SELECT gender, name, game_applied FROM characters WHERE id=?";
		$char=get_row($db,$sql,0,$_GET['who']);
		if(empty($char)) {
			echo 'Error: Character does not exist.';
			break;
		}
		if($char['game_applied']!=$which) {
			echo 'Error: Character has not applied to this game.';
			break;
		}
		$sql="UPDATE characters SET game_applied=0, game_id=?, health=".$game['start_health'].", max_health=".$game['start_health'].",
			pos_x=0, pos_y=0, next_x=0, next_y=0, weapon_id=0 WHERE id=?";
		query($db,$sql,$which,$_GET['who']);
		
		$sql="UPDATE games SET applied=applied-1, current_";
		$sql.=($char['gender']==1) ? "females=current_females" : "males=current_males";
		$sql.="+1, last_activity=".time()." WHERE id=?";
		$game=query($db,$sql,$which);
		
		$page->page=$page->loadPage('manage_approve',PAGE);
		$page->replace_tags($pageArray,$page->page);
		$page->replace_table($page->page);
		$page->outputPage();
//code for denial of a character
//as yet, there's nothing in place to make sure that character doesn't apply again
//thus, this could be open to abuse
//CHECK OUT LATER!!!!!!!
	case "deny":
		if($action=="deny") {
			$sql="SELECT name, game_applied FROM characters WHERE id=?";
			$char=get_row($db,$sql,0,$_GET['who']);
			if(empty($char)) {
				echo 'Error: Character does not exist.';
				break;
			}
			if($char['game_applied']!=$which) {
				echo 'Error: Character has not applied to this game.';
				break;
			}
			$sql="UPDATE characters SET game_applied=0 WHERE id=?";
			query($db,$sql,$_GET['who']);
			print($char['name'].' has been denied.');
			$sql="UPDATE games SET applied=applied-1, last_activity=".time()." WHERE id=?";
			query($db,$sql,$which);
		}
//lists characters who have applied to the game
	case "applied":
		$sql="SELECT SQL_CALC_FOUND_ROWS characters.*, users.username FROM characters JOIN users ON characters.user_id=users.user_id WHERE game_applied=?";
		$query=query($db, $sql,$which);
		$total=how_many($query);
		if($total==0) { 
			$page->page='<h4>Characters who\'ve applied</h4>No more characters have applied to the game.';
			$page->replace_tags($pageArray,$page->page);
			$page->outputPage();
			break;
		}
		$result=next_row($query);
		$page->table='';

		for($i=0;$i<$total;$i++) {
			$page->table=$page->table.$page->loadPage('manage_applied',TABLE);
			$result['gender']=($result['gender']==1) ? "Female" : "Male";
			$page->replace_tags($result,$page->table);
			
			$result= next_row($query);
		}
		
		$page->page=$page->loadPage('manage_applied',PAGE);
		$page->replace_tags($pageArray,$page->page);
		$page->replace_table($page->page);
		$page->outputPage();

		break;

//code for map uploading
//creates the map file, and positions in the database
//map size MUST be 300 by 300 pixels, and under 120kb, i think?  Check this
	case "uploading":
    //PHP code to determine the type of a file given
    //8 bytes of header data. Much more accurate than checking
    //the user-supplied Content-Type and of course this is far
    //better than relying on checking the file extension :).

    //(C)2004 r1ch.net. I place this code into the public domain
    //in the hope it is useful to somebody.
		if(empty($_FILES['userfile']['tmp_name'])) {
			error('File has an error.  Please resubmit');
		}
		$size=getimagesize($_FILES['userfile']['tmp_name']);
    //open a file
    $image_data = fopen($_FILES['userfile']['tmp_name'], "rb");

    //grab first 8 bytes, should be enough for most formats
    $header_bytes = fread($image_data, 8);

    //close file
    fclose ($image_data);

    //compare header to known signatures
    if (!strncmp ($header_bytes, "\xFF\xD8", 2))
        $file_format = ".JPEG";
    else if (!strncmp ($header_bytes, "\x89\x50\x4E\x47\x0D\x0A\x1A\x0A", 8))
        $file_format = ".PNG";
    else if (!strncmp ($header_bytes, "GIF", 3))
        $file_format = ".GIF";
    else
        $file_format = "unknown";
    
		if($file_format=="unknown") {
			error('File format unknown.  Only jpeg, png, or gif files are allowed.');
		}

		$uploaddir = 'card_templates/';
		$uploadfile = $uploaddir . $which . $file_format;
		if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
			$page->page='File uploaded.';
			$page->replace_tags($pageArray,$page->page);
			$page->outputPage();

		} else { error('Problem with file upload.  File not uploaded, try again.'); }
		break;
//deletes the ENTIE GAME
	case "delete":
		if(isset($_POST['confirm'])) {
			query($db,'DELETE FROM games WHERE id=?',$which);
			query($db,'DELETE FROM maps WHERE game_id=?',$which);
			query($db,'UPDATE characters SET game_applied=0 WHERE game_applied=?',$which);
			query($db,'UPDATE users SET gamesRUN=gamesRUN-1 WHERE user_id=?',$user);
			
			$page->page='<h4>Game Deleted</h4>Back to <a href="{URL}game/mine">my games.</a>';
			$page->replace_tags($pageArray,$page->page);
			$page->outputPage();
			break;
		} else {
			$page->page='Game cannot be deleted.  Use the checkbox to verify deletion.';
			$page->replace_tags($pageArray,$page->page);
			$page->outputPage();
		}
	case "edited":
		if($action!="delete") {
			$description=sanitize($_POST['desc']);
			$sql="UPDATE games SET description=? WHERE id=?";
			query($db,$sql,$description,$which);
			$pageArray['edit_msg']="Description Successfully Changed<br/><br/>";
		}
//the basic management portal of the game
//NOTE: once the game starts, there should be a different file, to make things easier
//possibly use this file as a portal, and include a file as needed, depending on game started status
	default:
		$sql="SELECT *, current_males+current_females AS total FROM games WHERE id=?";
		$game=get_row($db,$sql,0,$which);
		
		$game['description']=nl2br(stripslashes($game['description']));
		
		$page->page=$page->loadPage('manage',PAGE);

		$game['applied_link']=($game['applied']>0) ? '<br/><a class="small" href="manage/applied/{WHICH}">List Applicants</a>':'';
		$game['list_link']=($game['total']>0) ? '<br/><a class="small" href="manage/list/{WHICH}">List Characters</a>':'';
		if(!empty($game['map_type'])) {
			$game['map_form']=$page->loadPage('manage_map_edit_form',TABLE);
		} else {
			$game['map_form']=$page->loadPage('manage_map_upload_form',TABLE);
		}
		
		if(($game['total']>=8)&&(!empty($game['map_type']))) {
			$game['start_msg']='<a href="{URL}startgame/{WHICH}">Start Game</a>';
		} else {
			$game['start_msg']='You need';
			if($game['total']<8) {
				$game['start_msg'].=' at least 8 characters in the game';
				if(empty($game['map_type'])) {
					$game['start_msg'].=', and a map uploaded'; }
			} elseif(empty($game['map_type'])) {
				$game['start_msg'].=' a map uploaded'; }
			$game['start_msg'].=' before you can start the game.';
		}
		
		if(!isset($pageArray['edit_msg'])) {
			$pageArray['edit_msg']="";
		}			
		
		$page->replace_tags($game,$page->page);
		$page->replace_tags($pageArray,$page->page);
		$page->outputPage();
		break;
}
?>