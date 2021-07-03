( function()
{



    tinymce.create( 'tinymce.plugins.ydgdict_add_sentence', {
        init : function( ed, url ) {

            ed.addButton( 'ydgdict_add_sentence', {
                title : 'example sentence',
                icon : 'anchor',
                onclick : function() {
                    let german, english, sentence_id, idea_id = '';

                    const ul = ed.selection.getNode().closest( 'ul' );
                    if ( null !== ul ){
                        sentence_id = ul.getAttribute( 'data-ydgdict_sentence_id' );
                        if ( null === sentence_id )
                            sentence_id = 0;

                        idea_id = ul.getAttribute( 'data-ydgdict_idea_id' );
                        if ( null === idea_id )
                            idea_id = 0;

                        if ( 'UL' === ul.tagName ){
                            const lis = ul.children;
                            german = lis[0].textContent;
                            english = lis[1].textContent;
                        }
                    }

                    // get ideas from the DOM
                    // include the entry name in the text property (hence ideas_with_entries)
                    const ideas_with_entries = Array.prototype.map.call( document.querySelectorAll( '.ydgdict_idea_row .ydgdict_col_1' ), idea => {
                        return { 
                            value: idea.querySelector( '.ydgdict_idea_id' ).value, 
                            text: idea.closest( '.ydgdict_entry_set' ).querySelector( '.ydgdict_entry' ).value + " > " + idea.querySelector( '.ydgdict_idea' ).value, 
                        }
                    } ).filter( x => ( '' !== x.value ) ); // remove ideas not yet saved to the database

                    // add a 0 value entry back in (at the top) for assigning an idea later
                    ideas_with_entries.unshift( { value: 0, text: 'assign later' } );

                    var win = ed.windowManager.open( {
                        title: 'example sentence',
                        body: [ 
                            {
                                type: 'textbox',
                                multiline: true,
                                minHeight: 100,
                                name: 'german',
                                classes: 'ydgdict_german_sentence',
                                label: 'German',
                                value: german
                            }, {
                                type: 'button',
                                name: 'swap',
                                text: 'swap',
                                label: ' ',
                                onclick: function( e ) {
                                    const eng = win.find( '.ydgdict_english_sentence' );
                                    const ger = win.find( '.ydgdict_german_sentence' );
                                    const swap = eng.value();
                                    eng.value( ger.value() );
                                    ger.value( swap );

                                    var selected = win.find( '.ydgdict_sentence_idea' ).selected();
                                    console.log( selected );
                                }
                            }, {
                                type: 'textbox',
                                multiline: true,
                                minHeight: 100,
                                name: 'english',
                                classes: 'ydgdict_english_sentence',
                                label: 'English',
                                value: english
                            }, {
                                type: 'textbox',
                                name: 'audio',
                                label: 'Audio',
                            }, {
                                type: 'listbox',
                                name: 'idea',
                                classes: 'ydgdict_sentence_idea',
                                label: 'Idea',
                                values: ideas_with_entries,
                                onPostRender: function () {
                                  this.value( idea_id );
                                }
                            }
                        ],
                        onsubmit: function( e ) {
                            const form = new FormData();
                            form.append( 'action', 'ydgdict_create_sentence' );
                            form.append( '_ajax_nonce', data_for_metabox.nonce );
                            form.append( 'ydgdict_german', e.data.german );
                            form.append( 'ydgdict_english', e.data.english );
                            form.append( 'ydgdict_audio', e.data.audio );
                            form.append( 'ydgdict_idea', e.data.idea );
                            form.append( 'ydgdict_prev_idea', idea_id );
                            form.append( 'ydgdict_sentence_id', sentence_id );
                            form.append( 'ydgdict_post_id', ydgdict_blog_post_id );

                            fetch( data_for_metabox.ajax_url, {
                                body : form,
                                method : 'POST' 
                            } ).then( ( response ) => {
                                if ( ! response.ok )
                                    throw ( response.status )
                                return response.json();
                            } ).then( ( object ) => {
                                if ( null !== ul ){
                                    if ( 'UL' === ul.tagName ){
                                        ul.setAttribute( 'data-ydgdict_sentence_id', object.ydgdict_sentence_id );
                                        ul.setAttribute( 'data-ydgdict_idea_id', object.ydgdict_idea_id );
                                    }
                                } else
                                    ed.execCommand( 'mceInsertRawHTML', false, '<ul data-ydgdict_sentence_id="' + object.ydgdict_sentence_id + '"><li>' + e.data.german + '</li><li>' + e.data.english + '</li></ul>' );
                            } ).catch( ( error ) => console.log( 'ooops, ' + error ) );
						}
					});
                }
            } );
        },
        createControl : function( n, cm ) {
            return null;
        },
    } );
    tinymce.PluginManager.add( 'ydgdict_add_sentence', tinymce.plugins.ydgdict_add_sentence );
} )();