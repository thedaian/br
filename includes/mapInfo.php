<?
$SESSION_NAME="battle";
session_name($SESSION_NAME);
session_start();

include 'connect.inc';

$db=do_connect();

$which=$_POST['which'];

define('IMPASSIBLE',1);
define('DANGER_ZONE',2);
define('NEW_DANGER_ZONE',4);
define('START_LOCATION',8);

$sql="SELECT maps.*, games.owner_id FROM maps JOIN games ON maps.game_id=games.id WHERE maps.id=?";
$map=get_row($db,$sql,0,$which);
?>
<h4>Information about the map</h4>
Map ID: <?php echo $map['id']; ?><br/>
Position: <?php echo $map['pos_x']; echo ', '.$map['pos_y']; ?><br/>
Characters Here: <?php echo $map['players']; ?><br/>
<?
if($map['owner_id']==$_SESSION['UserID']) {
?>
<br/>
<form action="map.php" method="post">
<input type="hidden" name="action" value="edit"/>
<input type="hidden" name="which" value="<?php echo $map['game_id']; ?>"/>
<input type="hidden" name="map_id" value="<?php echo $map['id']; ?>"/>
Danger Zone<br/>
(next round): <input type="checkbox" name="danger" <? if($map['options']&NEW_DANGER_ZONE) { echo 'checked="checked"'; } ?>/><br/>
Impassible: <input type="checkbox" name="impass"<? if($map['options']&IMPASSIBLE) { echo 'checked="checked"'; } ?>/><br/>
Start Location: <input type="checkbox" name="start"<? if($map['options']&START_LOCATION) { echo 'checked="checked"'; } ?>/><br/>
<input type="submit" value="Edit Square"/>
</form>
<?
}
unconnect($db);
?>