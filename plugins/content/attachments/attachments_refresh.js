function refreshAttachments(siteUrl,ptype,pentity,pid,from){var url=siteUrl+"/index.php?option=com_attachments&task=attachmentsList";url+="&parent_id="+pid;url+="&parent_type="+ptype+"&parent_entity="+pentity;url+="&from="+from+"&tmpl=component&format=raw";id="attachmentsList_"+ptype+"_"+pentity+"_"+pid;var alist=document.getElementById(id);if(!alist){alist=window.parent.document.getElementById(id);}
if(!alist){id="attachmentsList_"+ptype+"_default_"+pid;alist=window.parent.document.getElementById(id);}
var a=new Request({url:url,method:'get',onComplete:function(response){alist.innerHTML=response;$$('a.modal-button').removeEvents('click');SqueezeBox.initialize({});SqueezeBox.assign($$('a.modal-button'),{parse:'rel'});}}).send();};
