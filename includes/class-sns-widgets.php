<?php

/**
 * Do some widget stuff
 */
class SnS_Widgets
{
	function init() {
		add_action( 'widgets_init', array( __CLASS__, 'widgets_init' ) );
	}
	
	function widgets_init() {
		register_widget( 'SnS_Hoops_Widget' );
	}
}

/**
 * Jumpin thru Hoops?
 * Super Admins ('unfiltered_html' & 'manage_options') set up the Shortcodes, Blog Admin users ('edit_theme_options') can use this widget.
 * Is essensially just a plain Text Widget with the hoops shortcode processed. 
 * An alternative approach could be to just process shortcodes on normal Text Widgets, but I'd rather limit the cases.
 */
//class SnS_Hoops_Widget extends WP_Widget_Text {
//}
class SnS_Hoops_Widget extends WP_Widget {

	function __construct() {
		$widget_ops = array(
			'classname' => 'widget_text_hoops',
			'description' => __( 'Hoops Ready Text Widget', 'scripts-n-styles' )
		);
		$control_ops = array(
			'width' => 400,
			'height' => 350
		);
		parent::__construct(
			'sns_text_hoops',
			__( 'Text' ),
			$widget_ops,
			$control_ops
		);
	}

	function widget( $args, $instance ) {
		extract( $args );
		
		$title = empty( $instance[ 'title' ] ) ? '' : $instance[ 'title' ];
		$text = empty( $instance[ 'text' ] ) ? '' : $instance[ 'text' ];
		
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );
		$text = apply_filters( 'widget_text', $text, $instance );
		$text = ! empty( $instance[ 'filter' ] ) ? wpautop( $text ) : $text;
		
		do_shortcode();
		
		echo $before_widget;
		if ( ! empty( $title ) )
			echo $before_title . $title . $after_title;
		echo '<div class="texthoopswidget">';
		echo $text;
		echo '</div>';
		echo $after_widget;
		
	}
	/*
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
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'text' => '', 'filter' => 0 ) );
		
		$title = esc_attr( strip_tags( $instance[ 'title' ] ) );
		$title_id = $this->get_field_id( 'title' );
		$title_name = $this->get_field_name( 'title' );
		
		$text = esc_textarea( $instance[ 'text' ] );
		$text_id = $this->get_field_id( 'text' );
		$text_name = $this->get_field_name( 'text' );
		
		$filter = (bool) $instance[ 'filter' ];
		$filter_id = $this->get_field_id( 'filter' );
		$filter_name = $this->get_field_name( 'filter' );
		
		// Print the Fields.
		?>
		<p><label for="<?php echo $title_id; ?>"><?php _e( 'Title:' ); ?></label><input class="widefat" id="<?php echo $title_id; ?>" name="<?php echo $title_name; ?>" type="text" value="<?php echo $title; ?>" /></p>

		<textarea class="widefat" rows="16" cols="20" id="<?php echo $text_id; ?>" name="<?php echo $text_name; ?>"><?php echo $text; ?></textarea>

		<p><input id="<?php echo $filter_id; ?>" name="<?php echo $filter_name; ?>" type="checkbox" <?php checked( $filter ); ?> />&nbsp;<label for="<?php echo $filter_id; ?>"><?php _e( 'Automatically add paragraphs' ); ?></label></p>
		<?php
	}
	*/
}

?>