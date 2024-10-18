function getTargetContents()
{
	//var t = dojo.dnd.manager().target;


	var x = target1.getAllNodes();
	//alert("x length: " + x.length);
	for(i =0;i<x.length;i++)
	{
		alert('getting element ' + i);
		//jsNode is javascript object for node
		//x[i] represents HTML element for the node
		var jsnode = target1.getItem(x[i].id);
		alert(DataToJson(jsnode.data));

		//alert("value " + i + " = " +jsnodeval.data);
	}

}

function checkNewGroup(){

	var namefield = document.getElementById("newgroupname");
	var val = namefield.value;

	if(val==null){
		alert("You need to enter a value for the user group name.");
		return false;
	}
	for(var i=0;i<val.length;i++) {
		if ((val.charAt(i)!=' ')&&(val.charAt(i)!="\t")&&(val.charAt(i)!="\n")&&(val.charAt(i)!="\r")){return true;}
		}
	alert("You need to enter a value for the user group name.");
	return false;
}



function submitUserGroupExpBuilderForm(){

//  This function is called when the user clicks on the 'Update Group' button in the script usergroupexperimentbuilder.inc.php
var selectedOption = document.getElementById('selectedBuilderGroup');
	targetnodecount = 0;
	var expval = selectedOption.value;
	if(expval > 0){

		var x = target1.getAllNodes();
		var arrayVals = "";
		var count = 0;
		for(i = 0; i < x.length;i++){
			var jsnode = target1.getItem(x[i].id);
			if(count != 0){
				arrayVals = arrayVals + "," + jsnode.data.id;
			}else{
				arrayVals = jsnode.data.id;
			}
			count++;
		}
		document.usergroupexpbuilder.expList.value = arrayVals;
		
		return true;
	}else{
		alert("You've not selected a Group to modify!");
		return false;
	}



}
function submitUserGroupBuilderForm(){

//  This function is called when the user clicks on the 'Update Group' button in the script usergroupbuilder.inc.php

var selectedOption = document.getElementById('selectedBuilderGroup');
	targetnodecount = 0;
	var expval = selectedOption.value;
	if(expval > 0){

		var x = target1.getAllNodes();
		var arrayVals = "";
		var count = 0;
		for(i = 0; i < x.length;i++){
			var jsnode = target1.getItem(x[i].id);
			if(count != 0){
				arrayVals = arrayVals + "," + jsnode.data.id;
			}else{
				arrayVals = jsnode.data.id;
			}
			count++;
		}
		document.usergroupbuilder.userList.value = arrayVals;
		
		return true;
	}else{
		alert("You've not selected a Group to modify!");
		return false;
	}



}




function submitExpBuilderForm(){

//  This function is called when the user clicks on the 'expBuilderSubmit' button
var selectedOption = document.getElementById('selectedBuilderExperiment');
	targetnodecount = 0;
	var expval = selectedOption.value;
	if(expval > 0){

		var x = target1.getAllNodes();
		var arrayVals = "";
		var count = 0;
		for(i = 0; i < x.length;i++){
			var jsnode = target1.getItem(x[i].id);
			if(count != 0){
				arrayVals = arrayVals + "," + jsnode.data.id;
			}else{
				arrayVals = jsnode.data.id;
			}
			count++;
		}
		document.expbuilder.expArrayList.value = arrayVals;
		alert("You've successfully modified the arrays associated w/ the experiment");
		return true;
	}else{
		alert("You've not selected an experiment to modify!");
		return false;
	}
}

// this function is called in the addgroupstoexperiments.inc.php script
function submitExpGroupBuilderForm(){

//  This function is called when the user clicks on the 'expBuilderSubmit' button
var selectedOption = document.getElementById('selectedBuilderExperiment');
	targetnodecount = 0;
	var expval = selectedOption.value;
	if(expval > 0){

		var x = target1.getAllNodes();
		var groupVals = "";
		var count = 0;
		for(i = 0; i < x.length;i++){
			var jsnode = target1.getItem(x[i].id);
			if(count != 0){
				groupVals = groupVals + "," + jsnode.data.id;
			}else{
				groupVals = jsnode.data.id;
			}
			count++;
		}
		document.expbuilder.expGroupList.value = groupVals;
		return true;
	}else{
		alert("You've not selected an experiment to modify!");
		return false;
	}
}




function updateMemberCheckList(){

	var selectedOption = document.getElementById('selectedBuilderGroup');
	var groupval = selectedOption.value;
            dojo.xhrGet ({
                // Location of the HTML content we want to grab
                url: 'updategroupmemberschecklist.php?groupid='+groupval,
       
                // Called when the page loaded successfully
                load: function (data) {
                    dojo.byId('groupList').innerHTML = data;
                },
       
                // Called if there was an error (such as a 404 response)
                error: function (data) {
                    console.error('Error: ', data);
                }
            });
        





}

function checkOwner(value, numboxes){

	var owner = document.getElementById('ownerid');
	//owner = owner.value;
	//alert("the value of owner is " + owner);

	//alert("the value of this box is: " + value);
	//alert("the # of of boxes is: " + numboxes);
	// how many of the boxes are checked?
	var checkedcount = 0;
	for(var i = 0; i < numboxes; i++){
		var thisid = "member" + i;
		var thisbox = document.getElementById(thisid);
		//var thisboxval = thisbox.value;
		if(thisbox.checked == true){
			checkedcount++;
		}
		//alert("this box is " + thisboxval);
	}
	//alert("the number of checked boxes is: " + checkedcount);
	if(checkedcount == 0){
		// is owner checked?
		if(owner.checked == true){
			//alert("owner is checked");
			owner.disabled = true;
		}else{
			alert("You cannot remove all Admins.  Rechecking and disabling the owner's checkbox.");
			owner.checked = true;
			owner.disabled = true;
		}
	
	}else{
		owner.disabled = false;

	}


	return true;


}



function updateExpGroupBuilderList(){

	var selectedOption = document.getElementById('selectedBuilderExperiment');
	targetnodecount = 0;
	var groupval = selectedOption.value;
	//alert('expval: '+expval);
	target1.selectAll();
	target1.deleteSelectedNodes();
	target1.clearItems();
	if(groupval >= 0){
		GetAllExperimentGroups(groupval);
	}
	else{
		dojo.byId("numGroupTargetItems").innerHTML = 0;
	}
}

function GetAllExperimentGroups(expval){
	
	dojo.xhrGet( {
		url: "updateexperimentgrouplist.php?expid="+expval,
		//url: "./IMAGES/experimentlist18052.json",
		handleAs: "json",
		load: function(responseObject, ioArgs) {
			all_nodes = responseObject;
			target1.selectAll();
			target1.deleteSelectedNodes();
			target1.clearItems();
			target1.insertNodes(false, all_nodes.items);
			//alert("num of target arrays: "+ targetnodecount);
			dojo.byId("numExpGroups").innerHTML = targetnodecount ;
			//dijit.byId('dialog1').hide();
		}
	})

}
function updateExpGroupList(){
var selectedOption = document.getElementById('selectedBuilderGroup');

//var target1 = document.getElementById('target1');
	targetnodecount = 0;
	var groupval = selectedOption.value;
	//alert('expval: '+expval);
	target1.selectAll();
	target1.deleteSelectedNodes();
	target1.clearItems();
	
	
		
	
	if(groupval >= 0){
	//alert("http://edge.oncology.wisc.edu/updatearraylist.php?expid=" +expval);
		GetAllGroupItems(groupval);

	}
	else{

		dojo.byId("numGroupItems").innerHTML = 0;
	}
	//GetAllGroupExpItems(groupval);





}



function updateGroupExpList(){
	var selectedOption = document.getElementById('selectedBuilderExperiment');
	
	
	targetnodecount = 0;
	var groupval = selectedOption.value;
	//alert('expval: '+expval);
	target1.selectAll();
	target1.deleteSelectedNodes();
	target1.clearItems();
	
	
		
	
	if(groupval >= 0){
	//alert("http://edge.oncology.wisc.edu/updatearraylist.php?expid=" +expval);
		GetAllGroupExpItems(groupval);

	}
	else{

		dojo.byId("numGroupItems").innerHTML = 0;
	}
	//GetAllGroupExpItems(groupval);


}

//Retrives list of all users associated w/ the group, groupid
function GetAllGroupItems(groupid)
{	

	dojo.xhrGet( {
		url: "updategrouplist.php?groupid="+groupid,
		//url: "./IMAGES/experimentlist18052.json",
		handleAs: "json",
		load: function(responseObject, ioArgs) {
			all_nodes = responseObject;
			target1.selectAll();
			target1.deleteSelectedNodes();
			target1.clearItems();
			target1.insertNodes(false, all_nodes.items);
			//alert("num of target arrays: "+ targetnodecount);
			dojo.byId("numGroupItems").innerHTML = targetnodecount ;
			//dijit.byId('dialog1').hide();
		}
	})
}




//Retrives list of all experiments associated w/ the group, groupid
function GetAllGroupExpItems(groupid)
{	

	dojo.xhrGet( {
		url: "updategroupexperimentlist.php?groupid="+groupid,
		//url: "./IMAGES/experimentlist18052.json",
		handleAs: "json",
		load: function(responseObject, ioArgs) {
			all_nodes = responseObject;
			target1.selectAll();
			target1.deleteSelectedNodes();
			target1.clearItems();
			target1.insertNodes(false, all_nodes.items);
			//alert("num of target arrays: "+ targetnodecount);
			dojo.byId("numGroupItems").innerHTML = targetnodecount ;
			//dijit.byId('dialog1').hide();
		}
	})
}

function updateArrayList() { // �?

	arrayList();
	//getTargetContents()
}

function arrayList(){

var selectedOption = document.getElementById('selectedBuilderExperiment');
//var target1 = document.getElementById('target1');
	targetnodecount = 0;
	var expval = selectedOption.value;
	//alert('expval: '+expval);
	target1.selectAll();
	target1.deleteSelectedNodes();
	target1.clearItems();
	if(expval >= 0){
	//alert("http://edge.oncology.wisc.edu/updatearraylist.php?expid=" +expval);
	GetAllItems(expval);

	}
	else{

		dojo.byId("numExpItems").innerHTML = 0;
	}
	GetAllSourceItems();
}



//Retrives list of all arrays associated w/ the experiment, expid
function GetAllItems(expid)
{	//alert('in all items!');
//var selectedValue = document.selectedBuilderExperiment.value;
//alert("The selected value is: " + selectedValue);

	dojo.xhrGet( {
		url: "updatearraylist.php?expid="+expid,
		//url: "./IMAGES/arraylist16344.json",
		handleAs: "json",
		load: function(responseObject, ioArgs) {
			all_nodes = responseObject;
			target1.selectAll();
			target1.deleteSelectedNodes();
			target1.clearItems();
			target1.insertNodes(false, all_nodes.items);
			//alert("num of target arrays: "+ targetnodecount);
			dojo.byId("numExpItems").innerHTML = targetnodecount ;
			//dijit.byId('dialog1').hide();
		}
	})
}

function GetAllSourceItems(value){
	//alert('in get all source items. value is ' + value);
	sourcenodecount = 0;
	if(value == 1){

		dojo.xhrGet( {
			url: "updatearraylist.php?arrayListType=1",
			handleAs: "json",
			load: function(responseObject, ioArgs) {
				nodes = responseObject;
				source1.selectAll();
				source1.deleteSelectedNodes();
				source1.clearItems();
				source1.insertNodes(false, nodes.items);
				dojo.byId("numArrayItems").innerHTML = sourcenodecount ;
				//dijit.byId('dialog1').hide();
			}
		})

//alert('after xhrGet');
	}else if(value==2){

		dojo.xhrGet( {
			url: "updatearraylist.php?arrayListType=2",
			handleAs: "json",
			load: function(responseObject, ioArgs) {
				nodes = responseObject;
				source1.selectAll();
				source1.deleteSelectedNodes();
				source1.clearItems();
				source1.insertNodes(false, nodes.items);
				dojo.byId("numArrayItems").innerHTML = sourcenodecount ;
				//dijit.byId('dialog1').hide();
			}
		})


	}else{

		//alert('displaying my arrays not associated w/ an experiment');
		dojo.xhrGet( {
			url: "updatearraylist.php?arrayListType=3",
			handleAs: "json",
			load: function(responseObject, ioArgs) {
				nodes = responseObject;
				source1.selectAll();
				source1.deleteSelectedNodes();
				source1.clearItems();
				source1.insertNodes(false, nodes.items);
				dojo.byId("numArrayItems").innerHTML = sourcenodecount ;
				//dijit.byId('dialog1').hide();
			}
		})

	}





}

// This function is used to populate the drag and drop list w/ user groups when building user groups.

function GetAllGroups(value){
	
	sourcenodecount = 0;
		//alert("Value is : " + value);
	
		//alert("before entering php file...");
		if(value == 1){
			dojo.xhrGet( {
				//url: "updateexplist.php?expListType="+value,url: "./IMAGES/experimentlist23964.json",
				url: "updateDNDGroupList.php?groupListType=1",
				
				handleAs: "json",
				load: function(responseObject, ioArgs) {
					nodes = responseObject;
					source1.selectAll();
					source1.deleteSelectedNodes();
					source1.clearItems();
					source1.insertNodes(false, nodes.items);
					dojo.byId("numUserItems").innerHTML = sourcenodecount ;
					//dijit.byId('dialog1').hide();
				}
			})
		}else if(value==2){
			dojo.xhrGet( {
			url: "updateDNDGroupList.php?groupListType=2",
			handleAs: "json",
			load: function(responseObject, ioArgs) {
					nodes = responseObject;
					source1.selectAll();
					source1.deleteSelectedNodes();
					source1.clearItems();
					source1.insertNodes(false, nodes.items);
					dojo.byId("numUserItems").innerHTML = sourcenodecount ;
					//dijit.byId('dialog1').hide();
				}
		})


		}else{
			dojo.xhrGet( {
			url: "updateDNDGroupList.php?groupListType=3",
			handleAs: "json",
			load: function(responseObject, ioArgs) {
					nodes = responseObject;
					source1.selectAll();
					source1.deleteSelectedNodes();
					source1.clearItems();
					source1.insertNodes(false, nodes.items);
					dojo.byId("numUserItems").innerHTML = sourcenodecount ;
					//dijit.byId('dialog1').hide();
				}
		})


		}

}
function GetAllUsers(value){
// used for populating the experiments source field on the user group add experiment page
sourcenodecount = 0;
		//alert("Value is : " + value);
	
		//alert("before entering php file...");
		if(value == 1){
			dojo.xhrGet( {
				//url: "updateexplist.php?expListType="+value,url: "./IMAGES/experimentlist23964.json",
				url: "updateuserlist.php?userListType=1",
				
				handleAs: "json",
				load: function(responseObject, ioArgs) {
					nodes = responseObject;
					source1.selectAll();
					source1.deleteSelectedNodes();
					source1.clearItems();
					source1.insertNodes(false, nodes.items);
					dojo.byId("numUserItems").innerHTML = sourcenodecount ;
					//dijit.byId('dialog1').hide();
				}
			})
		}else if(value==2){
			dojo.xhrGet( {
			url: "updateuserlist.php?userListType=2",
			handleAs: "json",
			load: function(responseObject, ioArgs) {
					nodes = responseObject;
					source1.selectAll();
					source1.deleteSelectedNodes();
					source1.clearItems();
					source1.insertNodes(false, nodes.items);
					dojo.byId("numUserItems").innerHTML = sourcenodecount ;
					//dijit.byId('dialog1').hide();
				}
		})


		}else{
			dojo.xhrGet( {
			url: "updateuserlist.php?userListType=3",
			handleAs: "json",
			load: function(responseObject, ioArgs) {
					nodes = responseObject;
					source1.selectAll();
					source1.deleteSelectedNodes();
					source1.clearItems();
					source1.insertNodes(false, nodes.items);
					dojo.byId("numUserItems").innerHTML = sourcenodecount ;
					//dijit.byId('dialog1').hide();
				}
		})


		}
}




function GetAllExperimentItems(value){

// used for populating the experiments source field on the user group add experiment page
sourcenodecount = 0;
		//alert("Value is : " + value);
	
		//alert("before entering php file...");
		if(value == 1){
			dojo.xhrGet( {
				//url: "updateexplist.php?expListType="+value,url: "./IMAGES/experimentlist23964.json",
				url: "updateexperimentlist.php?expListType=1",
				
				handleAs: "json",
				load: function(responseObject, ioArgs) {
					nodes = responseObject;
					source1.selectAll();
					source1.deleteSelectedNodes();
					source1.clearItems();
					source1.insertNodes(false, nodes.items);
					dojo.byId("numExpItems").innerHTML = sourcenodecount ;
					//dijit.byId('dialog1').hide();
				}
			})
		}else if(value==2){
			dojo.xhrGet( {
			url: "updateexperimentlist.php?expListType=2",
			handleAs: "json",
			load: function(responseObject, ioArgs) {
					nodes = responseObject;
					source1.selectAll();
					source1.deleteSelectedNodes();
					source1.clearItems();
					source1.insertNodes(false, nodes.items);
					dojo.byId("numExpItems").innerHTML = sourcenodecount ;
					//dijit.byId('dialog1').hide();
				}
		})


		}else{
			dojo.xhrGet( {
			url: "updateexperimentlist.php?expListType=3",
			handleAs: "json",
			load: function(responseObject, ioArgs) {
					nodes = responseObject;
					source1.selectAll();
					source1.deleteSelectedNodes();
					source1.clearItems();
					source1.insertNodes(false, nodes.items);
					dojo.byId("numExpItems").innerHTML = sourcenodecount ;
					//dijit.byId('dialog1').hide();
				}
		})


		}
}




function GetAllExpSourceItems(value){
	//alert('in get all EXP source items');
	//alert('value is ' + value);
	sourcenodecount = 0;
	if(value == 1){
		//alert("before entering php file...");
		dojo.xhrGet( {
			//url: "updateexplist.php?expListType="+value,
			url: "updateexplist.php?expListType=1",
			handleAs: "json",
			load: function(responseObject, ioArgs) {
				nodes = responseObject;
				source1.selectAll();
				source1.deleteSelectedNodes();
				source1.clearItems();
				source1.insertNodes(false, nodes.items);
				dojo.byId("numExps").innerHTML = sourcenodecount ;
				//dijit.byId('dialog1').hide();
			}
		})

		//alert('after xhrGet');
	}else if(value==2){

		dojo.xhrGet( {
			url: "updateexplist.php?expListType=2",
			handleAs: "json",
			load: function(responseObject, ioArgs) {
				nodes = responseObject;
				source1.selectAll();
				source1.deleteSelectedNodes();
				source1.clearItems();
				source1.insertNodes(false, nodes.items);
				dojo.byId("numExps").innerHTML = sourcenodecount ;
				//dijit.byId('dialog1').hide();
			}
		})


	}else{

		//alert('displaying my arrays not associated w/ an experiment');
		dojo.xhrGet( {
			url: "updateexplist.php?expListType=3",
			handleAs: "json",
			load: function(responseObject, ioArgs) {
				nodes = responseObject;
				source1.selectAll();
				source1.deleteSelectedNodes();
				source1.clearItems();
				source1.insertNodes(false, nodes.items);
				dojo.byId("numArrayItems").innerHTML = sourcenodecount ;
				//dijit.byId('dialog1').hide();
			}
		})

	}





}


function updateGroupList(){

	var selectedOption = document.getElementById('selectedBuilderGroup');
//var target1 = document.getElementById('target1');
	targetnodecount = 0;
	var groupval = selectedOption.value;
	//alert('expval: '+expval);
	target1.selectAll();
	target1.deleteSelectedNodes();
	target1.clearItems();
	
	
		
	
	if(groupval >= 0){
	//alert("http://edge.oncology.wisc.edu/updatearraylist.php?expid=" +expval);
		GetAllGroupItems(groupval);

	}
	else{

		dojo.byId("numGroupItems").innerHTML = 0;
	}
	//GetAllGroupExpItems(groupval);


}


//Node creator function used to create UI for shopping cart items.
var targetnode_creator = function(data, hint){
	targetnodecount++;
	var types = [];
	types.push('item');
	var node = dojo.doc.createElement("div");
	dojo.addClass(node,"dojoDndItem");

	html = "<table cellpadding='2' cellspacing='0'>" ;
	html += "<tbody><tr><td width='50px'>" ;
	html+= "<img src='"+data.img_url+"'/><br\>";
	html += "</td><td>";
	html+= data.name+"<br/></td></tr></tbody></table>";
	node.innerHTML = html;
	node.id = dojo.dnd.getUniqueId();
	return {node: node, data: data, type: types};
};


//Node creator function used to create UI for shopping cart items.
var sourcenode_creator = function(data, hint){

	var types = [];
	types.push('item');
	var node = dojo.doc.createElement("div");
	dojo.addClass(node,"dojoDndItem");

	html = "<table cellpadding='2' cellspacing='0'>" ;
	html += "<tbody><tr><td width='50px'>" ;
	html+= "<img src='"+data.img_url+"'/><br\>";
	html += "</td><td>";
	html+= data.name+"<br/></td></tr></tbody></table>";
	node.innerHTML = html;
	node.id = dojo.dnd.getUniqueId();
	sourcenodecount++;
	return {node: node, data: data, type: types};
};

