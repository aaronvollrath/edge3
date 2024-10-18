/* DrasticTools version 0.6.12
 * DrasticTools is released under the GPL license: 
 * Copyright (C) 2007 email: info@drasticdata.nl
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 *
 * =========================================================================================
 * If you find this sofware useful, we appreciate your donation on http://www.drasticdata.nl
 * Suggestions for improvement can be sent to: info@drasticdata.nl
 * ========================================================================================= 
 */
var dsgrid = {
"MSGUPDATEFAILED": "Failed to update cell",
"MSGREALLYDELETE": "Really delete this row?",
"MSGDELETEFAILED": "Failed to delete row",
"MSGINSERTFAILED": "Failed to add row",
"MSGREFRESHFAILED": "Failed to refresh grid",
"MSGCLICKSORT": "Sort on ",
"MSGDELETEROW": "Delete row",
"MSGCLICKEDIT": "Edit cell",
"MSGADDROW": "Add row"
};

var n=0;
var DDTYPECHAR 		= n++;
var DDTYPEINT 		= n++;
var DDTYPEBOOL 		= n++;
var DDTYPEURL 		= n++;
var DDTYPEMAILTO 	= n++;
var DDTYPEENUM 		= n++;

var drasticGrid = new Class ({
    options: {
		path: location.pathname,		// path of the php file to call
		pathimg: "DrasticTools/img/",	// path to images

		pagelength: 20,					// length of the grid. If dataset is longer the grid will do pagination	
		colwidth: 80,					// default width of column in px
		pp: 0,							// page pointer
		sortcol: null,					// name of column to sort on initially. Overrules the default of the data source
		sort: null,						// sort ascending (a) or descending (d)? Overrules the default of the data source
		columns: null,					// array of column configurations

		onClick: Class.empty,
		onMouseOver: Class.empty,
		onAddStart: Class.empty,
		onAddComplete: Class.empty,
		onDeleteStart: function(id){this.DefaultOnDeleteStart(id);},
		onDeleteComplete: Class.empty,
		onUpdateStart: Class.empty,
		onUpdateComplete: Class.empty		
    },	
	initialize: function(container, options){
		var t = this;
		t.container 	= $(container) || alert('container '+container+' not found...');
		t.setOptions(options);
		t.getMetaData();

		t.rowselected = -1;

		// gifs:
		t.del1   = t.options.pathimg+'del.gif';
		t.del2   = t.options.pathimg+'del2.gif';
		t.delempty = t.options.pathimg+'delempty.gif';
		t.add1   = t.options.pathimg+'add.gif';
		t.add2   = t.options.pathimg+'add2.gif';
		t.sort1  = t.options.pathimg+'sort1.gif';
		t.sort2  = t.options.pathimg+'sort2.gif';
		t.sortno = t.options.pathimg+'sortno.gif';
		t.edit 	 = t.options.pathimg+'edit15x15transparant.png';
		t.editmo = t.options.pathimg+'edit15x15.png';
		t.imgsrcup	 = t.options.pathimg+'up.png';
		t.imgsrcdown = t.options.pathimg+'down.png';
		t.ddPowered = t.options.pathimg+'DDPowered.png';
		t.ddPowered2 = t.options.pathimg+'DDPowered2.png';
		t.gifloading= t.options.pathimg+'loading.gif';
		
		// Define settings of columns
		t.columns = [];
		if (t.options.columns) {
			//fill in configuration setting from t.options.columns
			for (var i=0, j=0; i<t.options.columns.length; i++, j++){
				if (!t.cols.contains(t.options.columns[i].name)) {alert("no field with name '"+t.options.columns[i].name+"' in data source"); continue;}
				t.columns[j] = {};
				t.columns[j].name = t.options.columns[i].name;
				if ($defined(t.options.columns[i].type)) t.columns[j].type = t.options.columns[i].type;
				if ($defined(t.options.columns[i].values)) t.columns[j].type = t.options.columns[i].values;
				if ($defined(t.options.columns[i].displayname)) t.columns[j].displayname = t.options.columns[i].displayname;
				if ($defined(t.options.columns[i].editable)) t.columns[j].editable = t.options.columns[i].editable;				
				if ($defined(t.options.columns[i].width)) t.columns[j].width = t.options.columns[i].width;				
			}
		}
		else {
			for (var i=0; i<t.cols.length; i++){
				t.columns[i] = {};
				t.columns[i].name = t.cols[i];		
			}			
		}
		// fill in missing fields
		for (var i=0; i<t.columns.length; i++){
			if (!$defined(t.columns[i].type)) {
				var fldtype = t.flds.get(t.columns[i].name);
				if (fldtype.search("^tinyint") == 0) t.columns[i].type = DDTYPEBOOL;
				else if (t.cols_numeric.contains(t.columns[i].name)) t.columns[i].type = DDTYPEINT;
				else if (fldtype.search("^enum") == 0) {
					t.columns[i].type = DDTYPEENUM;
					t.columns[i].values = [];					
					var arr = fldtype.split("'");
					for (var j=1, k=0; j<arr.length; j+=2, k++) t.columns[i].values[k] = arr[j];
				}
				else 
					t.columns[i].type = DDTYPECHAR;
			}
			if (!$defined(t.columns[i].editable)) {
				t.columns[i].editable = t.editablecols.contains(t.columns[i].name);
			}
			if (!$defined(t.columns[i].displayname)) {
				t.columns[i].displayname = t.columns[i].name;
			}
			if (!$defined(t.columns[i].width)) {
				t.columns[i].width = t.options.colwidth;
			}							
		}
		// For debugging purposes:
		//alert(JSON.encode(t.columns));

		// Table header
		var table = new Element('table', {'class': 'drasticgrid'}).inject(t.container);
		var tbody = new Element('tbody').inject(table); // for IE
		var row = new Element('tr').inject(tbody);
		var containerwidth = 15;
		var col = new Element('th', {'id':t.container.id+'__del', styles:{'width':containerwidth}}).inject(row);
		
		for (var i=0; i < t.columns.length; i++)  {
			var fldname = t.columns[i].name;
			containerwidth += t.columns[i].width;
			var col = new Element('th', {'id':t.container.id+fldname, 'class':'th', styles:{'width':t.columns[i].width}}).inject(row);
			col.addEvent('mouseover', function(){this.addClass('thmouseover');});
			col.addEvent('mouseout', function(){this.removeClass('thmouseover');});
			var div = new Element('div', {'class':'divth'}).inject(col);
			div.set('text', t.columns[i].displayname);
			var sortimg = (t.options.sortcol!=fldname)?t.sortno:(t.options.sort == 'd'?t.sort2:t.sort1);
			var img = new Element('img', {
						'alt':'',
						'class':'imgsort',
						'src': sortimg
						}).inject(div);
			if (t.options.sortcol==fldname) t.sortgif = img;
			div.addEvent('click', function(){t.td_sort(this)});
			div.setProperty('title', dsgrid["MSGCLICKSORT"] + div.get('text'));			
		}
		var scrollwidth = 16;
		containerwidth += scrollwidth;
		var th = new Element('th', {styles: {'width': scrollwidth}}).inject(row); //dummy for scrollbar
		t.container.setStyle('width', containerwidth);
		
		//Data rows:
		for (var i=0; i < t.options.pagelength; i++)  {
			var class1 = ((i % 2) == 0)?'tdeven':'tdodd';	
			var row = new Element('tr', {'id':t.container.id+'row'+i, 'class':class1}).inject(tbody);
			row.addEvent('mouseover', function(){
				this.addClass('rowmouseover');
				if (t.data && t.data[0]) {
					var id = this.id.slice(t.container.id.length + 3).toInt();
					t.fireEvent("onMouseOver", t.data[0][id]);
				}			
			});
			row.addEvent('mouseout', function(){this.removeClass('rowmouseover');});

			var td = new Element('td', {'class':'divdel'}).inject(row);
			var img = new Element('img', {'id':'del'+i, 'alt':'', 'class':'imgdel', 'src':t.delempty}).inject(td);
			
			for(var j=0; j < t.columns.length; j++)  {
				var td = new Element('td', {'id':'td'+i, styles:{'width':t.columns[j].width}}).inject(row);
				td.ddcolumn = t.columns[j];
				td.ddrow = i;
				var div1 = new Element('div', {'class':'tddiv', styles:{'width':t.columns[j].width}}).inject(td);
				var div2 = new Element('div', {'id':'divtd'+i+':'+td.ddcolumn.name, 'class':'divtd'}).inject(div1);
				td.addEvent('click', function(){
					$(t.container).getElements('tr.rowselected').removeClass('rowselected');
					this.getParent().addClass('rowselected');
					if(!this.ddempty){
						t.rowselected = t.data[0][this.ddid];
						t.fireEvent("onClick", t.data[0][this.ddid]);
					}
				});				
			}
			if (i==0) t.tdslider = new Element('td', {'id': 'colslider', 'rowspan': t.options.pagelength}).inject(row);			
		}
		var row = new Element('tr').inject(tbody);
		var td = new Element('td', {'colspan': t.columns.length+1, styles:{'text-align': 'right'}}).inject(row);
		var img = new Element('img', {'title':'Powered by DrasticData', src: t.ddPowered}).inject(td);
		img.addEvent('click', function(){window.open('http://www.drasticdata.nl')});
		img.addEvent('mouseover', function(){this.src = t.ddPowered2;});
		img.addEvent('mouseout', function(){this.src = t.ddPowered;});
		img.setStyle('cursor', 'pointer');		

		//Load indicator:
		t.loading = new Element('div', {'styles': {'position': 'absolute', 'z-index': '100'}}).inject(t.container);
		var img = new Element('img', {'src': t.gifloading}).inject(t.loading);				

		t.refresh();		
	},
	getMetaData: function() {
		var t = this;
		var myajax = new Request({
			url: t.options.path,
			async: false,
			method: 'get',
			onFailure: function(){alert(dsgrid["MSGREFRESHFAILED"]);},
			onSuccess : function(responseText){
				t.metadata = JSON.decode(responseText);
				t.num_rows			= t.metadata[0];
				t.num_fields		= t.metadata[1];
				t.idname			= t.metadata[2];
				t.idcolnr			= t.metadata[3];
				t.cols				= t.metadata[4];
				t.cols_numeric		= t.metadata[5];
				t.add_allowed		= t.metadata[6];
				t.delete_allowed	= t.metadata[7];
				t.editablecols		= t.metadata[8];
				t.defaultcols		= t.metadata[9];
				if (!t.options.sortcol) t.options.sortcol = t.metadata[10];
				if (!t.options.sort) t.options.sort = t.metadata[11];
				t.flds				= new Hash(t.metadata[12]);	
				t.ppmax = Math.ceil(t.num_rows/t.options.pagelength)-1;					
			}
		}).send('op=vm');
	},
	showLoading: function() {
		var t = this;
		var c = t.container.getCoordinates();
		t.loading.setStyles({
			'visibility': 'visible',
			'top': c.top + (c.height/2) - 16,
			'left': c.left + (c.width/2) - 16
		    });
	},
	hideLoading: function() {
		var t = this;
		t.loading.setStyles({'visibility': 'hidden'});
	},	
	UpdateCell:	function(obj, value) {
		var t = this;
		if (value == obj.ddoldvalue) {t.restore_form(obj, null); return}
		var id = obj.ddid;
		var colname = obj.ddcolumn.name;
		var url = t.options.path+'?op=u&id='+obj.ddsqlid+'&col='+colname+'&value='+encodeURI(value);
		t.fireEvent("onUpdateStart", id, colname, value);
		t.showLoading();
		var myajax = new Request({
			url: url,
			method: 'get',
			onFailure: function(){
				t.hideLoading();
				alert(dsgrid["MSGUPDATEFAILED"]);
				t.restore_form(obj, null);
			},
			onComplete: function(responseText){
				t.hideLoading();
				if (responseText[0] == '0') {
					alert(dsgrid["MSGUPDATEFAILED"]);
					t.restore_form(obj, null);
				} else {
					t.fireEvent("onUpdateComplete", id, colname, value);
					t.restore_form(obj, value);
					new Fx.Tween(obj, {duration: 4000}).start('color', '#F00', '#505050');
				}
			}
		}).send();
	},
	restore_form: function(obj, value) {
		if (obj.ddcolumn.type == DDTYPEBOOL) return;
		obj.firstChild.dispose();
		obj.ddoldchild.inject(obj);
		if (!value) return;
		switch(obj.ddcolumn.type) {
			case DDTYPECHAR: obj.firstChild.firstChild.set('text',value);	break;
			case DDTYPEINT: obj.firstChild.firstChild.set('text',value);	break;
			case DDTYPEBOOL: break;
			case DDTYPEURL: obj.firstChild.firstChild.firstChild.set('text',value); break;
			case DDTYPEMAILTO:  obj.firstChild.firstChild.firstChild.set('text',value); break;
			case DDTYPEENUM: obj.firstChild.firstChild.set('text',value);	break;
		}
	},
	create_form: function(obj){
		var t = this;
		switch(obj.ddcolumn.type) {
			case DDTYPECHAR: obj.ddvalue = obj.firstChild.firstChild.innerHTML;	break;
			case DDTYPEINT: obj.ddvalue = obj.firstChild.firstChild.innerHTML;	break;
			case DDTYPEBOOL: break;
			case DDTYPEURL: obj.ddvalue = obj.firstChild.firstChild.firstChild.innerHTML; break;
			case DDTYPEMAILTO: obj.ddvalue = obj.firstChild.firstChild.firstChild.innerHTML; break;
			case DDTYPEENUM: obj.ddvalue = obj.firstChild.firstChild.innerHTML;	break;
		}
		obj.ddoldchild = obj.firstChild;
		obj.firstChild.dispose();
		obj.ddoldvalue = obj.ddvalue;
		var sel;
		if (obj.ddcolumn.type == DDTYPEENUM) {
			sel = new Element('select').inject(obj);
			for (var i=0; i<obj.ddcolumn.values.length; i++){
				var opt = new Element('option', {
					'selected': ((obj.ddvalue==obj.ddcolumn.values[i])? true: false),
					'value': obj.ddcolumn.values[i]
					}).inject(sel);
				opt.appendText(obj.ddcolumn.values[i]);				
			}
			sel.setStyle('width', obj.ddcolumn.width);
			sel.addEvent('keydown', function(e){
				var me = new Event(e);
				if (me.key == 'enter') t.UpdateCell(me.target.getParent(), me.target.value);
				if (me.key == 'esc') t.restore_form(me.target.getParent(), null);	
				});
			sel.addEvent('change', function(){t.UpdateCell(this.getParent(), this.value)});
			sel.addEvent('blur', function(){t.UpdateCell(this.getParent(), this.value)});
			sel.focus();
		} else {
			sel = new Element('input', {'type': 'text',	'value': obj.ddvalue}).inject(obj);
			sel.setStyle('width', obj.ddcolumn.width);			
			sel.addEvent('keydown', function(e){
				var me = new Event(e);
				if (me.key == 'enter') t.UpdateCell(me.target.getParent(), me.target.value);
				if (me.key == 'esc') t.restore_form(me.target.getParent(), null);		
				});
			sel.addEvent('blur', function(){t.UpdateCell(this.getParent(), this.value)});
			sel.focus();
			sel.select();
		}
	},
	tr_del: function(obj) {
		var t = this;
		var id = obj.id.slice(3);
		var row = t.container.getElementById(t.container.id+'row'+id);
		if (row.ddempty) return;
		var sqlid = t.data[0][id];
		new Fx.Tween(row, {duration: 500}).start('color', '#000','#A0A0A0');
		t.do_delete = true;
		t.fireEvent("onDeleteStart", sqlid);
		if (t.do_delete) {
			var url = t.options.path+'?op=d&id='+sqlid;
			t.showLoading();
			var myajax = new Request({
				url: url,
				method: 'get',
				onFailure: function(){
					t.hideLoading();
					alert(dsgrid["MSGDELETEFAILED"]);
					new Fx.Tween(row, {duration: 300}).start('color', '#A0A0A0','#000');
				},				
				onComplete: function(){
					if (t.data[0].length == 1) t.options.pp = Math.max(0,t.options.pp-1);
					t.getMetaData();
					t.refresh();
					new Fx.Tween(row, {duration: 300}).start('color', '#A0A0A0','#000');
					t.hideLoading();
					t.fireEvent("onDeleteComplete", sqlid);	
				}
			}).send();
		} else {
			new Fx.Tween(row, {duration: 300}).start('color', '#A0A0A0','#000');
		}
	},
	tr_add: function(obj) {
		var t = this;
		t.fireEvent("onAddStart");
		var url = t.options.path + '?op=a';
		t.showLoading();
		var myajax = new Request({
			url: url,
			method: 'get',
			onFailure: function(){
				alert(dsgrid["MSGINSERTFAILED"]);
				t.hideLoading();
			},
			onComplete: function(){
				t.getMetaData();
				if (t.options.pp == t.ppmax && t.data[0] && (t.data[0].length == t.options.pagelength)) 
					t.options.pp = t.ppmax + 1;
				else 
					t.options.pp = t.ppmax;
				t.refresh();
				t.hideLoading();
				t.fireEvent("onAddComplete");
			}
		}).send();
	},
	td_sort: function(obj){
		var t = this;
		var id =  obj.getParent().id.slice(t.container.id.length);
		if (t.options.sortcol == id && t.options.sort == 'a') {
			t.options.sort = 'd';
			obj.getLast().src = t.sort2;
		} else {
			if (t.sortgif) t.sortgif.src = t.sortno;
			t.options.sortcol = id;
			t.options.sort = 'a';
			t.sortgif = obj.getLast();
			t.sortgif.src = t.sort1;
		}
		t.refresh();
	},
	destroySlider: function() {
		var t = this;
		t.divslider.destroy();
		t.divup.destroy();
		t.divdown.destroy();
		t.divslider = t.knob = t.slider = null;
		t.tdslider.removeClass('visible');
	},
	pagepointers: function() {
		var t = this;
		var slider_necessary = (t.num_rows < t.options.pagelength)?false:true;
		t.ppmax = Math.ceil((t.add_allowed?t.num_rows+1:t.num_rows)/t.options.pagelength)-1;

		if (!slider_necessary && t.slider) t.destroySlider();
		if (slider_necessary && t.slider) {
			if (t.ppmax != t.ppmaxold) {
				t.destroySlider();
				t.ppmaxold = t.ppmax;
			}
		}
		if (slider_necessary && !t.slider) {
			//Create slider
			t.tdslider.addClass('visible');
			t.divup = new Element('div', {'class': 'up'}).inject(t.tdslider);
			t.imgup = new Element('img', {'alt':'previous',	'class':'imgup', 'src': t.imgsrcup}).inject(t.divup);
			t.heightdivslider = t.tdslider.getSize().y - (2*(t.divup.getSize().y));
			t.hknob = Math.max(t.heightdivslider / (t.ppmax+1), 20);
			t.divslider = new Element('div', {'class': 'slider','styles':{'height':t.heightdivslider, 'width':t.divup.getSize().x}}).inject(t.tdslider);
			t.knob = new Element('div', {'class': 'knob','styles':{'height':t.hknob}}).inject(t.divslider);
			t.tick = new Element('div', {'id': 'tick', 'text': 11}).inject(t.knob);
			t.tick.setStyle('top', (t.hknob/2) - (t.tick.getSize().y/2));
	        t.slider = new Slider(t.divslider, t.knob, {
	            range: [1, t.ppmax+1],	
	            wheel: true,
	            snap: true,
				mode: 'vertical',
	            onComplete: function(step){				
					if ((t.options.pp+1) != step) {t.options.pp = step-1; t.refresh()}
	            },
				onChange: function(step){
					$('tick').set('text',step);
					t.tick.setStyle('left', (t.knob.getSize().x/2) - (t.tick.getSize().x/2)-1);					
	            }			
	        });
			t.ppmaxold = t.ppmax;
					
			t.divdown = new Element('div', {'class': 'down'}).inject(t.tdslider);
			t.imgdown = new Element('img', {'alt':'next', 'class':'imgdown', 'src': t.imgsrcdown}).inject(t.divdown);

			t.knob.addEvent('mouseenter', function(){this.addClass('knobmouseover');});
			t.knob.addEvent('mouseleave', function(){this.removeClass('knobmouseover');});
			t.divup.addEvent('mouseenter', function(){this.addClass('upmouseover');});
			t.divup.addEvent('mouseleave', function(){this.removeClass('upmouseover');});
			t.divup.addEvent('click', function(){if((t.options.pp)>0){t.options.pp--;t.refresh();}});
			t.divdown.addEvent('mouseenter', function(){this.addClass('downmouseover');});
			t.divdown.addEvent('mouseleave', function(){this.removeClass('downmouseover');});
			t.divdown.addEvent('click', function(){if((t.options.pp)<t.ppmax){t.options.pp++;t.refresh();}});
		}
		if (t.slider) {
			t.slider.set(t.options.pp + 1);
			t.tick.setStyle('left', (t.knob.getSize().x/2) - (t.tick.getSize().x/2)-1);
		}
	},
	//
	// Public functions which can be called from the browser to change grid:
	//
	refresh: function() {
		var t = this;
		var start = t.options.pp * t.options.pagelength;
		var end   = start + t.options.pagelength;
		var url = t.options.path+'?op=v&start='+start+'&end='+end+'&cols='+t.cols+'&sortcol='+t.options.sortcol+'&sort='+t.options.sort;
		t.showLoading();
		var myajax = new Request({
			url: url,
			method: 'get',
			onFailure: function(){alert(dsgrid["MSGREFRESHFAILED"]);},	
			onComplete: function(responseText){
				t.data = JSON.decode(responseText);				
				t.pagepointers();
				var values = t.data[1];
				var addrowdone = false;
				for (var i=0; i<t.options.pagelength; i++) {
					var row = t.container.getElementById(t.container.id+'row'+i);
					row.ddempty = (values && (i < values.length))? false : true;
					
					// Selected row
					if (!row.ddempty && t.data[0] && (t.rowselected == t.data[0][i]))
						row.addClass('rowselected');
					else
						row.removeClass('rowselected');
					
					// Delete or Add button or nothing
					var img = row.getFirst().getFirst();
					img.removeEvents();
					img.src = t.delempty;
					img.setStyle('cursor', null);									
					if (!row.ddempty && t.delete_allowed) {
						img.src = t.del1;
						img.addEvent('click', function(){t.tr_del(this)});
						img.setProperty('title', dsgrid["MSGDELETEROW"]);
						img.addEvent('mouseover', function(){this.src = t.del2;});
						img.addEvent('mouseout', function(){this.src = t.del1;});
						img.setStyle('cursor', 'pointer');
					} 
					if (row.ddempty && t.add_allowed && !addrowdone) {
						img.src = t.add1;
						img.addEvent('click', function(){t.tr_add(this)});
						img.setProperty('title', dsgrid["MSGADDROW"]);
						img.addEvent('mouseover', function(){this.src = t.add2;});
						img.addEvent('mouseout', function(){this.src = t.add1;});
						img.setStyle('cursor', 'pointer');
						addrowdone = true;		
					}
						
					for (var j=0; j<t.columns.length; j++) {
						var el = t.container.getElementById('divtd'+i+':'+t.columns[j].name);
						el.empty();
						if (el.getNext()) el.getNext().dispose();
						if (row.ddempty) continue;
						var td = el.getParent().getParent();
						td.ddid = i;
						td.ddsqlid = t.data[0][i];
						td.ddempty = (values && (i < values.length) && values[i][j])? false : true;
						if (td.ddcolumn.type == DDTYPEBOOL) {
							var input = new Element('input', {'type':'checkbox', 'disabled': !td.ddcolumn.editable, 
													'styles': {'height': '13px'}}).inject(el);
							if (!td.ddempty && values[i][j]!=0) input.checked = true;
							if (td.ddcolumn.editable) {
								input.addEvent('change', function(){
									var td = this.getParent().getParent().getParent();
									var value = this.checked?1:0;
									td.ddoldvalue = value?0:1;
									t.UpdateCell(td, value);
								});
							}
						} else {
							if (td.ddcolumn.type == DDTYPEURL)
								el.set('html',(td.ddempty) ? "&nbsp;" : "<a href=\"" + values[i][j] + "\">" + values[i][j] + "</a>");
							else if (td.ddcolumn.type == DDTYPEMAILTO)
								el.set('html',(td.ddempty) ? "&nbsp;" : "<a href=\"mailto:" + values[i][j] + "\">" + values[i][j] + "</a>");
							else 
								el.set('html',(td.ddempty) ? "&nbsp;" : (values[i][j]));
							if (td.ddcolumn.editable) {
								var img = new Element('img', {'src': t.edit, 'class': 'imgedit'}).inject(td.getLast());
								img.addEvent('mouseover', function(){
									var row = this.getParent().getParent().getParent();
									if (!row.ddempty) this.src = t.editmo;
								});
								img.addEvent('mouseout', function(){this.src = t.edit;});
								img.addEvent('click', function(){
									var td = this.getParent().getParent();	
									var row = td.getParent();							
									if (!row.ddempty) {
										this.src = t.edit;
										t.create_form(td);
									}
								});
								img.setProperty('title', dsgrid["MSGCLICKEDIT"]);					
							}
						}																
					}
				}
				t.hideLoading();
			}
		}).send();
	},
	SelectPagePointer: function(pp) {
		var t = this;
		t.options.pp = pp;
		t.refresh();
	},
	DefaultOnClick: function(id) {
		var t = this;
		var myajax = new Request({
			url: t.options.path,
			async: false,
			method: 'get',
			onFailure: function(){return(-1);},
			onSuccess : function(responseText){
				t.rownr = JSON.decode(responseText);
				if (t.rownr == -1) return(-1);
				t.rowselected = id;
				t.options.pp = Math.floor(t.rownr/t.options.pagelength);
				t.refresh();
				return(0);
			}
		}).send('op=vrn&id='+id);		
	},
	DefaultOnDeleteStart: function(id) {
		var t = this;
		if (!window.confirm(dsgrid["MSGREALLYDELETE"])) t.do_delete = false;
	}
});
drasticGrid.implement(new Options, new Events);