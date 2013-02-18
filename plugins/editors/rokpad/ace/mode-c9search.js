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
define("ace/mode/c9search",["require","exports","module","ace/lib/oop","ace/mode/text","ace/tokenizer","ace/mode/c9search_highlight_rules","ace/mode/matching_brace_outdent","ace/mode/folding/c9search"],function(b,d,a){var f=b("../lib/oop");
var c=b("./text").Mode;var g=b("../tokenizer").Tokenizer;var e=b("./c9search_highlight_rules").C9SearchHighlightRules;var j=b("./matching_brace_outdent").MatchingBraceOutdent;
var i=b("./folding/c9search").FoldMode;var h=function(){this.$tokenizer=new g(new e().getRules(),"i");this.$outdent=new j();this.foldingRules=new i();};
f.inherits(h,c);(function(){this.getNextLineIndent=function(n,l,m){var k=this.$getIndent(l);return k;};this.checkOutdent=function(m,k,l){return this.$outdent.checkOutdent(k,l);
};this.autoOutdent=function(k,l,m){this.$outdent.autoOutdent(l,m);};}).call(h.prototype);d.Mode=h;});define("ace/mode/c9search_highlight_rules",["require","exports","module","ace/lib/oop","ace/mode/text_highlight_rules"],function(d,c,e){var f=d("../lib/oop");
var b=d("./text_highlight_rules").TextHighlightRules;var a=function(){this.$rules={start:[{token:["c9searchresults.constant.numeric","c9searchresults.text","c9searchresults.text"],regex:"(^\\s+[0-9]+)(:\\s*)(.+)"},{token:["string","text"],regex:"(.+)(:$)"}]};
};f.inherits(a,b);c.C9SearchHighlightRules=a;});define("ace/mode/matching_brace_outdent",["require","exports","module","ace/range"],function(c,b,d){var e=c("../range").Range;
var a=function(){};(function(){this.checkOutdent=function(f,g){if(!/^\s+$/.test(f)){return false;}return/^\s*\}/.test(g);};this.autoOutdent=function(k,l){var g=k.getLine(l);
var h=g.match(/^(\s*\})/);if(!h){return 0;}var i=h[1].length;var j=k.findMatchingBracket({row:l,column:i});if(!j||j.row==l){return 0;}var f=this.$getIndent(k.getLine(j.row));
k.replace(new e(l,0,l,i-1),f);};this.$getIndent=function(f){var g=f.match(/^(\s+)/);if(g){return g[1];}return"";};}).call(a.prototype);b.MatchingBraceOutdent=a;
});define("ace/mode/folding/c9search",["require","exports","module","ace/lib/oop","ace/range","ace/mode/folding/fold_mode"],function(b,a,c){var d=b("../../lib/oop");
var f=b("../../range").Range;var g=b("./fold_mode").FoldMode;var e=a.FoldMode=function(){};d.inherits(e,g);(function(){this.foldingStartMarker=/^(\S.*\:|Searching for.*)$/;
this.foldingStopMarker=/^(\s+|Found.*)$/;this.getFoldWidgetRange=function(m,j,q){var r=m.doc.getAllLines(q);var s=r[q];var o=/^(Found.*|Searching for.*)$/;
var n=/^(\S.*\:|\s*)$/;var p=o.test(s)?o:n;if(this.foldingStartMarker.test(s)){for(var k=q+1,h=m.getLength();k<h;k++){if(p.test(r[k])){break;}}return new f(q,s.length,k,0);
}if(this.foldingStopMarker.test(s)){for(var k=q-1;k>=0;k--){s=r[k];if(p.test(s)){break;}}return new f(k,s.length,q,0);}};}).call(e.prototype);});define("ace/mode/folding/fold_mode",["require","exports","module","ace/range"],function(b,a,c){var e=b("../../range").Range;
var d=a.FoldMode=function(){};(function(){this.foldingStartMarker=null;this.foldingStopMarker=null;this.getFoldWidget=function(h,g,i){var f=h.getLine(i);
if(this.foldingStartMarker.test(f)){return"start";}if(g=="markbeginend"&&this.foldingStopMarker&&this.foldingStopMarker.test(f)){return"end";}return"";
};this.getFoldWidgetRange=function(g,f,h){return null;};this.indentationBlock=function(l,p,g){var o=/\S/;var q=l.getLine(p);var j=q.search(o);if(j==-1){return;
}var h=g||q.length;var m=l.getLength();var n=p;var i=p;while(++p<m){var f=l.getLine(p).search(o);if(f==-1){continue;}if(f<=j){break;}i=p;}if(i>n){var k=l.getLine(i).length;
return new e(n,h,i,k);}};this.openingBracketBlock=function(j,l,k,h,f){var m={row:k,column:h+1};var g=j.$findClosingBracket(l,m,f);if(!g){return;}var i=j.foldWidgets[g.row];
if(i==null){i=this.getFoldWidget(j,g.row);}if(i=="start"&&g.row>m.row){g.row--;g.column=j.getLine(g.row).length;}return e.fromPoints(m,g);};}).call(d.prototype);
});