<?php

function ydgdict_init_cpt_entry()
{ 
    $args = array(
        'labels'        => array(
            'name'          => 'Dictionary',
            'singular_name' => 'Entry',
        ),
        'public'        => true,
        'show_ui'       => true,
        'has_archive'   => 'dictionary',
        'rewrite'       => array( 'slug' => 'dictionary/meaning' ),
        'hierarchical'  => true,
        'publicly_queryable' => true,
        // 'show_in_rest'  => true,
        // 'rest_base'     => 'dictionary',
        'menu_icon'     => 'dashicons-minus',
        'supports'      => array( 'title', 'editor', 'custom-fields', 'excerpt', 'page-attributes', 'comments' ),
    );
    register_post_type( 'entry', $args );
}
add_action( 'init', 'ydgdict_init_cpt_entry' );






/* 
 * ideas have to be added to the search results after the wp_query object is created
 * because search results are ultimately grouped by entry.
 * 
 * if the ideas table was joined to the entries table and one entry had for example
 * five ideas, only the last idea would be spared when all of the results for that
 * entry are eventually grouped together
 *
 */
function ydgdict_add_ideas_to_entries( $post, $wp_query ) 
{
    if ( 'entry' != $post->post_type ) return;
    
    // error_log( "<!-----------------------the_post action hook---------------------------->" );
    // error_log( print_r( $post->blog_post_ids, true) );

    $post->ideas = ydgdict_db_get_ideas( $post->ID );

    $post->entry_audio = get_post_meta( $post->ID, 'ydgdict_entry_audio', true );

    $post->blog_post_ids = ydgdict_db_get_blog_post_ids( $post->ID );
    // $post->blog_post_ids = get_post_meta( $post->ID, 'blog_post_id' ); 
    // $post->blog_post_ids = get_post_meta( $post->ID, 'blog_post_id', true ); 

    $post->entry_parent = array( 'post_id' => $post->entry_parent, 'post_title' => ( 0 == $post->entry_parent ) ? '' : get_the_title( $post->entry_parent ) );
    // error_log( print_r( $post->blog_post_ids, true) );
    
    $both = ydgdict_get_word_type_and_prefix( $post->ID );

    $post->entry_type = $both['type'];
    $post->prefix = $both['prefix'];
}
add_action( 'the_post', 'ydgdict_add_ideas_to_entries', 10, 2 );









function ydgdict_include_extra_entry_info( $clauses, $wp_query ) 
{
    if ( ( ! $wp_query->is_archive( 'entry' ) && 'entry' != $wp_query->get( 'post_type' ) ) ) return $clauses; //unchanged

    // error_log( "<!----------------------- this could be anything that requests our entries ---------------------------->" );

    global $wpdb;

    $select  = &$clauses['fields'];
    $select .= ", {$wpdb->posts}.ID as entry_post_id, {$wpdb->posts}.post_title as entry, {$wpdb->posts}.post_parent as entry_parent, ";
    $select .= "verbs.pres_part, verbs.past_part, verbs.preterit, verbs.aux, ";
    $select .= "nouns.gender, nouns.plural ";

    $join  = &$clauses['join'];
    $join .= " LEFT JOIN {$wpdb->prefix}ydgdict_verbs verbs ON ( {$wpdb->posts}.ID = verbs.entry_id ) ";
    $join .= " LEFT JOIN {$wpdb->prefix}ydgdict_nouns nouns ON ( {$wpdb->posts}.ID = nouns.entry_id ) ";
    
    return $clauses;
}
add_filter( 'posts_clauses', 'ydgdict_include_extra_entry_info', 10, 2 );









function ydgdict_order_entries_by_post_excerpt( $orderby, $wp_query ) 
{
    if ( ( ! $wp_query->is_archive( 'entry' ) && 'entry' != $wp_query->get( 'post_type' ) ) ) return $orderby; //unchanged

    global $wpdb;

    return " {$wpdb->posts}.post_excerpt";
}
add_filter( 'posts_orderby', 'ydgdict_order_entries_by_post_excerpt', 10, 2 );







/* CRUD */



// helpers for insert and update entry post type
function ydgdict_set_entry_word_type( $entry_post_id, $entry_type, $prefix )
{
    // CORRECT THE ENTRY TYPE
    if ( 'verb' == $entry_type && ! empty( $prefix ) )
    {
        $entry_type = $prefix;
    }
    
    $taxonomy = 'word_type';
    $term = term_exists( $entry_type, $taxonomy );

    wp_set_post_terms( $entry_post_id, $term, $taxonomy );
}







function ydgdict_db_update_entry_wp_post_relationships( $entry_post_id, $blog_post_ids )
{
    // SET THE BLOG POST IDS
    foreach ( $blog_post_ids as $blog_post_id )
    {
        ydgdict_db_insert_entry_wp_post_relationship( $entry_post_id, $blog_post_id );
    }
}







/*
 *
 * returns: true if entry_id is still associated with other posts
 *          false if it is not associated with any other post
 * 
 * use on delete post
 */
function ydgdict_dissociate_entry_from_post( $entry_id, $post_id )
{
    global $wpdb;
    
    // remove the entry_id post_id pair
	$wpdb->delete( 
		"{$wpdb->prefix}ydgdict_entries_wp_posts", 
		array( 
			'entry_id' => $entry_id,
			'post_id' => $post_id
		)
    );
    
    // find out if the entry is being used elsewhere
    $sql = "SELECT post_id, entry_id, primary_post FROM {$wpdb->prefix}ydgdict_entries_wp_posts WHERE entry_id = {$entry_id}";
    $results = $wpdb->get_results( $sql, ARRAY_A );

    if ( empty( $results ) ) return false; // THIS ENTRY ISNT ASSOCIATED WITH ANY OTHER POSTS. IT CAN BE DELETED

    // CHECK IF ONE OF THE REMAINING ENTRIES IS THE PRIMARY
    if ( false !== array_search( '1', array_column( $results, 'primary_post' ) ) ) return true; // THIS ENTRY IS ASSOCIATED WITH OTHER POSTS. DONT DELETE IT
    
    // IF THERE IS NO PRIMARY POST, UPDATE THE FIRST ONE
    return ( $wpdb->update( 
        "{$wpdb->prefix}ydgdict_entries_wp_posts", 
        array( 
            'primary_post' => 1, 
        ), // INSERT
        array( 
            'post_id' => $results[0]['post_id'], 
            'entry_id' => $results[0]['entry_id'], 
        ), // WHERE
        array( 
            '%d' 
        ), // INSERT INT
        array( 
            '%d', 
            '%d' 
        ) // WHERE IS ALSO INT
    ) ) ? true : false;
}







/* 
 *  $entry = array 
 *  (
 *      'entry' => string $entry,
 *      'entry_type' => string $entry_type, // TYPE NOT INCLUDING VERB PREFIXES!!!
 *      'blog_post_id' => int $blog_post_id,
 *  );
 */
function ydgdict_insert_entry_post( $entry )
{
    // INSERT

    // entries are ordered by excerpt in the archive, not title
    // if you want to move an entry without changing its title, update excerpt in the edit page
    $args = array(
        'post_type'     => 'entry', // WP POST TYPE not entry type
        'post_title'    => $entry['entry'], 
        'post_excerpt'    => $entry['entry'], 
        'post_author'    => 0, 
        'post_status'   => 'publish',
        'comment_status'   => 'open',
    );

    if ( isset( $entry['entry_parent_id'] ) && -1 != $entry['entry_parent_id'] )
    {
        $args = array_merge( $args, array(
            'post_parent'   => $entry['entry_parent_id'],
        ) );
    }
    
    $entry_post_id = wp_insert_post( $args, true );

    // error_log( print_r( $entry_post_id, true ) );

    // LOG ERRORS AND RETURN NULL
    if ( is_wp_error( $entry_post_id ) ) 
    {
        error_log( $entry_post_id->get_error_message() );
        return NULL;
    }

    // UPDATE THE BLOG POST IDS
    ydgdict_db_update_entry_wp_post_relationships( $entry_post_id, $entry['blog_post_ids'] );

    // OTHERWISE SET THE TAXONOMY TERMS
    ydgdict_set_entry_word_type( $entry_post_id, $entry['entry_type'], $entry['prefix'] );

    // UPDATE THE AUDIO
	if ( isset( $entry[ 'entry_audio' ] ) ) {
		update_post_meta( $entry_post_id, 'ydgdict_entry_audio', $entry[ 'entry_audio' ] );
	}

    // RETURN THE NEW ENTRY POST ID
    return $entry_post_id;
}







/* 
 *  $entry = array 
 *  (
 *      'entry_post_id' => int $entry_post_id,
 *      'entry' => string $entry,
 *      'entry_type' => string $entry_type, // TYPE NOT INCLUDING VERB PREFIXES!!!
 *  );
 */
function ydgdict_update_entry_post( $entry )
{  
    // entries are ordered by excerpt in the archive, not title
    // if you want to move an entry without changing its title, update excerpt in the edit page
    $args = array(
        'ID'            => $entry['entry_post_id'],
        'post_title'    => $entry['entry'],
    //    'post_excerpt'  => $entry['entry'], 
    );

    if ( isset( $entry['entry_parent_id'] ) && -1 != $entry['entry_parent_id'] )
    {
        $args = array_merge( $args, array(
            'post_parent'   => $entry['entry_parent_id'],
        ) );
    }

    $entry_post_id = wp_update_post( $args, true );

    if ( is_wp_error( $entry_post_id ))
    {
        error_log( $entry_post_id->get_error_message() );
        return NULL;
    }

    // UPDATE THE BLOG POST IDS
    ydgdict_db_update_entry_wp_post_relationships( $entry_post_id, $entry['blog_post_ids'] );

    // OTHERWISE UPDATE THE TAXONOMY TERMS
    ydgdict_set_entry_word_type( $entry_post_id, $entry['entry_type'], $entry['prefix'] );

    // UPDATE THE AUDIO
	if ( isset( $entry[ 'entry_audio' ] ) ) {
		update_post_meta( $entry_post_id, 'ydgdict_entry_audio', $entry[ 'entry_audio' ] );
	}

    return $entry_post_id;
}







// hook for delete entry post type
function ydgdict_on_delete_entry_post( $entry_id ) 
{
    $parent = get_post( $entry_id );
    
    // ONLY HOOK INTO ENTRY POSTS
    if ( $parent->post_type !== 'entry' ) return;

    global $wpdb;
    // delete all associated entry info and ideas 
	$wpdb->delete( 
		"{$wpdb->prefix}ydgdict_nouns", 
		array( 
			'entry_id' => $entry_id
		)
	);
	$wpdb->delete( 
		"{$wpdb->prefix}ydgdict_verbs", 
		array( 
			'entry_id' => $entry_id
		) 
    );
	$wpdb->delete( 
		"{$wpdb->prefix}ydgdict_ideas", 
		array( 
			'entry_id' => $entry_id
		) 
    );
}
add_action( 'delete_post', 'ydgdict_on_delete_entry_post' );







function ydgdict_delete_entry_post( $entry_id, $post_id )
{
    // remove the entry from the many-to-many associative array first
    // if the entry is still associated with other posts, keep it
    if ( ydgdict_dissociate_entry_from_post( $entry_id, $post_id ) ) return;

    // otherwise, delete the post
    return wp_delete_post( $entry_id, true ); // HOOK INTO THIS WITH ydgdict_on_delete_entry_post TO CHANGE MY DATABASE
}


/* CRUD END */









// the admin page: entries/dictionary that shows every entry post
function ydgdict_entry_post_type_columns( $columns )
{
	/** Add the blog_post_id column **/
	$myCustomColumns = array(
		'blog_post_ids' => 'mentioned in',
	);
	$columns = array_merge( $columns, $myCustomColumns );

    return $columns;
}
add_filter( 'manage_entry_posts_columns', 'ydgdict_entry_post_type_columns' );






// the admin page: entries/dictionary that shows every entry post
function ydgdict_entries_blog_post_id_column( $column_name, $entry_post_id )
{
    if ( $column_name == 'blog_post_ids' ) 
    {
        $blog_post_ids = ydgdict_db_get_blog_post_ids( $entry_post_id );

        foreach ( $blog_post_ids as $key => $blog_post_id )
        {
            if ( $key ) echo ', ' ;?><a href="<?php echo get_edit_post_link( $blog_post_id['post_id'] ); ?>" ><?php echo $blog_post_id['post_title']; ?></a><?php 
        }
	}
}
add_action( 'manage_entry_posts_custom_column', 'ydgdict_entries_blog_post_id_column', 10, 2 );




function entry_excerpt_meta_box( $post ) 
{
    ?>
    <label class="screen-reader-text" for="excerpt">sort title</label>
    <textarea rows="1" cols="40" name="excerpt" id="excerpt"><?php echo $post->post_excerpt; // textarea_escaped ?></textarea>
    <p>entries are sorted alphabetically according to this field NOT the title.</p>
    <?php
}





function ydgdict_entry_metaboxes()
{
    remove_meta_box( 'postexcerpt', 'entry', 'normal' );
    add_meta_box( 
        'postexcerpt', 
        'sort title', 
        'entry_excerpt_meta_box', 
        'entry', 
        'normal', 
        'high' 
    );
}
add_action( 'add_meta_boxes', 'ydgdict_entry_metaboxes' );