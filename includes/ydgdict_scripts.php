<?php

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

function ydgdict_admin_scripts( $hook )
{
    if ( 'post.php' == $hook || 'post-new.php' == $hook ) // FIXME: this loads on pages too
    {
        wp_enqueue_script( 
            'ydgdict_select_js',
            plugin_dir_url( dirname( __FILE__ ) ) . 'public/js/select2.min.js',
            ['jquery'], 
            false, 
            true
        );
        
        wp_enqueue_style( 
            'ydgdict_select_css',
            plugin_dir_url( dirname( __FILE__ ) ) . 'public/css/select2.min.css',
            [] 
        );

        wp_enqueue_style( 
            'ydgdict_entries_css',
            plugin_dir_url( dirname( __FILE__ ) ) . 'admin/css/ydgdict_entries.css',
            [],
            YDGDICT_VERSION 
        );

        wp_enqueue_script( 
            'ydgdict_entries_js',
            plugin_dir_url( dirname( __FILE__ ) ) . 'admin/js/ydgdict_entries.js',
            [], 
            YDGDICT_VERSION,
            true
        );
        
        wp_enqueue_script( 
            'ydgdict_metabox_js',
            plugin_dir_url( dirname( __FILE__ ) ) . 'admin/js/ydgdict_metabox.js',
            [], 
            YDGDICT_VERSION,
            true
        );

        wp_localize_script( 
            'ydgdict_metabox_js', 
            'data_for_metabox', 
            array(
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'nonce'    => wp_create_nonce( 'metabox_nonce' ),
            )
        );
    }
}
add_action( 'admin_enqueue_scripts', 'ydgdict_admin_scripts' );




function ydgdict_public_scripts()
{
    if ( is_archive( 'entry' ) || is_singular( 'entry' ) || is_singular( 'post' ) )
    {        
        wp_enqueue_style( 
            'ydgdict_glossary_css',
            plugin_dir_url( dirname( __FILE__ ) ) . 'public/css/ydgdict_glossary.css',
            [],
            YDGDICT_VERSION
        );
    }

    if ( is_archive( 'entry' ) )
    {
        wp_enqueue_script( 
            'ydgdict_glossary_js',
            plugin_dir_url( dirname( __FILE__ ) ) . 'public/js/ydgdict_glossary.js',
            ['jquery'], 
            YDGDICT_VERSION,
            true
        );

        wp_localize_script( 
            'ydgdict_glossary_js', 
            'data_for_glossary', 
            array(
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'nonce'    => wp_create_nonce( 'glossary_nonce' ),
            )
        );

        wp_enqueue_script( 
            'ydgdict_select_js',
            plugin_dir_url( dirname( __FILE__ ) ) . 'public/js/select2.min.js',
            ['jquery'], 
            false, 
            true
        );
        
        wp_enqueue_style( 
            'ydgdict_select_css',
            plugin_dir_url( dirname( __FILE__ ) ) . 'public/css/select2.min.css',
            [] 
        );
    }

    if ( is_singular( 'entry' ) )
    {
        wp_enqueue_script( 
            'ydgdict_entry_js',
            plugin_dir_url( dirname( __FILE__ ) ) . 'public/js/ydgdict_entry.js',
            [], 
            YDGDICT_VERSION,
            true
        );
    }
}
add_action( 'wp_enqueue_scripts', 'ydgdict_public_scripts' );