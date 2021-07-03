<?php







function ydgdict_filter_entry_results_by_blog_post( $clauses, $wp_query ) 
{
    // are we in the right place?
    if ( $wp_query->is_main_query() ) return $clauses; // unchanged

    // is the qurey from our plugin?
    if ( 'entry' != $wp_query->get( 'post_type' ) || ! isset( $wp_query->query_vars[ 'ydgdict_blog_post_id' ] )) return $clauses; //unchanged

    // error_log( "<!----------------------- this should be an article/post page ---------------------------->" );
    
    global $wpdb;

    if ( $blog_post_id = $wp_query->get( 'ydgdict_blog_post_id' ) )
    {
        $clauses['join'] .= " INNER JOIN {$wpdb->prefix}ydgdict_entries_wp_posts entriesposts ON ( {$wpdb->posts}.ID = entriesposts.entry_id ) ";
        $clauses['where'] = " AND ( entriesposts.post_id = {$blog_post_id} ) "; // ( entriesposts.primary_post = 1 AND ~ )
    }

    return $clauses;
}
add_filter( 'posts_clauses', 'ydgdict_filter_entry_results_by_blog_post', 10, 2 );









// FREE USE FUNCTION
// CALL THIS FUNCTION FROM WITHIN THE LOOP TO LOOP TO DISPLAY THE CURRENT POSTS ENTRIES IN A GLOSSARY
function ydgdict_display_post_glossary()
{
    if ( !is_singular( 'post' ) ) return;

    // TODO: if (! in loop) return;

    $args = array(
        'post_type' => 'entry',
        'posts_per_page'  => -1, // DO NOT CHANGE THIS
        'ydgdict_blog_post_id'    => get_the_ID(),
    );
    $ydgdict_query = new WP_Query( $args );

    if ( $ydgdict_query->have_posts() ) :

        ob_start();
    
        ?>

        <div id="ydgdict_glossary_container">
    
            <p id="ydgdict_bold ydgdict_italic">** vocab **</p>
            
            <ul id="ydgdict_glossary">

                <?php
    
                while ( $ydgdict_query->have_posts() ) : $ydgdict_query->the_post(); 

                    include( YDGDICT_PLUGIN_DIR . 'public/ydgdict_x_glossary_part_the_li.php' );

                endwhile; 

                wp_reset_postdata();
                
                ?>
    
            </ul><!-- .ydgdict_glossary -->
    
        </div>
        
        <?php

    endif; 

    return ob_get_clean();
}



// adds the glossary to the end of the articles
// filter the content instead of using the single.php template
// this way its in the content div and is for example hidden when non-members have exceeded max free views
function filter_the_content_on_articles( $content ) 
{
    if ( !is_singular( 'post' ) || !in_the_loop() || !is_main_query() ) return $content;

    // if you dont remove this filter WP will add P tags to all of the blank spaces in my code. breaks the formatting
    remove_filter('the_content', 'wpautop');

    // put the post glossary on the end of the content
    return $content . ydgdict_display_post_glossary();
}
// add_filter( 'the_content', 'filter_the_content_on_articles', 1 );