$(document).ready(function(){

$("#la-carte").on('click', 'div.ajout-rapide', function(e) {
	e.preventDefault();
	
	$(this).next('.cumul').show().delay(800).fadeOut("slow");
	
	var codep = $(this).attr("data-id"); //get product code
	$.getJSON( "https://www.croqui.fr/cart_process.php", {"product_code":codep} , function(data){
	});
	$("#shopping-cart-results" ).load( "https://www.croqui.fr/cart_process.php" );
	$(".qtepanier").load("https://www.croqui.fr/cart_process.php #qtepanierloader");
	$("#lePanier").addClass('notification--reveal');
});




//Remove items from cart
$("#shopping-cart-results").on('click', 'div.item__remove', function(e) {
	e.preventDefault();

	var pcode = $(this).attr("data-code"); //get product code
	$(this).parent().fadeOut(); //remove item element from box
	$.getJSON( "https://www.croqui.fr/cart_process.php", {"remove_code":pcode} , function(data){ //get Item count from Server
	});
	$("#shopping-cart-results" ).load( "https://www.croqui.fr/cart_process.php" );
	$(".qtepanier").load("https://www.croqui.fr/cart_process.php #qtepanierloader");
});


//Changement de formule
$("#shopping-cart-results").on('click', 'span.optionf', function(e) {
	e.preventDefault();

	var pcode = $(this).attr("data-code");
	$.getJSON( "https://www.croqui.fr/cart_process.php", {"choix":pcode} , function(data){
	});
	$("#shopping-cart-results" ).load( "https://www.croqui.fr/cart_process.php" );
	$(".qtepanier").load("https://www.croqui.fr/cart_process.php #qtepanierloader");
});


$("#payment-form").submit(function(e){
	var form_data = $(this).serialize();
	var button_content = $(this).find('button[type=submit]');
	button_content.html('En cours de validation...');
});



$("#calendar").load("https://www.croqui.fr/calendrier.php");

$( "#date" ).change(function() {  
	$( "#date option:selected" ).each(function() {
		$("#calendar").load("https://www.croqui.fr/calendrier.php", { choice : $(this).val() });
	});
})

});
