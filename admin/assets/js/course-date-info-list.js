(function ($) {
    $(document).ready(function () {
        $('.tts-cdi-fix-stock').on('click', function (e) {
            e.preventDefault();
            var $this = $(this),
                $tr = $this.closest('tr'),
                courseID = $this.data('cdid'),
                nonce = $this.data('nonce');
            $this.html('Fixing...');
            $.ajax({
                url: WPTBS_CDI.ajaxUrl,
                method: "post",
                data: {
                    'action': 'tbs_fix_cd_stock',
                    'course_id': courseID,
                    '_tbsnonce': nonce
                },
                dataType: "json",
                success: function(response){
                    if(!response || !response.status || "OK" !== response.status){
                        alert('Failed!');
                        return;
                    }
                    $tr.find('.column-remaining_places').html(response.remaining_places);
                    $this.hide(function () {
                        $this.remove();
                    });
                }

            }).fail(function(){
                alert('Failed!');
            });
        });
    });
})(jQuery);