        dojo.require("dojo.parser");

        dojo.require("dijit.Toolbar");

        dojo.require("dijit.layout.LayoutContainer");
        dojo.require("dijit.layout.SplitContainer");
        dojo.require("dijit.layout.AccordionContainer");
        dojo.require("dijit.layout.TabContainer");
        dojo.require("dijit.layout.ContentPane");
	dojo.require("dijit.form.Button");
dojo.require("dijit.Menu");
dojo.require("dijit.Tooltip");
dojo.require("dijit.Dialog");
dojo.require("dijit.form.ComboBox");
dojo.require("dijit.form.CheckBox");
dojo.require("dijit.form.FilteringSelect");
dojo.require("dijit.form.Textarea");
dojo.require("dijit.Tree");
dojo.require("dijit.TitlePane");
       dojo.require("dijit.form.TextBox");
        dojo.require("dijit.Editor");
	dojo.require("dojo.dnd.Source");




	var all_nodes,exptarget, expsource, source_nodes, targetnodecount, sourcenodecount;
dojo.addOnLoad(

function(){

dojo.byId('loaderInner').innerHTML += " done.";
			setTimeout("hideLoader()",250);

});



function drawdijitbox($element, $onoff){

	dijit.byId($element).disabled = $onoff;


}
  function drawbox($element,$onoff) // is used to enable/disable a textbox based on radio-buttons clicked
	{
		//alert("in drawbox");
		document.getElementById($element).disabled = $onoff;
	}



function loadScript(){

/*dijit.byId('expDesc').setDisabled(true);
 var elem = document.getElementById('expDesc');

        elem.style.position = 'absolute';
	elem.style.display = 'none';
*/
}




// drag-n-drop stuff
function AddItems(target,nodes)
{/*
	for (var i=0;i<nodes.length;i++)
	total += parseFloat((target.getItem(nodes[i].id)).data);
	dojo.byId("cost").innerHTML = total;*/
	/*
	var x = target.getAllNodes();
	if(target == target1){
		dojo.byId("numExpItems").innerHTML = x.length;
	}else{

		dojo.byId("numArrayItems").innerHTML = x.length;
	}*/
}

function SubstractItems(target,nodes)
{
/*
	for (var i=0;i<nodes.length;i++)
	total -= parseInt((target.getItem(nodes[i].id)).data);
	dojo.byId("cost").innerHTML = total;

	//alert("in subtract");
	var x = target.getAllNodes();
	if(target == target1){
		dojo.byId("numExpItems").innerHTML = x.length;
	}else{

		dojo.byId("numArrayItems").innerHTML = x.length;
	}
*/
}




// Returns JSON fragment for a single item.
function DataToJson(data)
{
var x= "{'id':"+"'"+data.id+"',";
x += "'name':"+"'"+data.name+"',";
x += "'price':"+"'"+data.price+"',";
x += "'rating':"+"'"+data.rating+"',";
x += "'description':"+"'"+data.description+"',";
x += "'img_url':"+"'"+data.img_url+"'},";
return x;

}
function init()
{
	exptarget = new dojo.dnd.Source("exptarget", { creator: targetnode_creator ,accept: ["item"],copyOnly: false});
	expsource = new dojo.dnd.Source("expsource", {creator: sourcenode_creator , accept: ["item"], copyOnly: false});

	//var x = GetAllItems();
	dojo.subscribe("/dnd/drop", function(source,nodes,iscopy){
  		/*var t = dojo.dnd.manager().target;
		//ClearMsg();

		if(t == target1){
			AddItems(t,nodes);
			SubstractItems(source1, nodes)
		}
		if(t == source1){
			AddItems(t,nodes);
			SubtractItems(target1,nodes);
		}
		*/

		var x = exptarget.getAllNodes();
		dojo.byId("numExpGroupItems").innerHTML = x.length;
		var x = expsource.getAllNodes();
		dojo.byId("numExps").innerHTML = x.length;



	});


	dojo.subscribe("/dnd/start", function(source,nodes,iscopy){
  		var t = dojo.dnd.manager().target;
		//alert("in subscribe");
		//ShowPrice(source,nodes);
		alert("This is t: " + t);

		if(t == exptarget){
			AddItems(t,nodes);
			alert('exptarget');
		}
		});




	dojo.subscribe("/dnd/cancel", function(){
  		//ClearMsg();
		});

}

dojo.addOnLoad(init);

function testexpbuildform(){
	alert('in testexpbuildform');
	return true;





}
function hideLoader(){
			var loader = dojo.byId('loader');
			dojo.fadeOut({ node: loader, duration:500,
				onEnd: function(){
					loader.style.display = "none";
				}
			}).play();
		}
