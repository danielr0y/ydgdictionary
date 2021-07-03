<?php



function ydgdict_shortcodes_init()
{
    function ydgdict_short_display_entry_page_content_cb( $atts = [], $content = null )
    {
        if ( !empty( $content ) ) return "enclosing shortcodes aren't supported.";

        return ydgdict_display_entry_page_content();
    }        
    add_shortcode( 'ydgdict_short_display_entry_page_content', 'ydgdict_short_display_entry_page_content_cb' );



    function ydgdict_short_display_post_glossary_cb( $atts = [], $content = null )
    {
        if ( !empty( $content ) ) return "enclosing shortcodes aren't supported.";

        return ydgdict_display_post_glossary();
    }
    add_shortcode( 'ydgdict_short_display_post_glossary', 'ydgdict_short_display_post_glossary_cb' );



    function ydgdict_short_display_full_glossary_cb( $atts = [], $content = null )
    {
        if ( !empty( $content ) ) return "enclosing shortcodes aren't supported.";

        return ydgdict_display_full_glossary();
    }
    add_shortcode( 'ydgdict_short_display_full_glossary', 'ydgdict_short_display_full_glossary_cb' );


}
add_action( 'init', 'ydgdict_shortcodes_init' );