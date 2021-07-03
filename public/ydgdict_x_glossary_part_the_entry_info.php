<?php

$entry = get_post();

$entry_type = ( empty( $entry->prefix ) ) ? $entry->entry_type : $entry->prefix;

$post_parent = ( $entry->post_parent ) ? get_post( $entry->post_parent ) : NULL;

?>
<span class='ydgdict_glossary_entry_type'>
    <a href="<?php echo get_term_link( $entry_type->term_id ); ?>" >(<?php echo $entry_type->name; ?>)</a>
</span>

<div class="ydgdict_glossary_entry_info">

     <?php if ( isset( $post_parent ) ) : ?>
        <span>based on: <a href="<?php echo get_permalink( $post_parent->ID ); ?>" ><?php echo $post_parent->post_title ?></a></span>
    <?php endif; ?>

    <?php // if ( 'verb' == $entry->entry_type->slug && isset( $post_parent ) ) : ?>
   

    <?php if ( "" != $entry->plural ) : ?>
        <span class="ydgdict_noun_plural">Plural: <?php echo $entry->plural; ?></span>
    <?php elseif ( preg_match ( '/_verb/', $entry_type->slug ) ) : ?>
        <div class="ydgdict_verb_past">
        <div class="ydgdict_spoken_past"><span style="    width: 200px;
    display: inline-block; font-style:italic;">spoken past: </span><span class="ydgdict_helper_verb"><?php echo $entry->aux; ?>  </span><span><?php echo $entry->past_part; ?></span></div> 
        <div class="ydgdict_written_past"><span style="    width: 200px;
    display: inline-block;font-style:italic;">written past (preterite): </span><?php echo $entry->preterit; ?></div></div>
    <?php endif; ?>
</div><!-- span.ydgdict_glossary_entry_info -->

<?php edit_post_link( __( 'Edit', 'coraline' ), '<span class="ydgdict_entry_edit">', '</span>' ); ?>