const ydgdict_glossary = document.getElementById( 'ydgdict_glossary' );
const ydgdict_glossary_container = document.getElementById( 'ydgdict_glossary_container' );

const ydgdict_filter_search = document.getElementById( 'ydgdict_glossary_filter_search' );
const ydgdict_filter_search_dest = document.getElementById( 'ydgdict_glossary_filter_search_dest' );
const ydgdict_filter_prefix = document.getElementById( 'ydgdict_glossary_filters_prefix' );
const ydgdict_filter_type = document.getElementById( 'ydgdict_glossary_filters_type' );


let ydgdict_request_in_progress = false;



// FUCNTIONS
function ydgdict_get_more_entries()
{
    if ( ydgdict_glossary.getAttribute( 'data-ydgdict_page' ) == ydgdict_glossary.getAttribute( 'data-ydgdict_total_pages' ) )
    {
        console.log( "there are no more pages to get" );
        return;
    }

    ydgdict_createRequest( ydgdict_prepare_get_more_entries_form, ydgdict_get_more_entries_success );
}



function ydgdict_display_pre_loaded_entries()
{
    const pre_loaded_entries = document.getElementById( "ydgdict_pre_loaded_entries" );

    if ( ! pre_loaded_entries ) return;

    ydgdict_glossary.appendChild( pre_loaded_entries.content );

    ydgdict_glossary_container.removeChild( pre_loaded_entries );
    
    ydgdict_get_more_entries();
}






// EVENT LISTENERS
window.onscroll = function() 
{
    if ( ! ydgdict_glossary_container ) return; // element does not exist on single entries

    const content_height = ydgdict_glossary_container.offsetHeight;
    const current_y = window.innerHeight + window.pageYOffset;

    if ( current_y + 100 >= content_height && ! ydgdict_request_in_progress )
    {
        ydgdict_display_pre_loaded_entries();
    } else if ( current_y == document.body.scrollHeight )
    {
        window.scrollBy( 0, -1 );
    }
};



ydgdict_filter_search.addEventListener( 'change', (e) => 
{
    if ( '' == e.target.value )
    {
        e.target.setAttribute( 'name', '' );
    } else
    {
        e.target.setAttribute( 'name', 's' );
    }
});



jQuery( ydgdict_filter_search_dest ).on( 'change', (e) => 
{
    if ( '0' == e.target.value )
    {
        e.target.setAttribute( 'name', '' );
    } else
    {
        e.target.setAttribute( 'name', 's_dest' );
    }
});



jQuery( ydgdict_filter_type ).on( 'change', (e) => 
{
    if ( 'verb' == e.target.value )
    {
        ydgdict_filter_prefix.removeAttribute( 'disabled' );
    } else
    {
        ydgdict_filter_prefix.disabled = true;
    }

    if ( '' == e.target.value )
    {
        e.target.setAttribute( 'name', '' );
    } else
    {
        e.target.setAttribute( 'name', 'word_type' );
    }
});



jQuery( ydgdict_filter_prefix ).on( 'change', (e) => 
{
    if ( '' == e.target.value )
    {
        e.target.removeAttribute( 'name' );
    } else
    {
        ydgdict_filter_prefix.setAttribute( 'name', 'word_type' );
    }
});






// THE AJAX STUFF
const ydgdict_prepare_get_more_entries_form = function ( form_data )
{
    // increase the current paged number by one
    ydgdict_glossary.setAttribute( 'data-ydgdict_page', Number( ydgdict_glossary.getAttribute( 'data-ydgdict_page' ) ) + 1 );
    
    form_data.append( 'action', 'ydgdict_glossary_get_more_entries' );
    form_data.append( '_ajax_nonce', data_for_glossary.nonce );
    form_data.append( 'paged', ydgdict_glossary.getAttribute( 'data-ydgdict_page' ) );
    form_data.append( 's_dest', ydgdict_filter_search_dest.value );
    form_data.append( 's', ydgdict_filter_search.value );
    form_data.append( 'word_type', ( '' == ydgdict_filter_prefix.value ) ? ydgdict_filter_type.value : ydgdict_filter_prefix.value );
}



const ydgdict_get_more_entries_success = function ( data )
{
    ydgdict_glossary.insertAdjacentHTML( 'afterend', data );
    
    ydgdict_request_in_progress = false;
}



function ydgdict_handleErrors( response )
{
    if ( !response.ok ) 
    {
        throw ( response.status + ': ' + response.statusText )
    }
    return response.json( );
}



const ydgdict_createRequest = function( populate_the_form, success )
{
    const form = new FormData();
    try{
        populate_the_form( form ); 
    }catch(e){
        console.log(e);
        return;
    }

    if ( ydgdict_request_in_progress ) return;
    ydgdict_request_in_progress = true;

    fetch( data_for_glossary.ajax_url, 
    {
        body : form, // TODO: sanitize and validate
        method : 'POST' 
    } ) .then( ( response ) => ydgdict_handleErrors( response ) )
        .then( ( data ) => success( data ) )
        .catch( ( error ) => console.log( error ) );
}





/* 
 * the program. after the document is ready
 */
jQuery(document).ready( () => 
{
    /* 
     * add the select2 javascript to the input elements
     */
    const select2_dest = jQuery(ydgdict_filter_search_dest).select2(
    {
        minimumResultsForSearch: Infinity,
        width: 'resolve'
    } );
    select2_dest.data('select2').$container.addClass("ydgdict_glossary_filter_search_dest");

    const select2_type = jQuery(ydgdict_filter_type).select2(
    {
        minimumResultsForSearch: Infinity,
        width: 'resolve'
    });
    select2_type.data('select2').$container.addClass("ydgdict_glossary_filters_type");

    const select2_prefix = jQuery(ydgdict_filter_prefix).select2(
    {
        minimumResultsForSearch: Infinity,
        width: 'resolve'
    });
    select2_prefix.data('select2').$container.addClass("ydgdict_glossary_filters_prefix");

    

    // pre-load a set of entires
    ydgdict_get_more_entries();
});