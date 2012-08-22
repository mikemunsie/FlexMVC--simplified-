window.munstro = {

/**
 * Get Hash Paramater
 *
 * @param str index
 * @return array
 */ 
	get_hash_param: function(index){
		var hash = window.location.hash;
		hash = hash.replace("#","");
		hash = hash.split("/");
		return hash[index];
	},

/**
 * Display a message to the user
 *
 * @param str message
 * @return void
 */ 	
	flash_message: function(message, position){
		if($("#flash").attr("id")){ $("#flash").html(message); }
		else{ $("body").append("<div id=\"flash\">" + message + "</div>"); }
		$('#flash').slideDown('fast').delay(5000).slideUp('fast');
	},
	
/**
 * Get URL variables
 * Thanks to: http://jquery-howto.blogspot.com/2009/09/get-url-parameters-values-with-jquery.html
 *
 * @param str name
 * @return str
 */ 		
	request_url_var: function(name){
		var vars = [], hash;
		var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
		for(var i = 0; i < hashes.length; i++){
			hash = hashes[i].split('=');
			vars.push(hash[0]);
			vars[hash[0]] = hash[1];
		}
		return vars[name];
	},
	
/**
 * Retrieve Data
 *
 * @return void
 */ 	
	post: function(action, data, scope, callback){
		if(!scope){ scope = this; }
		$.ajax({  
			type: "post",  
			url: "?api="+action,
			data: data,
			datatype: "json",
			success: function(response){
				if(callback){	
					callback.call(scope, response);
				}
			}
		});
	},

/**
 * Retrieve Data
 *
 * @return void
 */ 	
	get: function(action, data, scope, callback){
		if(!scope){ scope = this; }
		$.ajax({  
			type: "get",  
			url: "?api="+action,
			data: data,
			datatype: "json",
			success: function(response){
				if(callback){	
					callback.call(scope, response);
				}
			}
		});	
	}
};
$('#flash').slideDown('fast').delay(5000).slideUp('fast');	