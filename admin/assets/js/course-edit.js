;
(function($){
    function tbsTinyMceInit($element){
        var textfield_id = $element.attr("id"), 
            $form_line = $element.closest(".tbs-wp-editor-holder");
        try {
            if(_.isUndefined(tinyMCEPreInit.qtInit[textfield_id])){
                window.tinyMCEPreInit.qtInit[textfield_id] = _.extend({}, window.tinyMCEPreInit.qtInit[window.wpActiveEditor], {
                        id: textfield_id
                });
            }
            if(window.tinyMCEPreInit && window.tinyMCEPreInit.mceInit[window.wpActiveEditor]){
                window.tinyMCEPreInit.mceInit[textfield_id] = _.extend({}, window.tinyMCEPreInit.mceInit[window.wpActiveEditor], {
                    resize: "vertical",
                    height: 200,
                    id: textfield_id,
                    setup: function(ed) {
                        void 0 !== ed.on ? ed.on("init", function(ed) {
                            window.wpActiveEditor = textfield_id
                        }) : ed.onInit.add(function(ed) {
                            window.wpActiveEditor = textfield_id
                        })
                    }
                });
                window.tinyMCEPreInit.mceInit[textfield_id].plugins = window.tinyMCEPreInit.mceInit[textfield_id].plugins.replace(/,?wpfullscreen/, "");
                window.tinyMCEPreInit.mceInit[textfield_id].wp_autoresize_on = !1;
            }
            quicktags(window.tinyMCEPreInit.qtInit[textfield_id]);
            QTags._buttonsInit();
            if(window.tinymce){
                window.switchEditors && window.switchEditors.go(textfield_id, "tmce");
                "4" === tinymce.majorVersion && tinymce.execCommand("mceAddEditor", !0, textfield_id);
            }
            window.wpActiveEditor = textfield_id;
        }catch(e){
            $element.appendTo($form_line);
            $("#wp-" + textfield_id + "-wrap").remove();
        }
    };
    function tbsTinyMceGetContent($field){
        try {
            window.tinyMCE && _.isArray(window.tinyMCE.editors) && _.each(window.tinyMCE.editors, function(_editor) {
                $field.attr('id') === _editor.id && _editor.save();
            });
        } catch (e) {
            console && console.error && console.error(e);
        }
        return $field.val();
    }
    function tbsKillMceEditor($element){
        if(_.isUndefined(window.tinyMCE)){
            return;
        }
        var id = $element.attr("id");
        "4" === tinymce.majorVersion ? window.tinyMCE.execCommand("mceRemoveEditor", !0, id) : window.tinyMCE.execCommand("mceRemoveControl", !0, id);
    };
    function sort_clone_key($cloneWrap){
        $('.sd-visual-editor', $cloneWrap).each(function(){
            var fid = $(this).attr('id');
            tinyMCE && tinyMCE.execCommand("mceRemoveEditor", false, fid);
        });
        $cloneWrap.find('.tts-field-cloneable').each(function(index){
            var $clone = $(this);  
            $(':input', $clone).each(function(){
                var name = $(this).attr( 'name' ).replace( /\[(\d+)\]/, function( match, p1 ){
                        return '[' + index + ']';
                    });
                $(this).attr( 'name', name );

                var fid = $(this).attr( 'id' );
                if($(this).hasClass('sd-visual-editor')){
                    tinyMCE && tinyMCE.execCommand("mceRemoveEditor", false, fid);
                }
                fid = fid.replace( /-(\d+)-/, function( match, p1 ){
                        return '-' + index + '-';
                    });
                $(this).attr( 'id', fid );
            });

            $('label', $clone).each(function(){
                var l = $(this);
                var lfor = l.attr( 'for' ).replace( /-(\d+)-/, function( match, p1 ){
                    $('.tts-label-num', l).html(index + 1);
                            return '-' + index + '-';
                        });
                l.attr( 'for', lfor );
            });  
        });
        $('.sd-visual-editor', $cloneWrap).each(function(){
            var fid = $(this).attr('id');
            tinyMCE && tinyMCE.execCommand("mceAddEditor", false, fid);
        });
        
    }
    function sort_stickers_key($cloneWrap){
        $cloneWrap.find('li').each(function(index){
            var $clone = $(this);
            $(':input', $clone).each(function(){
                var name = $(this).attr( 'name' ).replace( /\[(\d+)\]/, function( match, p1 ){
                        return '[' + index + ']';
                    });
                $(this).attr( 'name', name );
            });
        });
        
    }
    
    $(document).ready(function(){
        $('.tts-mb-shortable').sortable({
            axis: "y",
            cursor: "move",
            distance: 5,
            connectWith: ">li",
            items: ">li",
            placeholder: "sortable-placeholder",
            forcePlaceholderSize: true,
            handle: '.tts-sahandle'
        });
        $('.tts-mb-clone-wrap').sortable({
            axis: "y",
            cursor: "move",
            distance: 5,
            connectWith: ".tts-field-cloneable",
            items: ".tts-field-cloneable",
            placeholder: "sortable-placeholder",
            forcePlaceholderSize: true,
            handle: '.tts-mb-clone-sahandle',
            update: function(event, ui){
                sort_clone_key($(event.target));
            },
            start: function(event, ui){
                $('.sd-visual-editor', $(event.target)).each(function(){
                    var fid = $(this).attr('id');
                    tinyMCE && tinyMCE.execCommand("mceRemoveEditor", false, fid);
                });
            },
            stop: function(event, ui){
                $('.sd-visual-editor', $(event.target)).each(function(){
                    var fid = $(this).attr('id');
                    tinyMCE && tinyMCE.execCommand("mceAddEditor", false, fid);
                });
            }
        });
        
        $('body').on('click', '.tts-add-media', function(e){
            e.preventDefault();
            var $field = $(this).prev('input');
            var $preview = false;
            if($(this).hasClass('tss-has-prev') && $(this).next('.tts-media-prev').size()){
                $preview = $(this).next('.tts-media-prev');
            }
            file_frame = wp.media.frames.file_frame = wp.media({
                title: 'Select Image',
                button: {
                text: 'Use This Image'
                },
                multiple: false // Set to true to allow multiple files to be selected
            });
            file_frame.on( 'select', function() {
                var attachment = file_frame.state().get('selection').toJSON();
                attachment = attachment[0];
                $field.val(attachment.url);
                if($preview){
                    $preview.html('<img src="'+attachment.url+'" alt=""/>');
                }
            });
            file_frame.open();
        });
        
        $('body').on('click', '.tts-mb-clone-delete', function(e){
            e.preventDefault();
            var $cloneW = $(this).closest('.tts-mb-clone-wrap');
            if($cloneW.find('.tts-field-cloneable').length <2){
                return false;
            }
            $(this).closest('.tts-field-cloneable').find('.sd-visual-editor').each(function(){
                var fid = $(this).attr('id');
                tinyMCE && tinyMCE.execCommand("mceRemoveEditor", false, fid);
            });
            
            $(this).closest('.tts-field-cloneable').remove();
            sort_clone_key($cloneW);
        });
        
        $('body').on('click', '.tts-mb-clone-add', function(e){
            e.preventDefault();
            var $cloneW = $(this).prev('.tts-mb-clone-wrap'),
                $clone = $cloneW.find('.tts-field-cloneable').last().clone();
            $('.mce-tinymce', $clone).remove();
            $(':input', $clone).each(function(){
                $(this).val('');
                $(this).attr('style', '');
            });
            
            
            $(this).prev('.tts-mb-clone-wrap').append($clone);
            sort_clone_key($cloneW);
        });
        $('.tts-add-stiker').on('click', function(e){
            e.preventDefault();
            var $cloneW = $(this).prev('.tts-stikers-ul'),
                $clone = $cloneW.find('li').last().clone();

            $(':input', $clone).each(function(){
                $(this).val('');
            });
            $('.tts-media-prev', $clone).html('');
            
            $cloneW.append($clone);
            sort_stickers_key($cloneW);
        });
        
        $('.tts-stikers-ul').sortable({
            axis: "x",
            cursor: "move",
            distance: 5,
            connectWith: ">li",
            items: ">li",
            placeholder: "sortable-placeholder",
            forcePlaceholderSize: true,
            handle: '.tts-sahandle',
            update: function(event, ui){
                sort_stickers_key($(event.target));
            }
        });
        $('body').on('click', '.tts-delete-sticker', function(e){
            e.preventDefault();
            var $cloneW = $(this).closest('.tts-stikers-ul');
            if($cloneW.find('li').length <2){
                return false;
            }
            $(this).closest('li').remove();
            sort_stickers_key($cloneW);
        });
        $('.sd-visual-editor').each(function(){
            var fid = $(this).attr('id');
            tinyMCE && tinyMCE.execCommand("mceAddEditor", false, fid);
        });
        var $modal = $('#tbs-modal');
        
        
        // modal
        var modalAjaxHandler = false;
        var $tbsModal = $("#tbs-modal").dialog({
            autoOpen: false,
            modal: true,
            width: Math.min($(window).width() - 20, 640),
            minWidth: 480,
            minHeight: 140,
            draggable: false,
            resizable: false,
            position: {my: "center top", at:"center top+64"},
            close: function(){
                var modalData = $tbsModal.data('modaldata');
                modalData.closeCallback &&(typeof modalData.closeCallback === "function") && modalData.closeCallback.call();
                $tbsModal.trigger('close.tbsmodal');
            },
            open: function(){
                var modalData = $tbsModal.data('modaldata');
                modalData.contentCallback &&(typeof modalData.contentCallback === "function") && modalData.contentCallback.call();
                $tbsModal.trigger('open.tbsmodal');
            }
        });
        $tbsModal.on('contentLoaded.tbsmodal', function(e){
            $tbsModal.find('#modal-loader').removeClass('tbs-active');
            $tbsModal.find('.tbs-date-field').datepicker({
                dateFormat: "yy-mm-dd",
                onSelect: function(date, ui) {
                    var $t = $(this);
                    if($t.hasClass("tbs-dtf-linked-max") && $t.data("maxfield")){
                        $($t.data("maxfield")).datepicker("option", "minDate", new Date(date));
                    }
                }
            });
            $tbsModal.find('.tbs-custom-select').selectmenu({});
            $tbsModal.find('.tbs-wp-editor-holder textarea').each(function(){
                tbsTinyMceInit($(this));
            });
        });
        $tbsModal.on('contentNotLoaded.tbsmodal', function(e){
            $tbsModal.find('#modal-loader').removeClass('tbs-active');
        });
        $tbsModal.on('close.tbsmodal', function(e){
            $tbsModal.find('.tbs-wp-editor-holder textarea').each(function(){
                tbsKillMceEditor($(this));
            });
            $tbsModal.find('.tbs-modal-inner').html('');
            $tbsModal.find('#modal-loader').removeClass('tbs-active');
            $tbsModal.data('modaldata', {});
            $tbsModal.dialog("option", "buttons", []);
            $tbsModal.dialog("option", "title", "");
            modalAjaxHandler = false;
            
        });
        $tbsModal.on('change', '#cd-location', function(e){
            if($(this).val() === 'tbs_custom'){
                $('#custom-location-field-wrap').addClass('tbs-active');
            }else{
                $('#custom-location-field-wrap').removeClass('tbs-active');
            }
        });
        $('#add-course-date').on('click', function(e){
            e.preventDefault();
            $tbsModal.dialog("option", "title", "Add a Course Date");
            $tbsModal.data('modaldata', {
                contentCallback: function(){
                    if(modalAjaxHandler){
                        return false;
                    }
                    $tbsModal.find('#modal-loader').addClass('tbs-active');
                    modalAjaxHandler = $.ajax({
                        type: "POST",
                        url: WPTBS.ajaxUrl,
                        data: {
                            action: 'tbs_get_course_date_edit_form',
                            course_id: $tbsModal.data('courseid'),
                            _tbsnonce: $tbsModal.data('nonce'),
                            type: 'add'
                        },
                        dataType: 'json',
                        success: function(response){
                            modalAjaxHandler = false;
                            if(typeof response === 'undefined'){
                                $tbsModal.trigger('contentNotLoaded.tbsmodal');
                                return;
                            }
                            if(response.status !== 'OK'){
                                $tbsModal.trigger('contentNotLoaded.tbsmodal');
                                return;
                            }
                            $tbsModal.find('.tbs-modal-inner').html(response.html);
                            $tbsModal.trigger('contentLoaded.tbsmodal');
                        },
                        error: function(){
                            $tbsModal.find('#modal-loader').removeClass('tbs-active');
                            modalAjaxHandler = false;
                        }
                    });
                }
            });
            $tbsModal.dialog("option", "title", "Add a Course Date");
            $tbsModal.dialog("option", "buttons",[
                {
                    text: "Save",
                    click: function(e){
                        if(modalAjaxHandler){
                            return false;
                        }
                        var data = {}, error = false;
                        
                        if($('#cd-is-private', $tbsModal).is(':checked')){
                            data['is_private'] = 1;
                        }else{
                            data['is_private'] = 0;
                        }
                        // Get start date
                        if($('#cd-start-date', $tbsModal).val()){
                            data['start_date'] = $('#cd-start-date', $tbsModal).val();
                            $('#cd-start-date', $tbsModal).removeClass('tbs-field-error');
                        }else{
                            $('#cd-start-date', $tbsModal).addClass('tbs-field-error');
                            error = true;
                        }
                        // Get duration
                        if($('#cd-duration', $tbsModal).val()){
                            data['duration'] = $('#cd-duration', $tbsModal).val();
                            $('#cd-duration', $tbsModal).removeClass('tbs-field-error');
                        }else{
                            $('#cd-duration', $tbsModal).addClass('tbs-field-error');
                            error = true;
                        }
                        // Get end date
                        if($('#cd-duration', $tbsModal).val() <= 1){
                            data['end_date'] = $('#cd-start-date', $tbsModal).val();
                        }else if($('#cd-end-date', $tbsModal).val()){
                            data['end_date'] = $('#cd-end-date', $tbsModal).val();
                            $('#cd-end-date', $tbsModal).removeClass('tbs-field-error');
                        }else{
                            $('#cd-end-date', $tbsModal).addClass('tbs-field-error');
                            error = true;
                        }
                        // Get price
                        if($('#cd-price', $tbsModal).val()){
                            data['price'] = $('#cd-price', $tbsModal).val();
                            $('#cd-price', $tbsModal).removeClass('tbs-field-error');
                        }else{
                            $('#cd-price', $tbsModal).addClass('tbs-field-error');
                            error = true;
                        }
                        // Get stock
                        if($('#cd-stock', $tbsModal).val()){
                            data['stock'] = $('#cd-stock', $tbsModal).val();
                        }
                        // Get trainer
                        if($('#cd-trainer', $tbsModal).val()){
                            data['trainer'] = $('#cd-trainer', $tbsModal).val();
                            $('#cd-trainer', $tbsModal).removeClass('tbs-field-error');
                        }else{
                            $('#cd-trainer', $tbsModal).addClass('tbs-field-error');
                            error = true;
                        }
                        // Get location
                        if($('#cd-location', $tbsModal).val()){
                            data['location'] = $('#cd-location', $tbsModal).val();
                            $('#cd-location', $tbsModal).removeClass('tbs-field-error');
                        }else{
                            $('#cd-location', $tbsModal).addClass('tbs-field-error');
                            error = true;
                        }
                        // Get custom location
                        if('tbs_custom' === $('#cd-location', $tbsModal).val()){
                            if($('#cd-custom-location', $tbsModal).val()){
                                data['custom_location'] = $('#cd-custom-location', $tbsModal).val();
                                $('#cd-custom-location', $tbsModal).removeClass('tbs-field-error');
                            }else{
                                $('#cd-custom-location', $tbsModal).addClass('tbs-field-error');
                                error = true;
                            }
                        }else{
                            data['custom_location'] = $('#cd-custom-location', $tbsModal).val();
                        }
                            
                        // Get joining instruction
                        var joiningIns = tbsTinyMceGetContent($('#cdjoininginstruction', $tbsModal));
                        if(joiningIns){
                            data['joining_instruction'] = joiningIns;
                        }
                        
                        // Get map
                        if($('#cd-map', $tbsModal).val()){
                            data['map'] = $('#cd-map', $tbsModal).val();
                        }
                        
                        // Get start-finish-time
                        if($('#cd-start-finish-time', $tbsModal).val()){
                            data['start_finish_time'] = $('#cd-start-finish-time', $tbsModal).val();
                            $('#cd-start-finish-time', $tbsModal).removeClass('tbs-field-error');
                        }else{
                            $('#cd-start-finish-time', $tbsModal).addClass('tbs-field-error');
                            error = true;
                        }
                        if(error){
                            return;
                        }
                        
                        $tbsModal.find('#modal-loader').addClass('tbs-active');
                        modalAjaxHandler = $.ajax({
                            type: "POST",
                            url: WPTBS.ajaxUrl,
                            data: {
                                action: 'tbs_add_course_date',
                                course_id: $tbsModal.data('courseid'),
                                _tbsnonce: $tbsModal.data('nonce'),
                                date_data: data
                            },
                            dataType: 'json',
                            success: function(response){
                                $('#tbs-no-course-found').length && $('#tbs-no-course-found').remove();
                                $tbsModal.find('#modal-loader').removeClass('tbs-active');
                                modalAjaxHandler = false;
                                if(typeof response === 'undefined'){
                                    alert('Failed! Please try again later.');
                                    return;
                                }
                                if(response.status !== 'OK'){
                                    alert('Failed! Please try again later.');
                                    return;
                                }
                                $('#course-dates-list table tbody').append(response.html);
                                $tbsModal.dialog('close');
                            },
                            error: function(){
                                $tbsModal.find('#modal-loader').removeClass('tbs-active');
                                modalAjaxHandler = false;
                            }
                        });
                    }
                }
            ]);
            $tbsModal.dialog("open");
        });
        $('body').on('click', '.tbs-btn-edit-course', function(e){
            var $button = $(this);
            e.preventDefault();
            $tbsModal.dialog("option", "title", "Edit Course Date");
            $tbsModal.data('modaldata', {
                contentCallback: function(){
                    if(modalAjaxHandler){
                        return false;
                    }
                    $tbsModal.find('#modal-loader').addClass('tbs-active');
                    modalAjaxHandler = $.ajax({
                        type: "POST",
                        url: WPTBS.ajaxUrl,
                        data: {
                            action: 'tbs_get_course_date_edit_form',
                            course_id: $tbsModal.data('courseid'),
                            _tbsnonce: $tbsModal.data('nonce'),
                            course_date_id: $button.data('coursedateid'),
                            type: 'edit'
                        },
                        dataType: 'json',
                        success: function(response){
                            modalAjaxHandler = false;
                            if(typeof response === 'undefined'){
                                $tbsModal.trigger('contentNotLoaded.tbsmodal');
                                return;
                            }
                            if(response.status !== 'OK'){
                                $tbsModal.trigger('contentNotLoaded.tbsmodal');
                                return;
                            }
                            $tbsModal.find('.tbs-modal-inner').html(response.html);
                            $tbsModal.trigger('contentLoaded.tbsmodal');
                        },
                        error: function(){
                            $tbsModal.find('#modal-loader').removeClass('tbs-active');
                            modalAjaxHandler = false;
                        }
                    });
                }
            });
            $tbsModal.dialog("option", "buttons",[
                {
                    text: "Update",
                    click: function(e){
                        if(modalAjaxHandler){
                            return false;
                        }
                        var data = {}, error = false;
                        if($('#cd-course-id', $tbsModal).val()){
                            data['course_date_id'] = $('#cd-course-id', $tbsModal).val();
                            $('#cd-course-id', $tbsModal).removeClass('tbs-field-error');
                        }else{
                            $('#cd-start-date', $tbsModal).addClass('tbs-field-error');
                            error = true;
                        }
                        if($('#cd-is-private', $tbsModal).is(':checked')){
                            data['is_private'] = 1;
                        }else{
                            data['is_private'] = 0;
                        }
                        // Get start date
                        if($('#cd-start-date', $tbsModal).val()){
                            data['start_date'] = $('#cd-start-date', $tbsModal).val();
                            $('#cd-start-date', $tbsModal).removeClass('tbs-field-error');
                        }else{
                            $('#cd-start-date', $tbsModal).addClass('tbs-field-error');
                            error = true;
                        }
                        // Get duration
                        if($('#cd-duration', $tbsModal).val()){
                            data['duration'] = $('#cd-duration', $tbsModal).val();
                            $('#cd-duration', $tbsModal).removeClass('tbs-field-error');
                        }else{
                            $('#cd-duration', $tbsModal).addClass('tbs-field-error');
                            error = true;
                        }
                        if($('#cd-duration', $tbsModal).val() <= 1){
                            data['end_date'] = $('#cd-start-date', $tbsModal).val();
                        }else if($('#cd-end-date', $tbsModal).val()){
                            data['end_date'] = $('#cd-end-date', $tbsModal).val();
                            $('#cd-end-date', $tbsModal).removeClass('tbs-field-error');
                        }else{
                            $('#cd-end-date', $tbsModal).addClass('tbs-field-error');
                            error = true;
                        }
                        // Get price cd-stock
                        if($('#cd-price', $tbsModal).val()){
                            data['price'] = $('#cd-price', $tbsModal).val();
                            $('#cd-price', $tbsModal).removeClass('tbs-field-error');
                        }else{
                            $('#cd-price', $tbsModal).addClass('tbs-field-error');
                            error = true;
                        }
                        // Get stock
                        if($('#cd-stock', $tbsModal).val()){
                            data['stock'] = $('#cd-stock', $tbsModal).val();
                        }
                        // Get trainer
                        if($('#cd-trainer', $tbsModal).val()){
                            data['trainer'] = $('#cd-trainer', $tbsModal).val();
                            $('#cd-trainer', $tbsModal).removeClass('tbs-field-error');
                        }else{
                            $('#cd-trainer', $tbsModal).addClass('tbs-field-error');
                            error = true;
                        }
                        // Get location
                        if($('#cd-location', $tbsModal).val()){
                            data['location'] = $('#cd-location', $tbsModal).val();
                            $('#cd-location', $tbsModal).removeClass('tbs-field-error');
                        }else{
                            $('#cd-location', $tbsModal).addClass('tbs-field-error');
                            error = true;
                        }
                        // Get custom location
                        if($('#cd-custom-location', $tbsModal).val()){
                            data['custom_location'] = $('#cd-custom-location', $tbsModal).val();
                            $('#cd-custom-location', $tbsModal).removeClass('tbs-field-error');
                        }else{
                            $('#cd-custom-location', $tbsModal).addClass('tbs-field-error');
                            if('tbs_custom' === $('#cd-trainer', $tbsModal).val()){
                                error = true;
                            }
                        }
                        // Get joining instruction
                        var joiningIns = tbsTinyMceGetContent($('#cdjoininginstruction', $tbsModal));
                        if(joiningIns){
                            data['joining_instruction'] = joiningIns;
                        }
                        
                        // Get map
                        if($('#cd-map', $tbsModal).val()){
                            data['map'] = $('#cd-map', $tbsModal).val();
                        }
                        
                        // Get start-finish-time
                        if($('#cd-start-finish-time', $tbsModal).val()){
                            data['start_finish_time'] = $('#cd-start-finish-time', $tbsModal).val();
                            $('#cd-start-finish-time', $tbsModal).removeClass('tbs-field-error');
                        }else{
                            $('#cd-start-finish-time', $tbsModal).addClass('tbs-field-error');
                            error = true;
                        }
                        if(error){
                            return;
                        }
                        $tbsModal.find('#modal-loader').addClass('tbs-active');
                        modalAjaxHandler = $.ajax({
                            type: "POST",
                            url: WPTBS.ajaxUrl,
                            data: {
                                action: 'tbs_update_course_date',
                                course_id: $tbsModal.data('courseid'),
                                _tbsnonce: $tbsModal.data('nonce'),
                                course_date_id: data['course_date_id'],
                                date_data: data
                            },
                            dataType: 'json',
                            success: function(response){
                                $('#tbs-no-course-found').length && $('#tbs-no-course-found').remove();
                                $tbsModal.find('#modal-loader').removeClass('tbs-active');
                                modalAjaxHandler = false;
                                if(typeof response === 'undefined'){
                                    alert('Failed! Please try again later.');
                                    return;
                                }
                                if(response.status !== 'OK'){
                                    alert('Failed! Please try again later.');
                                    return;
                                }
                                $button.closest('tr').replaceWith(response.html);
                                $tbsModal.dialog('close');
                            },
                            error: function(){
                                $tbsModal.find('#modal-loader').removeClass('tbs-active');
                                modalAjaxHandler = false;
                            }
                        });
                    }
                }
            ]);
            $tbsModal.dialog("open");
        });
        $('body').on('click', '.tbs-btn-delete-course', function(e){
            var $button = $(this);
            e.preventDefault();
            $tbsModal.dialog("option", "title", "Edit Course Date");
            $tbsModal.data('modaldata', {
                contentCallback: function(){
                    $tbsModal.find('.tbs-modal-inner').html("These Course Date will be permanently deleted and cannot be recovered. Are you sure?");
                }
            });
            $tbsModal.dialog("option", "buttons",[
                {
                    text: "Delete the Date",
                    click: function(e){
                        if(modalAjaxHandler){
                            return false;
                        }
                        $tbsModal.find('#modal-loader').addClass('tbs-active');
                        modalAjaxHandler = $.ajax({
                            type: "POST",
                            url: WPTBS.ajaxUrl,
                            data: {
                                action: 'tbs_delete_course_date',
                                course_id: $tbsModal.data('courseid'),
                                _tbsnonce: $tbsModal.data('nonce'),
                                course_date_id: $button.data('coursedateid')
                            },
                            dataType: 'json',
                            success: function(response){
                                $tbsModal.find('#modal-loader').removeClass('tbs-active');
                                modalAjaxHandler = false;
                                if(typeof response === 'undefined'){
                                    alert('Failed! Please try again later.');
                                    return;
                                }
                                if(response.status !== 'OK'){
                                    alert('Failed! Please try again later.');
                                    return;
                                }
                                $button.closest('tr').remove();
                                $tbsModal.dialog('close');
                            },
                            error: function(){
                                $tbsModal.find('#modal-loader').removeClass('tbs-active');
                                modalAjaxHandler = false;
                            }
                        });
                    }
                },
                {
                    text: "Cancel",
                    click: function(){
                        $tbsModal.dialog("close");
                    }
                }
            ]);
            $tbsModal.dialog("open");
        });
    });
})(jQuery);