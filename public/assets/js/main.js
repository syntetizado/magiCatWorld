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

// Add the following code if you want the name of the file appear on select
$(".custom-file-input").on("change", function() {
  var fileName = $(this).val().split("\\").pop();
  $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
});

$('#messageButton').click(function(){
	var message,title;
	message=$('#messageButton').data('message');
	title=$('#messageButton').data('title');
	$.ajax({
		type: "POST",
		url: "message",
		data: {message: message, title: title}, // serializes the form's elements.
        dataType: ('html'),
		success: function(data)
		{
            $('#information').html(data);
            $('#infoModal').modal('toggle');
		}
    })
})
