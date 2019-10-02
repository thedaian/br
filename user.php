<?php
if(isset($_REQUEST['action'])) {
	$action=$_REQUEST['action'];
} else { $action="new"; }

if(isset($_REQUEST['which'])) {
  $which=$_REQUEST['which'];
} else { $which=0; }
//sets permissions required based on the action
if(($action=="new")||($action=="register")||($action=="view")||($action=="addComment")) {
	$PERMISSIONS=0;
} else {
	if($action=="addComment") {
		$PERMISSIONS=2;
	} else {
		$PERMISSIONS=1;
	}
}

$PAGE="User Profile";
include 'includes/common.inc';
include 'includes/builds.inc';
include 'includes/headers.inc';

switch($action) {
//register a character
//NOTE this is the processing code, not the form
	case "register":
		$name=sanitize($_POST['name']);
		$password=sanitize($_POST['password']);
		$password2=sanitize($_POST['password2']);
		$email=sanitize($_POST['email']);
		$email2=sanitize($_POST['email2']);
		
		if(($password==$password2)&&($email==$email2)&&(!empty($name))) {
			$sql="SELECT user_id FROM users WHERE username=?";
			$query=query($db,$sql,$name);
			if(how_many($query)==0) {
				$password=crypt($password);
				$salt=substr($password, 0, 12);
				$password=substr($password, 12);

				$sql="INSERT INTO users (username, password, salt, email, last_login, created, user_level, last_IP,
							gamesIN, gamesRUN, gamesTOTAL, gamesSURVIVED, gamesDIED)
							VALUES(?,'$password','$salt',?,'0','".time()."','1',INET_ATON('".$_SERVER['REMOTE_ADDR']."'),
							'0','0','0','0','0')";
				query($db,$sql,$name,$email);
				
				$pageArray['name']=$name;
				$pageArray['password']=$password2;
				
				$page->page=$page->loadPage('user_register',PAGE);
				$page->replace_tags($pageArray,$page->page);
				$page->outputPage();
				break;
			}
			$error="That username (".$name.") already exists.  Please select another one.";
		} else { $error="One of the fields is blank or didn't match.  Try again."; }
		error($error);
//form for creating a new character
	case "new":
		$page->page=$page->loadPage('user_new',PAGE);
		$page->replace_tags($pageArray,$page->page);
		$page->outputPage();
		break;
//edit profile mode
	case "edit":
	case "profile":
		$result=get_row($db,"SELECT email FROM users WHERE user_id='$user'");
		if(!empty($result)) { //check to make sure user exists/is found
			$pageArray['email']=$result['email'];
			
			$page->page=$page->loadPage('user_edit',PAGE);
			$page->replace_tags($pageArray,$page->page);
			$page->outputPage();
		} else { error('User does not exist. I have no idea how you did that.'); }
		break;
//comment adding, specificly, the processing section
	case "addComment":
		$options=0;
		$sql="INSERT INTO comments (user_id, poster_id, time_posted, options, view_level, comment)
					VALUES('$user',?,'".time()."','$options',?,?)";
		query($db,$sql,$which,$_POST['view_level'],sanitize($_POST['comment']));
		echo 'Comment posted.';
//comment deletion
	case "delete":
		if($action=="delete") {
			$sql="DELETE FROM comments WHERE id=?";
			query($db,$sql,$_GET['id']);
		}
//profile viewing
//commenting code drops down from here
	case "view":
		if(isset($which)) {
			$result=get_row($db,"SELECT * FROM users WHERE user_id=?",0,$which);
			if(!empty($result)) {
				$result['created']=date("F j, G:i:s",$result['created']);
				$result['last_login']=date("F j, G:i:s",$result['last_login']);
				$pageArray['survival_rate']=($result['gamesTOTAL']>0) ? $result['gamesSURVIVED']/$result['gamesTOTAL']: 'No games played';
				$pageArray['comments']='';
				$pageArray['comment_Form']='';
				$pageArray['which']=$which;

				$sql="SELECT comments.*, users.username FROM comments JOIN users ON comments.poster_id=users.user_id WHERE comments.user_id='$user'";
				$query=query($db,$sql);
				$total=how_many($query);
				
				for($i=0;$i<$total;$i++) {
					$comment=next_row($query);
					if($comment['view_level']<=$_SESSION['GameRank']) {
						$pageArray['comments']=$pageArray['comments'].buildUserComment($comment);
					}
				}
				if($_SESSION['GameRank']>=2) {
					$pageArray['comment_Form']=$page->loadPage('user_comment_form',PAGE);
				}
				
				$page->page=$page->loadPage('user_view',PAGE);
				$page->replace_tags($pageArray,$page->page);
				$page->replace_tags($result,$page->page);
				$page->outputPage();
				
			} else { error('User does not exist.'); }
		} else {
			error('No user found.');
		}
		break;
	default:
		error('The page has encountered an error.  You\'re probably messing with the url.');
}
?>