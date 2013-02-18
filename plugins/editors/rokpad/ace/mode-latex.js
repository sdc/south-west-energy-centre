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
define("ace/mode/latex",["require","exports","module","ace/lib/oop","ace/mode/text","ace/tokenizer","ace/mode/latex_highlight_rules","ace/range"],function(d,f,b){var g=d("../lib/oop");
var e=d("./text").Mode;var h=d("../tokenizer").Tokenizer;var a=d("./latex_highlight_rules").LatexHighlightRules;var c=d("../range").Range;var i=function(){this.$tokenizer=new h(new a().getRules());
};g.inherits(i,e);(function(){this.toggleCommentLines=function(j,r,s,o){var q=true;var n=/^(\s*)\%/;for(var p=s;p<=o;p++){if(!n.test(r.getLine(p))){q=false;
break;}}if(q){var k=new c(0,0,0,0);for(var p=s;p<=o;p++){var t=r.getLine(p);var l=t.match(n);k.start.row=p;k.end.row=p;k.end.column=l[0].length;r.replace(k,l[1]);
}}else{r.indentRows(s,o,"%");}};this.getNextLineIndent=function(l,j,k){return this.$getIndent(j);};}).call(i.prototype);f.Mode=i;});define("ace/mode/latex_highlight_rules",["require","exports","module","ace/lib/oop","ace/mode/text_highlight_rules"],function(c,b,d){var e=c("../lib/oop");
var a=c("./text_highlight_rules").TextHighlightRules;var f=function(){this.$rules={start:[{token:"keyword",regex:"\\\\(?:[^a-zA-Z]|[a-zA-Z]+)",},{token:"lparen",regex:"[[({]"},{token:"rparen",regex:"[\\])}]"},{token:"string",regex:"\\$(?:(?:\\\\.)|(?:[^\\$\\\\]))*?\\$"},{token:"comment",regex:"%.*$"}]};
};e.inherits(f,a);b.LatexHighlightRules=f;});