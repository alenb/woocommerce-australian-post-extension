<h3><?php _e('Australian Post Shipping Method Settings','australian-post'); ?></h3>
		<div class="update-nag">For support, contact the developer: Waseem Senjer, waseem.senjer@gmail.com</div>
		<table class="form-table">
		<?php if(get_option('auspost_key')!=''): ?>
					<?php $this->generate_settings_html(); ?>
		<?php else: ?>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="auspost_key">License Key</label>
								</th>
				<td class="forminp">
					<fieldset>
						<legend class="screen-reader-text"><span>Shop Origin Post Code</span></legend>
						<input class="input-text regular-input " type="text" name="auspost_key" id="auspost_key" style=""  placeholder="">
						<p class="description">Enter your Shop postcode.</p>
					</fieldset>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="customer_email">Customer Email</label>
								</th>
				<td class="forminp">
					<fieldset>
						<legend class="screen-reader-text"><span>Shop Origin Post Code</span></legend>
						<input class="input-text regular-input " type="text" name="customer_email" id="customer_email" style=""  placeholder="">
						<p class="description">Enter your Shop postcode.</p>
					</fieldset>
				</td>
			</tr>

		<?php endif; ?>
				
			
		</table> 