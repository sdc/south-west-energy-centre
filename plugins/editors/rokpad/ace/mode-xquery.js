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
define("ace/mode/xquery",["require","exports","module","ace/worker/worker_client","ace/lib/oop","ace/mode/text","ace/tokenizer","ace/mode/xquery_highlight_rules","ace/mode/behaviour/xquery","ace/range"],function(d,f,b){var a=d("../worker/worker_client").WorkerClient;
var h=d("../lib/oop");var e=d("./text").Mode;var i=d("../tokenizer").Tokenizer;var k=d("./xquery_highlight_rules").XQueryHighlightRules;var g=d("./behaviour/xquery").XQueryBehaviour;
var c=d("../range").Range;var j=function(l){this.$tokenizer=new i(new k().getRules());this.$behaviour=new g(l);};h.inherits(j,e);(function(){this.getNextLineIndent=function(p,m,o){var l=this.$getIndent(m);
var n=m.match(/\s*(?:then|else|return|[{\(]|<\w+>)\s*$/);if(n){l+=o;}return l;};this.checkOutdent=function(n,l,m){if(!/^\s+$/.test(l)){return false;}return/^\s*[\}\)]/.test(m);
};this.autoOutdent=function(q,r,s){var m=r.getLine(s);var n=m.match(/^(\s*[\}\)])/);if(!n){return 0;}var o=n[1].length;var p=r.findMatchingBracket({row:s,column:o});
if(!p||p.row==s){return 0;}var l=this.$getIndent(r.getLine(p.row));r.replace(new c(s,0,s,o-1),l);};this.$getIndent=function(l){var m=l.match(/^(\s+)/);
if(m){return m[1];}return"";};this.toggleCommentLines=function(l,q,r,m){var n,t;var p=true;var s=/^\s*\(:(.*):\)/;for(n=r;n<=m;n++){if(!s.test(q.getLine(n))){p=false;
break;}}var o=new c(0,0,0,0);for(n=r;n<=m;n++){t=q.getLine(n);o.start.row=n;o.end.row=n;o.end.column=t.length;q.replace(o,p?t.match(s)[1]:"(:"+t+":)");
}};this.createWorker=function(m){this.$deltas=[];var n=new a(["ace"],"ace/mode/xquery_worker","XQueryWorker");var l=this;m.getDocument().on("change",function(o){l.$deltas.push(o.data);
});n.attachToDocument(m.getDocument());n.on("start",function(o){l.$deltas=[];});n.on("error",function(o){m.setAnnotations([o.data]);});n.on("ok",function(o){m.clearAnnotations();
});n.on("highlight",function(r){var p=0;var v=m.getLength()-1;var w=r.data.lines;var u=r.data.states;for(var q=0;q<l.$deltas.length;q++){var s=l.$deltas[q];
if(s.action==="insertLines"){var t=s.lines.length;for(var q=0;q<t;q++){w.splice(s.range.start.row+q,0,undefined);u.splice(s.range.start.row+q,0,undefined);
}}else{if(s.action==="insertText"){if(m.getDocument().isNewLine(s.text)){w.splice(s.range.end.row,0,undefined);u.splice(s.range.end.row,0,undefined);}else{w[s.range.start.row]=undefined;
u[s.range.start.row]=undefined;}}else{if(s.action==="removeLines"){var o=s.lines.length;w.splice(s.range.start.row,o);u.splice(s.range.start.row,o);}else{if(s.action==="removeText"){if(m.getDocument().isNewLine(s.text)){w[s.range.start.row]=undefined;
w.splice(s.range.end.row,1);u[s.range.start.row]=undefined;u.splice(s.range.end.row,1);}else{w[s.range.start.row]=undefined;u[s.range.start.row]=undefined;
}}}}}}m.bgTokenizer.lines=w;m.bgTokenizer.states=u;m.bgTokenizer.fireUpdateEvent(p,v);});return n;};}).call(j.prototype);f.Mode=j;});define("ace/mode/xquery_highlight_rules",["require","exports","module","ace/lib/oop","ace/mode/text_highlight_rules"],function(d,c,e){var f=d("../lib/oop");
var b=d("./text_highlight_rules").TextHighlightRules;var a=function(){var g=this.createKeywordMapper({keyword:"after|ancestor|ancestor-or-self|and|as|ascending|attribute|before|case|cast|castable|child|collation|comment|copy|count|declare|default|delete|descendant|descendant-or-self|descending|div|document|document-node|element|else|empty|empty-sequence|end|eq|every|except|first|following|following-sibling|for|function|ge|group|gt|idiv|if|import|insert|instance|intersect|into|is|item|last|le|let|lt|mod|modify|module|namespace|namespace-node|ne|node|only|or|order|ordered|parent|preceding|preceding-sibling|processing-instruction|rename|replace|return|satisfies|schema-attribute|schema-element|self|some|stable|start|switch|text|to|treat|try|typeswitch|union|unordered|validate|where|with|xquery|contains|paragraphs|sentences|times|words|by|collectionreturn|variable|version|option|when|encoding|toswitch|catch|tumbling|sliding|window|at|using|stemming|collection|schema|while|on|nodes|index|external|then|in|updating|value|of|containsbreak|loop|continue|exit|returning|append|json|position"},"identifier");
this.$rules={start:[{token:"text",regex:"<\\!\\[CDATA\\[",next:"cdata"},{token:"xml_pe",regex:"<\\?.*?\\?>"},{token:"comment",regex:"<\\!--",next:"comment"},{token:"comment",regex:"\\(:",next:"comment"},{token:"text",regex:"<\\/?",next:"tag"},{token:"constant",regex:"[+-]?\\d+(?:(?:\\.\\d*)?(?:[eE][+-]?\\d+)?)?\\b"},{token:"variable",regex:"\\$[a-zA-Z_][a-zA-Z0-9_\\-:]*\\b"},{token:"string",regex:'".*?"'},{token:"string",regex:"'.*?'"},{token:"text",regex:"\\s+"},{token:"support.function",regex:"\\w[\\w+_\\-:]+(?=\\()"},{token:g,regex:"[a-zA-Z_$][a-zA-Z0-9_$]*\\b"},{token:"keyword.operator",regex:"\\*|=|<|>|\\-|\\+"},{token:"lparen",regex:"[[({]"},{token:"rparen",regex:"[\\])}]"}],tag:[{token:"text",regex:">",next:"start"},{token:"meta.tag",regex:"[-_a-zA-Z0-9:]+"},{token:"text",regex:"\\s+"},{token:"string",regex:'".*?"'},{token:"string",regex:"'.*?'"}],cdata:[{token:"comment",regex:"\\]\\]>",next:"start"},{token:"comment",regex:"\\s+"},{token:"comment",regex:"(?:[^\\]]|\\](?!\\]>))+"}],comment:[{token:"comment",regex:".*?-->",next:"start"},{token:"comment",regex:".*:\\)",next:"start"},{token:"comment",regex:".+"}]};
};f.inherits(a,b);c.XQueryHighlightRules=a;});define("ace/mode/behaviour/xquery",["require","exports","module","ace/lib/oop","ace/mode/behaviour","ace/mode/behaviour/cstyle"],function(c,a,e){var f=c("../../lib/oop");
var g=c("../behaviour").Behaviour;var b=c("./cstyle").CstyleBehaviour;var d=function(h){this.inherit(b,["braces","parens","string_dquotes"]);this.parent=h;
};f.inherits(d,g);a.XQueryBehaviour=d;});define("ace/mode/behaviour/cstyle",["require","exports","module","ace/lib/oop","ace/mode/behaviour"],function(c,a,d){var e=c("../../lib/oop");
var f=c("../behaviour").Behaviour;var b=function(){this.add("braces","insertion",function(h,j,m,p,r){if(r=="{"){var q=m.getSelectionRange();var k=p.doc.getTextRange(q);
if(k!==""){return{text:"{"+k+"}",selection:false};}else{return{text:"{}",selection:[1,1]};}}else{if(r=="}"){var s=m.getCursorPosition();var t=p.doc.getLine(s.row);
var n=t.substring(s.column,s.column+1);if(n=="}"){var g=p.$findOpeningBracket("}",{column:s.column+1,row:s.row});if(g!==null){return{text:"",selection:[1,1]};
}}}else{if(r=="\n"){var s=m.getCursorPosition();var t=p.doc.getLine(s.row);var n=t.substring(s.column,s.column+1);if(n=="}"){var o=p.findMatchingBracket({row:s.row,column:s.column+1});
if(!o){return null;}var i=this.getNextLineIndent(h,t.substring(0,t.length-1),p.getTabString());var l=this.$getIndent(p.doc.getLine(o.row));return{text:"\n"+i+"\n"+l,selection:[1,i.length,1,i.length]};
}}}}});this.add("braces","deletion",function(l,k,j,m,h){var i=m.doc.getTextRange(h);if(!h.isMultiLine()&&i=="{"){var g=m.doc.getLine(h.start.row);var n=g.substring(h.end.column,h.end.column+1);
if(n=="}"){h.end.column++;return h;}}});this.add("parens","insertion",function(h,i,k,m,o){if(o=="("){var n=k.getSelectionRange();var j=m.doc.getTextRange(n);
if(j!==""){return{text:"("+j+")",selection:false};}else{return{text:"()",selection:[1,1]};}}else{if(o==")"){var p=k.getCursorPosition();var q=m.doc.getLine(p.row);
var l=q.substring(p.column,p.column+1);if(l==")"){var g=m.$findOpeningBracket(")",{column:p.column+1,row:p.row});if(g!==null){return{text:"",selection:[1,1]};
}}}}});this.add("parens","deletion",function(l,k,j,m,h){var i=m.doc.getTextRange(h);if(!h.isMultiLine()&&i=="("){var g=m.doc.getLine(h.start.row);var n=g.substring(h.start.column+1,h.start.column+2);
if(n==")"){h.end.column++;return h;}}});this.add("brackets","insertion",function(h,i,k,m,o){if(o=="["){var n=k.getSelectionRange();var j=m.doc.getTextRange(n);
if(j!==""){return{text:"["+j+"]",selection:false};}else{return{text:"[]",selection:[1,1]};}}else{if(o=="]"){var p=k.getCursorPosition();var q=m.doc.getLine(p.row);
var l=q.substring(p.column,p.column+1);if(l=="]"){var g=m.$findOpeningBracket("]",{column:p.column+1,row:p.row});if(g!==null){return{text:"",selection:[1,1]};
}}}}});this.add("brackets","deletion",function(l,k,j,m,h){var i=m.doc.getTextRange(h);if(!h.isMultiLine()&&i=="["){var g=m.doc.getLine(h.start.row);var n=g.substring(h.start.column+1,h.start.column+2);
if(n=="]"){h.end.column++;return h;}}});this.add("string_dquotes","insertion",function(h,k,n,q,u){if(u=='"'||u=="'"){var g=u;var s=n.getSelectionRange();
var l=q.doc.getTextRange(s);if(l!==""){return{text:g+l+g,selection:false};}else{var t=n.getCursorPosition();var w=q.doc.getLine(t.row);var v=w.substring(t.column-1,t.column);
if(v=="\\"){return null;}var p=q.getTokens(s.start.row);var i=0,j;var m=-1;for(var r=0;r<p.length;r++){j=p[r];if(j.type=="string"){m=-1;}else{if(m<0){m=j.value.indexOf(g);
}}if((j.value.length+i)>s.start.column){break;}i+=p[r].value.length;}if(!j||(m<0&&j.type!=="comment"&&(j.type!=="string"||((s.start.column!==j.value.length+i-1)&&j.value.lastIndexOf(g)===j.value.length-1)))){return{text:g+g,selection:[1,1]};
}else{if(j&&j.type==="string"){var o=w.substring(t.column,t.column+1);if(o==g){return{text:"",selection:[1,1]};}}}}}});this.add("string_dquotes","deletion",function(l,k,j,m,h){var i=m.doc.getTextRange(h);
if(!h.isMultiLine()&&(i=='"'||i=="'")){var g=m.doc.getLine(h.start.row);var n=g.substring(h.start.column+1,h.start.column+2);if(n=='"'){h.end.column++;
return h;}}});};e.inherits(b,f);a.CstyleBehaviour=b;});