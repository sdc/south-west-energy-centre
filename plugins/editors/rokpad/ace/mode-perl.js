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
define("ace/mode/perl",["require","exports","module","ace/lib/oop","ace/mode/text","ace/tokenizer","ace/mode/perl_highlight_rules","ace/mode/matching_brace_outdent","ace/range","ace/mode/folding/cstyle"],function(c,e,a){var f=c("../lib/oop");
var d=c("./text").Mode;var h=c("../tokenizer").Tokenizer;var g=c("./perl_highlight_rules").PerlHighlightRules;var j=c("./matching_brace_outdent").MatchingBraceOutdent;
var b=c("../range").Range;var k=c("./folding/cstyle").FoldMode;var i=function(){this.$tokenizer=new h(new g().getRules());this.$outdent=new j();this.foldingRules=new k();
};f.inherits(i,d);(function(){this.toggleCommentLines=function(l,s,t,p){var r=true;var u=/^(\s*)#/;for(var q=t;q<=p;q++){if(!u.test(s.getLine(q))){r=false;
break;}}if(r){var n=new b(0,0,0,0);for(var q=t;q<=p;q++){var v=s.getLine(q);var o=v.match(u);n.start.row=q;n.end.row=q;n.end.column=o[0].length;s.replace(n,o[1]);
}}else{s.indentRows(t,p,"#");}};this.getNextLineIndent=function(q,m,o){var l=this.$getIndent(m);var p=this.$tokenizer.getLineTokens(m,q);var r=p.tokens;
if(r.length&&r[r.length-1].type=="comment"){return l;}if(q=="start"){var n=m.match(/^.*[\{\(\[\:]\s*$/);if(n){l+=o;}}return l;};this.checkOutdent=function(n,l,m){return this.$outdent.checkOutdent(l,m);
};this.autoOutdent=function(l,m,n){this.$outdent.autoOutdent(m,n);};}).call(i.prototype);e.Mode=i;});define("ace/mode/perl_highlight_rules",["require","exports","module","ace/lib/oop","ace/mode/text_highlight_rules"],function(d,c,e){var f=d("../lib/oop");
var b=d("./text_highlight_rules").TextHighlightRules;var a=function(){var j=("base|constant|continue|else|elsif|for|foreach|format|goto|if|last|local|my|next|no|package|parent|redo|require|scalar|sub|unless|until|while|use|vars");
var g=("ARGV|ENV|INC|SIG");var i=("getprotobynumber|getprotobyname|getservbyname|gethostbyaddr|gethostbyname|getservbyport|getnetbyaddr|getnetbyname|getsockname|getpeername|setpriority|getprotoent|setprotoent|getpriority|endprotoent|getservent|setservent|endservent|sethostent|socketpair|getsockopt|gethostent|endhostent|setsockopt|setnetent|quotemeta|localtime|prototype|getnetent|endnetent|rewinddir|wantarray|getpwuid|closedir|getlogin|readlink|endgrent|getgrgid|getgrnam|shmwrite|shutdown|readline|endpwent|setgrent|readpipe|formline|truncate|dbmclose|syswrite|setpwent|getpwnam|getgrent|getpwent|ucfirst|sysread|setpgrp|shmread|sysseek|sysopen|telldir|defined|opendir|connect|lcfirst|getppid|binmode|syscall|sprintf|getpgrp|readdir|seekdir|waitpid|reverse|unshift|symlink|dbmopen|semget|msgrcv|rename|listen|chroot|msgsnd|shmctl|accept|unpack|exists|fileno|shmget|system|unlink|printf|gmtime|msgctl|semctl|values|rindex|substr|splice|length|msgget|select|socket|return|caller|delete|alarm|ioctl|index|undef|lstat|times|srand|chown|fcntl|close|write|umask|rmdir|study|sleep|chomp|untie|print|utime|mkdir|atan2|split|crypt|flock|chmod|BEGIN|bless|chdir|semop|shift|reset|link|stat|chop|grep|fork|dump|join|open|tell|pipe|exit|glob|warn|each|bind|sort|pack|eval|push|keys|getc|kill|seek|sqrt|send|wait|rand|tied|read|time|exec|recv|eof|chr|int|ord|exp|pos|pop|sin|log|abs|oct|hex|tie|cos|vec|END|ref|map|die|uc|lc|do");
var h=this.createKeywordMapper({keyword:j,"constant.language":g,"support.function":i},"identifier");this.$rules={start:[{token:"comment",regex:"#.*$"},{token:"string.regexp",regex:"[/](?:(?:\\[(?:\\\\]|[^\\]])+\\])|(?:\\\\/|[^\\]/]))*[/]\\w*\\s*(?=[).,;]|$)"},{token:"string",regex:'["](?:(?:\\\\.)|(?:[^"\\\\]))*?["]'},{token:"string",merge:true,regex:'["].*\\\\$',next:"qqstring"},{token:"string",regex:"['](?:(?:\\\\.)|(?:[^'\\\\]))*?[']"},{token:"string",merge:true,regex:"['].*\\\\$",next:"qstring"},{token:"constant.numeric",regex:"0x[0-9a-fA-F]+\\b"},{token:"constant.numeric",regex:"[+-]?\\d+(?:(?:\\.\\d*)?(?:[eE][+-]?\\d+)?)?\\b"},{token:h,regex:"[a-zA-Z_$][a-zA-Z0-9_$]*\\b"},{token:"keyword.operator",regex:"\\.\\.\\.|\\|\\|=|>>=|<<=|<=>|&&=|=>|!~|\\^=|&=|\\|=|\\.=|x=|%=|\\/=|\\*=|\\-=|\\+=|=~|\\*\\*|\\-\\-|\\.\\.|\\|\\||&&|\\+\\+|\\->|!=|==|>=|<=|>>|<<|,|=|\\?\\:|\\^|\\||x|%|\\/|\\*|<|&|\\\\|~|!|>|\\.|\\-|\\+|\\-C|\\-b|\\-S|\\-u|\\-t|\\-p|\\-l|\\-d|\\-f|\\-g|\\-s|\\-z|\\-k|\\-e|\\-O|\\-T|\\-B|\\-M|\\-A|\\-X|\\-W|\\-c|\\-R|\\-o|\\-x|\\-w|\\-r|\\b(?:and|cmp|eq|ge|gt|le|lt|ne|not|or|xor)"},{token:"lparen",regex:"[[({]"},{token:"rparen",regex:"[\\])}]"},{token:"text",regex:"\\s+"}],qqstring:[{token:"string",regex:'(?:(?:\\\\.)|(?:[^"\\\\]))*?"',next:"start"},{token:"string",merge:true,regex:".+"}],qstring:[{token:"string",regex:"(?:(?:\\\\.)|(?:[^'\\\\]))*?'",next:"start"},{token:"string",merge:true,regex:".+"}]};
};f.inherits(a,b);c.PerlHighlightRules=a;});define("ace/mode/matching_brace_outdent",["require","exports","module","ace/range"],function(c,b,d){var e=c("../range").Range;
var a=function(){};(function(){this.checkOutdent=function(f,g){if(!/^\s+$/.test(f)){return false;}return/^\s*\}/.test(g);};this.autoOutdent=function(k,l){var g=k.getLine(l);
var h=g.match(/^(\s*\})/);if(!h){return 0;}var i=h[1].length;var j=k.findMatchingBracket({row:l,column:i});if(!j||j.row==l){return 0;}var f=this.$getIndent(k.getLine(j.row));
k.replace(new e(l,0,l,i-1),f);};this.$getIndent=function(f){var g=f.match(/^(\s+)/);if(g){return g[1];}return"";};}).call(a.prototype);b.MatchingBraceOutdent=a;
});define("ace/mode/folding/cstyle",["require","exports","module","ace/lib/oop","ace/range","ace/mode/folding/fold_mode"],function(b,a,c){var d=b("../../lib/oop");
var f=b("../../range").Range;var g=b("./fold_mode").FoldMode;var e=a.FoldMode=function(){};d.inherits(e,g);(function(){this.foldingStartMarker=/(\{|\[)[^\}\]]*$|^\s*(\/\*)/;
this.foldingStopMarker=/^[^\[\{]*(\}|\])|^[\s\*]*(\*\/)/;this.getFoldWidgetRange=function(o,k,p){var q=o.getLine(p);var m=q.match(this.foldingStartMarker);
if(m){var l=m.index;if(m[1]){return this.openingBracketBlock(o,m[1],p,l);}var n=o.getCommentFoldRange(p,l+m[0].length);n.end.column-=2;return n;}if(k!=="markbeginend"){return;
}var m=q.match(this.foldingStopMarker);if(m){var l=m.index+m[0].length;if(m[2]){var n=o.getCommentFoldRange(p,l);n.end.column-=2;return n;}var j={row:p,column:l};
var h=o.$findOpeningBracket(m[1],j);if(!h){return;}h.column++;j.column--;return f.fromPoints(h,j);}};}).call(e.prototype);});define("ace/mode/folding/fold_mode",["require","exports","module","ace/range"],function(b,a,c){var e=b("../../range").Range;
var d=a.FoldMode=function(){};(function(){this.foldingStartMarker=null;this.foldingStopMarker=null;this.getFoldWidget=function(h,g,i){var f=h.getLine(i);
if(this.foldingStartMarker.test(f)){return"start";}if(g=="markbeginend"&&this.foldingStopMarker&&this.foldingStopMarker.test(f)){return"end";}return"";
};this.getFoldWidgetRange=function(g,f,h){return null;};this.indentationBlock=function(l,p,g){var o=/\S/;var q=l.getLine(p);var j=q.search(o);if(j==-1){return;
}var h=g||q.length;var m=l.getLength();var n=p;var i=p;while(++p<m){var f=l.getLine(p).search(o);if(f==-1){continue;}if(f<=j){break;}i=p;}if(i>n){var k=l.getLine(i).length;
return new e(n,h,i,k);}};this.openingBracketBlock=function(j,l,k,h,f){var m={row:k,column:h+1};var g=j.$findClosingBracket(l,m,f);if(!g){return;}var i=j.foldWidgets[g.row];
if(i==null){i=this.getFoldWidget(j,g.row);}if(i=="start"&&g.row>m.row){g.row--;g.column=j.getLine(g.row).length;}return e.fromPoints(m,g);};}).call(d.prototype);
});