/*  
 * Image Manager Extended                 2.0.13
 * @package                 JCE
 * @url                     http://www.joomlacontenteditor.net
 * @copyright               Copyright (C) 2006 - 2013 Ryan Demmer. All rights reserved
 * @license                 GNU/GPL Version 2 - http://www.gnu.org/licenses/gpl-2.0.html
 * @date                    16 January 2013
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.

 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * NOTE : Javascript files have been compressed for speed and can be uncompressed using http://jsbeautifier.org/
 */
(function(){var each=tinymce.each,Event=tinymce.dom.Event;tinymce.create('tinymce.plugins.ImageManagerExtended',{init:function(ed,url){var self=this;this.editor=ed;this.url=url;function isMceItem(n){return/mceItem/.test(n.className);};ed.addCommand('mceImageManagerExtended',function(){var n=ed.selection.getNode();if(n.nodeName=='IMG'&&isMceItem(n)){return;}
ed.windowManager.open({file:ed.getParam('site_url')+'index.php?option=com_jce&view=editor&layout=plugin&plugin=imgmanager_ext',width:780+ed.getLang('imgmanager_ext.delta_width',0),height:640+ed.getLang('imgmanager_ext.delta_height',0),inline:1,popup_css:false},{plugin_url:url});});ed.addButton('imgmanager_ext',{title:'imgmanager_ext.desc',cmd:'mceImageManagerExtended',image:url+'/img/imgmanager_ext.png'});ed.onNodeChange.add(function(ed,cm,n){cm.setActive('imgmanager_ext',n.nodeName=='IMG'&&!isMceItem(n));});ed.onInit.add(function(){if(ed&&ed.plugins.contextmenu){ed.plugins.contextmenu.onContextMenu.add(function(th,m,e){m.add({title:'imgmanager_ext.desc',icon_src:url+'/img/imgmanager_ext.png',cmd:'mceImageManagerExtended'});});}});},insertUploadedFile:function(o){var ed=this.editor;if(/\.(gif|png|jpeg|jpg)$/.test(o.file)){var args={'src':o.file,'alt':o.alt||o.name,'style':{}};var attribs=['alt','title','id','dir','class','usemap','style','longdesc'];if(o.styles){var s=ed.dom.parseStyle(ed.dom.serializeStyle(o.styles));tinymce.extend(args.style,s);delete o.styles;}
if(o.style){var s=ed.dom.parseStyle(o.style);tinymce.extend(args.style,s);delete o.style;}
tinymce.each(attribs,function(k){if(typeof o[k]!=='undefined'){args[k]=o[k];}});return ed.dom.create('img',args);}
return false;},getUploadURL:function(file){if(/image\/(gif|png|jpeg|jpg)/.test(file.type)){return this.editor.getParam('site_url')+'index.php?option=com_jce&view=editor&layout=plugin&plugin=imgmanager_ext';}
return false;},getInfo:function(){return{longname:'Image Manager Extended',author:'Ryan Demmer',authorurl:'http://www.joomlacontenteditor.net',infourl:'http://www.joomlacontenteditor.net',version:'2.0.13'};}});tinymce.PluginManager.add('imgmanager_ext',tinymce.plugins.ImageManagerExtended);})();