<?php
/**
 * Includes functions for all admin page templates and
 * functions that add menu pages in the dashboard. Also
 * has code for saving settings with defaults.
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

function sb_instagram_menu() {
	$cap = current_user_can( 'manage_instagram_feed_options' ) ? 'manage_instagram_feed_options' : 'manage_options';

	$cap = apply_filters( 'sbi_settings_pages_capability', $cap );

	global $sb_instagram_posts_manager;
	$notice = '';
	if ( $sb_instagram_posts_manager->are_critical_errors() ) {
		$notice = ' <span class="update-plugins sbi-error-alert"><span>!</span></span>';
	}

	add_menu_page(
		__( 'Instagram Feed', 'instagram-feed' ),
		__( 'Instagram Feed', 'instagram-feed' ) . $notice,
		$cap,
		'sb-instagram-feed',
		'sb_instagram_settings_page'
	);
	add_submenu_page(
		'sb-instagram-feed',
		__( 'Settings', 'instagram-feed' ),
		__( 'Settings', 'instagram-feed' ) . $notice,
		$cap,
		'sb-instagram-feed',
		'sb_instagram_settings_page'
	);
	add_submenu_page(
		'sb-instagram-feed',
		__( 'About Us', 'instagram-feed' ),
		__( 'About Us', 'instagram-feed' ),
		$cap,
		'sb-instagram-feed-about',
		'sb_instagram_about_page'
	);
}
add_action('admin_menu', 'sb_instagram_menu');

function sb_instagram_about_page() {
    do_action('sbi_admin_page' );
}

function sb_instagram_settings_page() {

	//Hidden fields
	$sb_instagram_settings_hidden_field = 'sb_instagram_settings_hidden_field';
	$sb_instagram_configure_hidden_field = 'sb_instagram_configure_hidden_field';
	$sb_instagram_customize_hidden_field = 'sb_instagram_customize_hidden_field';

	//Declare defaults
	$sb_instagram_settings_defaults = array(
		'sb_instagram_at'                   => '',
		'sb_instagram_user_id'              => '',
		'sb_instagram_preserve_settings'    => '',
		'sb_instagram_cache_time'           => 1,
		'sb_instagram_cache_time_unit'      => 'hours',
		'sbi_caching_type'                  => 'page',
		'sbi_cache_cron_interval'           => '12hours',
		'sbi_cache_cron_time'               => '1',
		'sbi_cache_cron_am_pm'              => 'am',
		'sb_instagram_width'                => '100',
		'sb_instagram_width_unit'           => '%',
		'sb_instagram_feed_width_resp'      => false,
		'sb_instagram_height'               => '',
		'sb_instagram_num'                  => '20',
		'sb_instagram_height_unit'          => '',
		'sb_instagram_cols'                 => '4',
		'sb_instagram_disable_mobile'       => false,
		'sb_instagram_image_padding'        => '5',
		'sb_instagram_image_padding_unit'   => 'px',
		'sb_instagram_sort'                 => 'none',
		'sb_instagram_background'           => '',
		'sb_instagram_show_btn'             => true,
		'sb_instagram_btn_background'       => '',
		'sb_instagram_btn_text_color'       => '',
		'sb_instagram_btn_text'             => __( 'Load More...', 'instagram-feed' ),
		'sb_instagram_image_res'            => 'auto',
		//Header
		'sb_instagram_show_header'          => true,
		'sb_instagram_header_size'  => 'small',
		'sb_instagram_header_color'         => '',
		'sb_instagram_custom_bio' => '',
		'sb_instagram_custom_avatar' => '',
		//Follow button
		'sb_instagram_show_follow_btn'      => true,
		'sb_instagram_folow_btn_background' => '',
		'sb_instagram_follow_btn_text_color' => '',
		'sb_instagram_follow_btn_text'      => __( 'Follow on Instagram', 'instagram-feed' ),
		//Misc
		'sb_instagram_custom_css'           => '',
		'sb_instagram_custom_js'            => '',
		'sb_instagram_cron'                 => 'no',
		'sb_instagram_backup' => true,
		'sb_ajax_initial' => false,
		'enqueue_css_in_shortcode' => false,
		'sb_instagram_ajax_theme'           => false,
		'sb_instagram_disable_resize'       => false,
		'sb_instagram_favor_local'          => false,
		'sb_instagram_minnum' => 0,
		'disable_js_image_loading'          => false,
		'enqueue_js_in_head'                => false,
		'sb_instagram_disable_mob_swipe' => false,
		'sbi_font_method' => 'svg',
		'sb_instagram_disable_awesome'      => false,
        'custom_template' => false,
        'disable_admin_notice' => false,
		'enable_email_report' => 'on',
		'email_notification' => 'monday',
		'email_notification_addresses' => get_option( 'admin_email' ),
	);
	//Save defaults in an array
	$options = wp_parse_args(get_option('sb_instagram_settings'), $sb_instagram_settings_defaults);
	update_option( 'sb_instagram_settings', $options );

	//Set the page variables
	$sb_instagram_at = $options[ 'sb_instagram_at' ];
	$sb_instagram_user_id = $options[ 'sb_instagram_user_id' ];
	$sb_instagram_preserve_settings = $options[ 'sb_instagram_preserve_settings' ];
	$sb_instagram_ajax_theme = $options[ 'sb_instagram_ajax_theme' ];
	$enqueue_js_in_head = $options[ 'enqueue_js_in_head' ];
	$disable_js_image_loading = $options[ 'disable_js_image_loading' ];
	$sb_instagram_disable_resize = $options[ 'sb_instagram_disable_resize' ];
	$sb_instagram_favor_local = $options[ 'sb_instagram_favor_local' ];
	$sb_instagram_minnum = $options[ 'sb_instagram_minnum' ];

	$sb_instagram_cache_time = $options[ 'sb_instagram_cache_time' ];
	$sb_instagram_cache_time_unit = $options[ 'sb_instagram_cache_time_unit' ];

	$sbi_caching_type = $options[ 'sbi_caching_type' ];
	$sbi_cache_cron_interval = $options[ 'sbi_cache_cron_interval' ];
	$sbi_cache_cron_time = $options[ 'sbi_cache_cron_time' ];
	$sbi_cache_cron_am_pm = $options[ 'sbi_cache_cron_am_pm' ];

	$sb_instagram_width = $options[ 'sb_instagram_width' ];
	$sb_instagram_width_unit = $options[ 'sb_instagram_width_unit' ];
	$sb_instagram_feed_width_resp = $options[ 'sb_instagram_feed_width_resp' ];
	$sb_instagram_height = $options[ 'sb_instagram_height' ];
	$sb_instagram_height_unit = $options[ 'sb_instagram_height_unit' ];
	$sb_instagram_num = $options[ 'sb_instagram_num' ];
	$sb_instagram_cols = $options[ 'sb_instagram_cols' ];
	$sb_instagram_disable_mobile = $options[ 'sb_instagram_disable_mobile' ];
	$sb_instagram_image_padding = $options[ 'sb_instagram_image_padding' ];
	$sb_instagram_image_padding_unit = $options[ 'sb_instagram_image_padding_unit' ];
	$sb_instagram_sort = $options[ 'sb_instagram_sort' ];
	$sb_instagram_background = $options[ 'sb_instagram_background' ];
	$sb_instagram_show_btn = $options[ 'sb_instagram_show_btn' ];
	$sb_instagram_btn_background = $options[ 'sb_instagram_btn_background' ];
	$sb_instagram_btn_text_color = $options[ 'sb_instagram_btn_text_color' ];
	$sb_instagram_btn_text = $options[ 'sb_instagram_btn_text' ];
	$sb_instagram_image_res = $options[ 'sb_instagram_image_res' ];
	//Header
	$sb_instagram_show_header = $options[ 'sb_instagram_show_header' ];
	$sb_instagram_header_size = $options[ 'sb_instagram_header_size' ];
	$sb_instagram_show_bio = isset( $options[ 'sb_instagram_show_bio' ] ) ? $options[ 'sb_instagram_show_bio' ] : true;
	$sb_instagram_custom_bio = $options[ 'sb_instagram_custom_bio' ];
	$sb_instagram_custom_avatar = $options[ 'sb_instagram_custom_avatar' ];
	$sb_instagram_header_color = $options[ 'sb_instagram_header_color' ];
	//Follow button
	$sb_instagram_show_follow_btn = $options[ 'sb_instagram_show_follow_btn' ];
	$sb_instagram_folow_btn_background = $options[ 'sb_instagram_folow_btn_background' ];
	$sb_instagram_follow_btn_text_color = $options[ 'sb_instagram_follow_btn_text_color' ];
	$sb_instagram_follow_btn_text = $options[ 'sb_instagram_follow_btn_text' ];
	//Misc
	$sb_instagram_custom_css = $options[ 'sb_instagram_custom_css' ];
	$sb_instagram_custom_js = $options[ 'sb_instagram_custom_js' ];
	$sb_instagram_cron = $options[ 'sb_instagram_cron' ];
	$sb_instagram_backup = $options[ 'sb_instagram_backup' ];
	$sb_ajax_initial = $options[ 'sb_ajax_initial' ];
	$enqueue_css_in_shortcode = $options[ 'enqueue_css_in_shortcode' ];
	$sbi_font_method = $options[ 'sbi_font_method' ];
	$sb_instagram_disable_awesome = $options[ 'sb_instagram_disable_awesome' ];
	$sb_instagram_custom_template = $options[ 'custom_template' ];
	$sb_instagram_disable_admin_notice = $options[ 'disable_admin_notice' ];
	$sb_instagram_enable_email_report = $options[ 'enable_email_report' ];
	$sb_instagram_email_notification = $options[ 'email_notification' ];
	$sb_instagram_email_notification_addresses = $options[ 'email_notification_addresses' ];
	//Check nonce before saving data
	if ( ! isset( $_POST['sb_instagram_settings_nonce'] ) || ! wp_verify_nonce( $_POST['sb_instagram_settings_nonce'], 'sb_instagram_saving_settings' ) ) {
		//Nonce did not verify
	} else {
		// See if the user has posted us some information. If they did, this hidden field will be set to 'Y'.
		if( isset($_POST[ $sb_instagram_settings_hidden_field ]) && $_POST[ $sb_instagram_settings_hidden_field ] == 'Y' ) {

			if( isset($_POST[ $sb_instagram_configure_hidden_field ]) && $_POST[ $sb_instagram_configure_hidden_field ] == 'Y' ) {

				$sb_instagram_at = sanitize_text_field( $_POST[ 'sb_instagram_at' ] );
				$sb_instagram_user_id = array();
				if ( isset( $_POST[ 'sb_instagram_user_id' ] )) {
					if ( is_array( $_POST[ 'sb_instagram_user_id' ] ) ) {
						foreach( $_POST[ 'sb_instagram_user_id' ] as $user_id ) {
							$sb_instagram_user_id[] = sanitize_text_field( $user_id );
						}
					} else {
						$sb_instagram_user_id[] = sanitize_text_field( $_POST[ 'sb_instagram_user_id' ] );
					}
				}
				isset($_POST[ 'sb_instagram_preserve_settings' ]) ? $sb_instagram_preserve_settings = sanitize_text_field( $_POST[ 'sb_instagram_preserve_settings' ] ) : $sb_instagram_preserve_settings = '';
				isset($_POST[ 'sb_instagram_cache_time' ]) ? $sb_instagram_cache_time = sanitize_text_field( $_POST[ 'sb_instagram_cache_time' ] ) : $sb_instagram_cache_time = '';
				isset($_POST[ 'sb_instagram_cache_time_unit' ]) ? $sb_instagram_cache_time_unit = sanitize_text_field( $_POST[ 'sb_instagram_cache_time_unit' ] ) : $sb_instagram_cache_time_unit = '';

				isset($_POST[ 'sbi_caching_type' ]) ? $sbi_caching_type = sanitize_text_field( $_POST[ 'sbi_caching_type' ] ) : $sbi_caching_type = '';
				isset($_POST[ 'sbi_cache_cron_interval' ]) ? $sbi_cache_cron_interval = sanitize_text_field( $_POST[ 'sbi_cache_cron_interval' ] ) : $sbi_cache_cron_interval = '';
				isset($_POST[ 'sbi_cache_cron_time' ]) ? $sbi_cache_cron_time = sanitize_text_field( $_POST[ 'sbi_cache_cron_time' ] ) : $sbi_cache_cron_time = '';
				isset($_POST[ 'sbi_cache_cron_am_pm' ]) ? $sbi_cache_cron_am_pm = sanitize_text_field( $_POST[ 'sbi_cache_cron_am_pm' ] ) : $sbi_cache_cron_am_pm = '';

				$options[ 'sb_instagram_at' ] = $sb_instagram_at;
				$options[ 'sb_instagram_user_id' ] = $sb_instagram_user_id;
				$options[ 'sb_instagram_preserve_settings' ] = $sb_instagram_preserve_settings;

				$options[ 'sb_instagram_cache_time' ] = $sb_instagram_cache_time;
				$options[ 'sb_instagram_cache_time_unit' ] = $sb_instagram_cache_time_unit;

				$options[ 'sbi_caching_type' ] = $sbi_caching_type;
				$options[ 'sbi_cache_cron_interval' ] = $sbi_cache_cron_interval;
				$options[ 'sbi_cache_cron_time' ] = $sbi_cache_cron_time;
				$options[ 'sbi_cache_cron_am_pm' ] = $sbi_cache_cron_am_pm;


				//Delete all SBI transients
				global $wpdb;
				$table_name = $wpdb->prefix . "options";
				$wpdb->query( "
                    DELETE
                    FROM $table_name
                    WHERE `option_name` LIKE ('%\_transient\_sbi\_%')
                    " );
				$wpdb->query( "
                    DELETE
                    FROM $table_name
                    WHERE `option_name` LIKE ('%\_transient\_timeout\_sbi\_%')
                    " );
				$wpdb->query( "
			        DELETE
			        FROM $table_name
			        WHERE `option_name` LIKE ('%\_transient\_&sbi\_%')
			        " );
				$wpdb->query( "
			        DELETE
			        FROM $table_name
			        WHERE `option_name` LIKE ('%\_transient\_timeout\_&sbi\_%')
			        " );

				if ( $sbi_caching_type === 'background' ) {
					delete_option( 'sbi_cron_report' );
					SB_Instagram_Cron_Updater::start_cron_job( $sbi_cache_cron_interval, $sbi_cache_cron_time, $sbi_cache_cron_am_pm );
				}

			} //End config tab post

			if( isset($_POST[ $sb_instagram_customize_hidden_field ]) && $_POST[ $sb_instagram_customize_hidden_field ] == 'Y' ) {

				//Validate and sanitize width field
				$safe_width = intval( sanitize_text_field( $_POST['sb_instagram_width'] ) );
				if ( ! $safe_width ) $safe_width = '';
				if ( strlen( $safe_width ) > 4 ) $safe_width = substr( $safe_width, 0, 4 );
				$sb_instagram_width = $safe_width;

				$sb_instagram_width_unit = sanitize_text_field( $_POST[ 'sb_instagram_width_unit' ] );
				isset($_POST[ 'sb_instagram_feed_width_resp' ]) ? $sb_instagram_feed_width_resp = sanitize_text_field( $_POST[ 'sb_instagram_feed_width_resp' ] ) : $sb_instagram_feed_width_resp = '';

				//Validate and sanitize height field
				$safe_height = intval( sanitize_text_field( $_POST['sb_instagram_height'] ) );
				if ( ! $safe_height ) $safe_height = '';
				if ( strlen( $safe_height ) > 4 ) $safe_height = substr( $safe_height, 0, 4 );
				$sb_instagram_height = $safe_height;

				$sb_instagram_height_unit = sanitize_text_field( $_POST[ 'sb_instagram_height_unit' ] );

				//Validate and sanitize number of photos field
				$safe_num = intval( sanitize_text_field( $_POST['sb_instagram_num'] ) );
				if ( ! $safe_num ) $safe_num = '';
				if ( strlen( $safe_num ) > 4 ) $safe_num = substr( $safe_num, 0, 4 );
				$sb_instagram_num = $safe_num;

				$sb_instagram_cols = sanitize_text_field( $_POST[ 'sb_instagram_cols' ] );
				isset($_POST[ 'sb_instagram_disable_mobile' ]) ? $sb_instagram_disable_mobile = sanitize_text_field( $_POST[ 'sb_instagram_disable_mobile' ] ) : $sb_instagram_disable_mobile = '';

				//Validate and sanitize padding field
				$safe_padding = intval( sanitize_text_field( $_POST['sb_instagram_image_padding'] ) );
				if ( ! $safe_padding ) $safe_padding = '';
				if ( strlen( $safe_padding ) > 4 ) $safe_padding = substr( $safe_padding, 0, 4 );
				$sb_instagram_image_padding = $safe_padding;

				$sb_instagram_image_padding_unit = sanitize_text_field( $_POST[ 'sb_instagram_image_padding_unit' ] );
				$sb_instagram_sort = sanitize_text_field( $_POST[ 'sb_instagram_sort' ] );
				$sb_instagram_background = sanitize_text_field( $_POST[ 'sb_instagram_background' ] );
				isset($_POST[ 'sb_instagram_show_btn' ]) ? $sb_instagram_show_btn = sanitize_text_field( $_POST[ 'sb_instagram_show_btn' ] ) : $sb_instagram_show_btn = '';
				$sb_instagram_btn_background = sanitize_text_field( $_POST[ 'sb_instagram_btn_background' ] );
				$sb_instagram_btn_text_color = sanitize_text_field( $_POST[ 'sb_instagram_btn_text_color' ] );
				$sb_instagram_btn_text = sanitize_text_field( $_POST[ 'sb_instagram_btn_text' ] );
				$sb_instagram_image_res = sanitize_text_field( $_POST[ 'sb_instagram_image_res' ] );
				//Header
				isset($_POST[ 'sb_instagram_show_header' ]) ? $sb_instagram_show_header = sanitize_text_field( $_POST[ 'sb_instagram_show_header' ] ) : $sb_instagram_show_header = '';
				isset($_POST[ 'sb_instagram_show_bio' ]) ? $sb_instagram_show_bio = sanitize_text_field( $_POST[ 'sb_instagram_show_bio' ] ) : $sb_instagram_show_bio = '';
				if ( function_exists( 'sanitize_textarea_field' ) ) {
					isset($_POST[ 'sb_instagram_custom_bio' ]) ? $sb_instagram_custom_bio = sanitize_textarea_field( $_POST[ 'sb_instagram_custom_bio' ] ) : $sb_instagram_custom_bio = '';
				} else {
					isset($_POST[ 'sb_instagram_custom_bio' ]) ? $sb_instagram_custom_bio = sanitize_text_field( $_POST[ 'sb_instagram_custom_bio' ] ) : $sb_instagram_custom_bio = '';
				}
				isset($_POST[ 'sb_instagram_custom_avatar' ]) ? $sb_instagram_custom_avatar = sanitize_text_field( $_POST[ 'sb_instagram_custom_avatar' ] ) : $sb_instagram_custom_avatar = '';
				if (isset($_POST[ 'sb_instagram_header_size' ]) ) $sb_instagram_header_size = $_POST[ 'sb_instagram_header_size' ];

				$sb_instagram_header_color = sanitize_text_field( $_POST[ 'sb_instagram_header_color' ] );
				//Follow button
				isset($_POST[ 'sb_instagram_show_follow_btn' ]) ? $sb_instagram_show_follow_btn = sanitize_text_field( $_POST[ 'sb_instagram_show_follow_btn' ] ) : $sb_instagram_show_follow_btn = '';
				$sb_instagram_folow_btn_background = sanitize_text_field( $_POST[ 'sb_instagram_folow_btn_background' ] );
				$sb_instagram_follow_btn_text_color = sanitize_text_field( $_POST[ 'sb_instagram_follow_btn_text_color' ] );
				$sb_instagram_follow_btn_text = sanitize_text_field( $_POST[ 'sb_instagram_follow_btn_text' ] );
				//Misc
				$sb_instagram_custom_css = $_POST[ 'sb_instagram_custom_css' ];
				$sb_instagram_custom_js = $_POST[ 'sb_instagram_custom_js' ];
				isset($_POST[ 'sb_instagram_ajax_theme' ]) ? $sb_instagram_ajax_theme = sanitize_text_field( $_POST[ 'sb_instagram_ajax_theme' ] ) : $sb_instagram_ajax_theme = '';
				isset($_POST[ 'enqueue_js_in_head' ]) ? $enqueue_js_in_head = $_POST[ 'enqueue_js_in_head' ] : $enqueue_js_in_head = '';
				isset($_POST[ 'disable_js_image_loading' ]) ? $disable_js_image_loading = $_POST[ 'disable_js_image_loading' ] : $disable_js_image_loading = '';
				isset($_POST[ 'sb_instagram_disable_resize' ]) ? $sb_instagram_disable_resize= sanitize_text_field( $_POST[ 'sb_instagram_disable_resize' ] ) : $sb_instagram_disable_resize = '';
				isset($_POST[ 'sb_instagram_favor_local' ]) ? $sb_instagram_favor_local = sanitize_text_field( $_POST[ 'sb_instagram_favor_local' ] ) : $sb_instagram_favor_local = '';
				isset($_POST[ 'sb_instagram_minnum' ]) ? $sb_instagram_minnum = sanitize_text_field( $_POST[ 'sb_instagram_minnum' ] ) : $sb_instagram_minnum = '';

				if (isset($_POST[ 'sb_instagram_cron' ]) ) $sb_instagram_cron = $_POST[ 'sb_instagram_cron' ];
				isset($_POST[ 'sb_instagram_backup' ]) ? $sb_instagram_backup = $_POST[ 'sb_instagram_backup' ] : $sb_instagram_backup = '';
				isset($_POST[ 'sb_ajax_initial' ]) ? $sb_ajax_initial = $_POST[ 'sb_ajax_initial' ] : $sb_ajax_initial = '';
				isset($_POST[ 'enqueue_css_in_shortcode' ]) ? $enqueue_css_in_shortcode = $_POST[ 'enqueue_css_in_shortcode' ] : $enqueue_css_in_shortcode = '';
				isset($_POST[ 'sbi_font_method' ]) ? $sbi_font_method = $_POST[ 'sbi_font_method' ] : $sbi_font_method = 'svg';
				isset($_POST[ 'sb_instagram_disable_awesome' ]) ? $sb_instagram_disable_awesome = sanitize_text_field( $_POST[ 'sb_instagram_disable_awesome' ] ) : $sb_instagram_disable_awesome = '';

				$options[ 'sb_instagram_width' ] = $sb_instagram_width;
				$options[ 'sb_instagram_width_unit' ] = $sb_instagram_width_unit;
				$options[ 'sb_instagram_feed_width_resp' ] = $sb_instagram_feed_width_resp;
				$options[ 'sb_instagram_height' ] = $sb_instagram_height;
				$options[ 'sb_instagram_height_unit' ] = $sb_instagram_height_unit;
				$options[ 'sb_instagram_num' ] = $sb_instagram_num;
				$options[ 'sb_instagram_cols' ] = $sb_instagram_cols;
				$options[ 'sb_instagram_disable_mobile' ] = $sb_instagram_disable_mobile;
				$options[ 'sb_instagram_image_padding' ] = $sb_instagram_image_padding;
				$options[ 'sb_instagram_image_padding_unit' ] = $sb_instagram_image_padding_unit;
				$options[ 'sb_instagram_sort' ] = $sb_instagram_sort;
				$options[ 'sb_instagram_background' ] = $sb_instagram_background;
				$options[ 'sb_instagram_show_btn' ] = $sb_instagram_show_btn;
				$options[ 'sb_instagram_btn_background' ] = $sb_instagram_btn_background;
				$options[ 'sb_instagram_btn_text_color' ] = $sb_instagram_btn_text_color;
				$options[ 'sb_instagram_btn_text' ] = $sb_instagram_btn_text;
				$options[ 'sb_instagram_image_res' ] = $sb_instagram_image_res;
				//Header
				$options[ 'sb_instagram_show_header' ] = $sb_instagram_show_header;
				$options[ 'sb_instagram_header_size' ] = $sb_instagram_header_size;
				$options[ 'sb_instagram_show_bio' ] = $sb_instagram_show_bio;
				$options[ 'sb_instagram_custom_bio' ] = $sb_instagram_custom_bio;
				$options[ 'sb_instagram_custom_avatar' ] = $sb_instagram_custom_avatar;
				$options[ 'sb_instagram_custom_bio' ] = $sb_instagram_custom_bio;
				$options[ 'sb_instagram_custom_avatar' ] = $sb_instagram_custom_avatar;
				$options[ 'sb_instagram_header_color' ] = $sb_instagram_header_color;
				//Follow button
				$options[ 'sb_instagram_show_follow_btn' ] = $sb_instagram_show_follow_btn;
				$options[ 'sb_instagram_folow_btn_background' ] = $sb_instagram_folow_btn_background;
				$options[ 'sb_instagram_follow_btn_text_color' ] = $sb_instagram_follow_btn_text_color;
				$options[ 'sb_instagram_follow_btn_text' ] = $sb_instagram_follow_btn_text;
				//Misc
				$options[ 'sb_instagram_custom_css' ] = $sb_instagram_custom_css;
				$options[ 'sb_instagram_custom_js' ] = $sb_instagram_custom_js;
				$options[ 'sb_instagram_ajax_theme' ] = $sb_instagram_ajax_theme;
				$options[ 'enqueue_js_in_head' ] = $enqueue_js_in_head;
				$options[ 'disable_js_image_loading' ] = $disable_js_image_loading;
				$options[ 'sb_instagram_disable_resize' ] = $sb_instagram_disable_resize;
				$options[ 'sb_instagram_favor_local' ] = $sb_instagram_favor_local;
				$options[ 'sb_instagram_minnum' ] = $sb_instagram_minnum;

				$options[ 'sb_ajax_initial' ] = $sb_ajax_initial;
				$options[ 'sb_instagram_cron' ] = $sb_instagram_cron;
				$options['sb_instagram_backup'] = $sb_instagram_backup;
				$options['enqueue_css_in_shortcode'] = $enqueue_css_in_shortcode;

				$options['sbi_font_method'] = $sbi_font_method;
				$options[ 'sb_instagram_disable_awesome' ] = $sb_instagram_disable_awesome;

				isset($_POST[ 'sb_instagram_custom_template' ]) ? $sb_instagram_custom_template = $_POST[ 'sb_instagram_custom_template' ] : $sb_instagram_custom_template = '';
				$options['custom_template'] = $sb_instagram_custom_template;
				isset($_POST[ 'sb_instagram_disable_admin_notice' ]) ? $sb_instagram_disable_admin_notice = $_POST[ 'sb_instagram_disable_admin_notice' ] : $sb_instagram_disable_admin_notice = '';
				$options['disable_admin_notice'] = $sb_instagram_disable_admin_notice;
				isset($_POST[ 'sb_instagram_enable_email_report' ]) ? $sb_instagram_enable_email_report = $_POST[ 'sb_instagram_enable_email_report' ] : $sb_instagram_enable_email_report = '';
				$options['enable_email_report'] = $sb_instagram_enable_email_report;
				isset($_POST[ 'sb_instagram_email_notification' ]) ? $sb_instagram_email_notification = $_POST[ 'sb_instagram_email_notification' ] : $sb_instagram_email_notification = '';
				$original = $options['email_notification'];
				$options['email_notification'] = $sb_instagram_email_notification;
				isset($_POST[ 'sb_instagram_email_notification_addresses' ]) ? $sb_instagram_email_notification_addresses = $_POST[ 'sb_instagram_email_notification_addresses' ] : $sb_instagram_email_notification_addresses = get_option( 'admin_email' );
				$options['email_notification_addresses'] = $sb_instagram_email_notification_addresses;

				if ( $original !== $sb_instagram_email_notification && $sb_instagram_enable_email_report === 'on' ){
					//Clear the existing cron event
					wp_clear_scheduled_hook('sb_instagram_feed_issue_email');

					$input = sanitize_text_field($_POST[ 'sb_instagram_email_notification' ] );
					$timestamp = strtotime( 'next ' . $input );

					if ( $timestamp - (3600 * 1) < time() ) {
						$timestamp = $timestamp + (3600 * 24 * 7);
					}
					$six_am_local = $timestamp + sbi_get_utc_offset() + (6*60*60);

					wp_schedule_event( $six_am_local, 'sbiweekly', 'sb_instagram_feed_issue_email' );
				}


				//Delete all SBI transients
				global $wpdb;
				$table_name = $wpdb->prefix . "options";
				$wpdb->query( "
                    DELETE
                    FROM $table_name
                    WHERE `option_name` LIKE ('%\_transient\_sbi\_%')
                    " );
				$wpdb->query( "
                    DELETE
                    FROM $table_name
                    WHERE `option_name` LIKE ('%\_transient\_timeout\_sbi\_%')
                    " );
				$wpdb->query( "
			        DELETE
			        FROM $table_name
			        WHERE `option_name` LIKE ('%\_transient\_&sbi\_%')
			        " );
				$wpdb->query( "
			        DELETE
			        FROM $table_name
			        WHERE `option_name` LIKE ('%\_transient\_timeout\_&sbi\_%')
			        " );

				if( $sb_instagram_cron == 'no' ) wp_clear_scheduled_hook('sb_instagram_cron_job');

				//Run cron when Misc settings are saved
				if( $sb_instagram_cron == 'yes' ){
					//Clear the existing cron event
					wp_clear_scheduled_hook('sb_instagram_cron_job');

					$sb_instagram_cache_time = $options[ 'sb_instagram_cache_time' ];
					$sb_instagram_cache_time_unit = $options[ 'sb_instagram_cache_time_unit' ];

					//Set the event schedule based on what the caching time is set to
					$sb_instagram_cron_schedule = 'hourly';
					if( $sb_instagram_cache_time_unit == 'hours' && $sb_instagram_cache_time > 5 ) $sb_instagram_cron_schedule = 'twicedaily';
					if( $sb_instagram_cache_time_unit == 'days' ) $sb_instagram_cron_schedule = 'daily';

					wp_schedule_event(time(), $sb_instagram_cron_schedule, 'sb_instagram_cron_job');

					sb_instagram_clear_page_caches();
				}

			} //End customize tab post

			//Save the settings to the settings array
			update_option( 'sb_instagram_settings', $options );

			?>
			<div class="updated"><p><strong><?php _e( 'Settings saved.', 'instagram-feed' ); ?></strong></p></div>
		<?php } ?>

	<?php } //End nonce check ?>


	<div id="sbi_admin" class="wrap">
        <?php
        $lite_notice_dismissed = get_transient( 'instagram_feed_dismiss_lite' );

        if ( ! $lite_notice_dismissed ) :
        ?>
        <div id="sbi-notice-bar" style="display:none">
            <span class="sbi-notice-bar-message"><?php _e( 'You\'re using Instagram Feed Lite. To unlock more features consider <a href="https://smashballoon.com/instagram-feed/?utm_campaign=instagram-free&utm_source=notices&utm_medium=litenotice" target="_blank" rel="noopener noreferrer">upgrading to Pro</a>.', 'instagram-feed'); ?></span>
            <button type="button" class="dismiss" title="<?php _e( 'Dismiss this message.', 'instagram-feed'); ?>" data-page="overview">
            </button>
        </div>
        <?php endif; ?>
		<div id="header">
			<h1><?php _e( 'Instagram Feed', 'instagram-feed' ); ?></h1>
		</div>
		<?php
		$sb_instagram_type = 'user';
		$returned_data = sbi_get_connected_accounts_data( $sb_instagram_at );
		$sb_instagram_at = $returned_data['access_token'];
		$connected_accounts = $returned_data['connected_accounts'];
		$user_feeds_returned = isset(  $returned_data['user_ids'] ) ? $returned_data['user_ids'] : false;
		if ( $user_feeds_returned ) {
			$user_feed_ids = $user_feeds_returned;
		} else {
			$user_feed_ids = ! is_array( $sb_instagram_user_id ) ? explode( ',', $sb_instagram_user_id ) : $sb_instagram_user_id;
		}

		$new_user_name = false;




		if( isset($_GET['access_token']) && isset($_GET['graph_api']) && empty($_POST) ) { ?>
			<?php
			$access_token = sbi_maybe_clean(urldecode($_GET['access_token']));
			//
			$url = 'https://graph.facebook.com/me/accounts?fields=instagram_business_account,access_token&limit=500&access_token='.$access_token;
			$args = array(
				'timeout' => 60,
				'sslverify' => false
			);
			$result = wp_remote_get( $url, $args );
			$pages_data = '{}';
			if ( ! is_wp_error( $result ) ) {
				$pages_data = $result['body'];
			} else {
				$page_error = $result;
			}

			$pages_data_arr = json_decode($pages_data);
			$num_accounts = 0;
			if(isset($pages_data_arr)){
				$num_accounts = is_array( $pages_data_arr->data ) ? count( $pages_data_arr->data ) : 0;
			}
			?>
            <div id="sbi_config_info" class="sb_list_businesses sbi_num_businesses_<?php echo $num_accounts; ?>">
                <div class="sbi_config_modal">
                    <div class="sbi-managed-pages">
						<?php if ( isset( $page_error ) && isset( $page_error->errors ) ) {
							foreach ($page_error->errors as $key => $item) {
								echo '<div class="sbi_user_id_error" style="display:block;"><strong>Connection Error: </strong>' . $key . ': ' . $item[0] . '</div>';
							}
						}
						?>
						<?php if( empty($pages_data_arr->data) ) : ?>
                            <span id="sbi-bus-account-error">
                            <p style="margin-top: 5px;"><b style="font-size: 16px">Couldn't find Business Profile</b><br />
                            Uh oh. It looks like this Facebook account is not currently connected to an Instagram Business profile. Please check that you are logged into the <a href="https://www.facebook.com/" target="_blank">Facebook account</a> in this browser which is associated with your Instagram Business Profile.</p>
                            <p><b style="font-size: 16px">Why do I need a Business Profile?</b><br />
                            A Business Profile is only required if you are displaying a Hashtag feed. If you want to display a regular User feed then you can do this by selecting to connect a Personal account instead. For directions on how to convert your Personal profile into a Business profile please <a href="https://smashballoon.com/instagram-business-profiles" target="_blank">see here</a>.</p>
                            </span>

						<?php elseif ( $num_accounts === 0 ): ?>
                            <span id="sbi-bus-account-error">
                            <p style="margin-top: 5px;"><b style="font-size: 16px">Couldn't find Business Profile</b><br />
                            Uh oh. It looks like this Facebook account is not currently connected to an Instagram Business profile. Please check that you are logged into the <a href="https://www.facebook.com/" target="_blank">Facebook account</a> in this browser which is associated with your Instagram Business Profile.</p>
                            <p>If you are, in fact, logged-in to the correct account please make sure you have Instagram accounts connected with your Facebook account by following <a href="https://smashballoon.com/reconnecting-an-instagram-business-profile/" target="_blank">this FAQ</a></p>
                            </span>
						<?php else: ?>
                            <p class="sbi-managed-page-intro"><b style="font-size: 16px;">Instagram Business profiles for this account</b><br /><i style="color: #666;">Note: In order to display a Hashtag feed you first need to select a Business profile below.</i></p>
							<?php if ( $num_accounts > 1 ) : ?>
                                <div class="sbi-managed-page-select-all"><input type="checkbox" id="sbi-select-all" class="sbi-select-all"><label for="sbi-select-all">Select All</label></div>
							<?php endif; ?>
                            <div class="sbi-scrollable-accounts">

								<?php foreach ( $pages_data_arr->data as $page => $page_data ) : ?>

									<?php if( isset( $page_data->instagram_business_account ) ) :

										$instagram_business_id = $page_data->instagram_business_account->id;

										$page_access_token = isset( $page_data->access_token ) ? $page_data->access_token : '';

										//Make another request to get page info
										$instagram_account_url = 'https://graph.facebook.com/'.$instagram_business_id.'?fields=name,username,profile_picture_url&access_token='.$access_token;

										$args = array(
											'timeout' => 60,
											'sslverify' => false
										);
										$result = wp_remote_get( $instagram_account_url, $args );
										$instagram_account_info = '{}';
										if ( ! is_wp_error( $result ) ) {
											$instagram_account_info = $result['body'];
										} else {
											$page_error = $result;
										}

										$instagram_account_data = json_decode($instagram_account_info);

										$instagram_biz_img = isset( $instagram_account_data->profile_picture_url ) ? $instagram_account_data->profile_picture_url : false;
										$selected_class = $instagram_business_id == $sb_instagram_user_id ? ' sbi-page-selected' : '';

										?>
										<?php if ( isset( $page_error ) && isset( $page_error->errors ) ) :
										foreach ($page_error->errors as $key => $item) {
											echo '<div class="sbi_user_id_error" style="display:block;"><strong>Connection Error: </strong>' . $key . ': ' . $item[0] . '</div>';
										}
									else : ?>
                                        <div class="sbi-managed-page<?php echo $selected_class; ?>" data-page-token="<?php echo esc_attr( $page_access_token ); ?>" data-token="<?php echo esc_attr( $access_token ); ?>" data-page-id="<?php echo esc_attr( $instagram_business_id ); ?>">
                                            <div class="sbi-add-checkbox">
                                                <input id="sbi-<?php echo esc_attr( $instagram_business_id ); ?>" type="checkbox" name="sbi_managed_pages[]" value="<?php echo esc_attr( $instagram_account_info ); ?>">
                                            </div>
                                            <div class="sbi-managed-page-details">
                                                <label for="sbi-<?php echo esc_attr( $instagram_business_id ); ?>"><img class="sbi-page-avatar" border="0" height="50" width="50" src="<?php echo esc_url( $instagram_biz_img ); ?>"><b style="font-size: 16px;"><?php echo esc_html( $instagram_account_data->name ); ?></b>
                                                    <br />@<?php echo esc_html( $instagram_account_data->username); ?><span style="font-size: 11px; margin-left: 5px;">(<?php echo esc_html( $instagram_business_id ); ?>)</span></label>
                                            </div>
                                        </div>
									<?php endif; ?>

									<?php endif; ?>

								<?php endforeach; ?>

                            </div> <!-- end scrollable -->
                            <p style="font-size: 11px; line-height: 1.5; margin-bottom: 0;"><i style="color: #666;">*<?php echo sprintf( __( 'Changing the password, updating privacy settings, or removing page admins for the related Facebook page may require %smanually reauthorizing our app%s to reconnect an account.', 'instagram-feed' ), '<a href="https://smashballoon.com/reauthorizing-our-instagram-facebook-app/" target="_blank" rel="noopener noreferrer">', '</a>' ); ?></i></p>

                            <a href="JavaScript:void(0);" id="sbi-connect-business-accounts" class="button button-primary" disabled="disabled" style="margin-top: 20px;">Connect Accounts</a>

						<?php endif; ?>

                        <a href="JavaScript:void(0);" class="sbi_modal_close"><i class="fa fa-times"></i></a>
                    </div>
                </div>
            </div>
		<?php } elseif ( isset( $_GET['access_token'] ) && isset( $_GET['account_type'] ) && empty( $_POST ) ) {
			$access_token = sanitize_text_field( $_GET['access_token'] );
			$account_type = sanitize_text_field( $_GET['account_type'] );
			$user_id = sanitize_text_field( $_GET['id'] );
			$user_name = sanitize_text_field( $_GET['username'] );
			$expires_in = (int)$_GET['expires_in'];
			$expires_timestamp = time() + $expires_in;

			$new_account_details = array(
				'access_token' => $access_token,
				'account_type' => $account_type,
				'user_id' => $user_id,
				'username' => $user_name,
				'expires_timestamp' => $expires_timestamp,
				'type' => 'basic'
			);


			$matches_existing_personal = sbi_matches_existing_personal( $new_account_details );
			$button_text = $matches_existing_personal ? __( 'Update This Account', 'instagram-feed' ) : __( 'Connect This Account', 'instagram-feed' );

			$account_json = sbi_json_encode( $new_account_details );

			$already_connected_as_business_account = (isset( $connected_accounts[ $user_id ] ) && $connected_accounts[ $user_id ]['type'] === 'business');

			?>

            <div id="sbi_config_info" class="sb_get_token">
                <div class="sbi_config_modal">
                    <div class="sbi_ca_username"><strong><?php echo esc_html( $user_name ); ?></strong></div>
                    <form action="<?php echo admin_url( 'admin.php?page=sb-instagram-feed' ); ?>" method="post">
                        <p class="sbi_submit">
							<?php if ( $already_connected_as_business_account ) :
								_e( 'The Instagram account you are logged into is already connected as a "business" account. Remove the business account if you\'d like to connect as a basic account instead (not recommended).', 'instagram-feed' );
								?>
							<?php else : ?>
                                <input type="submit" name="sbi_submit" id="sbi_connect_account" class="button button-primary" value="<?php echo esc_html( $button_text ); ?>">
							<?php  endif; ?>
                            <input type="hidden" name="sbi_account_json" value="<?php echo esc_attr( $account_json ) ; ?>">
                            <input type="hidden" name="sbi_connect_username" value="<?php echo esc_attr( $user_name ); ?>">
                            <a href="JavaScript:void(0);" class="button button-secondary" id="sbi_switch_accounts"><?php esc_html_e( 'Switch Accounts', 'instagram-feed' ); ?></a>
                        </p>
                    </form>
                    <a href="JavaScript:void(0);"><i class="sbi_modal_close fa fa-times"></i></a>
                </div>
            </div>
			<?php
		} elseif ( isset( $_POST['sbi_connect_username'] ) ) {

			$new_user_name = sanitize_text_field( $_POST['sbi_connect_username'] );
			$new_account_details = json_decode( stripslashes( $_POST['sbi_account_json'] ), true );
			array_map( 'sanitize_text_field', $new_account_details );

			$updated_options = sbi_connect_basic_account( $new_account_details );
			$connected_accounts = $updated_options['connected_accounts'];
			$user_feed_ids = $updated_options['sb_instagram_user_id'];
		}?>

		<?php //Display connected page
		if (isset( $sbi_connected_page ) && strpos($sbi_connected_page, ':') !== false) {

			$sbi_connected_page_pieces = explode(":", $sbi_connected_page);
			$sbi_connected_page_id = $sbi_connected_page_pieces[0];
			$sbi_connected_page_name = $sbi_connected_page_pieces[1];
			$sbi_connected_page_image = $sbi_connected_page_pieces[2];

			echo '&nbsp;';
			echo '<p style="font-weight: bold; margin-bottom: 5px;">Connected Business Profile:</p>';
			echo '<div class="sbi-managed-page sbi-no-select">';
			echo '<p><img class="sbi-page-avatar" border="0" height="50" width="50" src="'.$sbi_connected_page_image.'"><b>'.$sbi_connected_page_name.'</b> &nbsp; ('.$sbi_connected_page_id.')</p>';
			echo '</div>';
		}

		?>

        <form name="form1" method="post" action="">
			<input type="hidden" name="<?php echo $sb_instagram_settings_hidden_field; ?>" value="Y">
			<?php wp_nonce_field( 'sb_instagram_saving_settings', 'sb_instagram_settings_nonce' ); ?>

			<?php $sbi_active_tab = isset( $_GET[ 'tab' ] ) ? sanitize_text_field( $_GET['tab'] ) : 'configure'; ?>
			<h2 class="nav-tab-wrapper">
				<a href="?page=sb-instagram-feed&amp;tab=configure" class="nav-tab <?php echo $sbi_active_tab == 'configure' ? 'nav-tab-active' : ''; ?>"><?php _e( '1. Configure', 'instagram-feed' ); ?></a>
				<a href="?page=sb-instagram-feed&amp;tab=customize" class="nav-tab <?php echo $sbi_active_tab == 'customize' ? 'nav-tab-active' : ''; ?>"><?php _e( '2. Customize', 'instagram-feed' ); ?></a>
				<a href="?page=sb-instagram-feed&amp;tab=display"   class="nav-tab <?php echo $sbi_active_tab == 'display'   ? 'nav-tab-active' : ''; ?>"><?php _e( '3. Display Your Feed', 'instagram-feed' ); ?></a>
				<a href="?page=sb-instagram-feed&amp;tab=support"   class="nav-tab <?php echo $sbi_active_tab == 'support'   ? 'nav-tab-active' : ''; ?>"><?php _e( 'Support', 'instagram-feed' ); ?></a>
			</h2>

			<?php if( $sbi_active_tab == 'configure' ) { //Start Configure tab ?>
			<input type="hidden" name="<?php echo $sb_instagram_configure_hidden_field; ?>" value="Y">

			<table class="form-table">
				<tbody>
				<h3><?php _e( 'Configure', 'instagram-feed' ); ?></h3>

                <div id="sbi_config">
                    <a data-personal-basic-api="https://api.instagram.com/oauth/authorize?app_id=423965861585747&redirect_uri=https://api.smashballoon.com/instagram-basic-display-redirect.php&response_type=code&scope=user_profile,user_media&state=<?php echo admin_url('admin.php?page=sb-instagram-feed'); ?>"
                       data-new-api="https://www.facebook.com/dialog/oauth?client_id=254638078422287&redirect_uri=https://api.smashballoon.com/instagram-graph-api-redirect.php&scope=manage_pages,instagram_basic,instagram_manage_insights,instagram_manage_comments&state=<?php echo admin_url('admin.php?page=sb-instagram-feed'); ?>"
                       href="https://api.instagram.com/oauth/authorize?app_id=423965861585747&redirect_uri=https://api.smashballoon.com/instagram-basic-display-redirect.php&response_type=code&scope=user_profile,user_media&state=<?php echo admin_url('admin.php?page=sb-instagram-feed'); ?>" class="sbi_admin_btn"><i class="fa fa-user-plus" aria-hidden="true" style="font-size: 20px;"></i>&nbsp; <?php _e('Connect an Instagram Account', 'instagram-feed' ); ?></a>
                    <a href="https://smashballoon.com/instagram-feed/token/" target="_blank" style="position: relative; top: 14px; left: 15px;"><?php _e('Button not working?', 'instagram-feed'); ?></a>
                </div>

				<!-- Old Access Token -->
				<input name="sb_instagram_at" id="sb_instagram_at" type="hidden" value="<?php echo esc_attr( $sb_instagram_at ); ?>" size="80" maxlength="100" placeholder="Click button above to get your Access Token" />

                <tr valign="top">
                    <th scope="row"><label><?php _e( 'Instagram Accounts', 'instagram-feed' ); ?></label><span style="font-weight:normal; font-style:italic; font-size: 12px; display: block;"><?php _e('Use the button above to connect an Instagram account', 'instagram-feed'); ?></span></th>
                    <td class="sbi_connected_accounts_wrap">
						<?php if ( empty( $connected_accounts ) ) : ?>
                            <p class="sbi_no_accounts"><?php _e( 'No Instagram accounts connected. Click the button above to connect an account.', 'instagram-feed' ); ?></p><br />
						<?php else:
							if ( sbi_is_after_deprecation_deadline() ) {
								$deprecated_connected_account_message = __( '<b>Action Needed:</b> Reconnect this account to allow feed to update.', 'instagram-feed' );
							} else {
								$deprecated_connected_account_message = __( '<b>Action Needed:</b> Reconnect this account before June 1, 2020 to avoid disruption with this feed.', 'instagram-feed' );
							}

							$accounts_that_need_updating = sbi_get_user_names_of_personal_accounts_not_also_already_updated();
							?>
							<?php foreach ( $connected_accounts as $account ) :
							$username = $account['username'] ? $account['username'] : $account['user_id'];
							if ( isset( $account['local_avatar'] ) && $account['local_avatar'] && isset( $options['sb_instagram_favor_local'] ) && $options['sb_instagram_favor_local' ] === 'on' ) {
								$upload = wp_upload_dir();
								$resized_url = trailingslashit( $upload['baseurl'] ) . trailingslashit( SBI_UPLOADS_NAME );
								$profile_picture = '<img class="sbi_ca_avatar" src="'.$resized_url . $account['username'].'.jpg" />'; //Could add placeholder avatar image
							} else {
								$profile_picture = $account['profile_picture'] ? '<img class="sbi_ca_avatar" src="'.$account['profile_picture'].'" />' : ''; //Could add placeholder avatar image
							}

							$is_invalid_class = ! $account['is_valid'] ? ' sbi_account_invalid' : '';
							$in_user_feed = in_array( $account['user_id'], $user_feed_ids, true );
							$account_type = isset( $account['type'] ) ? $account['type'] : 'personal';
							$use_tagged = isset( $account['use_tagged'] ) && $account['use_tagged'] == '1';
							$is_private = isset( $account['private'] ) && $account['private'] !== false;

							if ( empty( $profile_picture ) && $account_type === 'personal' ) {
								$account_update = sbi_account_data_for_token( $account['access_token'] );
								if ( isset( $account['is_valid'] ) ) {
									$split = explode( '.', $account['access_token'] );
									$connected_accounts[ $split[0] ] = array(
										'access_token' => $account['access_token'],
										'user_id' => $split[0],
										'username' => $account_update['username'],
										'is_valid' => true,
										'last_checked' => time(),
										'profile_picture' => $account_update['profile_picture']
									);

									$sbi_options = get_option( 'sb_instagram_settings', array() );
									$sbi_options['connected_accounts'] = $connected_accounts;
									update_option( 'sb_instagram_settings', $sbi_options );
								}

							}
							$updated_or_new_account_class = $new_user_name === $username && $account_type !== 'business' ? ' sbi_ca_new_or_updated' : '';

							?>
                            <div class="sbi_connected_account<?php echo $is_invalid_class . $updated_or_new_account_class; ?><?php if ( $in_user_feed ) echo ' sbi_account_active' ?> sbi_account_type_<?php echo $account_type; ?>" id="sbi_connected_account_<?php esc_attr_e( $account['user_id'] ); ?>" data-accesstoken="<?php esc_attr_e( $account['access_token'] ); ?>" data-userid="<?php esc_attr_e( $account['user_id'] ); ?>" data-username="<?php esc_attr_e( $account['username'] ); ?>" data-type="<?php esc_attr_e( $account_type ); ?>" data-permissions="<?php if ( $use_tagged ) echo 'tagged'; ?>">
								<?php if ( $account_type === 'personal' && in_array( $username, $accounts_that_need_updating, true ) ) : ?>
                                    <div class="sbi_deprecated">
                                        <span><i class="fa fa-exclamation-circle" aria-hidden="true"></i><?php echo $deprecated_connected_account_message; ?> <button class="sbi_reconnect button-primary">Reconnect</button></span>
                                    </div>
								<?php endif; ?>
                                <div class="sbi_ca_alert">
                                    <span><?php _e( 'The Access Token for this account is expired or invalid. Click the button above to attempt to renew it.', 'instagram-feed' ) ?></span>
                                </div>
                                <div class="sbi_ca_info">

                                    <div class="sbi_ca_delete">
                                        <a href="<?php echo add_query_arg( 'disconnect', $account['user_id'], get_admin_url( null, 'admin.php?page=sb-instagram-feed' ) ); ?>" class="sbi_delete_account"><i class="fa fa-times"></i><span class="sbi_remove_text"><?php _e( 'Remove', 'instagram-feed' ); ?></span></a>
                                    </div>

                                    <div class="sbi_ca_username">
		                                <?php echo $profile_picture; ?>
                                        <strong><?php echo $username; ?><span><?php echo sbi_account_type_display( $account_type, isset( $account['private'] ) ); ?></span></strong>
                                    </div>

                                    <div class="sbi_ca_actions">
		                                <?php if ( ! $in_user_feed ) : ?>
                                            <a href="JavaScript:void(0);" class="sbi_use_in_user_feed button-primary"><i class="fa fa-plus-circle" aria-hidden="true"></i><?php _e( 'Add to Primary Feed', 'instagram-feed' ); ?></a>
		                                <?php else : ?>
                                            <a href="JavaScript:void(0);" class="sbi_remove_from_user_feed button-primary"><i class="fa fa-minus-circle" aria-hidden="true"></i><?php _e( 'Remove from Primary Feed', 'instagram-feed' ); ?></a>
		                                <?php endif; ?>
                                        <a class="sbi_ca_token_shortcode button-secondary" href="JavaScript:void(0);"><i class="fa fa-chevron-circle-right" aria-hidden="true"></i><?php _e( 'Add to another Feed', 'instagram-feed' ); ?></a>
                                        <a class="sbi_ca_show_token button-secondary" href="JavaScript:void(0);" title="<?php _e('Show access token and account info', 'instagram-feed'); ?>"><i class="fa fa-cog"></i></a>
		                                <?php if ( $is_private ) :
			                                $expires_in = max( 0, floor( ($account['expires_timestamp'] - time()) / DAY_IN_SECONDS ) );
			                                $message = $expires_in > 0 ? sprintf( __( 'Expires in %s days', 'instagram-feed' ), $expires_in ) : __( 'Access Token Expired', 'instagram-feed' );
			                                $alert_class = $expires_in < 10 ? ' sbi_alert' : '';
			                                ?>
                                            <div class="sbi_is_private<?php echo esc_attr( $alert_class ); ?>">
                                                <span><?php echo esc_html( $message ); ?></span>
                                                <a class="sbi_tooltip_link sbi_tooltip_outside" href="JavaScript:void(0);" style="position: relative; top: 2px;"><i class="fa fa-question-circle" aria-hidden="true"></i></a>

                                                <a href="https://api.instagram.com/oauth/authorize?app_id=423965861585747&redirect_uri=https://api.smashballoon.com/instagram-basic-display-redirect.php&response_type=code&scope=user_profile,user_media&state=<?php echo admin_url( 'admin.php?page=sb-instagram-feed' ); ?>" class="button button-secondary"><?php _e( 'Refresh now', 'instagram-feed' ); ?></a>
                                            </div>
                                            <p class="sbi_tooltip sbi-more-info" style="display: none; width: 100%; box-sizing: border-box;"><?php echo sprintf( __( 'This account is a "private" account on Instagram. It needs to be manually reconnected every 60 days. %sChange this account to be "public"%s to have access tokens that are automatically refreshed.', 'instagram-feed' ), '<a href="https://help.instagram.com/116024195217477/In" target="_blank">', '</a>' ); ?></p>
		                                <?php endif; ?>

                                    </div>

                                    <div class="sbi_ca_shortcode">

                                        <p><?php _e('Copy and paste this shortcode into your page or widget area', 'instagram-feed'); ?>:<br>
											<?php if ( !empty( $account['username'] ) ) : ?>
                                                <code>[instagram-feed user="<?php echo $account['username']; ?>"]</code>
											<?php endif; ?>
                                        </p>

                                        <p><?php _e('To add multiple users in the same feed, simply separate them using commas', 'instagram-feed'); ?>:<br>
											<?php if ( !empty( $account['username'] ) ) : ?>
                                                <code>[instagram-feed user="<?php echo $account['username']; ?>, a_second_user, a_third_user"]</code>
											<?php endif; ?>

                                        <p><?php echo sprintf( __('Click on the %s tab to learn more about shortcodes', 'instagram-feed'), '<a href="?page=sb-instagram-feed&tab=display" target="_blank">'. __( 'Display Your Feed', 'instagram-feed' ) . '</a>' ); ?></p>
                                    </div>

                                    <div class="sbi_ca_accesstoken">
                                        <span class="sbi_ca_token_label"><?php _e('Access Token', 'instagram-feed');?>:</span><input type="text" class="sbi_ca_token" value="<?php echo $account['access_token']; ?>" readonly="readonly" onclick="this.focus();this.select()" title="<?php _e('To copy, click the field then press Ctrl + C (PC) or Cmd + C (Mac).', 'instagram-feed');?>"><br>
                                        <span class="sbi_ca_token_label"><?php _e('User ID', 'instagram-feed');?>:</span><input type="text" class="sbi_ca_user_id" value="<?php echo $account['user_id']; ?>" readonly="readonly" onclick="this.focus();this.select()" title="<?php _e('To copy, click the field then press Ctrl + C (PC) or Cmd + C (Mac).', 'instagram-feed');?>"><br>
                                    </div>

                                </div>

                            </div>

						<?php endforeach;  ?>
						<?php endif; ?>
                        <a href="JavaScript:void(0);" class="sbi_manually_connect button-secondary"><?php _e( 'Manually Connect an Account', 'instagram-feed' ); ?></a>
                        <div class="sbi_manually_connect_wrap">
                            <input name="sb_manual_at" id="sb_manual_at" type="text" value="" style="margin-top: 4px; padding: 5px 9px; margin-left: 0px;" size="64" minlength="15" maxlength="200" placeholder="<?php esc_attr_e( 'Enter a valid Instagram Access Token', 'instagram-feed' ); ?>" /><span class='sbi_business_profile_tag'><?php _e('Business or Basic Display', 'instagram-feed');?></span>
                            <div class="sbi_manual_account_id_toggle">
                                <label><?php _e('Please enter the User ID for this Profile:', 'instagram-feed');?></label>
                                <input name="sb_manual_account_id" id="sb_manual_account_id" type="text" value="" style="margin-top: 4px; padding: 5px 9px; margin-left: 0px;" size="40" minlength="5" maxlength="100" placeholder="Eg: 15641403491391489" />
                            </div>
                            <p id="sbi_no_js_warning" class="sbi_notice"><?php echo sprintf( __('It looks like JavaScript is not working on this page. Some features may not work fully. Visit %sthis page%s for help resolving this issue.', 'instagram-feed'), '<a href="https://smashballoon.com/i-cant-connect-or-manage-accounts-on-the-instagram-feed-settings-page/" target="_blank" rel="noopener">', '</a>' ); ?></p>
                            <p class="sbi_submit" style="display: inline-block;"><input type="submit" name="sbi_submit" id="sbi_manual_submit" class="button button-primary" value="<?php _e('Connect This Account', 'instagram-feed' );?>"></p>
                        </div>
                    </td>
                </tr>

				<tr valign="top" class="sbi_feed_type">
					<th scope="row"><label><?php _e('Show Photos From:', 'instagram-feed'); ?></label><code class="sbi_shortcode"> type
							Eg: type=user id=12986477
						</code></th>
					<td>
						<div class="sbi_row">
							<div class="sbi_col sbi_one">
								<input type="radio" name="sb_instagram_type" id="sb_instagram_type_user" value="user" <?php if($sb_instagram_type == "user") echo "checked"; ?> />
								<label class="sbi_radio_label" for="sb_instagram_type_user"><?php _e( 'User Account:', 'instagram-feed' ); ?></label>
							</div>
							<div class="sbi_col sbi_two">
								<div class="sbi_user_feed_ids_wrap">
									<?php foreach ( $user_feed_ids as $feed_id ) : if ( $feed_id !== '' ) :?>
										<?php if( count($connected_accounts) > 0 ) { ?><div id="sbi_user_feed_id_<?php echo $feed_id; ?>" class="sbi_user_feed_account_wrap"><?php } ?>

										<?php if ( isset( $connected_accounts[ $feed_id ] ) && ! empty( $connected_accounts[ $feed_id ]['username'] ) ) : ?>
											<strong><?php echo $connected_accounts[ $feed_id ]['username']; ?></strong> <span>(<?php echo $feed_id; ?>)</span>
											<input name="sb_instagram_user_id[]" id="sb_instagram_user_id" type="hidden" value="<?php echo esc_attr( $feed_id ); ?>" />
										<?php elseif ( isset( $connected_accounts[ $feed_id ] ) && ! empty( $connected_accounts[ $feed_id ]['access_token'] ) ) : ?>
											<strong><?php echo $feed_id; ?></strong>
											<input name="sb_instagram_user_id[]" id="sb_instagram_user_id" type="hidden" value="<?php echo esc_attr( $feed_id ); ?>" />
										<?php endif; ?>

										<?php if( count($connected_accounts) > 0 ) { ?></div><?php } ?>
									<?php endif; endforeach; ?>
								</div>

								<?php if ( empty( $user_feed_ids ) ) : ?>
									<p class="sbi_no_accounts" style="margin-top: -3px; margin-right: 10px;"><?php _e( 'Connect a user account above', 'instagram-feed' ); ?></p>
								<?php endif; ?>

								<a class="sbi_tooltip_link" href="JavaScript:void(0);" style="margin: 5px 0 10px 0; display: inline-block; height: 19px;"><?php _e("How to display User feeds", 'instagram-feed' ); ?></a>
								<div class="sbi_tooltip"><?php _e("<p><b>Displaying Posts from Your User Account</b><br />Simply connect an account using the button above.</p><p style='padding-top:8px;'><b>Displaying Posts from Other Instagram Accounts</b><br />Due to recent changes in the Instagram API it is no longer possible to display photos from other Instagram accounts which you do not have access to. You can only display the user feed of an account which you connect above. You can connect as many account as you like by logging in using the button above, or manually copy/pasting an Access Token by selecting the 'Manually Connect an Account' option.</p><p style='padding-top:10px;'><b>Multiple Acounts</b><br />It is only possible to display feeds from Instagram accounts which you own. In order to display feeds from multiple accounts, first connect them above and then use the buttons to add the account either to your primary feed or to another feed on your site.</p>", 'instagram-feed'); ?></div><br />
							</div>

						</div>

						<div class="sbi_pro sbi_row">
							<div class="sbi_col sbi_one">
								<input disabled type="radio" name="sb_instagram_type" id="sb_instagram_type_hashtag" value="hashtag" <?php if($sb_instagram_type == "hashtag") echo "checked"; ?> />
								<label class="sbi_radio_label" for="sb_instagram_type_hashtag"><?php _e( 'Hashtag:', 'instagram-feed' ); ?></label>
							</div>
							<div class="sbi_col sbi_two">

								<p class="sbi_pro_tooltip"><?php _e( 'Upgrade to the Pro version to display Hashtag and Tagged feeds', 'instagram-feed' ); ?><i class="fa fa-caret-down" aria-hidden="true"></i></p>
								<a href="https://smashballoon.com/instagram-feed/?utm_campaign=instagram-free&utm_source=settings&utm_medium=hashtags" target="_blank" class="sbi_lock"><i class="fa fa-rocket"></i><?php _e('Pro', 'instagram-feed'); ?></a>

								<input readonly type="text" size="25" style="height: 32px; top: -2px; position: relative; box-shadow: none;" disabled />
								&nbsp;<a class="sbi_tooltip_link sbi_pro" href="JavaScript:void(0);"><?php _e( 'What is this?', 'instagram-feed' ); ?></a>

								<p class="sbi_tooltip"><?php _e( 'Display posts from a specific hashtag instead of from a user', 'instagram-feed' ); ?></p>
							</div>
						</div>

                        <div class="sbi_pro sbi_row">
                            <div class="sbi_col sbi_one">
                                <input disabled type="radio" name="sb_instagram_type" id="sb_instagram_type_tagged" value="tagged" <?php if($sb_instagram_type == "tagged") echo "checked"; ?> />
                                <label class="sbi_radio_label" for="sb_instagram_type_tagged"><?php _e( 'Tagged:', 'instagram-feed' ); ?></label>
                            </div>
                            <div class="sbi_col sbi_two">
                                <input readonly type="text" size="25" style="height: 32px; top: -2px; position: relative; box-shadow: none;" disabled />
                                &nbsp;<a class="sbi_tooltip_link sbi_pro" href="JavaScript:void(0);"><?php _e( 'What is this?', 'instagram-feed' ); ?></a>

                                <p class="sbi_tooltip"><?php _e( 'Display posts that your account has been tagged in.', 'instagram-feed' ); ?></p>
                            </div>
                        </div>

                        <div class="sbi_pro sbi_row sbi_mixed_directions">
                            <div class="sbi_col sbi_one">
                                <input type="radio" name="sb_instagram_type" disabled />
                                <label class="sbi_radio_label" for="sb_instagram_type_mixed">Mixed:</label>
                            </div>
                            <div class="sbi_col sbi_two">
                                <input readonly type="text" size="25" style="height: 32px; top: -2px; position: relative; box-shadow: none;" disabled />
                                &nbsp;<a class="sbi_tooltip_link sbi_pro" href="JavaScript:void(0);"><?php _e( 'What is this?', 'instagram-feed' ); ?></a>

                                <div class="sbi_tooltip sbi_type_tooltip">
                                    <p>
			                            <?php echo sprintf( __('To display multiple feed types in a single feed, use %s in your shortcode and then add the user name or hashtag for each feed into the shortcode, like so: %s. This will combine a user feed and a hashtag feed into the same feed.', 'instagram-feed'), 'type="mixed"', '<code>[instagram-feed type="mixed" user="smashballoon" hashtag="#awesomeplugins"]</code>' ); ?>
                                    </p>
                                    <p style="padding-top: 8px;"><b>Note:</b> To display a hashtag feed, it is required that you first connect an Instagram Business Profile using the <b>"Connect an Instagram Account"</b> button above. &nbsp;<a href="https://smashballoon.com/instagram-business-profiles/" target="_blank">Why is this required?</a>
                                    </p>
                                </div>
                            </div>

                        </div>

						<div class="sbi_row sbi_pro">
							<br>
							<a class="sbi_tooltip_link sbi_pro" href="JavaScript:void(0);" style="margin-left: 0;"><i class="fa fa-question-circle" aria-hidden="true" style="margin-right: 6px;"></i><?php _e('Combine multiple feed types into a single feed', 'instagram-feed'); ?></a>
							<p class="sbi_tooltip">
								<b><?php _e( 'Please note: this is only available in the <a href="https://smashballoon.com/instagram-feed/?utm_campaign=instagram-free&utm_source=settings&utm_medium=multipletypes" target="_blank">Pro version</a>', 'instagram-feed' ); ?>.</b><br />
								<?php echo sprintf( __('To display multiple feed types in a single feed, use %s in your shortcode and then add each user name or hashtag of each feed into the shortcode, like so: %s. This will combine a user feed and a hashtag feed into the same feed.', 'instagram-feed'), 'type="mixed"', '<code>[instagram-feed type="mixed" user="smashballoon" hashtag="#awesomeplugins"]</code>' ); ?>
							</p>
						</div>

					</td>
				</tr>

				<tr>
					<th class="bump-left"><label for="sb_instagram_preserve_settings" class="bump-left"><?php _e("Preserve settings when plugin is removed", 'instagram-feed'); ?></label></th>
					<td>
						<input name="sb_instagram_preserve_settings" type="checkbox" id="sb_instagram_preserve_settings" <?php if($sb_instagram_preserve_settings == true) echo "checked"; ?> />
						<label for="sb_instagram_preserve_settings"><?php _e('Yes', 'instagram-feed'); ?></label>
						<a class="sbi_tooltip_link" href="JavaScript:void(0);"><?php _e('What does this mean?', 'instagram-feed'); ?></a>
						<p class="sbi_tooltip"><?php _e('When removing the plugin your settings are automatically erased. Checking this box will prevent any settings from being deleted. This means that you can uninstall and reinstall the plugin without losing your settings.', 'instagram-feed'); ?></p>
					</td>
				</tr>


                <tr valign="top" class="sbi_cron_cache_opts">
                    <th scope="row"><?php _e( 'Check for new posts', 'instagram-feed' ); ?></th>
                    <td>

                        <div class="sbi_row">
                            <input type="radio" name="sbi_caching_type" id="sbi_caching_type_page" value="page" <?php if ( $sbi_caching_type === 'page' ) echo 'checked'; ?>>
                            <label for="sbi_caching_type_page"><?php _e( 'When the page loads', 'instagram-feed' ); ?></label>
                            <a class="sbi_tooltip_link" href="JavaScript:void(0);" style="position: relative; top: 2px;"><i class="fa fa-question-circle" aria-hidden="true"></i></a>
                            <p class="sbi_tooltip sbi-more-info"><?php _e( 'Your Instagram post data is temporarily cached by the plugin in your WordPress database. There are two ways that you can set the plugin to check for new data', 'instagram-feed' ); ?>:<br><br>
	                            <?php _e( '<b>1. When the page loads</b><br>Selecting this option means that when the cache expires then the plugin will check Instagram for new posts the next time that the feed is loaded. You can choose how long this data should be cached for. If you set the time to 60 minutes then the plugin will clear the cached data after that length of time, and the next time the page is viewed it will check for new data. <b>Tip:</b> If you\'re experiencing an issue with the plugin not updating automatically then try enabling the setting labeled <b>\'Force cache to clear on interval\'</b> which is located on the \'Customize\' tab.', 'instagram-feed' ); ?>
                                <br><br>
	                            <?php _e( '<b>2. In the background</b><br>Selecting this option means that the plugin will check for new data in the background so that the feed is updated behind the scenes. You can select at what time and how often the plugin should check for new data using the settings below. <b>Please note</b> that the plugin will initially check for data from Instagram when the page first loads, but then after that will check in the background on the schedule selected - unless the cache is cleared.</p>', 'instagram-feed' ); ?>
                        </div>
                        <div class="sbi_row sbi-caching-page-options" style="display: none;">
	                        <?php _e( 'Every', 'instagram-feed' ); ?>:
                            <input name="sb_instagram_cache_time" type="text" value="<?php echo esc_attr( $sb_instagram_cache_time ); ?>" size="4" />
                            <select name="sb_instagram_cache_time_unit">
                                <option value="minutes" <?php if($sb_instagram_cache_time_unit == "minutes") echo 'selected="selected"' ?> ><?php _e('Minutes', 'instagram-feed'); ?></option>
                                <option value="hours" <?php if($sb_instagram_cache_time_unit == "hours") echo 'selected="selected"' ?> ><?php _e('Hours', 'instagram-feed'); ?></option>
                                <option value="days" <?php if($sb_instagram_cache_time_unit == "days") echo 'selected="selected"' ?> ><?php _e('Days', 'instagram-feed'); ?></option>
                            </select>
                            <a class="sbi_tooltip_link" href="JavaScript:void(0);"><?php _e('What does this mean?', 'instagram-feed'); ?></a>
                            <p class="sbi_tooltip"><?php _e('Your Instagram posts are temporarily cached by the plugin in your WordPress database. You can choose how long the posts should be cached for. If you set the time to 1 hour then the plugin will clear the cache after that length of time and check Instagram for posts again.', 'instagram-feed'); ?></p>
                        </div>

                        <div class="sbi_row">
                            <input type="radio" name="sbi_caching_type" id="sbi_caching_type_cron" value="background" <?php if ( $sbi_caching_type === 'background' ) echo 'checked'; ?>>
                            <label for="sbi_caching_type_cron"><?php _e( 'In the background', 'instagram-feed' ); ?></label>
                        </div>
                        <div class="sbi_row sbi-caching-cron-options" style="display: block;">

                            <select name="sbi_cache_cron_interval" id="sbi_cache_cron_interval">
                                <option value="30mins" <?php if ( $sbi_cache_cron_interval === '30mins' ) echo 'selected'; ?>><?php _e( 'Every 30 minutes', 'instagram-feed' ); ?></option>
                                <option value="1hour" <?php if ( $sbi_cache_cron_interval === '1hour' ) echo 'selected'; ?>><?php _e( 'Every hour', 'instagram-feed' ); ?></option>
                                <option value="12hours" <?php if ( $sbi_cache_cron_interval === '12hours' ) echo 'selected'; ?>><?php _e( 'Every 12 hours', 'instagram-feed' ); ?></option>
                                <option value="24hours" <?php if ( $sbi_cache_cron_interval === '24hours' ) echo 'selected'; ?>><?php _e( 'Every 24 hours', 'instagram-feed' ); ?></option>
                            </select>

                            <div id="sbi-caching-time-settings" style="display: none;">
	                            <?php _e('at' ); ?>

                                <select name="sbi_cache_cron_time" style="width: 80px">
                                    <option value="1" <?php if ( $sbi_cache_cron_time === '1' ) echo 'selected'; ?>>1:00</option>
                                    <option value="2" <?php if ( $sbi_cache_cron_time === '2' ) echo 'selected'; ?>>2:00</option>
                                    <option value="3" <?php if ( $sbi_cache_cron_time === '3' ) echo 'selected'; ?>>3:00</option>
                                    <option value="4" <?php if ( $sbi_cache_cron_time === '4' ) echo 'selected'; ?>>4:00</option>
                                    <option value="5" <?php if ( $sbi_cache_cron_time === '5' ) echo 'selected'; ?>>5:00</option>
                                    <option value="6" <?php if ( $sbi_cache_cron_time === '6' ) echo 'selected'; ?>>6:00</option>
                                    <option value="7" <?php if ( $sbi_cache_cron_time === '7' ) echo 'selected'; ?>>7:00</option>
                                    <option value="8" <?php if ( $sbi_cache_cron_time === '8' ) echo 'selected'; ?>>8:00</option>
                                    <option value="9" <?php if ( $sbi_cache_cron_time === '9' ) echo 'selected'; ?>>9:00</option>
                                    <option value="10" <?php if ( $sbi_cache_cron_time === '10' ) echo 'selected'; ?>>10:00</option>
                                    <option value="11" <?php if ( $sbi_cache_cron_time === '11' ) echo 'selected'; ?>>11:00</option>
                                    <option value="0" <?php if ( $sbi_cache_cron_time === '0' ) echo 'selected'; ?>>12:00</option>
                                </select>

                                <select name="sbi_cache_cron_am_pm" style="width: 50px">
                                    <option value="am" <?php if ( $sbi_cache_cron_am_pm === 'am' ) echo 'selected'; ?>>AM</option>
                                    <option value="pm" <?php if ( $sbi_cache_cron_am_pm === 'pm' ) echo 'selected'; ?>>PM</option>
                                </select>
                            </div>

	                        <?php
	                        if ( wp_next_scheduled( 'sbi_feed_update' ) ) {
		                        $time_format = get_option( 'time_format' );
		                        if ( ! $time_format ) {
			                        $time_format = 'g:i a';
                                }
                                //
		                        $schedule = wp_get_schedule( 'sbi_feed_update' );
		                        if ( $schedule == '30mins' ) $schedule = __( 'every 30 minutes', 'instagram-feed' );
		                        if ( $schedule == 'twicedaily' ) $schedule = __( 'every 12 hours', 'instagram-feed' );
		                        $sbi_next_cron_event = wp_next_scheduled( 'sbi_feed_update' );
		                        echo '<p class="sbi-caching-sched-notice"><span><b>' . __( 'Next check', 'instagram-feed' ) . ': ' . date( $time_format, $sbi_next_cron_event + sbi_get_utc_offset() ) . ' (' . $schedule . ')</b> - ' . __( 'Note: Saving the settings on this page will clear the cache and reset this schedule', 'instagram-feed' ) . '</span></p>';
	                        } else {
		                        echo '<p style="font-size: 11px; color: #666;">' . __( 'Nothing currently scheduled', 'instagram-feed' ) . '</p>';
	                        }
	                        ?>

                        </div>

                    </td>
                </tr>

				</tbody>
			</table>

			<?php submit_button(); ?>
		</form>

		<p><i class="fa fa-chevron-circle-right" aria-hidden="true"></i>&nbsp; <?php _e('Next Step: <a href="?page=sb-instagram-feed&tab=customize">Customize your Feed</a>', 'instagram-feed'); ?></p>

		<p><i class="fa fa-life-ring" aria-hidden="true"></i>&nbsp; <?php _e('Need help setting up the plugin? Check out our <a href="https://smashballoon.com/instagram-feed/free/?utm_campaign=instagram-free&utm_source=supportpage&utm_medium=setupdirections" target="_blank">setup directions</a>', 'instagram-feed'); ?></p>


		<?php } // End Configure tab ?>



		<?php if( $sbi_active_tab == 'customize' ) { //Start Configure tab ?>

			<p class="sb_instagram_contents_links" id="general">
				<span><?php _e( 'Quick links:', 'instagram-feed' ); ?> </span>
				<a href="#general"><?php _e( 'General', 'instagram-feed' ); ?></a>
				<a href="#layout"><?php _e( 'Layout', 'instagram-feed' ); ?></a>
				<a href="#photos"><?php _e( 'Photos', 'instagram-feed' ); ?></a>
				<a href="#headeroptions"><?php _e( 'Header', 'instagram-feed' ); ?></a>
				<a href="#loadmore"><?php _e( "'Load More' Button", 'instagram-feed' ); ?></a>
				<a href="#follow"><?php _e( "'Follow' Button", 'instagram-feed' ); ?></a>
				<a href="#customcss"><?php _e( 'Custom CSS', 'instagram-feed' ); ?></a>
				<a href="#customjs"><?php _e( 'Custom JavaScript', 'instagram-feed' ); ?></a>
			</p>

			<input type="hidden" name="<?php echo $sb_instagram_customize_hidden_field; ?>" value="Y">

			<h3><?php _e( 'General', 'instagram-feed' ); ?></h3>

			<table class="form-table">
				<tbody>
				<tr valign="top">
					<th scope="row"><label><?php _e('Width of Feed', 'instagram-feed'); ?></label><code class="sbi_shortcode"> width  widthunit
							Eg: width=50 widthunit=%</code></th>
					<td>
						<input name="sb_instagram_width" type="text" value="<?php echo esc_attr( $sb_instagram_width ); ?>" id="sb_instagram_width" size="4" maxlength="4" />
						<select name="sb_instagram_width_unit" id="sb_instagram_width_unit">
							<option value="px" <?php if($sb_instagram_width_unit == "px") echo 'selected="selected"' ?> ><?php _e('px', 'instagram-feed'); ?></option>
							<option value="%" <?php if($sb_instagram_width_unit == "%") echo 'selected="selected"' ?> ><?php _e('%', 'instagram-feed'); ?></option>
						</select>
						<div id="sb_instagram_width_options">
							<input name="sb_instagram_feed_width_resp" type="checkbox" id="sb_instagram_feed_width_resp" <?php if($sb_instagram_feed_width_resp == true) echo "checked"; ?> /><label for="sb_instagram_feed_width_resp"><?php _e('Set to be 100% width on mobile?', 'instagram-feed'); ?></label>
							<a class="sbi_tooltip_link" href="JavaScript:void(0);"><?php _e( 'What does this mean?', 'instagram-feed' ); ?></a>
							<p class="sbi_tooltip"><?php _e("If you set a width on the feed then this will be used on mobile as well as desktop. Check this setting to set the feed width to be 100% on mobile so that it is responsive.", 'instagram-feed'); ?></p>
						</div>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label><?php _e('Height of Feed', 'instagram-feed'); ?></label><code class="sbi_shortcode"> height  heightunit
							Eg: height=500 heightunit=px</code></th>
					<td>
						<input name="sb_instagram_height" type="text" value="<?php echo esc_attr( $sb_instagram_height ); ?>" size="4" maxlength="4" />
						<select name="sb_instagram_height_unit">
							<option value="px" <?php if($sb_instagram_height_unit == "px") echo 'selected="selected"' ?> ><?php _e('px', 'instagram-feed'); ?></option>
							<option value="%" <?php if($sb_instagram_height_unit == "%") echo 'selected="selected"' ?> ><?php _e('%', 'instagram-feed'); ?></option>
						</select>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label><?php _e('Background Color', 'instagram-feed'); ?></label><code class="sbi_shortcode"> background
							Eg: background=d89531</code></th>
					<td>
						<input name="sb_instagram_background" type="text" value="<?php echo esc_attr( $sb_instagram_background ); ?>" class="sbi_colorpick" />
					</td>
				</tr>
				</tbody>
			</table>

			<hr id="layout" />
			<h3><?php _e('Layout', 'instagram-feed'); ?></h3>

			<table class="form-table">
				<tbody>
				<?php
				$selected_type = isset( $sb_instagram_layout_type ) ? $sb_instagram_layout_type : 'grid';
				$layout_types = array(
					'grid' => __( 'Grid', 'instagram-feed' ),
					'carousel' => __( 'Carousel', 'instagram-feed' ),
					'masonry' => __( 'Masonry', 'instagram-feed' ),
					'highlight' => __( 'Highlight', 'instagram-feed' )
				);
				$layout_images = array(
					'grid' => SBI_PLUGIN_URL . 'img/grid.png',
					'carousel' => SBI_PLUGIN_URL . 'img/carousel.png',
					'masonry' => SBI_PLUGIN_URL . 'img/masonry.png',
					'highlight' => SBI_PLUGIN_URL . 'img/highlight.png'
				);
				?>
				<tr valign="top">
					<th scope="row" class="sbi_pro"><label title="<?php _e('Click for shortcode option', 'instagram-feed'); ?>"><?php _e('Layout Type', 'instagram-feed'); ?></label><br /><span class="sbi_note" style="margin: 5px 0 0 0; font-weight: normal;"><?php _e('Select a layout to see associated<br />options', 'instagram-feed'); ?></span></th>
					<td>
						<div class="sbi_layouts">
							<?php foreach( $layout_types as $layout_type => $label ) : ?>
								<div class="sbi_layout_cell sbi_pro">
									<input class="sb_layout_type" id="sb_layout_type_<?php echo esc_attr( $layout_type ); ?>" name="sb_instagram_layout_type" type="radio" value="<?php echo esc_attr( $layout_type ); ?>" <?php if ( $selected_type === $layout_type ) echo 'checked'; ?>/><label for="sb_layout_type_<?php echo esc_attr( $layout_type ); ?>"><span class="sbi_label"><?php echo esc_html( $label ); ?></span><img src="<?php echo esc_url( $layout_images[ $layout_type ] ); ?>" /></label>
								</div>
							<?php endforeach; ?>

							<p class="sbi_pro_tooltip"><?php _e('Upgrade to the Pro version to unlock these layouts', 'instagram-feed'); ?><i class="fa fa-caret-down" aria-hidden="true"></i></p>
							<a href="https://smashballoon.com/instagram-feed/?utm_campaign=instagram-free&utm_source=settings&utm_medium=layouts" target="_blank" class="sbi_lock"><i class="fa fa-rocket"></i><?php _e('Pro', 'instagram-feed'); ?></a>

						</div>
						<div class="sb_layout_options_wrap sbi_pro">
							<a href="JavaScript:void(0);" class="sbi_close_options"><i class="fa fa-close"></i></a>
							<div class="sb_instagram_layout_settings sbi_layout_type_grid">
								<i class="fa fa-info-circle" aria-hidden="true" style="margin-right: 8px;"></i><span class="sbi_note" style="margin-left: 0;"><?php _e('A uniform grid of square-cropped images.', 'instagram-feed'); ?></span>
							</div>
							<div class="sb_instagram_layout_settings sbi_layout_type_masonry">
								<i class="fa fa-info-circle" aria-hidden="true" style="margin-right: 8px;"></i><span class="sbi_note" style="margin-left: 0;"><?php _e('Images in their original aspect ratios with no vertical space between posts.', 'instagram-feed'); ?></span>
							</div>
							<div class="sb_instagram_layout_settings sbi_layout_type_carousel">
								<div class="sb_instagram_layout_setting">
									<i class="fa fa-info-circle" aria-hidden="true" style="margin-right: 8px;"></i><span class="sbi_note" style="margin-left: 0;"><?php _e('Posts are displayed in a slideshow carousel.', 'instagram-feed'); ?></span>
								</div>
								<div class="sb_instagram_layout_setting">

									<label><?php _e('Number of Rows', 'instagram-feed'); ?></label><code class="sbi_shortcode"> carouselrows
										Eg: carouselrows=2</code>
									<br>
									<span class="sbi_note" style="margin: -5px 0 -10px 0; display: block;"><?php _e('Use the "Number of Columns" setting below this section to set how many posts are visible in the carousel at a given time.', 'instagram-feed'); ?></span>
									<br>
									<select name="sb_instagram_carousel_rows" id="sb_instagram_carousel_rows">
										<option value="1">1</option>
										<option value="2" selected="selected">2</option>
									</select>
								</div>
								<div class="sb_instagram_layout_setting">
									<label><?php _e('Loop Type', 'instagram-feed'); ?></label><code class="sbi_shortcode"> carouselloop
										Eg: carouselloop=rewind
										carouselloop=infinity</code>
									<br>
									<select name="sb_instagram_carousel_loop" id="sb_instagram_carousel_loop">
										<option value="rewind"><?php _e('Rewind', 'instagram-feed'); ?></option>
										<option value="infinity" selected="selected"><?php _e('Infinity', 'instagram-feed'); ?></option>
									</select>
								</div>
								<div class="sb_instagram_layout_setting">
									<input type="checkbox" name="sb_instagram_carousel_arrows" id="sb_instagram_carousel_arrows" checked="checked">
									<label><?php _e('Show Navigation Arrows', 'instagram-feed'); ?></label><code class="sbi_shortcode"> carouselarrows
										Eg: carouselarrows=true</code>
								</div>
								<div class="sb_instagram_layout_setting">
									<input type="checkbox" name="sb_instagram_carousel_pag" id="sb_instagram_carousel_pag">
									<label><?php _e('Show Pagination', 'instagram-feed'); ?></label><code class="sbi_shortcode"> carouselpag
										Eg: carouselpag=true</code>
								</div>
								<div class="sb_instagram_layout_setting">
									<input type="checkbox" name="sb_instagram_carousel_autoplay" id="sb_instagram_carousel_autoplay">
									<label><?php _e('Enable Autoplay', 'instagram-feed'); ?></label><code class="sbi_shortcode"> carouselautoplay
										Eg: carouselautoplay=true</code>
								</div>
								<div class="sb_instagram_layout_setting">
									<label><?php _e('Interval Time', 'instagram-feed'); ?></label><code class="sbi_shortcode"> carouseltime
										Eg: carouseltime=8000</code>
									<br>
									<input name="sb_instagram_carousel_interval" type="text" value="5000" size="6"><?php _e("miliseconds", 'instagram-feed'); ?>
								</div>
							</div>

							<div class="sb_instagram_layout_settings sbi_layout_type_highlight">
								<div class="sb_instagram_layout_setting">
									<i class="fa fa-info-circle" aria-hidden="true" style="margin-right: 8px;"></i><span class="sbi_note" style="margin-left: 0;"><?php _e('Masonry style, square-cropped, image only (no captions or likes/comments below image). "Highlighted" posts are twice as large.', 'instagram-feed'); ?></span>
								</div>
								<div class="sb_instagram_layout_setting">
									<label title="Click for shortcode option"><?php _e('Highlighting Type', 'instagram-feed'); ?></label><code class="sbi_shortcode"> highlighttype
										Eg: highlighttype=pattern</code>
									<br>
									<select name="sb_instagram_highlight_type" id="sb_instagram_highlight_type">
										<option value="pattern" selected="selected"><?php _e('Pattern', 'instagram-feed'); ?></option>
										<option value="id"><?php _e('Post ID', 'instagram-feed'); ?></option>
										<option value="hashtag"><?php _e('Hashtag', 'instagram-feed'); ?></option>
									</select>
								</div>
								<div class="sb_instagram_highlight_sub_options sb_instagram_highlight_pattern sb_instagram_layout_setting" style="display: block;">
									<label></label><code class="sbi_shortcode"> highlightoffset
										Eg: highlightoffset=2</code>
									<br>
									<input name="sb_instagram_highlight_offset" type="number" min="0" value="0" style="width: 50px;">
								</div>
								<div class="sb_instagram_highlight_sub_options sb_instagram_highlight_pattern sb_instagram_layout_setting" style="display: block;">
									<label><?php _e('Pattern', 'instagram-feed'); ?></label><code class="sbi_shortcode"> highlightpattern
										Eg: highlightpattern=3</code>
									<br>
									<span><?php _e('Highlight every', 'instagram-feed'); ?></span><input name="sb_instagram_highlight_factor" type="number" min="2" value="6" style="width: 50px;"><span><?php _e('posts', 'instagram-feed'); ?></span>
								</div>
								<div class="sb_instagram_highlight_sub_options sb_instagram_highlight_hashtag sb_instagram_layout_setting" style="display: none;">
									<label><?php _e('Highlight Posts with these Hashtags', 'instagram-feed'); ?></label>
									<input name="sb_instagram_highlight_hashtag" id="sb_instagram_highlight_hashtag" type="text" size="40" value="#fishing">&nbsp;<a class="sbi_tooltip_link" href="JavaScript:void(0);"><?php _e('What is this?', 'instagram-feed'); ?></a>
									<br>
									<span class="sbi_note" style="margin-left: 0;"><?php _e('Separate multiple hashtags using commas', 'instagram-feed'); ?></span>


									<p class="sbi_tooltip"><?php _e('You can use this setting to highlight posts by a hashtag. Use a specified hashtag in your posts and they will be automatically highlighted in your feed.', 'instagram-feed'); ?></p>
								</div>
								<div class="sb_instagram_highlight_sub_options sb_instagram_highlight_ids sb_instagram_layout_setting" style="display: none;">
									<label><?php _e('Highlight Posts by ID', 'instagram-feed'); ?></label>
									<textarea name="sb_instagram_highlight_ids" id="sb_instagram_highlight_ids" style="width: 100%;" rows="3">sbi_1852317219231323590_3269008872</textarea>
									<br>
									<span class="sbi_note" style="margin-left: 0;"><?php _e('Separate IDs using commas', 'instagram-feed'); ?></span>

									&nbsp;<a class="sbi_tooltip_link" href="JavaScript:void(0);"><?php _e('What is this?', 'instagram-feed'); ?></a>
									<p class="sbi_tooltip"><?php _e('You can use this setting to highlight posts by their ID. Enable and use "moderation mode", check the box to show post IDs underneath posts, then copy and paste IDs into this text box.', 'instagram-feed'); ?></p>
								</div>
							</div>

						</div>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label><?php _e('Number of Photos', 'instagram-feed'); ?></label><code class="sbi_shortcode"> num
							Eg: num=6</code></th>
					<td>
						<input name="sb_instagram_num" type="text" value="<?php echo esc_attr( $sb_instagram_num ); ?>" size="4" maxlength="4" />
						<span class="sbi_note"><?php _e('Number of photos to show initially.', 'instagram-feed'); ?></span>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label><?php _e('Number of Columns', 'instagram-feed'); ?></label><code class="sbi_shortcode"> cols
							Eg: cols=3</code></th>
					<td>
						<select name="sb_instagram_cols">
							<option value="1" <?php if($sb_instagram_cols == "1") echo 'selected="selected"' ?> ><?php _e('1', 'instagram-feed'); ?></option>
							<option value="2" <?php if($sb_instagram_cols == "2") echo 'selected="selected"' ?> ><?php _e('2', 'instagram-feed'); ?></option>
							<option value="3" <?php if($sb_instagram_cols == "3") echo 'selected="selected"' ?> ><?php _e('3', 'instagram-feed'); ?></option>
							<option value="4" <?php if($sb_instagram_cols == "4") echo 'selected="selected"' ?> ><?php _e('4', 'instagram-feed'); ?></option>
							<option value="5" <?php if($sb_instagram_cols == "5") echo 'selected="selected"' ?> ><?php _e('5', 'instagram-feed'); ?></option>
							<option value="6" <?php if($sb_instagram_cols == "6") echo 'selected="selected"' ?> ><?php _e('6', 'instagram-feed'); ?></option>
							<option value="7" <?php if($sb_instagram_cols == "7") echo 'selected="selected"' ?> ><?php _e('7', 'instagram-feed'); ?></option>
							<option value="8" <?php if($sb_instagram_cols == "8") echo 'selected="selected"' ?> ><?php _e('8', 'instagram-feed'); ?></option>
							<option value="9" <?php if($sb_instagram_cols == "9") echo 'selected="selected"' ?> ><?php _e('9', 'instagram-feed'); ?></option>
							<option value="10" <?php if($sb_instagram_cols == "10") echo 'selected="selected"' ?> ><?php _e('10', 'instagram-feed'); ?></option>
						</select>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label><?php _e('Padding around Images', 'instagram-feed'); ?></label><code class="sbi_shortcode"> imagepadding  imagepaddingunit</code></th>
					<td>
						<input name="sb_instagram_image_padding" type="text" value="<?php echo esc_attr( $sb_instagram_image_padding ); ?>" size="4" maxlength="4" />
						<select name="sb_instagram_image_padding_unit">
							<option value="px" <?php if($sb_instagram_image_padding_unit == "px") echo 'selected="selected"' ?> ><?php _e('px', 'instagram-feed'); ?></option>
							<option value="%" <?php if($sb_instagram_image_padding_unit == "%") echo 'selected="selected"' ?> ><?php _e('%', 'instagram-feed'); ?></option>
						</select>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label><?php _e("Disable mobile layout", 'instagram-feed'); ?></label><code class="sbi_shortcode"> disablemobile
							Eg: disablemobile=true</code></th>
					<td>
						<input type="checkbox" name="sb_instagram_disable_mobile" id="sb_instagram_disable_mobile" <?php if($sb_instagram_disable_mobile == true) echo 'checked="checked"' ?> />
						&nbsp;<a class="sbi_tooltip_link" href="JavaScript:void(0);"><?php _e( 'What does this mean?', 'instagram-feed' ); ?></a>
						<p class="sbi_tooltip"><?php _e("By default on mobile devices the layout automatically changes to use fewer columns. Checking this setting disables the mobile layout.", 'instagram-feed'); ?></p>
					</td>
				</tr>
				</tbody>
			</table>

			<?php submit_button(); ?>

			<hr id="photos" />
			<h3><?php _e('Photos', 'instagram-feed'); ?></h3>

			<table class="form-table">
				<tbody>
				<tr valign="top">
					<th scope="row"><label><?php _e('Sort Photos By', 'instagram-feed'); ?></label><code class="sbi_shortcode"> sortby
							Eg: sortby=random</code></th>
					<td>
						<select name="sb_instagram_sort">
							<option value="none" <?php if($sb_instagram_sort == "none") echo 'selected="selected"' ?> ><?php _e('Newest to oldest', 'instagram-feed'); ?></option>
							<option value="random" <?php if($sb_instagram_sort == "random") echo 'selected="selected"' ?> ><?php _e('Random', 'instagram-feed'); ?></option>
						</select>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label><?php _e('Image Resolution', 'instagram-feed'); ?></label><code class="sbi_shortcode"> imageres
							Eg: imageres=thumb</code></th>
					<td>

						<select name="sb_instagram_image_res">
							<option value="auto" <?php if($sb_instagram_image_res == "auto") echo 'selected="selected"' ?> ><?php _e('Auto-detect (recommended)', 'instagram-feed'); ?></option>
							<option value="thumb" <?php if($sb_instagram_image_res == "thumb") echo 'selected="selected"' ?> ><?php _e('Thumbnail (150x150)', 'instagram-feed'); ?></option>
							<option value="medium" <?php if($sb_instagram_image_res == "medium") echo 'selected="selected"' ?> ><?php _e('Medium (320x320)', 'instagram-feed'); ?></option>
							<option value="full" <?php if($sb_instagram_image_res == "full") echo 'selected="selected"' ?> ><?php _e('Full size (640x640)', 'instagram-feed'); ?></option>
						</select>

						&nbsp;<a class="sbi_tooltip_link" href="JavaScript:void(0);"><?php _e( 'What does Auto-detect mean?', 'instagram-feed'); ?></a>
						<p class="sbi_tooltip"><?php _e("Auto-detect means that the plugin automatically sets the image resolution based on the size of your feed.", 'instagram-feed'); ?></p>

					</td>
				</tr>
				</tbody>
			</table>

			<span><a href="javascript:void(0);" class="button button-secondary sbi-show-pro"><b>+</b> <?php _e('Show Pro Options', 'instagram-feed'); ?></a></span>

			<div class="sbi-pro-options">
				<p class="sbi-upgrade-link">
					<i class="fa fa-rocket" aria-hidden="true"></i>&nbsp; <a href="https://smashballoon.com/instagram-feed/?utm_campaign=instagram-free&utm_source=settings&utm_medium=general" target="_blank"><?php _e('Upgrade to Pro to enable these settings', 'instagram-feed'); ?></a>
				</p>
				<table class="form-table">
					<tbody>
					<tr valign="top" class="sbi_pro">
						<th scope="row"><label><?php _e('Media Type to Display','instagram-feed'); ?></label></th>
						<td>
							<select name="sb_instagram_media_type" disabled>
								<option value="all"><?php _e('All','instagram-feed'); ?></option>
								<option value="photos"><?php _e('Photos only','instagram-feed'); ?></option>
								<option value="videos"><?php _e('Videos only','instagram-feed'); ?></option>
							</select>
						</td>
					</tr>

					<tr valign="top" class="sbi_pro">
						<th scope="row"><label><?php _e("Enable Pop-up Lightbox", 'instagram-feed'); ?></label></th>
						<td>
							<input type="checkbox" name="sb_instagram_captionlinks" id="sb_instagram_captionlinks" disabled />
						</td>
					</tr>

					<tr valign="top" class="sbi_pro">
						<th scope="row"><label><?php _e("Link Posts to URL in Caption (Shoppable feed)",'instagram-feed'); ?></label></th>
						<td>
							<input type="checkbox" name="sb_instagram_captionlinks" id="sb_instagram_captionlinks" disabled />
							&nbsp;<a class="sbi_tooltip_link sbi_pro" href="JavaScript:void(0);"><?php _e("What will this do?",'instagram-feed'); ?></a>
							<p class="sbi_tooltip"><?php _e("Checking this box will change the link for each post to any url included in the caption for that Instagram post. The lightbox will be disabled. Visit <a href='https://smashballoon.com/make-a-shoppable-feed?utm_campaign=instagram-free&utm_source=settings&utm_medium=shoppable'>this link</a> to learn how this works.",'instagram-feed'); ?></p>
						</td>
					</tr>
					</tbody>
				</table>
			</div>


			<hr />
			<h3><?php _e('Photo Hover Style','instagram-feed'); ?></h3>

			<p style="padding-bottom: 18px;">
				<a href="https://smashballoon.com/instagram-feed/?utm_campaign=instagram-free&utm_source=settings&utm_medium=photohover" target="_blank"><?php _e('Upgrade to Pro to enable Photo Hover styles','instagram-feed'); ?></a><br />
				<a href="javascript:void(0);" class="button button-secondary sbi-show-pro"><b>+</b> <?php _e('Show Pro Options','instagram-feed'); ?></a>
			</p>

			<div class="sbi-pro-options" style="margin-top: -15px;">
				<table class="form-table">
					<tbody>
					<tr valign="top" class="sbi_pro">
						<th scope="row"><label><?php _e('Hover Background Color', 'instagram-feed'); ?></label></th>
						<td>
							<input name="sb_hover_background" type="text" disabled class="sbi_colorpick" />
						</td>
					</tr>
					<tr valign="top" class="sbi_pro">
						<th scope="row"><label><?php _e('Hover Text Color', 'instagram-feed'); ?></label></th>
						<td>
							<input name="sb_hover_text" type="text" disabled class="sbi_colorpick" />
						</td>
					</tr>
					<tr valign="top" class="sbi_pro">
						<th scope="row"><label><?php _e('Information to display', 'instagram-feed'); ?></label></th>
						<td>
							<div>
								<input name="sbi_hover_inc_username" type="checkbox" disabled />
								<label for="sbi_hover_inc_username"><?php _e('Username', 'instagram-feed'); ?></label>
							</div>
							<div>
								<input name="sbi_hover_inc_icon" type="checkbox" disabled />
								<label for="sbi_hover_inc_icon"><?php _e('Expand Icon', 'instagram-feed'); ?></label>
							</div>
							<div>
								<input name="sbi_hover_inc_date" type="checkbox" disabled />
								<label for="sbi_hover_inc_date"><?php _e('Date', 'instagram-feed'); ?></label>
							</div>
							<div>
								<input name="sbi_hover_inc_instagram" type="checkbox" disabled />
								<label for="sbi_hover_inc_instagram"><?php _e('Instagram Icon/Link', 'instagram-feed'); ?></label>
							</div>
							<div>
								<input name="sbi_hover_inc_location" type="checkbox" disabled />
								<label for="sbi_hover_inc_location"><?php _e('Location', 'instagram-feed'); ?></label>
							</div>
							<div>
								<input name="sbi_hover_inc_caption" type="checkbox" disabled />
								<label for="sbi_hover_inc_caption"><?php _e('Caption', 'instagram-feed'); ?></label>
							</div>
							<div>
								<input name="sbi_hover_inc_likes" type="checkbox" disabled />
								<label for="sbi_hover_inc_likes"><?php _e('Like/Comment Icons', 'instagram-feed'); ?></label>
							</div>
						</td>
					</tr>

					</tbody>
				</table>
			</div>


			<hr />
			<h3><?php _e( 'Carousel', 'instagram-feed' ); ?></h3>
			<p style="padding-bottom: 18px;">
				<a href="https://smashballoon.com/instagram-feed/?utm_campaign=instagram-free&utm_source=settings&utm_medium=carousel" target="_blank"><?php _e('Upgrade to Pro to enable Carousels', 'instagram-feed'); ?></a><br />
				<a href="javascript:void(0);" class="button button-secondary sbi-show-pro"><b>+</b> <?php _e('Show Pro Options', 'instagram-feed'); ?></a>
			</p>

			<div class="sbi-pro-options" style="margin-top: -15px;">
				<table class="form-table">
					<tbody>
					<tr valign="top" class="sbi_pro">
						<th scope="row"><label><?php _e("Enable Carousel", 'instagram-feed'); ?></label></th>
						<td>
							<input type="checkbox" disabled />
							&nbsp;<a class="sbi_tooltip_link" href="JavaScript:void(0);"><?php _e("What is this?", 'instagram-feed'); ?></a>
							<p class="sbi_tooltip"><?php _e("Enable this setting to create a carousel slider out of your photos.", 'instagram-feed'); ?></p>
						</td>
					</tr>
					<tr valign="top" class="sbi_pro">
						<th scope="row"><label><?php _e("Show Navigation Arrows", 'instagram-feed'); ?></label></th>
						<td>
							<input type="checkbox" disabled />
						</td>
					</tr>
					<tr valign="top" class="sbi_pro">
						<th scope="row"><label><?php _e("Show Pagination", 'instagram-feed'); ?></label></th>
						<td>
							<input type="checkbox" disabled />
						</td>
					</tr>
					<tr valign="top" class="sbi_pro">
						<th scope="row"><label><?php _e("Enable Autoplay", 'instagram-feed'); ?></label></th>
						<td>
							<input type="checkbox" disabled />
						</td>
					</tr>
					<tr valign="top" class="sbi_pro">
						<th scope="row"><label><?php _e("Interval Time", 'instagram-feed'); ?></label></th>
						<td>
							<input name="sb_instagram_carousel_interval" type="text" disabled size="6" /><?php _e("milliseconds", 'instagram-feed'); ?>
						</td>
					</tr>
					</tbody>
				</table>
			</div>



			<hr id="headeroptions" />
			<h3><?php _e("Header", 'instagram-feed'); ?></h3>
			<table class="form-table">
				<tbody>
				<tr valign="top">
					<th scope="row"><label><?php _e("Show Feed Header", 'instagram-feed'); ?></label><code class="sbi_shortcode"> showheader
							Eg: showheader=false</code></th>
					<td>
						<input type="checkbox" name="sb_instagram_show_header" id="sb_instagram_show_header" <?php if($sb_instagram_show_header == true) echo 'checked="checked"' ?> />
					</td>
				</tr>
                <tr valign="top">
                    <th scope="row"><label><?php _e('Header Size', 'instagram-feed'); ?></label><code class="sbi_shortcode"> headersize
                            Eg: headersize=medium</code></th>
                    <td>
                        <select name="sb_instagram_header_size" id="sb_instagram_header_size" style="float: left;">
                            <option value="small" <?php if($sb_instagram_header_size == "small") echo 'selected="selected"' ?> ><?php _e('Small', 'instagram-feed'); ?></option>
                            <option value="medium" <?php if($sb_instagram_header_size == "medium") echo 'selected="selected"' ?> ><?php _e('Medium', 'instagram-feed'); ?></option>
                            <option value="large" <?php if($sb_instagram_header_size == "large") echo 'selected="selected"' ?> ><?php _e('Large', 'instagram-feed'); ?></option>
                        </select>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label><?php _e("Show Bio Text", 'instagram-feed'); ?></label><code class="sbi_shortcode"> showbio
                            Eg: showbio=false</code></th>
                    <td>
						<?php $sb_instagram_show_bio = isset( $sb_instagram_show_bio ) ? $sb_instagram_show_bio  : true; ?>
                        <input type="checkbox" name="sb_instagram_show_bio" id="sb_instagram_show_bio" <?php if($sb_instagram_show_bio == true) echo 'checked="checked"' ?> />
                        <span class="sbi_note"><?php _e("Only applies for Instagram accounts with bios", 'instagram-feed'); ?></span>
                        <div class="sb_instagram_box" style="display: block;">
                            <div class="sb_instagram_box_setting" style="display: block;">
                                <label style="padding-bottom: 0;"><?php _e("Add Custom Bio Text", 'instagram-feed'); ?></label><code class="sbi_shortcode" style="margin-top: 5px;"> custombio
                                    Eg: custombio="My custom bio."</code>
                                <br>
                                <span class="sbi_aside" style="padding-bottom: 5px; display: block;"><?php _e("Use your own custom bio text in the feed header. Bio text is automatically retrieved from Instagram for Business accounts.", 'instagram-feed'); ?></span>

                                <textarea type="text" name="sb_instagram_custom_bio" id="sb_instagram_custom_bio" ><?php echo esc_textarea( stripslashes( $sb_instagram_custom_bio ) ); ?></textarea>
                                &nbsp;<a class="sbi_tooltip_link sbi_tooltip_under" href="JavaScript:void(0);"><?php _e("Why is my bio not displaying automatically?", 'instagram-feed'); ?></a>
                                <p class="sbi_tooltip" style="padding: 10px 0 0 0; width: 99%;"><?php echo sprintf( __("Instagram is deprecating their old API for Personal accounts on June 1, 2020. The plugin supports their new API, however, their new API does not yet include the bio text for Personal accounts. If you require this feature then it is available if you convert your Instagram account from a Personal to a Business account by following %s. Note: If you previously had a Personal account connected then the plugin has saved the avatar for that feed and will continue to use it automatically.", 'instagram-feed'), '<a href="https://smashballoon.com/instagram-business-profiles/" target="_blank">these directions</a>' ); ?></p>
                            </div>
                        </div>

                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row"><label><?php _e("Use Custom Avatar", 'instagram-feed'); ?></label><code class="sbi_shortcode"> customavatar
                            Eg: customavatar="https://my-website.com/avatar.jpg"</code></th>
                    <td>
                        <input type="text" name="sb_instagram_custom_avatar" class="large-text" id="sb_instagram_custom_avatar" value="<?php echo esc_attr( stripslashes( $sb_instagram_custom_avatar ) ); ?>" placeholder="https://example.com/avatar.jpg" />
                        <span class="sbi_aside"><?php _e("Avatar is automatically retrieved from Instagram for Business accounts", 'instagram-feed'); ?></span>
                        <br>
                        <a class="sbi_tooltip_link sbi_tooltip_under" href="JavaScript:void(0);"><?php _e("Why is my avatar not displaying automatically?", 'instagram-feed'); ?></a>

                        <p class="sbi_tooltip sbi_tooltip_under_text" style="padding: 10px 0 0 0;"><?php echo sprintf( __("Instagram is deprecating their old API for Personal accounts on June 1, 2020. The plugin supports their new API, however, their new API does not yet include the avatar image for Personal accounts. If you require this feature then it is available if you convert your Instagram account from a Personal to a Business account by following %s. Note: If you previously had a Personal account connected then the plugin has saved the bio text for that feed and will continue to use it automatically.", 'instagram-feed'), '<a href="https://smashballoon.com/instagram-business-profiles/" target="_blank">these directions</a>' ); ?></p>

                    </td>
                </tr>
				<tr valign="top">
					<th scope="row"><label><?php _e('Header Text Color', 'instagram-feed'); ?></label><code class="sbi_shortcode"> headercolor
							Eg: headercolor=fff</code></th>
					<td>
						<input name="sb_instagram_header_color" type="text" value="<?php echo esc_attr( $sb_instagram_header_color ); ?>" class="sbi_colorpick" />
					</td>
				</tr>
				</tbody>
			</table>

			<span><a href="javascript:void(0);" class="button button-secondary sbi-show-pro"><b>+</b> <?php _e('Show Pro Options', 'instagram-feed'); ?></a></span>

			<div class="sbi-pro-options">
				<p class="sbi-upgrade-link">
					<i class="fa fa-rocket" aria-hidden="true"></i>&nbsp; <a href="https://smashballoon.com/instagram-feed/?utm_campaign=instagram-free&utm_source=settings&utm_medium=header" target="_blank"><?php _e('Upgrade to Pro to enable these settings', 'instagram-feed'); ?></a>
				</p>
				<table class="form-table">
					<tbody>
					<tr valign="top" class="sbi_pro">
						<th scope="row"><label><?php _e('Header Style','instagram-feed'); ?></label></th>
						<td>
							<select name="sb_instagram_header_style" style="float: left;">
								<option value="circle"><?php _e('Standard','instagram-feed'); ?></option>
								<option value="boxed"><?php _e('Boxed','instagram-feed'); ?></option>
								<option value="centered"><?php _e('Centered','instagram-feed'); ?></option>
							</select>
						</td>
					</tr>
					<tr valign="top" class="sbi_pro">
						<th scope="row"><label><?php _e("Show Number of Followers",'instagram-feed'); ?></label></th>
						<td>
							<input type="checkbox" disabled />
							<span class="sbi_note"><?php _e("This only applies when displaying photos from a User ID",'instagram-feed'); ?></span>
						</td>
					</tr>
					</tbody>
				</table>
			</div>

			<?php submit_button(); ?>


			<hr />
			<h3><?php _e("Caption", 'instagram-feed'); ?></h3>
			<p style="padding-bottom: 18px;">
				<a href="https://smashballoon.com/instagram-feed/?utm_campaign=instagram-free&utm_source=settings&utm_medium=caption" target="_blank"><?php _e("Upgrade to Pro to enable Photo Captions", 'instagram-feed'); ?></a><br />
				<a href="javascript:void(0);" class="button button-secondary sbi-show-pro"><b>+</b> <?php _e("Show Pro Options", 'instagram-feed'); ?></a>
			</p>

			<div class="sbi-pro-options" style="margin-top: -15px;">
				<table class="form-table">
					<tbody>
					<tr valign="top" class="sbi_pro">
						<th scope="row"><label><?php _e("Show Caption", 'instagram-feed'); ?></label></th>
						<td>
							<input type="checkbox" disabled />
						</td>
					</tr>
					<tr valign="top" class="sbi_pro">
						<th scope="row"><label><?php _e("Maximum Text Length", 'instagram-feed'); ?></label></th>
						<td>
							<input disabled size="4" />Characters
							&nbsp;<a class="sbi_tooltip_link" href="JavaScript:void(0);"><?php _e("What is this?", 'instagram-feed'); ?></a>
							<p class="sbi_tooltip"><?php _e("The number of characters of text to display in the caption. An elipsis link will be added to allow the user to reveal more text if desired.", 'instagram-feed'); ?></p>
						</td>
					</tr>
					<tr valign="top" class="sbi_pro">
						<th scope="row"><label><?php _e('Text Color', 'instagram-feed'); ?></label></th>
						<td>
							<input type="text" disabled class="sbi_colorpick" />
						</td>
					</tr>
					<tr valign="top" class="sbi_pro">
						<th scope="row"><label><?php _e('Text Size', 'instagram-feed'); ?></label></th>
						<td>
							<select name="sb_instagram_caption_size" style="width: 180px;" disabled>
								<option value="inherit"  ><?php _e('Inherit from theme', 'instagram-feed'); ?></option>
								<option value="10" >10px</option>
								<option value="11" >11px</option>
								<option value="12" >12px</option>
								<option value="13" >13px</option>
								<option value="14" >14px</option>
								<option value="16" >16px</option>
								<option value="18" >18px</option>
								<option value="20" >20px</option>
								<option value="24" >24px</option>
								<option value="28" >28px</option>
								<option value="32" >32px</option>
								<option value="36" >36px</option>
								<option value="40" >40px</option>
							</select>
						</td>
					</tr>
					</tbody>
				</table>
			</div>


			<hr />
			<h3><?php _e("Likes &amp; Comments", 'instagram-feed'); ?></h3>
			<p style="padding-bottom: 18px;">
				<a href="https://smashballoon.com/instagram-feed/?utm_campaign=instagram-free&utm_source=settings&utm_medium=stats" target="_blank"><?php _e("Upgrade to Pro to enable Likes &amp; Comments", 'instagram-feed'); ?></a><br />
				<a href="javascript:void(0);" class="button button-secondary sbi-show-pro"><b>+</b> <?php _e("Show Pro Options", 'instagram-feed'); ?></a>
			</p>

			<div class="sbi-pro-options" style="margin-top: -15px;">
				<table class="form-table">
					<tbody>
					<tr valign="top" class="sbi_pro">
						<th scope="row"><label><?php _e("Show Icons", 'instagram-feed'); ?></label></th>
						<td>
							<input type="checkbox" disabled />
						</td>
					</tr>
					<tr valign="top" class="sbi_pro">
						<th scope="row"><label><?php _e('Icon Color', 'instagram-feed'); ?></label></th>
						<td>
							<input type="text" disabled class="sbi_colorpick" />
						</td>
					</tr>
					<tr valign="top" class="sbi_pro">
						<th scope="row"><label><?php _e('Icon Size', 'instagram-feed'); ?></label></th>
						<td>
							<select disabled name="sb_instagram_meta_size" style="width: 180px;">
								<option value="inherit"><?php _e('Inherit from theme', 'instagram-feed'); ?></option>
								<option value="10" >10px</option>
								<option value="11" >11px</option>
								<option value="12" >12px</option>
								<option value="13" >13px</option>
								<option value="14" >14px</option>
								<option value="16" >16px</option>
								<option value="18" >18px</option>
								<option value="20" >20px</option>
								<option value="24" >24px</option>
								<option value="28" >28px</option>
								<option value="32" >32px</option>
								<option value="36" >36px</option>
								<option value="40" >40px</option>
							</select>
						</td>
					</tr>
					</tbody>
				</table>
			</div>


			<hr />
			<h3><?php _e('Lightbox Comments', 'instagram-feed'); ?></h3>

			<p style="padding-bottom: 18px;">
				<a href="https://smashballoon.com/instagram-feed/?utm_campaign=instagram-free&utm_source=settings&utm_medium=comments" target="_blank"><?php _e('Upgrade to Pro to enable Comments', 'instagram-feed'); ?></a><br />
				<a href="javascript:void(0);" class="button button-secondary sbi-show-pro"><b>+</b> <?php _e('Show Pro Options', 'instagram-feed'); ?></a>
			</p>

			<div class="sbi-pro-options" style="margin-top: -15px;">
				<table class="form-table">
					<tbody>

					<tr valign="top" class="sbi_pro">
						<th scope="row"><label><?php _e('Show Comments in Lightbox', 'instagram-feed'); ?></label></th>
						<td style="padding: 5px 10px 0 10px;">
							<input type="checkbox" disabled style="margin-right: 15px;" />
							<input class="button-secondary" style="margin-top: -5px;" disabled value="<?php echo esc_attr( 'Clear Comment Cache', 'instagram-feed' ); ?>" />
							&nbsp;<a class="sbi_tooltip_link" href="JavaScript:void(0);"><?php _e("What is this?", 'instagram-feed'); ?></a>
							<p class="sbi_tooltip"><?php _e("This will remove the cached comments saved in the database", 'instagram-feed'); ?></p>
						</td>
					</tr>
					<tr valign="top" class="sbi_pro">
						<th scope="row"><label><?php _e('Number of Comments', 'instagram-feed'); ?></label></th>
						<td>
							<input name="sb_instagram_num_comments" type="text" disabled size="4" />
							<span class="sbi_note"><?php _e('Max number of latest comments.', 'instagram-feed'); ?></span>
							&nbsp;<a class="sbi_tooltip_link" href="JavaScript:void(0);"><?php _e("What is this?", 'instagram-feed'); ?></a>
							<p class="sbi_tooltip"><?php _e("This is the maximum number of comments that will be shown in the lightbox. If there are more comments available than the number set, only the latest comments will be shown", 'instagram-feed'); ?></p>
						</td>
					</tr>

					</tbody>
				</table>
			</div>


			<hr id="loadmore" />
			<h3><?php _e("'Load More' Button", 'instagram-feed'); ?></h3>
			<table class="form-table">
				<tbody>
				<tr valign="top">
					<th scope="row"><label><?php _e("Show the 'Load More' button", 'instagram-feed'); ?></label><code class="sbi_shortcode"> showbutton
							Eg: showbutton=false</code></th>
					<td>
						<input type="checkbox" name="sb_instagram_show_btn" id="sb_instagram_show_btn" <?php if($sb_instagram_show_btn == true) echo 'checked="checked"' ?> />
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label><?php _e('Button Background Color', 'instagram-feed'); ?></label><code class="sbi_shortcode"> buttoncolor
							Eg: buttoncolor=8224e3</code></th>
					<td>
						<input name="sb_instagram_btn_background" type="text" value="<?php echo esc_attr( $sb_instagram_btn_background ); ?>" class="sbi_colorpick" />
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label><?php _e('Button Text Color', 'instagram-feed'); ?></label><code class="sbi_shortcode"> buttontextcolor
							Eg: buttontextcolor=eeee22</code></th>
					<td>
						<input name="sb_instagram_btn_text_color" type="text" value="<?php echo esc_attr( $sb_instagram_btn_text_color ); ?>" class="sbi_colorpick" />
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label><?php _e('Button Text', 'instagram-feed'); ?></label><code class="sbi_shortcode"> buttontext
							Eg: buttontext="Show more.."</code></th>
					<td>
						<input name="sb_instagram_btn_text" type="text" value="<?php echo esc_attr( stripslashes( $sb_instagram_btn_text ) ); ?>" size="20" />
					</td>
				</tr>
				</tbody>
			</table>

			<?php submit_button(); ?>

			<hr id="follow" />
			<h3><?php _e("'Follow' Button", 'instagram-feed'); ?></h3>
			<table class="form-table">
				<tbody>
				<tr valign="top">
					<th scope="row"><label><?php _e("Show the Follow button", 'instagram-feed'); ?></label><code class="sbi_shortcode"> showfollow
							Eg: showfollow=true</code></th>
					<td>
						<input type="checkbox" name="sb_instagram_show_follow_btn" id="sb_instagram_show_follow_btn" <?php if($sb_instagram_show_follow_btn == true) echo 'checked="checked"' ?> />
					</td>
				</tr>

				<tr valign="top">
					<th scope="row"><label><?php _e('Button Background Color', 'instagram-feed'); ?></label><code class="sbi_shortcode"> followcolor
							Eg: followcolor=28a1bf</code></th>
					<td>
						<input name="sb_instagram_folow_btn_background" type="text" value="<?php echo esc_attr( $sb_instagram_folow_btn_background ); ?>" class="sbi_colorpick" />
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label><?php _e('Button Text Color', 'instagram-feed'); ?></label><code class="sbi_shortcode"> followtextcolor
							Eg: followtextcolor=000</code></th>
					<td>
						<input name="sb_instagram_follow_btn_text_color" type="text" value="<?php echo esc_attr( $sb_instagram_follow_btn_text_color ); ?>" class="sbi_colorpick" />
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label><?php _e('Button Text', 'instagram-feed'); ?></label><code class="sbi_shortcode"> followtext
							Eg: followtext="Follow me"</code></th>
					<td>
						<input name="sb_instagram_follow_btn_text" type="text" value="<?php echo esc_attr( stripslashes( $sb_instagram_follow_btn_text ) ); ?>" size="30" />
					</td>
				</tr>
				</tbody>
			</table>

			<hr id="filtering" />
			<h3><?php _e('Post Filtering', 'instagram-feed'); ?></h3>

			<p style="padding-bottom: 18px;">
				<a href="https://smashballoon.com/instagram-feed/?utm_campaign=instagram-free&utm_source=settings&utm_medium=filtering" target="_blank"><?php _e('Upgrade to Pro to enable Post Filtering options', 'instagram-feed'); ?></a><br />
				<a href="javascript:void(0);" class="button button-secondary sbi-show-pro"><b>+</b> <?php _e('Show Pro Options', 'instagram-feed'); ?></a>
			</p>

			<div class="sbi-pro-options" style="margin-top: -15px;">

				<table class="form-table">
					<tbody>
					<tr valign="top" class="sbi_pro">
						<th scope="row"><label><?php _e('Remove photos containing these words or hashtags', 'instagram-feed'); ?></label></th>
						<td>
							<div class="sb_instagram_apply_labels">
								<p><?php _e('Apply to:', 'instagram-feed'); ?></p>
								<input class="sb_instagram_incex_one_all" type="radio" value="all" disabled /><label><?php _e('All feeds', 'instagram-feed'); ?></label>
								<input class="sb_instagram_incex_one_all" type="radio" value="one" disabled /><label><?php _e('One feed', 'instagram-feed'); ?></label>
							</div>

							<input disabled name="sb_instagram_exclude_words" id="sb_instagram_exclude_words" type="text" style="width: 70%;" value="" />
							<br />
							<span class="sbi_note" style="margin-left: 0;"><?php _e('Separate words/hashtags using commas', 'instagram-feed'); ?></span>
							&nbsp;<a class="sbi_tooltip_link sbi_pro" href="JavaScript:void(0);"><?php _e( 'What is this?', 'instagram-feed'); ?></a>
							<p class="sbi_tooltip"><?php _e("You can use this setting to remove photos which contain certain words or hashtags in the caption. Separate multiple words or hashtags using commas.", 'instagram-feed'); ?></p>
						</td>
					</tr>

					<tr valign="top" class="sbi_pro">
						<th scope="row"><label><?php _e('Show photos containing these words or hashtags', 'instagram-feed'); ?></label></th>
						<td>
							<div class="sb_instagram_apply_labels">
								<p><?php _e('Apply to:', 'instagram-feed'); ?></p>
								<input class="sb_instagram_incex_one_all" type="radio" value="all" disabled /><label><?php _e('All feeds', 'instagram-feed'); ?></label>
								<input class="sb_instagram_incex_one_all" type="radio" value="one" disabled /><label><?php _e('One feed', 'instagram-feed'); ?></label>
							</div>

							<input disabled name="sb_instagram_include_words" id="sb_instagram_include_words" type="text" style="width: 70%;" value="" />
							<br />
							<span class="sbi_note" style="margin-left: 0;"><?php _e('Separate words/hashtags using commas', 'instagram-feed'); ?></span>
							&nbsp;<a class="sbi_tooltip_link sbi_pro" href="JavaScript:void(0);"><?php _e( 'What is this?', 'instagram-feed'); ?></a>
							<p class="sbi_tooltip"><?php _e("You can use this setting to only show photos which contain certain words or hashtags in the caption. For example, adding <code>sheep, cow, dog</code> will show any photos which contain either the word sheep, cow, or dog. Separate multiple words or hashtags using commas.", 'instagram-feed'); ?></p>
						</td>
					</tr>
					</tbody>
				</table>
			</div>


			<hr id="moderation" />
			<h3><?php _e('Moderation', 'instagram-feed'); ?></h3>

			<p style="padding-bottom: 18px;">
				<a href="https://smashballoon.com/instagram-feed/?utm_campaign=instagram-free&utm_source=settings&utm_medium=moderation" target="_blank"><?php _e('Upgrade to Pro to enable Moderation options', 'instagram-feed'); ?></a><br />
				<a href="javascript:void(0);" class="button button-secondary sbi-show-pro"><b>+</b> <?php _e('Show Pro Options', 'instagram-feed'); ?></a>
			</p>

			<div class="sbi-pro-options" style="margin-top: -15px;">
				<table class="form-table">
					<tbody>
					<tr valign="top" class="sbi_pro">
						<th scope="row"><label><?php _e('Moderation Type', 'instagram-feed'); ?></label></th>
						<td>
							<input class="sb_instagram_moderation_mode" checked="checked" disabled type="radio" value="visual" style="margin-top: 0;" /><label><?php _e('Visual', 'instagram-feed'); ?></label>
							<input class="sb_instagram_moderation_mode" disabled type="radio" value="manual" style="margin-top: 0; margin-left: 10px;"/><label><?php _e('Manual', 'instagram-feed'); ?></label>

							<p class="sbi_tooltip" style="display: block;"><?php _e("<b>Visual Moderation Mode</b><br />This adds a button to each feed that will allow you to hide posts, block users, and create white lists from the front end using a visual interface. Visit <a href='https://smashballoon.com/guide-to-moderation-mode/?utm_campaign=instagram-free&utm_source=settings&utm_medium=moderationmode' target='_blank'>this page</a> for details", 'instagram-feed'); ?></p>

						</td>
					</tr>

					<tr valign="top" class="sbi_pro">
						<th scope="row"><label><?php _e('Only show posts by these users', 'instagram-feed'); ?></label></th>
						<td>
							<input type="text" style="width: 70%;" disabled /><br />
							<span class="sbi_note" style="margin-left: 0;"><?php _e('Separate usernames using commas', 'instagram-feed'); ?></span>

							&nbsp;<a class="sbi_tooltip_link" href="JavaScript:void(0);"><?php _e("What is this?", 'instagram-feed'); ?></a>
							<p class="sbi_tooltip"><?php _e("You can use this setting to show photos only from certain users in your feed. Just enter the usernames here which you want to show. Separate multiple usernames using commas.", 'instagram-feed'); ?></p>
						</td>
					</tr>
					<tr valign="top" class="sbi_pro">
						<th scope="row"><label><?php _e('White lists', 'instagram-feed'); ?></label></th>
						<td>
							<div class="sbi_white_list_names_wrapper">
								<?php _e("No white lists currently created", 'instagram-feed'); ?>
							</div>

							<input disabled class="button-secondary" type="submit" value="<?php esc_attr_e( 'Clear White Lists', 'instagram-feed' ); ?>" />
							&nbsp;<a class="sbi_tooltip_link" href="JavaScript:void(0);" style="display: inline-block; margin-top: 5px;"><?php _e("What is this?", 'instagram-feed'); ?></a>
							<p class="sbi_tooltip"><?php _e("This will remove all of the white lists from the database", 'instagram-feed'); ?></p>
						</td>
					</tr>

					</tbody>
				</table>
			</div>



			<hr id="customcss" />
			<h3><?php _e('Misc', 'instagram-feed'); ?></h3>

			<table class="form-table">
				<tbody>
				<tr valign="top">
					<td style="padding-bottom: 0;">
						<?php _e('<strong style="font-size: 15px;">Custom CSS</strong><br />Enter your own custom CSS in the box below', 'instagram-feed'); ?>
					</td>
				</tr>
				<tr valign="top">
					<td>
						<textarea name="sb_instagram_custom_css" id="sb_instagram_custom_css" style="width: 70%;" rows="7"><?php echo esc_textarea( stripslashes($sb_instagram_custom_css), 'instagram-feed' ); ?></textarea>
					</td>
				</tr>
				<tr valign="top" id="customjs">
					<td style="padding-bottom: 0;">
						<?php _e('<strong style="font-size: 15px;">Custom JavaScript</strong><br />Enter your own custom JavaScript/jQuery in the box below', 'instagram-feed'); ?>
					</td>
				</tr>
				<tr valign="top">
					<td>
						<textarea name="sb_instagram_custom_js" id="sb_instagram_custom_js" style="width: 70%;" rows="7"><?php echo esc_textarea( stripslashes($sb_instagram_custom_js), 'instagram-feed' ); ?></textarea>
					</td>
				</tr>
				</tbody>
			</table>
			<table class="form-table">
				<tbody>

				<tr valign="top">
					<th scope="row"><label for="sb_instagram_ajax_theme" class="bump-left"><?php _e("Are you using an Ajax powered theme?", 'instagram-feed'); ?></label></th>
					<td>
						<input name="sb_instagram_ajax_theme" type="checkbox" id="sb_instagram_ajax_theme" <?php if($sb_instagram_ajax_theme == true) echo "checked"; ?> />
						<label for="sb_instagram_ajax_theme"><?php _e('Yes', 'instagram-feed'); ?></label>
						<a class="sbi_tooltip_link" href="JavaScript:void(0);"><?php _e('What does this mean?', 'instagram-feed'); ?></a>
						<p class="sbi_tooltip"><?php _e("When navigating your site, if your theme uses Ajax to load content into your pages (meaning your page doesn't refresh) then check this setting. If you're not sure then please check with the theme author.", 'instagram-feed'); ?></p>
					</td>
				</tr>

                <tr>
                    <th class="bump-left"><label class="bump-left"><?php _e("Image Resizing", 'instagram-feed'); ?></label></th>
                    <td>
                        <input name="sb_instagram_disable_resize" type="checkbox" id="sb_instagram_disable_resize" <?php if($sb_instagram_disable_resize == true) echo "checked"; ?> />
                        <label for="sb_instagram_disable_resize"><?php _e('Disable Local Image Storing and Resizing', 'instagram-feed'); ?></label><br><br>
                        <input name="sb_instagram_favor_local" type="checkbox" id="sb_instagram_favor_local" <?php if($sb_instagram_favor_local == true) echo "checked"; ?> />
                        <label for="sb_instagram_favor_local"><?php _e('Favor Local Images', 'instagram-feed'); ?></label><br><br>

                        <input id="sbi_reset_resized" class="button-secondary" type="submit" value="<?php esc_attr_e( 'Reset Resized Images' ); ?>" style="vertical-align: middle;"/>
                        <a class="sbi_tooltip_link" href="JavaScript:void(0);"><?php _e('What does this mean?', 'instagram-feed'); ?></a>
                        <p class="sbi_tooltip"><?php _e("The plugin creates and stores resized versions of images in order to serve a more optimized resolution size in the feed. Click this button to clear all data related to resized images. Enable the setting to favor local images to always use a local, resized image if one is available.", 'instagram-feed'); ?></p>
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row"><label><?php _e('API request size', 'instagram-feed'); ?></label><code class="sbi_shortcode"> minnum
                            Eg: minnum=25</code></th>
                    <td>
                        <input name="sb_instagram_minnum" type="number" min="0" max="100" value="<?php echo esc_attr( $sb_instagram_minnum ); ?>" />
                        <span class="sbi_note"><?php _e('Leave at "0" for default', 'instagram-feed'); ?></span>
                        <a class="sbi_tooltip_link" href="JavaScript:void(0);"><?php _e('What does this mean?', 'instagram-feed'); ?></a>
                        <p class="sbi_tooltip"><?php _e("If your feed contains a lot of IG TV posts or your feed is not displaying any posts despite there being posts available on Instagram.com, try increasing this number to 25 or more.", 'instagram-feed'); ?></p>
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row"><label><?php _e('Enqueue JS file in head', 'instagram-feed'); ?></label></th>
                    <td>
                        <input type="checkbox" name="enqueue_js_in_head" id="sb_instagram_enqueue_js_in_head" <?php if($enqueue_js_in_head == true) echo 'checked="checked"' ?> />
                        <a class="sbi_tooltip_link" href="JavaScript:void(0);"><?php _e('What does this mean?', 'instagram-feed'); ?></a>
                        <p class="sbi_tooltip"><?php _e("Check this box if you'd like to enqueue the JavaScript file for the plugin in the head instead of the footer.", 'instagram-feed'); ?></p>
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row"><label><?php _e('Enqueue CSS file with shortcode', 'instagram-feed'); ?></label></th>
                    <td>
                        <input type="checkbox" name="enqueue_css_in_shortcode" id="sb_instagram_enqueue_css_in_shortcode" <?php if($enqueue_css_in_shortcode == true) echo 'checked="checked"' ?> />
                        <a class="sbi_tooltip_link" href="JavaScript:void(0);"><?php _e('What does this mean?', 'instagram-feed'); ?></a>
                        <p class="sbi_tooltip"><?php _e("Check this box if you'd like to only include the CSS file for the plugin when the feed is on the page.", 'instagram-feed'); ?></p>
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row"><label><?php _e('Disable JS Image Loading', 'instagram-feed'); ?></label></th>
                    <td>
                        <input type="checkbox" name="disable_js_image_loading" id="sb_instagram_disable_js_image_loading" <?php if($disable_js_image_loading == true) echo 'checked="checked"' ?> />
                        <a class="sbi_tooltip_link" href="JavaScript:void(0);"><?php _e('What does this mean?', 'instagram-feed'); ?></a>
                        <p class="sbi_tooltip"><?php _e("Check this box to have images loaded server side instead of with JS.", 'instagram-feed'); ?></p>
                    </td>
                </tr>

				<tr valign="top">
					<th><label><?php _e("Enable Backup Caching", 'instagram-feed'); ?></label></th>
					<td class="sbi-customize-tab-opt">
						<input name="sb_instagram_backup" type="checkbox" id="sb_instagram_backup" <?php if($sb_instagram_backup == true) echo "checked"; ?> />
						<input id="sbi_clear_backups" class="button-secondary" type="submit" style="position: relative; top: -4px;" value="<?php esc_attr_e( 'Clear Backup Cache', 'instagram-feed' ); ?>" />
						<a class="sbi_tooltip_link" href="JavaScript:void(0);"><?php _e('What does this mean?', 'instagram-feed'); ?></a>
						<p class="sbi_tooltip"><?php _e('Every feed will save a duplicate version of itself in the database to be used if the normal cache is not available.', 'instagram-feed'); ?></p>
					</td>
				</tr>

                <tr>
                    <th class="bump-left">
                        <label class="bump-left"><?php _e("Load initial posts with AJAX", 'instagram-feed'); ?></label>
                    </th>
                    <td>
                        <input name="sb_ajax_initial" type="checkbox" id="sb_ajax_initial" <?php if($sb_ajax_initial == true) echo "checked"; ?> />
                        <a class="sbi_tooltip_link" href="JavaScript:void(0);"><?php _e('What does this mean?', 'instagram-feed'); ?></a>
                        <p class="sbi_tooltip"><?php _e("Initial posts will be loaded using AJAX instead of added to the page directly. If you use page caching, this will allow the feed to update according to the \"Check for new posts every\" setting on the \"Configure\" tab.", 'instagram-feed'); ?></p>
                    </td>
                </tr>

				<tr>
					<th class="bump-left">
						<label for="sb_instagram_cron" class="bump-left"><?php _e("Force cache to clear on interval", 'instagram-feed'); ?></label>
					</th>
					<td>
						<select name="sb_instagram_cron">
							<option value="unset" <?php if($sb_instagram_cron == "unset") echo 'selected="selected"' ?> > - </option>
							<option value="yes" <?php if($sb_instagram_cron == "yes") echo 'selected="selected"' ?> ><?php _e('Yes', 'instagram-feed'); ?></option>
							<option value="no" <?php if($sb_instagram_cron == "no") echo 'selected="selected"' ?> ><?php _e('No', 'instagram-feed'); ?></option>
						</select>

						<a class="sbi_tooltip_link" href="JavaScript:void(0);"><?php _e('What does this mean?', 'instagram-feed'); ?></a>
						<p class="sbi_tooltip"><?php _e("If you're experiencing an issue with the plugin not auto-updating then you can set this to 'Yes' to run a scheduled event behind the scenes which forces the plugin cache to clear on a regular basis and retrieve new data from Instagram.", 'instagram-feed'); ?></p>
					</td>
				</tr>
				</tbody>
			</table>
			<table class="form-table">
				<tbody>
				<tr valign="top">
					<th scope="row"><label><?php _e("Disable Icon Font", 'instagram-feed'); ?></label></th>
					<td>
						<input type="checkbox" name="sb_instagram_disable_awesome" id="sb_instagram_disable_awesome" <?php if($sb_instagram_disable_awesome == true) echo 'checked="checked"' ?> /> <?php _e( 'Yes', 'instagram-feed' ); ?>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="sbi_font_method"><?php _e("Icon Method", 'instagram-feed'); ?></label></th>
					<td>
						<select name="sbi_font_method" id="sbi_font_method" class="default-text">
							<option value="svg" id="sbi-font_method" class="default-text" <?php if($sbi_font_method == 'svg') echo 'selected="selected"' ?>>SVG</option>
							<option value="fontfile" id="sbi-font_method" class="default-text" <?php if($sbi_font_method == 'fontfile') echo 'selected="selected"' ?>><?php _e("Font File", 'instagram-feed'); ?></option>
						</select>
						<a class="sbi_tooltip_link" href="JavaScript:void(0);"><?php _e('What does this mean?', 'instagram-feed'); ?></a>
						<p class="sbi_tooltip"><?php _e("This plugin uses SVGs for all icons in the feed. Use this setting to switch to font icons.", 'instagram-feed'); ?></p>
					</td>
				</tr>
                <tr>
                    <th class="bump-left"><label class="bump-left"><?php _e("Enable Custom Templates", 'instagram-feed'); ?></label></th>
                    <td>
                        <input name="sb_instagram_custom_template" type="checkbox" id="sb_instagram_custom_template" <?php if($sb_instagram_custom_template == true) echo "checked"; ?> />
                        <label for="sb_instagram_custom_template"><?php _e('Yes', 'instagram-feed'); ?></label>
                        <a class="sbi_tooltip_link" href="JavaScript:void(0);"><?php _e('What does this mean?', 'instagram-feed'); ?></a>
                        <p class="sbi_tooltip"><?php _e("The default HTML for the feed can be replaced with custom templates added to your theme's folder. Enable this setting to use these templates. See <a href=\"https://smashballoon.com/guide-to-creating-custom-templates/\" target=\"_blank\">this guide</a>", 'instagram-feed'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th class="bump-left"><label class="bump-left"><?php _e("Disable Admin Error Notice", 'instagram-feed'); ?></label></th>
                    <td>
                        <input name="sb_instagram_disable_admin_notice" type="checkbox" id="sb_instagram_disable_admin_notice" <?php if($sb_instagram_disable_admin_notice == true) echo "checked"; ?> />
                        <label for="sb_instagram_disable_admin_notice"><?php _e('Yes', 'instagram-feed'); ?></label>
                        <a class="sbi_tooltip_link" href="JavaScript:void(0);"><?php _e('What does this mean?', 'instagram-feed'); ?></a>
                        <p class="sbi_tooltip"><?php _e("This will permanently disable the feed error notice that displays in the bottom right corner for admins on the front end of your site.", 'instagram-feed'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th class="bump-left"><label class="bump-left"><?php _e("Feed Issue Email Report", 'instagram-feed'); ?></label></th>
                    <td>
                        <input name="sb_instagram_enable_email_report" type="checkbox" id="sb_instagram_enable_email_report" <?php if($sb_instagram_enable_email_report == 'on') echo "checked"; ?> />
                        <label for="sb_instagram_enable_email_report"><?php _e('Yes', 'instagram-feed'); ?></label>
                        <a class="sbi_tooltip_link" href="JavaScript:void(0);"><?php _e('What does this mean?', 'instagram-feed'); ?></a>
                        <p class="sbi_tooltip"><?php _e("Instagram Feed will send a weekly notification email using your site's wp_mail() function if one or more of your feeds is not updating or is not displaying. If you're not receiving the emails in your inbox, you may need to configure an SMTP service using another plugin like WP Mail SMTP.", 'instagram-feed'); ?></p>

                        <div class="sb_instagram_box" style="display: block;">
                            <div class="sb_instagram_box_setting">
                                <label><?php _e('Schedule Weekly on', 'instagram-feed'); ?></label><br>
								<?php
								$schedule_options = array(
									array(
										'val' => 'monday',
										'label' => __( 'Monday', 'instagram-feed' )
									),
									array(
										'val' => 'tuesday',
										'label' => __( 'Tuesday', 'instagram-feed' )
									),
									array(
										'val' => 'wednesday',
										'label' => __( 'Wednesday', 'instagram-feed' )
									),
									array(
										'val' => 'thursday',
										'label' => __( 'Thursday', 'instagram-feed' )
									),
									array(
										'val' => 'friday',
										'label' => __( 'Friday', 'instagram-feed' )
									),
									array(
										'val' => 'saturday',
										'label' => __( 'Saturday', 'instagram-feed' )
									),
									array(
										'val' => 'sunday',
										'label' => __( 'Sunday', 'instagram-feed' )
									),
								);

								if ( isset( $_GET['flag'] ) ){
									echo '<span id="sbi-goto"></span>';
								}
								?>
                                <select name="sb_instagram_email_notification" id="sb_instagram_email_notification">
									<?php foreach ( $schedule_options as $schedule_option ) : ?>
                                        <option value="<?php echo esc_attr( $schedule_option['val'] ) ; ?>" <?php if ( $schedule_option['val'] === $sb_instagram_email_notification ) { echo 'selected';} ?>><?php echo esc_html( $schedule_option['label'] ) ; ?></option>
									<?php endforeach; ?>
                                </select>
                            </div>
                            <div class="sb_instagram_box_setting">
                                <label><?php _e('Email Recipients', 'instagram-feed'); ?></label><br><input class="regular-text" type="text" name="sb_instagram_email_notification_addresses" value="<?php echo esc_attr( $sb_instagram_email_notification_addresses ); ?>"><span class="sbi_note"><?php _e('separate multiple emails with commas', 'instagram-feed'); ?></span>
                                <br><br><?php _e( 'Emails not working?', 'instagram-feed' ) ?> <a href="https://smashballoon.com/email-report-is-not-in-my-inbox/" target="_blank"><?php _e( 'See our related FAQ', 'instagram-feed' ) ?></a>
                            </div>
                        </div>

                    </td>
                </tr>
				<?php
				$usage_tracking = get_option( 'sbi_usage_tracking', false );
				if ( $usage_tracking !== false ) :

				if ( isset( $_POST['sb_instagram_enable_email_report'] ) ) {
					$usage_tracking['enabled'] = false;
					if ( isset( $_POST['sbi_usage_tracking_enable'] ) ) {
						$usage_tracking['enabled'] = true;
					}
					update_option( 'sbi_usage_tracking', $usage_tracking, false );
				}
				$sbi_usage_tracking_enable = isset( $usage_tracking['enabled'] ) ? $usage_tracking['enabled'] : true;

				// only show this setting after they have opted in or opted out using the admin notice
				?>
                <tr>

                    <th class="bump-left"><label class="bump-left"><?php _e("Enable Usage Tracking", 'instagram-feed'); ?></label></th>
                    <td>
                        <input name="sbi_usage_tracking_enable" type="checkbox" id="sbi_usage_tracking_enable" <?php if( $sbi_usage_tracking_enable ) echo "checked"; ?> />
                        <label for="sbi_usage_tracking_enable"><?php _e('Yes', 'instagram-feed'); ?></label>
                        <a class="sbi_tooltip_link" href="JavaScript:void(0);"><?php _e('What is usage tracking?', 'instagram-feed'); ?></a>
                        <p class="sbi_tooltip"><?php _e("Instagram Feed will record information and statistics about your site in order for the team at Smash Balloon to learn more about how our plugins are used. The plugin will never collect any sensitive information like access tokens, email addresses, or user information.", 'instagram-feed'); ?></p>
                    </td>
                </tr>
                <?php endif; ?>

                </tbody>
			</table>

			<?php submit_button(); ?>

			</form>

			<p><i class="fa fa-chevron-circle-right" aria-hidden="true"></i>&nbsp; <?php _e('Next Step: <a href="?page=sb-instagram-feed&tab=display">Display your Feed</a>', 'instagram-feed'); ?></p>

			<p><i class="fa fa-life-ring" aria-hidden="true"></i>&nbsp; <?php _e('Need help setting up the plugin? Check out our <a href="https://smashballoon.com/instagram-feed/free/?utm_campaign=instagram-free&utm_source=settings&utm_medium=display" target="_blank">setup directions</a>', 'instagram-feed'); ?></p>


		<?php } //End Customize tab ?>



		<?php if( $sbi_active_tab == 'display' ) { //Start Display tab ?>

			<h3><?php _e('Display your Feed', 'instagram-feed'); ?></h3>
			<p><?php _e("Copy and paste the following shortcode directly into the page, post or widget where you'd like the feed to show up:", 'instagram-feed'); ?></p>
			<input type="text" value="[instagram-feed]" size="16" readonly="readonly" style="text-align: center;" onclick="this.focus();this.select()" title="<?php _e('To copy, click the field then press Ctrl + C (PC) or Cmd + C (Mac).', 'instagram-feed'); ?>" />

			<h3 style="padding-top: 10px;"><?php _e( 'Multiple Feeds', 'instagram-feed' ); ?></h3>
			<p><?php _e("If you'd like to display multiple feeds then you can set different settings directly in the shortcode like so:", 'instagram-feed'); ?>
				<code>[instagram-feed num=9 cols=3]</code></p>
			<p><?php _e( 'You can display as many different feeds as you like, on either the same page or on different pages, by just using the shortcode options below. For example:', 'instagram-feed' ); ?><br />
				<code>[instagram-feed]</code><br />
				<code>[instagram-feed num=4 cols=4 showfollow=false]</code><br />
			</p>
			<p><?php _e("See the table below for a full list of available shortcode options:", 'instagram-feed'); ?></p>

			<p><span class="sbi_table_key"></span><?php _e('Pro version only', 'instagram-feed'); ?></p>

			<table class="sbi_shortcode_table">
				<tbody>
				<tr valign="top">
					<th scope="row"><?php _e('Shortcode option', 'instagram-feed'); ?></th>
					<th scope="row"><?php _e('Description', 'instagram-feed'); ?></th>
					<th scope="row"><?php _e('Example', 'instagram-feed'); ?></th>
				</tr>

				<tr class="sbi_table_header"><td colspan=3><?php _e("Configure Options", 'instagram-feed'); ?></td></tr>
				<tr class="sbi_pro">
					<td>type</td>
					<td><?php _e("Display photos from a User ID (user)<br />Display posts from a Hashtag (hashtag)", 'instagram-feed'); ?><br /><?php _e("Display photos that the account was tagged in (tagged)", 'instagram-feed'); ?></td>
					<td><code>[instagram-feed type=user]</code><br /><code>[instagram-feed type=hashtag]</code><br /><code>[instagram-feed type=tagged]</code></td>
				</tr>
				<tr>
					<td>user</td>
					<td><?php _e('Your Instagram User Name. This must be from a connected account on the "Configure" tab.', 'instagram-feed'); ?></td>
					<td><code>[instagram-feed user="smashballoon"]</code></td>
				</tr>
				<tr class="sbi_pro">
					<td>hashtag</td>
					<td><?php _e('Any hashtag. Separate multiple hashtags by commas.', 'instagram-feed'); ?></td>
					<td><code>[instagram-feed hashtag="#awesome"]</code></td>
				</tr>
                <tr class="sbi_pro">
                    <td>tagged</td>
                    <td><?php _e('Your Instagram User Name. Separate multiple users by commas.', 'instagram-feed'); ?></td>
                    <td><code>[instagram-feed tagged="smashballoon"]</code></td>
                </tr>

				<tr class="sbi_table_header"><td colspan=3><?php _e("Customize Options", 'instagram-feed'); ?></td></tr>
				<tr>
					<td>width</td>
					<td><?php _e("The width of your feed. Any number.", 'instagram-feed'); ?></td>
					<td><code>[instagram-feed width=50]</code></td>
				</tr>
				<tr>
					<td>widthunit</td>
					<td><?php _e("The unit of the width. 'px' or '%'", 'instagram-feed'); ?></td>
					<td><code>[instagram-feed widthunit=%]</code></td>
				</tr>
				<tr>
					<td>height</td>
					<td><?php _e("The height of your feed. Any number.", 'instagram-feed'); ?></td>
					<td><code>[instagram-feed height=250]</code></td>
				</tr>
				<tr>
					<td>heightunit</td>
					<td><?php _e("The unit of the height. 'px' or '%'", 'instagram-feed'); ?></td>
					<td><code>[instagram-feed heightunit=px]</code></td>
				</tr>
				<tr>
					<td>background</td>
					<td><?php _e("The background color of the feed. Any hex color code.", 'instagram-feed'); ?></td>
					<td><code>[instagram-feed background=#ffff00]</code></td>
				</tr>
				<tr>
					<td>class</td>
					<td><?php _e("Add a CSS class to the feed container", 'instagram-feed'); ?></td>
					<td><code>[instagram-feed class=feedOne]</code></td>
				</tr>

				<tr class="sbi_table_header"><td colspan=3><?php _e("Layout Options", 'instagram-feed'); ?></td></tr>
				<tr class="sbi_pro">
					<td>layout</td>
					<td><?php _e("How posts are arranged visually in the feed. There are four layouts: Grid, Carousel Slider, Masonry Grid, or Highlight Grid.  Options:", 'instagram-feed' ); ?> 'grid', 'carousel', 'masonry', or 'highlight'</td>
					<td><code>[instagram-feed layout=grid]</code></td>
				</tr>
				<tr>
					<td>num</td>
					<td><?php _e("The number of photos to display initially. Maximum is 33.", 'instagram-feed'); ?></td>
					<td><code>[instagram-feed num=10]</code></td>
				</tr>
				<tr class="sbi_pro">
					<td>nummobile</td>
					<td><?php _e("The number of photos to display initially for mobile screens (smaller than 480 pixels).", 'instagram-feed'); ?></td>
					<td><code>[instagram-feed nummobile=6]</code></td>
				</tr>
				<tr>
					<td>cols</td>
					<td><?php _e("The number of columns in your feed. 1 - 10.", 'instagram-feed'); ?></td>
					<td><code>[instagram-feed cols=5]</code></td>
				</tr>
				<tr class="sbi_pro">
					<td>colsmobile</td>
					<td><?php _e("The number of columns in your feed for mobile screens (smaller than 480 pixels).", 'instagram-feed'); ?></td>
					<td><code>[instagram-feed colsmobile=2]</code></td>
				</tr>
				<tr>
					<td>imagepadding</td>
					<td><?php _e("The spacing around your photos", 'instagram-feed'); ?></td>
					<td><code>[instagram-feed imagepadding=10]</code></td>
				</tr>
				<tr>
					<td>imagepaddingunit</td>
					<td><?php _e("The unit of the padding. 'px' or '%'", 'instagram-feed'); ?></td>
					<td><code>[instagram-feed imagepaddingunit=px]</code></td>
				</tr>

				<tr class="sbi_table_header"><td colspan=3><?php _e("Carousel Options", 'instagram-feed'); ?></td></tr>
				<tr class="sbi_pro">
					<td>carouselrows</td>
					<td><?php _e("Choose 1 or 2 rows of posts in the carousel", 'instagram-feed'); ?></td>
					<td><code>[instagram-feed carouselrows=1]</code></td>
				</tr>
				<tr class="sbi_pro">
					<td>carouselloop</td>
					<td><?php _e("Infinitely loop through posts or rewind", 'instagram-feed'); ?></td>
					<td><code>[instagram-feed carouselloop=rewind]</code></td>
				</tr>
				<tr class="sbi_pro">
					<td>carouselarrows</td>
					<td><?php _e("Display directional arrows on the carousel", 'instagram-feed'); ?></td>
					<td><code>[instagram-feed carouselarrows=true]</code></td>
				</tr>
				<tr class="sbi_pro">
					<td>carouselpag</td>
					<td><?php _e("Display pagination links below the carousel", 'instagram-feed'); ?></td>
					<td><code>[instagram-feed carouselpag=true]</code></td>
				</tr>
				<tr class="sbi_pro">
					<td>carouselautoplay</td>
					<td><?php _e("Make the carousel autoplay", 'instagram-feed'); ?></td>
					<td><code>[instagram-feed carouselautoplay=true]</code></td>
				</tr>
				<tr class="sbi_pro">
					<td>carouseltime</td>
					<td><?php _e("The interval time between slides for autoplay. Time in miliseconds.", 'instagram-feed'); ?></td>
					<td><code>[instagram-feed carouseltime=8000]</code></td>
				</tr>

				<tr class="sbi_table_header"><td colspan=3><?php _e("Highlight Options", 'instagram-feed'); ?></td></tr>
				<tr class="sbi_pro">
					<td>highlighttype</td>
					<td><?php _e("Choose from 3 different ways of highlighting posts including by pattern, hashtag, post id or. Options:", 'instagram-feed'); ?> 'pattern', 'hashtag', 'id'.</td>
					<td><code>[instagram-feed highlighttype=hashtag]</code></td>
				</tr>
				<tr class="sbi_pro">
					<td>highlightpattern</td>
					<td><?php _e("How often a post is highlighted.", 'instagram-feed'); ?></td>
					<td><code>[instagram-feed highlightpattern=7]</code></td>
				</tr>
				<tr class="sbi_pro">
					<td>highlightoffset</td>
					<td><?php _e("When to start the highlight pattern.", 'instagram-feed'); ?></td>
					<td><code>[instagram-feed highlightoffset=3]</code></td>
				</tr>
				<tr class="sbi_pro">
					<td>highlighthashtag</td>
					<td><?php _e("Highlight posts with these hashtags.", 'instagram-feed'); ?></td>
					<td><code>[instagram-feed highlighthashtag=best]</code></td>
				</tr>





				<tr class="sbi_table_header"><td colspan=3><?php _e("Photos Options", 'instagram-feed'); ?></td></tr>
				<tr>
					<td>sortby</td>
					<td><?php _e("Sort the posts by Newest to Oldest (none) or Random (random)", 'instagram-feed'); ?></td>
					<td><code>[instagram-feed sortby=random]</code></td>
				</tr>
				<tr>
					<td>imageres</td>
					<td><?php _e("The resolution/size of the photos including full, medium, thumbnail, and auto (based on size of image on page). Options:", 'instagram-feed'); ?> 'auto', full', 'medium' or 'thumb'.</td>
					<td><code>[instagram-feed imageres=full]</code></td>
				</tr>
				<tr class="sbi_pro">
					<td>media</td>
					<td><?php _e("Display all media, only photos, or only videos", 'instagram-feed'); ?></td>
					<td><code>[instagram-feed media=photos]</code></td>
				</tr>
				<tr class="sbi_pro">
					<td>disablelightbox</td>
					<td><?php _e("Whether to disable the photo Lightbox. It is enabled by default.", 'instagram-feed'); ?></td>
					<td><code>[instagram-feed disablelightbox=true]</code></td>
				</tr>
				<tr>
					<td>disablemobile</td>
					<td><?php _e("Disable the mobile layout. Options:", 'instagram-feed'); ?> 'true' or 'false'.</td>
					<td><code>[instagram-feed disablemobile=true]</code></td>
				</tr>
				<tr class="sbi_pro">
					<td>captionlinks</td>
					<td><?php _e("Whether to use urls in captions for the photo's link instead of linking to instagram.com.", 'instagram-feed'); ?></td>
					<td><code>[instagram-feed captionlinks=true]</code></td>
				</tr>

				<tr class="sbi_table_header"><td colspan=3><?php _e("Lightbox Comments Options", 'instagram-feed'); ?></td></tr>
				<tr class="sbi_pro">
					<td>lightboxcomments</td>
					<td><?php _e("Whether to show comments in the lightbox for this feed.", 'instagram-feed'); ?></td>
					<td><code>[instagram-feed lightboxcomments=true]</code></td>
				</tr>
				<tr class="sbi_pro">
					<td>numcomments</td>
					<td><?php _e("Number of comments to show starting from the most recent.", 'instagram-feed'); ?></td>
					<td><code>[instagram-feed numcomments=10]</code></td>
				</tr>

				<tr class="sbi_table_header"><td colspan=3><?php _e("Photos Hover Style Options", 'instagram-feed'); ?></td></tr>
				<tr class="sbi_pro">
					<td>hovercolor</td>
					<td><?php _e("The background color when hovering over a photo. Any hex color code.", 'instagram-feed'); ?></td>
					<td><code>[instagram-feed hovercolor=#ff0000]</code></td>
				</tr>
				<tr class="sbi_pro">
					<td>hovertextcolor</td>
					<td><?php _e("The text/icon color when hovering over a photo. Any hex color code.", 'instagram-feed'); ?></td>
					<td><code>[instagram-feed hovertextcolor=#fff]</code></td>
				</tr>
				<tr class="sbi_pro">
					<td>hoverdisplay</td>
					<td><?php _e("The info to display when hovering over the photo such as the user name, post date, Instagram icon, location, caption, and like counts. Options:", 'instagram-feed'); ?><br />username, date, instagram, location, caption, likes</td>
					<td><code>[instagram-feed hoverdisplay="date, location, likes"]</code></td>
				</tr>

				<tr class="sbi_table_header"><td colspan=3><?php _e("Header Options", 'instagram-feed'); ?></td></tr>
				<tr>
					<td>showheader</td>
					<td><?php _e("Whether to show the feed Header. Options:", 'instagram-feed'); ?> 'true' or 'false'.</td>
					<td><code>[instagram-feed showheader=false]</code></td>
				</tr>
				<tr>
					<td>showbio</td>
					<td><?php _e("Display the bio in the header. Options:", 'instagram-feed'); ?> 'true' or 'false'</td>
					<td><code>[instagram-feed showbio=true]</code></td>
				</tr>
                <tr>
                    <td>custombio</td>
                    <td><?php _e("Display a custom bio in the header", 'instagram-feed'); ?></td>
                    <td><code>[instagram-feed custombio="My custom bio."]</code></td>
                </tr>
                <tr>
                    <td>customavatar</td>
                    <td><?php _e("Display a custom avatar in the header. Enter the full URL of an image file.", 'instagram-feed'); ?></td>
                    <td><code>[instagram-feed customavatar="https://example.com/avatar.jpg"]</code></td>
                </tr>
				<tr>
					<td>headersize</td>
					<td><?php _e("Size of the header including small, medium and large. Options:", 'instagram-feed'); ?> small, medium, or large.</td>
					<td><code>[instagram-feed headersize=medium]</code></td>
				</tr>
				<tr>
					<td>headercolor</td>
					<td><?php _e("The color of the Header text. Any hex color code.", 'instagram-feed'); ?></td>
					<td><code>[instagram-feed headercolor=#333]</code></td>
				</tr>

				<tr class="sbi_table_header"><td colspan=3><?php _e("'Load More' Button Options", 'instagram-feed'); ?></td></tr>
				<tr>
					<td>showbutton</td>
					<td><?php _e("Whether to show the 'Load More' button. Options:", 'instagram-feed'); ?> 'true' or 'false'.</td>
					<td><code>[instagram-feed showbutton=false]</code></td>
				</tr>
				<tr>
					<td>buttoncolor</td>
					<td><?php _e("The background color of the button. Any hex color code.", 'instagram-feed'); ?></td>
					<td><code>[instagram-feed buttoncolor=#000]</code></td>
				</tr>
				<tr>
					<td>buttontextcolor</td>
					<td><?php _e("The text color of the button. Any hex color code.", 'instagram-feed'); ?></td>
					<td><code>[instagram-feed buttontextcolor=#fff]</code></td>
				</tr>
				<tr>
					<td>buttontext</td>
					<td><?php _e("The text used for the button.", 'instagram-feed'); ?></td>
					<td><code>[instagram-feed buttontext="Load More Photos"]</code></td>
				</tr>

				<tr class="sbi_table_header"><td colspan=3><?php _e("'Follow on Instagram' Button Options", 'instagram-feed'); ?></td></tr>
				<tr>
					<td>showfollow</td>
					<td><?php _e("Whether to show the 'Follow on Instagram' button. Options:", 'instagram-feed'); ?> 'true' or 'false'.</td>
					<td><code>[instagram-feed showfollow=false]</code></td>
				</tr>
				<tr>
					<td>followcolor</td>
					<td><?php _e("The background color of the button. Any hex color code.", 'instagram-feed'); ?></td>
					<td><code>[instagram-feed followcolor=#ff0000]</code></td>
				</tr>
				<tr>
					<td>followtextcolor</td>
					<td><?php _e("The text color of the button. Any hex color code.", 'instagram-feed'); ?></td>
					<td><code>[instagram-feed followtextcolor=#fff]</code></td>
				</tr>
				<tr>
					<td>followtext</td>
					<td><?php _e("The text used for the button.", 'instagram-feed'); ?></td>
					<td><code>[instagram-feed followtext="Follow me"]</code></td>
				</tr>

				<tr class="sbi_table_header"><td colspan=3><?php _e("Caption Options", 'instagram-feed'); ?></td></tr>
				<tr class="sbi_pro">
					<td>showcaption</td>
					<td><?php _e("Whether to show the photo caption. Options:", 'instagram-feed'); ?> 'true' or 'false'.</td>
					<td><code>[instagram-feed showcaption=false]</code></td>
				</tr>
				<tr class="sbi_pro">
					<td>captionlength</td>
					<td><?php _e("The number of characters of the caption to display", 'instagram-feed'); ?></td>
					<td><code>[instagram-feed captionlength=50]</code></td>
				</tr>
				<tr class="sbi_pro">
					<td>captioncolor</td>
					<td><?php _e("The text color of the caption. Any hex color code.", 'instagram-feed'); ?></td>
					<td><code>[instagram-feed captioncolor=#000]</code></td>
				</tr>
				<tr class="sbi_pro">
					<td>captionsize</td>
					<td><?php _e("The size of the caption text. Any number.", 'instagram-feed'); ?></td>
					<td><code>[instagram-feed captionsize=24]</code></td>
				</tr>

				<tr class="sbi_table_header"><td colspan=3><?php _e("Likes &amp; Comments Options", 'instagram-feed'); ?></td></tr>
				<tr class="sbi_pro">
					<td>showlikes</td>
					<td><?php _e("Whether to show the Likes &amp; Comments. Options:", 'instagram-feed'); ?> 'true' or 'false'.</td>
					<td><code>[instagram-feed showlikes=false]</code></td>
				</tr>
				<tr class="sbi_pro">
					<td>likescolor</td>
					<td><?php _e("The color of the Likes &amp; Comments. Any hex color code.", 'instagram-feed'); ?></td>
					<td><code>[instagram-feed likescolor=#FF0000]</code></td>
				</tr>
				<tr class="sbi_pro">
					<td>likessize</td>
					<td><?php _e("The size of the Likes &amp; Comments. Any number.", 'instagram-feed'); ?></td>
					<td><code>[instagram-feed likessize=14]</code></td>
				</tr>

				<tr class="sbi_table_header"><td colspan=3><?php _e("Post Filtering Options", 'instagram-feed'); ?></td></tr>
				<tr class="sbi_pro">
					<td>excludewords</td>
					<td><?php _e("Remove posts which contain certain words or hashtags in the caption.", 'instagram-feed'); ?></td>
					<td><code>[instagram-feed excludewords="bad, words"]</code></td>
				</tr>
				<tr class="sbi_pro">
					<td>includewords</td>
					<td><?php _e("Only display posts which contain certain words or hashtags in the caption.", 'instagram-feed'); ?></td>
					<td><code>[instagram-feed includewords="sunshine"]</code></td>
				</tr>

				<tr class="sbi_table_header"><td colspan=3><?php _e("Auto Load More on Scroll", 'instagram-feed'); ?></td></tr>
				<tr class="sbi_pro">
					<td>autoscroll</td>
					<td><?php _e("Load more posts automatically as the user scrolls down the page.", 'instagram-feed'); ?></td>
					<td><code>[instagram-feed autoscroll=true]</code></td>
				</tr>
				<tr class="sbi_pro">
					<td>autoscrolldistance</td>
					<td><?php _e("Distance before the end of feed or page that triggers the loading of more posts.", 'instagram-feed'); ?></td>
					<td><code>[instagram-feed autoscrolldistance=200]</code></td>
				</tr>

				</tbody>
			</table>

			<p><i class="fa fa-life-ring" aria-hidden="true"></i>&nbsp; <?php _e('Need help setting up the plugin? Check out our <a href="https://smashballoon.com/instagram-feed/free/?utm_campaign=instagram-free&utm_source=display&utm_medium=supportsetup" target="_blank">setup directions</a>', 'instagram-feed'); ?></p>

		<?php } //End Display tab ?>


		<?php if( $sbi_active_tab == 'support' ) { //Start Support tab ?>

			<div class="sbi_support">

				<br/>
				<h3 style="padding-bottom: 10px;"><?php _e("Need help?", 'instagram-feed'); ?></h3>

				<p>
			    <span class="sbi-support-title"><i class="fa fa-life-ring" aria-hidden="true"></i>&nbsp; <a
					    href="https://smashballoon.com/instagram-feed/free/?utm_campaign=instagram-free&utm_source=settings&utm_medium=general"
					    target="_blank"><?php _e( 'Setup Directions', 'instagram-feed' ); ?></a></span>
					<?php _e( 'A step-by-step guide on how to setup and use the plugin.', 'instagram-feed' ); ?>
				</p>

				<p>
			    <span class="sbi-support-title"><i class="fa fa-youtube-play" aria-hidden="true"></i>&nbsp; <a
					    href="https://www.youtube.com/embed/q6ZXVU4g970" target="_blank"
					    id="sbi-play-support-video"><?php _e( 'Watch a Video', 'instagram-feed' ); ?></a></span>
					<?php _e( "Watch a short video demonstrating how to set up, customize and use the plugin.<br /><b>Please note</b> that the video shows the set up and use of the <b><a href='https://smashballoon.com/instagram-feed/?utm_campaign=instagram-free&utm_source=settings&utm_medium=general' target='_blank'>Pro version</a></b> of the plugin, but the process is the same for this free version. The only difference is some of the features available.", 'instagram-feed' ); ?>

					<iframe id="sbi-support-video"
					        src="//www.youtube.com/embed/q6ZXVU4g970?theme=light&amp;showinfo=0&amp;controls=2" width="960"
					        height="540" frameborder="0" allowfullscreen="allowfullscreen"></iframe>
				</p>

				<p>
			    <span class="sbi-support-title"><i class="fa fa-question-circle" aria-hidden="true"></i>&nbsp; <a
					    href="https://smashballoon.com/instagram-feed/support/faq/?utm_campaign=instagram-free&utm_source=support&utm_medium=faqs"
					    target="_blank"><?php _e( 'FAQs and Docs', 'instagram-feed' ); ?></a></span>
					<?php _e( 'View our expansive library of FAQs and documentation to help solve your problem as quickly as possible.', 'instagram-feed' ); ?>
				</p>

				<div class="sbi-support-faqs">

					<ul>
						<li><b><?php _e( 'FAQs', 'instagram-feed' ); ?></b></li>
						<li>&bull;&nbsp; <?php _e( '<a href="https://smashballoon.com/my-photos-wont-load/?utm_campaign=instagram-free&utm_source=support&utm_medium=faqsnophotos" target="_blank">My Instagram Feed Won\'t Load</a>', 'instagram-feed' ); ?></li>
						<li>&bull;&nbsp; <?php _e( '<a href="https://smashballoon.com/my-instagram-access-token-keep-expiring/?utm_campaign=instagram-free&utm_source=support&utm_medium=faqsexpiring" target="_blank">My Access Token Keeps Expiring</a>', 'instagram-feed' ); ?></li>
						<li style="margin-top: 8px; font-size: 12px;"><a href="https://smashballoon.com/instagram-feed/support/faq/?utm_campaign=instagram-free&utm_source=support&utm_medium=faqs" target="_blank"><?php _e( 'See All', 'instagram-feed' ); ?><i class="fa fa-chevron-right" aria-hidden="true"></i></a></li>
					</ul>

					<ul>
						<li><b><?php _e("Documentation", 'instagram-feed'); ?></b></li>
						<li>&bull;&nbsp; <?php _e( '<a href="https://smashballoon.com/instagram-feed/free?utm_campaign=instagram-free&utm_source=support&utm_medium=installation" target="_blank">Installation and Configuration</a>', 'instagram-feed' ); ?></li>
						<li>&bull;&nbsp; <?php _e( '<a href="https://smashballoon.com/display-multiple-instagram-feeds/?utm_campaign=instagram-free&utm_source=support&utm_medium=multiple" target="_blank">Displaying multiple feeds</a>', 'instagram-feed' ); ?></li>
						<li>&bull;&nbsp; <?php _e( '<a href="https://smashballoon.com/instagram-feed-faq/customization/?utm_campaign=instagram-free&utm_source=support&utm_medium=customizing" target="_blank">Customizing your Feed</a>', 'instagram-feed' ); ?></li>
					</ul>
				</div>

				<p>
			    <span class="sbi-support-title"><i class="fa fa-envelope" aria-hidden="true"></i>&nbsp; <a
					    href="https://smashballoon.com/instagram-feed/support/?utm_campaign=instagram-free&utm_source=support&utm_medium=contact"
					    target="_blank"><?php _e( 'Request Support', 'instagram-feed' ); ?></a></span>
					<?php _e( 'Still need help? Submit a ticket and one of our support experts will get back to you as soon as possible.<br /><b>Important:</b> Please include your <b>System Info</b> below with all support requests.', 'instagram-feed' ); ?>
				</p>
			</div>

			<hr />

			<h3><?php _e('System Info &nbsp; <i style="color: #666; font-size: 11px; font-weight: normal;">Click the text below to select all</i>', 'instagram-feed'); ?></h3>




			<?php $sbi_options = get_option('sb_instagram_settings'); ?>
			<textarea readonly="readonly" onclick="this.focus();this.select()" title="To copy, click the field then press Ctrl + C (PC) or Cmd + C (Mac)." style="width: 100%; max-width: 960px; height: 500px; white-space: pre; font-family: Menlo,Monaco,monospace;">
## SITE/SERVER INFO: ##
Site URL:                 <?php echo site_url() . "\n"; ?>
Home URL:                 <?php echo home_url() . "\n"; ?>
WordPress Version:        <?php echo get_bloginfo( 'version' ) . "\n"; ?>
PHP Version:              <?php echo PHP_VERSION . "\n"; ?>
Web Server Info:          <?php echo $_SERVER['SERVER_SOFTWARE'] . "\n"; ?>

## ACTIVE PLUGINS: ##
<?php
$plugins = get_plugins();
$active_plugins = get_option( 'active_plugins', array() );

foreach ( $plugins as $plugin_path => $plugin ) {
	// If the plugin isn't active, don't show it.
	if ( ! in_array( $plugin_path, $active_plugins ) )
		continue;

	echo $plugin['Name'] . ': ' . $plugin['Version'] ."\n";
}
?>

## PLUGIN SETTINGS: ##
sb_instagram_plugin_type => Instagram Feed Free
<?php
global $wpdb;
foreach( $sbi_options as $key => $val ) {
	if ( $key !== 'connected_accounts' ) {
		if ( is_array( $val ) ) {
			foreach ( $val as $item ) {
				if ( is_array( $item ) ) {
					foreach ( $item as $key2 => $val2 ) {
						echo "$key2 => $val2\n";
					}
				} else {
					echo "$key => $item\n";
				}
			}
		} else {
			echo "$key => $val\n";
		}
	}

}
?>

## CONNECTED ACCOUNTS: ##<?php echo "\n";
				$con_accounts = isset( $sbi_options['connected_accounts'] ) ? $sbi_options['connected_accounts'] : array();
				$business_accounts = array();
				$basic_accounts = array();
				if ( ! empty( $con_accounts ) ) {
					foreach ( $con_accounts as $account ) {
						$type = isset( $account['type'] ) ? $account['type'] : 'personal';

						if ( $type === 'business' ) {
							$business_accounts[] = $account;
						} elseif ( $type === 'basic' ) {
							$basic_accounts[] = $account;
						}
						echo '*' . $account['user_id'] . '*' . "\n";
						var_export( $account );
						echo "\n";
					}
				}
				?>

## API RESPONSE: ##
<?php
$first_con_basic_account = isset( $basic_accounts[0] ) ? $basic_accounts[0] : array();
$first_con_business_account = isset( $business_accounts[0] ) ? $business_accounts[0] : array();

if ( ! empty( $first_con_basic_account ) ) {
	echo '*BASIC ACCOUNT*';
	echo "\n";
	$connection = new SB_Instagram_API_Connect( $first_con_basic_account, 'header' );
	$connection->connect();
	if ( ! $connection->is_wp_error() && ! $connection->is_instagram_error() ) {
		foreach ( $connection->get_data() as $key => $item ) {
			if ( is_array ( $item ) ) {
				foreach ( $item as $key2 => $item2 ) {
					echo $key2 . ' => ' . esc_html( $item2 ) . "\n";
				}
			} else {
				echo $key . ' => ' . esc_html( $item ) . "\n";
			}
		}
	} else {
		if ( $connection->is_wp_error() ) {
			$response = $connection->get_wp_error();
			if ( isset( $response ) && isset( $response->errors ) ) {
				foreach ( $response->errors as $key => $item ) {
					echo $key . ' => ' . $item[0] . "\n";
				}
			}
		} else {
			$error = $connection->get_data();
			var_export( $error );
		}
	}
	echo "\n";
} else {
	echo 'no connected basic accounts';
	echo "\n";
}
if ( ! empty( $first_con_business_account ) ) {
	echo '*BUSINESS ACCOUNT*';
	echo "\n";
	$connection = new SB_Instagram_API_Connect( $first_con_business_account, 'header' );
	$connection->connect();
	if ( ! $connection->is_wp_error() && ! $connection->is_instagram_error() ) {
		foreach ( $connection->get_data() as $key => $item ) {
			if ( is_array ( $item ) ) {
				foreach ( $item as $key2 => $item2 ) {
					echo $key2 . ' => ' . esc_html( $item2 ) . "\n";
				}
			} else {
				echo $key . ' => ' . esc_html( $item ) . "\n";
			}
		}
	} else {
		if ( $connection->is_wp_error() ) {
			$response = $connection->get_wp_error();
			if ( isset( $response ) && isset( $response->errors ) ) {
				foreach ( $response->errors as $key => $item ) {
					echo $key . ' => ' . $item[0] . "\n";
				}
			}
		} else {
			$error = $connection->get_data();
			var_export( $error );
		}
	}
} else {
	echo 'no connected business accounts';
} ?>

## Cron Events: ##
<?php
$cron = _get_cron_array();
foreach ( $cron as $key => $data ) {
	$is_target = false;
	foreach ( $data as $key2 => $val ) {
		if ( strpos( $key2, 'sbi' ) !== false || strpos( $key2, 'sb_instagram' ) !== false ) {
			$is_target = true;
			echo $key2;
			echo "\n";
		}
	}
	if ( $is_target) {
		echo date( "Y-m-d H:i:s", $key );
		echo "\n";
		echo 'Next Scheduled: ' . ((int)$key - time())/60 . ' minutes';
		echo "\n\n";
	}
}
?>
## Cron Cache Report: ##
<?php $cron_report = get_option( 'sbi_cron_report', array() );
if ( ! empty( $cron_report ) ) {
	var_export( $cron_report );
}
echo "\n";
?>

## Access Token Refresh: ##
<?php $cron_report = get_option( 'sbi_refresh_report', array() );
if ( ! empty( $cron_report ) ) {
	var_export( $cron_report );
}
echo "\n";
?>

## Resizing: ##
<?php $upload     = wp_upload_dir();
$upload_dir = $upload['basedir'];
$upload_dir = trailingslashit( $upload_dir ) . SBI_UPLOADS_NAME;
if ( file_exists( $upload_dir ) ) {
	echo 'upload directory exists';
} else {
	$created = wp_mkdir_p( $upload_dir );

	if ( ! $created ) {
		echo 'cannot create upload directory';
	}
}
echo "\n";
echo "\n";

$table_name      = esc_sql( $wpdb->prefix . SBI_INSTAGRAM_POSTS_TYPE );
$feeds_posts_table_name = esc_sql( $wpdb->prefix . SBI_INSTAGRAM_FEEDS_POSTS );

if ( $wpdb->get_var( "show tables like '$feeds_posts_table_name'" ) != $feeds_posts_table_name ) {
	echo 'no feeds posts table';
	echo "\n";

} else {
	$last_result = $wpdb->get_results( "SELECT * FROM $feeds_posts_table_name ORDER BY id DESC LIMIT 1;" );
	if ( is_array( $last_result ) && isset( $last_result[0] ) ) {
		echo '*FEEDS POSTS TABLE*';
		echo "\n";

		foreach ( $last_result as $column ) {

			foreach ( $column as $key => $value ) {
				echo $key . ': ' . esc_html( $value ) . "\n";;
			}
		}

	} else {
		echo 'feeds posts has no rows';
		echo "\n";
	}
}
echo "\n";

if ( $wpdb->get_var( "show tables like '$table_name'" ) != $table_name ) {
	echo 'no posts table';
	echo "\n";

} else {


	$last_result = $wpdb->get_results( "SELECT * FROM $table_name ORDER BY id DESC LIMIT 1;" );
	if ( is_array( $last_result ) && isset( $last_result[0] ) ) {
		echo '*POSTS TABLE*';
		echo "\n";
		foreach ( $last_result as $column ) {

			foreach ( $column as $key => $value ) {
				echo $key . ': ' . esc_html( $value ) . "\n";;
			}
		}

	} else {
		echo 'feeds posts has no rows';
		echo "\n";
	}
}

?>

## Error Log: ##
<?php
global $sb_instagram_posts_manager;
$errors = $sb_instagram_posts_manager->get_errors();
if ( ! empty( $errors ) ) :
	foreach ( $errors as $type => $error ) :
		echo $type . ': ' . $error[1] . "\n";
	endforeach;
endif;
$error_page = $sb_instagram_posts_manager->get_error_page();
if ( $error_page ) {
	echo 'Feed with error: ' . esc_url( get_the_permalink( $error_page ) ). "\n";
}
$ajax_statuses = $sb_instagram_posts_manager->get_ajax_status();
if ( ! $ajax_statuses['successful'] ) {
	?>
## AJAX Status ##
	<?php
	echo 'test not successful';
}
?>
</textarea>
            <div><input id="sbi_reset_log" class="button-secondary" type="submit" value="<?php esc_attr_e( 'Reset Error Log' ); ?>" style="vertical-align: middle;"/></div>

			<?php
		} //End Support tab
		?>


		<div class="sbi_quickstart">
			<h3><i class="fa fa-rocket" aria-hidden="true"></i>&nbsp; <?php _e('Display your feed', 'instagram-feed'); ?></h3>
			<p><?php _e('Copy and paste this shortcode directly into the page, post or widget where you\'d like to display the feed:', 'instagram-feed'); ?>        <input type="text" value="[instagram-feed]" size="15" readonly="readonly" style="text-align: center;" onclick="this.focus();this.select()" title="To copy, click the field then press Ctrl + C (PC) or Cmd + C (Mac)."></p>
			<p><?php _e('Find out how to display <a href="?page=sb-instagram-feed&amp;tab=display">multiple feeds</a>.', 'instagram-feed'); ?></p>
		</div>

		<a href="https://smashballoon.com/instagram-feed/demo/?utm_campaign=instagram-free&utm_source=footer&utm_medium=ad" target="_blank" class="sbi-pro-notice">
			<img src="<?php echo SBI_PLUGIN_URL . 'img/instagram-pro-promo.png?2019'; ?>" alt="<?php esc_attr_e( 'Instagram Feed Pro', 'instagram-feed' ); ?>">
		</a>

		<p class="sbi_plugins_promo dashicons-before dashicons-admin-plugins"> <?php _e('Check out our other free plugins: <a href="https://wordpress.org/plugins/custom-facebook-feed/" target="_blank">Facebook</a>, <a href="https://wordpress.org/plugins/custom-twitter-feeds/" target="_blank">Twitter</a>, and <a href="https://wordpress.org/plugins/feeds-for-youtube/" target="_blank">YouTube</a>.', 'instagram-feed' ); ?></p>

		<div class="sbi_share_plugin">
			<h3><?php _e('Like the plugin? Help spread the word!', 'instagram-feed'); ?></h3>

			<button id="sbi_admin_show_share_links" class="button secondary" style="margin-bottom: 1px;"><i class="fa fa-share-alt" aria-hidden="true"></i>&nbsp;&nbsp;Share the plugin</button> <div id="sbi_admin_share_links"></div>
		</div>

	</div> <!-- end #sbi_admin -->

<?php } //End Settings page