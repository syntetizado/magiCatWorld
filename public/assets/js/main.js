$('#productMore').click(function(){
	var number;
	number=parseInt($('#productBuyNumber').html(), 0);
	if (number<99){
		number=number+1;
		$('#productBuyNumber').html(number);
		var tagPrice = $('#price').data('price')*$('#productBuyNumber').html();
		tagPrice = tagPrice.toFixed(2);
		$('#price').html(tagPrice+" €");
	}
});

$('#productLess').click(function(){
	var number;
	number=parseInt($('#productBuyNumber').html(), 0);
	if (number>1){
		number=number-1;
		$('#productBuyNumber').html(number);
		var tagPrice = $('#price').data('price')*$('#productBuyNumber').html();
		tagPrice = tagPrice.toFixed(2);
		$('#price').html(tagPrice+" €");
		$('#price').data("quantity")=$('#productBuyNumber').html(number);;
	}
});

$('#buyButton').click(function(){
	var number,product;
	number=parseInt($('#productBuyNumber').html(), 0);
	product=$('#buyButton').data('product');
	url="/cesta/agregar/item?quantity="+number+"&product-id="+product;
	window.location.href = url;
});


$('#deleteButton').click(function(){
	var idReview;
	idReview=$('#deleteButton').data('idreview');
	slug=$('#deleteButton').data('slug');
	url="/comentario/borrar?idreview="+idReview+"&slug="+slug;
	window.location.href = url;
});

// Add the following code if you want the name of the file appear on select
$(".custom-file-input").on("change", function() {
  var fileName = $(this).val().split("\\").pop();
  $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
});

$( document ).ready(function(){
	height=$(".get-this").outerHeight();
	$(".to-this").css({height: height});
});
