<?php foreach ( $entry->ideas as $key => $idea ) : ?>
    <div class="ydgdict_glossary_entry_idea_wrapper">
        <div class="ydgdict_glossary_entry_idea">
            <span class='idea_number'><?php echo ( $key + 1 ) . ". "; ?></span>
            <span class="ydgdict_glossary_entry_idea_trans"> <?php echo $idea['idea'];  ?> </span>

            <?php if( "" !== $idea['idea_note'] ) : ?>

                <span class="ydgdict_glossary_entry_note">(<?php echo preg_replace( '/(\((?:akk\w{0,7}|dat\w{0,3})\)|\+\s*(?:akk\w{0,7}|dat\w{0,3}))/i', "<sup>$1</sup>", $idea['idea_note']); ?>)</span>
            <?php endif; ?>
        </div>
    </div>
<?php endforeach; ?>