/**
*
*  AJAX IFRAME METHOD (AIM)
*  http://www.webtoolkit.info/
*
**/

webtoolkit = window.webtoolkit || {};
webtoolkit.Aim = {

	disposable: [],

	cleanup: function () {
		var Aim = webtoolkit.Aim;
		if (Aim.disposable.length) {
			for (var i in Aim.disposable) {
				Aim.disposable[i].parentNode.removeChild(Aim.disposable[i]);
			}
			Aim.disposable = [];
		}
	},

	frame : function(c) {
		webtoolkit.Aim.cleanup();
		var n = 'f' + Math.floor(Math.random() * 99999);
		var d = document.createElement('DIV');
		d.innerHTML = '<iframe style="display:none" src="about:blank" id="'+n+'" name="'+n+'" onload="webtoolkit.Aim.loaded(\''+n+'\')"></iframe>';
		document.body.appendChild(d);

		var i = document.getElementById(n);
		if (c && typeof(c.onComplete) == 'function') {
			i.onComplete = c.onComplete;
		}

		return n;
	},

	form : function(f, name) {
		f.setAttribute('target', name);
	},

	submit : function(f, c) {
		webtoolkit.Aim.form(f, webtoolkit.Aim.frame(c));
		if (c && typeof(c.onStart) == 'function') {
			return c.onStart();
		} else {
			return true;
		}
	},

	loaded : function(id) {
		var i = document.getElementById(id);
		if (i.contentDocument) {
			var d = i.contentDocument;
		} else if (i.contentWindow) {
			var d = i.contentWindow.document;
		} else {
			var d = window.frames[id].document;
		}
		if (d.location.href == 'about:blank') {
			return;
		}

		if (typeof(i.onComplete) == 'function') {
			i.onComplete(d.body.innerHTML);
		}
		webtoolkit.Aim.disposable.push(i.parentNode);
	}

}