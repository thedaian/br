<?php
$PERMISSIONS=0;
$PAGE="Homepage";
include 'includes/common.inc';
include 'includes/user.inc';

//login and logout code
if(isset($_REQUEST['action'])) {
	switch($_REQUEST['action']) {
		case 'login':
			$error=login($_POST['name'],$_POST['password']);
			//if the login was successful, then output a redirect page
			if(empty($error)) {
				include 'template/login_success.tpl';
				die();
			}
			break;
		case 'logout':
			$user=0;
			$_SESSION['UserID']=0;
			$_SESSION['GameRank']=0;
			$_SESSION['gameID']=0;
			$sql="UPDATE sessions SET action='Logged Out', end='".(time()+3600)."', user_id='0' WHERE sessionID='".session_id()."'";
			query($db,$sql);
			break;
	}
}

include 'includes/builds.inc';
include 'includes/headers.inc';

//if the user isn't logged in, display the login form
if($user==0) {
	$pageArray['form']=$page->loadPage('form_login',PAGE);
	if(!empty($error)) {//checks to see if there was an error with the login or not
		$pageArray['error']='Error Logging In:<br/>'.$error.'.<br/>';
	} else { $pageArray['error']=''; }
	$page->replace_tags($pageArray,$pageArray['form']); //replaces the tags for the form value
} else { $pageArray['form']=''; } //otherwise, set the form value to null, so it doesn't show up

$pageArray['news']="Please note: This site is not yet operational. Characters may be purged from the database at random times.";

$page->page=$page->loadPage('index',PAGE);
$page->replace_tags($pageArray,$page->page);

$page->outputPage();
?>