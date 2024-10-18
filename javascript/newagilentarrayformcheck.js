function initializeNewRNASampleForm(numorganisms){

	// there are a couple of things that need to be taken care.  
	// #1 - first, we need to make sure that the correct strain and genevariation options are available based on the organism currently selected.
	//alert('in initialization of rna sample form');
	var organism = document.agilentrnasample.organism;
	
	var strain = document.agilentrnasample.strain;
	
	var genevariation = document.agilentrnasample.genevariation;
	//alert("organism value = " + organism.value);
	var selectedradiobutton = organism.value; //getSelectedRadio(organism);
	toggleLayerOn('strain', numorganisms, selectedradiobutton);
	toggleLayerOn('variation', numorganisms, selectedradiobutton);
	//#2 - second, we need to make sure that the pregnancy stuff is correct.  sometimes if you change the state from male to female and there was pregnancy selected w/ gestation period, 
	// the pregnancy state stays at no.
	var sex = document.agilentrnasample.sex;
	selectedradiobutton = getSelectedRadio(sex);
	// there are three sex values: 0 = male, 1=female,2 = unknown
	
	if(selectedradiobutton == 0){
		document.agilentrnasample.pregnant[1].checked=false;
			document.agilentrnasample.pregnant[0].checked=true;
		document.agilentrnasample.gestation.disabled = true;
	}
}


	//function disableRadioGroup($element, $onoff) to be generized for any element soon
	function disablePregnantRadioGroup($ele,$onoff)
	{
		if($onoff=="true"){
			document.getElementById('gestation').disabled = true;
			document.agilentrnasample.gestation.disabled = true;
			
			document.agilentrnasample.gestation.value = 0;
			for(var i=0; i<document.agilentrnasample.pregnant.length; i++){
				document.agilentrnasample.pregnant[i].disabled = true;
				
				
				
			}
			//setCheckedValue(document.agilentrnasample.pregnant, 0);
			document.agilentrnasample.pregnant[1].checked=false;
			document.agilentrnasample.pregnant[0].checked=true;
		}
		else{
			//document.getElementById('gestation').disabled = false;
			for(var i=0; i<document.agilentrnasample.pregnant.length; i++){
			document.agilentrnasample.pregnant[i].disabled = false;
			//setCheckedValue(document.agilentrnasample.pregnant, 0);
			
			}
			//setCheckedValue(document.agilentrnasample.pregnant, 0);
			document.agilentrnasample.pregnant[1].checked=false;
			document.agilentrnasample.pregnant[0].checked=true;
			document.agilentrnasample.gestation.disabled = true
		}
	}


	function checkForm2() // validates form data entered by user
	{
	
	var samplename = document.agilentrnasample.samplename.value;
	var treatment = document.agilentrnasample.treatment;
	var organism = document.agilentrnasample.organism;
	var rnasize = document.agilentrnasample.rnagroupsize.value;
	var concentration = document.agilentrnasample.concentration.value;
	var strain = document.agilentrnasample.strain;
	var genevariation = document.agilentrnasample.genevariation;
	var age = document.agilentrnasample.age.value;
	var ageunit = document.agilentrnasample.ageunit;
	var sex = document.agilentrnasample.sex;
	var pregnant = document.agilentrnasample.pregnant.value;
	var gestationPeriod = document.agilentrnasample.gestation.value;
	var vehicle = document.agilentrnasample.vehicle;
	var tissue = document.agilentrnasample.tissue;
	var dose = document.agilentrnasample.dose.value;
	var doseunitid = document.agilentrnasample.doseunitid;
	var route = document.agilentrnasample.route;
	var dosetimehrs = document.agilentrnasample.dosehours.value;
	var dosetimemins = document.agilentrnasample.doseminutes.value;
	var harvesthrs = document.agilentrnasample.harvesthours.value;
	var harvestmins = document.agilentrnasample.harvestminutes.value;
	var duration = document.agilentrnasample.duration.value;
	var durationunit = document.agilentrnasample.durationunit;
	var notPregnant = document.agilentrnasample.gestation.disabled;
	var filename1 = document.agilentrnasample.file1.value;
	var file1desc = document.agilentrnasample.file1desc.value;
	var filename2 = document.agilentrnasample.file2.value;
	var file2desc = document.agilentrnasample.file2desc.value;
	var filename3 = document.agilentrnasample.file3.value;
	var file3desc = document.agilentrnasample.file3desc.value;

	if(!isBlank(filename1)){
		if(isBlank(file1desc)){
			alert("You did not enter a description for file #1!");
			return false;
		}

	}

	if(!isBlank(filename2)){
		if(isBlank(file2desc)){
			alert("You did not enter a description for file #2!");
			return false;
		}

	}

	if(!isBlank(filename3)){
		if(isBlank(file3desc)){
			alert("You did not enter a description for file #3!");
			return false;
		}

	}
	var selectedradiobutton = getSelectedRadio(vehicle);
	if(selectedradiobutton < 0){
		alert("You've not chosen a Vehicle value!");
		return false;

	}
	//selectedradiobutton = getSelectedRadio(treatment);
	//if(selectedradiobutton < 0){
	//	alert("You've not chosen a Treatment/Condition value!");
	//	return false;
	//}
	//selectedradiobutton = getSelectedRadio(organism);
	//if(selectedradiobutton < 0){
	//	alert("You've not chosen a Organism value!");
	//	return false;
	//}
	selectedradiobutton = getSelectedRadio(strain);
	if(selectedradiobutton < 0){
		alert("You've not chosen a Strain value!");
		return false;

	}
	selectedradiobutton = getSelectedRadio(genevariation);
	if(selectedradiobutton < 0){
		alert("You've not chosen a Genetic Variation value!");
		return false;

	}
	selectedradiobutton = getSelectedRadio(ageunit);
	if(selectedradiobutton < 0){
		alert("You've not chosen a Age Units value!");
		return false;

	}
	selectedradiobutton = getSelectedRadio(sex);
	if(selectedradiobutton < 0){
		alert("You've not chosen a Sex value!");
		return false;

	}
	selectedradiobutton = getSelectedRadio(sex);
	if(selectedradiobutton < 0){
		alert("You've not chosen a Sex value!");
		return false;

	}
	selectedradiobutton = getSelectedRadio(doseunitid);
	if(selectedradiobutton < 0){
		alert("You've not chosen a Dose Units value!");
		return false;

	}
	selectedradiobutton = getSelectedRadio(route);
	if(selectedradiobutton < 0){
		alert("You've not chosen a Route value!");
		return false;

	}
	selectedradiobutton = getSelectedRadio(durationunit);
	if(selectedradiobutton < 0){
		alert("You've not chosen a Duration Units value!");
		return false;

	}
	// Validating that gestation period has a non-blank and numeric value when organism is pregnant

	if(notPregnant == false){
		if(isBlank(gestationPeriod)){
			alert("You've left the Gestation Period field blank!");
			return false;
		}
		if(!isNumber(gestationPeriod)){
			alert("You need to enter a numerical value in the Gestation Period field.");
			return false;
		}
		if(gestationPeriod<=0){
			alert("Please enter a valid gestation period (>0)");
			return false;
		}
	}
	
	if(isBlank(rnasize)){
		alert("You've left the RNA group size field blank!");
		return false;
	}
	if(isBlank(age)){
		alert("You've left the Age field blank!");
		return false;
	}
	if(isBlank(dose)){
		alert("You've left the dose field blank!");
		return false;
	}
	if(isBlank(dosetimehrs)){
		alert("You've left the dose time hours field blank!");
		return false;
	}
	if(isBlank(dosetimemins)){
		alert("You've left the dose time minutes field blank!");
		return false;
	}
	if(isBlank(harvesthrs)){
		alert("You've left the harvest time hours field blank!");
		return false;
	}
	if(isBlank(harvestmins)){
		alert("You've left the harvest time minutes field blank!");
		return false;
	}
	if(isBlank(samplename)){
		alert("You've left the RNA sample name field blank!");
		return false;
	}
	if(isBlank(concentration)){
		alert("You've left the RNA concentration field blank!");
		return false;
	}
	if(isBlank(duration)){
		alert("You've left the duration field blank!");
		return false;
	}
	



	if(isNumber(rnasize) && isNumber(age)  && isNumber(dose) && isNumber(dosetimehrs) && isNumber(dosetimemins) && isNumber(harvesthrs) &&  isNumber(harvestmins)  && isNumber(concentration)&& isNumber(duration)){
		if(rnasize > 0 && age >= 0 && dose >= 0 && dosetimehrs >= 0 && dosetimemins >= 0 && harvesthrs >= 0 && harvestmins >= 0){
			if(dosetimehrs < 24 && dosetimemins < 60 && harvesthrs < 24 && harvestmins < 60){
				return true;
			}
			else{
				/*
				if(accessnum < 1){
					alert("Please enter a valid access number.  ie. >= 1");
					return false;
				}
				else{
				*/
				//if{
					alert("Please enter a valid time value.  Hours >= 00 and <=23, Minutes >= 00 and <=59");
					return false;
				//}
			}
		}
		else{
			alert("Please enter a valid numerical value in the fields that require one.");
			return false;
		}
	}
	else{
		alert("You need to enter a numerical value in one of the fields that require one.  Please rectify this situation.");
		return false;
	}

	}

	function isBlank(val){ //returns true if value contains only spaces
	//alert("in isBlank..");
	if(val==null){return true;}
	for(var i=0;i<val.length;i++) {
		if ((val.charAt(i)!=' ')&&(val.charAt(i)!="\t")&&(val.charAt(i)!="\n")&&(val.charAt(i)!="\r")){return false;}
		}
	return true;
	}

	function isNumber(num1) { // returns true if num1 is a number
	//alert("in isNumber!!!");
	if ((num1 / 2 >= 0)||(num1 / 2 < 0))
   		return true
  	else
   		return false
	}

function toggleLayerOn( whichLayer, numValues, showValue )
{
 	 var elem, vis, showLayer, hideLayer;

  // Show the selected organism's strain layer....
	showLayer = whichLayer + showValue;
	//alert("Layer to show: " + showLayer);
	if( document.getElementById ){ // this is the way the standards work
		elem = document.getElementById( showLayer );
	}else if( document.all ){ // this is the way old msie versions work
		elem = document.all[showLayer];
	}else if( document.layers ){ // this is the way nn4 works
		elem = document.layers[showLayer];
	}
	
		vis = elem.style;
		vis.display = 'block';
		for(i=0;i<numValues;i++){
			if(i != showValue){
				hideLayer = whichLayer + i;
				//alert("in loop hideLayer: " + hideLayer);
				if( document.getElementById ){ // this is the way the standards work
					elem = document.getElementById( hideLayer );
				}else if( document.all ){ // this is the way old msie versions work
					elem = document.all[hideLayer];
				}else if( document.layers ){ // this is the way nn4 works
					elem = document.layers[hideLayer];
				}
				vis = elem.style;
				vis.display = 'none'
			}

		}
		
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

function isNumber(num1) {
	//alert("in isNumber!!!");

 if ((num1 / 2 >= 0)||(num1 / 2 < 0))
   return true 
  else 
   return false 
 }

function hideDiv(strain) {

if(strain==0){
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

function checkCy5Yield(){
	var cy5yield = document.updatecy5yield.cy5yield.value;
	if(isBlank(cy5yield)){
		alert("You need to enter a value for Cy5 Yield!");
		return false;
	}
	if(!isNumber(cy5yield)){
		alert("You need to enter a numerical value for Cy5 Yield!");
		return false;
	}
	return true;
}


function checkCy5SpecificActivity(){
	var cy5sa = document.updatecy5specificactivity.cy5specificactivity.value;
	if(isBlank(cy5sa)){
		alert("You need to enter a value for Cy5 Specific Activity!");
		return false;
	}
	if(!isNumber(cy5sa)){
		alert("You need to enter a numerical value for Cy5 Specific Activity!");
		return false;
	}
	return true;
}


function checkCy3Yield(){
	var cy3yield = document.updatecy3yield.cy3yield.value;
	if(isBlank(cy3yield)){
		alert("You need to enter a value for Cy3 Yield!");
		return false;
	}
	if(!isNumber(cy3yield)){
		alert("You need to enter a numerical value for Cy3 Yield!");
		return false;
	}
	return true;
}

function checkCy3SpecificActivity(){
	var cy3sa = document.updatecy3specificactivity.cy3specificactivity.value;
	if(isBlank(cy3sa)){
		alert("You need to enter a value for Cy3 Specific Activity!");
		return false;
	}
	if(!isNumber(cy3sa)){
		alert("You need to enter a numerical value for Cy3 Specific Activity!");
		return false;
	}
	return true;
}

// Used for parsing the form for submitting a completed array: edgearraysubmit.inc.php
function checkArraySubmit(){
	var arrayname = document.newedgearray.arraydesc.value;
	var cy5yield = document.newedgearray.cy5yield.value;
	var cy5specificactivity = document.newedgearray.cy5specificactivity.value;
	var cy3yield = document.newedgearray.cy3yield.value;
	var cy3specificactivity = document.newedgearray.cy3specificactivity.value;
	var arrayfile = document.newedgearray.file.value;
	if(isBlank(arrayname)){
		alert("You need to enter an array name!");
		return false;
	}
	if(isBlank(cy5yield)){
		alert("You need to enter a value for Cy5 Yield!");
		return false;
	}
	if(isBlank(cy5specificactivity)){
		alert("You need to enter a value for Cy5 Specific Activity!");
		return false;
	}
	if(isBlank(cy3yield)){
		alert("You need to enter a value for Cy3 Yield!");
		return false;
	}
	if(isBlank(cy3specificactivity)){
		alert("You need to enter a value for Cy3 Specific Activity!");
		return false;
	}
	if(!isNumber(cy5yield)){
		alert("You need to enter a numerical value for Cy5 Yield!");
		return false;
	}
	if(!isNumber(cy5specificactivity)){
		alert("You need to enter a numerical value for Cy5 Specific Activity!");
		return false;
	}
	if(!isNumber(cy3yield)){
		alert("You need to enter a numerical value for Cy3 Yield!");
		return false;
	}
	if(!isNumber(cy3specificactivity)){
		alert("You need to enter a numerical value for Cy3 Specific Activity!");
		return false;
	}
	if(isBlank(arrayfile)){
		alert("You need to select a file!");
		return false;
	}
	return true;
}