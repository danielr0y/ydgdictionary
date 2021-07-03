const ydgdict_wp_form_post = document.getElementById( 'post' );
const ydgdict_upload_csv = document.getElementById( 'ydgdict_upload_csv' );
const ydgdict_save_entries = document.getElementById( 'ydgdict_post_button' );




function ydgdict_click_save()
{
    ydgdict_save_entries.dispatchEvent( new Event('click') );
}



ydgdict_wp_form_post.addEventListener( 'submit', (e) => 
{
    ydgdict_click_save();
});


if ( ydgdict_upload_csv )
{
    ydgdict_upload_csv.addEventListener( 'change', (e) => 
    {
        ydgdict_createRequest( ydgdict_populate_form_for_upload_file, ydgdict_display_uploaded );
    });
}


ydgdict_save_entries.addEventListener( 'click', (e) => 
{
    ydgdict_disable_button( e.target );
    ydgdict_createRequest( ydgdict_populate_form_for_save_entries, ydgdict_display_entries );
});



function ydgdict_process_entries_from_server( parsedText )
{
    if ( ! parsedText.length )
    {
        ydgdict_insert_new_entry_set_with_idea();
    }
    else
    {
        Array.prototype.forEach.call( parsedText, ( entry_set_from_server ) => 
        {
            // generate a new entry set
            const new_entry_set = ydgdict_insert_new_entry_set();

            ydgdict_process_entry_from_server( new_entry_set, entry_set_from_server )
        } ); // end forEach parsedText
    }
}






// populate the POST Request for the save button
const ydgdict_populate_form_for_save_entries = function( form_data )
{
    form_data.append( 'action', 'ydgdict_save_entries' );
    form_data.append( '_ajax_nonce', data_for_metabox.nonce );
    form_data.append( 'ydgdict_post_type', ydgdict_post_type );

    if ( ydgdict_blog_post_id )
        form_data.append( 'ydgdict_blog_post_id', ydgdict_blog_post_id );

    // all the shit from the posts
    const list = ydgdict_form_table.querySelectorAll( '[name^="ydgdict_"]:not([disabled])' );
    Array.prototype.forEach.call( list, ( element ) => 
    {
        if ( !element.checkValidity() )
        {
            throw element;
        }

        // console.log( element.name, element.value );
        form_data.append( element.name, element.value );
    });
}





// success callback for the save button
const ydgdict_display_entries = function( parsedText )
{
    // console.log( parsedText );

    ydgdict_clear_the_form();
    
    // put the new stuff in
    ydgdict_process_entries_from_server( parsedText );

    // enable the button again
    ydgdict_enable_button( '#ydgdict_post_button' );
}



// populate the POST Request for the upload csv feature
const ydgdict_populate_form_for_upload_file = function( form_data )
{
    form_data.append( 'action', 'ydgdict_upload_file' );
    form_data.append( '_ajax_nonce', data_for_metabox.nonce );
    form_data.append( 'ydgdict_the_csv_file', ydgdict_upload_csv.files[0] );
    form_data.append( 'ydgdict_blog_post_id', ydgdict_blog_post_id );
}



// success callback for the upload csv feature
const ydgdict_display_uploaded = function( parsedText )
{
    // console.log( parsedText );
    ydgdict_process_entries_from_server( parsedText );
}




ydgdict_click_save();