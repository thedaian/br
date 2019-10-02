<?
/*
user.inc

function checkPassword($input, $salt, $password)
	$input- plaintext password value
	$salt- salt, from database
	$password- encrypted password from database
	
	returns true on a successful password, otherwise, returns false

function login($username, $password)
	$username- user supplied username
	$password- user supplied password
	
	returns any error messages, otherwise, returns an empty string


*/

function checkPassword($input, $salt, $password)
{
	$encode=crypt($input,$salt);
	if(substr($encode,12)==$password) {
		return true;
	} else {
		return false;
	}
}

function login($username, $password)
{
	global $db, $user;
	//make sure there's no empty fields
	if(empty($username)||empty($password)) {
		return "Please enter a username and password";
	}
	//get some basic info from the database
	$sql="SELECT user_id, salt, password, user_level FROM users WHERE username=?";
	$info=get_row($db,$sql, 0, $username);
	//if the result returned no rows, then error out, otherwise
	if(isset($info['user_id'])) {
		//check the password
		if(checkPassword($password,$info['salt'],$info['password'])) {
			//sets the user variable to the user ID, and stores it in a session  also stores the user rank
			$user=$info['user_id'];
			$_SESSION['UserID']=$user;
			$_SESSION['GameRank']=$info['user_level'];
			//delete the old user session
			query($db,"DELETE FROM sessions WHERE user_id='$user' OR sessionID='".session_id()."'");
				
			$sql="INSERT INTO sessions (`sessionID`, `user_ID`, `session_begun`, `end`, `action`, `user_IP`) VALUES('".session_id()."','$user',
						'".time()."','".(time()+3600)."', 'Logging In',INET_ATON('".$_SERVER['REMOTE_ADDR']."'))";
			query($db,$sql);
			$sql="UPDATE users SET last_login='".time()."' WHERE user_id='$user'";
			query($db,$sql);
			return "";
		} else {
			return "Incorrect Password";
		}
	} else {
		return "User doesn't exist";
	}
}
?>