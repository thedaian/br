<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<?php
$SESSION_NAME="battle";
session_name($SESSION_NAME);
session_start();

if(!isset($_SESSION['loggedIn'])) {
  echo '<title>Error</title><body><h1>You are not logged in.  This page is not accessible.</h1></body></html>';
	exit();
}

$mode=$_GET['mode'];
$PAGE="Admin Panel, mode: " . $mode;
$PERMISSIONS=4;

if($_SESSION['GameRank']<$PERMISSIONS) {
  echo '<title>No Admittance</title></head><body><h1>You do not have the ability to access this page.</h1></body></html>';
  exit();
}
$user=$_SESSION['UserID'];

require_once("includes/connect.inc");
$db=do_connect();

$time=time()+3600;

$sql="UPDATE sessions SET action='$PAGE', end='$time' WHERE user_ID=$user";
query($db,$sql);
?>
<title>Battle Royale Admin Panel</title>
<link rel="stylesheet" type="text/css" href="admin/admin.css" />
</head>
<body>
<h2>Administration Panel</h2>
<table id="menu">
<tr><td><a href="admin.php?mode=view">View Accounts</a></td>
<td><a href="admin.php?mode=sessions">View Sessions</a></td>
<td><a href="admin.php?mode=settings">Change Settings</a></td>
<td><a href="admin.php?mode=kickass">Do Kickass Stuff</a></td></tr>
<tr><td><a href="admin.php?mode=chars">View Characters</a></td>
<td><a href="admin.php?mode=games&action=view">Active Games</a></td>
<td><a href="admin.php?mode=weapons&action=view">Weapon List</a></td>
<td><a href="admin.php?mode=logs">Logs</a></td></tr>
</table><br><hr><br>
<?php
switch($mode) {
  case 'view':
    require 'admin/view.php';
    break;
	case 'chars':
    require 'admin/viewchars.php';
    break;
  case 'edit':
    require 'admin/editchar.php';
    break;
  case 'weapons':
    require 'admin/weapons.php';
    break;
	case 'games':
    require 'admin/games.php';
    break;
  case 'settings':
    require 'admin/settings.php';
    break;
  case 'sessions':
    require 'admin/sessions.php';
    break;
  case 'kickass':
    require 'admin/kickass.php';
    break;
  case 'news':
    require 'admin/editnews.php';
    break;
	case 'logs':
    require 'admin/viewlogs.php';
    break;
  case 'delete':
    if($confirm=='Yes') {
      $sql="DELETE FROM users WHERE user_id=$which";
      query($db,$sql);
      $sql="DELETE FROM characters WHERE user_id=$which";
      query($db,$sql);
      echo 'Character deleted';
    } else { echo 'Deletion Not possible right now.'; }
   break;
  case 'noDelete':
		if($confirm=='Yes') {
			$sql="UPDATE users SET DoNotDelete=1 WHERE id='$which'";
			query($db,$sql);
			echo 'User will NOT be deleted during Maintence';
		} else { echo 'Stuff'; }
   break;
  case 'reset':
  if($confirm=='Yes') {
   $sql="UPDATE users SET gamesIN=0, gamesRUN=0, gamesTOTAL=0, gamesSURVIVED=0, gamesDIED=0
        WHERE id='$which'";
   query($db, $sql);
   echo 'User was reset.  Stupid user.';
  } else { echo 'User reset unsuccessful.  Use the checkbox next time, dumbass'; }
  break;
}
unconnect($db);
?>
</body>
</html>