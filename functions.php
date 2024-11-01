<?php

	function wif_update_options($post) {
		global $wif_default_options;
		foreach($wif_default_options as $default_option) {
			$option = $post[$default_option];
			$option = ($default_option == 'wif_watermark_apply_sizes') ? implode(',', $option) : $option;
			$option = sanitize_text_field($option);
			update_option($default_option, $option);
		}
	}

	function wif_get_options() {
		global $wif_default_options;
		foreach($wif_default_options as $default_option) {
			$return[$default_option] = get_option($default_option);
			$return[$default_option] = ($default_option == 'wif_watermark_apply_sizes') ? explode(',', $return[$default_option]) : $return[$default_option];
		}
		return $return;
	}

	add_action('admin_menu','wif_create_menu');
	function wif_create_menu() {
		add_media_page('WP Image Filters', 'WP Image Filters', 'manage_options', WIF_SLUG, 'wif_options_render');
	}

	function wif_options_render() {
		include WIF_PLUGIN_DIR.'/admin.php';
	}

	function wif_checkVersion($current_v, $required_v) {
		return version_compare($current_v, $required_v, '>=');
	}

	add_action('wp_ajax_wif_get_image', 'wif_get_preview_image');
	function wif_get_preview_image() {
		if(isset($_GET['id'])) {
			$image = wp_get_attachment_image(filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT), 'thumbnail', false, ['id' => 'wif-watermark-image-preview']);
			$data  = ['image' => $image];
			wp_send_json_success($data);
		} else {
			wp_send_json_error();
		}
	}

	function wif_generate_image($file, $mime_type) {
		switch($mime_type) {
			case 'image/gif':
				$gif = imagecreatefromgif($file);
				if($gif) {
					$width            = imagesx($gif);
					$height           = imagesy($gif);
					$im               = imagecreatetruecolor($width, $height);
					$transparentColor = imagecolorallocatealpha($im, 0, 0, 0, 127);
					imagecolortransparent($im, $transparentColor);
					imagefill($im, 0, 0, $transparentColor);
					imagecopy($im, $gif, 0, 0, 0, 0, $width, $height);
					imagedestroy($gif);
				}
				break;
			case 'image/jpeg':
				$im = imagecreatefromjpeg($file);
				break;
			case 'image/png':
				$im = imagecreatefrompng($file);
				break;
			case 'image/webp':
				if(!function_exists('imagewebp')) {
					$im = imagecreatefromwebp($file);
				}
				break;
			case 'image/bmp':
			case 'image/x-ms-bmp':
			case 'image/x-windows-bmp':
				$im = imagecreatefrombmp($file);
				break;
		}

		return $im;
	}

	function wif_create_file_image($im, $mime_type) {
		ob_start();

		switch($mime_type) {
			case 'image/gif':
				imagesavealpha($im, true);
				imagegif($im, null);
				break;
			case 'image/jpeg':
				imageinterlace($im, true);
				imagejpeg($im, null, WIF_IMAGE_QUALITY);
				break;
			case 'image/png':
				imagesavealpha($im, true);
				imagepng($im, null, round(9 * WIF_IMAGE_QUALITY / 100));
				break;
			case 'image/webp':
				if(!function_exists('imagewebp')) {
					imagesavealpha($im, true);
					imagewebp($im, null, WIF_IMAGE_QUALITY);
				}
				break;
			case 'image/bmp':
			case 'image/x-ms-bmp':
			case 'image/x-windows-bmp':
				imageinterlace($im, true);
				imagebmp($im, null, WIF_IMAGE_QUALITY);
				break;
		}

		$data = ob_get_contents();
		ob_end_clean();

		return $data;
	}

	function wif_watermark_image($filename, $upload_dir, $watermark_image_id) {
		$wif_watermark_image_file = get_attached_file($watermark_image_id);
		$file_path                = $upload_dir['path'].'/'.$filename;
		$info                     = getimagesize($file_path);
		$mime_type                = $info['mime'];
		$info_watermark           = getimagesize($wif_watermark_image_file);
		$mime_type_watermark      = $info_watermark['mime'];

		$im        = wif_generate_image($file_path, $mime_type);
		$watermark = wif_generate_image($wif_watermark_image_file, $mime_type_watermark);

		$marge_right = 10;
		$marge_bottom = 10;
		$sx = imagesx($watermark);
		$sy = imagesy($watermark);

		imagecopy($im, $watermark, imagesx($im) - $sx - $marge_right, imagesy($im) - $sy - $marge_bottom, 0, 0, imagesx($watermark), imagesy($watermark));

		$data = wif_create_file_image($im, $mime_type);

		file_put_contents($file_path, $data);

		imagedestroy($im);

		return $filename;
	}

	function wif_flip_image($filename, $upload_dir) {
		$file_path = $upload_dir['path'].'/'.$filename;
		$info      = getimagesize($file_path);
		$mime_type = $info['mime'];

		$im = wif_generate_image($file_path, $mime_type);

		imageflip($im, IMG_FLIP_HORIZONTAL);

		$data = wif_create_file_image($im, $mime_type);

		file_put_contents($file_path, $data);

		imagedestroy($im);

		return $filename;
	}

	add_filter('wp_generate_attachment_metadata', 'wif_process_image', 1, 2);
	function wif_process_image($meta, $attachment_id) {
		/* echo '<pre>';
		var_dump($meta);
		echo '</pre>'; */
		$wif_options = wif_get_options();
		$upload_dir  = wp_upload_dir();

		if($wif_options['wif_status'] == 1) {
			foreach($meta['sizes'] as $key => $size) {

				$filename = $size['file'];

				if($wif_options['wif_horizontal_flip_image'] == 1) {
					wif_flip_image($filename, $upload_dir);
				}

				if($wif_options['wif_watermark_apply_sizes'] && $wif_options['wif_watermark'] == 1 && $wif_options['wif_watermark_image']) {
					if(in_array($key, $wif_options['wif_watermark_apply_sizes'])) {
						wif_watermark_image($filename, $upload_dir, $wif_options['wif_watermark_image']);
					}
				}

			}

			$meta['image_meta']['copyright'] = ''; // remove copyright field
			$meta['image_meta']['credit']    = ''; // remove credit field
		}

		return $meta;
	}

?>