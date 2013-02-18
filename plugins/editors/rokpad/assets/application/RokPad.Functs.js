/*
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2012 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
((function(){if(typeof this.RokPadData=="undefined"){this.RokPadData={};}this.RokPadData.insertion={};var d=function(e){if(e.matches){RokPadDevice="portable";
}else{RokPadDevice="desktop";}RokPadData.insertion={onSave:function(f){if(e.matches){return;}document.id(f).set("value",RokPad.editors[f].getValue());},onGetContent:function(f){if(e.matches){return document.id(f).get("value");
}else{return RokPad.editors[f].getValue();}},onSetContent:function(g,f){if(e.matches){document.id(g).set("value",f);}else{RokPad.editors[g].setValue(f);
}},onGetInsertMethod:function(g,f){if(e.matches){insertAtCursor(document.id(f),g);}else{RokPad.editors[f].replaceSelection(g);RokPad.editors[f].getEditor().focus();
}}};};if(respond.mediaQueriesSupported&&!Browser.ie8){var b=window.matchMedia("only screen and (max-device-width: 1024px)");if(b.addListener){b.addListener(d);
}else{window.addEvent("resize",function(){if(this.getSize().x<=1024){b={matches:true};}else{b={matches:false};}d(b);});}d(b);}else{if(Browser.ie8){d({matches:true});
}else{d({});}}if(typeof insertAtCursor!=="function"){this.insertAtCursor=function(h,g){if(document.selection){h.focus();sel=document.selection.createRange();
sel.text=g;}else{if(h.selectionStart||h.selectionStart=="0"){var f=h.selectionStart;var e=h.selectionEnd;h.value=h.value.substring(0,f)+g+h.value.substring(e,h.value.length);
}else{h.value+=g;}}};}Function.implement({bind:function(i){var e=this,f=arguments.length>1?Array.slice(arguments,1):null,h=function(){};var g=function(){var k=i,l=arguments.length;
if(this instanceof g){h.prototype=e.prototype;k=new h();}var j=(!f&&!l)?e.call(k):e.apply(k,f&&l?f.concat(Array.slice(arguments)):f||arguments);return k==i?j:k;
};return g;}});String.implement({indexOfAll:function(f){var h=new RegExp(f,"g");var e,g=[];while((e=h.exec(this))!==null){g.push(e.index);}return g;}});
Object.merge(Element.NativeEvents,{dragstart:2,drag:2,dragenter:2,dragleave:2,dragover:2,drop:2,dragend:2});String.implement({substitute:function(e,f){return String(this).replace(f||(/\\?\{([^{}]+)\}/g),function(h,g){if(h.charAt(0)=="\\"){return h.slice(1);
}return(e[g]!=null)?e[g]:h;});}});RokPadData.modesList=[];RokPadData.modesByName={c9search:["C9Search","c9search_results"],coffee:["CoffeeScript","coffee|^Cakefile"],coldfusion:["ColdFusion","cfm"],csharp:["C#","cs"],css:["CSS","css"],diff:["Diff","diff|patch"],golang:["Go","go"],groovy:["Groovy","groovy"],haxe:["haXe","hx"],html:["HTML","htm|html|xhtml"],c_cpp:["C/C++","c|cc|cpp|cxx|h|hh|hpp"],clojure:["Clojure","clj"],java:["Java","java"],javascript:["JavaScript","js"],json:["JSON","json"],jsx:["JSX","jsx"],latex:["LaTeX","latex|tex|ltx|bib"],less:["LESS","less"],liquid:["Liquid","liquid"],lua:["Lua","lua"],luapage:["LuaPage","lp"],markdown:["Markdown","md|markdown"],ocaml:["OCaml","ml|mli"],perl:["Perl","pl|pm"],pgsql:["pgSQL","pgsql"],php:["PHP","php|phtml"],powershell:["Powershell","ps1"],python:["Python","py"],ruby:["Ruby","ru|gemspec|rake|rb"],scad:["OpenSCAD","scad"],scala:["Scala","scala"],scss:["SCSS","scss|sass"],sh:["SH","sh|bash|bat"],sql:["SQL","sql"],svg:["SVG","svg"],text:["Text","txt"],textile:["Textile","textile"],xml:["XML","xml|rdf|rss|wsdl|xslt|atom|mathml|mml|xul|xbl"],xquery:["XQuery","xq"],yaml:["YAML","yaml"]};
for(var a in RokPadData.modesByName){var c=RokPadData.modesByName[a];c.push(a);RokPadData.modesList.push(c);}window.addEvent("domready",function(){document.getElements(".rokpad-tip").twipsy({placement:"left",offset:5});
var e=Browser.Platform.mac?"mac":"win";document.getElements(".rokpad-keyboard-"+e).setStyle("display","block");document.getElements(".rokpad-kbd-"+e).setStyle("display","inline-block");
this.RokPad=new RokPadClass();if(Browser.ie8){document.getElements(".rokpad-editor-wrapper").addClass("rokpad-ie8");}}.bind(this));})());