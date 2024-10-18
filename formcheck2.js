
 function t(num,l,r)
         {

	   //var load = window.open("./sample.php?sampleid="+num+"&amp;orderby=hybrids.finalratio&amp;sort=asc&amp;lcomp=lte&amp;lval="+l+"&amp;rcomp=gte&amp;rval="+r);
         }
    function ci(cloneid, arrayid){

	var load = window.open("./cloneinfosvg.php?cloneid=" +
                        cloneid + "_" + arrayid );
	 }

		function g(id){
		var load = window.open("./cloneinfosvgname.php?cloneid=" +id);

		}

function isNumber(num1) {
	//alert("in isNumber!!!");

 if ((num1 / 2 >= 0)||(num1 / 2 < 0))
   return true 
  else 
   return false 
 }

function checkSampleForm(){
	var lval = document.query.lval.value
	var rval = document.query.rval.value
	//alert("in checkSampleForm");
	//alert("lval is: " + lval);
	if(isBlank(lval) || isBlank(rval)){
		alert("Please make sure that you've entered a value for the Minimal Induction and Minimal Repression");
		return false;
	}
	if(isNumber(lval) && isNumber(rval)){
		//alert("is a number...");
		if(lval > -1){
			alert("Please enter a negative value (<= -1) for Minimum Repression!");
			return false;
		}
		if(rval < 1){
			alert("Please enter a positive value (>= 1) for Minimum Induction!");
			return false;
		}
		return true;
	}

	else{
		alert ("Please make sure that you've entered a valid numerical value for the Minimum Repression and/or Minimum Induction");
		return false;
	}

}
// This is used to check the query form on question1.php...
function checkQuestion1Form(){
	var genename = document.query.genename.value;
	var refseq = document.query.refseq.value;
	var goid = document.query.goid.value;
	var goterm = document.query.goterm.value;
	//alert ("in checkQuestion1Form");
	if(isBlank(genename) && isBlank(refseq) && isBlank(goid) && isBlank(goterm)){
		alert("Please make sure that you've entered a value in one of the search fields!");
		return false;
	}
	var value = checkSampleForm();
	//alert("value is: " + value);
	return value;

}
function checkClassificationForm()
{

 var chemcheckboxChecked = false;

   var groups = document.query.numberGroups.value;
   var posFold = document.query.rval.value;
   var negFold = document.query.lval.value;
   var infogain = document.query.infogain.value;

	 dml = document.forms['query'];
   len = dml.elements.length;
   for (var i=0, j=len; i<j; i++) {
       myType = dml.elements[i].type;
       myName = dml.elements[i].name;

       if (myType == 'checkbox') {
       	  if(dml.elements[i].checked){
            chemcheckboxChecked = true;
	  }
       }
   }

	if (!chemcheckboxChecked){
	alert('Please select at least 1 chemical or condition.');
	return false;
	}


	if(!isNumber(groups)){
		alert("Please enter a valid positive integer number value (>= 1) for the number of defined classes!");
			return false;

	}
	if(isNumber(groups)){
		if(groups < 1){
			alert("Please enter a positive integer number value (>= 1) for the number of defined classes");
			return false;
		}
	}

	if(!isNumber(posFold)){
		alert("Please enter a positive number value (>= 1) for minimum induction!");
			return false;

	}
	if(isNumber(posFold)){
		if(posFold < 1){
			alert("Please enter a positive number value (>= 1) for minimum induction!");
			return false;
		}
	}

	if(!isNumber(negFold)){
		alert("Please enter a negative number value (<= -1) for minimum repression!");
			return false;

	}
	if(isNumber(negFold)){
		if(negFold > -1){
			alert("Please enter a negative number value (<= -1) for minimum repression!");
			return false;
		}
	}
	if(!isNumber(infogain)){
		alert("Please enter a valid positive integer number value (>= 1) for information gain!");
			return false;

	}
	if(isNumber(infogain)){
		if(infogain < 1){
			alert("Please enter a positive integer number value (>= 1) for informationgain");
			return false;
		}
	}

}











function checkClusteringForm()
{
   var chemcheckboxChecked = false;
   var num = document.query.number.value;
   var groups = document.query.numberGroups.value;
   var posFold = document.query.rval.value;
   var negFold = document.query.lval.value;
   var maxposFold = document.query.rvalmax.value;
   var minnegFold = document.query.lvalmin.value;




   dml = document.forms['query'];
   len = dml.elements.length;
   for (var i=0, j=len; i<j; i++) {
       myType = dml.elements[i].type;
       myName = dml.elements[i].name;

       if (myType == 'checkbox') {
       	  if(dml.elements[i].checked){
            chemcheckboxChecked = true;
	  }
       }
   }

	if (!chemcheckboxChecked){
	alert('Please select at least 1 chemical or condition.');
	return false;
	}

	if(!isNumber(num)){
		alert("Please enter a positive integer number value (>= 1) for the number of clusters for k-means!");
			return false;

	}
	if(isNumber(num)){
		if(num < 1){
			alert("Please enter a positive integer number value (>= 1) for the number of clusters for k-means!");
			return false;
		}
	}

	if(!isNumber(groups)){
		alert("Please enter a positive integer number value (>= 1) for the number of groups for ordering of selections!");
			return false;

	}
	if(isNumber(groups)){
		if(groups < 1){
			alert("Please enter a positive integer number value (>= 1) for the number of groups for ordering of selections!");
			return false;
		}
	}

	if(!isNumber(posFold)){
		alert("Please enter a positive number value (>= 1) for minimum induction!");
			return false;

	}
	if(isNumber(posFold)){
		if(posFold < 1){
			alert("Please enter a positive number value (>= 1) for minimum induction!");
			return false;
		}
	}

	if(!isNumber(negFold)){
		alert("Please enter a negative number value (<= -1) for minimum repression!");
			return false;

	}
	if(isNumber(negFold)){
		if(negFold > -1){
			alert("Please enter a negative number value (<= -1) for minimum repression!");
			return false;
		}
	}

	if(!isBlank(maxposFold)){
		if(!isNumber(maxposFold)){
			alert("Please enter a positive number value (>= 1) for minimum induction ceiling (2nd value)");
				return false;

		}
		if(isNumber(maxposFold)){
			if(maxposFold < 1){
				alert("Please enter a positive number value (>= 1) for minimum induction ceiling! (2nd value)");
				return false;
			}
		}
	}
	if(!isBlank(minnegFold)){
		if(!isNumber(minnegFold)){
			alert("Please enter a negative number value (<= -1) for minimum repression floor (2nd value)");
				return false;

		}
		if(isNumber(minnegFold)){
			if(minnegFold > -1){
				alert("Please enter a negative number value (<= -1) for minimum repression floor! (2nd value)");
				return false;
			}
		}
	}

    return true;
}

function checkClusteringSelectClonesForm()
{
   var chemcheckboxChecked = false;
   var num = document.query.number.value;
   var groups = document.query.numberGroups.value;


   dml = document.forms['query'];
   len = dml.elements.length;
   for (var i=0, j=len; i<j; i++) {
       myType = dml.elements[i].type;
       myName = dml.elements[i].name;

       if (myType == 'checkbox') {
       	  if(dml.elements[i].checked){
            chemcheckboxChecked = true;
	  }
       }
   }

	if (!chemcheckboxChecked){
	alert('Please select at least 1 chemical or condition.');
	return false;
	}

	if(!isNumber(num)){
		alert("Please enter a positive integer number value (>= 1) for the number of clusters for k-means!");
			return false;

	}
	if(isNumber(num)){
		if(num < 1){
			alert("Please enter a positive integer number value (>= 1) for the number of clusters for k-means!");
			return false;
		}
	}

	if(!isNumber(groups)){
		alert("Please enter a positive integer number value (>= 1) for the number of clusters for k-means!");
			return false;

	}
	if(isNumber(groups)){
		if(groups < 1){
			alert("Please enter a positive integer number value (>= 1) for the number of groups for ordering of selections!");
			return false;
		}
	}



    return true;
}


function checkQuestion3Form(){
var chem = document.query.chem.value;
	//alert ("in checkQuestion1Form");
	if(isBlank(chem)){
		alert("Please make sure that you've selected a treatment.");
		return false;
	}
	var value = checkSampleForm();
	//alert("value is: " + value);
	return value;

}

function checkQuestion2Form(){

	var value = checkSampleForm();
	return value;

}

//-------------------------------------------------------------------
// isBlank(value)
//   Returns true if value only contains spaces
//-------------------------------------------------------------------
function isBlank(val){
	//alert("in isBlank..");
	if(val==null){return true;}
	for(var i=0;i<val.length;i++) {
		if ((val.charAt(i)!=' ')&&(val.charAt(i)!="\t")&&(val.charAt(i)!="\n")&&(val.charAt(i)!="\r")){return false;}
		}
	return true;
}

function openme(cloneid, versionid){

w = screen.width;
h = screen.height;
ah = h - 25;
//alert("in openme...");
nw=document.open("genesamplelist.php?cloneid="+cloneid+"&versionid="+versionid+"");
//window.nw.moveTo(0,0);

}

function opensample(sampleid, lowerbound, upperbound){
var sampletd = document.getElementById("samp" + sampleid);
var sampval = sampletd.innerHTML;

window.location.href="http://genome.oncology.wisc.edu/edge2/sample.php?sampleid="+sampval+"&orderby=hybrids.finalratio&sort=asc&lcomp=lte&lval="+lowerbound+"&rcomp=gte&rval="+upperbound+"";
}

function viewcloneinfo(clonenumber,arraynumber){
	window.open('http://genome.oncology.wisc.edu/edge2/cloneinfo.php?cloneid=' + clonenumber + '&versionid=&arrayid=' + arraynumber+'');
}

function openWindow(url,name,w,h){
var u = url;
 var load = window.open("http://genome.oncology.wisc.edu/edge2/"+url+"");
}

function popup(mylink, windowname)
{
if (! window.focus)return true;
var href;
//if (typeof(mylink) == 'string')
//href=mylink;
//else
href=mylink.href;
window.open(href, windowname, 'width=400,height=400,resizable=yes,scrollbars=no');
return false;
}

function fixwindow(width, height){
window.resizeTo(width, height);
window.statusbar.visible = false;
window.menubar.visible = false;
return true;
}
function openSite(){
nw = window.open("http://www.google.com");
}

 function showsvg(coo){
      document.embeds['heatmap'].getSVGDocument().getElementById('svgObject').setAttribute('viewBox',coo);
      document.embeds['heatmap'].getSVGDocument().getElementById('svgObject').removeAttribute('width');
      document.embeds['heatmap'].getSVGDocument().getElementById('svgObject').removeAttribute('height');
    }

function hideTrxRow(tohide) {

if(tohide==0){
	hide=document.getElementById("kmeansoption");
	show=document.getElementById("hierarchicaloption");
	// Check to see if Don't Cluster is checked.  if it is not, hide the order options option....

	if(document.query.trxCluster[1].checked){
		hideorder= hideOrderRows(1);
	}else{
		hideorder = hideOrderRows(0);
	}
}
if(tohide==1){
	show=document.getElementById("kmeansoption");
	hide=document.getElementById("hierarchicaloption");
	hideorder= hideOrderRows(1);
}
   hide.style.display="none";
   show.style.display="";

	return true;
}

function hideTrxRowOnLoad(){

	var visible = document.query.clusterAlgo[0].checked;
	var visible2 = document.query.seloptions[0].checked;
	var visible3 = document.query.orderoptions[0].checked;
	var visible4 = document.query.seloptions[2].checked;
	//alert(visible3);
	//alert("in onload");
	//alert(visible);
	if(visible == true){
	//alert("hiding kmeans");
	x = hideTrxRow(0); // hide the kmeans row
	}
	else{
	y = hideTrxRow(1);
	}
	if(visible3 == true){
	t=hideNumGroups(0);
	}
	else{
	t=hideNumGroups(1);
	}

	if(visible4 == true){
	// Both selection options are to be shown...
		a=hideSelsRow(2);
	}else{
		if(visible2 == true){
			z=hideSelsRow(0);
		}else{
			b=hideSelsRow(1);
		}
	}

	return true;
}



function hideTrxRowOnLoadAgilentClustering(){

	var visible = document.query.clusterAlgo[0].checked;
	//var visible2 = document.query.seloptions[0].checked;
	var visible3 = document.query.orderoptions[0].checked;
	//var visible4 = document.query.seloptions[2].checked;
	//alert(visible3);
	//alert("in onload");
	//alert(visible);
	if(visible == true){
	//alert("hiding kmeans");
	x = hideTrxRow(0); // hide the kmeans row
	}
	else{
	y = hideTrxRow(1);
	}
	if(visible3 == true){
	t=hideNumGroups(0);
	}
	else{
	t=hideNumGroups(1);
	}
	return true;
}
function hideTrxRowOnLoad2(numclasses){
	var visible = document.query.clusterAlgo[0].checked;
	var visible2 = document.query.seloptions[0].checked;
	
	if(visible == 1){
	//alert("hiding kmeans");
	x = hideTrxRow(0); // hide the kmeans row
	}
	else{
	y = hideTrxRow(1);
	}

	if(visible2 == 1){
	z=hideSelsRow(0); // hide the kmeans row
	}
	if(visible2==2){
		a=hideSelsRow(2);
	}
	if(visible2==0){
	b=hideSelsRow(1);
	}

	return true;
}

function hideOrderRows(tohide){
if(tohide == 0){
	hide0=document.getElementById("orderoption0");
	hide1=document.getElementById("orderoption1");
	document.query.orderoptions.checked = false;
	hide0.style.display="none";
	hide1.style.display="none";
	x=hideNumGroups(0);
}

if(tohide == 1){
	show0=document.getElementById("orderoption0");
	show1=document.getElementById("orderoption1");
	show0.style.display="";
	show1.style.display="";
	var visible2 = document.query.orderoptions[1].checked;
	if(visible2 == false){
		x=hideNumGroups(0);
	}else{
		x=hideNumGroups(1);
	}
}

return true;

}

function hideSelsRow(tohide) {

if(tohide==0){
	hide1=document.getElementById("individualoption1");
	hide2=document.getElementById("individualoption2");
	show1=document.getElementById("groupoption1");
	//show2=document.getElementById("groupoption2");
	show3=document.getElementById("groupoption3");
	hide1.style.display="none";
  	hide2.style.display="none";
	show1.style.display="";
	//show2.style.display="";
	show3.style.display="";
}
if(tohide==1){
	show1=document.getElementById("individualoption1");
	show2=document.getElementById("individualoption2");
	hide1=document.getElementById("groupoption1");
	//hide2=document.getElementById("groupoption2");
	hide3=document.getElementById("groupoption3");
	hide1.style.display="none";
	//hide2.style.display="none";
	hide3.style.display="none";
	show1.style.display="";
	show2.style.display="";
}
if(tohide == 2){
	show1=document.getElementById("individualoption1");
	show2=document.getElementById("individualoption2");
	show1.style.display="";
   	show2.style.display="";
	show1=document.getElementById("groupoption1");
	//show2=document.getElementById("groupoption2");
	show3=document.getElementById("groupoption3");
	show1.style.display="";
   	//show2.style.display="";
   	show3.style.display="";

}


	return true;
}

function show_div(div_id, numclasses) {
	//alert(div_id);
    // hide all the divs
    for(var i=0;i<=numclasses;i++) {
    	//alert('section' + i);
	if(document.getElementById('section' + i)){
	//alert('section' + i);
	 document.getElementById('section'+i).style.display = 'none';
	}
    }

    // show the requested div
    document.getElementById(div_id).style.display = 'block';
    return false;
}

function show_querydiv(div_id, numclasses) {
    // hide all the divs
    for(var i=0;i<numclasses;i++) {
	 document.getElementById('querysection'+i).style.display = 'none';
    }

    // show the requested div
    document.getElementById(div_id).style.display = 'block';
    return false;
}

function checkOrder(length){

return true;

}

function hideNumGroups(tohide){
if(tohide==0){
	hide1=document.getElementById("ordergroups1");
	hide1.style.display="none";
}
if(tohide==1){
	show1=document.getElementById("ordergroups1");
	show1.style.display="";

}
return true;
}

