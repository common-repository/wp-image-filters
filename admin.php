<?php

	wp_enqueue_media();
	wp_enqueue_script('wif_script', plugins_url('/js/script.js' , __FILE__), ['jquery'], time());

	$tabs = [
		'general-options' => __('General Options', WIF_SLUG),
		'compatibility'   => __('Compatibility', WIF_SLUG)
	];

	$current_tab = sanitize_text_field($_GET['tab']);

	$active_tab = ($current_tab) ? $current_tab : 'general-options';

?>

<div class="wrap">
	<h1>
		WP Image Filters <small style="margin-left: .5rem; font-size: 80%; font-family: monospace; letter-spacing: -2px; color: gray;"><?php echo WIF_VERSION; ?></small>
	</h1>

	<div class="nav-tab-wrapper">
		<?php foreach($tabs as $label => $tab) { ?>
			<a class="nav-tab <?php echo $label == $active_tab ? 'nav-tab-active' : ''; ?>"
			    href="<?php echo admin_url('admin.php?page='.WIF_SLUG.'&tab='.$label); ?>">
			    <?php echo $tab; ?>
			</a>
		<?php } ?>
	</div>

	<form action="<?php echo admin_url('admin.php?page='.WIF_SLUG.'&tab='.$active_tab); ?>" method="POST" style="margin: 2rem;">
		<?php include WIF_PLUGIN_DIR.'/'.$active_tab.'.php'; ?>
	</form>
	<?php ?>
</div>