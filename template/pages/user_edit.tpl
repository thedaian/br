<h4>Editing Profile</h4>
<form action="user/edit" method="post">
<label for="email">Change Email: </label>
<input type="text" class="box" name="email" size="20" id="email" value="{EMAIL}" maxlength="100"/><br/><br/>
<label for="pass">Change Password: </label><input type="password" class="box" name="password" size="15" id="pass"/><br/>
<label for="pass2">Retype Password: </label><input type="password" class="box" name="password2" size="15" id="pass2"/><br/>
<input type="submit" value="Edit Profile"/>
</form>
<a class="small" href="{URL}user/view/{USERID}">View Profile</a>