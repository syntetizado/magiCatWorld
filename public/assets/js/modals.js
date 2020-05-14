// this is the id of the form


$('form[name="form_user_register"]').submit(function(e) {

    e.preventDefault(); // avoid to execute the actual submit of the form.

    var form = $(this);
    var url = form.attr('action');
    $.ajax({
		type: "POST",
		url: "ajaxregister",
		data: form.serialize(), // serializes the form's elements.
        dataType: ('html'),
		success: function(data)
		{
            $('#information').html(data);
            $('#registerModal').modal('toggle');
            $('#infoModal').modal('toggle');
            $('#gotoLogin').click( function(){
                    $('#infoModal').modal('toggle');
                    $('#loginModal').modal('toggle');
                });
            $('#gotoRegister').click( function(){
                    $('#infoModal').modal('toggle');
                    $('#registerModal').modal('toggle');
                });
		}
    })
	;
});

$('form[name="form_user_login"]').submit(function(e) {

    e.preventDefault(); // avoid to execute the actual submit of the form.

    var form = $(this);
    var url = form.attr('action');
    $.ajax({
		type: "POST",
		url: "ajaxlogin",
		data: form.serialize(), // serializes the form's elements.
        dataType: ('html'),
		success: function(data)
		{
            $('#information').html(data);
            $('#loginModal').modal('toggle');
            $('#infoModal').modal('toggle');
            $('#gotoLogin').click( function(){
                    $('#infoModal').modal('toggle');
                    $('#loginModal').modal('toggle');
                });
            $('#gotoRegister').click( function(){
                    $('#infoModal').modal('toggle');
                    $('#registerModal').modal('toggle');
                });
		}
    })
	;
});
