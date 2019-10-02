<?php
if(isset($_REQUEST['action'])) {
  $action=$_REQUEST['action']; //registar globals is OFF, gets action
} else { $action=""; }

switch($action) {
 case "ON":
   $sql="UPDATE settings SET options=0 WHERE id=1";
   query($db, $sql);
   echo "Maintenance mode turned off.";
   break;
 case "OFF":
   $sql="UPDATE settings SET options=1 WHERE id=1";
   query($db, $sql);
   echo "Maintenance mode turned on.";
   break;
 case "change":
   $max_games=$_POST['max_games'];
   $max_chars=$_POST['max_chars'];
   $sql = "UPDATE settings SET max_games='$max_games', max_chars='$max_chars' WHERE id=1";
   query($db,$sql);
   echo "Settings Changed Successfully.  I hope.<br/><br/>";
 default:
   $sql="SELECT * FROM settings WHERE id=1";
   $result=get_row($db, $sql);

   if($result['options']&1) { $maint="ON"; $toggle="Turn OFF"; } else { $maint="OFF"; $toggle="Turn ON"; }
   ?>
   <form action="admin.php?mode=settings&action=change" method="POST">
   <table>
   <tr><td>Maximum Amount of Games for a single user: </td>
   <td><input type="text" name="max_games" value="<?php echo $result['max_games']; ?>"/></td></tr>

   <tr><td>Maximum Amount of Characters for a single user: </td>
   <td><input type="text" name="max_chars" value="<?php echo $result['max_chars']; ?>"/></td></tr>

   <tr><td colspan="2"><input class="button" type="submit" value="Change Settings"/></form></td></tr>

   <form action="admin.php?mode=settings&action=<?php echo $maint; ?>" method="POST">
   <tr><td>Maintenance mode is currently turned <?php echo $maint; ?></td></tr>
   <tr><td><input class="button" type="submit" value="<?php echo $toggle; ?>"/></td></tr>
   </table>
 <?php
}