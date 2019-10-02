<h4>Create a game</h4>
{ERROR}
<form action="{URL}game/create" method="post">
<table id="create">
<tr><td class="half">
<label for="name">Game Name</label><br/>
<input type="text" name="name" id="name" size="30" maxlength="20" value="{NAME}"/><br/><br/>
<label for="description">Description</label><br/>
<textarea id="description" name="desc" cols="27" rows="4">{DESC}</textarea><br/>
</td>
<td valign="top" class="half">
<label for="max" class="option">Max Players</label><input type="text" name="max" id="max" size="5" maxlength="2"/><br/><br/>
<label for="private" class="option">Private Game </label><input type="checkbox" name="private" id="private"/><br/>
<label for="onlyusers" class="option">Allow Only Power Users </label><input type="checkbox" name="onlyusers" id="onlyusers"/><br/>
<label for="males" class="option">Males Only </label><input type="checkbox" name="males" id="males"/><br/>
<label for="females" class="option">Females Only </label><input type="checkbox" name="females" id="females"/>
</td></tr>
</table>
<br/>
<input type="submit" value="Create Game"/>
</form>