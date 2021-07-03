<?
/* 
 * Plugin name: Your Daily German Dictionary
 * Description: add featured words to posts and display them all in a glossary
 * Version:     20210319
 * Author:      Daniel Roy
 * Text Domain: ydgdict
 */

if ( !defined( 'ABSPATH' ) ) exit;

// CONSTANTS
if ( !defined( 'YDGDICT_VERSION' ) )        define( 'YDGDICT_VERSION', '20210319' ); // UPDATE THE HEADER VERSION TOO!!!!
if ( !defined( 'YDGDICT_PLUGIN_DIR' ) )     define( 'YDGDICT_PLUGIN_DIR', plugin_dir_path( __FILE__) );


// CUSTOM POST TYPES AND TAXONOMIES
require_once( YDGDICT_PLUGIN_DIR . 'includes/ydgdict_tax_word_type.php' ); // THIS MUST BE INCLUDED BEFORE THE ENTRY CUSTOM POST TYPE
require_once( YDGDICT_PLUGIN_DIR . 'includes/ydgdict_cpt_entry.php' );

// GENERAL INCLUDES
require_once( YDGDICT_PLUGIN_DIR . 'includes/ydgdict_functions.php' );
require_once( YDGDICT_PLUGIN_DIR . 'includes/ydgdict_scripts.php' );
require_once( YDGDICT_PLUGIN_DIR . 'includes/ydgdict_shortcodes.php' );

// PUBLIC SITE FEATURES
require_once( YDGDICT_PLUGIN_DIR . 'public/ydgdict_entry.php' );
require_once( YDGDICT_PLUGIN_DIR . 'public/ydgdict_post_glossary.php' );
require_once( YDGDICT_PLUGIN_DIR . 'public/ydgdict_full_glossary.php' );

// ADMIN FEATURES
if ( is_admin() ) 
{
    // ARTICLE EDIT PAGE METABOX
    require_once( YDGDICT_PLUGIN_DIR . 'admin/ydgdict_article_metabox.php' );

    // ENTRY EDIT PAGE METABOX
    require_once( YDGDICT_PLUGIN_DIR . 'admin/ydgdict_entry_metabox.php' );

    // TINYMCE BUTTON
    require_once( YDGDICT_PLUGIN_DIR . 'admin/ydgdict_tinymce.php' );
}



function ydgdict_activate()
{
    // INSTALL THE CUSTOM POST TYPE AND TAXONOMY
    ydgdict_init_taxonomy_word_type();
    ydgdict_init_cpt_entry();
    ydgdict_init_word_type_terms();

    // FLUSH REWRITE RULES TO ENSURE 
    flush_rewrite_rules();
    
    // ADD TABLES TO THE DATBASE
    ydgdict_activate_database();
}
register_activation_hook( __FILE__, 'ydgdict_activate' );





function ydgdict_deactivate()
{
    // REMOVE THE PLUGIN VERSION FROM THE DATABASE
    ydgdict_db_insert_option( 'plugin_version', 'NULL' );
}
register_deactivation_hook( __FILE__, 'ydgdict_deactivate' );





function ydgdict_uninstall()
{
    // do not delete the database
}
// register_uninstall_hook(__FILE__, 'ydgdict_uninstall');





// this function runs every page load and checks if the database needs to be updated
function ydgdict_check_version()
{
    if ( YDGDICT_VERSION != ydgdict_db_get_option( 'plugin_version' ) ) ydgdict_activate_database();
}
add_action('plugins_loaded', 'ydgdict_check_version');





// temporarily remove the editor from posts during development
// add_action( 'init', function() { remove_post_type_support( 'post', 'editor' ); });
