<?php 


if ( !defined( 'ABSPATH' ) ) exit;



function ydgdict_db_insert_sentence( $german, $english, $audio, $post_id = null , $sentence_id = null)
{
	global $wpdb;
	
    $result = $wpdb->replace(
        "{$wpdb->prefix}ydgdict_sentences", 
        array(
            'sentence_id' => $sentence_id,
            'german' => $german,
            'english' => $english,
            'audio' => $audio,
            'post_id' => $post_id // this just helps in case the sentence hasnt 
            // been assigned to an idea. that its not just floating around in space. 
            // at least we know what article it was mentioned in
        )
    );
    if ( false === $result )
        return false;

	return $wpdb->insert_id;
}



function ydgdict_assign_sentence_to_idea( $sentence_id, $idea_id, $prev_idea_id = null )
{
    global $wpdb;

    if ( empty( $prev_idea_id ) )
    {
        $result = $wpdb->insert( 
            "{$wpdb->prefix}ydgdict_ideas_sentences", 
            array(
                'sentence_id' => $sentence_id,
                'idea_id' => $idea_id
            ), 
            array( '%d', '%d' )
        );

        return ( false === $result ) ? false : $idea_id;
    }

    if ( empty( $idea_id ) ) // && ! empty( prev_idea_id )
    {
        $result = $wpdb->delete( 
            "{$wpdb->prefix}ydgdict_ideas_sentences", 
            array( 
                'sentence_id' => $sentence_id,
                'idea_id' => $prev_idea_id
            ), 
            array( '%d', '%d' )
        );

        return ( false === $result ) ? false : $idea_id;
    }

    $result = $wpdb->update( 
        "{$wpdb->prefix}ydgdict_ideas_sentences", 
        array( 
            'sentence_id' => $sentence_id,
            'idea_id' => $idea_id
        ), 
        array( 
            'sentence_id' => $sentence_id,
            'idea_id' => $prev_idea_id
        ), 
        array( '%d', '%d' ), 
        array( '%d', '%d' )
    );

    return ( false === $result ) ? false : $idea_id;
}





function ydgdict_catch_that_image( $post_id ) 
{
    $post = get_post( $post_id );
    if ( !is_object( $post ) ) return false;

    preg_match('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);

    return ( !empty( $matches[1] ) ) ? $matches[1] : false;
}





function ydgdict_db_get_ideas( $entry_post_id )
{
	global $wpdb;

    $query = "SELECT ID AS idea_id, idea, note as idea_note 
            FROM {$wpdb->prefix}ydgdict_ideas 
            WHERE entry_id = " . $entry_post_id;
    
	$results = $wpdb->get_results( $query, ARRAY_A );
	return $results;
}





function ydgdict_db_get_blog_post_ids( $entry_post_id )
{
	global $wpdb;

    $query  = "SELECT post_id, primary_post 
            FROM {$wpdb->prefix}ydgdict_entries_wp_posts 
            WHERE entry_id = " . $entry_post_id;
    
    $results = $wpdb->get_results( $query, ARRAY_A );
    
    foreach( $results as &$row )
    {
        $blog_post = get_post( $row['post_id'] );
        $row['post_title'] = $blog_post->name_in_archive;
    }
	return $results;
}







// delete ideas from the many-to-many and from the ideas table
function ydgdict_db_delete_idea( $idea_id )
{
	global $wpdb;
	
	// delete from ideas by idea_id
    return $wpdb->delete( 
        "{$wpdb->prefix}ydgdict_ideas", 
        array( 
            'ID' => $idea_id
        ) 
    );
}






function ydgdict_db_insert_entry_wp_post_relationship( $entry_post_id, $blog_post_id )
{
	global $wpdb;
	
    return $wpdb->replace( 
        "{$wpdb->prefix}ydgdict_entries_wp_posts", 
        array(
            'entry_id' => $entry_post_id,
            'post_id' => $blog_post_id['post_id'],
            'primary_post' => $blog_post_id['primary_post'],
        )
    );
}






function ydgdict_db_insert_verb( $entry_id, $pres_part, $past_part, $preterit, $aux )
{
	global $wpdb;
	
    return $wpdb->replace( 
        "{$wpdb->prefix}ydgdict_verbs", 
        array(
            'entry_id' => $entry_id,
            'pres_part' => $pres_part,
            'past_part' => $past_part,
            'preterit' => $preterit,
            'aux' => $aux,
        )
    );
}




function ydgdict_db_insert_noun( $entry_id, $gender, $plural )
{
	global $wpdb;
	
    return $wpdb->replace( 
        "{$wpdb->prefix}ydgdict_nouns", 
        array( 
            'entry_id' => $entry_id,
            'gender' => $gender,
            'plural' => $plural
        )
    );
}






// the plugin has it's own options table for things that need to be kept between installs
function ydgdict_db_insert_option( $name, $value )
{
	global $wpdb;
	
    return $wpdb->replace( 
        "{$wpdb->prefix}ydgdict_options", 
        array( 
            'option_name' => $name,
            'option_value' => $value
        )
    );
}






function ydgdict_db_get_option( $option )
{
	global $wpdb;

    $query = "SELECT option_value FROM {$wpdb->prefix}ydgdict_options 
            WHERE option_name = '{$option}'";

	$result = $wpdb->get_row( $query, ARRAY_A );
	if ( empty( $result ) ) return false;

	return $result['option_value'];
}







// inserts into the ideas table and the ideas_entries intermediate table
function ydgdict_db_insert_idea( $entry_id, $idea, $note, $idea_id = NULL) 
{
	global $wpdb;
	
    // insert the new idea first. we need it's ID.
	return $wpdb->replace( 
		"{$wpdb->prefix}ydgdict_ideas", 
		array( 
			'ID' => $idea_id,
			'entry_id' => $entry_id,
			'idea' => $idea,
			'note' => $note
        ) 
	);
	// return $wpdb->insert_id;
}





function ydgdict_get_word_types_and_prefixes_helper( $terms )
{
    $prefixes = array_filter( $terms, function( $type )
    {
        return preg_match( '/_verb/', $type->slug );
    } );

    $types = array_udiff( $terms, $prefixes, function( $term, $verb )
    {
        if ( (array)$term < (array)$verb) {
            return -1;
        } elseif ( (array)$term > (array)$verb) {
            return 1;
        } else {
            return 0;
        }
    } );

    return array( 'types' => $types, 'prefixes' => $prefixes );
}





function ydgdict_get_word_types_and_prefixes()
{
    $terms = get_terms( array(
        'taxonomy' => 'word_type',
        'hide_empty' => false,
    ) );
    if ( is_wp_error( $terms ) ) return false;

    return ydgdict_get_word_types_and_prefixes_helper( $terms );
}





function ydgdict_get_word_type_and_prefix( $entry_post_id )
{
    $terms = get_the_terms( $entry_post_id, 'word_type' );
    if ( is_wp_error( $terms ) || empty( $terms ) ) return false;

    $both = ydgdict_get_word_types_and_prefixes_helper( $terms );

    if ( empty( $both['types'] ) )
    {
        $both['types'][0] = get_term_by('slug', 'verb', 'word_type');
    }

    return array( 
        'type' => $both['types'][0], 
        'prefix' => ( !empty( $both['prefixes'] ) ) ? $both['prefixes'][0] : NULL,
    );
}




function ydgdict_remove_parent_prefixes( $prefixes )
{
    return array_filter( $prefixes, function( $prefix )
    {
        return ( !get_term_children( $prefix->term_id, 'word_type' ) );
    } );
}




function ydgdict_remove_child_prefixes( $prefixes )
{
    return array_filter( $prefixes, function( $prefix )
    {
        $verb = get_term_by('slug', 'verb', 'word_type');
        return ( $prefix->parent == $verb->term_id );
    } );
}





function ydgdict_activate_database()
{
    // dependencies
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    global $wpdb;
    
    $charset_collate = $wpdb->get_charset_collate();


    // TODO: REMOVE THIS BLOCK ONCE THE SERVER IS RUNNING CPTs
    if ( empty( ydgdict_db_get_option( 'updated_to_CPTS' ) ) )
    {
        $sql = "CREATE TABLE {$wpdb->prefix}ydgdict_ideas_entries (
                idea_id int(11) NOT NULL,
                entry_id int(11) NOT NULL,
                PRIMARY KEY  (idea_id,entry_id),
                KEY entry_id (entry_id)
                ) $charset_collate;";
        // dbDelta( $sql );
    }


    // THE TABLES
    $sql = "CREATE TABLE {$wpdb->prefix}ydgdict_options (
        option_name varchar(25) NOT NULL,
        option_value longtext,
        UNIQUE KEY option_name (option_name)
        ) $charset_collate;";
    dbDelta( $sql );


    $sql = "CREATE TABLE {$wpdb->prefix}ydgdict_nouns (
            entry_id int(11) NOT NULL,
            gender enum('der', 'die', 'das') NOT NULL,
            plural varchar(25) DEFAULT NULL,
            UNIQUE KEY entry_id (entry_id)
            ) $charset_collate;";
    dbDelta( $sql );


    $sql = "CREATE TABLE {$wpdb->prefix}ydgdict_verbs (
            entry_id int(11) NOT NULL,
            pres_part varchar(25) NOT NULL,
            past_part varchar(25) NOT NULL,
            preterit varchar(25) NOT NULL,
            aux enum('hat', 'ist') NOT NULL, 
            UNIQUE KEY entry_id (entry_id)
            ) $charset_collate;";
    dbDelta( $sql );


    $sql = "CREATE TABLE {$wpdb->prefix}ydgdict_ideas (
            ID int(11) NOT NULL AUTO_INCREMENT,
            entry_id int(11) NOT NULL,
            idea varchar(255) NOT NULL,
            note varchar(255) DEFAULT NULL,
            KEY entry_id (entry_id),
            PRIMARY KEY  (ID)
            ) $charset_collate;";
    dbDelta( $sql );


    $sql = "CREATE TABLE {$wpdb->prefix}ydgdict_entries_wp_posts (
            post_id bigint(20) unsigned NOT NULL,
            entry_id bigint(20) unsigned NOT NULL,
            primary_post tinyint(1) unsigned NOT NULL DEFAULT 1,
            KEY post_id (post_id),
            KEY entry_id (entry_id),
            PRIMARY KEY (post_id,entry_id)
            ) $charset_collate;";
    dbDelta( $sql );


    $sql = "CREATE TABLE {$wpdb->prefix}ydgdict_sentences (
            sentence_id int(11) NOT NULL AUTO_INCREMENT,
            german varchar(255) NOT NULL,
            english varchar(255) NOT NULL,
            audio varchar(255) DEFAULT NULL,
            post_id bigint(20) unsigned,
            PRIMARY KEY (sentence_id)
            ) $charset_collate;";
    dbDelta( $sql );



    $sql = "CREATE TABLE {$wpdb->prefix}ydgdict_ideas_sentences (
            idea_id int(11) NOT NULL,
            sentence_id int(11) NOT NULL,
            KEY idea_id (idea_id),
            KEY sentence_id (sentence_id),
            PRIMARY KEY (idea_id,sentence_id)
            ) $charset_collate;";
    dbDelta( $sql );



    $sql = "CREATE TABLE {$wpdb->prefix}ydgdict_flashcards (
            ID int(11) NOT NULL AUTO_INCREMENT,
            sentence_id int(11) NOT NULL,
            note int(11) DEFAULT NULL,
            picture blob DEFAULT NULL,
            KEY sentence_id (sentence_id),
            PRIMARY KEY (ID)
            ) $charset_collate;";
    dbDelta( $sql );


    // end TABLES


    ydgdict_db_insert_option( 'plugin_version', YDGDICT_VERSION );
}
