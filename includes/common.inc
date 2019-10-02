<?php
$SESSION_NAME="battle";
session_name($SESSION_NAME);
session_start();

require_once 'connect.inc';
$db=do_connect();

if(!isset($_SESSION['loggedIn'])) {
	$user=0;
	$_SESSION['UserID']=0;
	$_SESSION['GameRank']=0;
	$_SESSION['loggedIn']=1;
	$sql="INSERT INTO sessions (`sessionID`, `user_ID`, `session_begun`, `end`, `action`, `user_IP`) VALUES('".session_id()."','0',
								'".time()."','".(time()+3600)."', '$PAGE',INET_ATON('".$_SERVER['REMOTE_ADDR']."'))";
	query($db, $sql);
} else {
	$user=$_SESSION['UserID'];
	$sql="UPDATE sessions SET action='$PAGE', end='".(time()+3600)."' WHERE user_ID='$user' AND sessionID='".session_id()."'";
	query($db,$sql);
}

//if the user rank is less than the permissions of the page, don't let them in
//ranks: 0=guest, 1=user, 2=power user, 3=moderator, 4=administrator
if($_SESSION['GameRank']<$PERMISSIONS) {
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html><head><title>Battle Royale Online: Access Denied</title></head>
<body>
<h1>You cannot access this page.</h1>
</body></html>
<?php
  exit();
}
include 'config.php';

function sanitize($unSafe)
{
	$safe=strip_tags($unSafe);
	$safe=stripslashes($safe);
	$safe=htmlspecialchars($safe, ENT_NOQUOTES);
	return $safe;
}
?>