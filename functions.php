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

// Enqueue editor UI assets.
add_action( 'enqueue_block_editor_assets', 'tt4_dark_mode_editor_assets' );

function tt4_dark_mode_editor_assets() {
	wp_enqueue_style(
		'tt4-dark-mode-editor',
		get_theme_file_uri( 'assets/editor.css' ),
		filemtime( get_theme_file_path( 'assets/editor.css' ) )
	);
}

// == The below is only needed if not adding CSS via `theme.json`. == //

/*
// Enqueue front-end assets.
add_action( 'wp_enqueue_scripts', 'tt4_dark_mode_enqueue_assets' );

function tt4_dark_mode_enqueue_assets() {
	wp_enqueue_style( 'tt4-dark-mode', get_stylesheet_uri() );
}

// Add editor stylesheet.
add_action( 'after_setup_theme', 'tt4_dark_mode_theme_setup' );

function tt4_dark_mode_theme_setup() {
	add_editor_style( get_stylesheet_uri() );
}
*/
