<?php
$PERMISSIONS=1;
$PAGE="Messages";
include 'includes/common.inc';
include 'includes/builds.inc';
include 'includes/headers.inc';

if(isset($_REQUEST['action'])) {
  $action=$_REQUEST['action'];
} else { $action=''; }
if(isset($_REQUEST['which'])) {
  $which=$_REQUEST['which'];
} else { $which=0; }

//set the pageArray msgText to zero so that it exists when the template looks for it
$pageArray['msgText']='';

switch($action) {
	case "send":
		$to=sanitize($_POST['to']);
		$from=sanitize($_POST['from']);
		$pageArray['msgText']=sanitize($_POST['msgText']);

		$sql="SELECT id FROM characters WHERE id=?";
		$query=query($db,$sql,$to);
		$total=how_many($query);

		if(($total>0)||($to==0)) { //if the user is trying to send a message to no one, toss an error
			$now=time();
			$sql="INSERT INTO messages (sender_id, receiver_id, game_id, type, time_sent, msg_text) VALUES(?,?,?,'0','$now',?)";
			query($db,$sql,$from,$to,$which,$pageArray['msgText']);
			$sql="INSERT INTO messages (sender_id, receiver_id, game_id, type, time_sent, msg_text) VALUES(?,?,?,'1','$now',?)";
			query($db,$sql,$from,$to,$which,$pageArray['msgText']);

			$pageArray['msg']='Message Sent.';
		} else {
			$pageArray['msg']='User not found in the database.';
			$which=0;
			$action="new";
		}
		$type=1;
		break;
	case "delete":
		$type=$_GET['type'];
		$sql="DELETE FROM messages WHERE id=?";
		query($db,$sql,$_GET['id']);
		$pageArray['msg']='Message deleted.';
		break;
	default: //if there's no action variable being passed into the page, do normal stuff
		//checks to see if the user is accessing the page from the front page or the ajax script
		$pageArray['msg']='Send and recieve messages.';
		$pageArray['which']=$which;
		$type=1; //used for sorting the message list.  2 is inbox, 1 is outbox, and 0 is saved messages
}

$pageArray['who']=(isset($_GET['who'])) ? $_GET['who'] : '0';
$pageArray['friends']='';

//gets the friends list, if there is one
$sql="SELECT id, name FROM characters WHERE game_id = ?";// AND user_id<>'$user'";
$query=query($db,$sql,$which);
$total=how_many($query);

//loop through the array
if($total>0) {
	$char=next_row($query);
	for($i=0;$i<$total;$i++) {
		$pageArray['friends'].='<option value="'.$char['id'].'">'.$char['name'].'</option>';
		$char=next_row($query);
	}
}

//finds the id of the character in this game, used so the code knows where the message came from
//note: code only works correctly when the player CANNOT be in their own games
$sql="SELECT id FROM characters WHERE game_id=? AND user_id='$user'";
$char=get_row($db,$sql,0,$which);

$pageArray['from']=(empty($char)) ? '0' : $char['id'];

//resets the table, so that it's empty for the resulting code
$page->table='';

//file to generate and output the message list
//is also used for AJAX stuff, so effectivly has two entry points
include 'includes/msgList.php';
//function to actually generate the message list, accepts the type of message list we're looking at
msgList($type);
//replaces the table, which is actually the list
$page->replace_table($pageArray['msg_list']);
//output the page, and other stuff
$page->page=$page->loadPage('messages',PAGE);
$page->replace_tags($pageArray,$page->page);
$page->outputPage();
?>