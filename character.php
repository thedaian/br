<?php
if(isset($_REQUEST['action'])) {
  $action=$_REQUEST['action'];
} else { $action=""; }
//character.php
//anyone can create characters
//used for management of characters
$PERMISSIONS=1;
$PAGE="Character: ".ucfirst($action);
include 'includes/common.inc';
include 'includes/builds.inc';
include 'includes/headers.inc';

switch($action) {
//code for successful editing of a character
	case "edited":
		$sql="SELECT user_id, game_id, game_applied FROM characters WHERE id=?";
		$char=get_row($db,$sql,0,$_POST['which']);
		if(!empty($char)) { //make sure the character exists
			if($char['user_id']==$user) { //make sure the user owns this character
				if(($char['game_id']==0)&&($char['game_applied']==0)) { //make sure the character is not currently in a game or applied to a game
					$sanName=sanitize($_POST['name']);
					$sanDesc=sanitize($_POST['desc']);
					$sanGender=sanitize($_POST['gender']);
					$sql="UPDATE characters SET name=?, description=?, gender=? WHERE id=?";
					query($db,$sql,$sanName,$sanDesc,$sanGender,$_POST['which']);
					//output message
					echo $_POST['name'].'edited successfully.<br/><br/>';
				} else { echo '<h4>Error</h4>You cannot edit a character that has applied to a game or is in a game.';	}
			} else { error('That is not your character to edit.');	}
		} else {	error('Error: Character not found.'); }
//listing of your characters
	case "list":
		if(isset($_REQUEST['style'])) {
			$style=$_REQUEST['style']; //registar globals is OFF, gets style
		} else { $style=""; }
		if(isset($_REQUEST['sort'])) {
			$sort=$_REQUEST['sort']; //registar globals is OFF, gets sort
		} else { $sort=""; }
		if(isset($_REQUEST['start'])) {
			$start=$_REQUEST['start']; //registar globals is OFF, gets start
		} else { $start=0; }
		
		if(empty($sort)) { $sort="id"; $style="ASC"; $start=0; }
		//possibly allow this as an option, along with other such things
		define('PER_PAGE',10);
		
		$sortArray=array('id'=>'','name'=>'','current_males'=>'','current_females'=>'','creation_time'=>'','last_activity'=>'','desc'=>'');

		if(empty($sort)) { $sort="id"; $style="ASC"; $start=0; }
		$pageArray['start']=$start;
		$sortArray[$sort]='selected="true"';

		$sql="SELECT SQL_CALC_FOUND_ROWS * FROM characters ORDER BY " . $sort .' '. $style . " LIMIT " . $start . ", ".PER_PAGE;
		$query=query($db, $sql);
		$grandtotal=get_row($db,"SELECT FOUND_ROWS()",1);
		$total=how_many($query);
		
		if($total==0) { //characters exist check
			$page->page=$page->loadPage('character_list_none',PAGE);
			$page->replace_tags($pageArray,$page->page);
			$page->outputPage();
			break;
		}

		$pageArray['page_numbers']=buildPageNumbers('game/find');
		$page->table='';
		$result=next_row($query);
		
		for($i=0;$i<$total;$i++) {
			$page->table=$page->table.$page->loadPage('character_list',TABLE);

			if($result['game_id']>0) {
				$gamename=get_row($db,"SELECT name FROM games WHERE id=".$result['game_id'],1);
				$result['game_name']='<a href="{URL}gameInfo/'.$result['game_id'].'">'.$gamename[0].'</a>';
			} else { $result['game_name']='Not in a game'; }

			if($result['game_applied']>0) {
				$gamename=get_row($db,"SELECT name FROM games WHERE id=".$result['game_applied'],1);
				$result['applied_name']='<a href="{URL}game/view/'.$result['game_applied'].'">'.$gamename[0].'</a>';
			} else { $result['applied_name']='No game'; }

			$result['gender']=($result['gender']==1) ? "Female" : "Male";

			$page->replace_tags($result,$page->table);
			$result=next_row($query);
		}
		
		$page->page=$page->loadPage('character_list',PAGE);
		$page->replace_tags($sortArray,$page->page);
		$page->replace_tags($pageArray,$page->page);
		$page->replace_table($page->page);
		$page->outputPage();
		break;
//create a character
//actual database updating code, not the form
	case "create":
		$name=sanitize($_POST['name']);
		if(!empty($name)) {
			$desc=sanitize($_POST['desc']);
			$gender=sanitize($_POST['gender']);

			$sql="INSERT INTO characters (user_id, game_id, name, description, gender) VALUES('$user','0',?,?,?);";
			query($db,$sql,$name,$desc,$gender);
			
			$page->page=$page->loadPage('character_create',PAGE);
			$page->replace_tags($pageArray,$page->page);
			$page->outputPage();
			break;
		} else {
			$error="Please provide a name.";
		}
//form for creating the character
	case "new":
		$pageArray['error']=isset($error) ? 'Error: '.$error.'<br/><br/>' : '';
		
		$page->page=$page->loadPage('character_new',PAGE);
		$page->replace_tags($pageArray,$page->page);
		$page->outputPage();
		break;
//viewing an individual character
	case "view":
		$sql="SELECT characters.*, users.username FROM characters JOIN users ON characters.user_id=users.user_id WHERE characters.id=?";
		$char=get_row($db,$sql,0,$_GET['which']);
		if(!empty($char)) {
			$char['name']=stripslashes($char['name']);
			$char['description']=stripslashes($char['description']);
			
			$char['gender'] = 0 ? 'Male': 'Female';
			//if($char['gender']==1) { echo 'Female'; }
			if($_SESSION['GameRank']>=2) {
				$pageArray['owner']='<br/><br/>Owner: <a href="{URL}user/view/{USER_ID}">{USERNAME}</a>';
			}
			$page->page=$page->loadPage('character_view',PAGE);
			$page->replace_tags($pageArray,$page->page);
			$page->replace_tags($char,$page->page);
			$page->outputPage();
		} else {	echo '<h4>Error:</h4>Character not found.';	}
		break;
//form for editing a characters information
	case "edit":
		$sql="SELECT * FROM characters WHERE id=?";
		$char=get_row($db,$sql,0,$_GET['which']);
		if(!empty($char)) { //character found check
			if($char['user_id']==$user) { //verifies the user owns the character
				if(($char['game_id']==0)&&($char['game_applied']==0)) {  //makes sure the character has NOT applied to, or is in, a game
					$char['name']=stripslashes($char['name']);
					$char['description']=stripslashes($char['description']);
					$pageArray['which']=$_GET['which'];
					$pageArray['gender0'] = $char['gender']==0 ? ' selected="true"': '';
					$pageArray['gender1'] = $char['gender']==1 ? ' selected="true"': '';
					
					$page->page=$page->loadPage('character_edit',PAGE);
					$page->replace_tags($pageArray,$page->page);
					$page->replace_tags($char,$page->page);
					$page->outputPage();
				} else { echo '<h4>Error</h4>You cannot edit a character that has applied to a game or is in a game.';	}
			} else { error('Error: That is not your character to edit.');	}
		} else {	error('Error: Character not found.');	}
		break;
	default:
		error('This should never happen, but if it did, then you did something to the URL, so be ashamed.');
		break;
}
?>