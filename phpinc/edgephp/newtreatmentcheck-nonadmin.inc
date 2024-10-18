<script language="JavaScript">

function isNumber(num1) {
	//alert("in isNumber!!!");

 if ((num1 / 2 >= 0)||(num1 / 2 < 0))
   return true
  else
   return false
}


function changeAccessNumber(){
	var visible = document.forms[0].visible[0].checked;
	var accessnumber = document.forms[0]-nonadmin.accessnumber.value;

	if(visible == "1"){
		window.document.forms[0].accessnumber.value='1';
		alert("Setting Access Number to 1.  This means everyone will be able \nto view the data associated with this treatment");
		//return true;

	}
	else{
		window.document.forms[0].accessnumber.value='99';
		alert("Setting Access Number to 99.  This means that only people with an Access Number of 99 or above will be able to view the data for this treatment");
		alert("Please change this if needed so that submitters can view their own data.");
		//return true;
	}
}


function checkForm(){

	document.write("Are we here?");
	var samplename = document.forms[0].samplename.value;
	var rnasize = document.forms[0].rnagroupsize.value;
	var age = document.forms[0].age.value;
	var dose = document.forms[0].dose.value;
	var dosetimehrs = document.forms[0].dosehours.value;
	var dosetimemins = document.forms[0].doseminutes.value;
	var harvesthrs = document.forms[0].harvesthours.value;
	var harvestmins = document.forms[0].harvestminutes.value;
	var datafile = document.forms[0].file.value;
	var accessnum = document.forms[0].accessnumber.value;
	



	// Are any of these blank????
	if(isBlank(samplename) || isBlank(rnasize) || isBlank(age) || isBlank(dose) || isBlank(dosetimehrs) || isBlank(dosetimemins) || isBlank(harvesthrs) || isBlank(harvestmins) || isBlank(datafile) || isBlank(accessnum) ){
		alert("You've left a required field blank!");
		return false;
	}

	if(isNumber(rnasize) && isNumber(age)  && isNumber(dose) && isNumber(dosetimehrs) && isNumber(dosetimemins) && isNumber(harvesthrs) && isNumber(harvestmins) && isNumber(accessnum)){
		if(rnasize > 0 && age > 0 && dose >= 0 && dosetimehrs >= 0 && dosetimemins >= 0 && harvesthrs >= 0 && harvestmins >= 0){
			if(dosetimehrs < 24 && dosetimemins < 60 && harvesthrs < 24 && harvestmins < 60 && accessnum >= 1){
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
				if{
					alert("Please enter a valid time value.  Hours >= 00 and <=23, Minutes >= 00 and <=59");
					return false;
				}
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

function changeAccessNumberEditTrx(){

	var visible1 = document.commitchanges.visible[1].checked;
	var visible2 = document.commitchanges.visible[2].checked;
	var accessnumber1 = document.commitchanges.accessnumber.value;

	if(visible1 == "1"){
		window.document.commitchanges.accessnumber.value='1';
		alert("Setting Access Number to 1.  This means everyone will be able \nto view the data associated with this treatment");
		//return true;

	}
	if(visible2 == "1"){
		window.document.commitchanges.accessnumber.value='99';
		alert("Setting Access Number to 99.  This means that only people with an Access Number of 99 or above will be able to view the data for this treatment");
		alert("Please change this if needed so that submitters can view their own data.");
		//return true;
	}
}

function checkChangesForm(){
	var samplename = document.commitchanges.samplename.value;
	var rnasize = document.commitchanges.rnagroupsize.value;
	var age = document.commitchanges.age.value;
	var dose = document.commitchanges.dose.value;
	var dosetimehrs = document.commitchanges.dosehours.value;
	var dosetimemins = document.commitchanges.doseminutes.value;
	var harvesthrs = document.commitchanges.harvesthours.value;
	var harvestmins = document.commitchanges.harvestminutes.value;
	//var datafile = document.commitchanges.file.value;
	var accessnum = document.commitchanges.accessnumber.value;
	
	
	

	// Are any of these blank????
	if(isBlank(samplename) || isBlank(rnasize) || isBlank(age) || isBlank(dose) || isBlank(dosetimehrs) || isBlank(dosetimemins) || isBlank(harvesthrs) || isBlank(harvestmins) || isBlank(accessnum) ){
		alert("You've left a required field blank!");
		return false;
	}

	if(isNumber(rnasize) && isNumber(age)  && isNumber(dose) && isNumber(dosetimehrs) && isNumber(dosetimemins) && isNumber(harvesthrs) && isNumber(harvestmins) && isNumber(accessnum)){
		if(rnasize > 0 && age > 0 && dose >= 0 && dosetimehrs >= 0 && dosetimemins >= 0 && harvesthrs >= 0 && harvestmins >= 0){
			if(dosetimehrs < 24 && dosetimemins < 60 && harvesthrs < 24 && harvestmins < 60 && accessnum >= 1){
				return true;
			}
			else{
				if(accessnum < 1){
					alert("Please enter a valid access number.  ie. >= 1");
					return false;
				}
				else{
					alert("Please enter a valid time value.  Hours >= 00 and <=23, Minutes >= 00 and <=59");
					return false;
				}
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


</script>

