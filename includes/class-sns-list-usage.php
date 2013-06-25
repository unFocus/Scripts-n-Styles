<?php
if ( ! class_exists( 'WP_List_Table' ) ) require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );

class SnS_List_Usage extends WP_List_Table {

	function ajax_user_can() {
		return current_user_can( 'unfiltered_html' ) && current_user_can( 'manage_options' );
	}

	function column_default( $post, $column_name ) {
		$return = '';
		switch( $column_name ){
			case 'status':
				return $post->post_status;
			case 'ID':
			case 'post_type':
				return $post->$column_name;
			case 'script_data':
				if ( isset( $post->sns_scripts[ 'scripts_in_head' ] ) ) {
                    $return .= '<div>' . __( 'Scripts (head)', 'scripts-n-styles' ) . '</div>';
                }
                if ( isset( $post->sns_scripts[ 'scripts' ] ) ) {
                    $return .= '<div>' . __( 'Scripts', 'scripts-n-styles' ) . '</div>';
                }
                if ( isset( $post->sns_scripts[ 'enqueue_scripts' ] ) ) {
                    $return .= '<div>' . __( 'Enqueued Scripts', 'scripts-n-styles' ) . '</div>';
                }
				return $return;
			case 'style_data':
				if ( isset( $post->sns_styles[ 'classes_mce' ] ) ) {
                    $return .= '<div>' . __( 'TinyMCE Formats', 'scripts-n-styles' ) . '</div>';
                }
                if ( isset( $post->sns_styles[ 'styles' ] ) ) {
                    $return .= '<div>' . __( 'Styles', 'scripts-n-styles' ) . '</div>';
                }
                if ( isset( $post->sns_styles[ 'classes_post' ] ) ) {
                    $return .= '<div>' . __( 'Post Classes', 'scripts-n-styles' ) . '</div>';
                }
                if ( isset( $post->sns_styles[ 'classes_body' ] ) ) {
                    $return .= '<div>' . __( 'Body Classes', 'scripts-n-styles' ) . '</div>';
                }
				return $return;
			default:
				return print_r( $post, true );
		}
	}

	function column_title( $post ) {
		$edit_link = esc_url( get_edit_post_link( $post->ID ) );
		$edit_title = esc_attr( sprintf( __( 'Edit &#8220;%s&#8221;' ), $post->post_title ) );

		$actions = array(
			'edit'      => sprintf( '<a title="%s" href="%s">%s</a>', $edit_title, $edit_link, __( 'Edit' ) ),
		);

		$return = '<strong>';
		if ( $this->ajax_user_can() && $post->post_status != 'trash' ) {
			$return .= '<a class="row-title"';
			$return .= ' href="'. $edit_link .'"';
			$return .= ' title="'. $edit_title .'">';
			$return .= $post->post_title;
			$return .= '</a>';
		} else {
			$return .= $post->post_title;
		}
		$this->_post_states( $post );
		$return .= '</strong>';
		$return .= $this->row_actions( $actions );

		return $return;
	}

	function get_columns() {
		$columns = array(
			'title'			=> __( 'Title' ),
			'ID'			=> __( 'ID' ),
			'status'		=> __( 'Status' ),
			'post_type'		=> __( 'Post Type', 'scripts-n-styles' ),
			'script_data'	=> __( 'Script Data', 'scripts-n-styles' ),
			'style_data'	=> __( 'Style Data', 'scripts-n-styles' )
		);

		return $columns;
	}

	function prepare_items() {
		$screen_id = get_current_screen()->id;
		$per_page = $this->get_items_per_page( str_replace( '-', '_', "{$screen_id}_per_page" ) );

        $this->_column_headers = array(
			$this->get_columns(),
			array(),
			$this->get_sortable_columns()
		);

		/**
		 * Get Relavent Posts.
		 */
		$posts = get_posts( array(
			'numberposts' => -1,
			'post_type' => 'any',
			'post_status' => 'any',
			'orderby' => 'ID',
			'order' => 'ASC',
			'meta_key' => '_SnS'
		) );

		$items = $this->_add_meta_data( $posts );

		$total_items = count( $items );

		/**
		 * Reduce items to current page's posts.
		 */
		$this->items = array_slice(
			$items,
			( ( $this->get_pagenum() - 1 ) * $per_page ),
			$per_page
		);

		$this->set_pagination_args( compact( 'total_items', 'per_page' ) );
	}

	function _post_states( $post ) {
		$post_states = array();
		$return = '';
		if ( isset($_GET[ 'post_status' ]) )
			$post_status = $_GET[ 'post_status' ];
		else
			$post_status = '';

		if ( ! empty( $post->post_password ) )
			$post_states[ 'protected' ] = __( 'Password protected' );
		if ( 'private' == $post->post_status && 'private' != $post_status )
			$post_states[ 'private' ] = __( 'Private' );
		if ( 'draft' == $post->post_status && 'draft' != $post_status )
			$post_states[ 'draft' ] = __( 'Draft' );
		if ( 'pending' == $post->post_status && 'pending' != $post_status )
			/* translators: post state */
			$post_states[ 'pending' ] = _x( 'Pending', 'post state' );
		if ( is_sticky($post->ID) )
			$post_states[ 'sticky' ] = __( 'Sticky' );

		$post_states = apply_filters( 'display_post_states', $post_states );

		if ( ! empty( $post_states ) ) {
			$state_count = count( $post_states );
			$i = 0;
			$return .= ' - ';
			foreach ( $post_states as $state ) {
				++$i;
				( $i == $state_count ) ? $sep = '' : $sep = ', ';
				$return .= "<span class='post-state'>$state$sep</span>";
			}
		}

		if ( get_post_format( $post->ID ) )
			$return .= ' - <span class="post-state-format">' . get_post_format_string( get_post_format( $post->ID ) ) . '</span>';

		return $return;
	}

	function _add_meta_data( $posts ) {
		foreach( $posts as $post) {
			$SnS = get_post_meta( $post->ID, '_SnS', true );
			$styles = isset( $SnS[ 'styles' ] ) ? $SnS[ 'styles' ]: array();
			$scripts = isset( $SnS[ 'scripts' ] ) ? $SnS[ 'scripts' ]: array();
			if ( ! empty( $styles ) )
				$post->sns_styles = $styles;
			if ( ! empty( $scripts ) )
				$post->sns_scripts = $scripts;
		}
		return $posts;
	}
}
?>