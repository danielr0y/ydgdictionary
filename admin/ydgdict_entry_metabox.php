<?php

/**
 * this file registers callback functions for things that an individual entry can do
 * 
 */

if ( !defined( 'ABSPATH' ) ) { exit; }

// entry metabox html callback
function ydgdict_entry_metabox_html()
{
    include( YDGDICT_PLUGIN_DIR . 'admin/ydgdict_metabox_part_the_table.php' );
}




// add metaboxes
function ydgdict_add_entry_metabox()
{
    add_meta_box(
        'ydgdict_entry_metabox',
        'YDG Dictionary Entry',
        'ydgdict_entry_metabox_html',
        'entry',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'ydgdict_add_entry_metabox');