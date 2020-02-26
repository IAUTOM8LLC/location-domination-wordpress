(function ($) {
    $(document).ready(function () {
        var isDisabled = $('#mpb_location_type').is(':disabled');
        var txt = $('#mpb_location_type :selected').val();
        if(txt === '1' && !isDisabled){
            $('.dd-message').html('Once you save changes you will download the entire cities database into your site. This may take a few minutes to download depending on your server and internet speeds. All future service pages will be created for each city.');
        }
        else{
            $('.dd-message').html('');
        }
        $(document).on("change", "#mpb_location_type", function () {
            var txt = $(this).children("option:selected").val();
            if(txt === '1' && !isDisabled){
                $('.dd-message').html('Once you save changes you will download the entire cities database into your site. This may take a few minutes to download depending on your server and internet speeds. All future service pages will be created for each city.');
            }
            else{
                $('.dd-message').html('');
            }
        })
    });
}(jQuery));