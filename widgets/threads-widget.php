<?php

add_action( 'widgets_init', create_function( '', 'register_widget( "simple_forum_threads_widget" );' ) );
class Simple_Forum_Threads_Widget extends WP_Widget {

	public function __construct() {
		parent::__construct(
	 		'simple_forum_threads_widget',
			'Forum Threads',
			array(
				'description'	=> __( 'List forum threads', 'simple-forum-widgets' )
			)
		);
	}

	private function widget_fields() {
		$fields = array(

			'widget_title' => array(
				'simple_forum_widgets_name'			=> 'widget_title',
				'simple_forum_widgets_title'			=> __( 'Title', 'simple-forum-widgets' ),
				'simple_forum_widgets_field_type'		=> 'text',
				'simple_forum_widgets_field_class'		=> ''
			),

			'threads_api_url' => array (
				'simple_forum_widgets_name'			=> 'threads_api_url',
				'simple_forum_widgets_title'			=> __( 'API URL for forum threds ', 'simple-forum-widgets' ),
				'simple_forum_widgets_field_type'		=> 'textarea',
				'simple_forum_widgets_field_class'		=> ''
			),

			'open_new_tab' => array (
				'simple_forum_widgets_name'			=> 'open_new_tab',
				'simple_forum_widgets_title'			=> __( 'Open link in new tab', 'simple-forum-widgets' ),
				'simple_forum_widgets_field_type'		=> 'checkbox',
				'simple_forum_widgets_field_class'		=> 'checkbox'
			),

			'force_widget_styles' => array (
				'simple_forum_widgets_name'			=> 'force_widget_styles',
				'simple_forum_widgets_title'			=> __( 'Use Widget Styles', 'simple-forum-widgets' ),
				'simple_forum_widgets_field_type'		=> 'checkbox',
				'simple_forum_widgets_field_class'		=> 'checkbox tec-sf-widget-force-styles'
			),

			'div_class' => array (
				'simple_forum_widgets_name'			=> 'div_class',
				'simple_forum_widgets_title'			=> __( 'Class for div tag', 'simple-forum-widgets' ),
				'simple_forum_widgets_field_type'		=> 'text',
				'simple_forum_widgets_field_class'		=> 'text x_style'
			),

			'ul_class' => array (
				'simple_forum_widgets_name'			=> 'ul_class',
				'simple_forum_widgets_title'			=> __( 'Class for ul tag', 'simple-forum-widgets' ),
				'simple_forum_widgets_field_type'		=> 'text',
				'simple_forum_widgets_field_class'		=> 'text x_style'
			),

			'li_class' => array (
				'simple_forum_widgets_name'			=> 'li_class',
				'simple_forum_widgets_title'			=> __( 'Class for li tag', 'simple-forum-widgets' ),
				'simple_forum_widgets_field_type'		=> 'text',
				'simple_forum_widgets_field_class'		=> 'text x_style'
			),

			'a_class' => array (
				'simple_forum_widgets_name'			=> 'a_class',
				'simple_forum_widgets_title'			=> __( 'Class for a tag', 'simple-forum-widgets' ),
				'simple_forum_widgets_field_type'		=> 'text',
				'simple_forum_widgets_field_class'		=> 'text x_style'
			),

			'span_class' => array (
				'simple_forum_widgets_name'			=> 'span_class',
				'simple_forum_widgets_title'			=> __( 'Class for relies span tag', 'simple-forum-widgets' ),
				'simple_forum_widgets_field_type'		=> 'text',
				'simple_forum_widgets_field_class'		=> 'text x_style'
			)
		);

		return $fields;
	}

	public function widget( $args, $instance ) {
		extract( $args );

		$widget_title 			= apply_filters( 'widget_title', $instance['widget_title'] );

		$threads_api_url 		= $instance['threads_api_url'];
		$open_new_tab			= $instance['open_new_tab'];
		$force_styles 			= $instance['force_widget_styles'];
		$div_class 				= $instance['div_class'];
		$ul_class 				= $instance['ul_class'];
		$li_class 				= $instance['li_class'];
		$a_class 				= $instance['a_class'];
		$span_class 			= $instance['span_class'];

		echo $before_widget;

		$sf_threads = wp_remote_get( $threads_api_url );
		$threads = json_decode($sf_threads['body']);
		if (isset($threads->status) && $threads->status === false) {
			echo isset($threads->message) ? $threads->message : $threads->error;
		} else {
		?>

		<div class="<?php echo $div_class; if ($force_styles) { echo ' tec-sf-widget'; }?>">
			<?php if( isset( $widget_title ) ) { ?>
			<div class="tec-sf-widget-title">
			   <?php
				   if( isset( $widget_title ) ) {
					   echo $before_title . $widget_title . $after_title;
				   }
			   ?>
		   </div>
		   <?php } ?>

		   <?php
		   if( isset( $threads ) && !empty( $threads ) ) {
			   echo '<ul class="';
			   if (!$force_styles) { echo $ul_class; }
			   echo '">';
			   foreach($threads as $thread) {
				   echo '<li class="';
				   if (!$force_styles) { echo $li_class; }
				   echo '"><a href="'.$thread->thread_url.'" class="';
				   if (!$force_styles) { echo $a_class; }
				   echo '"';
				   if ($open_new_tab) { echo ' target="_blank"'; }
				   echo '>'.$thread->title.' <span class="';
				   if (!$force_styles) { echo $span_class; }
				   echo '">'.$thread->replies.'</span></a></li>';
			   }
			   echo '</ul>';
		   }
		   ?>

		</div>

		<?php
		}
		echo $after_widget;
	}

	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$widget_fields = $this->widget_fields();

		foreach( $widget_fields as $widget_field ) {
			extract( $widget_field );
			$instance[$simple_forum_widgets_name] = simple_forum_widgets_updated_field_value( $widget_field, $new_instance[$simple_forum_widgets_name] );
			echo $instance[$simple_forum_widgets_name];
		}

		return $instance;
	}

	public function form( $instance ) {
		$widget_fields = $this->widget_fields();
		echo '<div class="tec-sf-widget-threads">';

		foreach( $widget_fields as $widget_field ) {
			extract( $widget_field );
			$simple_forum_widgets_field_value = isset( $instance[$simple_forum_widgets_name] ) ? esc_attr( $instance[$simple_forum_widgets_name] ) : '';
			simple_forum_widgets_show_widget_field( $this, $widget_field, $simple_forum_widgets_field_value );

		}
		echo '</div>';
		echo "<script> jQuery(document).ready(function($) { $(document).on('change' , '.tec-sf-widget-force-styles' , function(){ if(this.checked) { $(this).parents('.tec-sf-widget-threads').find('.x_style').hide(); } else { $(this).parents('.tec-sf-widget-threads').find('.x_style').show(); } }); if ($('.tec-sf-widget-threads .tec-sf-widget-force-styles').is(':checked')) { $('.tec-sf-widget-force-styles').parents('.tec-sf-widget-threads').find('.x_style').hide(); } else { $('.tec-sf-widget-force-styles').parents('.tec-sf-widget-threads').find('.x_style').show(); } }); </script>";
	}

}
