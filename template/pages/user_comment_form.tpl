<br/>
<form action="{URL}user/addComment/{WHICH}" method="post">
<label for="comment">Comment</label><br/>
<textarea id="comment" name="comment" cols="35" rows="4"></textarea><br/>
<select name="view_level">
<option value="1">All Users</option>
<option value="2">Power Users Only</option>
<option value="3">Game Moderators</option>
<option value="4">Administrators</option>
</select><br/><br/>
<input type="submit" value="Add Comment"/>
</form>