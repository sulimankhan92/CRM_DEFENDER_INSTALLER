/**
 * Check for every line of lines array if line[i] is a valid ip address
 * @param {array} lines 
 * @return {string} errorString: Error Message. Return an empty string if everything is ok
 */
function verifyIP (lines) {
	var errorString = "";
	var theName = "IP Address";
	for(var i = 0;i < lines.length;i++){
		var IPvalue = lines[i];
		if (IPvalue != null && IPvalue!=""){
			var regexTest = /^(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/.test(IPvalue);
			if (!regexTest)
				errorString += theName + ': '+IPvalue+' is not a valid IP address.\n';
			   } //if IPvalue != null && IPvalue!=""
			} //for
	return errorString;
} //function verifyIP

/**
 * Check if the value is integer
 * @param {} value 
 * @return {string} Error Message. Return an empty string if everything is ok
 */
function isInt(value) {
	var errorString = "";
	var theName = "Max allowed attempts";
	if (value == null || value==""){
		return "";
	} //if null
	if ( !isNaN(value) && 
		 parseInt(Number(value)) == value && 
		 !isNaN(parseInt(value, 10))){
		 if (value>100)
			errorString += theName + ': '+value+' is too high.\n';
		return errorString;
	} //if NaN
	else
		errorString += theName + ': '+value+' is not a valid input.\n';
	return errorString;	
}

/**
 * Check if the value is correct email
 * @param {} value 
 * @return {string} Error Message. Return an empty string if everything is ok
 */
function verifyAddr(recipientAddress) {
	var errorString = "";
	var theName = "Recipient Email Address";
	if (recipientAddress == null || recipientAddress=="")
		return "";
	var regexTest = /^[a-zA-Z0-9.!#$%&’*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/.test(recipientAddress);
	if (!regexTest) {  
		errorString += theName + ': '+recipientAddress+' is not a valid email.\n';
		return errorString;
	}//if
	else{
		
		return "";
	}	
}

/**
 * Triggered by the submit event, catch values needed and launches verifyIP and isInt functions
 * @return {string} Error Message. Return an empty string if everything is ok
 */
function verify(){
	var message = ""; 
	var lines = document.getElementById('whiteList').value.split('\n');
	var maxFailedAccesses = document.getElementById('maxFailedAccesses').value;
	var recipientAddress = document.getElementById('recipientAddress').value;
	message+= verifyIP (lines);
	message+= isInt (maxFailedAccesses);
	message+= verifyAddr(recipientAddress);
	return message;
} //verify

$(document).ready(function() {
	$("#EditView").submit(function(event){
	if (document.getElementById('cancelbutton').value!=1) {
		var $result = verify();
		if($result!="") { 
			alert($result);
			event.preventDefault();  
		} //if 
	} //if
  }); //EditView submit

	$("#searchLocalNetIP").click(function() {
		getUserIP(function(ip) {
			var whiteListField = document.getElementById('whiteList');
			var currentWhiteListedIps = whiteListField.value.split("\n");

			if (!currentWhiteListedIps.includes(ip)) {
				if (whiteListField.value.trim() === "") {
					whiteListField.value = ip;
				} else {
					whiteListField.value += "\n" + ip;
				}
			}else{
				alert("Ip address is already exist in this whiteList");
			}
		}, function(error) {
		// alert("Error: " + error);
			showWebRTCSettingsPopup();
		});
	});
	
	$.getJSON("https://jsonip.com/?callback=?", function (data) {
		window.globalPublicIP = data.ip;
	}); //getJSON

	$("#searchPublicNetIP").click(function(){
		var resultIP = document.getElementById('globalIP').value;
		var whiteListField = document.getElementById('whiteList');
		var currentWhiteListedIps = whiteListField.value.split("\n");

		if (!currentWhiteListedIps.includes(resultIP)) {
		if (whiteListField.value.trim() === "") {
			whiteListField.value = resultIP; // If the field is empty, just add the IP
		} else {
			whiteListField.value += "\n" + resultIP; // Append IP on a new line
		}
		} else {
		alert("IP address already exists in the whitelist.");
		}
	}); //searchPublicNetIP
  
}); //document.ready function


function getUserIP(onNewIP, onError) { // onNewIP - your listener function for new IPs, onError - error handler
    var myPeerConnection = window.RTCPeerConnection || window.mozRTCPeerConnection || window.webkitRTCPeerConnection;

    if (!myPeerConnection) {
    onError('Your browser does not support WebRTC');
    return;
  }

    var pc = new myPeerConnection({
      iceServers: []
    }),
    noop = function() {},
    localIPs = {},
    ipRegex = /([0-9]{1,3}(\.[0-9]{1,3}){3}|[a-f0-9]{1,4}(:[a-f0-9]{1,4}){7})/g,
    key;

    function iterateIP(ip) {
    if (!localIPs[ip]) onNewIP(ip);
    localIPs[ip] = true;
  }

    // Create a bogus data channel
    pc.createDataChannel("");

    // Create offer and set local description
    pc.createOffer().then(function(sdp) {
    sdp.sdp.split('\n').forEach(function(line) {
      if (line.indexOf('candidate') < 0) return;
      line.match(ipRegex).forEach(iterateIP);
    });

    pc.setLocalDescription(sdp, noop, noop);
  }).catch(function(reason) {
    // Error creating offer
    onError('Failed to create WebRTC offer: ' + reason.message);
  });

    // Listen for candidate events
    pc.onicecandidate = function(ice) {
    if (!ice || !ice.candidate || !ice.candidate.candidate || !ice.candidate.candidate.match(ipRegex)) return;
    ice.candidate.candidate.match(ipRegex).forEach(iterateIP);
  };

    // Set a timeout to detect if no IP was found within 2 seconds
    setTimeout(function() {
    if (Object.keys(localIPs).length === 0) {
      onError('Failed to retrieve IP address. Please check your browser settings or extension (e.g., "Anonymize local IPs exposed by WebRTC").');
    }
  }, 200); // Adjust timeout duration as necessary
}

function showWebRTCSettingsPopup() {
  // Create the background overlay and modal container
  var modalBackground = document.createElement('div');
  modalBackground.classList.add('modal-background');

  var modalContent = document.createElement('div');
  modalContent.classList.add('modal-content');
  modalContent.innerHTML = `
      <h2>Enable WebRTC Setting</h2>
      <p>To use this feature, you need to enable the 'Anonymize local IPs exposed by WebRTC' flag in your browser settings:</p>
      <ul>
        <li style="padding-top: 9px;">
          For Chrome: <a href="#" onclick="return false;">chrome://flags/#enable-webrtc-hide-local-ips-with-mdns</a>
          <button onclick="copyToClipboard('chrome://flags/#enable-webrtc-hide-local-ips-with-mdns')">Copy</button>
        </li>
        <li style="padding-top: 9px;">
          For Edge: <a href="#" onclick="return false;">edge://flags/#enable-webrtc-hide-local-ips-with-mdns</a>
          <button onclick="copyToClipboard('edge://flags/#enable-webrtc-hide-local-ips-with-mdns')">Copy</button>
        </li>
        <li style="padding-top: 9px;">
          For Firefox: Go to the appropriate settings manually.
        </li>
      </ul>
      <button class="button" id="closePopup">Close</button>
    `;

  // Append the modal and background to the body
  document.body.appendChild(modalBackground);
  document.body.appendChild(modalContent);
  document.body.classList.add('modal-open'); // Prevent scrolling

  // Close the modal when clicking the close button
  document.getElementById('closePopup').addEventListener('click', function() {
    closeModal();
  });

  // Close the modal when clicking outside of the content
  modalBackground.addEventListener('click', function(e) {
    if (e.target === modalBackground) {
      closeModal();
    }
  });

  // Close the modal function
  function closeModal() {
    document.body.removeChild(modalBackground);
    document.body.removeChild(modalContent);
    document.body.classList.remove('modal-open'); // Restore body scrolling
  }
}

// Function to copy the text to clipboard
function copyToClipboard(text) {
  var tempInput = document.createElement('input');
  tempInput.style.position = 'absolute';
  tempInput.style.left = '-9999px';
  tempInput.value = text;
  document.body.appendChild(tempInput);
  tempInput.select();
  document.execCommand('copy');
  document.body.removeChild(tempInput);
  alert('Copied to clipboard: ' + text);
}
