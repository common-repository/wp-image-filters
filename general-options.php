<?php

	if($_POST) {
		wif_update_options($_POST);
		// <div class="updated notice">
		//     <p>Something has been updated, awesome</p>
		// </div>
	}

	$wif_options = wif_get_options();

?>

<table class="form-table">
	<tbody>
		<tr>
			<th scope="row"><?php _e('Status', WIF_SLUG); ?></th>
			<td>
				<label>
					<input type="checkbox" name="wif_status" value="1" id="wif_status" <?php echo ($wif_options['wif_status'] == 1) ? 'checked' : ''; ?>> <?php _e('Active', WIF_SLUG); ?>
				</label>
				<p class="description">
					<?php _e('Globally activate/deactivate plugin.', WIF_SLUG); ?>
				</p>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php _e('Horizontal Flip Image', WIF_SLUG); ?></th>
			<td>
				<label>
					<input type="checkbox" name="wif_horizontal_flip_image" value="1" id="wif_horizontal_flip_image" <?php echo ($wif_options['wif_horizontal_flip_image'] == 1) ? 'checked' : ''; ?>> <?php _e('Active', WIF_SLUG); ?>
				</label>
				<p class="description">
					<?php _e('Flip images horizontally.', WIF_SLUG); ?>
				</p>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php _e('Watermark', WIF_SLUG); ?></th>
			<td>
				<label>
					<input type="checkbox" name="wif_watermark" value="1" id="wif_watermark" <?php echo ($wif_options['wif_watermark'] == 1) ? 'checked' : ''; ?>> <?php _e('Active', WIF_SLUG); ?>
				</label>
				<p class="description">
					<?php _e('Apply watermark. Watermark location is "bottom right" by default.', WIF_SLUG); ?>
				</p>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php _e('Watermark Apply Sizes', WIF_SLUG); ?></th>
			<td>
				<?php foreach(get_intermediate_image_sizes() as $key => $size) { ?>
					<label style="display: block;">
						<input type="checkbox" name="wif_watermark_apply_sizes[]" value="<?php echo $size; ?>" id="wif_watermark_apply_sizes_<?php echo $key; ?>" <?php echo (in_array($size, $wif_options['wif_watermark_apply_sizes'])) ? 'checked' : ''; ?>> <?php echo $size; ?>
					</label>
				<?php } ?>
				<p class="description">
					<?php _e('Apply watermark only to specific image sizes.', WIF_SLUG); ?>
				</p>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php _e('Watermark Image', WIF_SLUG); ?></th>
			<td>
				<?php

					if(intval($wif_options['wif_watermark_image']) > 0) {
						$image = wp_get_attachment_image($wif_options['wif_watermark_image'], 'thumbnail', false, array('id' => 'wif-watermark-image-preview'));
						echo $image.'<br>';
					}

				?>
				<input type="hidden" name="wif_watermark_image" id="wif_watermark_image" value="<?php echo esc_attr($wif_options['wif_watermark_image']); ?>">
				<input type='button' class="button" value="<?php _e('Select a image', WIF_SLUG); ?>" id="wif_media_manager">
				<p class="description">
					<?php _e('Select your watermark image.', WIF_SLUG); ?>
				</p>
			</td>
		</tr>
	</tbody>
</table>

<hr>

<p class="submit">
	<input type="submit" id="submit" class="button button-primary" value="<?php _e('Save Changes', WIF_SLUG); ?>">
</p>