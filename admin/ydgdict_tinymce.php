<?php




function add_plugin( $plugin_array )
{
    $plugin_array['ydgdict_add_sentence'] = plugins_url( 'js/ydgdict_tinymce.js', __FILE__ );
    return $plugin_array;
}



function register_button( $buttons )
{
    array_push( $buttons, "|", "ydgdict_add_sentence" );
    return $buttons;
}



function my_recent_posts_button()
{
    if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) )  return;

    if ( get_user_option( 'rich_editing' ) == 'true' )
    {
        add_filter( 'mce_external_plugins', 'add_plugin' );
        add_filter( 'mce_buttons', 'register_button' );
    }
}
add_action( 'admin_init', 'my_recent_posts_button' );




function ydgdict_create_sentence()
{
    if ( !check_ajax_referer( 'metabox_nonce' ) ) die();

    $my_post = stripslashes_deep( $_POST );

    // error_log( print_r( $my_post, true ) );

    $sentence_id = ydgdict_db_insert_sentence( $my_post['ydgdict_german'], $my_post['ydgdict_english'], $my_post['ydgdict_audio'], $my_post['ydgdict_post_id'], $my_post['ydgdict_sentence_id'] );
    $idea_id = 0;

    if ( ! empty( $my_post['ydgdict_idea'] ) || ! empty( $my_post['ydgdict_prev_idea'] ) )
    {
        $idea_id = ydgdict_assign_sentence_to_idea( $sentence_id, $my_post['ydgdict_idea'], $my_post['ydgdict_prev_idea'] );
    }

    echo json_encode( [ 'ydgdict_sentence_id' => $sentence_id, 'ydgdict_idea_id' => $idea_id ] );
    die();
}
add_action( 'wp_ajax_ydgdict_create_sentence', 'ydgdict_create_sentence' );