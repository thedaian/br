<h4>Edit Character: {NAME}</h4>
<form action="{URL}character/edited/{WHICH}" method="post">
<label for="name">Character Name: </label>
<input type="text" name="name" id="name" size="23" maxlength="100" value="{NAME}"/><br/><br/>
<label for="description">Description</label><br/>
<textarea id="description" name="desc" cols="35" rows="4">{description}</textarea><br/><br/>
Gender: <select name="gender">
<option value="0"{GENDER0}>Male</option>
<option value="1"{GENDER1}>Female</option>
</select><br/><br/>
<input type="submit" value="Edit Character"/>
</form>
<br/>
<form action="{URL}character/delete/{WHICH}" method="post">
<span class="small">Confirm Deletion: </span><input type="checkbox" name="confirm"/><br/>
<input type="submit" value="Delete Character"/>