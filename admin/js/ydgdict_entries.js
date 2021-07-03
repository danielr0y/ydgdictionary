'use strict';

// globals
const ydgdict_entry_set = document.getElementById( 'ydgdict_entry_set' );
const ydgdict_add_button = document.getElementById( 'ydgdict_add_button' );
const ydgdict_form_table = document.getElementById( 'ydgdict_form_table' );
const ydgdict_blog_post_id = document.getElementById( 'post_ID' ).value;
const ydgdict_post_type = document.getElementById( 'post_type' ).value;




// event listeners
ydgdict_entry_set.addEventListener( 'click', (e) => 
{
    ydgdict_insert_new_entry_set_with_idea();
} );



ydgdict_form_table.addEventListener( 'click', (e) => 
{
    const calling_entry_set = e.target.parentNode.parentNode.parentNode;

    if ( 'ydgdict_add_entry_button' == e.target.className )
    {
        ydgdict_insert_new_entry_set_with_idea( calling_entry_set );
    }

    if ( 'ydgdict_add_idea_button' == e.target.className )
    {
        const calling_idea = e.target.parentNode.parentNode;
        ydgdict_insert_new_idea( calling_entry_set, calling_idea );
    }

    if ( 'ydgdict_del_idea_button' == e.target.className )
    {
        const calling_idea = e.target.parentNode.parentNode;
        ydgdict_delete_idea( calling_idea );
    }

    if ( 'ydgdict_del_entry_button' == e.target.className )
    {
        const calling_entry_set = e.target.parentNode.parentNode.parentNode;
        ydgdict_delete_entry( calling_entry_set );
    }
} );



ydgdict_form_table.addEventListener( 'keydown', (e) =>
{
    if ( 'Enter' !== e.key )
        return;

    e.preventDefault();

    if ( 'ydgdict_entry_blog_post_add' == e.target.className && '' !== e.target.value )
        e.target.dispatchEvent( new Event( "change", { "bubbles" : true } ) );

    return false;
});



ydgdict_form_table.addEventListener( 'change', (e) => 
{
    if ( 'ydgdict_entry_blog_post_add' == e.target.className )
    {
        const id = parseInt( e.target.value );

        if ( Number.isNaN( id ) )
            return;

        if ( confirm( "add this entry to post " + id + "?\nthis doesnt happen asyncronously. you have to save entries" ) )
            ydgdict_insert_new_blog_post_id( e.target.closest( 'tbody.ydgdict_entry_set' ), { post_id: id, post_title: id, primary_post: 0 } );
        
        e.target.value = "";
    }

    if ( 'ydgdict_entry_type' == e.target.className )
    {
        const entry_set = e.target.parentNode.parentNode.parentNode;
        ydgdict_add_info_to_entry( entry_set );
    }

    if ( 'ydgdict_entry_parent' == e.target.className )
    {
        // e.target.value holds the parent row number, not the ID
        // this function will retrieve the ID from the parent row.
        const parent_row_number = e.target.value;

        // check for no parent or unchanged parent
        if ( 0 == parent_row_number )
        {
            ydgdict_populate_input_field( e.target.parentNode, 'input.ydgdict_entry_parent_id', 0 );
            return;
        }
        else if ( -1 == parent_row_number )
        {
            ydgdict_populate_input_field( e.target.parentNode, 'input.ydgdict_entry_parent_id', -1 );
            return;
        }
        
        // select the parent row from the table ie.) where data-ydgdict_entry_set_no = parent_row_number
        const parent = ydgdict_form_table.querySelector( `[data-ydgdict_entry_set_no="${parent_row_number}"`);

        // the only time this is expected to happen has already been caught with above but still... 
        if ( null === parent ) return;

        // get the parent_id
        const parent_id = parent.querySelector( 'input.ydgdict_entry_post_id' );

        // if the parent has an ID from the database, use it for this entry's parent
        // otherwise, disable this entry's parent id input field. 
        // the select box will then be used instead and the server will get the ID once its been assigned
        if ( parent_id.hasAttribute( 'value' ) )
        {
            ydgdict_populate_input_field( e.target.parentNode, 'input.ydgdict_entry_parent_id', parent_id.value );
        } else
        {
            // the value here doesnt actually matter. the element gets disabled
            const field = ydgdict_populate_input_field( e.target.parentNode, 'input.ydgdict_entry_parent_id', parent_row_number, true );
            
            console.log( field.value );
        }


        // console.log( parent_row_number );
    }

} );



ydgdict_form_table.addEventListener( 'input', (e) => 
{
    if ( 'ydgdict_entry' == e.target.className )
    {
        if ( e.target.value.length < 2 )
        {
            ydgdict_hide_element( e.target.nextElementSibling );
        } 
        else
        {
            ydgdict_createRequest( ydgdict_populate_form_for_entry_search, ydgdict_success_for_entry_search, e.target );
        }
    }
} );



ydgdict_form_table.addEventListener( 'focusin', (e) => 
{
    if ( 'ydgdict_entry_parent' == e.target.className )
    {
        const calling_entry = e.target.parentNode.parentNode.parentNode.parentNode.querySelector( '.ydgdict_entry' );
        const options = e.target.children;
        const prev_selected_option = options[e.target.selectedIndex];

        // CLEAR THE LIST
        const length = options.length;
        for ( let i = length -1; i != 0; i-- )
        {
            if ( options[i].value <= 0 || options[i].value == prev_selected_option.value ) continue;

            e.target.removeChild( options[i] );
        }

        // REPOPULATE THE LIST WITH ANY NEW ENTRIES THAT MIGHT HAVE BEEN ADDED
        const entries = ydgdict_form_table.querySelectorAll( '.ydgdict_entry' );

        entries.forEach( ( entry ) => 
        {
            // DONT INCLUDE THE CURRENT ENTRY- IT CANT BE A PARENT
            // OR THE PREVIOUSLY SELECTED ENTRY- IT WASNT REMOVED FROM THE LIST
            if ( calling_entry == entry || prev_selected_option.innerText == entry.value ) return;

            const entry_id = entry.parentNode.parentNode.parentNode.getAttribute( 'data-ydgdict_entry_set_no' );

            // INSERT THE NEW OPTION. IF ITS THE SAME 
            ydgdict_populate_select_field( e.target, null, entry_id, entry.value );
        });
    }
} );






// misc functions 
function ydgdict_hide_element( element )
{
    let hidden = element.classList.contains( 'ydgdict_hideme' );
    if ( !hidden )
    {
        hidden = element.classList.toggle( 'ydgdict_hideme' );
        // hidden will now be true
    }
    return hidden;
}



function ydgdict_show_element( element )
{
    let hidden = element.classList.contains( 'ydgdict_hideme' );
    if ( hidden )
    {
        hidden = element.classList.toggle( 'ydgdict_hideme' );
        // hidden will now be false
    }
    return hidden;
}



function ydgdict_delete_entry( calling_entry )
{
    ydgdict_delete( calling_entry, 'input.ydgdict_entry_post_id', 'data-ydgdict_next_entry_to_delete', 'ydgdict_entries_to_delete' );
}



function ydgdict_delete_idea( calling_idea )
{
    ydgdict_delete( calling_idea, 'input.ydgdict_idea_id', 'data-ydgdict_next_idea_to_delete', 'ydgdict_ideas_to_delete' );
}



function ydgdict_disable_all_inputs( calling_set )
{
    const to_disable = calling_set.querySelectorAll( 'input:not([name*="_to_delete["]), select, textarea' );
    Array.prototype.forEach.call( to_disable, ( input ) => 
    {
        input.disabled = true;
    } );
}



function ydgdict_disable_button( button )
{
    button.disabled = true;
}



function ydgdict_enable_button( selector )
{
    const button = document.querySelector( selector );
    button.disabled = false;
}



function ydgdict_use_next_data_no( dataAttribute, parent )
{
    let next = parent.getAttribute( dataAttribute );
    next = ( null === next || '' === next ) ? 1 : next;
    parent.setAttribute( dataAttribute , Number( next ) + 1 );
    return next;
}



function ydgdict_delete( caller, selector, dataAttribute, name )

{
    const the_id = caller.querySelector( selector );
    const _no = ydgdict_use_next_data_no( dataAttribute, ydgdict_form_table );
    //delete it
    the_id.name = `${name}[${_no}]`;
    ydgdict_disable_all_inputs( caller );
}



function ydgdict_clear_the_form()
{
    // clear the form
    const entries = ydgdict_form_table.querySelectorAll( 'tbody.ydgdict_entry_set' );
    if ( 0 != entries.length )
    {
        Array.prototype.forEach.call( entries, ( entry ) => 
        {
            ydgdict_form_table.removeChild( entry );
        } );
    }
    ydgdict_form_table.removeAttribute( 'data-ydgdict_next_idea_to_delete');
    ydgdict_form_table.removeAttribute( 'data-ydgdict_next_entry_to_delete');
    ydgdict_form_table.removeAttribute( 'data-ydgdict_next_entry_set_no');
}



function ydgdict_insert_new_entry_set_with_idea( calling_entry_set = false )
{
    // calling_entry_set determines where the new set will be inserted on the page
    const new_entry_set = ( calling_entry_set ) ? ydgdict_insert_new_entry_set( calling_entry_set ) : ydgdict_insert_new_entry_set();

    // add the current page here
    ydgdict_insert_new_blog_post_id( new_entry_set, { post_id: ydgdict_blog_post_id, post_title: 'this post', primary_post: 1 } );

    ydgdict_insert_new_idea( new_entry_set );
}






// DOM insert helper functions
function ydgdict_name_the_field( parent, fieldSelector, name, required = false )
{
    const field = parent.querySelector( fieldSelector );
    field.name = name;
    // field.required = required;

    return field;
}






// DOM insert functions
function ydgdict_insert_new_blog_post_id( calling_entry_set, value )
{
    const blog_post_id = calling_entry_set.querySelector( 'input.ydgdict_entry_blog_post_id' );
    const entry_set_no = calling_entry_set.getAttribute( 'data-ydgdict_entry_set_no' );
    
    // build the new blog post id input field
    const new_blog_post_id = ( blog_post_id.value ) ? blog_post_id.cloneNode( true ) : blog_post_id;
    
    let entry_blog_post_no = Number( new_blog_post_id.getAttribute( 'data-ydgdict_blog_post' ) );
    if ( new_blog_post_id.value )
        entry_blog_post_no = entry_blog_post_no + 1;

    new_blog_post_id.value = value['post_id'];
    new_blog_post_id.name = `ydgdict_rows[${entry_set_no}][blog_post_ids][${entry_blog_post_no}][post_id]`;
    new_blog_post_id.setAttribute( 'data-ydgdict_blog_post', entry_blog_post_no );

    // insert the entry set
    // beforebegin because the next index is 1 more than the first id found with queryselector
    blog_post_id.insertAdjacentElement( 'beforebegin', new_blog_post_id );

    // select field for selecting the primary id
    ydgdict_populate_select_field( 'select.ydgdict_entry_blog_post_primary', calling_entry_set, value['post_id'], value['post_title'], Number( value['primary_post'] ) );
}



function ydgdict_insert_new_idea( calling_entry_set, calling_idea = calling_entry_set )
{
    const idea = document.getElementById( 'ydgdict_idea_row' );
    const idea_no = ydgdict_use_next_data_no( 'data-ydgdict_next_idea_no', calling_entry_set );
    const entry_set_no = calling_entry_set.getAttribute( 'data-ydgdict_entry_set_no' );
    
    // build the idea
    const fragment = idea.content.cloneNode( true );
    // get the element because insertAjkacentElement doesnt suppost document fragments
    const new_idea = fragment.firstElementChild;
    
    new_idea.setAttribute( 'data-ydgdict_idea_no', idea_no );

    // name parts of the new idea       
    ydgdict_name_the_field( new_idea, "input.ydgdict_idea_id", `ydgdict_rows[${entry_set_no}][ideas][${idea_no}][idea_id]` );
    ydgdict_name_the_field( new_idea, "textarea.ydgdict_idea", `ydgdict_rows[${entry_set_no}][ideas][${idea_no}][idea]`, true );
    ydgdict_name_the_field( new_idea, "textarea.ydgdict_idea_note", `ydgdict_rows[${entry_set_no}][ideas][${idea_no}][idea_note]` );

    // insert the idea before the end of the entry set or after the end of the calling idea
    const position = ( calling_idea == calling_entry_set ) ? 'beforeEnd' : 'afterEnd';
    return calling_idea.insertAdjacentElement( position, new_idea );
}



function ydgdict_insert_new_entry_set( calling_entry_set = ydgdict_form_table )
{
    const entry_set_no = ydgdict_use_next_data_no( 'data-ydgdict_next_entry_set_no', ydgdict_form_table );

    // build the entry set
    const fragment = ydgdict_entry_set.content.cloneNode( true );

    // get the element out of the fragment so that insertAdjacentElement works
    const new_entry_set = fragment.firstElementChild;

    // name parts of the new entry set
    new_entry_set.setAttribute( 'data-ydgdict_entry_set_no', entry_set_no );
    if ( 'entry' == ydgdict_post_type )
    {
        new_entry_set.querySelectorAll( '[class$="_entry_button"]' ).forEach( ( node ) => 
        {
            node.setAttribute( 'disabled', true );
        });
    }

    ydgdict_name_the_field( new_entry_set, "input.ydgdict_entry_post_id", `ydgdict_rows[${entry_set_no}][entry_post_id]` );
    ydgdict_name_the_field( new_entry_set, "input.ydgdict_entry", `ydgdict_rows[${entry_set_no}][entry]`, true );
    ydgdict_name_the_field( new_entry_set, "input.ydgdict_entry_audio", `ydgdict_rows[${entry_set_no}][entry_audio]` );
    ydgdict_name_the_field( new_entry_set, "select.ydgdict_entry_type", `ydgdict_rows[${entry_set_no}][entry_type]`, true );
    ydgdict_name_the_field( new_entry_set, "select.ydgdict_entry_parent", `ydgdict_rows[${entry_set_no}][entry_parent_row]` );
    ydgdict_name_the_field( new_entry_set, "input.ydgdict_entry_parent_id", `ydgdict_rows[${entry_set_no}][entry_parent_id]` );
    ydgdict_name_the_field( new_entry_set, "input.ydgdict_entry_blog_post_id", `ydgdict_rows[${entry_set_no}][blog_post_ids][0][post_id]` );
    ydgdict_name_the_field( new_entry_set, "select.ydgdict_entry_blog_post_primary", `ydgdict_rows[${entry_set_no}][entry_blog_post_primary]` );

    // insert the entry set
    const position = ( ydgdict_form_table == calling_entry_set ) ? 'beforeEnd' : 'afterEnd';
    return calling_entry_set.insertAdjacentElement( position, new_entry_set );
}



function ydgdict_insert_entry_info( entry_set, template )
{
    const entry_row = entry_set.querySelector( '.ydgdict_entry_row' );

    // build the entry info
    const fragment = template.content.cloneNode( true );

    // get a reference to the element that we will return (insertBefore won't give it to use because we're inserting a document fragment)
    const new_info = [].slice.call( fragment.children, 0 );

    // insert the entry info
    entry_set.insertBefore( fragment, entry_row.nextElementSibling )

    return new_info;
}



function ydgdict_add_info_to_entry( entry_set )
{
    const entry_type = entry_set.querySelector( 'select.ydgdict_entry_type' ).value;
    const existing_info = entry_set.querySelectorAll( '.ydgdict_entry_info' );
    const entry_set_no = entry_set.getAttribute( 'data-ydgdict_entry_set_no' );

    // remove existing entry info
    Array.prototype.forEach.call( existing_info, ( element ) => 
    {
        entry_set.removeChild( element );
    } );


    // insert the entry info
    if ( 'noun' == entry_type )
    {
        const new_info = ydgdict_insert_entry_info( entry_set, document.getElementById( 'ydgdict_noun_info' ) );
    
        // name parts of the entry info
        ydgdict_name_the_field( new_info[0], '.ydgdict_noun_gender', `ydgdict_rows[${entry_set_no}][gender]`, true )
        ydgdict_name_the_field( new_info[0], 'input.ydgdict_noun_plural', `ydgdict_rows[${entry_set_no}][plural]` )

        return new_info;
    }


    if ( 'verb' == entry_type )
    {
        const new_info = ydgdict_insert_entry_info( entry_set, document.getElementById( 'ydgdict_verb_info' ) )
        
        // name parts of the entry info
        jQuery( ydgdict_name_the_field( new_info[0], 'select.ydgdict_verb_prefix', `ydgdict_rows[${entry_set_no}][prefix]` ) ).select2();
        ydgdict_name_the_field( new_info[0], 'select.ydgdict_verb_aux', `ydgdict_rows[${entry_set_no}][aux]`, true )
        ydgdict_name_the_field( new_info[0], 'input.ydgdict_verb_past_part', `ydgdict_rows[${entry_set_no}][past_part]`, true )
        ydgdict_name_the_field( new_info[1], 'input.ydgdict_verb_preterit', `ydgdict_rows[${entry_set_no}][preterit]` )
        ydgdict_name_the_field( new_info[1], 'input.ydgdict_verb_pres_part', `ydgdict_rows[${entry_set_no}][pres_part]` )

        return new_info;
    }

    return false;
}






// populate helper functions
function ydgdict_populate_input_field( parent, fieldSelector, value, disabled = false )
{
    const field = ( 'object' === typeof fieldSelector ) ? fieldSelector : parent.querySelector( fieldSelector );
    field.value = value;
    field.disabled = disabled;

    return field;
}



function ydgdict_populate_textarea_field( parent, fieldSelector, innerText )
{
    const field = parent.querySelector( fieldSelector );
    field.innerText = innerText;
}

/**
 * 
 * @param {(string|object)} field if String; select = calling_element.querySelector( field ). if Object; select = field, In this case, calling_element is ignored
 * @param {object} calling_element querySelector only looks in this element. ignored if field istype object
 * @param {*} value the value send to the server
 * @param {string} innerText the text displayed in the HTML
 * @param {boolean} [selected=false] option.selected
 */
function ydgdict_populate_select_field( field, calling_element, value, innerText, selected = false )
{
    const select = ( 'object' === typeof field ) ? field : calling_element.querySelector( field );
    
    const option = document.createElement( 'option' );
    option.value = value;
    option.innerText = innerText;
    option.selected = selected;

    return select.appendChild( option );
}



function ydgdict_select_field_set_selected( parent, fieldSelector, valueToSelect, select2 = false )
{
    if ( '' == valueToSelect ) return;
    
    const field = parent.querySelector( fieldSelector );

    if ( select2 )
    {
        jQuery( field ).val( valueToSelect );
        jQuery( field ).trigger('change');
        return;
    }

    const select_me = field.querySelector( `option[value="${valueToSelect}"]` );
    if( null === select_me ) return;

    const selected = field.querySelectorAll( 'option[selected]' );
    Array.prototype.forEach.call(selected, ( option ) => 
    {
        option.removeAttribute( 'selected' );
    });

    select_me.setAttribute( "selected", true );
}



function ydgdict_process_entry_from_server( new_entry_set, entry_set_from_server )
{
    // then populate it with data 
    ydgdict_populate_input_field( new_entry_set, 'input.ydgdict_entry', entry_set_from_server['entry'] );

    if ( 'undefined' !== typeof entry_set_from_server['entry_post_id'] )
    {
        ydgdict_populate_input_field( new_entry_set, 'input.ydgdict_entry_post_id', entry_set_from_server['entry_post_id'] );
    }

    if ( 'undefined' !== typeof entry_set_from_server['entry_audio'] )
    {
        ydgdict_populate_input_field( new_entry_set, 'input.ydgdict_entry_audio', entry_set_from_server['entry_audio'] );
    }

    if ( 'undefined' !== typeof entry_set_from_server['entry_type']['slug'] )
    {
        ydgdict_select_field_set_selected( new_entry_set, 'select.ydgdict_entry_type', entry_set_from_server['entry_type']['slug'] );
    }

    // console.log( entry_set_from_server['entry_parent'] );

    if ( 'undefined' !== typeof entry_set_from_server['entry_parent'] )
    {
        const entry_parent = new_entry_set.querySelector( 'input.ydgdict_entry_parent_id' );
        entry_parent.disabled = false;
        // entry_parent.setAttribute( 'data-ydgdict_parent_post_title', entry_set_from_server['entry_parent']['post_title'] );

        ydgdict_populate_input_field( new_entry_set, entry_parent, entry_set_from_server['entry_parent']['post_id'] );

        if ( '0' !== entry_set_from_server['entry_parent']['post_id'] )
        {
            ydgdict_populate_select_field( 'select.ydgdict_entry_parent', new_entry_set, -1, entry_set_from_server['entry_parent']['post_title'], true )
        }
    }

    // console.log( entry_set_from_server['blog_post_ids'] );

    if ( 'undefined' !== typeof entry_set_from_server['blog_post_ids'] )
    {
        let primary_post_id = null;
        // loop through all the associated blog posts
        entry_set_from_server['blog_post_ids'].forEach( ( blog_post_id ) =>
        {
            // looking for the primary post
            if ( 1 == blog_post_id['primary_post'] ) 
                primary_post_id = blog_post_id['post_id'];
            
            // but insert an id every time
            ydgdict_insert_new_blog_post_id( new_entry_set, blog_post_id );
        } );

        // IF THE PRIMARY BLOG POST IS NOT THIS POST, DISABLE PARENT SELECTOR
        if ( primary_post_id != ydgdict_blog_post_id ) 
            new_entry_set.querySelector( 'select.ydgdict_entry_parent' ).disabled = true;
    }

    if ( 'noun' == entry_set_from_server['entry_type']['slug'] )
    {
        const entry_info = ydgdict_add_info_to_entry( new_entry_set );

        ydgdict_select_field_set_selected( entry_info[0], 'select.ydgdict_noun_gender', entry_set_from_server['gender'] );
        ydgdict_populate_input_field( entry_info[0], 'input.ydgdict_noun_plural', entry_set_from_server['plural'] );
    }
    
    if ( 'verb' == entry_set_from_server['entry_type']['slug'] )
    {
        const entry_info = ydgdict_add_info_to_entry( new_entry_set );
        
        const prefix = ( entry_set_from_server['prefix'] ) ? entry_set_from_server['prefix']['slug'] : null;
        ydgdict_select_field_set_selected( entry_info[0], 'select.ydgdict_verb_prefix', prefix, true );
        ydgdict_select_field_set_selected( entry_info[0], 'select.ydgdict_verb_aux', entry_set_from_server['aux'] );
        ydgdict_populate_input_field( entry_info[0], 'input.ydgdict_verb_past_part', entry_set_from_server['past_part'] );

        ydgdict_populate_input_field( entry_info[1], 'input.ydgdict_verb_preterit', entry_set_from_server['preterit'] );
        ydgdict_populate_input_field( entry_info[1], 'input.ydgdict_verb_pres_part', entry_set_from_server['pres_part'] );
    }

    if ( undefined !== entry_set_from_server['ideas']  )
    {
        if ( ! entry_set_from_server['ideas'].length )
        {
            ydgdict_insert_new_idea( new_entry_set );
        }
        else
        {
            Array.prototype.forEach.call( entry_set_from_server['ideas'], ( idea_from_server ) => 
            {
                const new_idea = ydgdict_insert_new_idea( new_entry_set );

                ydgdict_populate_input_field( new_idea, 'input.ydgdict_idea_id', idea_from_server['idea_id'] );
                ydgdict_populate_textarea_field( new_idea, 'textarea.ydgdict_idea', idea_from_server['idea'] );
                ydgdict_populate_textarea_field( new_idea, 'textarea.ydgdict_idea_note', idea_from_server['idea_note'] );
            })
        }
    }

    return new_entry_set;
}








// the AJAX stuff
function ydgdict_populate_form_for_entry_search( form_data, calling_entry )
{
    form_data.append( 'action', 'ydgdict_import_entry_search' );
    form_data.append( '_ajax_nonce', data_for_metabox.nonce );
    form_data.append( 'ydgdict_s', calling_entry.value );
}


function ydgdict_success_for_entry_search( results, calling_entry )
{
    // console.log( results );
    
    const ul = calling_entry.nextElementSibling;
    const length = ul.children.length;

    // CLEAR THE UL
    for ( let i = length; i != 0; i-- )
    {
        ul.removeChild( ul.children[i-1] );
    }

    // REPOPULATE THE UL
    results.forEach( ( entry ) => 
    {
        const li = document.createElement( 'li' );
        const a = document.createElement( 'a' );
        
        a.innerText = entry['post_title'];
        a.className = 'ydgdict_entry_search_result';
        a.href = '#';
        a.addEventListener( 'click', ( e ) =>
        {
            e.preventDefault();

            ydgdict_hide_element( ul );

            ydgdict_process_entry_from_server( calling_entry.parentNode.parentNode.parentNode, entry );
        } );

        li.appendChild( a );

        ul.appendChild( li );
    } );

    // ADD CLOSE THE UL FUNCTIONALITY
    document.addEventListener( 'click', ( e ) => 
    {
        if ( e.target != calling_entry )
        {
            ydgdict_hide_element( ul );
        }
    } );

    // DISPLAY THE UL
    ydgdict_show_element( ul );
}


function ydgdict_handleErrors( response )
{
    if ( !response.ok ) throw ( response.status + ': ' + response.statusText )
    return response.json( );
}



function ydgdict_createRequest( populate_the_form, success, calling_entry = null )
{
    const form = new FormData();
    try
    {
        if ( null === calling_entry )
        {
            populate_the_form( form ); 
        } else
        {
            populate_the_form( form, calling_entry ); 
        }
    }catch( e )
    {
        console.log( e );
        return;
    }
    
    fetch( data_for_metabox.ajax_url, 
        {
        body : form, // TODO: sanitize and validate
        method : 'POST' 
    } ) .then( ( response ) => ydgdict_handleErrors( response ) )
        .then( ( data ) => 
        {
            if ( null === calling_entry )
            {
                success( data );
            } else
            {
                success( data, calling_entry );
            }
        } ).catch( ( error ) => console.log( error ) );
}