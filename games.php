<?php
if(isset($_REQUEST['action'])) {
  $action=$_REQUEST['action'];
} else { $action=""; }
//lists game, and allows users to join a game if desired
//also allows for game creation
$PERMISSIONS=1;
$PAGE="Games: ".ucfirst($action);
include 'includes/common.inc';
include 'includes/builds.inc';
include 'includes/headers.inc';

define('ACTIVE_GAME',1);
define('OPTION_PRIVATE',2);
define('OPTION_POWER_USERS_ONLY',4);
define('OPTION_MALES_ONLY',8);
define('OPTION_FEMALES_ONLY',16);

switch($action) {
//find a game
//simply a big list of games, based on binary values
	case "find":
		if(isset($_REQUEST['style'])) {
			$style=$_REQUEST['style']; //registar globals is OFF, gets style
		} else { $style=""; }
		if(isset($_REQUEST['sort'])) {
			$sort=$_REQUEST['sort']; //registar globals is OFF, gets sort
		} else { $sort=""; }
		if(isset($_REQUEST['start'])) {
			$start=$_REQUEST['start']; //registar globals is OFF, gets start
		} else { $start=0; }
		
		define('PER_PAGE',50);
		
		$sortArray=array('id'=>'','name'=>'','current_males'=>'','current_females'=>'','creation_time'=>'','last_activity'=>'','desc'=>'');

		if(empty($sort)) { $sort="id"; $style="ASC"; $start=0; }
		$pageArray['start']=$start;
		$sortArray[$sort]='selected="true"';
		
		$sql="SELECT SQL_CALC_FOUND_ROWS games.*, games.current_males+games.current_females AS total,
					games.max_players-(games.current_males+games.current_females) AS remaining, users.user_id, users.username
				FROM games JOIN users ON games.owner_id=users.user_id ORDER BY " . $sort .' '. $style . " LIMIT " . $start . ", ".PER_PAGE;
		$query=query($db, $sql);
		$grandtotal=get_row($db,"SELECT FOUND_ROWS()",1);
		$total=how_many($query);
		$result=next_row($query);
		
		$pageArray['page_numbers']=buildPageNumbers('game/find');
		$page->table='';

		for($i=0;$i<$total;$i++) {
			$page->table=$page->table.$page->loadPage('game_find',TABLE);
			
			$result['creation_time']=date("F j, G:i",$result['creation_time']);
			$result['last_activity']=date("F j, G:i",$result['last_activity']);

			$page->replace_tags($result,$page->table);
			
			$result=next_row($query);
		}

		$page->page=$page->loadPage('game_find',PAGE);
		$page->replace_tags($sortArray,$page->page);
		$page->replace_tags($pageArray,$page->page);
		$page->replace_table($page->page);
		$page->outputPage();
		break;
//game creation.  requires user to be logged in as a game creator
//this is the code for actually creating the game, and not the form the user fills out
	case "create":
		if($_SESSION['GameRank']>=2) {
			$name=sanitize($_POST['name']);
			$description=sanitize($_POST['desc']);
			if(!empty($name)) {
				$max=intval($_POST['max'],10);
				if((!empty($max))&&($max<255)&&($max>=10)) {
					$sql="SELECT id FROM games WHERE name=?";
					$query=query($db,$sql,$name);
					if(how_many($query)==0) {
						$options=ACTIVE_GAME;
						$options+=(isset($_POST['private'])) ? OPTION_PRIVATE : 0;
						$options+=(isset($_POST['onlyusers'])) ? OPTION_POWER_USERS_ONLY : 0;
						$options+=(isset($_POST['males'])) ? OPTION_MALES_ONLY : 0;
						$options+=(isset($_POST['females'])) ? OPTION_FEMALES_ONLY : 0;

						$now=time();
						$sql="INSERT INTO games (owner_id, name, description, max_players, creation_time, last_activity, options)
									VALUES('$user',?,?,?,'$now','$now','$options')";
						query($db,$sql,$name,$description,$max);

						query($db,"UPDATE users SET gamesRUN=gamesRUN+1 WHERE user_id='$user'");

						$page->page=$page->loadPage('game_create',PAGE);
						$page->replace_tags($pageArray,$page->page);
						$page->outputPage();
						break;
					} else { $error="Game name exists.  Choose a different one."; }
				} else { $error="Max players is empty or is too low (must be at least 10).  Enter a new value."; }
			} else { $error="Please enter a name for the game."; }
		} else {
			error('<h4>Permission Denied</h4>Your user rank is too low to create a game.  Go do something else first.');
		}
//game creation
//this is the form users fill out to create the game
	case "new":
		if($_SESSION['GameRank']>=2) {
			if(isset($error)) {
				$pageArray['error']='Error: '.$error.'<br/><br/>';
				$pageArray['name']=$_POST['name'];
				$pageArray['desc']=$_POST['desc'];
			} else {
				$pageArray['error']='';
				$pageArray['name']='';
				$pageArray['desc']='';
			}
			
			$page->page=$page->loadPage('game_new',PAGE);
			$page->replace_tags($pageArray,$page->page);
			$page->outputPage();
		} else {
			error('<h4>Permission Denied</h4>Your user rank is too low to create a game.  Go do something else first.');
		}
		break;
//apply to a game
//you must have a character to apply to the game, and it must be empty enough to apply
	case "apply":
		$which=$_GET['which'];
		$query=query($db,"SELECT id, current_males+current_females AS total, max_players FROM games WHERE id='$which'");
		$total=how_many($query);
		if($total>0) {
			$game=next_row($query);
			if($game['total']<$game['max_players']) {
				query($db,"UPDATE characters SET game_applied=? WHERE id=?",$which,$_POST['charlist']);
				query($db,"UPDATE games SET applied=applied+1 WHERE id=?",$which);

				echo 'You have applied to the game.  You will need to be approved by the game owner.  Thank you.';
			} else {
				$error="Game has filled up.  Apply to a different game, or apply faster next time.";
			}
		} else { $error="Game not found."; }
//view a game
//this is a lot like the game info
	case "view":
		//gets a value for which, or uses the one from the apply function
		if(isset($_GET['which'])) { $which=$_GET['which']; }
		//dies on an error if there's no $which
		if(!isset($which)) { error('Error: No game id found.'); }
		//preset all pageArray values
		$pageArray['error']='';
		$pageArray['manage']='';
		$pageArray['applyHeader']='';
		$pageArray['apply']='';
		$pageArray['which']=$which;
		//loads up any error message that might have come from the apply function
		if(isset($error)) { $pageArray['error']='Error: '.$error.'<br/><br/>'; }
		
		$sql="SELECT games.*, games.current_males+games.current_females AS total,
					games.max_players-(games.current_males+games.current_females) AS remaining,
					users.user_id, users.username FROM games JOIN users ON games.owner_id=users.user_id WHERE games.id=?";
		$result=get_row($db,$sql,0,$which);
		
		if(empty($result)) { error('Error: Game does not exist.'); }
		//check if the current user owns the game
		if($result['owner_id']==$user) {
			$pageArray['manage']='<span class="small"><a href="{URL}manage/{ID}">Manage Game</a></span><br/>';
		}
		//check to see if any spots are left for the game, if so, build the application form
		if($result['remaining']>0) {
			$pageArray['applyHeader']='<table id="create"><tr><td class="half">';
			$pageArray['apply']='</td><td valign="top"><h4>Apply to this game</h4>';
			
			//if the game requires power users only
			if(($result['options']&OPTION_POWER_USERS_ONLY)&&($_SESSION['GameRank']<2)) {
				$pageArray['apply'].='You cannot apply to this game.';
			} else {
				//grap a character the user owns, to make sure they have a character
				$query=query($db,"SELECT id FROM characters WHERE user_id='$user' AND game_applied=? LIMIT 1",$which);
				if(how_many($query)==0) {
					//if they do, grab a full list of characters who aren't in a game, and haven't applied to one recently
					$sql="SELECT id, name FROM characters WHERE user_id='$user' AND game_id=0 AND game_applied=0";
					//check for character gender
					if($result['options']&OPTION_MALES_ONLY) { $sql.=" AND gender=0"; $reason="Males Only"; }
					if($result['options']&OPTION_FEMALES_ONLY) { $sql.=" AND gender=1"; $reason="Females Only"; }
					$query=query($db,$sql);
				//I think this is a check for all characters.  might not be entirely needed, possibly remove later on
					$grandtotal=get_row($db,"SELECT id FROM characters WHERE user_id='$user' AND game_id=0 AND game_applied=0",1);
				
					if($grandtotal[0]==0) {
						$pageArray['apply'].='<a href="character/new">Make a character</a> first.';
					} else {
						//as long as the player HAS characters, run some checks to make sure they can apply, then build the actual form
						$total=how_many($query);
						if($total==0) {
							$pageArray['apply'].="Sorry, but none of your characters can apply to this game. Reason: $reason.";
						} else {
							$applyForm=$page->loadPage('game_apply_form',PAGE);
							$form['characters']='';
							//loop through all the characters
							for($i=0;$i<$total;$i++) {
								$char=next_row($query,1);
								$form['characters'].="<option value=\"$char[0]\">$char[1]</option>";
							}

							$page->replace_tags($form,$applyForm);
							$pageArray['apply'].=$applyForm;
						}
					}
				} else { $pageArray['apply'].='You have already applied to this game.  You cannot apply to the same game twice.'; }
			}
			$pageArray['apply'].='</td></tr></table>';
		}
		$result['description']=nl2br($result['description']);
		//output the actual page
		$page->page=$page->loadPage('game_view',PAGE);
		$page->replace_tags($pageArray,$page->page);
		$page->replace_tags($result,$page->page);
		$page->outputPage();
		
		break;
//lists all of the users game
//might want to require a check for user rank?
	case "mine":
		if(isset($_REQUEST['style'])) {
			$style=$_REQUEST['style']; //registar globals is OFF, gets style
		} else { $style=""; }
		if(isset($_REQUEST['sort'])) {
			$sort=$_REQUEST['sort']; //registar globals is OFF, gets sort
		} else { $sort=""; }
		if(isset($_REQUEST['start'])) {
			$start=$_REQUEST['start']; //registar globals is OFF, gets start
		} else { $start=0; }
		define('PER_PAGE',20);

		$sortArray=array('id'=>'','name'=>'','current_males'=>'','current_females'=>'','creation_time'=>'','last_activity'=>'','desc'=>'');

		if(empty($sort)) { $sort="id"; $style="ASC"; $start=0; }
		$pageArray['start']=$start;
		$sortArray[$sort]='checked="checked"';

		$sql="SELECT SQL_CALC_FOUND_ROWS games.*, current_males+current_females AS total,
					games.max_players-(games.current_males+games.current_females) AS remaining FROM games WHERE owner_id='$user'
					ORDER BY " . $sort .' '. $style . " LIMIT " . $start . ", ".PER_PAGE;
		$query=query($db, $sql);
		$grandtotal=get_row($db,"SELECT FOUND_ROWS()",1);
		$total=how_many($query);
		$result=next_row($query);

		$pageArray['page_numbers']=buildPageNumbers('game/mine');
		$page->table='';

		for($i=0;$i<$total;$i++) {
			$page->table=$page->table.$page->loadPage('game_mine',TABLE);

			if($result['applied']>0) {
				$result['list']=' <span class="small"><a href="{URL}manage/applied/{ID}">List</a>';
			} else { $result['list']=''; }
			$result['creation_time']=date("F j, G:i",$result['creation_time']);
			$result['last_activity']=date("F j, G:i",$result['last_activity']);

			$page->replace_tags($result,$page->table);

			$result=next_row($query);
		}

		$page->page=$page->loadPage('game_mine',PAGE);
		$page->replace_tags($sortArray,$page->page);
		$page->replace_tags($pageArray,$page->page);
		$page->replace_table($page->page);
		$page->outputPage();
		break;
	default:
		error('This should never happen, but if it did, then you did something to the URL, so be ashamed.');
		break;
}
?>