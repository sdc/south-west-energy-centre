// JavaScript Document

function getCookie( name ) {
	var start = document.cookie.indexOf( name + "=" );
	var len = start + name.length + 1;
	if ( ( !start ) && ( name != document.cookie.substring( 0, name.length ) ) ) {
		return null;
	}
	if ( start == -1 ) return null;
	var end = document.cookie.indexOf( ';', len );
	if ( end == -1 ) end = document.cookie.length;
	return unescape( document.cookie.substring( len, end ) );
}

function setCookie( name, value, expires, path, domain, secure ) {
	var today = new Date();
	today.setTime( today.getTime() );
	if ( expires ) {
		expires = expires * 1000 * 60 * 60 * 24;
	}
	var expires_date = new Date( today.getTime() + (expires) );
	document.cookie = name+'='+escape( value ) +
		( ( expires ) ? ';expires='+expires_date.toGMTString() : '' ) + //expires.toGMTString()
		( ( path ) ? ';path=' + path : '' ) +
		( ( domain ) ? ';domain=' + domain : '' ) +
		( ( secure ) ? ';secure' : '' );
}

function select_all_usergroups(){	
	var e = document.getElementById('accesslevel_selector');
	//e.disabled = true;	
	var i = 0;
	var n = e.options.length;	
	for (i = 0; i < n; i++) {		
		e.options[i].selected = true;
	}	
}

function usergroups_to_cookie(){
	var e = document.getElementById('accesslevel_selector');	
	var i = 0;
	var n = e.options.length;	
	usergroup_string = '';
	first = true;
	for (i = 0; i < n; i++) {
		if(e.options[i].selected==true){
			if(first && e.options[i].value!='all'){				
				usergroup_string = e.options[i].value;
				first = false;
			}else{
				usergroup_string = usergroup_string+','+e.options[i].value;
			}
		}
	}	
	setCookie('am_selected_grouplevels', usergroup_string, '', '', '', '');
}

