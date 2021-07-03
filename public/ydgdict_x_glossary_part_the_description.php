<div class="ydgdict_glossary_description " > <!-- removed class "ydgdict_hideme" to make the stuff always visible -->

    <div class="ydgdict_glossary_desc_text_wrap">
        <p><?php echo $blog_post->_aioseop_description; ?></p>

        <p><strong>Vocab:</strong></p>

        <p><?php echo $blog_post->vocab_shortlist; ?></p>

        <span >
            <a class="ydgdict_dec_goto_button" href="<?php echo get_permalink( $blog_post->ID ) ?>" >open article</a>
        </span>
    </div>

    <?php if ( $src = ydgdict_catch_that_image( $blog_post->ID ) ) : ?>

        <img class='ydgdict_desc_img' src='<?php echo $src;?>' /> <!-- change data_src to scr to show image immediately -->
    <?php endif; ?>

    <br style="clear: both">
</div>