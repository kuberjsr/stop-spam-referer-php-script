<?php
/************************************************************************/
/* Stop Spam Referer - Security system                                  */
/* ===================================                                  */
/* Write by Cyril Levert                                                */
/* Copyright (c) 2009                                                   */
/* http://www.php-minimus.org                                           */
/* dev@php-minimus.org                                                  */
/* Release version 1.0.0                                                */
/* Release date : 12-14-2009                                            */
/*                                                                      */
/* This program is free software.                                       */
/************************************************************************/

defined('BAD_REFERER_ACTIVE') or die();

if ( BAD_REFERER_ACTIVE === false ) return;

define('BAD_REFERER_PRODUCTION', true );


/** URL IP BAN */
define('KARKAR', 'index.php');
define('SERVER_NAME_BAD_REFERER', strtolower( htmlentities( strip_tags( $_SERVER['SERVER_NAME'] ) ) ) );

/** IP BAN */
$IP_DENY  = array( );

/** REFERER WHITE LIST  */
if ( BAD_REFERER_PRODUCTION === false ) {
	$REFERER_ALLOW  = array( SERVER_NAME_BAD_REFERER, '192.', '127.0', 'localhost' );
} else {
	$REFERER_ALLOW  = array( SERVER_NAME_BAD_REFERER );
}

/** DYNAMIC SCRIPT FOLDER  */
define('FOLDER_BAD_REFERER', @dirname(__FILE__).'/' );



/** PHP Functions */
if( ! function_exists('BAD_REFERER_get_ip') ) {
	FUNCTION BAD_REFERER_get_ip() {
		if ( BAD_REFERER_get_env('HTTP_X_FORWARDED_FOR') ) {
			return BAD_REFERER_get_env('HTTP_X_FORWARDED_FOR');
		} elseif ( BAD_REFERER_get_env('HTTP_CLIENT_IP') ) {
			return BAD_REFERER_get_env('HTTP_CLIENT_IP');
		} else {
			return BAD_REFERER_get_env('REMOTE_ADDR');
		}
	}
}

if( ! function_exists('BAD_REFERER_get_env') ) {
	FUNCTION BAD_REFERER_get_env($st_var) {
		global $HTTP_SERVER_VARS;
		if(isset($_SERVER[$st_var])) {
			return strip_tags( $_SERVER[$st_var] );
		} elseif(isset($_ENV[$st_var])) {
			return strip_tags( $_ENV[$st_var] );
		} elseif(isset($HTTP_SERVER_VARS[$st_var])) {
			return strip_tags( $HTTP_SERVER_VARS[$st_var] );
		} elseif(getenv($st_var)) {
			return strip_tags( getenv($st_var) );
		} elseif(function_exists('apache_getenv') && apache_getenv($st_var, true)) {
			return strip_tags( apache_getenv($st_var, true) );
		}
		return '';
	}
}

if( ! function_exists('BAD_REFERER_get_referer') ) {
	FUNCTION BAD_REFERER_get_referer() {
		if( BAD_REFERER_get_env('HTTP_REFERER') )
			return BAD_REFERER_get_env('HTTP_REFERER');
		return '';
	}
}

define('BAD_REFERER_GET_REFERER', strtolower( BAD_REFERER_get_referer() ) );
define('BAD_REFERER_GET_IP', BAD_REFERER_get_ip() );

/** stop ips not allowed */
if ( count( $IP_DENY ) > 0 ) {
	if ( in_array( BAD_REFERER_GET_IP, $IP_DENY ) ) {
		header('location: '.KARKAR.'');
		die();
	}
}


if ( ! defined( BAD_REFERER_GET_REFERER ) && stristr( BAD_REFERER_GET_REFERER, SERVER_NAME_BAD_REFERER ) ) {

	return;

} else {

	define('BAD_REFERER_ID', true );
	if ( is_file(FOLDER_BAD_REFERER.'list_referers.php') )
		include_once(FOLDER_BAD_REFERER.'list_referers.php');
	if ( is_file(FOLDER_BAD_REFERER.'list_custom.php') )
		include_once(FOLDER_BAD_REFERER.'list_custom.php');

	$check1  = str_replace($REFERER_ALLOW, '*', BAD_REFERER_GET_REFERER);
	if( BAD_REFERER_GET_REFERER != $check1 ) return;

	$check  = str_replace($ct_rules, '*', BAD_REFERER_GET_REFERER );
	if( BAD_REFERER_GET_REFERER != $check ) {
		header('location: http://'.BAD_REFERER_GET_REFERER );
		die();
	}

	$check2  = str_replace($ct_rules2, '*', BAD_REFERER_GET_REFERER );
	if( BAD_REFERER_GET_REFERER != $check2 ) {
		header('location: http://'.BAD_REFERER_GET_REFERER );
		die();
	}
}