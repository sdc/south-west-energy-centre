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
define("ace/mode/diff",["require","exports","module","ace/lib/oop","ace/mode/text","ace/tokenizer","ace/mode/diff_highlight_rules","ace/mode/folding/diff"],function(c,f,b){var g=c("../lib/oop");
var e=c("./text").Mode;var h=c("../tokenizer").Tokenizer;var d=c("./diff_highlight_rules").DiffHighlightRules;var a=c("./folding/diff").FoldMode;var i=function(){this.$tokenizer=new h(new d().getRules(),"i");
this.foldingRules=new a(["diff","index","\\+{3}","@@|\\*{5}"],"i");};g.inherits(i,e);(function(){}).call(i.prototype);f.Mode=i;});define("ace/mode/diff_highlight_rules",["require","exports","module","ace/lib/oop","ace/mode/text_highlight_rules"],function(c,b,d){var e=c("../lib/oop");
var a=c("./text_highlight_rules").TextHighlightRules;var f=function(){this.$rules={start:[{regex:"^(?:\\*{15}|={67}|-{3}|\\+{3})$",token:"punctuation.definition.separator.diff",name:"keyword"},{regex:"^(@@)(\\s*.+?\\s*)(@@)(.*)$",token:["constant","constant.numeric","constant","comment.doc.tag"]},{regex:"^(\\d+)([,\\d]+)(a|d|c)(\\d+)([,\\d]+)(.*)$",token:["constant.numeric","punctuation.definition.range.diff","constant.function","constant.numeric","punctuation.definition.range.diff","invalid"],name:"meta."},{regex:"^(?:(\\-{3}|\\+{3}|\\*{3})( .+))$",token:["constant.numeric","meta.tag"]},{regex:"^([!+>])(.*?)(\\s*)$",token:["support.constant","text","invalid"],},{regex:"^([<\\-])(.*?)(\\s*)$",token:["support.function","string","invalid"],},{regex:"^(diff)(\\s+--\\w+)?(.+?)( .+)?$",token:["variable","variable","keyword","variable"]},{regex:"^Index.+$",token:"variable"},{regex:"^(.*?)(\\s*)$",token:["invisible","invalid"]}]};
};e.inherits(f,a);b.DiffHighlightRules=f;});define("ace/mode/folding/diff",["require","exports","module","ace/lib/oop","ace/mode/folding/fold_mode","ace/range"],function(b,a,c){var d=b("../../lib/oop");
var g=b("./fold_mode").FoldMode;var f=b("../../range").Range;var e=a.FoldMode=function(i,h){this.regExpList=i;this.flag=h;this.foldingStartMarker=RegExp("^("+i.join("|")+")",this.flag);
};d.inherits(e,g);(function(){this.getFoldWidgetRange=function(n,k,q){var r=n.getLine(q);var h={row:q,column:r.length};var o=this.regExpList;for(var m=1;
m<=o.length;m++){var p=RegExp("^("+o.slice(0,m).join("|")+")",this.flag);if(p.test(r)){break;}}for(var j=n.getLength();++q<j;){r=n.getLine(q);if(p.test(r)){break;
}}if(q==h.row+1){return;}return f.fromPoints(h,{row:q-1,column:r.length});};}).call(e.prototype);});define("ace/mode/folding/fold_mode",["require","exports","module","ace/range"],function(b,a,c){var e=b("../../range").Range;
var d=a.FoldMode=function(){};(function(){this.foldingStartMarker=null;this.foldingStopMarker=null;this.getFoldWidget=function(h,g,i){var f=h.getLine(i);
if(this.foldingStartMarker.test(f)){return"start";}if(g=="markbeginend"&&this.foldingStopMarker&&this.foldingStopMarker.test(f)){return"end";}return"";
};this.getFoldWidgetRange=function(g,f,h){return null;};this.indentationBlock=function(l,p,g){var o=/\S/;var q=l.getLine(p);var j=q.search(o);if(j==-1){return;
}var h=g||q.length;var m=l.getLength();var n=p;var i=p;while(++p<m){var f=l.getLine(p).search(o);if(f==-1){continue;}if(f<=j){break;}i=p;}if(i>n){var k=l.getLine(i).length;
return new e(n,h,i,k);}};this.openingBracketBlock=function(j,l,k,h,f){var m={row:k,column:h+1};var g=j.$findClosingBracket(l,m,f);if(!g){return;}var i=j.foldWidgets[g.row];
if(i==null){i=this.getFoldWidget(j,g.row);}if(i=="start"&&g.row>m.row){g.row--;g.column=j.getLine(g.row).length;}return e.fromPoints(m,g);};}).call(d.prototype);
});