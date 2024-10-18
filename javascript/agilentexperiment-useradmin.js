        dojo.require("dojo.parser");

        dojo.require("dijit.Toolbar");

        dojo.require("dijit.layout.LayoutContainer");
        dojo.require("dijit.layout.SplitContainer");
        dojo.require("dijit.layout.AccordionContainer");
        dojo.require("dijit.layout.TabContainer");
        dojo.require("dijit.layout.ContentPane");
	dojo.require("dijit.form.Button");
	//dojo.require("dojo.data.ItemFileReadStore");
dojo.require("dojo.data.ItemFileReadStore");
//dojo.require("dijit.Menu");
dojo.require("dijit.Tooltip");
dojo.require("dijit.Dialog");
//dojo.require("dijit.form.ComboBox");
dojo.require("dijit.form.CheckBox");
dojo.require("dijit.form.FilteringSelect");
//dojo.require("dijit.form.Textarea");
dojo.require("dijit.Tree");
dojo.require("dijit.TitlePane");
       dojo.require("dijit.form.TextBox");
       // dojo.require("dijit.Editor");
	dojo.require("dojo.dnd.Source");




	var all_nodes,target1, source1, exptarget, expsource, source_nodes, targetnodecount, sourcenodecount;

dojo.addOnLoad(

function(){
	if(dojo.byId('loaderInner') != null){
		dojo.byId('loaderInner').innerHTML += " done.";
		setTimeout("hideLoader()",250);
	}
	dojo.subscribe("mytree", null, function(message){
		if(message.event=="execute"){
			if(experimentsStore.getValue(message.item, "type") == "experiment"){
				//alert("You clicked on experiment: " +experimentsStore.getLabel(message.item));
				var arrayid = experimentsStore.getValue(message.item, "expid");
				dijit.byId('thirdDialog').setHref("edge3experimentinfo.php?expid="+arrayid);
dijit.byId('thirdDialog').show();

			}else{
				var arrayid = experimentsStore.getValue(message.item, "arrayid");
				//window.open("edge3arrayinfo.php?arrayid="+arrayid);
	window.showModalDialog("edge3arrayinfo.php?arrayid="+arrayid,'',
  'dialogHeight:800px;dialogWidth:800px');
				//dijit.byId('thirdDialog').setHref("edge3arrayinfo.php?arrayid="+arrayid);
//dijit.byId('thirdDialog').show();


			}
		}
		});
});


function checkCreateArrayForm(){
	if(document.createarray.arraytype.value == -1){
		alert("You have not chosen an array type!");
		return false;
	}
	if(document.createarray.cy3channel.value == -1){
		alert("You have not chosen a cy3 value!");
		return false;
	}
	if(document.createarray.cy5channel.value == -1){
		alert("You have not chosen a cy5 value!");
		return false;
	}
	if(document.createarray.arrayname.value == ""){
		alert("You have not entered an array name!");
		return false;
	}
	
	return true;
}


function editExpName(){

	if(document.getElementById('editName').checked == true){
			// Turn off editing of description...
			drawbox('nameExp',false);
			var index = document.agilentexp.selectedExperiment.value - 1;
			var expName = document.agilentexp.selectedExperiment[index].innerHTML;
			//alert('value: ' + theContents)
			document.agilentexp.nameExp.value = expName;


		}else{
			drawbox('nameExp',true);
		}

}


function updateexpdesc(index,self) { // �?
	 window.location.href = self+"?neweditexp=1&editid="+index; 
	
/*
	if(document.agilentexp.selectedExperiment.value != -1){

	// keep the editable boxes disabled for the time being...
  	drawbox('nameExp',true);
  	drawdijitbox('expDesc',true);
	document.agilentexp.editDesc.checked = false;
	document.getElementById('editDesc').disabled = false;
	document.getElementById('editName').disabled = false;
	document.getElementById('editName').checked = false;
	dijit.byId('expDesc').setDisabled(true)
	document.agilentexp.nameExp.value = "Check 'Edit Treatment Name?' checkbox to modify current treatment name.";
	

      dojo.xhrGet( { // �
					// The following URL must match that used to test the server.
        url: "./updateexpdesc.php?expid=" +document.agilentexp.selectedExperiment.value,
        handleAs: "text",

        timeout: 5000, // Time in milliseconds

        // The LOAD function will be called on a successful response.
        load: function(response, ioArgs) { // �
		var ewin = document.getElementById("editordiv");
		var ifr = ewin.getElementsByTagName("iframe");
		ifr[0].contentWindow.document.body.innerHTML = response;
		
         // dijit.byId("expDesc").setValue(response); // �
          return response; // �
        },

        // The ERROR function will be called in an error case.
        error: function(response, ioArgs) { // �
          console.error("HTTP status code: ", ioArgs.xhr.status); // �
          return response; // �
          }
        });

  
	  }else{
    	//alert("nameExp selected");
		

    }
*/
}






function drawdijitbox(element, onoff){

	dijit.byId(element).disabled = onoff;


}
  function drawbox(element,onoff) // is used to enable/disable a textbox based on radio-buttons clicked
	{
		//alert("in drawbox");
		document.getElementById(element).disabled = onoff;
	}

 function drawboxOtherRNASample(element,onoff) // is used to enable/disable a textbox based on radio-buttons clicked
	{ 
		//alert("in drawbox");
		/*document.getElementById($element).disabled = $onoff;
		if($onoff == false){
			alert("Please enter the value of 'Other' in the field associated and the additional Sample Information field below.<br>Example: Strain = New Strain Name"); 
		}*/
	}


 function editExpDesc(element) // is used to enable/disable a textbox based on radio-buttons clicked
	{
		//alert("in editDesc and element is: "+$element);
		var expdesc = document.getElementById('info');
		var editDesc = document.getElementById('editDesc');
		if(editDesc.checked == true){
			// Turn off editing of description...
			//alert("enabling editor");
			expdesc.disabled=false;


		}else{
			//alert("disabling editor");
			expdesc.disabled=true;

		}
	}


function loadScript(){
var expdesc = document.getElementById('info');
	if(expdesc != null){
		expdesc.disabled=true;
	}

 var elem = document.getElementById('expDeschidden');
	if(elem != null){
		elem.style.position = 'absolute';
		elem.style.display = 'none';
	}
var selectedexp = document.agilentexp.selectedExperiment.value;

if(selectedexp != null){ 
	if(selectedexp == -1){
			drawbox('nameExp',false);
			//drawdijitbox('info',false);
			document.agilentexp.info.value="Title:\n\nPurpose/Background:\n\nExperimental Design:\n\nControl:\n\nPapers of Interest:\n\nContact Information:\n\n(To whom questions should be addressed if different than individual creating experiment)";
			
			document.agilentexp.info.disabled = false;
			
			document.agilentexp.editDesc.checked = true;
			document.agilentexp.editDesc.disabled = true;
			document.agilentexp.nameExp.value = "Enter new name here";
			document.agilentexp.editName.checked = true;
			document.agilentexp.editName.disabled = true;
			drawbox('info', false);
			
	
		}
}

}

function getArray(){

dijit.byId('thirdDialog').setHref("agilentarrayinfo.php?arrayid=2");
dijit.byId('thirdDialog').show();


}

function submitStuff(){
	//alert("in submit stuff!");
 if(document.getElementById('editName').checked == false && document.getElementById('editDesc').checked == false){
	alert("You made no changes to this entry! Not submitting for update/new Experiment.");
	return false;
 }else{
	//document.agilentexp.expDesc.value = dijit.byId("expDesc").getValue();
	//alert(dijit.byId("expDesc").getValue());
	return true;
}
//alert("exiting submit stuff");

}


// drag-n-drop stuff
function AddItems(target,nodes,initval)
{/*
	for (var i=0;i<nodes.length;i++)
	total += parseFloat((target.getItem(nodes[i].id)).data);
	dojo.byId("cost").innerHTML = total;*/
	var x = target.getAllNodes();
	if(target == target1){
		if(initval == 1){
			dojo.byId("numExpItems").innerHTML = x.length;
		}else{
			dojo.byId("numGroupItems").innerHTML = x.length;
		}
	}else{
		if(initval ==1){ 
			dojo.byId("numArrayItems").innerHTML = x.length;
		}else{
			dojo.byId("numExpItems").innerHTML = x.length;
		}
	}
}



function ClearMsg()
{   //dojo.byId("msg").innerHTML = "";

}

function init(initval)
{
	target1 = new dojo.dnd.Source("target1", { creator: targetnode_creator ,accept: ["item"],copyOnly: false});
	source1 = new dojo.dnd.Source("source1", {creator: sourcenode_creator , accept: ["item"], copyOnly: false});
	dojo.subscribe("/dnd/drop", function(source,nodes,iscopy){
		var x = target1.getAllNodes();
		if(initval == 1){
			// We are dealing with agilentexperimentbuilder.inc
			dojo.byId("numExpItems").innerHTML = x.length;
			var x = source1.getAllNodes();
			dojo.byId("numArrayItems").innerHTML = x.length;
		}else if(initval==2){
			// We are dealing with addgroupstoexper.inc.php
			dojo.byId("numExpGroups").innerHTML = x.length;
			var x = source1.getAllNodes();
			dojo.byId("numUserItems").innerHTML = x.length;

		}else{
			dojo.byId("numGroupItems").innerHTML=x.length;
			var x = source1.getAllNodes();
			dojo.byId("numUserItems").innerHTML = x.length;

		}
	});


	dojo.subscribe("/dnd/start", function(source,nodes,iscopy){
  		var t = dojo.dnd.manager().target;
		if(t == target1){
			
			AddItems(t,nodes,initval);
			alert('target1');
		}
		if(t == exptarget){
			AddItems(t,nodes,initval);
			alert('exptarget');
		}
		});




	dojo.subscribe("/dnd/cancel", function(){
  		//ClearMsg();
		});

}


function testexpbuildform(){
	//alert('in testexpbuildform');
	return true;





}
function hideLoader(){
			var loader = dojo.byId('loader');
			dojo.fadeOut({ node: loader, duration:250,
				onEnd: function(){
					loader.style.display = "none";
				}
			}).play();
		}
