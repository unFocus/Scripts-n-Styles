<?php
/**
 * Usage Page.
 *
 * @package Scripts-N-Styles
 */

namespace unFocus\SnS;

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( \ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * A class for displaying a list of items in an ajaxified HTML table.
 */
class List_Usage extends \WP_List_Table {

	/**
	 * Checks the current user's permissions
	 */
	public function ajax_user_can() {
		return current_user_can( 'unfiltered_html' ) && current_user_can( 'manage_options' );
	}

	/**
	 * Build columns.
	 *
	 * @param object $post A WordPress $post object.
	 */
	public function column_script_data( $post ) {
		$return = '';
		if ( isset( $post->sns_scripts['scripts_in_head'] ) ) {
			$return .= '<div>' . __( 'Scripts (head)', 'scripts-n-styles' ) . '</div>';
		}
		if ( isset( $post->sns_scripts['scripts'] ) ) {
			$return .= '<div>' . __( 'Scripts', 'scripts-n-styles' ) . '</div>';
		}
		if ( isset( $post->sns_scripts['enqueue_scripts'] ) ) {
			$return .= '<div>' . __( 'Enqueued Scripts', 'scripts-n-styles' ) . '</div>';
		}
		return $return;
	}
	/**
	 * Build columns.
	 *
	 * @param object $post A WordPress $post object.
	 */
	public function column_style_data( $post ) {
		$return = '';
		if ( isset( $post->sns_styles['classes_mce'] ) ) {
			$return .= '<div>' . __( 'TinyMCE Formats', 'scripts-n-styles' ) . '</div>';
		}
		if ( isset( $post->sns_styles['styles'] ) ) {
			$return .= '<div>' . __( 'Styles', 'scripts-n-styles' ) . '</div>';
		}
		if ( isset( $post->sns_styles['classes_post'] ) ) {
			$return .= '<div>' . __( 'Post Classes', 'scripts-n-styles' ) . '</div>';
		}
		if ( isset( $post->sns_styles['classes_body'] ) ) {
			$return .= '<div>' . __( 'Body Classes', 'scripts-n-styles' ) . '</div>';
		}
		return $return;
	}
	/**
	 * Build columns.
	 *
	 * @param object $post A WordPress $post object.
	 */
	public function column_hoops_data( $post ) {
		$hoops = [];
		if ( isset( $post->sns_hoops ) ) {
			foreach ( $post->sns_hoops as $hoop => $shortcode ) {
				$hoops[] = '[hoops name="' . $hoop . '"]';
			}
		}
		return implode( '<br>', $hoops );
	}
	/**
	 * Build columns.
	 *
	 * @param object $post        A WordPress $post object.
	 * @param object $column_name A column name.
	 */
	public function column_default( $post, $column_name ) {
		$return = '';
		switch ( $column_name ) {
			case 'status':
				return $post->post_status;
			case 'ID':
			case 'post_type':
				return $post->$column_name;
			default:
				return print_r( $post, true );
		}
	}

	/**
	 * Build title column.
	 *
	 * @param object $post A WordPress $post object.
	 */
	public function column_title( $post ) {
		$edit_link = esc_url( get_edit_post_link( $post->ID ) );
		// Translators: Link to edit the post.
		$edit_title = esc_attr( sprintf( __( 'Edit &#8220;%s&#8221;', 'scripts-n-styles' ), $post->post_title ) );

		$actions = array(
			'edit' => sprintf( '<a title="%s" href="%s">%s</a>', $edit_title, $edit_link, __( 'Edit', 'scripts-n-styles' ) ),
		);

		$return = '<strong>';
		if ( $this->ajax_user_can() && 'trash' != $post->post_status ) {
			$return .= '<a class="row-title"';
			$return .= ' href="' . $edit_link . '"';
			$return .= ' title="' . $edit_title . '">';
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

	/**
	 * Define columns.
	 */
	public function get_columns() {
		$columns = array(
			'title'         => __( 'Title', 'scripts-n-styles' ),
			'ID'            => __( 'ID', 'scripts-n-styles' ),
			'status'        => __( 'Status', 'scripts-n-styles' ),
			'post_type'     => __( 'Post Type', 'scripts-n-styles' ),
			'script_data'   => __( 'Script Data', 'scripts-n-styles' ),
			'style_data'    => __( 'Style Data', 'scripts-n-styles' ),
			'hoops_data'    => __( 'Hoops Data', 'scripts-n-styles' ),
		);

		return $columns;
	}

	/**
	 * Build query.
	 */
	public function prepare_items() {
		$screen_id = get_current_screen()->id;
		$per_page = $this->get_items_per_page( "{$screen_id}_per_page", 20 );

		/**
		 * Get Relavent Posts.
		 */
		$query = new \WP_Query( [
			'posts_per_page' => $per_page,
			'paged' => $this->get_pagenum(),
			'post_type' => 'any',
			'post_status' => 'any',
			'orderby' => 'ID',
			'order' => 'ASC',
			'meta_key' => '_SnS',
		] );

		$this->items = $this->_add_meta_data( $query->posts );

		$this->set_pagination_args( [
			'total_items' => $query->found_posts,
			'per_page' => $per_page,
			'total_pages' => $query->max_num_pages,
		] );

		$this->_column_headers = array(
			$this->get_columns(),
			array(),
			$this->get_sortable_columns(),
		);
	}

	/**
	 * Checks the current user's permissions
	 *
	 * @param object $post A WordPress $post object.
	 */
	public function _post_states( $post ) {
		$post_states = array();
		$return = '';
		if ( isset( $_GET['post_status'] ) ) {
			$post_status = sanitize_text_field( wp_unslash( $_GET['post_status'] ) );
		} else {
			$post_status = '';
		}

		if ( ! empty( $post->post_password ) ) {
			$post_states['protected'] = __( 'Password protected', 'scripts-n-styles' );
		}
		if ( 'private' == $post->post_status && 'private' != $post_status ) {
			$post_states['private'] = __( 'Private', 'scripts-n-styles' );
		}
		if ( 'draft' == $post->post_status && 'draft' != $post_status ) {
			$post_states['draft'] = __( 'Draft', 'scripts-n-styles' );
		}
		if ( 'pending' == $post->post_status && 'pending' != $post_status ) {
			/* translators: post state */
			$post_states['pending'] = _x( 'Pending', 'post state', 'scripts-n-styles' );
		}
		if ( is_sticky( $post->ID ) ) {
			$post_states['sticky'] = __( 'Sticky', 'scripts-n-styles' );
		}

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

		if ( get_post_format( $post->ID ) ) {
			$return .= ' - <span class="post-state-format">' . get_post_format_string( get_post_format( $post->ID ) ) . '</span>';
		}

		return $return;
	}

	/**
	 * Build metadata onto post objects.
	 *
	 * @param array $posts An array of $post objects.
	 */
	public function _add_meta_data( $posts ) {
		foreach ( $posts as $post ) {
			$sns = get_post_meta( $post->ID, '_SnS', true );
			$styles  = isset( $sns['styles'] ) ? $sns['styles'] : array();
			$scripts = isset( $sns['scripts'] ) ? $sns['scripts'] : array();
			$hoops   = isset( $sns['shortcodes'] ) ? $sns['shortcodes'] : array();
			if ( ! empty( $styles ) ) {
				$post->sns_styles = $styles;
			}
			if ( ! empty( $scripts ) ) {
				$post->sns_scripts = $scripts;
			}
			if ( ! empty( $hoops ) ) {
				$post->sns_hoops = $hoops;
			}
		}
		return $posts;
	}
}
