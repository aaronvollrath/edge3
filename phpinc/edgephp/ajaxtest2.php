<script type="text/javascript">
<!--
	var http = createRequestObject();

	function createRequestObject() {
		var xmlhttp;
		try { xmlhttp=new ActiveXObject("Msxml2.XMLHTTP"); }
	  catch(e) {
	    try { xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");}
	    catch(f) { xmlhttp=null; }
	  }
	  if(!xmlhttp&&typeof XMLHttpRequest!="undefined") {
	  	xmlhttp=new XMLHttpRequest();
	  }
		return  xmlhttp;
	}
 // -->
</script>



<script type="text/javascript">
<!--
	function sendRequestTextPost() {
	var rnd = Math.random();
	var myvalue1 = escape(document.getElementById("myvalue1").value);
	var myvalue2 = escape(document.getElementById("myvalue2").value);
	try{
    http.open('POST',  'serverscript.php');
    http.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    http.onreadystatechange = handleResponseText;
		http.send('myvalue1='+myvalue1+'&myvalue2='+myvalue2+'&rnd='+rnd);
	}
	catch(e){}
	finally{}
}
 // -->
</script>



<script type="text/javascript">
<!--
	function sendRequestTextGet() {
	var rnd = Math.random();
	var myvalue1 = escape(document.getElementById("myvalue1").value);
	var myvalue2 = escape(document.getElementById("myvalue2").value);
	try{
    http.open('GET',  'serverscript.php?myvalue1='+myvalue1+'&myvalue2='+myvalue2+'&rnd='+rnd);
    http.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    http.onreadystatechange = handleResponseText;
		http.send(null);
	}
	catch(e){}
	finally{}
}
 // -->
</script>



<script type="text/javascript">
<!--
	function sendRequestXmlPost() {
	var rnd = Math.random();
	var myvalue1 = escape(document.getElementById("myvalue1").value);
	var myvalue2 = escape(document.getElementById("myvalue2").value);
	try{
    http.open('POST',  'serverscript.php');
    http.setRequestHeader('Content-Type', "text/xml");
    http.onreadystatechange = handleResponseXml;
		http.send('myvalue1='+myvalue1+'&myvalue2='+myvalue2+'&rnd='+rnd);
	}
	catch(e){}
	finally{}
}
 // -->
</script>


<script type="text/javascript">
<!--
	function sendRequestXmlGet() {
	var rnd = Math.random();
	var myvalue1 = escape(document.getElementById("myvalue1").value);
	var myvalue2 = escape(document.getElementById("myvalue2").value);
	try{
    http.open('GET',  'serverscript.php?myvalue1='+myvalue1+'&myvalue2='+myvalue2+'&rnd='+rnd);
    http.setRequestHeader('Content-Type', "text/xml");
    http.onreadystatechange = handleResponseXml;
		http.send(null);
	}
	catch(e){}
	finally{}
}
 // -->
</script>


<script type="text/javascript">
<!--
function handleResponseText() {
	try{
    if((http.readyState == 4)&& (http.status == 200)){
    	var response = http.responseText;
      document.getElementById("idforresults").innerHTML = response;
		}
  }
	catch(e){alert("hello");}
	finally{}
}
 // -->
</script>



<script type="text/javascript">
<!--
function handleResponseXML() {
	try{
    if((http.readyState == 4)&& (http.status == 200)){
    	var response = http.responseXML.documentElement;
    	var response_value = response.getElementsByTagName('tagname')[0].firstChild.nodeValue;
    	document.getElementById("idforresults").innerHTML = response_value;
		}
  }
	catch(e){}
	finally{}
}
 // -->
</script>


<script type="text/javascript">
<!--
function keyUp() {
	window.setTimeout("sendRequest()", 400);
}
// -->
</script>



<!-- BASIC WITH BUTTON PRESS -->
<form>
<label>My Value 1: <input type="text" name="myvalue1" id="myvalue1"></label><br />
<label>My Value 2: <input type="text" name="myvalue2" id="myvalue2"></label><br />
<input type="button" name="call" value="submit" onClick="sendRequestTextPost()">



<!-- BASIC WITH ON KEY PRESS -->
<!-- SHOULD PROBABLY ONLY BE USED WHEN ONE INPUT IS SUPPLIED -->
<label>My Value 1: <input type="text" id="myvalue" onkeyup="keyUp()"></label><br />
</form>



<span id="idforresults"></span>