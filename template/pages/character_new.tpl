<h4>Create New Character</h4>
{ERROR}
<form action="{URL}character/create" method="post">
<label for="name">Character Name: </label>
<input type="text" name="name" id="name" size="23" maxlength="100"/><br/><br/>
<label for="description">Description</label><br/>
<textarea id="description" name="desc" cols="35" rows="4"></textarea><br/><br/>
Gender: <select name="gender"><option value="0">Male</option><option value="1">Female</option></select><br/><br/>
<input type="submit" value="Create Character"/>
</form>