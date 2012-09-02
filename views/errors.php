<?php 
	flexMVC::$bodyclass = "errors";
	flexMVC::$layout = "plain"; 
	flexMVC::$title = "FlexMVC"; 
	flexMVC::add_scripts("_public/javascripts/index.js"); 
	flexMVC::add_css("_public/stylesheets/style.css", "_public/stylesheets/errors.css");
?>

<div class="page_wrap" style="margin-top:50px">
	<div class="page_wrap" style="text-align:center">
		<div class="tron_blue" style="margin:auto;font-size:100px"></div>
		<h1 style="font-size:50px">This page simply does not exist.</h1>
	</div>
</div>