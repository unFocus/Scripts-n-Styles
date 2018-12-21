<?php
/**
 * Usage Page.
 *
 * @package Scripts-N-Styles
 */

namespace unFocus\SnS;

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once \ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
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
			$return .= '<div>' . esc_html__( 'Scripts (head)', 'scripts-n-styles' ) . '</div>';
		}
		if ( isset( $post->sns_scripts['scripts'] ) ) {
			$return .= '<div>' . esc_html__( 'Scripts', 'scripts-n-styles' ) . '</div>';
		}
		if ( isset( $post->sns_scripts['enqueue_scripts'] ) ) {
			$return .= '<div>' . esc_html__( 'Enqueued Scripts', 'scripts-n-styles' ) . '</div>';
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
			$return .= '<div>' . esc_html__( 'TinyMCE Formats', 'scripts-n-styles' ) . '</div>';
		}
		if ( isset( $post->sns_styles['styles'] ) ) {
			$return .= '<div>' . esc_html__( 'Styles', 'scripts-n-styles' ) . '</div>';
		}
		if ( isset( $post->sns_styles['classes_post'] ) ) {
			$return .= '<div>' . esc_html__( 'Post Classes', 'scripts-n-styles' ) . '</div>';
		}
		if ( isset( $post->sns_styles['classes_body'] ) ) {
			$return .= '<div>' . esc_html__( 'Body Classes', 'scripts-n-styles' ) . '</div>';
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
	 * Build title column.
	 *
	 * @param object $post A WordPress $post object.
	 */
	public function column_title( $post ) {
		$edit_link = esc_url( get_edit_post_link( $post->ID ) );
		// Translators: Link to edit the post.
		$edit_title = esc_attr( sprintf( __( 'Edit &#8220;%s&#8221;', 'scripts-n-styles' ), $post->post_title ) );

		?>
		<strong>
		<?php
		if ( $this->ajax_user_can() && 'trash' !== $post->post_status ) {
			?>
			<a class="row-title" href="<?php echo esc_attr( $edit_link ); ?>" title="<?php echo esc_attr( $edit_title ); ?>">
			<?php echo esc_html( $post->post_title ); ?>
			</a>
			<?php
		} else {
			echo esc_html( $post->post_title );
		}
		_post_states( $post );
		?>
		</strong>
		(type: <?php echo esc_html( $post->post_type ); ?>)
		<?php
	}

	/**
	 * Define columns.
	 */
	public function get_columns() {
		return [
			'title'       => esc_html__( 'Title', 'scripts-n-styles' ),
			'script_data' => esc_html__( 'Script Data', 'scripts-n-styles' ),
			'style_data'  => esc_html__( 'Style Data', 'scripts-n-styles' ),
			'hoops_data'  => esc_html__( 'Hoops Data', 'scripts-n-styles' ),
		];
	}

	/**
	 * Build query.
	 */
	public function prepare_items() {
		$screen_id = get_current_screen()->id;
		$per_page  = $this->get_items_per_page( "{$screen_id}_per_page", 20 );

		/**
		 * Get Relavent Posts.
		 */
		$query = new \WP_Query( [
			'posts_per_page' => $per_page,
			'paged'          => $this->get_pagenum(),
			'post_type'      => 'any',
			'post_status'    => 'any',
			'orderby'        => 'ID',
			'order'          => 'ASC',
			'meta_key'       => '_SnS', // WPCS: slow query ok.
		] );

		$this->items = $this->_add_meta_data( $query->posts );

		$this->set_pagination_args( [
			'total_items' => $query->found_posts,
			'per_page'    => $per_page,
			'total_pages' => $query->max_num_pages,
		] );

		$this->_column_headers = [
			$this->get_columns(),
			[],
			$this->get_sortable_columns(),
		];
	}

	/**
	 * Build metadata onto post objects.
	 *
	 * @param array $posts An array of $post objects.
	 */
	public function _add_meta_data( $posts ) {
		foreach ( $posts as $post ) {
			$sns     = get_post_meta( $post->ID, '_SnS', true );
			$styles  = isset( $sns['styles'] ) ? $sns['styles'] : [];
			$scripts = isset( $sns['scripts'] ) ? $sns['scripts'] : [];
			$hoops   = isset( $sns['shortcodes'] ) ? $sns['shortcodes'] : [];
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
