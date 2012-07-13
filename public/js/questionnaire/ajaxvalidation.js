$(function()
{

	/*
    $('submit').submit(function() {
        doValidation($(this).parent().prev().find('label').attr('for'));
        return false;
    });
    */

    $("input").blur(function()
    {
        var formElementId = $(this).parent().prev().find('label').attr('for');
        doValidation(formElementId);
    });
});
function doValidation(id)
{
    var url = '/ibftool/questionnaire/validateform'
    var data = {};
    $("input").each(function()
    {
        data[$(this).attr('name')] = $(this).val();
    });
    $.post(url,data,function(resp)
    {
        $("#"+id).parent().find('.errors').remove();
        $("#"+id).parent().append(getErrorHtml(resp[id], id));
    },'json');
}
function getErrorHtml(formErrors , id)
{
	var o = "";
	if (formErrors != "undefined" && formErrors.length > 0) {
    	o += '<ul id="errors-'+id+'" class="errors">';
	    for(errorKey in formErrors)
	    {
	        o += '<li>' + formErrors[errorKey] + '</li>';
	    }
	    o += '</ul>';
	}
    return o;
}