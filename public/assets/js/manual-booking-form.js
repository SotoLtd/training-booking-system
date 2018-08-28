(function($){
    $(document).ready(function(){
        $('#terms-conditions').is(':checked') ? $('#mbs-submit-form').prop('disabled', false) : $('#mbs-submit-form').prop('disabled', true);
        $('#terms-conditions').on('click', function(){
            $('#terms-conditions').is(':checked') ? $('#mbs-submit-form').prop('disabled', false) : $('#mbs-submit-form').prop('disabled', true);
        });
    });
})(jQuery);