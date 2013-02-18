/*
 * Copyright (c) 2010, Ajax.org B.V.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of Ajax.org B.V. nor the
 *       names of its contributors may be used to endorse or promote products
 *       derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL AJAX.ORG B.V. BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */
"no use strict";var console={log:function(a){postMessage({type:"log",data:a});}};var window={console:console};var normalizeModule=function(e,a){if(a.indexOf("!")!==-1){var d=a.split("!");
return normalizeModule(e,d[0])+"!"+normalizeModule(e,d[1]);}if(a.charAt(0)=="."){var c=e.split("/").slice(0,-1).join("/");var a=c+"/"+a;while(a.indexOf(".")!==-1&&b!=a){var b=a;
var a=a.replace(/\/\.\//,"/").replace(/[^\/]+\/\.\.\//,"");}}return a;};var require=function(e,d){var d=normalizeModule(e,d);var a=require.modules[d];if(a){if(!a.initialized){a.initialized=true;
a.exports=a.factory().exports;}return a.exports;}var c=d.split("/");c[0]=require.tlns[c[0]]||c[0];var b=c.join("/")+".js";require.id=d;importScripts(b);
return require(e,d);};require.modules={};require.tlns={};var define=function(d,c,a){if(arguments.length==2){a=c;if(typeof d!="string"){c=d;d=require.id;
}}else{if(arguments.length==1){a=d;d=require.id;}}if(d.indexOf("text!")===0){return;}var b=function(f,e){return require(d,f,e);};require.modules[d]={factory:function(){var e={exports:{}};
var f=a(b,e.exports,e);if(f){e.exports=f;}return e;}};};function initBaseUrls(a){require.tlns=a;}function initSender(){var c=require(null,"ace/lib/event_emitter").EventEmitter;
var b=require(null,"ace/lib/oop");var a=function(){};(function(){b.implement(this,c);this.callback=function(e,d){postMessage({type:"call",id:d,data:e});
};this.emit=function(d,e){postMessage({type:"event",name:d,data:e});};}).call(a.prototype);return new a();}var main;var sender;onmessage=function(b){var c=b.data;
if(c.command){main[c.command].apply(main,c.args);}else{if(c.init){initBaseUrls(c.tlns);require(null,"ace/lib/fixoldbrowsers");sender=initSender();var a=require(null,c.module)[c.classname];
main=new a(sender);}else{if(c.event&&sender){sender._emit(c.event,c.data);}}}};
/*
    Copyright (c) 2009, 280 North Inc. http://280north.com/
    MIT License. http://github.com/280north/narwhal/blob/master/README.md
*/
define("ace/lib/fixoldbrowsers",["require","exports","module","ace/lib/regexp","ace/lib/es5-shim"],function(b,a,c){b("./regexp");
b("./es5-shim");});define("ace/lib/regexp",["require","exports","module"],function(b,a,c){var h={exec:RegExp.prototype.exec,test:RegExp.prototype.test,match:String.prototype.match,replace:String.prototype.replace,split:String.prototype.split},f=h.exec.call(/()??/,"")[1]===undefined,e=function(){var i=/^/g;
h.test.call(i,"");return !i.lastIndex;}();if(e&&f){return;}RegExp.prototype.exec=function(n){var l=h.exec.apply(this,arguments),k,j;if(typeof(n)=="string"&&l){if(!f&&l.length>1&&d(l,"")>-1){j=RegExp(this.source,h.replace.call(g(this),"g",""));
h.replace.call(n.slice(l.index),j,function(){for(var o=1;o<arguments.length-2;o++){if(arguments[o]===undefined){l[o]=undefined;}}});}if(this._xregexp&&this._xregexp.captureNames){for(var m=1;
m<l.length;m++){k=this._xregexp.captureNames[m-1];if(k){l[k]=l[m];}}}if(!e&&this.global&&!l[0].length&&(this.lastIndex>l.index)){this.lastIndex--;}}return l;
};if(!e){RegExp.prototype.test=function(j){var i=h.exec.call(this,j);if(i&&this.global&&!i[0].length&&(this.lastIndex>i.index)){this.lastIndex--;}return !!i;
};}function g(i){return(i.global?"g":"")+(i.ignoreCase?"i":"")+(i.multiline?"m":"")+(i.extended?"x":"")+(i.sticky?"y":"");}function d(m,k,l){if(Array.prototype.indexOf){return m.indexOf(k,l);
}for(var j=l||0;j<m.length;j++){if(m[j]===k){return j;}}return -1;}});
/*
    Copyright (c) 2009, 280 North Inc. http://280north.com/
    MIT License. http://github.com/280north/narwhal/blob/master/README.md
*/
define("ace/lib/es5-shim",["require","exports","module"],function(g,ad,e){if(!Function.prototype.bind){Function.prototype.bind=function i(al){var am=this;
if(typeof am!="function"){throw new TypeError();}var aj=u.call(arguments,1);var ak=function(){if(this instanceof ak){var ap=function(){};ap.prototype=am.prototype;
var ao=new ap;var an=am.apply(ao,aj.concat(u.call(arguments)));if(an!==null&&Object(an)===an){return an;}return ao;}else{return am.apply(al,aj.concat(u.call(arguments)));
}};return ak;};}var c=Function.prototype.call;var K=Array.prototype;var z=Object.prototype;var u=K.slice;var h=c.bind(z.toString);var T=c.bind(z.hasOwnProperty);
var ab;var ah;var aa;var af;var q;if((q=T(z,"__defineGetter__"))){ab=c.bind(z.__defineGetter__);ah=c.bind(z.__defineSetter__);aa=c.bind(z.__lookupGetter__);
af=c.bind(z.__lookupSetter__);}if(!Array.isArray){Array.isArray=function C(aj){return h(aj)=="[object Array]";};}if(!Array.prototype.forEach){Array.prototype.forEach=function d(aj){var ak=L(this),am=arguments[1],al=0,an=ak.length>>>0;
if(h(aj)!="[object Function]"){throw new TypeError();}while(al<an){if(al in ak){aj.call(am,ak[al],al,ak);}al++;}};}if(!Array.prototype.map){Array.prototype.map=function J(ak){var al=L(this),ao=al.length>>>0,aj=Array(ao),an=arguments[1];
if(h(ak)!="[object Function]"){throw new TypeError();}for(var am=0;am<ao;am++){if(am in al){aj[am]=ak.call(an,al[am],am,al);}}return aj;};}if(!Array.prototype.filter){Array.prototype.filter=function Q(ak){var al=L(this),ao=al.length>>>0,aj=[],an=arguments[1];
if(h(ak)!="[object Function]"){throw new TypeError();}for(var am=0;am<ao;am++){if(am in al&&ak.call(an,al[am],am,al)){aj.push(al[am]);}}return aj;};}if(!Array.prototype.every){Array.prototype.every=function O(aj){var ak=L(this),an=ak.length>>>0,am=arguments[1];
if(h(aj)!="[object Function]"){throw new TypeError();}for(var al=0;al<an;al++){if(al in ak&&!aj.call(am,ak[al],al,ak)){return false;}}return true;};}if(!Array.prototype.some){Array.prototype.some=function X(aj){var ak=L(this),an=ak.length>>>0,am=arguments[1];
if(h(aj)!="[object Function]"){throw new TypeError();}for(var al=0;al<an;al++){if(al in ak&&aj.call(am,ak[al],al,ak)){return true;}}return false;};}if(!Array.prototype.reduce){Array.prototype.reduce=function r(ak){var al=L(this),an=al.length>>>0;
if(h(ak)!="[object Function]"){throw new TypeError();}if(!an&&arguments.length==1){throw new TypeError();}var am=0;var aj;if(arguments.length>=2){aj=arguments[1];
}else{do{if(am in al){aj=al[am++];break;}if(++am>=an){throw new TypeError();}}while(true);}for(;am<an;am++){if(am in al){aj=ak.call(void 0,aj,al[am],am,al);
}}return aj;};}if(!Array.prototype.reduceRight){Array.prototype.reduceRight=function B(ak){var al=L(this),an=al.length>>>0;if(h(ak)!="[object Function]"){throw new TypeError();
}if(!an&&arguments.length==1){throw new TypeError();}var aj,am=an-1;if(arguments.length>=2){aj=arguments[1];}else{do{if(am in al){aj=al[am--];break;}if(--am<0){throw new TypeError();
}}while(true);}do{if(am in this){aj=ak.call(void 0,aj,al[am],am,al);}}while(am--);return aj;};}if(!Array.prototype.indexOf){Array.prototype.indexOf=function w(ak){var aj=L(this),am=aj.length>>>0;
if(!am){return -1;}var al=0;if(arguments.length>1){al=U(arguments[1]);}al=al>=0?al:Math.max(0,am+al);for(;al<am;al++){if(al in aj&&aj[al]===ak){return al;
}}return -1;};}if(!Array.prototype.lastIndexOf){Array.prototype.lastIndexOf=function S(ak){var aj=L(this),am=aj.length>>>0;if(!am){return -1;}var al=am-1;
if(arguments.length>1){al=Math.min(al,U(arguments[1]));}al=al>=0?al:am-Math.abs(al);for(;al>=0;al--){if(al in aj&&ak===aj[al]){return al;}}return -1;};
}if(!Object.getPrototypeOf){Object.getPrototypeOf=function ae(aj){return aj.__proto__||(aj.constructor?aj.constructor.prototype:z);};}if(!Object.getOwnPropertyDescriptor){var b="Object.getOwnPropertyDescriptor called on a non-object: ";
Object.getOwnPropertyDescriptor=function A(al,am){if((typeof al!="object"&&typeof al!="function")||al===null){throw new TypeError(b+al);}if(!T(al,am)){return;
}var an,aj,ao;an={enumerable:true,configurable:true};if(q){var ak=al.__proto__;al.__proto__=z;var aj=aa(al,am);var ao=af(al,am);al.__proto__=ak;if(aj||ao){if(aj){an.get=aj;
}if(ao){an.set=ao;}return an;}}an.value=al[am];return an;};}if(!Object.getOwnPropertyNames){Object.getOwnPropertyNames=function R(aj){return Object.keys(aj);
};}if(!Object.create){Object.create=function k(al,am){var ak;if(al===null){ak={__proto__:null};}else{if(typeof al!="object"){throw new TypeError("typeof prototype["+(typeof al)+"] != 'object'");
}var aj=function(){};aj.prototype=al;ak=new aj();ak.__proto__=al;}if(am!==void 0){Object.defineProperties(ak,am);}return ak;};}function E(aj){try{Object.defineProperty(aj,"sentinel",{});
return"sentinel" in aj;}catch(ak){}}if(Object.defineProperty){var j=E({});var G=typeof document=="undefined"||E(document.createElement("div"));if(!j||!G){var D=Object.defineProperty;
}}if(!Object.defineProperty||D){var f="Property description must be an object: ";var V="Object.defineProperty called on non-object: ";var p="getters & setters can not be defined on this javascript engine";
Object.defineProperty=function Y(ak,am,an){if((typeof ak!="object"&&typeof ak!="function")||ak===null){throw new TypeError(V+ak);}if((typeof an!="object"&&typeof an!="function")||an===null){throw new TypeError(f+an);
}if(D){try{return D.call(Object,ak,am,an);}catch(al){}}if(T(an,"value")){if(q&&(aa(ak,am)||af(ak,am))){var aj=ak.__proto__;ak.__proto__=z;delete ak[am];
ak[am]=an.value;ak.__proto__=aj;}else{ak[am]=an.value;}}else{if(!q){throw new TypeError(p);}if(T(an,"get")){ab(ak,am,an.get);}if(T(an,"set")){ah(ak,am,an.set);
}}return ak;};}if(!Object.defineProperties){Object.defineProperties=function M(aj,ak){for(var al in ak){if(T(ak,al)){Object.defineProperty(aj,al,ak[al]);
}}return aj;};}if(!Object.seal){Object.seal=function W(aj){return aj;};}if(!Object.freeze){Object.freeze=function t(aj){return aj;};}try{Object.freeze(function(){});
}catch(m){Object.freeze=(function t(ak){return function aj(al){if(typeof al=="function"){return al;}else{return ak(al);}};})(Object.freeze);}if(!Object.preventExtensions){Object.preventExtensions=function N(aj){return aj;
};}if(!Object.isSealed){Object.isSealed=function ai(aj){return false;};}if(!Object.isFrozen){Object.isFrozen=function Z(aj){return false;};}if(!Object.isExtensible){Object.isExtensible=function s(ak){if(Object(ak)===ak){throw new TypeError();
}var aj="";while(T(ak,aj)){aj+="?";}ak[aj]=true;var al=T(ak,aj);delete ak[aj];return al;};}if(!Object.keys){var o=true,v=["toString","toLocaleString","valueOf","hasOwnProperty","isPrototypeOf","propertyIsEnumerable","constructor"],l=v.length;
for(var x in {toString:null}){o=false;}Object.keys=function I(al){if((typeof al!="object"&&typeof al!="function")||al===null){throw new TypeError("Object.keys called on a non-object");
}var ao=[];for(var ak in al){if(T(al,ak)){ao.push(ak);}}if(o){for(var am=0,an=l;am<an;am++){var aj=v[am];if(T(al,aj)){ao.push(aj);}}}return ao;};}if(!Date.prototype.toISOString||(new Date(-62198755200000).toISOString().indexOf("-000001")===-1)){Date.prototype.toISOString=function n(){var aj,al,am,ak;
if(!isFinite(this)){throw new RangeError;}aj=[this.getUTCMonth()+1,this.getUTCDate(),this.getUTCHours(),this.getUTCMinutes(),this.getUTCSeconds()];ak=this.getUTCFullYear();
ak=(ak<0?"-":(ak>9999?"+":""))+("00000"+Math.abs(ak)).slice(0<=ak&&ak<=9999?-4:-6);al=aj.length;while(al--){am=aj[al];if(am<10){aj[al]="0"+am;}}return ak+"-"+aj.slice(0,2).join("-")+"T"+aj.slice(2).join(":")+"."+("000"+this.getUTCMilliseconds()).slice(-3)+"Z";
};}if(!Date.now){Date.now=function ac(){return new Date().getTime();};}if(!Date.prototype.toJSON){Date.prototype.toJSON=function P(aj){if(typeof this.toISOString!="function"){throw new TypeError();
}return this.toISOString();};}if(Date.parse("+275760-09-13T00:00:00.000Z")!==8640000000000000){Date=(function(al){var aj=function aj(aq,aw,ao,av,au,ax,ap){var ar=arguments.length;
if(this instanceof al){var at=ar==1&&String(aq)===aq?new al(aj.parse(aq)):ar>=7?new al(aq,aw,ao,av,au,ax,ap):ar>=6?new al(aq,aw,ao,av,au,ax):ar>=5?new al(aq,aw,ao,av,au):ar>=4?new al(aq,aw,ao,av):ar>=3?new al(aq,aw,ao):ar>=2?new al(aq,aw):ar>=1?new al(aq):new al();
at.constructor=aj;return at;}return al.apply(this,arguments);};var am=new RegExp("^(\\d{4}|[+-]\\d{6})(?:-(\\d{2})(?:-(\\d{2})(?:T(\\d{2}):(\\d{2})(?::(\\d{2})(?:\\.(\\d{3}))?)?(?:Z|(?:([-+])(\\d{2}):(\\d{2})))?)?)?)?$");
for(var ak in al){aj[ak]=al[ak];}aj.now=al.now;aj.UTC=al.UTC;aj.prototype=al.prototype;aj.prototype.constructor=aj;aj.parse=function an(ar){var aq=am.exec(ar);
if(aq){aq.shift();for(var at=1;at<7;at++){aq[at]=+(aq[at]||(at<3?1:0));if(at==1){aq[at]--;}}var ap=+aq.pop(),aw=+aq.pop(),ao=aq.pop();var av=0;if(ao){if(aw>23||ap>59){return NaN;
}av=(aw*60+ap)*60000*(ao=="+"?-1:1);}var au=+aq[0];if(0<=au&&au<=99){aq[0]=au+400;return al.UTC.apply(this,aq)+av-12622780800000;}return al.UTC.apply(this,aq)+av;
}return al.parse.apply(this,arguments);};return aj;})(Date);}var ag="\x09\x0A\x0B\x0C\x0D\x20\xA0\u1680\u180E\u2000\u2001\u2002\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200A\u202F\u205F\u3000\u2028\u2029\uFEFF";
if(!String.prototype.trim||ag.trim()){ag="["+ag+"]";var F=new RegExp("^"+ag+ag+"*"),y=new RegExp(ag+ag+"*$");String.prototype.trim=function a(){return String(this).replace(F,"").replace(y,"");
};}var U=function(aj){aj=+aj;if(aj!==aj){aj=0;}else{if(aj!==0&&aj!==(1/0)&&aj!==-(1/0)){aj=(aj>0||-1)*Math.floor(Math.abs(aj));}}return aj;};var H="a"[0]!="a",L=function(aj){if(aj==null){throw new TypeError();
}if(H&&typeof aj=="string"&&aj){return aj.split("");}return Object(aj);};});define("ace/lib/event_emitter",["require","exports","module"],function(b,a,c){var d={};
d._emit=d._dispatchEvent=function(f,k){this._eventRegistry=this._eventRegistry||{};this._defaultHandlers=this._defaultHandlers||{};var j=this._eventRegistry[f]||[];
var g=this._defaultHandlers[f];if(!j.length&&!g){return;}if(typeof k!="object"||!k){k={};}if(!k.type){k.type=f;}if(!k.stopPropagation){k.stopPropagation=function(){this.propagationStopped=true;
};}if(!k.preventDefault){k.preventDefault=function(){this.defaultPrevented=true;};}for(var h=0;h<j.length;h++){j[h](k);if(k.propagationStopped){break;}}if(g&&!k.defaultPrevented){return g(k);
}};d.setDefaultHandler=function(e,f){this._defaultHandlers=this._defaultHandlers||{};if(this._defaultHandlers[e]){throw new Error("The default handler for '"+e+"' is already set");
}this._defaultHandlers[e]=f;};d.on=d.addEventListener=function(e,g){this._eventRegistry=this._eventRegistry||{};var f=this._eventRegistry[e];if(!f){f=this._eventRegistry[e]=[];
}if(f.indexOf(g)==-1){f.push(g);}};d.removeListener=d.removeEventListener=function(e,h){this._eventRegistry=this._eventRegistry||{};var g=this._eventRegistry[e];
if(!g){return;}var f=g.indexOf(h);if(f!==-1){g.splice(f,1);}};d.removeAllListeners=function(e){if(this._eventRegistry){this._eventRegistry[e]=[];}};a.EventEmitter=d;
});define("ace/lib/oop",["require","exports","module"],function(b,a,c){a.inherits=(function(){var d=function(){};return function(f,e){d.prototype=e.prototype;
f.super_=e.prototype;f.prototype=new d();f.prototype.constructor=f;};}());a.mixin=function(f,d){for(var e in d){f[e]=d[e];}};a.implement=function(e,d){a.mixin(e,d);
};});define("ace/mode/json_worker",["require","exports","module","ace/lib/oop","ace/worker/mirror","ace/mode/json/json_parse"],function(b,a,c){var e=b("../lib/oop");
var f=b("../worker/mirror").Mirror;var g=b("./json/json_parse");var d=a.JsonWorker=function(h){f.call(this,h);this.setTimeout(200);};e.inherits(d,f);(function(){this.onUpdate=function(){var i=this.doc.getValue();
try{var h=g(i);}catch(j){var k=this.charToDocumentPosition(j.at-1);this.sender.emit("error",{row:k.row,column:k.column,text:j.message,type:"error"});return;
}this.sender.emit("ok");};this.charToDocumentPosition=function(o){var m=0;var j=this.doc.getLength();var l=this.doc.getNewLineCharacter().length;if(!j){return{row:0,column:0};
}var n=0;while(m<j){var k=this.doc.getLine(m);var h=k.length+l;if(n+h>o){return{row:m,column:o-n};}n+=h;m+=1;}return{row:m-1,column:k.length};};}).call(d.prototype);
});define("ace/worker/mirror",["require","exports","module","ace/document","ace/lib/lang"],function(c,b,d){var a=c("../document").Document;var f=c("../lib/lang");
var e=b.Mirror=function(h){this.sender=h;var j=this.doc=new a("");var i=this.deferredUpdate=f.deferredCall(this.onUpdate.bind(this));var g=this;h.on("change",function(k){j.applyDeltas([k.data]);
i.schedule(g.$timeout);});};(function(){this.$timeout=500;this.setTimeout=function(g){this.$timeout=g;};this.setValue=function(g){this.doc.setValue(g);
this.deferredUpdate.schedule(this.$timeout);};this.getValue=function(g){this.sender.callback(this.doc.getValue(),g);};this.onUpdate=function(){};}).call(e.prototype);
});define("ace/document",["require","exports","module","ace/lib/oop","ace/lib/event_emitter","ace/range","ace/anchor"],function(d,c,e){var g=d("./lib/oop");
var f=d("./lib/event_emitter").EventEmitter;var h=d("./range").Range;var a=d("./anchor").Anchor;var b=function(i){this.$lines=[];if(i.length==0){this.$lines=[""];
}else{if(Array.isArray(i)){this.insertLines(0,i);}else{this.insert({row:0,column:0},i);}}};(function(){g.implement(this,f);this.setValue=function(j){var i=this.getLength();
this.remove(new h(0,0,i,this.getLine(i-1).length));this.insert({row:0,column:0},j);};this.getValue=function(){return this.getAllLines().join(this.getNewLineCharacter());
};this.createAnchor=function(j,i){return new a(this,j,i);};if("aaa".split(/a/).length==0){this.$split=function(i){return i.replace(/\r\n|\r/g,"\n").split("\n");
};}else{this.$split=function(i){return i.split(/\r\n|\r|\n/);};}this.$detectNewLine=function(j){var i=j.match(/^.*?(\r\n|\r|\n)/m);if(i){this.$autoNewLine=i[1];
}else{this.$autoNewLine="\n";}};this.getNewLineCharacter=function(){switch(this.$newLineMode){case"windows":return"\r\n";case"unix":return"\n";case"auto":return this.$autoNewLine;
}};this.$autoNewLine="\n";this.$newLineMode="auto";this.setNewLineMode=function(i){if(this.$newLineMode===i){return;}this.$newLineMode=i;};this.getNewLineMode=function(){return this.$newLineMode;
};this.isNewLine=function(i){return(i=="\r\n"||i=="\r"||i=="\n");};this.getLine=function(i){return this.$lines[i]||"";};this.getLines=function(j,i){return this.$lines.slice(j,i+1);
};this.getAllLines=function(){return this.getLines(0,this.getLength());};this.getLength=function(){return this.$lines.length;};this.getTextRange=function(j){if(j.start.row==j.end.row){return this.$lines[j.start.row].substring(j.start.column,j.end.column);
}else{var i=this.getLines(j.start.row+1,j.end.row-1);i.unshift((this.$lines[j.start.row]||"").substring(j.start.column));i.push((this.$lines[j.end.row]||"").substring(0,j.end.column));
return i.join(this.getNewLineCharacter());}};this.$clipPosition=function(i){var j=this.getLength();if(i.row>=j){i.row=Math.max(0,j-1);i.column=this.getLine(j-1).length;
}return i;};this.insert=function(i,m){if(!m||m.length===0){return i;}i=this.$clipPosition(i);if(this.getLength()<=1){this.$detectNewLine(m);}var k=this.$split(m);
var l=k.splice(0,1)[0];var j=k.length==0?null:k.splice(k.length-1,1)[0];i=this.insertInLine(i,l);if(j!==null){i=this.insertNewLine(i);i=this.insertLines(i.row,k);
i=this.insertInLine(i,j||"");}return i;};this.insertLines=function(m,j){if(j.length==0){return{row:m,column:0};}if(j.length>65535){var i=this.insertLines(m,j.slice(65535));
j=j.slice(0,65535);}var l=[m,0];l.push.apply(l,j);this.$lines.splice.apply(this.$lines,l);var k=new h(m,0,m+j.length,0);var n={action:"insertLines",range:k,lines:j};
this._emit("change",{data:n});return i||k.end;};this.insertNewLine=function(i){i=this.$clipPosition(i);var k=this.$lines[i.row]||"";this.$lines[i.row]=k.substring(0,i.column);
this.$lines.splice(i.row+1,0,k.substring(i.column,k.length));var j={row:i.row+1,column:0};var l={action:"insertText",range:h.fromPoints(i,j),text:this.getNewLineCharacter()};
this._emit("change",{data:l});return j;};this.insertInLine=function(i,l){if(l.length==0){return i;}var k=this.$lines[i.row]||"";this.$lines[i.row]=k.substring(0,i.column)+l+k.substring(i.column);
var j={row:i.row,column:i.column+l.length};var m={action:"insertText",range:h.fromPoints(i,j),text:l};this._emit("change",{data:m});return j;};this.remove=function(j){j.start=this.$clipPosition(j.start);
j.end=this.$clipPosition(j.end);if(j.isEmpty()){return j.start;}var m=j.start.row;var k=j.end.row;if(j.isMultiLine()){var l=j.start.column==0?m:m+1;var i=k-1;
if(j.end.column>0){this.removeInLine(k,0,j.end.column);}if(i>=l){this.removeLines(l,i);}if(l!=m){this.removeInLine(m,j.start.column,this.getLine(m).length);
this.removeNewLine(j.start.row);}}else{this.removeInLine(m,j.start.column,j.end.column);}return j.start;};this.removeInLine=function(m,k,p){if(k==p){return;
}var j=new h(m,k,m,p);var i=this.getLine(m);var l=i.substring(k,p);var o=i.substring(0,k)+i.substring(p,i.length);this.$lines.splice(m,1,o);var n={action:"removeText",range:j,text:l};
this._emit("change",{data:n});return j.start;};this.removeLines=function(l,j){var i=new h(l,0,j+1,0);var k=this.$lines.splice(l,j-l+1);var m={action:"removeLines",range:i,nl:this.getNewLineCharacter(),lines:k};
this._emit("change",{data:m});return k;};this.removeNewLine=function(m){var l=this.getLine(m);var i=this.getLine(m+1);var k=new h(m,l.length,m+1,0);var j=l+i;
this.$lines.splice(m,2,j);var n={action:"removeText",range:k,text:this.getNewLineCharacter()};this._emit("change",{data:n});};this.replace=function(j,k){if(k.length==0&&j.isEmpty()){return j.start;
}if(k==this.getTextRange(j)){return j.end;}this.remove(j);if(k){var i=this.insert(j.start,k);}else{i=j.start;}return i;};this.applyDeltas=function(l){for(var k=0;
k<l.length;k++){var m=l[k];var j=h.fromPoints(m.range.start,m.range.end);if(m.action=="insertLines"){this.insertLines(j.start.row,m.lines);}else{if(m.action=="insertText"){this.insert(j.start,m.text);
}else{if(m.action=="removeLines"){this.removeLines(j.start.row,j.end.row-1);}else{if(m.action=="removeText"){this.remove(j);}}}}}};this.revertDeltas=function(l){for(var k=l.length-1;
k>=0;k--){var m=l[k];var j=h.fromPoints(m.range.start,m.range.end);if(m.action=="insertLines"){this.removeLines(j.start.row,j.end.row-1);}else{if(m.action=="insertText"){this.remove(j);
}else{if(m.action=="removeLines"){this.insertLines(j.start.row,m.lines);}else{if(m.action=="removeText"){this.insert(j.start,m.text);}}}}}};}).call(b.prototype);
c.Document=b;});define("ace/range",["require","exports","module"],function(b,a,c){var d=function(f,g,e,h){this.start={row:f,column:g};this.end={row:e,column:h};
};(function(){this.isEqual=function(e){return this.start.row==e.start.row&&this.end.row==e.end.row&&this.start.column==e.start.column&&this.end.column==e.end.column;
};this.toString=function(){return("Range: ["+this.start.row+"/"+this.start.column+"] -> ["+this.end.row+"/"+this.end.column+"]");};this.contains=function(f,e){return this.compare(f,e)==0;
};this.compareRange=function(f){var g,e=f.end,h=f.start;g=this.compare(e.row,e.column);if(g==1){g=this.compare(h.row,h.column);if(g==1){return 2;}else{if(g==0){return 1;
}else{return 0;}}}else{if(g==-1){return -2;}else{g=this.compare(h.row,h.column);if(g==-1){return -1;}else{if(g==1){return 42;}else{return 0;}}}}};this.comparePoint=function(e){return this.compare(e.row,e.column);
};this.containsRange=function(e){return this.comparePoint(e.start)==0&&this.comparePoint(e.end)==0;};this.intersects=function(e){var f=this.compareRange(e);
return(f==-1||f==0||f==1);};this.isEnd=function(f,e){return this.end.row==f&&this.end.column==e;};this.isStart=function(f,e){return this.start.row==f&&this.start.column==e;
};this.setStart=function(f,e){if(typeof f=="object"){this.start.column=f.column;this.start.row=f.row;}else{this.start.row=f;this.start.column=e;}};this.setEnd=function(f,e){if(typeof f=="object"){this.end.column=f.column;
this.end.row=f.row;}else{this.end.row=f;this.end.column=e;}};this.inside=function(f,e){if(this.compare(f,e)==0){if(this.isEnd(f,e)||this.isStart(f,e)){return false;
}else{return true;}}return false;};this.insideStart=function(f,e){if(this.compare(f,e)==0){if(this.isEnd(f,e)){return false;}else{return true;}}return false;
};this.insideEnd=function(f,e){if(this.compare(f,e)==0){if(this.isStart(f,e)){return false;}else{return true;}}return false;};this.compare=function(f,e){if(!this.isMultiLine()){if(f===this.start.row){return e<this.start.column?-1:(e>this.end.column?1:0);
}}if(f<this.start.row){return -1;}if(f>this.end.row){return 1;}if(this.start.row===f){return e>=this.start.column?0:-1;}if(this.end.row===f){return e<=this.end.column?0:1;
}return 0;};this.compareStart=function(f,e){if(this.start.row==f&&this.start.column==e){return -1;}else{return this.compare(f,e);}};this.compareEnd=function(f,e){if(this.end.row==f&&this.end.column==e){return 1;
}else{return this.compare(f,e);}};this.compareInside=function(f,e){if(this.end.row==f&&this.end.column==e){return 1;}else{if(this.start.row==f&&this.start.column==e){return -1;
}else{return this.compare(f,e);}}};this.clipRows=function(g,f){if(this.end.row>f){var e={row:f+1,column:0};}if(this.start.row>f){var h={row:f+1,column:0};
}if(this.start.row<g){var h={row:g,column:0};}if(this.end.row<g){var e={row:g,column:0};}return d.fromPoints(h||this.start,e||this.end);};this.extend=function(h,f){var g=this.compare(h,f);
if(g==0){return this;}else{if(g==-1){var i={row:h,column:f};}else{var e={row:h,column:f};}}return d.fromPoints(i||this.start,e||this.end);};this.isEmpty=function(){return(this.start.row==this.end.row&&this.start.column==this.end.column);
};this.isMultiLine=function(){return(this.start.row!==this.end.row);};this.clone=function(){return d.fromPoints(this.start,this.end);};this.collapseRows=function(){if(this.end.column==0){return new d(this.start.row,0,Math.max(this.start.row,this.end.row-1),0);
}else{return new d(this.start.row,0,this.end.row,0);}};this.toScreenRange=function(f){var e=f.documentToScreenPosition(this.start);var g=f.documentToScreenPosition(this.end);
return new d(e.row,e.column,g.row,g.column);};}).call(d.prototype);d.fromPoints=function(f,e){return new d(f.row,f.column,e.row,e.column);};a.Range=d;});
define("ace/anchor",["require","exports","module","ace/lib/oop","ace/lib/event_emitter"],function(c,b,d){var f=c("./lib/oop");var e=c("./lib/event_emitter").EventEmitter;
var a=b.Anchor=function(h,i,g){this.document=h;if(typeof g=="undefined"){this.setPosition(i.row,i.column);}else{this.setPosition(i,g);}this.$onChange=this.onChange.bind(this);
h.on("change",this.$onChange);};(function(){f.implement(this,e);this.getPosition=function(){return this.$clipPositionToDocument(this.row,this.column);};
this.getDocument=function(){return this.document;};this.onChange=function(i){var k=i.data;var g=k.range;if(g.start.row==g.end.row&&g.start.row!=this.row){return;
}if(g.start.row>this.row){return;}if(g.start.row==this.row&&g.start.column>this.column){return;}var j=this.row;var h=this.column;if(k.action==="insertText"){if(g.start.row===j&&g.start.column<=h){if(g.start.row===g.end.row){h+=g.end.column-g.start.column;
}else{h-=g.start.column;j+=g.end.row-g.start.row;}}else{if(g.start.row!==g.end.row&&g.start.row<j){j+=g.end.row-g.start.row;}}}else{if(k.action==="insertLines"){if(g.start.row<=j){j+=g.end.row-g.start.row;
}}else{if(k.action=="removeText"){if(g.start.row==j&&g.start.column<h){if(g.end.column>=h){h=g.start.column;}else{h=Math.max(0,h-(g.end.column-g.start.column));
}}else{if(g.start.row!==g.end.row&&g.start.row<j){if(g.end.row==j){h=Math.max(0,h-g.end.column)+g.start.column;}j-=(g.end.row-g.start.row);}else{if(g.end.row==j){j-=g.end.row-g.start.row;
h=Math.max(0,h-g.end.column)+g.start.column;}}}}else{if(k.action=="removeLines"){if(g.start.row<=j){if(g.end.row<=j){j-=g.end.row-g.start.row;}else{j=g.start.row;
h=0;}}}}}}this.setPosition(j,h,true);};this.setPosition=function(j,i,g){var k;if(g){k={row:j,column:i};}else{k=this.$clipPositionToDocument(j,i);}if(this.row==k.row&&this.column==k.column){return;
}var h={row:this.row,column:this.column};this.row=k.row;this.column=k.column;this._emit("change",{old:h,value:k});};this.detach=function(){this.document.removeEventListener("change",this.$onChange);
};this.$clipPositionToDocument=function(h,g){var i={};if(h>=this.document.getLength()){i.row=Math.max(0,this.document.getLength()-1);i.column=this.document.getLine(i.row).length;
}else{if(h<0){i.row=0;i.column=0;}else{i.row=h;i.column=Math.min(this.document.getLine(i.row).length,Math.max(0,g));}}if(g<0){i.column=0;}return i;};}).call(a.prototype);
});define("ace/lib/lang",["require","exports","module"],function(b,a,c){a.stringReverse=function(f){return f.split("").reverse().join("");};a.stringRepeat=function(f,g){return new Array(g+1).join(f);
};var d=/^\s\s*/;var e=/\s\s*$/;a.stringTrimLeft=function(f){return f.replace(d,"");};a.stringTrimRight=function(f){return f.replace(e,"");};a.copyObject=function(g){var h={};
for(var f in g){h[f]=g[f];}return h;};a.copyArray=function(j){var h=[];for(var g=0,f=j.length;g<f;g++){if(j[g]&&typeof j[g]=="object"){h[g]=this.copyObject(j[g]);
}else{h[g]=j[g];}}return h;};a.deepCopy=function(g){if(typeof g!="object"){return g;}var h=g.constructor();for(var f in g){if(typeof g[f]=="object"){h[f]=this.deepCopy(g[f]);
}else{h[f]=g[f];}}return h;};a.arrayToMap=function(f){var h={};for(var g=0;g<f.length;g++){h[f[g]]=1;}return h;};a.createMap=function(g){var h=Object.create(null);
for(var f in g){h[f]=g[f];}return h;};a.arrayRemove=function(h,g){for(var f=0;f<=h.length;f++){if(g===h[f]){h.splice(f,1);}}};a.escapeRegExp=function(f){return f.replace(/([.*+?^${}()|[\]\/\\])/g,"\\$1");
};a.getMatchOffsets=function(g,f){var h=[];g.replace(f,function(i){h.push({offset:arguments[arguments.length-2],length:i.length});});return h;};a.deferredCall=function(g){var i=null;
var h=function(){i=null;g();};var f=function(j){f.cancel();i=setTimeout(h,j||0);return f;};f.schedule=f;f.call=function(){this.cancel();g();return f;};
f.cancel=function(){clearTimeout(i);i=null;return f;};return f;};});define("ace/mode/json/json_parse",["require","exports","module"],function(f,i,d){var e,b,a={'"':'"',"\\":"\\","/":"/",b:"\b",f:"\f",n:"\n",r:"\r",t:"\t"},p,n=function(q){throw {name:"SyntaxError",message:q,at:e,text:p};
},j=function(q){if(q&&q!==b){n("Expected '"+q+"' instead of '"+b+"'");}b=p.charAt(e);e+=1;return b;},h=function(){var r,q="";if(b==="-"){q="-";j("-");}while(b>="0"&&b<="9"){q+=b;
j();}if(b==="."){q+=".";while(j()&&b>="0"&&b<="9"){q+=b;}}if(b==="e"||b==="E"){q+=b;j();if(b==="-"||b==="+"){q+=b;j();}while(b>="0"&&b<="9"){q+=b;j();}}r=+q;
if(isNaN(r)){n("Bad number");}else{return r;}},k=function(){var t,s,r="",q;if(b==='"'){while(j()){if(b==='"'){j();return r;}else{if(b==="\\"){j();if(b==="u"){q=0;
for(s=0;s<4;s+=1){t=parseInt(j(),16);if(!isFinite(t)){break;}q=q*16+t;}r+=String.fromCharCode(q);}else{if(typeof a[b]==="string"){r+=a[b];}else{break;}}}else{r+=b;
}}}}n("Bad string");},m=function(){while(b&&b<=" "){j();}},c=function(){switch(b){case"t":j("t");j("r");j("u");j("e");return true;case"f":j("f");j("a");
j("l");j("s");j("e");return false;case"n":j("n");j("u");j("l");j("l");return null;}n("Unexpected '"+b+"'");},o,l=function(){var q=[];if(b==="["){j("[");
m();if(b==="]"){j("]");return q;}while(b){q.push(o());m();if(b==="]"){j("]");return q;}j(",");m();}}n("Bad array");},g=function(){var r,q={};if(b==="{"){j("{");
m();if(b==="}"){j("}");return q;}while(b){r=k();m();j(":");if(Object.hasOwnProperty.call(q,r)){n('Duplicate key "'+r+'"');}q[r]=o();m();if(b==="}"){j("}");
return q;}j(",");m();}}n("Bad object");};o=function(){m();switch(b){case"{":return g();case"[":return l();case'"':return k();case"-":return h();default:return b>="0"&&b<="9"?h():c();
}};return function(t,r){var q;p=t;e=0;b=" ";q=o();m();if(b){n("Syntax error");}return typeof r==="function"?function s(y,x){var w,u,z=y[x];if(z&&typeof z==="object"){for(w in z){if(Object.hasOwnProperty.call(z,w)){u=s(z,w);
if(u!==undefined){z[w]=u;}else{delete z[w];}}}}return r.call(y,x,z);}({"":q},""):q;};});