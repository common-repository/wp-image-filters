<table class="form-table">
	<tbody>
		<tr>
			<th scope="row"><?php _e('PHP Version', WIF_SLUG); ?></th>
			<td>
				<p>
					<?php

						if(wif_checkVersion(phpversion(), WIF_PHP_VERSION_REQUIRED)) {
							$php_comp_message = __('Your server PHP version is OK.');
						} else {
							$php_comp_message = '<span style="color:red">'.sprintf(__('Your server PHP version is not enough. (Required: %s) Please update and use more updated version or contact your hostmaster.'), WIF_PHP_VERSION_REQUIRED).'</span>';
						}

						echo $php_comp_message;

					?>
				</p>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php _e('GD Version', WIF_SLUG); ?></th>
			<td>
				<p>
					<?php

						if(extension_loaded('gd')) {
							$gd_comp_message = __('GD is installed. OK.');
						} else {
							$gd_comp_message = '<span style="color:red">'.__('To use this plugin, you need to install PHP GD Library.').'</span>';
						}

						echo $gd_comp_message;

					?>
				</p>
			</td>
		</tr>
	</tbody>
</table>