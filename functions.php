<?php

/**
 * Theme functions file, which is autoloaded by WordPress. This file is used to
 * load any other necessary PHP files and bootstrap the theme.
 *
 * @author    Your Name <yourname@some-email-service-or-another.com>
 * @copyright Copyright (c) 2024, Your Name
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/justintadlock/tt4-dark-mode
 */

add_action( 'init', 'tt4_dark_mode_register_user_meta' );

function tt4_dark_mode_register_user_meta() {
	$sanitize = fn($value) => in_array( $value, ['light', 'dark'], true ) ? $value : '';

	register_meta('user', 'tt4-dark-mode-color-scheme', [
		'label'             => __('Color Scheme', 'tt4-dark-mode'),
		'description'       => __('Stores the preferred color scheme for the site.', 'tt4-dark-mode' ),
		'default'           => '',
		'sanitize_callback' => $sanitize,
		'show_in_rest'      => true,
		'single'            => true,
		'type'              => 'string'
	]);
}

// Enqueue editor UI assets.
add_action( 'enqueue_block_editor_assets', 'tt4_dark_mode_editor_assets' );

function tt4_dark_mode_editor_assets() {
	wp_enqueue_style(
		'tt4-dark-mode-editor',
		get_theme_file_uri( 'assets/editor.scss' ),
		filemtime( get_theme_file_path( 'assets/editor.scss' ) )
	);
}

add_action( 'init', 'tt4_dark_mode_block_assets' );

function tt4_dark_mode_block_assets() {
	if ( ! file_exists( get_theme_file_path( 'public/css/core-button.asset.php' ) ) ) {
		return;
	}

	$asset = include get_theme_file_path( 'public/css/core-button.asset.php' );

	wp_enqueue_block_style( 'core/button', [
		'handle' => 'tt4-dark-mode-core-button',
		'src'    => get_theme_file_uri( 'public/css/core-button.css' ),
		'path'   => get_theme_file_path( 'public/css/core-button.css' ),
		'deps'   => $asset['dependencies'],
		'ver'    => $asset['version']
	]);
}

add_filter( 'block_type_metadata_settings', 'tt4_dark_mode_block_type_metadata_settings' );

function tt4_dark_mode_block_type_metadata_settings(array $settings) {
	if ('core/button' === $settings['name']) {
		$settings['supports']['interactivity'] = true;
	}

	return $settings;
}

add_filter( 'render_block_core/button', 'tt4_dark_mode_render_button', 10, 2 );

function tt4_dark_mode_render_button( string $content, array $block ) {
	if (
		! isset( $block['attrs']['className'] )
		|| ! str_contains( $block['attrs']['className'], 'toggle-color-scheme' )
	) {
		return $content;
	}

	$processor = new WP_HTML_Tag_Processor($content);

	if (
		! $processor->next_tag([ 'class_name' => 'toggle-color-scheme'])
		|| ! $processor->next_tag('button')
	) {
		return $processor->get_updated_html();
	}

	// Add interactivity directives to the `<button>`.
	$attr = [
		'data-wp-interactive'           => 'tt4-dark-mode/color-scheme',
		'data-wp-on--click'             => 'actions.toggle',
		'data-wp-init'                  => 'callbacks.init',
		'data-wp-watch'                 => 'callbacks.updateScheme',
		'data-wp-bind--aria-pressed'    => 'state.isDark',
		'data-wp-class--is-dark-scheme' => 'state.isDark'
	];

	foreach ($attr as $name => $value) {
		$processor->set_attribute($name, $value);
	}

	// Set the initial interactivity state and enqueue assets.
	wp_interactivity_state( 'tt4-dark-mode/color-scheme', [
		'colorScheme'       => tt4_dark_mode_get_color_scheme(),
		'isDark'            => tt4_dark_mode_is_dark_scheme(),
		'userId'            => get_current_user_id(),
		'name'              => 'tt4-dark-mode-color-scheme',
		'cookiePath'        => COOKIEPATH,
		'cookieDomain'      => COOKIE_DOMAIN
	] );


	if ( is_user_logged_in() ) {
		wp_enqueue_script( 'wp-api-fetch' );
	}

	$script = include get_theme_file_path( 'public/js/color-scheme.asset.php' );

	wp_enqueue_script_module(
		'tt4-dark-mode-color-scheme',
		get_theme_file_uri( 'public/js/color-scheme.js' ),
		$script['dependencies'],
		$script['version']
	);

	return $processor->get_updated_html();
}

function tt4_dark_mode_get_color_scheme() {
	$key = 'tt4-dark-mode-color-scheme';
	$valid_schemes = [ 'light', 'dark' ];

	if ( is_user_logged_in() ) {
		$scheme = get_user_meta( get_current_user_id(), $key, true );

		if ( $scheme && in_array( $scheme, $valid_schemes, true ) ) {
			return $scheme;
		}
	}

	if ( isset( $_COOKIE[ $key ] ) ) {
		$scheme = sanitize_key( wp_unslash( $_COOKIE[ $key ] ) );

		if ( $scheme && in_array( $scheme, $valid_schemes, true ) ) {
			return $scheme;
		}
	}

	return 'light dark';
}

function tt4_dark_mode_is_dark_scheme() {
	$scheme = tt4_dark_mode_get_color_scheme();

	return match( $scheme ) {
		'dark'   => true,
		'light'  => false,
		default  => null
	};
}
