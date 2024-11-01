<?php

function simple_forum_widgets_create_menu() {

	add_menu_page('Simple Forum Admin Settings', 'Simple Forum', 'administrator', __FILE__, 'simple_forum_widgets_settings_page' , plugins_url('/assets/images/icon.png', __FILE__) );

	add_action( 'admin_init', 'register_simple_forum_widgets_settings' );
}
add_action('admin_menu', 'simple_forum_widgets_create_menu');

function simple_forum_widgets_check_request() {
   if (isset($_GET['SFRequest'])) {
      require dirname(__FILE__).'/simple-forum-widgets-sso.php';
   }
}
add_action('wp_loaded', 'simple_forum_widgets_check_request');

function register_simple_forum_widgets_settings() {

	 wp_enqueue_script(
		'simple_forum_widgets',
		plugins_url('/assets/js/simple-forum-widgets.js', __FILE__),
		array('jquery'),
		SIMPLE_FORUM_WIDGETS_VERSION
	 );

	register_setting( 'simple-forum-widgets-settings-group', 'sf_sso_enable' );
	register_setting( 'simple-forum-widgets-settings-group', 'sf_base_url', 'host_validation_function' );
	register_setting( 'simple-forum-widgets-settings-group', 'sf_client_id' );
	register_setting( 'simple-forum-widgets-settings-group', 'sf_secret' );
}

function host_validation_function( $val ) {
	$host = parse_url(home_url());
    $dest = parse_url($val);
    if ($host['host'] == $dest['host']) {
		return $val;
	} else {
		return false;
	}
}

function simple_forum_widgets_settings_page() {
	if (!current_user_can('manage_options'))
	 wp_die(__('You do not have sufficient permissions to access this page.'));

  	$options = get_option('sf-options');
?>
<script type="text/javascript">
jQuery(document).ready(function($) {
	$('.generate-secret').click(function() {
		var self = $(this);
		var len = self.attr('data-length');
		$.ajax({
			url: '<?= site_url('?SFRequest=generate-secret')?>&length='+len,
			success: function(data) {
				self.prev('input').val(data);
			}
		});
		return false;
	});
});
</script>
<div class="wrap">
<h1>Settings - Simple Forum Widgets (Beta)</h1>

<form method="post" action="options.php">
    <?php settings_fields( 'simple-forum-widgets-settings-group' ); ?>
    <?php do_settings_sections( 'simple-forum-widgets-settings-group' ); ?>
    <table class="form-table">
        <tr valign="top">
        <th scope="row">
			<?php echo __('Simple Forum Base URL', 'simple-forum-widgets'); ?>
		</th>
        <td>
			<input type="text" name="sf_base_url" value="<?php echo esc_attr( get_option('sf_base_url') ); ?>" aria-describedby="base-url-description" class="regular-text" />
			<p id="base-url-description" class="description"><?php echo __('Please enter Simple Forum base_url here (same domain/host accepted).', 'simple-forum-widgets'); ?></p>
		</td>
        </tr>

        <tr valign="top">
        <th scope="row">
			<?php echo __('Client ID', 'simple-forum-widgets'); ?>
		</th>
        <td>
			<input type="text" name="sf_client_id" value="<?php echo esc_attr( get_option('sf_client_id') ); ?>" aria-describedby="client-id-description" class="regular-text" />
			<a class="generate-secret" href="#" data-length="14">
				<?php echo __('Generate', 'simple-forum-widgets'); ?>
			</a>
			<p id="client-id-description" class="description"><?php echo __('Client id is a url-friendly value that identifies your WordPress site to Simple Forum.', 'simple-forum-widgets'); ?></p>
		</td>
        </tr>

        <tr valign="top">
        <th scope="row">
			<?php echo __('Secret', 'simple-forum-widgets'); ?>
		</th>
        <td>
			<input type="text" name="sf_secret" value="<?php echo esc_attr( get_option('sf_secret') ); ?>" aria-describedby="secret-description" class="sf-secret regular-text" />
			<a class="generate-secret" href="#" data-length="40">
				<?php echo __('Generate', 'simple-forum-widgets'); ?>
			</a>
			<p id="secret-description" class="description"><?php echo __('Secret that Simple Forum uses to ensure that your WordPress site is a trusted source.', 'simple-forum-widgets'); ?></p>
		</td>
        </tr>
		<tr valign="top">
        <th scope="row"></th>
        <td>
			<?php submit_button(); ?>
		</td>
        </tr>
	</table>

</form>

</div>
<?php }
