<?php
namespace unFocus\SnS;

add_action( 'widgets_init', function() {
	$options = get_option( 'SnS_options' );
	if ( isset( $options[ 'hoops_widget' ] ) && 'yes' == $options[ 'hoops_widget' ] )
		register_widget( '\unFocus\SnS\Widget' );
} );

class Widget extends \WP_Widget
{
	function __construct() {
		parent::__construct(
			'sns_hoops',
			__( 'Hoops', 'scripts-n-styles' ),
			[
				'classname' => 'sns_widget_text',
				'description' => __( 'Arbitrary text or HTML (including "hoops" shortcodes)', 'scripts-n-styles' ),
				'customize_selective_refresh' => true,
			],
			[
				'width' => 400,
				'height' => 350
			]
		);
	}

	function widget( $args, $instance ) {
		global $shortcode_tags;

		extract( $args );
		$title = apply_filters( 'widget_title', empty( $instance[ 'title' ] ) ? '' : $instance[ 'title' ], $instance, $this->id_base );
		$text = apply_filters( 'widget_text', empty( $instance[ 'text' ] ) ? '' : $instance[ 'text' ], $instance );

		echo $before_widget;
		if ( ! empty( $title ) )
			echo $before_title . $title . $after_title;
		echo '<div class="hoopstextwidget">';
		$content = ! empty( $instance[ 'filter' ] ) ? wpautop( $text ) : $text;

		$backup = $shortcode_tags;
		remove_all_shortcodes();

		add_shortcode( 'sns_shortcode', '\unFocus\SnS\hoops_shortcode' );
		add_shortcode( 'hoops', '\unFocus\SnS\hoops_shortcode' );

		$content = do_shortcode( $content );

		$shortcode_tags = $backup;

		echo $content;
		echo '</div>';
		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance[ 'title' ] = strip_tags( $new_instance[ 'title' ] );
		if ( current_user_can( 'unfiltered_html' ) )
			$instance[ 'text' ] =  $new_instance[ 'text' ];
		else
			$instance[ 'text' ] = stripslashes( wp_filter_post_kses( addslashes( $new_instance[ 'text' ] ) ) ); // wp_filter_post_kses() expects slashed
		$instance[ 'filter' ] = isset( $new_instance[ 'filter' ] );
		return $instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'text' => '' ) );
		$title = strip_tags( $instance[ 'title' ] );
		$text = esc_textarea( $instance[ 'text' ] );
		?>
			<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" /></p>

			<textarea class="widefat" rows="16" cols="20" id="<?php echo $this->get_field_id( 'text' ); ?>" name="<?php echo $this->get_field_name( 'text' ); ?>"><?php echo $text; ?></textarea>

			<p><input id="<?php echo $this->get_field_id( 'filter' ); ?>" name="<?php echo $this->get_field_name( 'filter' ); ?>" type="checkbox" <?php checked( isset( $instance[ 'filter' ] ) ? $instance[ 'filter' ] : 0 ); ?> />&nbsp;<label for="<?php echo $this->get_field_id( 'filter' ); ?>"><?php _e( 'Automatically add paragraphs' ); ?></label></p>
		<?php
	}
}