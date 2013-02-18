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
define("ace/mode/sql",["require","exports","module","ace/lib/oop","ace/mode/text","ace/tokenizer","ace/mode/sql_highlight_rules","ace/range"],function(c,e,a){var f=c("../lib/oop");
var d=c("./text").Mode;var g=c("../tokenizer").Tokenizer;var h=c("./sql_highlight_rules").SqlHighlightRules;var b=c("../range").Range;var i=function(){this.$tokenizer=new g(new h().getRules());
};f.inherits(i,d);(function(){this.toggleCommentLines=function(k,r,s,n){var p=true;var q=[];var t=/^(\s*)--/;for(var o=s;o<=n;o++){if(!t.test(r.getLine(o))){p=false;
break;}}if(p){var j=new b(0,0,0,0);for(var o=s;o<=n;o++){var u=r.getLine(o);var l=u.match(t);j.start.row=o;j.end.row=o;j.end.column=l[0].length;r.replace(j,l[1]);
}}else{r.indentRows(s,n,"--");}};}).call(i.prototype);e.Mode=i;});define("ace/mode/sql_highlight_rules",["require","exports","module","ace/lib/oop","ace/mode/text_highlight_rules"],function(c,b,e){var f=c("../lib/oop");
var a=c("./text_highlight_rules").TextHighlightRules;var d=function(){var j=("select|from|where|and|or|group|by|order|limit|offset|having|as|case|when|else|end|type|left|right|join|on|outer|desc|asc");
var g=("true|false|null");var i=("count|min|max|avg|sum|rank|now|coalesce");var h=this.createKeywordMapper({"support.function":i,keyword:j,"constant.language":g},"identifier",true);
this.$rules={start:[{token:"comment",regex:"--.*$"},{token:"string",regex:'".*?"'},{token:"string",regex:"'.*?'"},{token:"constant.numeric",regex:"[+-]?\\d+(?:(?:\\.\\d*)?(?:[eE][+-]?\\d+)?)?\\b"},{token:h,regex:"[a-zA-Z_$][a-zA-Z0-9_$]*\\b"},{token:"keyword.operator",regex:"\\+|\\-|\\/|\\/\\/|%|<@>|@>|<@|&|\\^|~|<|>|<=|=>|==|!=|<>|="},{token:"paren.lparen",regex:"[\\(]"},{token:"paren.rparen",regex:"[\\)]"},{token:"text",regex:"\\s+"}]};
};f.inherits(d,a);b.SqlHighlightRules=d;});