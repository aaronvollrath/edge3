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
"MSGREFRESHFAILED": "Failed to refresh cloud"
};
/*
 * drasticCloud
 */
var drasticCloud = new Class ({
    options: {
		path: location.pathname,		// path of the php file to call
		pathimg: "img/",				// path to images		
		nroffonts: 24,					// The number of different font sizes used in the cloud
		
		namecol: null,					// name to show in the cloud
		sizecol: null,					// column to use to resize the items in the cloud
		log: true,						// use a logarithmic distribution (true) or linear (false) (default: true)
		colorcol: null,					// column which should be used to color items
		sortcol: null,					// name of column to sort on initially. Overrules the default of the data source
		sort: null,						// sort ascending (a) or descending (d)? Overrules the default of the data source		

		showmenu: true,					// Show the menu? (default: true)	
		showmenufull: false,			// Show the menu opened (default: false)		
		shownamecol: true,				// Show the name selector? (default: true)		
		showlog: true,					// Show the log/linear selector? (default: true)
		showsizecol: true,				// Show the column selector? (default: true)
		showcolorcol: true, 			// Show the color column selector? (default: true)		
		showsortcol: true,				// Show the sortcolumn selector? (default: true)
		showsort: true,					// Show the ascending / descendig sort selector? (default: true)
		
		onClick: function(id){this.DefaultOnClick(id);},	
		onMouseOver: function(id){this.DefaultOnMouseOver(id);}
    },	
	initialize: function(container, options){
		var t = this;
		t.container 	= $(container) || alert('container '+container+' not found...');
		t.fontsize = parseInt(t.container.getStyle('font-size').slice(0,-2));
		t.setOptions(options);		
		t.getMetaData();
		
		// gifs:
		t.ddPowered = t.options.pathimg+'DDPowered.png';
		t.ddPowered2 = t.options.pathimg+'DDPowered2.png';
	
		// Containers for the menu of the tag cloud:
		if (t.options.showmenu) {
			t.divmenu = new Element('div', {'class': 'drasticcloudmenu'}).inject(t.container);
			t.divmenutitle = new Element('div', {'class': 'drasticcloudmenutitle'}).inject(t.divmenu).appendText('Cloud Menu');				
			t.divmenu.appendText('Show ');
			if (t.options.shownamecol) {
				if (!t.options.namecol) t.options.namecol = t.cols[0];
				var sel = new Element('select').inject(t.divmenu);
				for (var i=0; i< t.cols.length; i++) {
					var opt = new Element('option', {'value': t.cols[i]}).inject(sel);
					opt.set('text', t.cols[i]);
					if (t.options.namecol == t.cols[i]) opt.setProperty('selected', 'selected');
				}
				sel.addEvent('change', function(){t.setnamecol(this)});
			}
			if (t.options.showsizecol) {
				t.divmenu.appendText(' sized by ');
				if (!t.options.sizecol) t.options.sizecol = t.cols[0];
				var sel = new Element('select').inject(t.divmenu);
				for (var i=0; i< t.cols_numeric.length; i++) {
					var opt = new Element('option', {'value': t.cols_numeric[i]}).inject(sel);
					opt.set('text', t.cols_numeric[i]);
					if (t.options.sizecol == t.cols_numeric[i]) opt.setProperty('selected', 'selected');
				}
				sel.addEvent('change', function(){t.setsizecol(this)});
			}
			if (t.options.showlog) {
				t.divmenu.appendText(' scaled ');
				var sel = new Element('select').inject(t.divmenu);
				var opt1 = new Element('option', {'value': 'log'}).inject(sel);
				opt1.set('text', 'log');
				var opt2 = new Element('option', {'value': 'linear'}).inject(sel);
				opt2.set('text', 'linear');
				if (!t.options.log) opt2.setProperty('selected', 'selected'); 
				sel.addEvent('change', function(){t.setscale(this)});
			}			
			if (t.options.showcolorcol) {
				t.divmenu.appendText(' colored by ');
				var sel = new Element('select').inject(t.divmenu);
				var opt = new Element('option', {'value': ' '}).inject(sel);
				opt.set('text', ' ');
				if (!t.options.colorcol) opt.setProperty('selected', 'selected');
				for (var i=0; i< t.cols.length; i++) {
					if (t.cols_numeric.contains(t.cols[i]) ||
						(t.flds.get(t.cols[i]).search("^enum") == 0)) {
						var opt = new Element('option', {'value': t.cols[i]}).inject(sel);
						opt.set('text', t.cols[i]);
						if (t.options.colorcol == t.cols[i]) opt.setProperty('selected', 'selected');
					}
				}
				sel.addEvent('change', function(){t.setcolorcol(this)});
			}		
			if (t.options.showsortcol) {
				t.divmenu.appendText(' sorted on ');
				var sel = new Element('select').inject(t.divmenu);
				for (var i=0; i< t.cols.length; i++) {
					var opt = new Element('option', {'value': t.cols[i]}).inject(sel);
					opt.set('text', t.cols[i]);
					if (t.options.sortcol == t.cols[i]) opt.setProperty('selected', 'selected');
				}
				sel.addEvent('change', function(){t.setsortcol(this)});
			}
			if (t.options.showsort) {
				t.divmenu.appendText(' sorted ');
				var sel = new Element('select').inject(t.divmenu);
				var opt1 = new Element('option', {'value':'a'}).inject(sel);
				opt1.set('text', 'ascending');
				if (t.options.sort == 'a') opt2.setProperty('selected', 'selected'); 
				var opt2 = new Element('option', {'value':'d'}).inject(sel);
				opt2.set('text', 'descending');
				if (t.options.sort == 'd') opt2.setProperty('selected', 'selected'); 
				sel.addEvent('change', function(){t.setsort(this)});
			}				
			t.divmenutoggle = new Element('div', {'class': 'drasticcloudmenutoggle1'}).inject(t.container);
			t.menuToggleSlide = new Fx.Slide(t.divmenu, {duration: 500});
			t.divmenutoggle.addEvent('click', function(){
				t.menuToggleSlide.toggle().chain(function(){
					t.divmenutoggle.toggleClass('drasticcloudmenutoggle2');
				});			
			});
			if (!t.options.showmenufull) {
				t.divmenutoggle.addClass('drasticcloudmenutoggle2');
				t.menuToggleSlide.hide();
			}
		}

		//Container for the tag cloud:
		t.div = new Element('div', {'class': 'drasticcloud'});
		t.div.inject(t.container);
		t.p = new Element('p', {'class': 'drasticcloud'});
		t.p.inject(t.div);

		var div = new Element('div', {styles:{'text-align': 'right'}}).inject(t.container);
		var img = new Element('img', {'title':'Powered by DrasticData', src: t.ddPowered}).inject(div);
		img.addEvent('click', function(){window.open('http://www.drasticdata.nl')});
		img.addEvent('mouseover', function(){this.src = t.ddPowered2;});
		img.addEvent('mouseout', function(){this.src = t.ddPowered;});
		img.setStyle('cursor', 'pointer');		
		
		t.refresh();
	},
	setnamecol: function(obj) {
		var t = this;
		t.options.namecol = obj.value;
		t.refresh();
	},		
	setsizecol: function(obj) {
		var t = this;
		t.options.sizecol = obj.value;
		t.refresh();
	},
	setscale: function(obj) {
		var t = this;
		t.options.log = (obj.value == "log")? true: false;
		t.refresh();
	},
	setcolorcol: function(obj) {
		var t = this;
		t.options.colorcol = (obj.value == ' ')? null: obj.value;
		t.refresh();
	},		
	setsortcol: function(obj) {
		var t = this;
		t.options.sortcol = obj.value;
		t.refresh();
	},
	setsort: function(obj) {
		var t = this;
		t.options.sort = obj.value;
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
			}
		}).send('op=vm');
	},

	//
	// Public functions which can be called from the browser:
	//
	refresh: function() {
		var t = this;
		var url = t.options.path+'?op=v&cols='+t.options.sizecol+','+t.options.namecol+(t.options.colorcol?','+t.options.colorcol:'')+
											'&sortcol='+t.options.sortcol+'&sort='+t.options.sort;
		var myajax = new Request({
			url: url,
			method: 'get',
			onFailure: function(){alert(dsgrid["MSGREFRESHFAILED"]);},	
			onComplete: function(responseText){
				t.data = JSON.decode(responseText);
				var values = t.data[1];
				var min = Number.POSITIVE_INFINITY, max = Number.NEGATIVE_INFINITY;
				var mincolorcol = Number.POSITIVE_INFINITY, maxcolorcol = Number.NEGATIVE_INFINITY;
				for (var i=0; i < t.data[0].length; i++)  {
					var value = parseFloat(values[i][0]);
					min = Math.min(min, value);
					max = Math.max(max, value);
					if (t.options.colorcol) {
						var valuecolorcol = parseFloat(values[i][2]);
						mincolorcol = Math.min(mincolorcol, valuecolorcol);
						maxcolorcol = Math.max(maxcolorcol, valuecolorcol);
					}
				}
				var realmin = min;				
				if (t.options.log) {
					min = (realmin >=1)? Math.log(min): 0;
					max = (realmin >=1)? Math.log(max): Math.log(max+(1-realmin));
				}
				var interval = (max-min) /t.options.nroffonts;
				
				// create the real cloud:
				t.p.empty();
				for (var i=0; i < t.data[0].length; i++)  {
					var id = t.data[0][i];
					var name = values[i][1];
					var value = parseFloat(values[i][0]);
					if (isNaN(value)) continue;
					var div = new Element('div', {'id':t.container.id+'div'+id, 'class':'drasticcloud-div'}).inject(t.p);
					var a = new Element('a', {'id':t.container.id+'a'+id, 'class':'drasticcloud-a'}).inject(div);
					a.set('text', name);
					a.setProperty('title', t.options.sizecol+": "+value);	
					if (t.options.log) value = (realmin >=1)? Math.log(value): Math.log(value+(1-realmin));
					var fontdiff = (value - min) / interval;
					var fs = t.fontsize + fontdiff;
					a.setStyle('font-size', fs);
					if (t.options.colorcol) {
						var fldtype = t.flds.get(t.options.colorcol);
						if (fldtype.search("^enum") == 0) {
							var intv = Math.floor(100/t.flds.getKeys().length);
							var arr = fldtype.split("'");
							for (var j=1; j<arr.length; j+=2) {if (values[i][2] == arr[j]) break;}
							div.setStyle('color', $HSB(j*intv,80,100));
						} else if (fldtype.search("^tinyint") == 0) {
							div.addClass((values[i][2] == 0)?'drasticcloud-div-false':'drasticcloud-div-true');
						} else {							
							mincolorcol = mincolorcol - (0.3*(maxcolorcol-mincolorcol));
							div.setStyle('color', $HSB(30,(values[i][2] - mincolorcol)/(maxcolorcol - mincolorcol)*100,100));							
						}
					}
					
					if (i<t.data[0].length-1) t.p.appendText(' | ');
				}
				el = $(t.container).getElements('a.drasticcloud-a');
				for (var i=0; i< el.length; i++) {
					el[i].addEvent('mouseover', function(){
						var id = this.id.slice(t.container.id.length+1);
						this.addClass('drasticcloud-a-mouseover');
						t.fireEvent("onMouseOver", id);
					});
					el[i].addEvent('mouseout', function(){this.removeClass('drasticcloud-a-mouseover')});
					el[i].addEvent('click', function(){
						var id = this.id.slice(t.container.id.length+1);
						t.fireEvent("onClick", id);
					});					
				}	
				//t.tips = new Tips($$('.drasticcloud-a'), {className: 'drasticcloud-tip'});
			}
		}).send();
	},
	DefaultOnClick: function(id) {
		var t = this;
		$(t.container).getElements('a.drasticcloud-a-selected').removeClass('drasticcloud-a-selected');
		t.container.getElementById(t.container.id+'a'+id).addClass('drasticcloud-a-selected');			
	},
	DefaultOnMouseOver: function(id) {
		var t = this;
	}
});
drasticCloud.implement(new Options, new Events);