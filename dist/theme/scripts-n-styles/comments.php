<?php
/**
 * Comments template
 *
 * @package Scripts-N-Styles
 * @subpackage Theme
 */

if ( have_comments() ) {
	wp_list_comments();
	if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) {
		previous_comments_link();
		next_comments_link();
	}
}
comment_form();
