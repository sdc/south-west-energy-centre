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
define("ace/mode/yaml",["require","exports","module","ace/lib/oop","ace/mode/text","ace/tokenizer","ace/mode/yaml_highlight_rules","ace/mode/matching_brace_outdent","ace/mode/folding/coffee"],function(c,e,b){var f=c("../lib/oop");
var d=c("./text").Mode;var h=c("../tokenizer").Tokenizer;var g=c("./yaml_highlight_rules").YamlHighlightRules;var j=c("./matching_brace_outdent").MatchingBraceOutdent;
var a=c("./folding/coffee").FoldMode;var i=function(){this.$tokenizer=new h(new g().getRules());this.$outdent=new j();this.foldingRules=new a();};f.inherits(i,d);
(function(){this.getNextLineIndent=function(o,l,n){var k=this.$getIndent(l);if(o=="start"){var m=l.match(/^.*[\{\(\[]\s*$/);if(m){k+=n;}}return k;};this.checkOutdent=function(m,k,l){return this.$outdent.checkOutdent(k,l);
};this.autoOutdent=function(k,l,m){this.$outdent.autoOutdent(l,m);};}).call(i.prototype);e.Mode=i;});define("ace/mode/yaml_highlight_rules",["require","exports","module","ace/lib/oop","ace/mode/text_highlight_rules"],function(d,b,e){var f=d("../lib/oop");
var a=d("./text_highlight_rules").TextHighlightRules;var c=function(){this.$rules={start:[{token:"comment",regex:"#.*$"},{token:"comment",regex:"^---"},{token:"variable",regex:"[&\\*][a-zA-Z0-9-_]+"},{token:["identifier","text"],regex:"(\\w+\\s*:)(\\w*)"},{token:"keyword.operator",regex:"<<\\w*:\\w*"},{token:"keyword.operator",regex:"-\\s*(?=[{])"},{token:"string",regex:'["](?:(?:\\\\.)|(?:[^"\\\\]))*?["]'},{token:"string",merge:true,regex:"[\\|>]\\w*",next:"qqstring"},{token:"string",regex:"['](?:(?:\\\\.)|(?:[^'\\\\]))*?[']"},{token:"constant.numeric",regex:"[+-]?\\d+(?:(?:\\.\\d*)?(?:[eE][+-]?\\d+)?)?\\b"},{token:"constant.language.boolean",regex:"(?:true|false|yes|no)\\b"},{token:"invalid.illegal",regex:"\\/\\/.*$"},{token:"paren.lparen",regex:"[[({]"},{token:"paren.rparen",regex:"[\\])}]"},{token:"text",regex:"\\s+"}],qqstring:[{token:"string",regex:"(?=(?:(?:\\\\.)|(?:[^:]))*?:)",next:"start"},{token:"string",merge:true,regex:".+"}]};
};f.inherits(c,a);b.YamlHighlightRules=c;});define("ace/mode/matching_brace_outdent",["require","exports","module","ace/range"],function(c,b,d){var e=c("../range").Range;
var a=function(){};(function(){this.checkOutdent=function(f,g){if(!/^\s+$/.test(f)){return false;}return/^\s*\}/.test(g);};this.autoOutdent=function(k,l){var g=k.getLine(l);
var h=g.match(/^(\s*\})/);if(!h){return 0;}var i=h[1].length;var j=k.findMatchingBracket({row:l,column:i});if(!j||j.row==l){return 0;}var f=this.$getIndent(k.getLine(j.row));
k.replace(new e(l,0,l,i-1),f);};this.$getIndent=function(f){var g=f.match(/^(\s+)/);if(g){return g[1];}return"";};}).call(a.prototype);b.MatchingBraceOutdent=a;
});define("ace/mode/folding/coffee",["require","exports","module","ace/lib/oop","ace/mode/folding/fold_mode","ace/range"],function(b,a,c){var d=b("../../lib/oop");
var g=b("./fold_mode").FoldMode;var f=b("../../range").Range;var e=a.FoldMode=function(){};d.inherits(e,g);(function(){this.getFoldWidgetRange=function(o,k,s){var m=this.indentationBlock(o,s);
if(m){return m;}var r=/\S/;var t=o.getLine(s);var l=t.search(r);if(l==-1||t[l]!="#"){return;}var i=t.length;var p=o.getLength();var q=s;var j=s;while(++s<p){t=o.getLine(s);
var h=t.search(r);if(h==-1){continue;}if(t[h]!="#"){break;}j=s;}if(j>q){var n=o.getLine(j).length;return new f(q,i,j,n);}};this.getFoldWidget=function(n,j,o){var p=n.getLine(o);
var h=p.search(/\S/);var k=n.getLine(o+1);var i=n.getLine(o-1);var l=i.search(/\S/);var m=k.search(/\S/);if(h==-1){n.foldWidgets[o-1]=l!=-1&&l<m?"start":"";
return"";}if(l==-1){if(h==m&&p[h]=="#"&&k[h]=="#"){n.foldWidgets[o-1]="";n.foldWidgets[o+1]="";return"start";}}else{if(l==h&&p[h]=="#"&&i[h]=="#"){if(n.getLine(o-2).search(/\S/)==-1){n.foldWidgets[o-1]="start";
n.foldWidgets[o+1]="";return"";}}}if(l!=-1&&l<h){n.foldWidgets[o-1]="start";}else{n.foldWidgets[o-1]="";}if(h<m){return"start";}else{return"";}};}).call(e.prototype);
});define("ace/mode/folding/fold_mode",["require","exports","module","ace/range"],function(b,a,c){var e=b("../../range").Range;var d=a.FoldMode=function(){};
(function(){this.foldingStartMarker=null;this.foldingStopMarker=null;this.getFoldWidget=function(h,g,i){var f=h.getLine(i);if(this.foldingStartMarker.test(f)){return"start";
}if(g=="markbeginend"&&this.foldingStopMarker&&this.foldingStopMarker.test(f)){return"end";}return"";};this.getFoldWidgetRange=function(g,f,h){return null;
};this.indentationBlock=function(l,p,g){var o=/\S/;var q=l.getLine(p);var j=q.search(o);if(j==-1){return;}var h=g||q.length;var m=l.getLength();var n=p;
var i=p;while(++p<m){var f=l.getLine(p).search(o);if(f==-1){continue;}if(f<=j){break;}i=p;}if(i>n){var k=l.getLine(i).length;return new e(n,h,i,k);}};this.openingBracketBlock=function(j,l,k,h,f){var m={row:k,column:h+1};
var g=j.$findClosingBracket(l,m,f);if(!g){return;}var i=j.foldWidgets[g.row];if(i==null){i=this.getFoldWidget(j,g.row);}if(i=="start"&&g.row>m.row){g.row--;
g.column=j.getLine(g.row).length;}return e.fromPoints(m,g);};}).call(d.prototype);});