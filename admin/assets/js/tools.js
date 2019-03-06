(function($){
    tbsToolsReloadAccounts = {
        init: function($button){
            this.$button = $button;
            this.total = parseInt($button.data('total'), 10);
            this.nonce = $button.data('nonce');
            this.current = 1;
            this.done = 0;
            this.limit = 20;
            this.$progress = $button.siblings('.tbs-crm-reload-account-progress');
            this.$progressBar = this.$progress.find('.tbs-crm-reload-account-progress-bar');
            this.$progressCount = this.$progress.find('.tbs-crm-reload-account-progress-count');
            this.showLoader();
            this.run();
        },
        showLoader: function(){
            this.$progress.addClass('tbs-active');
            this.$progressCount.html(this.done + '/' + this.total);
        },
        processResponse: function(response){
            if(!response || !response.status || (response.status !== 'OK')){
                return false;
            }
            this.done = Math.min(this.current * this.limit, this.total);
            this.$progressCount.html(this.done + '/' + this.total);
            this.$progressBar.css('width', Math.min(100, Math.floor(100 * (this.done/this.total))) + '%');
            this.current++;
            if(this.done < this.total){
                this.run();
            }
        },
        run: function(){
            ob = this;
            $.ajax({
                url: TBS_Tools.ajaxUrl,
                method: "post",
                dataType: "json",
                data: {
                    action: 'tbh_crm_reload_accounts',
                    current: this.current,
                    limit: this.limit,
                    _tbsnonce: this.nonce
                },
                success: function(response){
                    ob.processResponse(response);
                }
            });
        }
    };
    $(document).ready(function () {
        $('.tbs-crm-reload-accounts').on('click', function (event) {
            event.preventDefault();
            tbsToolsReloadAccounts.init($(this));
        });
    });
})(jQuery);