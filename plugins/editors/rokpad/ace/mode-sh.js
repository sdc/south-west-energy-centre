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
define("ace/mode/sh",["require","exports","module","ace/lib/oop","ace/mode/text","ace/tokenizer","ace/mode/sh_highlight_rules","ace/range"],function(c,f,a){var g=c("../lib/oop");
var e=c("./text").Mode;var h=c("../tokenizer").Tokenizer;var d=c("./sh_highlight_rules").ShHighlightRules;var b=c("../range").Range;var i=function(){this.$tokenizer=new h(new d().getRules());
};g.inherits(i,e);(function(){this.toggleCommentLines=function(k,r,s,o){var q=true;var t=/^(\s*)#/;for(var p=s;p<=o;p++){if(!t.test(r.getLine(p))){q=false;
break;}}if(q){var l=new b(0,0,0,0);for(var p=s;p<=o;p++){var u=r.getLine(p);var n=u.match(t);l.start.row=p;l.end.row=p;l.end.column=n[0].length;r.replace(l,n[1]);
}}else{r.indentRows(s,o,"#");}};this.getNextLineIndent=function(p,l,n){var k=this.$getIndent(l);var o=this.$tokenizer.getLineTokens(l,p);var q=o.tokens;
if(q.length&&q[q.length-1].type=="comment"){return k;}if(p=="start"){var m=l.match(/^.*[\{\(\[\:]\s*$/);if(m){k+=n;}}return k;};var j={pass:1,"return":1,raise:1,"break":1,"continue":1};
this.checkOutdent=function(n,k,l){if(l!=="\r\n"&&l!=="\r"&&l!=="\n"){return false;}var o=this.$tokenizer.getLineTokens(k.trim(),n).tokens;if(!o){return false;
}do{var m=o.pop();}while(m&&(m.type=="comment"||(m.type=="text"&&m.value.match(/^\s+$/))));if(!m){return false;}return(m.type=="keyword"&&j[m.value]);};
this.autoOutdent=function(m,n,o){o+=1;var k=this.$getIndent(n.getLine(o));var l=n.getTabString();if(k.slice(-l.length)==l){n.remove(new b(o,k.length-l.length,o,k.length));
}};}).call(i.prototype);f.Mode=i;});define("ace/mode/sh_highlight_rules",["require","exports","module","ace/lib/oop","ace/mode/text_highlight_rules"],function(c,b,d){var e=c("../lib/oop");
var a=c("./text_highlight_rules").TextHighlightRules;var f=function(){var k=("!|{|}|case|do|done|elif|else|esac|fi|for|if|in|then|until|while|&|;|export|local|read|typeset|unset|elif|select|set");
var l=("[|]|alias|bg|bind|break|builtin|cd|command|compgen|complete|continue|dirs|disown|echo|enable|eval|exec|exit|fc|fg|getopts|hash|help|history|jobs|kill|let|logout|popd|printf|pushd|pwd|return|set|shift|shopt|source|suspend|test|times|trap|type|ulimit|umask|unalias|wait");
var t=this.createKeywordMapper({keyword:k,"constant.language":l,"invalid.deprecated":"debugger"},"identifier");var m="(?:(?:[1-9]\\d*)|(?:0))";var s="(?:\\.\\d+)";
var g="(?:\\d+)";var n="(?:(?:"+g+"?"+s+")|(?:"+g+"\\.))";var r="(?:(?:"+n+"|"+g+"))";var p="(?:"+r+"|"+n+")";var i="(?:&"+g+")";var o="[a-zA-Z][a-zA-Z0-9_]*";
var j="(?:(?:\\$"+o+")|(?:"+o+"=))";var q="(?:\\$(?:SHLVL|\\$|\\!|\\?))";var h="(?:"+o+"\\s*\\(\\))";this.$rules={start:[{token:"comment",regex:"#.*$"},{token:"string",regex:'"(?:[^\\\\]|\\\\.)*?"'},{token:"variable.language",regex:q},{token:"variable",regex:j},{token:"support.function",regex:h,},{token:"support.function",regex:i},{token:"string",regex:"'(?:[^\\\\]|\\\\.)*?'"},{token:"constant.numeric",regex:p},{token:"constant.numeric",regex:m+"\\b"},{token:t,regex:"[a-zA-Z_$][a-zA-Z0-9_$]*\\b"},{token:"keyword.operator",regex:"\\+|\\-|\\*|\\*\\*|\\/|\\/\\/|~|<|>|<=|=>|=|!="},{token:"paren.lparen",regex:"[\\[\\(\\{]"},{token:"paren.rparen",regex:"[\\]\\)\\}]"},{token:"text",regex:"\\s+"}]};
};e.inherits(f,a);b.ShHighlightRules=f;});