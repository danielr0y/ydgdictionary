<?php

/**
 * this file adds a metabox to post pages 
 * 
 * it registers callback functions for everything the metabox can do
 */

if ( !defined( 'ABSPATH' ) ) { exit; }

include( YDGDICT_PLUGIN_DIR . 'admin/ydgdict_article_metabox_functions.php' );






/**
 * the metabox
 * 
 */

// article metabox html callback
function ydgdict_metabox_html()
{
    include( YDGDICT_PLUGIN_DIR . 'admin/ydgdict_metabox_part_the_table.php' );?>
    <hr />
    <div>upload a csv file: <input id="ydgdict_upload_csv" type="file" accept=".csv" /></div><?php
}




// add metaboxes
function ydgdict_add_article_metabox()
{
    add_meta_box(
        'ydgdict_metabox_id',
        'YDG Dictionary',
        'ydgdict_metabox_html',
        'post'
    );
}
add_action('add_meta_boxes', 'ydgdict_add_article_metabox');






/**
 * the entry points and their callback functions
 * 
 */

// uploads new entries and retrieves all from database
function ydgdict_save_entries()
{
    if ( !check_ajax_referer( 'metabox_nonce' ) ) die();
    
    // global $wpdb;
    // global $post;
    $my_post = stripslashes_deep($_POST);


    
    // error_log( print_r( $my_post, true ) );




    if ( array_key_exists( 'ydgdict_rows', $my_post ) )
    {
        // EACH INSERT ID IS SAVED BACK INTO THE ARRAY. HENCE $row MUST BE A REFERENCE
        foreach ( $my_post['ydgdict_rows'] as &$row )
        {

            // SKIP ROW IF THERE IS NO ENTRY 
            if ( empty( $row['entry'] ) ) continue;

            // CORRECT THE BLOG POST ID
            if ( !empty( $row['blog_post_ids'] ) )
            {
                foreach ( $row['blog_post_ids'] as &$blog_post_id )
                {
                    $blog_post_id['primary_post'] = ( $row['entry_blog_post_primary'] === $blog_post_id['post_id'] ) ? 1 : 0;
                    // error_log( $row['entry'] . ": " . $blog_post_id['post_id'] . " " . $blog_post_id['primary_post'] );
                }
            }

            // CORRECT VERB PREFIXES
            if ( ! array_key_exists( 'prefix', $row ) )
            {
                $row['prefix'] = null;
            }

            // CORRECT TYPE
            if ( empty( $row['entry_type'] ) )
            {
                $row['entry_type'] = 'phrase';
            }

            // INSERT OR UPDATE ENTRY POST
            if ( empty( $row['entry_post_id'] ) ) 
            {
                // INSERT NEW ENTRY
                $row['entry_post_id'] = ydgdict_insert_entry_post( $row );
            } else
            {
                // UPDATE EXISTING ENTRY
                $row['entry_post_id'] = ydgdict_update_entry_post( $row ); // TODO: EVERY SINGLE ENTRY IS UPDATED EVERYTIME EVEN IF THERE IS NO CHANGE
            }

            // IF STILL EMPTY, GIVE UP      TODO: DO SOMETHING MORE USEFUL?
            if ( empty( $row['entry_post_id'] ) ) continue; 

            // ENTER EXTRA ENTRY INFO
            if ( 'noun' == $row['entry_type'] )
            {
                ydgdict_db_insert_noun( $row['entry_post_id'], $row['gender'], $row['plural'] );
            }

            if ( 'verb' == $row['entry_type'] )
            {
                ydgdict_db_insert_verb( $row['entry_post_id'], $row['pres_part'], $row['past_part'], $row['preterit'], $row['aux'] );
            }

            // ENTER IDEAS
            if ( !array_key_exists( 'ideas', $row ) ) continue;
            foreach ( $row['ideas'] as $idea )
            {
                if ( !empty( $idea['idea'] ) )
                {
                    ydgdict_db_insert_idea( $row['entry_post_id'], $idea['idea'], $idea['idea_note'], $idea['idea_id'] );
                }
            }
        }
        unset( $row );



        // THERE ARE TWO WAYS TO SET THE PARENT ID
        // WITH AN ENTRY ID DIRECTLY ( THIS IS ONLY POSSIBLE IF THAT ENTRY WAS LOADED FROM THE DATABASE )
        // OR BY THE ROW NUMBER ( THIS IS NECESSARY FOR PARENTS THAT WERE JUST ADDED. IE THEY HAVENT BEEN THROUGHT THE DB YET )
        foreach ( $my_post['ydgdict_rows'] as $row ) 
        {
            // IF entry_parent_id IS SET, THE PARENT ID HAS ALREADY BEEN SET. SKIP THIS ENTRY
            // IF entry_parent_row IS EMPTY, IT DOESNT HAVE A PARENT, SKIP IT TOO
            if ( isset( $row['entry_parent_id'] ) || empty( $row['entry_parent_row'] ) ) continue;
            
            // OTHERWISE entry_parent_row HOLDS THE PARENT'S POSITION IN THE ARRAY

            // USE entry_parent_row TO JUMP TO THE CORRECT ENTRY AND GET THE ID
            // THE ID WAS SET IN THE PREVIOUS FOREACH LOOP ABOVE
            $parent_id = $my_post['ydgdict_rows'][$row['entry_parent_row']]['entry_post_id'];

            // MAKE THE UPDATE
            $args = array( 
                'ID' => $row['entry_post_id'], 
                'post_parent' => $parent_id, 
            );
            wp_update_post( $args );
        }
    }



    
    if ( array_key_exists( 'ydgdict_ideas_to_delete', $my_post ) )
    {
        // this is used to delete single ideas. it is not used when deleting an entry
        foreach ( $my_post['ydgdict_ideas_to_delete'] as $idea_id ) 
        {
            if ( null == $idea_id || 'null' == $idea_id ) continue;
            ydgdict_db_delete_idea( $idea_id );
        }
    }

    if ( array_key_exists( 'ydgdict_entries_to_delete', $my_post ) )
    {
        // deleting entries also deletes associated entry info and ideas
        foreach ( $my_post['ydgdict_entries_to_delete'] as $entry_id ) 
        {
            if ( null == $entry_id || 'null' == $entry_id ) continue;
            ydgdict_delete_entry_post( $entry_id, $my_post['ydgdict_blog_post_id'] );
        }
    }







    $args = array(
        'post_type' => 'entry',
        'posts_per_page' => -1, // DO NOT CHANGE THIS
        ( ( 'entry' == $my_post['ydgdict_post_type'] ) ? 'p' : 'ydgdict_blog_post_id' ) => $my_post['ydgdict_blog_post_id'],
    );
    $ydgdict_query = new WP_Query( $args );





    if ( $ydgdict_query->have_posts() ) : 
        
        while ( $ydgdict_query->have_posts() ) : $ydgdict_query->the_post(); 

            // the loop is necessary to trigger the 'the_post' hook which I've used to add extra info to each post. 
        endwhile; 

        wp_reset_postdata();
    endif; 


    // error_log( print_r( $ydgdict_query->posts, true ) );


    echo json_encode( $ydgdict_query->posts );
    die();
}
// ENTRY POINT
// post new entries then retrieve all entries from the database
add_action( 'wp_ajax_ydgdict_save_entries', 'ydgdict_save_entries' );






// loads entries from the csv file onto the page ready to be posted to the database
function ydgdict_upload_file()
{
    if ( !check_ajax_referer( 'metabox_nonce' ) ) die();

    // create the data to sent back to Javascript
    $lines = ydgdict_get_csv_lines( $_FILES['ydgdict_the_csv_file']['tmp_name'], $_POST['ydgdict_blog_post_id'] );

    echo json_encode( $lines );
    die();
}
// ENTRY POINT
// upload csv file from the metabox
add_action( 'wp_ajax_ydgdict_upload_file', 'ydgdict_upload_file' );






// callback for suggested search results when importing existing entries into an article
function ydgdict_import_entry_search()
{
    if ( !check_ajax_referer( 'metabox_nonce' ) ) die();

    // create the data to sent back to Javascript
    $my_post = stripslashes_deep($_POST);

    $args = array(
        'post_type' => 'entry',
        's' => $my_post['ydgdict_s'],
        'ydgdict_import_entry' => 1,
        'posts_per_page' => 8,
    );
    $ydgdict_query = new WP_Query( $args );

    
    // error_log( "<!--------------------WP_QUERY------------------------------->" );
    // error_log( print_r( $ydgdict_query->request, true ) );
    


    if ( $ydgdict_query->have_posts() ) : 
        
        while ( $ydgdict_query->have_posts() ) : $ydgdict_query->the_post(); 

            // the loop is necessary to trigger the 'the_post' hook which I've used to add extra info to each post. 
        endwhile; 

        wp_reset_postdata();
    endif; 





    echo json_encode( $ydgdict_query->posts );
    die();
}
// ENTRY POINT
// import existing entries into an article
add_action( 'wp_ajax_ydgdict_import_entry_search', 'ydgdict_import_entry_search' );





// reorder search results for the import entry search function
function ydgdict_import_entry_order_search_results( $orderby, $wp_query ) 
{
    if ( ! $wp_query->get( 'ydgdict_import_entry', false ) ) return $orderby; //unchanged

    global $wpdb;
    $search_term = $wp_query->get( 's' );
    
    return " {$wpdb->posts}.post_title LIKE '{$search_term}' DESC, {$wpdb->posts}.post_title LIKE '{$search_term}%' DESC, {$wpdb->posts}.post_title LIKE '%{$search_term}%' DESC ";
}
add_filter( 'posts_orderby', 'ydgdict_import_entry_order_search_results', 20, 2 ); // priority 20 is important