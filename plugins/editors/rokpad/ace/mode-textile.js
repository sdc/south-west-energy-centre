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
define("ace/mode/textile",["require","exports","module","ace/lib/oop","ace/mode/text","ace/tokenizer","ace/mode/textile_highlight_rules","ace/mode/matching_brace_outdent"],function(b,d,a){var f=b("../lib/oop");
var c=b("./text").Mode;var g=b("../tokenizer").Tokenizer;var e=b("./textile_highlight_rules").TextileHighlightRules;var i=b("./matching_brace_outdent").MatchingBraceOutdent;
var h=function(){this.$tokenizer=new g(new e().getRules());this.$outdent=new i();};f.inherits(h,c);(function(){this.getNextLineIndent=function(l,j,k){if(l=="intag"){return k;
}return"";};this.checkOutdent=function(l,j,k){return this.$outdent.checkOutdent(j,k);};this.autoOutdent=function(j,k,l){this.$outdent.autoOutdent(k,l);
};}).call(h.prototype);d.Mode=h;});define("ace/mode/textile_highlight_rules",["require","exports","module","ace/lib/oop","ace/mode/text_highlight_rules"],function(d,c,e){var f=d("../lib/oop");
var a=d("./text_highlight_rules").TextHighlightRules;var b=function(){this.$rules={start:[{token:function(g){if(g.charAt(0)=="h"){return"markup.heading."+g.charAt(1);
}else{return"markup.heading";}},regex:"h1|h2|h3|h4|h5|h6|bq|p|bc|pre",next:"blocktag"},{token:"keyword",regex:"[\\*]+|[#]+"},{token:"text",regex:".+"}],blocktag:[{token:"keyword",regex:"\\. ",next:"start"},{token:"keyword",regex:"\\(",next:"blocktagproperties"}],blocktagproperties:[{token:"keyword",regex:"\\)",next:"blocktag"},{token:"string",regex:"[a-zA-Z0-9\\-_]+"},{token:"keyword",regex:"#"}]};
};f.inherits(b,a);c.TextileHighlightRules=b;});define("ace/mode/matching_brace_outdent",["require","exports","module","ace/range"],function(c,b,d){var e=c("../range").Range;
var a=function(){};(function(){this.checkOutdent=function(f,g){if(!/^\s+$/.test(f)){return false;}return/^\s*\}/.test(g);};this.autoOutdent=function(k,l){var g=k.getLine(l);
var h=g.match(/^(\s*\})/);if(!h){return 0;}var i=h[1].length;var j=k.findMatchingBracket({row:l,column:i});if(!j||j.row==l){return 0;}var f=this.$getIndent(k.getLine(j.row));
k.replace(new e(l,0,l,i-1),f);};this.$getIndent=function(f){var g=f.match(/^(\s+)/);if(g){return g[1];}return"";};}).call(a.prototype);b.MatchingBraceOutdent=a;
});