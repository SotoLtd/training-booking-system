(function($){
    var tbsBookings = window.tbsBookings = tbsBookings  || {};
    tbsBookings.Models = {};
    tbsBookings.Collections = {};
    tbsBookings.Storage = {};
    tbsBookings.Views = {};
    tbsBookings.errorClass = 'tbs-field-error';
    
    
    // Booking Model
    tbsBookings.Models.Booking = Backbone.Model.extend({
        defaults: {
            id: 0,
            status: "tbs-draft",
            dataEntryComplete: false,
            emailOptin: false,
            suppressOrderEmails: false,
            onlineFormUrl: "",
            address: {},
            items: [],
            delegates: [],
            totals: "",
            saveData: true
        },
        validateData: function(){
            var failed = false;
            this.set("saveData", true);
            if( tbsBookings.delegateListContainerView.validateEmails() ){
                failed = true;
            }
            if(!this.get("dataEntryComplete") ){
                this.set("saveData", !failed);
                return;
            }
            if( tbsBookings.addressView.validate() ){
                failed = true;
            }
            if( tbsBookings.delegateListContainerView.validate() ){
                failed = true;
            }
            this.set("saveData", !failed);
            
        },
        loadData: function(){
            if(!this.get("id")){
                return;
            }
            this.trigger("request");
            var model = this;
            $.ajax({
                url: TBS_Booking_Settings.ajaxUrl,
                method: "get",
                data: {
                    action: "tbs_load_booking",
                    _tbsnonce: TBS_Booking_Settings.fetchNonce,
                    order_id: this.get("id")
                },
                dataType: "json",
                success: function(response){
                    if(!response || !response.status || "OK" !== response.status){
                        model.trigger("booking.savefailed");
                        return;
                    }
                    model.set(response.bookingData);
                    model.trigger("sync", model, {}, {});
                }
                
            }).fail(function(){
                model.trigger("booking.savefailed");
            });
        },
        saveData: function(){
            this.set("status", tbsBookings.Storage.general.get("status"));
            this.set("dataEntryComplete", tbsBookings.Storage.general.get("dataEntryComplete"));
            this.set("emailOptin", tbsBookings.Storage.general.get("emailOptin"));
            this.set("suppressOrderEmails", tbsBookings.Storage.general.get("suppressOrderEmails"));
            this.set("delegates", tbsBookings.delegateListContainerView.getDelegates());
            this.set("address", tbsBookings.Storage.address.toJSON());
            this.validateData();
            if(!this.get("saveData")){
                this.trigger("booking.invalid");
                return;
            }
            this.trigger("request");
            var itemsData = tbsBookings.Storage.items.map(function(item){
                return {
                    id: item.get("id"),
                    delegates: item.get("delegates"),
                    item_id: item.get("itemID"),
                    total: item.get("total"),
                    subtotal: item.get("subtotal")
                };
            });
            var model = this;
            $.ajax({
                url: TBS_Booking_Settings.ajaxUrl,
                method: "post",
                data: {
                    action: "tbs_save_booking",
                    _tbsnonce: TBS_Booking_Settings.saveNonce,
                    order_id: model.get("id"),
                    status: model.get("status"),
                    data_entry_complete: model.get("dataEntryComplete"),
                    email_optin: model.get("emailOptin"),
                    suppress_order_emails: model.get("suppressOrderEmails"),
                    customer_data: model.get("address"),
                    items: itemsData,
                    delegates: model.get("delegates")
                },
                dataType: "json",
                success: function(response){
                    if(!response || !response.status || "OK" !== response.status){
                        model.trigger("booking.savefailed");
                        return;
                    }
                    window.location.href = response.editUrl;
                }
                
            }).fail(function(){
                model.trigger("booking.savefailed");
            });
        },
        generateOnlineUrl: function(){
            var model = this;
            $.ajax({
                url: TBS_Booking_Settings.ajaxUrl,
                method: "post",
                data: {
                    action: "tbs_generate_online_url",
                    _tbsnonce: TBS_Booking_Settings.saveNonce,
                    order_id: model.get("id")
                },
                dataType: "json",
                success: function(response){
                    if(!response || !response.status || "OK" !== response.status){
                        model.trigger("booking.generateurlfailed");
                        return;
                    }
                    model.set("onlineFormUrl", response.onlineFormUrl);
                    model.trigger("booking.generateurlsuccess", model, {}, {});
                }
                
            }).fail(function(){
                model.trigger("booking.generateurlfailed");
            });
        }
    });
    
    // General Model
    tbsBookings.Models.General = Backbone.Model.extend({
        defaults: {
            "status": "tbs-draft",
            "dataEntryComplete": false,
            "emailOptin": false,
            "suppressOrderEmails": false,
            "onlineFormUrl": ""
        }
    });
    // Adress Model
    tbsBookings.Models.Address = Backbone.Model.extend({
        defaults: {
            "first_name": "",
            "last_name": "",
            "company": "",
            "address_1": "",
            "address_2": "",
            "city": "",
            "postcode": "",
            "country": "GB",
            "state": "",
            "email": "",
            "phone": ""
        }
    });
    // Delegat Model
    tbsBookings.Models.Delegate = Backbone.Model.extend({
        defaults: {
            "id": 0,
            "first_name": "",
            "last_name": "",
            "email": "",
            "notes": "",
            "userID": 0,
            "courseDateID": 0,
            "courseID": 0
        }
    });
    // Item model
    tbsBookings.Models.Item = Backbone.Model.extend({
        defaults: {
            "id": false,
            "itemID": false,
            "courseID": false,
            "isPrivate": false,
            "isAccridated": false,
            "startDate": 0,
            "durtation": 0,
            "price": "",
            "priceVal": 0,
            "maxDelegates": 0,
            "places": 0,
            "title": "",
            "courseDateTitle": "",
            "coursePermalink": "",
            "delegates": 0,
            "total": 0,// This price is after applying discounts, Taxes etc.
            "subtotal": 0,// This price is before applying any discounts or Taxes
            "vat": 0,
            "trainerID": false,
            "trainerName": "",
            "location": "",
        },
        addDelegates: function(delegates){
            var oldDelegates = this.get("delegates"),
                changedDelegates = oldDelegates + delegates,
                unitCost = this.getUnitPrice(),
                total, subtotal;
            subtotal = !this.get('isPrivate') ? this.get('priceVal') * changedDelegates : this.get('priceVal');
            total = !this.get('isPrivate') ? unitCost * changedDelegates : unitCost;
            this.set( "delegates", changedDelegates );
            this.set( "total",  total);
            this.set( "subtotal",  subtotal);
        },
        getUnitPrice: function(){
            var total = this.get("total"),
                subtoal = this.get("subtotal"),
                delegates = this.get("delegates"), 
                isPrivate = this.get("isPrivate"), 
                unitCost;
            if(!delegates){
                return this.get("priceVal");
            }
            if(subtoal === total){
                return !isPrivate ? this.get("priceVal") : total;
            }
            delegates = Math.max(1, delegates);
            unitCost = !isPrivate ? parseFloat( total / delegates ) : total;
            return unitCost;
        },
        templateData: function(){
            var data = {
                url: this.get("coursePermalink"),
                title: this.get("courseDateTitle"),
                isPrivate: this.get("isPrivate"),
                id: this.get("id"),
                unitPrice: this.getUnitPrice(),
                total: this.get("total"),
                delegates: this.get("delegates"),
                delegateStock: this.get("places")
            };
            return data;
        },
        manageDelegateStock: function(newDelegates){
            var oldDelegates = this.get("delegates"),
                change, newStock;
            change = newDelegates - oldDelegates;
            if(newDelegates === oldDelegates){
                return;
            }
            newStock = change > 0 ? this.get("places") - change : this.get("places") + change;
            this.set("places", newStock );
        }
    });
    // Course Date model for modal listing
    tbsBookings.Models.CourseDate = Backbone.Model.extend({
        defaults: {
            "id": false,
            "courseID": false,
            "isPrivate": false,
            "isAccridated": false,
            "startDate": 0,
            "durtation": 0,
            "price": "",
            "priceVal": 0,
            "maxDelegates": 0,
            "places": 0,
            "title": "",
            "courseDateTitle": "",
            "coursePermalink": "",
            "trainerID": false,
            "trainerName": "",
            "location": ""
            
        },
        reducePlaces: function(place){
            this.set("places", Math.max(0, this.get("places") - place ) );
        }
    });
    // Delegates Collection
    tbsBookings.Collections.Delegates = Backbone.Collection.extend({
        model: tbsBookings.Models.Delegate
    });
    // Booking Items Collection
    tbsBookings.Collections.Items = Backbone.Collection.extend({
        model: tbsBookings.Models.Item,
        url: function(){
            return TBS_Booking_Settings.ajaxUrl + "?action=tbs_booking_get_items&_tbsnonce=" + TBS_Booking_Settings.fetchNonce;
        },
        parse: function(response){
            if(!response || !response.status || "OK" !== response.status){
                return [];
            }
            tbsBookings.Storage.booking.set("id", response.ID);
            tbsBookings.Storage.booking.set("totals", response.totals);
            return response.items;
        }
    });
    
    // Course Dates list Collection for modal listing
    tbsBookings.Collections.CourseDates = Backbone.Collection.extend({
        model: tbsBookings.Models.CourseDate,
        url: function(){
             return TBS_Booking_Settings.ajaxUrl + "?action=tbs_booking_get_course_dates&_tbsnonce=" + TBS_Booking_Settings.fetchNonce;
        },
        parse: function(response){
            if(!response || !response.status || "OK" !== response.status){
                return [];
            }
            return response.courseDates;
        }
    });
    // Customer Status View
    tbsBookings.Views.General = Backbone.View.extend({
        el: $("#tbs-general-settings"),
        events: {
            "change #booking-status": "updateStatus",
            "click #data-entry-complete": "updateDataEntryComplete",
            "click #email-optin": "updateEmailOptin",
            "click #suppress-order-emails": "updateSuppressOrderEmails",
            "click #copytoclipboard-online-form-url": "copyUrltoClipBoard",
            "click #generate-online-form-url": "generateOnlineFormUrl"
        },
        initialize: function(){
            this.listenTo(tbsBookings.Storage.booking, "change:status", this.renderStatus);
            this.listenTo(tbsBookings.Storage.booking, "change:dataEntryComplete", this.renderDataEntryComplete);
            this.listenTo(tbsBookings.Storage.booking, "change:emailOptin", this.renderEmailOptin);
            this.listenTo(tbsBookings.Storage.booking, "change:suppressOrderEmails", this.renderSuppressOrderEmails);
            this.listenTo(tbsBookings.Storage.booking, "change:onlineFormUrl", this.renderOnlineFormUrl);
            this.render();
        },
        render: function(){
            this.$("#booking-status").val(this.model.get("status"));
            this.$("#data-entry-complete").prop("checked", this.model.get("dataEntryComplete"));
            this.$("#email-optin").prop("checked", this.model.get("emailOptin"));
            this.$("#suppress-order-emails").prop("checked", this.model.get("suppressOrderEmails"));
        },
        renderStatus: function(booking, val){
            this.$("#booking-status").val(val);
            this.model.set("status", val, {silent: true});
        },
        renderDataEntryComplete: function(booking, val){
            this.$("#data-entry-complete").prop("checked", val);
            this.model.set("dataEntryComplete", val, {silent: true});
        },
        renderEmailOptin: function(booking, val){
           this.$("#email-optin").prop("checked", val);
           this.model.set("emailOptin", val, {silent: true});
        },
        renderSuppressOrderEmails: function(booking, val){
           this.$("#suppress-order-emails").prop("checked", val);
           this.model.set("suppressOrderEmails", val, {silent: true});
        },
        renderOnlineFormUrl: function(booking, val){
           this.$("#online-form-url").val(val).attr("placeholder", "Generate url");
           this.model.set("onlineFormUrl", val, {silent: true});
        },
        updateStatus: function(e){
            this.model.set("status", this.$("#booking-status").val(), {silent: true});
        },
        updateDataEntryComplete: function(e){
            this.model.set("dataEntryComplete", this.$("#data-entry-complete").is(":checked"), {silent: true});
            
            if(this.$("#data-entry-complete").is(":checked")){
                this.$("#email-optin").prop("checked", true);
                this.model.set("emailOptin", true, {silent: true});
            }else{
                this.$("#booking-status").val("tbs-draft");
                this.model.set("status", "tbs-draft", {silent: true});
            }
        },
        updateEmailOptin: function(e){
            this.model.set("emailOptin", this.$("#email-optin").is(":checked"), {silent: true});
        },
        updateSuppressOrderEmails: function(e){
           this.model.set("suppressOrderEmails", this.$("#suppress-order-emails").is(":checked"), {silent: true});
        },
        copyUrltoClipBoard: function(e){
           var copyText = this.$("#online-form-url");
           if(!copyText.val()){
               return;
           }
           copyText.select();
           document.execCommand("copy");
        },
        generateOnlineFormUrl: function(e){
           this.$("#online-form-url").val("").attr("placeholder", "Generating...");
           tbsBookings.Storage.booking.generateOnlineUrl();
        }
        
    });
    // Customer Address View
    tbsBookings.Views.Address = Backbone.View.extend({
        el: $("#booking-customer-address"),
        events: {
            "change .booking-address-input": "update"
        },
        initialize: function(){
            this.listenTo(tbsBookings.Storage.booking, "change:address", this.loadFromBooking);
        },
        render: function(){
            _.each(this.model.toJSON(), function(val, key){
                this.$('[data-modelkey="' + key + '"]').val(val);
            },this);
        },
        update: function(e){
            var key = $(e.target).data("modelkey"), 
                val = $(e.target).val();
            this.model.set(key, val, {silent: true});
        },
        loadFromBooking: function(booking, address){
            this.model.set(address);
            this.render();
        },
        validate: function(){
            var hasError = false;
            this.$(".tbs-rquired-field").each(function(){
                var $this = $(this);
                _.isEmpty( $this.val() ) ? ( $this.addClass(tbsBookings.errorClass), (hasError = true)) : $this.removeClass(tbsBookings.errorClass);
            });
            return hasError;
        }
    });
    // Booking Delegate View
    tbsBookings.Views.Delgate = Backbone.View.extend({
        tagName: "div",
        className: "booking-delegate",
        events: {
            "change .delegate-field": "update"
        },
        template: _.template($("#delegate-fields-template").html()),
        initialize: function(){
            // Listen to add event when an item is added
            this.listenTo(this.model, "remove", this.remove);
            // List to change event to update event ciew
            this.listenTo(this.model, "change", this.render);
        },
        render: function(){
            // Render the view for this item
            this.$el.html(this.template(this.model.attributes));
            // Return this for chaning purpose
            return this;
        },
        update: function(e){
            var key = $(e.target).data("modelkey"), 
                val = $(e.target).val();
            this.model.set(key, val, {silent: true});
        }
    });
    // Booking Delegates list View
    tbsBookings.Views.DelgateList = Backbone.View.extend({
        tagName: "div",
        className: "booking-item-delegate-list clearfix",
        template: _.template($("#delegate-list-template").html()),
        initialize: function(options){
            // Get item title for this delegate list
            this.itemTitle = options.itemTitle;
            // Listen to add event when an item is added
            this.listenTo(this.collection, "add", this.renderDelegate);
            // Render the delegate list
            this.render();
        },
        render: function(){
            // Iterate over eacah delegate to create view and render item
            this.$el.html(this.template({itemTitle: this.itemTitle}));
            this.collection.each(function(delegateModel){
                this.renderDelegate(delegateModel);
            }, this);
            return this;
        },
        renderDelegate: function(delegateModel){
            var delegatView = new tbsBookings.Views.Delgate({
                model: delegateModel
            });
            this.$(".booking-delegate-list").append(delegatView.render().el);
        }
    });
    
    // Booking Delegates list container View
    tbsBookings.Views.DelegatesListsContainer = Backbone.View.extend({
        el: $("#tbs-booking-delgates"),
        initialize: function(){
            this.lists = {};
            this.collection = tbsBookings.Storage.items;
            // Listen to change event when an item is change
            this.listenTo(this.collection, "change:delegates", this.changeList);
            // Listen to add event when an item is added
            this.listenTo(this.collection, "add", this.addList);
            // Listen to add event when an item is added
            this.listenTo(this.collection, "remove", this.removeList);
            // Listen to reset event when items are fetched from server
            this.listenTo(this.collection, "reset", this.resetList);
            // Listen to booking delegate changes
            this.listenTo(tbsBookings.Storage.booking, "sync", this.fillDelegateData);
        },
        fillDelegateData: function(booking){
            _.each(this.lists, function(listItem){
                listItem.delegateCollection.each(function(delegateModel){
                    var data = _.findWhere(booking.get("delegates"), {id: delegateModel.get("id"), courseDateID: delegateModel.get("courseDateID")});
                    data && delegateModel.set(data);
                });
            }, this);
        },
        resetList: function(){
            this.collection.each(function(item){
                this.changeList(item, item.get("delegates"));
            }, this);
            return this;
        },
        changeList: function(item, delegates){
            // Update a collection of delegates for this item
            // Check if the item list exists
            if(!_.has(this.lists, "item-" + item.get("id"))){
                // Does not exist
                // So add it
                this.addList(item);
                return;
            }
            
            var listItem = this.lists["item-" + item.get("id")],
                previousDelegatesNo = listItem.delegateCollection.length;
            
            if(delegates > listItem.delegateCollection.length){
                // Iterate through the additional number of delegates
                _.times(delegates - listItem.delegateCollection.length, function(n){
                    listItem.delegateCollection.add({
                        id: previousDelegatesNo + n + 1,
                        courseDateID: listItem.itemID,
                        courseID: listItem.courseID
                    });
                });
                return;
            }
            // Get the extra deligates needs to be removed
            var toRemove = listItem.delegateCollection.filter(function(delegateModel){
                return delegateModel.get("id") > delegates;
            });
            // Remove the extra delegates
            if(toRemove){
                listItem.delegateCollection.remove(toRemove);
            }
            
        },
        addList: function(item){
            // Add a collection of delegates for this item
            var listItem  = {};
            listItem.itemTitle = item.get("courseDateTitle");
            listItem.itemID = item.get("id");
            listItem.courseID = item.get("courseID");
            listItem.delegateCollection = new tbsBookings.Collections.Delegates;
            _.times(item.get("delegates"), function(n){
                listItem.delegateCollection.add({
                    id: n + 1,
                    courseDateID: listItem.itemID,
                    courseID: listItem.courseID
                });
            });
            listItem.view = new tbsBookings.Views.DelgateList({
                itemTitle: listItem.itemTitle,
                collection: listItem.delegateCollection
            });
            this.$el.append(listItem.view.render().el);
            this.lists["item-" + item.get("id")] = listItem;
        },
        removeList: function(item){
            // Check if the item list exists
            if(!_.has(this.lists, "item-" + item.get("id"))){
                // Does not exist
                // Do nothing
                return;
            }
            this.lists[ "item-" + item.get("id")].view.remove();
            delete this.lists[ "item-" + item.get("id")];
        },
        getDelegates: function(){
            var delegates = _.map(this.lists, function(listItem){
                return listItem.delegateCollection.toJSON();
            });
            return _.flatten(delegates);
        },
        validate: function(){
            var hasError = false;
            this.$(".tbs-rquired-field").each(function(){
                var $this = $(this);
                _.isEmpty( $this.val() ) ? ( $this.addClass(tbsBookings.errorClass), (hasError = true)) : $this.removeClass(tbsBookings.errorClass);
            });
            return hasError;
        },
        validateEmails: function(){
            var emails = {}, hasError = false;
            this.$('.delegate-field-email').each(function(){
                var $this = $(this), key;
                if( _.isEmpty($this.val()) ){
                    $this.removeClass(tbsBookings.errorClass);
                }else{
                    key = $this.data("coursedateid") + "_" + $this.val();
                    if(!_.isArray(emails[key])){
                        emails[key] = [];
                    }
                    emails[key].push($this);
                }
            });
            console.log(emails);
            _.each(emails, function($fields){
                if($fields.length < 2){
                    _.each($fields, function($field){
                        $field.removeClass(tbsBookings.errorClass);
                    });
                }else{
                    hasError = true;
                    _.each($fields, function($field){
                        $field.addClass(tbsBookings.errorClass);
                    });
                }
            });
            return hasError;
        }
        
    });
    // Booking View: Item
    tbsBookings.Views.Item = Backbone.View.extend({
        tagName: "tr",
        template: _.template($("#booking-item-template").html()),
        events: {
            "click .edit-item": "editItem",
            "click .remove-item": "removeItem",
            "click .save-item": "saveItem",
            "click .cancel-item": "cancelEditing"
        },
        initialize: function(){
            // Listen to models change events so that view is update automatically
            this.listenTo(this.model, "change", this.render);
            // When model is removed from items collection we will remove this view.
            this.listenTo(this.model, "remove", this.remove);
        },
        render: function(){
            // Render the view for this item
            this.$el.html(this.template(this.model.templateData()));
            // Return this for chaning purpose
            return this;
        },
        editItem: function(){
            // Enter edit mode
            this.$el.addClass("tbs-edit-mode");
        },
        cancelEditing: function(){
            // Exit edit mode
            this.$el.removeClass("tbs-edit-mode");
            this.render();
        },
        removeItem: function(){
            // Remove the item model from items collections
            tbsBookings.Storage.items.remove(this.model);
            // Now remove the view itself
            this.remove();
        },
        saveItem: function(){
            // Remove edit mode
            this.$el.removeClass( "tbs-edit-mode" );
            // Get input delegates number
            var delegates = parseFloat( this.$(".delegate_number").val() );
            var unitCost = parseFloat( this.$(".item-price").val() );
            // Recalculate the places available for this date
            //this.model.manageDelegateStock(delegates);
            // Set input delegates to the model
            // This will trigger change events so the view updates itself
            var subtotal = !this.model.get('isPrivate') ? this.model.get('priceVal') * delegates : this.model.get('priceVal');
            var total = !this.model.get('isPrivate') ? unitCost * delegates : unitCost;
            this.model.set( "delegates", delegates );
            this.model.set( "total",  total);
            this.model.set( "subtotal",  subtotal);
        }
    });
    // Bookings: Items list view
    tbsBookings.Views.Items = Backbone.View.extend({
        el: $("#tbs-booking-courses-table tbody"),
        initialize: function(){
            // Listen to add event when an item is added
            this.listenTo(this.collection, "add", this.renderCourseDate);
            // Listen to reset event when items are fetched from server
            this.listenTo(this.collection, "reset", this.render);
            // Listen to booking items change
            this.listenTo(tbsBookings.Storage.booking, "change:items", this.updateList);
            // Render inital items
            this.render();
        },
        render: function(){
            // Empth the element first
            this.$el.html("");
            // Iterate over each item and render its veiw
            this.collection.each(function(item){
                this.renderCourseDate(item);
            }, this);
            return this;
        },
        renderCourseDate: function(item){
            // Initiate the view for the item model
            var itemView = new tbsBookings.Views.Item({
                model: item
            });
            // Get html form the view and append it to list view
            this.$el.append( itemView.render().el );
        },
        updateList: function(booking, items){
            this.collection.set(items);
        }
    });
    // Item List Buttons 
    tbsBookings.Views.ItemButtons = Backbone.View.extend({
        el: $("#tbs-booking-course-buttons"),
        events: {
            "click .booking-add-course-date": "openModal",
            "click .booking-recalculate": "recalCulate",
            "click .booking-save": "saveBooking",
        },
        openModal: function(e){
            e.preventDefault();
            // Open modal
            tbsBookings.modalView.open();
        },
        recalCulate: function(){
            if(!tbsBookings.Storage.items.size()){
                return;
            }
            // Recalculate Totals, Vats for the selected items
            // Get the id and delgates number form the items collection
            var bookingData = {}, 
                itemsData = tbsBookings.Storage.items.map(function(item){
                    return {
                        id: item.get("id"),
                        delegates: item.get("delegates"),
                        item_id: item.get("itemID"),
                        total: item.get("total"),
                        subtotal: item.get("subtotal"),
                    };
                });
            bookingData.order_id = tbsBookings.Storage.booking.get("id");
            bookingData.data_entry_complete = tbsBookings.Storage.booking.get("dataEntryComplete");
            
            bookingData.city = tbsBookings.Storage.address.get("city");
            bookingData.postcode = tbsBookings.Storage.address.get("postcode");
            bookingData.country = tbsBookings.Storage.address.get("country");
            bookingData.state = tbsBookings.Storage.address.get("state");
            
            bookingData.items = itemsData;
            // request to server for all updated information per items
            // and totals price and VATs
            tbsBookings.Storage.items.fetch( {reset: true, data: bookingData} );
        },
        saveBooking: function(){
            tbsBookings.Storage.booking.saveData();
        }
        
    });
    // Bookig Totals View
    tbsBookings.Views.BookingTotals = Backbone.View.extend({
        el: $("#tbs-booking-totals"),
        initialize: function(){
            // Cache items collection for this view
            this.collection = tbsBookings.Storage.items;
            // Listen to add event when an item is added
            this.listenTo(tbsBookings.Storage.booking, "change:totals", this.updateTotals);
            
        },
        updateTotals: function(booking, totals){
            this.$el.html(totals);
        }
    });
    
    // Bookings: Modal Course Date view
    tbsBookings.Views.CourseDate = Backbone.View.extend({
        tagName: "tr",
        template: _.template($("#course-date-template").html()),
        events: {
            "click .add-to-booking-course": "addToBooking"
        },
        initialize: function(){
            this.listenTo(this.model, "change", this.render);
            this.listenTo(this.model, "destroy", this.remove);
        },
        render: function(){
            this.$el.html(this.template(this.model.attributes));
            return this;
        },
        addToBooking: function(){
            var delegates = parseInt(this.$(".delegate_number").val());
            if(delegates < 1){
                return;
            }
            //this.model.reducePlaces(delegates);
            // Check if already exists
            // If so increase the delgate number only
            var item = tbsBookings.Storage.items.findWhere({"id": this.model.get("id")});
            if(!item){
                var itemData = this.model.toJSON();
                console.log(itemData);
                item = new tbsBookings.Models.Item( itemData );
            }
            item.addDelegates(delegates);
            tbsBookings.Storage.items.add(item);
        }
    });
    // Bookings: Modal course dates list view
    tbsBookings.Views.CourseDatesList = Backbone.View.extend({
        el: $("#tbs-modal-course-date-list table tbody"),
        initialize: function(){
            this.listenTo(this.collection, "reset", this.render);
            this.render();
        },
        render: function(){
            this.$el.html("");
            this.collection.each(function(item){
                this.renderCourseDate(item);
            }, this);
            return this;
        },
        renderCourseDate: function(item){
            var courseDateView = new tbsBookings.Views.CourseDate({
                model: item
            });
            this.$el.append( courseDateView.render().el );
        }
    });
    // Booking: Modal view
    tbsBookings.Views.Modal = Backbone.View.extend({
        el: $("#tbs-modal"),
        events: {
            "change #tbs-booking-courses-dd": "fetchCourseDates",
            "click #tbs-booking-modal-close": "close"
        },
        initialize: function(){
            var view = this;
            this.$el.dialog({
                autoOpen: false,
                modal: true,
                width: Math.min($(window).width() - 20, 690),
                minWidth: 690,
                minHeight: 240,
                draggable: true,
                resizable: false,
                position: {my: "center top", at:"center top+64"},
                close: function(){
                    view.reset();
                },
                open: function(){

                },
                buttons: {
                    Done: function(){
                         view.close();
                    }
                }
            });
            this.courseDateList = new tbsBookings.Views.CourseDatesList({
                collection: tbsBookings.Storage.courseDates
            });
        },
        open: function(){
            this.$el.dialog("open");
        },
        close: function(){
            this.$el.dialog("close");
        },
        reset: function(){
            this.$("#tbs-booking-courses-dd").val("");
            this.courseDateList.collection.reset();
        },
        fetchCourseDates: function(e){
            var courseID = this.$("#tbs-booking-courses-dd").find("option:selected").val();
            if(!courseID){
                this.courseDateList.collection.reset();
            }
            var requestData = {course_id: courseID};
            this.courseDateList.collection.fetch({reset: true, data: requestData});
        }
    });
    // Loaders
    tbsBookings.Views.Loader = Backbone.View.extend({
        initialize: function(options){
            this.boundTo = options.boundTo;
            if(_.isArray(this.boundTo)){
                _.each(this.boundTo, function(boundObject){
                    this.listenTo(boundObject, "request", this.showLoader);
                    this.listenTo(boundObject, "sync", this.hideLoader);
                    this.listenTo(boundObject, "booking.savefailed", this.hideLoader);
                }, this);
            }else{
                this.listenTo(this.boundTo, "request", this.showLoader);
                this.listenTo(this.boundTo, "sync", this.hideLoader);
                this.listenTo(this.boundTo, "booking.savefailed", this.hideLoader);
            }
        },
        showLoader: function(){
            this.$el.addClass("tbs-active");
        },
        hideLoader: function(){
            this.$el.removeClass("tbs-active");
        },
    });
    // Initiate
    tbsBookings.init = function(){
        var orderID = 0;
        if(TBS_Booking_Settings.bookingID){
            orderID  = parseInt(TBS_Booking_Settings.bookingID);
        }
        this.Storage.booking = new tbsBookings.Models.Booking({id: orderID});
        this.Storage.general = new tbsBookings.Models.General;
        this.Storage.address = new tbsBookings.Models.Address;
        this.Storage.items = new tbsBookings.Collections.Items;
        this.Storage.courseDates = new tbsBookings.Collections.CourseDates;
        
        this.generalView = new tbsBookings.Views.General({
            model: this.Storage.general
        });
        this.addressView = new tbsBookings.Views.Address({
            model: tbsBookings.Storage.address
        });
        this.delegateListContainerView = new tbsBookings.Views.DelegatesListsContainer;
        this.itemLisView = new tbsBookings.Views.Items({
            collection: tbsBookings.Storage.items
        });
        this.totalsView = new tbsBookings.Views.BookingTotals;
        this.modalView = new tbsBookings.Views.Modal;
        this.modalView.reset();
        this.itemButtonsView = new tbsBookings.Views.ItemButtons;
        
        // Init Loaders
        this.addressLoader = new tbsBookings.Views.Loader({
            el: $("#tbs-address-loader"),
            boundTo: [tbsBookings.Storage.booking, tbsBookings.Storage.items]
        });
        this.delegatesLoader = new tbsBookings.Views.Loader({
            el: $("#tbs-delegates-loader"),
            boundTo: [tbsBookings.Storage.booking, tbsBookings.Storage.items]
        });
        this.courseLoader = new tbsBookings.Views.Loader({
            el: $("#tbs-course-loader"),
            boundTo: [tbsBookings.Storage.booking, tbsBookings.Storage.items]
        });
        this.modalLoader = new tbsBookings.Views.Loader({
            el: $("#modal-course-dates-modal"),
            boundTo: tbsBookings.Storage.courseDates
        });
        
        // Add events to update booking model
        this.Storage.items.on("all", function(){
            tbsBookings.Storage.booking.set('items', tbsBookings.Storage.items.map(function(item){
                return {
                    id: item.get("id"),
                    itemID: item.get("itemID"),
                    courseID: item.get("courseID"),
                    delegates: item.get("delegates"),
                    total: item.get("total"),
                    subtotal: item.get("subtotal")
                }
            }));
        });
        
        // Inital loading of booking data
        this.Storage.booking.loadData();
    };
    $(document).ready(function(){
        tbsBookings.init();
    });
})(jQuery);