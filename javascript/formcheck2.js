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






function check_kNearestForm()
{
   var chemcheckboxChecked = false;
   //var num = document.knearest.number.value;
   var groups = document.knearest.numberGroups.value;
  




   dml = document.forms['knearest'];
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
		alert("Please enter a positive integer number value (>= 1) for the number of groups for ordering of selections!");
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




function checkClusteringForm(formtype)
{
   var chemcheckboxChecked = false;
   var num = document.query.number.value;
   var groups = document.query.numberGroups.value;
   var posFold = document.query.rval.value;
   var negFold = document.query.lval.value;
   var maxposFold = document.query.rvalmax.value;
   var minnegFold = document.query.lvalmin.value;
	
	if(formtype == 1){
		// we need to check to see if feature #s have been entered for selected clustering....
		 var clones = document.query.cloneList.value;
		//alert("here are the clones: " + clones);
		if(isBlank(clones)){
			alert("You need to enter at least one Feature Number value!");
			return false;
		}
	}



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

function checkOrderedForm()
{
//alert("in check ordered form");
	//return true;
	//
   var chemcheckboxChecked = false;
   //var num = document.queryorder.number.value;
   var groups = document.queryorder.numberGroups.value;
   var clones = document.queryorder.cloneList.value;
   //alert("here are the clones: " + clones);
   if(isBlank(clones)){
	alert("You need to enter at least Feature Number value!");
	return false;
   }
//alert("the number of groups is : " + groups);

   dml = document.forms['queryorder'];
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


function checkGeneQueryForm(){
	var index = document.genequery.thisorganism.value
	if(index < 0 || index == ""){
		alert("ERROR: You need to select an organism array.");
		return false;
	}
	
	// now we need to check to make sure they entered something in at least one of the textareas....
	
	var valueentered = false;
	
	var genenametext = document.genequery.genename.value;
	if(genenametext != ""){
		valueentered = true;
	}
	var refseqtext = document.genequery.refseq.value;
	if(refseqtext != ""){
		valueentered = true;
	}
	var locuslinktext = document.genequery.locuslink.value;
	if(locuslinktext != ""){
		valueentered = true;
	}
	var unigenetext = document.genequery.unigene.value;
	if(unigenetext != ""){
		valueentered = true;
	}
	var genbanktext = document.genequery.genbank.value;
	if(genbanktext != ""){
		valueentered = true;
	}
	var featurenumbertext = document.genequery.featurenumber.value;
	if(featurenumbertext != ""){
		valueentered = true;
	}
	var ensembltext = document.genequery.ensembl.value;
	if(ensembltext != ""){
		valueentered = true;
	}
	var genedesctext = document.genequery.genedesc.value;
	if(genedesctext != ""){
		valueentered = true;
	}
	var goidtext = document.genequery.goidtext.value;
	if(goidtext != ""){
		valueentered = true;
	}
	var goidnumtext = document.genequery.goidnum.value;
	if(goidnumtext != ""){
		valueentered = true;
	}
	if(!valueentered){
		alert("You did not enter a value for any of the available search options.  Please do so.");
		return false;
	}
	return true;
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
//alert("tohide = " + tohide);
if(tohide==0){
	hide=document.getElementById("kmeansoption");
	show=document.getElementById("hierarchicaloption");
	// Check to see if Don't Cluster is checked.  if it is not, hide the order options option....
	var avalue = document.getElementById('clustertrx');
	if(avalue){
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

function hideTrxRowEdge2(tohide) {
//alert("tohide = " + tohide);
if(tohide==0){
	hide=document.getElementById("kmeansoption");
	show=document.getElementById("hierarchicaloption");
	// Check to see if Don't Cluster is checked.  if it is not, hide the order options option....
	var avalue = document.getElementById('clustertrx');
	if(avalue){
		hideorder= hideOrderRowsEdge2(1);
	}else{
		hideorder = hideOrderRowsEdge2(0);
	}
}
if(tohide==1){
	show=document.getElementById("kmeansoption");
	hide=document.getElementById("hierarchicaloption");
	hideorder= hideOrderRowsEdge2(1);
}
   hide.style.display="none";
   show.style.display="";

	return true;
}

function hideTrxRowOnLoad(){

	var visible = document.query.clusterAlgo[0].checked;
	//var visible2 = document.query.seloptions[0].checked;
	var visible3 = document.query.orderoptions[0].checked;
	var visible4 = document.query.seloptions[2].checked;
	//alert(visible3);
	//alert("in onload");
	//alert(visible);
	if(visible == true){
	//alert("hiding kmeans");
	x = hideTrxRowEdge2(0); // hide the kmeans row
	}
	else{
	y = hideTrxRowEdge2(1);
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
	//alert("in hideTrxRow...AgilentClustering...");
	var visible = document.getElementById("hier");//document.query.clusterAlgo[0].checked;
	//var visible2 = document.query.seloptions[0].checked;
	//var visible3 = //document.getElementById("defaultordering");//document.query.orderoptions[0].checked;
	var visible3 = document.getElementById("noclustertrx");
	//var visible4 = document.getElementById("clustertrx");
	//alert("visible3.value: " + visible3.value);
	
	//var visible4 = document.query.seloptions[2].checked;
	//alert(visible3);
	//alert("in onload");
	//alert(visible);
	if(visible.checked){
		//alert("hiding kmeans");
		x = hideTrxRow(0); // hide the kmeans row
	}
	else{
		y = hideTrxRow(1);
	}

	if(visible3.checked != true){
	//alert('no cluster trx checked');
	//t=hideNumGroups(0);
		t=hideOrderRows(0);
	}
	else{
	//alert('noclustertrxchecked');
	//t=hideNumGroups(1);
		t=hideOrderRows(1);
	}
	//hideOrderRows(0);

	return true;
}

function hideTrxRowOnLoadSelectedCloneClustering(){
	//alert("in hideTrxRow...SelectedClustering...");
	var visible = document.getElementById("hier");//document.query.clusterAlgo[0].checked;
	//var visible2 = document.query.seloptions[0].checked;
	//var visible3 = document.getElementById("defaultordering");//document.query.orderoptions[0].checked;
	var visible3 = document.getElementById("noclustertrx");
	//var visible4 = document.query.seloptions[2].checked;
	//alert(visible3);
	//alert("in onload");
	//alert(visible);
	/*
	if(visible.checked){
		//alert("hiding kmeans");
		x = hideTrxRow(0); // hide the kmeans row
	}
	else{
	y = hideTrxRow(1);
	}
	if(visible3.checked){
	t=hideNumGroups(0);
	}
	else{
	t=hideNumGroups(1);
	}
	hideOrderRows(0);
	*/
	if(visible.checked){
		//alert("hiding kmeans");
		x = hideTrxRow(0); // hide the kmeans row
	}
	else{
		y = hideTrxRow(1);
	}

	if(visible3.checked != true){
	//alert('no cluster trx checked');
	//t=hideNumGroups(0);
		t=hideOrderRows(0);
	}
	else{
	//alert('noclustertrxchecked');
	//t=hideNumGroups(1);
		t=hideOrderRows(1);
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
	var visible2 = document.getElementById('defaultordering');//document.query.orderoptions[1].checked;
	if(visible2.checked){
		//alert("hidenumgroups(0)");
		x=hideNumGroups(0);
	}else{
		//alert("hidenumgroups(1)");
		x=hideNumGroups(1);
	}
}

return true;

}

function hideOrderRowsEdge2(tohide){
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
	if(visible2.checked){
		//alert("hidenumgroups(0)");
		x=hideNumGroups(1);
	}else{
		//alert("hidenumgroups(1)");
		x=hideNumGroups(0);
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

// toggleLayer is used to show or hide a div....

function toggleLayer( whichLayer )
{
  var elem, vis;
  if( document.getElementById ) // this is the way the standards work
    elem = document.getElementById( whichLayer );
  else if( document.all ) // this is the way old msie versions work
      elem = document.all[whichLayer];
  else if( document.layers ) // this is the way nn4 works
    elem = document.layers[whichLayer];
  vis = elem.style;
  // if the style.display value is blank we try to figure it out here
  if(vis.display==''&&elem.offsetWidth!=undefined&&elem.offsetHeight!=undefined)
    vis.display = (elem.offsetWidth!=0&&elem.offsetHeight!=0)?'block':'none';
  vis.display = (vis.display==''||vis.display=='block')?'none':'block';
}


function checkOrder(length){
	var ttest = document.order.ttest;
	var anova = document.order.anova; 
	var anovapvalue = document.order.anovapvalue;
	var ttestpvalue = document.order.ttestpvalue;
	var checkstatmethod = null;
	if(ttest != null){
		if (ttest.checked == false){
			//alert('test is not checked.');
			return true;
		}
	}
	if(anova != null){
		if(anova.checked == false){
			return true;
		}
	}
	
	if(ttest != null  && anova != null) {
		if(ttest.checked == true && anova.checked == true){
		alert("You can only check one statistical test value!");
		return false;
		}
	}else{
		// only one value is checked...	
		
		if(ttest != null){
			if(ttest.value == 1){
				//alert("t-test is checked");
				var selectedradiobutton = getSelectedRadio(ttestpvalue);
				if(selectedradiobutton < 0){
					alert('t-Test pvalue not selected...');
					return false;
				}
				
			}
			// Now need to check to make sure there are enough groups checked.  this will be the number of groups chosen - 1;
			var numberofgroups = document.order.numberOfGroups.value;
			var groupcount = 0;
			
			for(var i = 1; i < numberofgroups; i++){
				var groupstr = "group" + i;
				thisgroupcheckbox = document.getElementById(groupstr);
				if(thisgroupcheckbox.checked){
					groupcount++;
				}
			}
			var numboxes = numberofgroups - 1;
			if(groupcount == 1){
				return true;
			}else{
				alert("You've chosen t-Test, please check only 1 of the group separator boxes\nto distinguish between the groups or un-check the t-Test box.");
				return false;
			}
		}
		if(anova != null){
			if(anova.value == 1){
				var selectedradiobutton = getSelectedRadio(anovapvalue);
				if(selectedradiobutton < 0){
					alert('ANOVA pvalue not selected...');
					return false;
				}		
			}
			
			// Now need to check to make sure there are enough groups checked.  this will be the number of groups chosen - 1;
			var numberofgroups = document.order.numberOfGroups.value;
			var groupcount = 0;
			for(var i = 1; i < numberofgroups; i++){
				var groupstr = "group" + i;
				thisgroupcheckbox = document.getElementById(groupstr);
				if(thisgroupcheckbox.checked){
					groupcount++;
				}
			}
			var numboxes = numberofgroups - 1;
			if(groupcount >= numboxes){
				return true;
			}else{
				alert("You've chosen ANOVA with " + numberofgroups + " groups, please check at least " + numboxes + " of the group separator boxes\nto distinguish between the groups or un-check the ANOVA box.");
				return false;
			}
		}
	}
	
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

function hideFilter(tohide){

if(tohide==0){
	hide1=document.getElementById("sdrow1");
	hide1.style.display="none";
	show1=document.getElementById("foldchangerow1");
	show1.style.display="";
	show1=document.getElementById("foldchangerow2");
	show1.style.display="";
}
if(tohide==1){
	show1=document.getElementById("foldchangerow1");
	show1.style.display="none";
	show1=document.getElementById("foldchangerow2");
	show1.style.display="none";
	hide1=document.getElementById("sdrow1");
	hide1.style.display="";

}
return true;
}

