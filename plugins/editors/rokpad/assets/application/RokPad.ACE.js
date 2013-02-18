/*
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2012 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
((function(){var a=new Class({Implements:[Options,Events],options:{wrapper:null,container:null,id:null,onChange:function(b){var c=this.editor.getSession().getUndoManager();
["undo","redo"].each(function(d){var f="[data-rokpad-"+d+"]",g="[class*=rok-button-disabled]",e=":not("+g+")";if(c["has"+d.capitalize()]()){this.wrapper.getElements(f+g).removeClass("rok-button-disabled");
}else{this.wrapper.getElements(f+e).addClass("rok-button-disabled");}},this);}},initialize:function(c,b){this.setOptions(b);this.wrapper=b.wrapper?document.id(b.wrapper)||document.getElement(b.wrapper)||null:null;
this.container=b.container?document.id(b.container)||document.getElement(b.container)||null:null;this.textarea=document.id(c)||document.getElement(c)||null;
if(!this.container){throw new Error('Container for injecting ACE "'+b.container+'" not found in the DOM.');}if(!this.wrapper){throw new Error('Wrapper "'+b.wrapper+'" not found in the DOM.');
}if(!this.textarea){throw new Error('Original textarea "'+b.textarea+'" not found in the DOM.');}if(this.textarea.getParent("form")){this.textarea.inject(this.textarea.getParent("form"));
}if(!matchMedia("(max-device-width: 1024px)").matches){this.textarea.setStyle("display","none");}this.editor=ace.edit(this.container);this.insert(this.textarea.get("value"));
var d=this.editor.getSession().getUndoManager();d.reset.delay(1,d);this.attachEvents();this.container.getElement(".ace_layer.ace_text-layer").set("contenteditable","");
return this;},getEditor:function(){return this.editor;},attachEvents:function(){var b=this.options;this.textarea.addEvent("blur",function(){this.setValue(this.textarea.get("value"));
}.bind(this));this.editor.on("blur",function(){this.textarea.set("value",this.getValue());}.bind(this));this.editor.on("change",function(c){this.fireEvent("change",c,1);
}.bind(this));document.id(this.editor.renderer.scroller).addEvents({click:function(){this.editor.blur();this.editor.focus();}.bind(this),scroll:function(){var d=this.editor.renderer,e=d.$gutter,c=d.scroller,f=c.scrollLeft;
document.id(e)[f<5?"removeClass":"addClass"]("horscroll");}.bind(this)});this.editor.commands.addCommand({name:"find",bindKey:{win:"Ctrl-F",mac:"Command-F|Command-Alt-F"},exec:function(d){var c=this.wrapper.getElement("[data-rokpad-action-method=find] input");
RokPad._setActionbar(b.id,"find");RokPad._showActionbar(b.id);c.select();c.focus();}.bind(this)});this.editor.commands.addCommand({name:"findreplace",bindKey:{win:"Ctrl-Shift-F",mac:"Command-Shift-F"},exec:function(d){var c=this.wrapper.getElement("[data-rokpad-action-method=find] input");
RokPad._setActionbar(b.id,"replace");RokPad._showActionbar(b.id);c.select();c.focus();}.bind(this)});this.editor.commands.addCommand({name:"gotoline",bindKey:{win:"Ctrl-L",mac:"Command-L"},exec:function(d){var c=this.wrapper.getElement("[data-rokpad-action-method=goto] input");
RokPad._setActionbar(b.id,"goto");RokPad._showActionbar(b.id);c.select();c.focus();}.bind(this)});this.editor.commands.addCommand({name:"findnext",bindKey:{win:"Ctrl-K",mac:"Command-G"},exec:function(d){var c=RokPad._getRange(b.id,RokPad._retrieve("actionSettings"));
d.findNext(c);}.bind(this)});this.editor.commands.addCommand({name:"findprevious",bindKey:{win:"Ctrl-Shift-K",mac:"Command-Shift-G"},exec:function(d){var c=RokPad._getRange(b.id,RokPad._retrieve("actionSettings"));
d.findPrevious(c);}.bind(this)});this.editor.commands.addCommand({name:"escape",bindKey:{win:"Esc",mac:"Esc"},exec:function(c){RokPad._hideActionbar(b.id);
}});this.editor.commands.addCommand({name:"save",bindKey:{win:"Ctrl-S",mac:"Command-S"},exec:function(c){RokPad.save();}});this.editor.commands.addCommand({name:"findselection",bindKey:{win:"Ctrl-E",mac:"Command-E"},exec:function(f){var c=this.wrapper.getElement("[data-rokpad-action-method=find] input"),g=this.wrapper.getElement("[data-rokpad-action=find]"),e,d=RokPad._retrieve("actionSettings");
f.selection.selectWord();e=RokPad.editors[b.id].getSelection();RokPad._setActionbar(b.id,"find");RokPad._showActionbar(b.id);c.set("value",e);d.needle=e;
RokPad._store("actionSettings",d);c.select();c.focus();g.fireEvent("click");}.bind(this)});},insert:function(b){this.editor.insert(b);return this;},getValue:function(){return this.editor.getSession().getValue();
},setValue:function(b){this.editor.getSession().setValue(b);return this;},getSelection:function(){return this.editor.getSession().getTextRange(this.editor.getSelectionRange());
},setTheme:function(c){var b=this.editor;if(!c){throw new Error('Second argument "theme" of ACE::setTheme must be passed in.');}if(!c.match(/^ace\/theme/)){c="ace/theme/"+c;
}b.setTheme(c);return this;},setFontSize:function(c){var b=this.editor;if(!c){throw new Error('Second argument "fontsize" of ACE::setFontSize must be passed in.');
}if(typeof c=="number"){c+="px";}b.setFontSize(c);return this;},setMode:function(c){var b=this.editor;if(!c){throw new Error('Second argument "mode" of ACE::setMode must be passed in.');
}b.getSession().setMode(c);return this;},setUseSoftTabs:function(c){var b=this.editor;b.getSession().setUseSoftTabs(c);return this;},replaceSelection:function(i){var g=this.editor,h=g.getSession(),f,c,e,d=[],b=[];
c=Array.from(g.getSelection()[g.getSelection().inMultiSelectMode?"getAllRanges":"getRange"]());c.each(function(j,k){f=h.getTextRange(j);e=f?i.replace(/\{text\}/g,f):i;
h.replace(j,e);},this);}});this.RokPadACE=a;})());