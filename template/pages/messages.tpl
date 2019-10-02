<h4>Your messages</h4>
<span id="message">{MSG}</span><br/>
<a class="small" href="javascript:void(0)" onclick="toggleMessage();" id="msgAction">Close Message Box</a><br/>
<div id="newMessage">
<form action="{URL}messages/send/{WHICH}" name="messageForm" onsubmit="return false" method="post">
<input type="hidden" name="from" value="{FROM}"/>
To: <input type="text" name="to" value="{WHO}" size="6" />
<select name="contacts" onchange="selectContact()"><option value="0">Game Operator</option>
{FRIENDS}
</select><br/>
<textarea name="msgText" cols="30" rows="6">{MSGTEXT}</textarea>
<br/><br/>
<input type="submit" onclick="sendMsg()" value="Send Message" />
</form>
</div><br/>
<div id="msgList">{MSG_LIST}</div>