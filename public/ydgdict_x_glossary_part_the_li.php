<?php

$entry = get_post();

?>

<li class="glossary_list <?php echo $entry->entry_type->name . " "; ?>" >

	<div class="ydgdict_glossary_entry"><a href="<?php echo get_permalink( get_the_ID() ); ?>" ><?php echo ( isset( $entry->gender ) ) ? "{$entry->entry}, {$entry->gender}" : $entry->entry; ?></a>
		<div class='ydgdict_glossary_entry_audio examples-audio'><?php echo wp_audio_shortcode( array( 'src' => $entry->entry_audio ) ); ?></div>
	</div>

	<?php 

	include( YDGDICT_PLUGIN_DIR . 'public/ydgdict_x_glossary_part_the_entry_info.php' );

	include( YDGDICT_PLUGIN_DIR . 'public/ydgdict_x_glossary_part_the_ideas.php' );
	
	if ( !is_singular( 'post' ) ) : 

		// include( YDGDICT_PLUGIN_DIR . 'public/ydgdict_x_glossary_part_the_description.php' );
	
	endif; 
	if ( current_user_can( 'manage_options' ) ) echo "*".get_the_ID();
	?>
</li>
