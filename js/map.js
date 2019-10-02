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
//declaring variables
var mainRequest = createRequestObject();
var returned;

/*
Functions for changing the main screen
*/
function showInfo(){
  var response;
  contentDiv = document.getElementById('map_info');
  if (mainRequest.status == 200){
    try {
			contentDiv.innerHTML = mainRequest.responseText;
    }
		catch (e) {
			// IE fails unless we wrap the string in another element.
			contentDiv.innerHTML = response;
			document.appendChild(contentDiv);
		}
  } else {
		contentDiv.innerHTML = "Error: Status "+mainRequest.status;
  }
}

function saveThemPOST(postData){
  if (window.ActiveXObject) {
		mainRequest = createRequestObject();
	}
  mainRequest.onreadystatechange = function() {
	  if (mainRequest.readyState == 4){
	    showInfo();
		}
	}
  mainRequest.open("POST", url+"includes/mapInfo.php", true);
  mainRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
  mainRequest.setRequestHeader('Content-Length', postData.length);
  mainRequest.send(postData);
}

function info(id) {
	postData="which="+id;
	saveThemPOST(postData);
}

//Used to prevent forms on the AJAX frames from refreshing the page
function fake() {
	return false;
}