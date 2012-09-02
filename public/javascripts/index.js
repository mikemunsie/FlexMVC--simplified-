var home = function(){
    var self = this;

    self.home = function(){

    	munstro.get("test/test", false, this, function(results){
    		console.log(results);
    	});
    }
    
  // Call teh constructor
  self.home()

};

$(document).ready(function() {
  window.home = new home();
  ko.applyBindings(window.home, $("body").get(0));
});