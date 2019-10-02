<h4>Manage Game</h4>
<table class="full">
<tr><td class="half">
Name: {NAME}<br/><br/>
{EDIT_MSG}
<form action="{URL}manage/edited/{WHICH}" method="post">
<label for="description">Description</label><br/>
<textarea id="description" name="desc" cols="35" rows="4">{DESCRIPTION}</textarea><br/><br/>
<input type="submit" value="Change Description"/>
</form><br/>
There are {applied} characters who have applied to the game. 
{APPLIED_LINK}
<br/><br/>
There are {total} characters currently in the game. 
{LIST_LINK}
</td><td class="half">
{MAP_FORM}
<form action="{URL}manage/delete/{WHICH}" method="post">
<span class="small">Confirm Game Deletion: </span><input type="checkbox" name="confirm"/><br/>
<input type="submit" value="Delete Game"/>
</form>
<br/>
{START_MSG}
</td></tr></table>