<script language="JavaScript">
// check name field
// Checking for username input and email validity on the client side....
function checkName()
{
// if empty, pop up alert
	if (document.register.uname.value == "")
	{
		alert("Please enter a valid name");
		return false;
	}
	else
	{
	return true;
	}
}

// check to make sure values are entered in the password fields...

function checkpasswords(){
	var flag;
	if (document.register.passwd.value == "")
	{
		alert("Please enter a password");
		return false;
	}
	else if(document.register.passwd_again.value == "")
	{
		alert("Please re-enter your password");
		return false;
	}
	else{
		// need to check the length of the password in question....
		return checkPasswordLength(document.register.passwd.value);
	}
}

function checkPasswordLength(pw) {
	if(pw.length< 5) {
  		alert("Your password is less than 5 characters!")
  		alert("You must choose a password that is at least 5 characters in length.")
    		return false
 	}
 	return true
}


// check email field
function checkEmail()
{
	var flag;
	var str = document.register.email.value;
	// regex to match email addresses
	var pattern =
	/^([a-zA-Z0-9])+([\.a-zA-Z0-9_-])*@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-]+)+/;
	var flag = pattern.test(str);

	if(!flag)
	{
		alert ("Please enter a valid email address");
		return false;
	}
	else
	{
		return true;
	}
}

// function to check all form data
function checkForm()
{
	//alert("in checkForm()");
	if (checkName() && checkEmail() && checkpasswords())
	{
	
		if(document.register.firstname.value == ""){
			alert("Please enter your first name.");
			return false;
		}
		if(document.register.lastname.value == ""){
			alert("Please enter your last name.");
			return false;
		}
		if(document.register.org.value == ""){
			alert("Please enter your organization.");
			return false;
		}
		if(document.register.address1.value == ""){
			alert("Please enter your address.");
			return false;
		}
		if(document.register.city.value == ""){
			alert("Please enter your city.");
			return false;
		}

		return true;
	}
	else
	{
		return false;
	}
}

// This will put the focus into the username field... called in onLoad event in the Body tag...
function putFocus(formInst, elementInst) {
	if(document.forms.length >0) {
		document.forms[formInst].elements[elementInst].focus();
	}
}

function checkUpdateForm(){
//alert("In checkUpdateForm()");
	// First dealing w/ password stuff....
	if (document.register.oldpass.value == ""){
		//don't need to check because user is not changing password...
		//just continue....  bad programming, but works for moment....
	}
	else{ // need to check the new password field to see if it is long enough...
		if(document.register.newpass1.value == ""){
			alert("You need to enter a pasword in the New Password field");
			return false;
		}
		else{
			if(document.register.newpass1.value.length < 5){
				alert("Please enter a password that is five characters or longer in the New Password field");
				return false;
			}
			else{ // need to check if there is a value in the newpass2 field.
				if(document.register.newpass2.value == ""){
					alert("You need to enter a password value in the Confirm Password field.");
					return false;
				}
				// need to check if the field is Confirm Password field is long enough....
				if(document.register.newpass2.value.length < 5){
						alert("You need to enter a password greater than or equal to 5 characters in length in the Confirm Password field.");
					return false;
				}

			}
		}
		//alert("The value entered for newpass1: " + document.register.newpass1.value + "newpass2: " + document.register.newpass2.value);
		// now need to see if the passwords match....
		if(document.register.newpass1.value != document.register.newpass2.value){
			alert("The values you entered for new password and confirm password are different, please reenter");
			document.register.reset();
			return false;
		}
	}
	if(document.register.email.value != ""){
		if(!checkEmail()){
			return false;
		}
	}

	return true;
}



</script>
