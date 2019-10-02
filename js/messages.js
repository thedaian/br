//ajax object creation function
function createRequestObject() {
   var req;
   try {
				req=new ActiveXObject("Msxml2.XMLHTTP");
			} catch (e) {
				try {
					req=new ActiveXObject("Microsoft.XMLHTTP");
				} catch (oc) {
					req=null;
				}
			}
			if(!req && typeof XMLHttpRequest != "undefined")
				req = new XMLHttpRequest();
			if (!req)
				alert("Could not create connection object.");
   return req;
}

var midFrameRequest = createRequestObject();

/*
Functions for changing various screens
Used by the market, messages, and probably others
*/
function loadMidFrame(URL,div){
  if (window.ActiveXObject) {
		midFrameRequest = createRequestObject();
	}
  midFrameRequest.onreadystatechange = function() {
	  if (midFrameRequest.readyState == 4){
	    changeMidFrame(div);
		}
	}
  midFrameRequest.open("GET", url+URL, true);
  midFrameRequest.send(null);
}

function changeMidFrame(div){
    if (midFrameRequest.readyState == 4){
        statusDiv = document.getElementById(div);
        if (midFrameRequest.status == 200){
            statusDiv.innerHTML = midFrameRequest.responseText;
        } else {
            statusDiv.innerHTML = "Error: Status "+itemRequest.status;
        }
    }
}

function toggleMessage() {
	var state=document.getElementById('newMessage').style.display;
	if(state=="none") {
		document.getElementById('newMessage').style.display = "block";
		document.getElementById('msgAction').innerHTML= "Close Message Box";
	} else {
		document.getElementById('newMessage').style.display = "none";
		document.getElementById('msgAction').innerHTML= "Open Message Box";
	}
}

function selectMessage(type,which) {
	loadMidFrame("includes/msgList.php?which="+which+"&type="+type,"msgList");
}

function sendMsg(id) {
	var msgText=document.messageForm.msgText.value;
	if((msgText=="")||(msgText==" ")) {
		document.getElementById('message').innerHTML = "The message must contain text.";
		document.messageForm.msgText.focus();
		return false;
	}
	document.messageForm.submit();
}

function selectContact() {
	document.messageForm.to.value = document.messageForm.contacts.value;
}