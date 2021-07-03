(function($)
{
    var audio_modal = 
    {
        form_table : '',
        frame : '',

		/**
		 * initialise the object.
		 */
        init : function()
        {
            this.form_table = document.getElementById( 'ydgdict_form_table' );
            this.form_table.addEventListener( 'click', this.openAudioFrame );
        },

		/**
		 * Open the featured audio media modal.
		 */
        openAudioFrame: function( event ) 
        {
            // as the event bubbles up to form_table, read the class of each element 
            // only continue on add_audio_button
            if ( 'ydgdict_add_audio_button' != event.target.className ) 
                return;

            // init frame if neccessary
			if ( ! this.frame )
				this.initFrame();

            // open the frame
            this.frame.open();
		},

		/**
		 * Create a media modal select frame, and store it so the instance can be reused when needed.
		 */
        initFrame: function() 
        {
            this.frame = wp.media({ frame: 'audioDetails' });

			// When a file is selected, run a callback.
            this.frame.on( 'select', this.selectAudio );
		},

		/**
		 * Callback handler for when an attachment is selected in the media modal.
		 * Gets the selected attachment information, and sets it within the control.
		 */
        selectAudio: function() 
        {
			// Get the attachment from the modal frame.
			var attachment = this.frame.state().get( 'selection' ).first().toJSON();
			// $( '#audio-attachment-id' ).val( attachment.id );
			// $( '#audio-attachment-title' ).text( attachment.title );
			// this.audioEmbed( attachment );
		},
    };
    audio_modal.init();
})(jQuery);


(function($)
{

    const article_modal = 
    {
        modal : new wp.media.view.Modal(
        {
            // 

            // A controller object is expected, but let's just pass
            // a fake one to illustrate this proof of concept without
            // getting console errors.
            controller: { trigger: function() {} }
        }),

        ModalContentView : wp.Backbone.View.extend(
        {
            initialize : function()
            {
                this.$el.on( 'input', this.search_callback );
            },
            template : wp.template( 'add-article-modal-content' ),
        }),

        init : function()
        {
            // Assign the ModalContentView to the modal as the `content` subview.
            // Proxies to View.views.set( '.media-modal-content', content );
            const modal = this.modal.content( new this.ModalContentView() );

            console.log( this.modal );

            ydgdict_form_table.addEventListener( 'click', ( event ) => 
            {
                if ( 'ydgdict_add_article_button' == event.target.className )
                    this.modal.open();
            });

            if ( typeof this.search_box == "undefined" )
                this.search_box = modal.$el.find( '.ydgdict_add_article_search' );

        },
        
        search_box : undefined,

        search_callback : function( event )
        {
            if ( 'ydgdict_add_article_search' != event.target.className )
                return;

            // try bind call or apply() here to pass context to createRequest
            ydgdict_createRequest.call( this, [ this.ajax_populate_the_form, this.ajax_success ] );
        },

        ajax_populate_the_form : function( form_data )
        {
            form_data.append( 'action', 'add_article_search' );
            form_data.append( '_ajax_nonce', data_for_metabox.nonce );
            form_data.append( 'ydgdict_s', this.search_box.value );
        },

        ajax_success : function( response )
        {
            console.log( response );
        },
    };
    article_modal.init();    
})(jQuery)