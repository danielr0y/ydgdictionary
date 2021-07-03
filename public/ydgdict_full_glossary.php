<?php




// CHANGE THE NUMBER OF POST SHOWN ON THE INITIAL PAGE LOAD/ HOw MANY POSTS ARE LAZY LOADED
if ( !defined( 'YDGDICT_ENTRIES_POST_PER_PAGE' ) ) define( 'YDGDICT_ENTRIES_POST_PER_PAGE', 50 );

// POSTS PER PAGE IS USUALLY -1 MEANING GET ALL
// FILTER IT ON THE ARCHIVE PAGE
function ydgdict_set_entries_archive_post_per_page( $wp_query ) 
{
    if ( ! $wp_query->is_archive( 'entry' ) || is_admin() ) return;
    
    // TO EDIT, CHANGE CONSTANT DEFINED ABOVE
    $wp_query->set( 'posts_per_page', YDGDICT_ENTRIES_POST_PER_PAGE );
}
add_action( 'pre_get_posts', 'ydgdict_set_entries_archive_post_per_page' );





function ydgdict_add_archive_query_vars( $qvars )
{
    $qvars[] = 's_dest';
    return $qvars;
}
add_filter( 'query_vars', 'ydgdict_add_archive_query_vars' );






/* 
 * allows search on our archive pages without jumping to the search template
 */
function ydgdict_search_template( $template ) 
{
    if ( is_tax( 'word_type' ) )
    {
        return locate_template( "taxonomy-word_type" );
    }
    else if ( is_post_type_archive ( 'entry' ) ) 
    {
        return locate_template( "archive-entry" );
    }

    return $template;
}
add_filter( 'search_template', 'ydgdict_search_template' );







/* 
 * makes taxonomy term queries on our archive pages jumping to the taxonomy template
 */
function ydgdict_entry_archive_template( $template ) 
{
    if ( ! is_tax( 'word_type' ) ) return $template;
    
    return locate_template( "taxonomy-word_type" );
}
add_filter( 'archive_template', 'ydgdict_entry_archive_template' );







/* 
 * this does not include any ideas fields in the select clause. no ideas fields will be returned
 * 
 * this just joins the ideas table so there WHERE clause can include idea fields
 * 
 * the ideas are added into the wp_query object later with the the_post filter
 * this is neccessary because the results are grouped together by entry ID
 */
function ydgdict_include_ideas_in_search( $join, $wp_query ) 
{
    if ( ! $wp_query->is_archive( 'entry' ) || ! $wp_query->is_search() || is_admin() ) return $join; // unchanged

    global $wpdb;

    $join .= " INNER JOIN {$wpdb->prefix}ydgdict_ideas ideas ON ( {$wpdb->posts}.ID = ideas.entry_id ) ";
    
    return $join;
}
add_filter( 'posts_join', 'ydgdict_include_ideas_in_search', 10, 2 );







/* 
 * group search results together by entry
 */
function ydgdict_group_search_results_by_entry( $groupby, $wp_query ) 
{
    if ( ! $wp_query->is_archive( 'entry' ) || ! $wp_query->is_search() || is_admin() ) return $groupby; // unchanged

    global $wpdb;

    return " {$wpdb->posts}.ID ";
}
add_filter( 'posts_groupby', 'ydgdict_group_search_results_by_entry', 10, 2 );







/* 
 * order dictionary search results by cascading options
 */
function ydgdict_order_search_results_by_post_excerpt( $orderby, $wp_query ) 
{
    if ( ! $wp_query->is_archive( 'entry' ) || ! $wp_query->is_search() || is_admin() ) return $orderby; //unchanged

    global $wpdb;
    $search_term = $wp_query->get( 's' );
    
    return " {$wpdb->posts}.post_excerpt LIKE '{$search_term}' DESC, {$wpdb->posts}.post_excerpt LIKE '{$search_term}%' DESC, {$wpdb->posts}.post_excerpt LIKE '%{$search_term}%' DESC ";
}
add_filter( 'posts_orderby', 'ydgdict_order_search_results_by_post_excerpt', 15, 2 ); // priority 15 is important







function ydgdict_specify_a_seach_destination( $where, $wp_query ) 
{
    if ( ! $wp_query->is_archive( 'entry' ) || ! $wp_query->is_search() || is_admin() ) return $where; // unchanged

    global $wpdb;

    /* 
     * PART OF THE SQL QUERY READS 
     * 
     *  WHERE 1=1 AND (
     *      (
     *          ( wp_posts.post_title LIKE %abc% ) OR           -- THESE LINES --
     *      )
     *  )
     * 
     * IF THERE ARE MORE THAN ONE SEARCH KEYWORDS IT WOULD LOOK SOMETHING LIKE THIS
     * 
     *  WHERE 1=1 AND (
     *      (
     *          ( wp_posts.post_title LIKE %abc% ) OR           -- THESE LINES --
     *      ) OR
     *      (
     *          ( wp_posts.post_title LIKE %xyz% ) OR           -- THESE LINES --
     *      )
     *  )
     * 
     * WE WANT TO DUPLCIATE THE LINE(S) CONTAINING THE SEACH TERM(S) 
     * SWAPPING OUT wp_posts.post_title FOR $fields_to_include (defiend below)
     * 
     * 
     * DEPENDING ON WHERE WE SHOULD SEARCH ( 's_dest' )
     * WE'LL END UP WITH SOMETHING LIKE THIS
     * 
     *  WHERE 1=1 AND (
     *      (
     *          ( wp_posts.post_title LIKE %abc% ) OR
     *          ( ydgdict_nouns.gender LIKE %abc% ) OR
     *          ( ydgdict_nouns.plural LIKE %abc% ) OR
     *      ) OR
     *      (
     *          ( wp_posts.post_title LIKE %xyz% ) OR
     *          ( ydgdict_nouns.gender LIKE %xyz% ) OR
     *          ( ydgdict_nouns.plural LIKE %xyz% ) OR
     *      )
     *  )
     */


    /* 
     * SO... 
     * 
     * GET THE $the_line FROM $where
     * 
     * EACH INSTANCE OF $the_line IS A DIFFERENT SEARCH KEYWORD and therefore a $line_to_copy
     */
    $field_to_replace = preg_quote( "{$wpdb->posts}.post_title" );

    // this was broken out into a function for use in another part below
    function get_the_line( $field )
    {
        return "/\(\s*{$field}\s+LIKE\s+'.*?'\)\s*OR/";
    }

    $the_line = get_the_line( $field_to_replace );

    preg_match_all( $the_line, $where, $lines_to_copy );

    $lines_to_copy = $lines_to_copy[0];
    /* 
     *  NOW WE HAVE
     * 
     *  $lines_to_copy: array(
     *      [0] => '( wp_posts.post_title LIKE %abc% ) OR'
     *      [1] => '( wp_posts.post_title LIKE %xyz% ) OR'
     *  )
     */
    

    /* 
     * DETERMINE WHICH FIELDS WE SHOULD INCLUDE IN THE SQL QUERY
     */
    switch ( $wp_query->get( 's_dest', 0 ) )
    {
        case 0: // EVERYWHERE
            $fields_to_include = array( "verbs.pres_part", "verbs.past_part", "verbs.preterit", "verbs.aux", "nouns.gender", "nouns.plural", "ideas.idea" );
            break;
        case 1: // JUST THE ENTRY (AND ITS FORMS)
            $fields_to_include = array( "verbs.pres_part", "verbs.past_part", "verbs.preterit", "verbs.aux", "nouns.gender", "nouns.plural" );
            break;
        case 2: // JUST THE IDEAS
            $fields_to_include = array( "ideas.idea" );
            break;
    }
    

    /* 
     * FOR EACH OF THE $fields_to_include 
     * CREATE A $new_line_to_insert INTO THE SQL QUERY 
     * BY DUPLICATING EACH $line_to_copy 
     * SWAPPING OUT $field_to_replace WITH $field_to_include
     */
    foreach ( $fields_to_include as $field_to_include )
    {
        foreach ( $lines_to_copy as $line_to_copy )
        {
            // CREATE THE LINE TO INSERT 
            $new_line_to_insert = preg_replace( "/{$field_to_replace}/", $field_to_include, $line_to_copy, 1 );
            
            /* 
             * AND INSERT IT INTO THE SQL QUERY
             * 
             * BY REPLACING THE $line_to_copy WITH 
             * $line_to_copy PLUS $new_line_to_insert
             */
            $line_to_copy = preg_quote( $line_to_copy );
            $where = preg_replace( "/{$line_to_copy}/", "$0 " . $new_line_to_insert, $where, 1 );
        }
    }

    /* 
     * FINALLY, IF 'SEARCH JUST THE IDEAS' IS SELECTED
     * REMOVE $the_line (wp_posts.post_title) AND (wp_posts.post_excerpt) FROM THE QUERY
     */
    if ( $wp_query->get( 's_dest' ) == 2 )
    {
        // remove post_title
        $where = preg_replace( $the_line, '', $where, 1 );

        // remove post_excerpt
        $post_excerpt = get_the_line( "{$wpdb->posts}.post_excerpt");
        $where = preg_replace( $post_excerpt, '', $where, 1 );
    }
    // error_log( "sql: " . print_r( $where, true ) );

    return $where;
}
add_filter( 'posts_where', 'ydgdict_specify_a_seach_destination', 10, 2 );







// FREE USE FUNCTION
// CALL THIS FUNCTOIN FROM WHEREVER YOUD LIKE TO DISPLAY THE GLOSSARY
// THIS FUNCTION ASSUMES THE WP_Query EXISTS AND CONTAINS POSTS OF TYPE 'entry'
// THIS FUNTION IS CALLED BY THE SHORTCODE ydgdict_short_display_ful_glossary
function ydgdict_display_full_glossary()
{
    global $wp_query;

    // error_log( print_r( $wp_query->request, true ) );
    ob_start(); 

    ?>

    <div id="ydgdict_glossary_container">

        <?php include( YDGDICT_PLUGIN_DIR . 'public/ydgdict_x_glossary_part_the_search.php' ); ?>

        <ul id="ydgdict_glossary" data-ydgdict_page="1" data-ydgdict_total_pages="<?php echo $wp_query->max_num_pages; ?>">
        
            <?php 

            if ( $wp_query->have_posts() ) : 

                while ( $wp_query->have_posts() ) : $wp_query->the_post(); 

                    include( YDGDICT_PLUGIN_DIR . 'public/ydgdict_x_glossary_part_the_li.php' );

                endwhile; 

            else : echo "no entries were found.";
            
            endif; 
            
            ?>

        </ul><!-- .ydgdict_glossary -->

    </div>

    <?php 

    return ob_get_clean();
}







// CALLBACK FUNCTION FOR THE GLOSSARY LAZY LOAD AJAX HANDLER.
function ydgdict_glossary_get_more_entries()
{
    if ( !check_ajax_referer( 'glossary_nonce' ) ) die();
    
    $my_post = stripslashes_deep( $_POST );

    // error_log( '$_POST: ' . print_r( $my_post, true ) );

    $args = array(
        'post_type' => 'entry',
        'posts_per_page'  => YDGDICT_ENTRIES_POST_PER_PAGE,
    );

    if ( ! empty( $my_post['paged'] ) )
    {
        $paged_args = array(
            'paged' => $my_post['paged'],
        );
        $args = array_merge( $args, $paged_args );
    }

    if ( ! empty( $my_post['s'] ) )
    {
        $search_args = array(
            's' => $my_post['s'],
            's_dest' => $my_post['s_dest'],
        );
        $args = array_merge( $args, $search_args );
    }

    if ( ! empty( $my_post['word_type'] ) )
    {
        $tax_args = array(
            'tax_query' => array(
                array(
                    'taxonomy' => 'word_type',
                    'field'    => 'slug',
                    'terms'    => $my_post['word_type'],
                ),
            ),
        );
        $args = array_merge( $args, $tax_args );
    }

    $ydgdict_query = new WP_Query( $args );

    // error_log( print_r( $ydgdict_query, true ) );

    ob_start(); 
    
    if ( $ydgdict_query->have_posts() ) :
        
        ?>

        <template id="ydgdict_pre_loaded_entries">
    
        <?php
            
        while ( $ydgdict_query->have_posts() ) : $ydgdict_query->the_post(); 

            include( YDGDICT_PLUGIN_DIR . 'public/ydgdict_x_glossary_part_the_li.php' );

        endwhile; 
        
        wp_reset_postdata();
        
        ?>

        </template>
    
        <?php
    endif; 

    echo json_encode( ob_get_clean() );
    die();
}
add_action('wp_ajax_nopriv_ydgdict_glossary_get_more_entries', 'ydgdict_glossary_get_more_entries');
add_action('wp_ajax_ydgdict_glossary_get_more_entries', 'ydgdict_glossary_get_more_entries');