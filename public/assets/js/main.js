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
})

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
})

$('#buyButton').click(function(){
	var number,product;
	number=parseInt($('#productBuyNumber').html(), 0);
	product=$('#buyButton').data('product');
	url="/cesta/agregar?quantity="+number+"&product-id="+product;
	window.location.href = url;
})
