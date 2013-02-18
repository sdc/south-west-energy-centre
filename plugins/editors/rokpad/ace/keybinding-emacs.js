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
define("ace/keyboard/emacs",["require","exports","module","ace/lib/dom","ace/keyboard/hash_handler","ace/lib/keys"],function(d,f,b){var e=d("../lib/dom");
var i=function(n,m){var l=this.scroller.getBoundingClientRect();var k=Math.floor((n+this.scrollLeft-l.left-this.$padding-e.getPageScrollLeft())/this.characterWidth);
var o=Math.floor((m+this.scrollTop-l.top-e.getPageScrollTop())/this.lineHeight);return this.session.screenToDocumentPosition(o,k);};var h=d("./hash_handler").HashHandler;
f.handler=new h();var c=false;f.handler.attach=function(k){if(!c){c=true;e.importCssString("            .emacs-mode .ace_cursor{                border: 2px rgba(50,250,50,0.8) solid!important;                -moz-box-sizing: border-box!important;                box-sizing: border-box!important;                background-color: rgba(0,250,0,0.9);                opacity: 0.5;            }            .emacs-mode .ace_cursor.ace_hidden{                opacity: 1;                background-color: transparent;            }            .emacs-mode .ace_cursor.ace_overwrite {                opacity: 1;                background-color: transparent;                border-width: 0 0 2px 2px !important;            }            .emacs-mode .ace_text-layer {                z-index: 4            }            .emacs-mode .ace_cursor-layer {                z-index: 2            }","emacsMode");
}k.renderer.screenToTextCoordinates=i;k.setStyle("emacs-mode");};f.handler.detach=function(k){delete k.renderer.screenToTextCoordinates;k.unsetStyle("emacs-mode");
};var j=d("../lib/keys").KEY_MODS;var g={C:"ctrl",S:"shift",M:"alt"};["S-C-M","S-C","S-M","C-M","S","C","M"].forEach(function(l){var k=0;l.split("-").forEach(function(m){k=k|j[g[m]];
});g[k]=l.toLowerCase()+"-";});f.handler.bindKey=function(k,m){if(!k){return;}var l=this.commmandKeyBinding;k.split("|").forEach(function(n){n=n.toLowerCase();
l[n]=m;n=n.split(" ")[0];if(!l[n]){l[n]="null";}},this);};f.handler.handleKeyboard=function(n,m,r,s){if(m==-1){if(n.count){var q=Array(n.count+1).join(r);
n.count=null;return{command:"insertstring",args:q};}}if(r=="\x00"){return;}var k=g[m];if(k=="c-"||n.universalArgument){var o=parseInt(r[r.length-1]);if(o){n.count=o;
return{command:"null"};}}n.universalArgument=false;if(k){r=k+r;}if(n.keyChain){r=n.keyChain+=" "+r;}var l=this.commmandKeyBinding[r];n.keyChain=l=="null"?r:"";
if(!l){return;}if(l=="null"){return{command:"null"};}if(l=="universalArgument"){n.universalArgument=true;return{command:"null"};}if(typeof l!="string"){var p=l.args;
l=l.command;}if(typeof l=="string"){l=this.commands[l]||n.editor.commands.commands[l];}if(!l.readonly&&!l.isYank){n.lastCommand=null;}if(n.count){var o=n.count;
n.count=0;return{args:p,command:{exec:function(v,t){for(var u=0;u<o;u++){l.exec(v,t);}}}};}return{command:l,args:p};};f.emacsKeys={"Up|C-p":"golineup","Down|C-n":"golinedown","Left|C-b":"gotoleft","Right|C-f":"gotoright","C-Left|M-b":"gotowordleft","C-Right|M-f":"gotowordright","Home|C-a":"gotolinestart","End|C-e":"gotolineend","C-Home|S-M-,":"gotostart","C-End|S-M-.":"gotoend","S-Up|S-C-p":"selectup","S-Down|S-C-n":"selectdown","S-Left|S-C-b":"selectleft","S-Right|S-C-f":"selectright","S-C-Left|S-M-b":"selectwordleft","S-C-Right|S-M-f":"selectwordright","S-Home|S-C-a":"selecttolinestart","S-End|S-C-e":"selecttolineend","S-C-Home":"selecttostart","S-C-End":"selecttoend","C-l":"recenterTopBottom","M-s":"centerselection","M-g":"gotoline","C-x C-p":"selectall","C-Down":"gotopagedown","C-Up":"gotopageup","PageDown|C-v":"gotopagedown","PageUp|M-v":"gotopageup","S-C-Down":"selectpagedown","S-C-Up":"selectpageup","C-s":"findnext","C-r":"findprevious","M-C-s":"findnext","M-C-r":"findprevious","S-M-5":"replace",Backspace:"backspace","Delete|C-d":"del","Return|C-m":{command:"insertstring",args:"\n"},"C-o":"splitline","M-d|C-Delete":{command:"killWord",args:"right"},"C-Backspace|M-Backspace|M-Delete":{command:"killWord",args:"left"},"C-k":"killLine","C-y|S-Delete":"yank","M-y":"yankRotate","C-g":"keyboardQuit","C-w":"killRegion","M-w":"killRingSave","C-Space":"setMark","C-x C-x":"exchangePointAndMark","C-t":"transposeletters","M-u":"touppercase","M-l":"tolowercase","M-/":"autocomplete","C-u":"universalArgument","M-;":"togglecomment","C-/|C-x u|S-C--|C-z":"undo","S-C-/|S-C-x u|C--|S-C-z":"redo","C-x r":"selectRectangularRegion"};
f.handler.bindKeys(f.emacsKeys);f.handler.addCommands({recenterTopBottom:function(l){var m=l.renderer;var o=m.$cursorLayer.getPixelPosition();var k=m.$size.scrollerHeight-m.lineHeight;
var n=m.scrollTop;if(Math.abs(o.top-n)<2){n=o.top-k;}else{if(Math.abs(o.top-n-k*0.5)<2){n=o.top;}else{n=o.top-k*0.5;}}l.session.setScrollTop(n);},selectRectangularRegion:function(k){k.multiSelect.toggleBlockSelection();
},setMark:function(){},exchangePointAndMark:{exec:function(l){var k=l.selection.getRange();l.selection.setSelectionRange(k,!l.selection.isBackwards());
},readonly:true,multiselectAction:"forEach"},killWord:{exec:function(m,l){m.clearSelection();if(l=="left"){m.selection.selectWordLeft();}else{m.selection.selectWordRight();
}var k=m.getSelectionRange();var n=m.session.getTextRange(k);f.killRing.add(n);m.session.remove(k);m.clearSelection();},multiselectAction:"forEach"},killLine:function(l){l.selection.selectLine();
var k=l.getSelectionRange();var m=l.session.getTextRange(k);f.killRing.add(m);l.session.remove(k);l.clearSelection();},yank:function(k){k.onPaste(f.killRing.get());
k.keyBinding.$data.lastCommand="yank";},yankRotate:function(k){if(k.keyBinding.$data.lastCommand!="yank"){return;}k.undo();k.onPaste(f.killRing.rotate());
k.keyBinding.$data.lastCommand="yank";},killRegion:function(k){f.killRing.add(k.getCopyText());k.commands.byName.cut.exec(k);},killRingSave:function(k){f.killRing.add(k.getCopyText());
}});var a=f.handler.commands;a.yank.isYank=true;a.yankRotate.isYank=true;f.killRing={$data:[],add:function(k){k&&this.$data.push(k);if(this.$data.length>30){this.$data.shift();
}},get:function(){return this.$data[this.$data.length-1]||"";},pop:function(){if(this.$data.length>1){this.$data.pop();}return this.get();},rotate:function(){this.$data.unshift(this.$data.pop());
return this.get();}};});