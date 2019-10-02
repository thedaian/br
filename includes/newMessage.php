<form action="messages.php" name="messageForm" onsubmit="return false" method="post">
<input type="hidden" name="action" value="send"/>
<input type="hidden" name="which" value="<?php echo $_GET['which']; ?>"/>
To: <input type="text" name="to" value="<?php if(isset($_GET['who'])) { echo $_GET['who']; } else { echo '0'; } ?>" size="6" />
<select name="contacts" onchange="selectContact()"><option value="0">Game Operator</option>
<?php
if(!isset($PAGE)) { //if the user isn't coming in from the main page, then load normal variables
	if(!isset($_SESSION['loggedIn'])) {
		$SESSION_NAME="battle";
		session_name($SESSION_NAME);
		session_start();
	}
	$user=$_SESSION['UserID'];
	require_once 'connect.inc';
	$db=do_connect();
}
//gets the friends list, if there is one
$sql="SELECT id, name FROM characters WHERE game_id = ?";// AND user_id<>'$user'";
$query=query($db,$sql,$_GET['which']);
$total=how_many($query);

if($total>0) {
	$char=next_row($query);
	for($i=0;$i<$total;$i++) {
		echo '<option value="'.$char['id'].'">'.$char['name'].'</option>';
		$char=next_row($query);
	}
}

$sql="SELECT id FROM characters WHERE game_id=? AND user_id='$user'";
$char=get_row($db,$sql,0,$which);
if(empty($char)) {
	$from=0;
} else { $from=$char['id']; }
?>
</select><br/>
<textarea name="msgText" cols="30" rows="6"><?php if(isset($msgText)) { echo $msgText; } ?></textarea>
<br/><br/>
<input type="hidden" name="from" value="<?php echo $from; ?>"/>
<input type="submit" onclick="sendMsg()" value="Send Message" />
</form>
<a class="small" href="javascript:void(0)" onclick="closeMessage();">Don't send message</a>