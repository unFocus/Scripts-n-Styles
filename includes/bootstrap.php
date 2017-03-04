<?php
namespace unFocus\SnS;

const VERSION = '4.0.0-alpha';
const OPTION_GROUP = 'scripts_n_styles';
const ADMIN_MENU_SLUG = 'sns';
const REGISTERED =  [
	'utils', 'common', 'sack', 'quicktags', 'colorpicker', 'editor', 'wp-fullscreen', 'wp-ajax-response', 'wp-pointer', 'autosave',
	'heartbeat', 'wp-auth-check', 'wp-lists', 'prototype', 'scriptaculous-root', 'scriptaculous-builder', 'scriptaculous-dragdrop',
	'scriptaculous-effects', 'scriptaculous-slider', 'scriptaculous-sound', 'scriptaculous-controls', 'scriptaculous', 'cropper',
	'jquery', 'jquery-core', 'jquery-migrate', 'jquery-ui-core', 'jquery-effects-core', 'jquery-effects-blind', 'jquery-effects-bounce',
	'jquery-effects-clip', 'jquery-effects-drop', 'jquery-effects-explode', 'jquery-effects-fade', 'jquery-effects-fold',
	'jquery-effects-highlight', 'jquery-effects-pulsate', 'jquery-effects-scale', 'jquery-effects-shake', 'jquery-effects-slide',
	'jquery-effects-transfer', 'jquery-ui-accordion', 'jquery-ui-autocomplete', 'jquery-ui-button', 'jquery-ui-datepicker',
	'jquery-ui-dialog', 'jquery-ui-draggable', 'jquery-ui-droppable', 'jquery-ui-menu', 'jquery-ui-mouse', 'jquery-ui-position',
	'jquery-ui-progressbar', 'jquery-ui-resizable', 'jquery-ui-selectable', 'jquery-ui-slider', 'jquery-ui-sortable',
	'jquery-ui-spinner', 'jquery-ui-tabs', 'jquery-ui-tooltip', 'jquery-ui-widget', 'jquery-form', 'jquery-color', 'suggest',
	'schedule', 'jquery-query', 'jquery-serialize-object', 'jquery-hotkeys', 'jquery-table-hotkeys', 'jquery-touch-punch',
	'jquery-masonry', 'thickbox', 'jcrop', 'swfobject', 'plupload', 'plupload-html5', 'plupload-flash', 'plupload-silverlight',
	'plupload-html4', 'plupload-all', 'plupload-handlers', 'wp-plupload', 'swfupload', 'swfupload-swfobject', 'swfupload-queue',
	'swfupload-speed', 'swfupload-all', 'swfupload-handlers', 'comment-reply', 'json2', 'underscore', 'backbone', 'wp-util',
	'wp-backbone', 'revisions', 'imgareaselect', 'mediaelement', 'wp-mediaelement', 'password-strength-meter', 'user-profile',
	'user-suggest', 'admin-bar', 'wplink', 'wpdialogs', 'wpdialogs-popup', 'word-count', 'media-upload', 'hoverIntent', 'customize-base',
	'customize-loader', 'customize-preview', 'customize-controls', 'accordion', 'shortcode', 'media-models', 'media-views',
	'media-editor', 'mce-view', 'less.js', 'coffeescript', 'chosen', 'coffeelint', 'mustache', 'html5shiv', 'html5shiv-printshiv',
	'google-diff-match-patch', 'codemirror' ];

const CM_THEMES = [ 'default',
	'3024-day', '3024-night', 'abcdef', 'ambiance',
	'base16-dark', 'base16-light', 'bespin', 'blackboard',
	'cobalt', 'colorforth',
	'dracula', 'duotone-dark', 'duotone-light',
	'eclipse', 'elegant', 'erlang-dark',
	'hopscotch', 'icecoder', 'isotope',
	'lesser-dark', 'liquibyte',
	'material', 'mbo', 'mdn-like', 'midnight', 'monokai',
	'neat', 'neo', 'night',
	'panda-syntax', 'paraiso-dark', 'paraiso-light', 'pastel-on-dark',
	'railscasts', 'rubyblue',
	'seti', 'solarized',
	'the-matrix', 'tomorrow-night-bright', 'tomorrow-night-eighties',
	'ttcn', 'twilight',
	'vibrant-ink',
	'xq-dark', 'xq-light',
	'yeti', 'zenburn' ];

add_action( 'wp_before_admin_bar_render', function() {
	global $wp_admin_bar;
	$wp_admin_bar->add_node( [
		'id'    => 'Scripts_n_Styles',
		'title' => 'Scripts n Styles',
		'href'  => '#',
		'meta'  => array( 'class' => 'Scripts_n_Styles' )
	] );
}, 11 );

require_once( "main.php" );

require_once( "class-sns-widget.php" );
