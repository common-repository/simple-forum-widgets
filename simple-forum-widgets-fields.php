<?php

function simple_forum_widgets_show_widget_field( $instance = '', $widget_field = '', $thsp_field_value = '' ) {
	extract( $widget_field );

	switch( $simple_forum_widgets_field_type ) {

		case 'text' : ?>
			<p class="<?php echo $instance->get_field_id( $simple_forum_widgets_field_class ); ?>">
				<label for="<?php echo $instance->get_field_id( $simple_forum_widgets_name ); ?>"><?php echo $simple_forum_widgets_title; ?>:</label>
				<input class="widefat" id="<?php echo $instance->get_field_id( $simple_forum_widgets_name ); ?>" name="<?php echo $instance->get_field_name( $simple_forum_widgets_name ); ?>" type="text" value="<?php echo $thsp_field_value; ?>" />

				<?php if( isset( $simple_forum_widgets_description ) ) { ?>
				<br />
				<small><?php echo $simple_forum_widgets_description; ?></small>
				<?php } ?>
			</p>
			<?php
			break;

		case 'textarea' : ?>
			<p>
				<label for="<?php echo $instance->get_field_id( $simple_forum_widgets_name ); ?>"><?php echo $simple_forum_widgets_title; ?>:</label>
				<textarea class="widefat" rows="6" id="<?php echo $instance->get_field_id( $simple_forum_widgets_name ); ?>" name="<?php echo $instance->get_field_name( $simple_forum_widgets_name ); ?>"><?php echo $thsp_field_value; ?></textarea>
			</p>
			<?php
			break;

		case 'checkbox' : ?>
			<p>
				<input id="<?php echo $instance->get_field_id( $simple_forum_widgets_name ); ?>" name="<?php echo $instance->get_field_name( $simple_forum_widgets_name ); ?>" type="checkbox" value="1" <?php checked( '1', $thsp_field_value ); ?> class="<?php echo $instance->get_field_id( $simple_forum_widgets_field_class ); ?>"/>
				<label for="<?php echo $instance->get_field_id( $simple_forum_widgets_name ); ?>"><?php echo $simple_forum_widgets_title; ?></label>

				<?php if( isset( $simple_forum_widgets_description ) ) { ?>
				<br />
				<small><?php echo $simple_forum_widgets_description; ?></small>
				<?php } ?>
			</p>
			<?php
			break;

	}
}
