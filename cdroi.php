<?php
/*
Plugin Name: Current Day ROI
Description: Calculate Return on Investment based on current date. Use Shortcode, e.g. <code>[cdroi date="31.05.2017" value="2.4"]</code>.
Author: Bego Mario Garde
Author URI: https://pixolin.de
Version: 1.0
License: GPL2
Text Domain: cdroi
Domain Path: languages
*/

/*

    Copyright (C) 2017  Bego Mario Garde  <pixolin@pixolin.de>

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

add_shortcode( 'cdroi','cdroi_shortcode' );
/**
 * Shortcode Current Date Return on Investement
 * @param  [type] $atts Shortcode Attributes
 * @return [type]       Frontend text
 */
function cdroi_shortcode( $atts ) {
	$atts = shortcode_atts( array(
		'date' => '',
		'value' => 1,
	), $atts );

	// for localization use NumberFormatter
	$formatter = new NumberFormatter( _x( 'en_US', 'Don\'t translate, insert locale instead', 'cdroi' ), NumberFormatter::PERCENT );

	// check if value is numeric, else return error message
	if ( is_numeric( $atts['value'] ) ) {
		$value = $atts['value'];
	} else {
		// error message if value not correct
		return sprintf( __( 'invalid value "%s"', 'cdroi' ), $atts['value'] );
	}

	// check if real date used, else return error message
	if ( cdroi_checkdate( $atts['date'] ) ) {

		// do some date math
		$date1 = DateTime::createFromFormat( 'd.m.Y', $atts['date'] );
		$date2 = new DateTime();
		$interval = $date1->diff( $date2 );

		// check if date is less than current date
		$days = $interval->format( '%r%a days' );
		if ( 0 > $days ) {
			return sprintf( __( 'wrong date "%s"', 'cdroi' ) , $atts['date'] );
		}

		// desired calculation
		$result = $interval->days * $value;

		// finally the output
		return sprintf(
			esc_html__( 'After %1$s days %2$s of your investment are returned (at %3$s daily interest).', 'cdroi' ),
			$interval->days,
			number_format_i18n( $result, 2 ) . '%',
			number_format_i18n( $value, 2 ) . '%'
		);
	} else {
		// error message if date format not correct
		return sprintf( __( 'invalid date "%s"', 'cdroi' ), $atts['date'] );
	}

}

/**
 * Validate date
 * @param  [type] $date Date provided
 * @return [type]       is date
 */
function cdroi_checkdate( $date ) {
	if ( false === strtotime( $date ) ) {
		return false;
	} else {
		list($day, $month, $year ) = explode( '.', $date );
		if ( false === checkdate( $month, $day, $year ) ) {
			return false;
		}
	}
	return true;
}

add_action( 'plugins_loaded', 'cdroi_load_textdomain' );
/**
 * Load plugin textdomain.
 */
function cdroi_load_textdomain() {
	load_plugin_textdomain( 'cdroi', false, basename( dirname( __FILE__ ) ) . '/languages' );
}
