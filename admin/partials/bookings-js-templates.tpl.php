
<div style="display: none">
<div id="tbs-modal" class="tbs-booking-modal" title="Add Course Dates">
	<div class="tbs-modal-inner">
		<button id="tbs-booking-modal-close" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only"><span class="ui-button-text">Done</span></button>
		<h3>Course</h3>
		<div id="tbs-modal-courses">
			<?php echo tbs_grouped_courses_dropdown(array('select_id' => 'tbs-booking-courses-dd', 'first_option' => __('Select a Course&hellip;', TBS_i18n::get_domain_name()))); ?>
		</div>
		<h3>Course Dates</h3>
		<div id="tbs-modal-course-date-list">
			<table>
				<thead>
					<tr>
						<th>Date</th>
						<th>location</th>
						<th>Price</th>
						<th>Delegates</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					
				</tbody>
			</table>
			<div id="modal-course-dates-modal" class="modal-loader">
				<div class="tbs-loader"></div>
			</div>
		</div>
	</div>
</div>
</div>

<script id="course-date-template" type="text/template">
	<td class="tbs-booking-item"><%= isPrivate ? 'Private: ' + title : title %></td>
	<td class="tbs-booking-item"><%= location %></td>
	<td class="tbs-booking-item-cost"><%= price %></td>
	<td class="tbs-booking-item-delegates">
		<input class="delegate_number" type="number" step="1" min="1" max="<%= places %>" value="1"/>
	</td>
	<td>
		<% if(places > 0){ %>
		<button class="button add-to-booking-course">Add</button>
		<% }else { %>
		<span>Sold Out</span>
		<% } %>
	</td>
</script>

<script id="booking-item-template" type="text/template">
	<td class="tbs-booking-item">
		<a href="<%= url %>" target="_blak"><%= isPrivate ? 'Private: ' + title : title %></a>
		<input type="hidden" name="tbs_booking_items[]" value="<%= id %>"/>
	</td>
	<td class="tbs-booking-item-cost">
		<span class="tbs-item-price"><span class="woocommerce-Price-currencySymbol"><?php echo get_woocommerce_currency_symbol(); ?></span><%= unitPrice %></span>
		<input class="item-price" type="text" value="<%= unitPrice %>"/>
	</td>
	<td class="tbs-booking-item-delegates">
		<span><%= delegates %></span>
		<input type="hidden" name="tbs_booking_items_delegate[<%= id %>]" value="<%= delegates %>"/>
		<input class="delegate_number" type="number" step="1" min="1" max="<%= delegateStock %>" value="<%= delegates %>"/>
	</td>
	<td class="tbs-booking-item-total"><span class="woocommerce-Price-currencySymbol"><?php echo get_woocommerce_currency_symbol(); ?></span><%= total.toFixed(2) %></td>
	<td class="tbs-booking-item-actions">
		<button class="button edit-item">edit</button>
		<button class="button button-primary save-item">Save</button>
		<button class="button button-link-delete remove-item">Remove</button>
		<button class="button cancel-item">Cancel</button>
	</td>
</script>

<script id="delegate-fields-template" type="text/template">
	<div class="booking-delegate-inner">
		<h4>Delegate <%= id %></h4>
		<p class="form-field">
			<label for="delegate<%= id %>_first_name">First name</label>
			<input type="text" class="delegate-field tbs-rquired-field" data-modelkey="first_name" id="delegate<%= id %>_first_name" value="<%= first_name %>"/>
		</p>
		<p class="form-field">
			<label for="delegate<%= id %>_last_name">Last name</label>
			<input type="text" class="delegate-field tbs-rquired-field" data-modelkey="last_name" id="delegate<%= id %>_last_name" value="<%= last_name %>"/>
		</p>
		<p class="form-field">
			<label for="delegate<%= id %>_email">Email address</label>
			<input type="email" class="delegate-field delegate-field-email" data-modelkey="email" id="delegate<%= id %>_email" value="<%= email %>" data-coursedateid="<%= courseDateID %>"/>
			<span class="description">If not known please leave blank, details will be sent to the Booker for forwarding</span>
		</p>
		<p class="form-field">
			<label for="delegate<%= id %>_notes">Notes</label>
			<input type="text" class="delegate-field" data-modelkey="notes" id="delegate<%= id %>_notes" value="<%= notes %>"/>
		</p>
	</div>
</script>
<script id="delegate-list-template" type="text/template">
	<div class="booking-item-delegates">
		<h3><%= itemTitle %></h3>
		<div class="booking-delegate-list clearfix">
		</div>
	</div>
</script>