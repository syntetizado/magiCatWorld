// this is the id of the form


$('form[name="form_user_register"]').submit(function(e) {

    e.preventDefault(); // avoid to execute the actual submit of the form.

    var form = $(this);
    var url = form.attr('action');
    $.ajax({
		type: "POST",
		url: "ajaxregister",
		data: form.serialize(), // serializes the form's elements.
		success: function(data)
		{
		   alert(data); // show response from the php script.
		}
    })
	;
});
