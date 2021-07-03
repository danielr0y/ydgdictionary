<?php
$both = ydgdict_get_word_types_and_prefixes();
$types = $both['types'];
$prefixes = ydgdict_remove_parent_prefixes( $both['prefixes'] );

$post = get_post();
// error_log( print_r( $post, true ) );
?>
<div>
<table id="ydgdict_form_table"></table>
<template id="ydgdict_entry_set">
    <tbody class="ydgdict_entry_set" >
        <tr class="ydgdict_entry_row" >
            <td class="ydgdict_col_1" colspan="2">
                <input class="ydgdict_entry_post_id" type="hidden" />
                <input class="ydgdict_entry" placeholder="entry" type="search">
                <ul class="ydgdict_entry_search ydgdict_hideme" ></ul>
                <input class="ydgdict_entry_audio" placeholder="audio url" />
            <!-- </td>
            <td class="ydgdict_col_2"> -->
                <select class="ydgdict_entry_type" >
                    <option disabled selected value=''>type</option>
                <?php foreach ($types as $type ) : ?>
                    <option value="<?php echo $type->slug; ?>" ><?php echo $type->name; ?></option>
                <?php endforeach; ?>
                </select>
            </td>
            <td class="ydgdict_col_3">
                <input class="ydgdict_add_entry_button" type="button" value="+" />
                <input class="ydgdict_del_entry_button" type="button" value="-" />
            </td>
        </tr>
        <tr class="ydgdict_entry_row_2" >
            <td class="ydgdict_col_1">
                <label class="ydgdict_entry_parent" >
                    <div>parent entry</div>
                    <input class="ydgdict_entry_parent_id" type="hidden" value="0" disabled />
                    <select class="ydgdict_entry_parent" >
                        <option value="0" >no parent</option>
                    </select>
                </label>
            </td>
            <td class="ydgdict_col_2" colspan="2" >
            <label class="ydgdict_entry_blog_post_ids" >
                <div>primary post</div>
                <input class="ydgdict_entry_blog_post_id" data-ydgdict_blog_post="0" type="hidden" />
                <select class="ydgdict_entry_blog_post_primary" ></select>
                <input class="ydgdict_entry_blog_post_add" placeholder="add article by id" />
            </label>
            </td>
        </tr>
    </tbody>
</template>
<template id="ydgdict_noun_info">
    <tr class="ydgdict_entry_info" >
        <td class="ydgdict_col_1">
            <select class="ydgdict_noun_gender" >
                <option disabled selected >gender</option>
                <option value="der" >der</option>
                <option value="die" >die</option>
                <option value="das" >das</option>
            </select>
        </td>
        <td class="ydgdict_col_2"><input class="ydgdict_noun_plural" placeholder="plural" /></td>
        <td class="ydgdict_col_3"></td>
    </tr>
</template>
<template id="ydgdict_verb_info">
    <tr class="ydgdict_entry_info" >
        <td class="ydgdict_col_1">
            <select class="ydgdict_verb_prefix" >
                <option value="" selected>no prefix</option>
            <?php foreach ($prefixes as $prefix ) : ?>
                <option value="<?php echo $prefix->slug; ?>" ><?php echo $prefix->name; ?></option>
            <?php endforeach; ?>
            </select>
        </td>
        <td class="ydgdict_col_2" colspan="2" >
            <select class="ydgdict_verb_aux" >
                <option value="hat" selected >hat</option>
                <option value="ist" >ist</option>
            </select>
            <input class="ydgdict_verb_past_part" placeholder="past participle" />
        </td>
    </tr>
    <tr class="ydgdict_entry_info" >
        <td class="ydgdict_col_1">
            <input class="ydgdict_verb_preterit" placeholder="preterit" />
        </td>
        <td class="ydgdict_col_2">
            <input class="ydgdict_verb_pres_part" placeholder="present participle" />
        </td>
        <td class="ydgdict_col_3">
        </td>
    </tr>
</template>
<template id="ydgdict_idea_row">
    <tr class="ydgdict_idea_row">
        <td class="ydgdict_col_1">
            <input class="ydgdict_idea_id" type="hidden" />
            <textarea class="ydgdict_idea" placeholder="idea" ></textarea>
        </td>
        <td class="ydgdict_col_2">
            <textarea class="ydgdict_idea_note" placeholder="notes" ></textarea>
        </td>
        <td class="ydgdict_col_3">
            <input class="ydgdict_add_idea_button" type="button" value="+" />
            <input class="ydgdict_del_idea_button" type="button" value="-" />
        </td>
    </tr>
</template>

</div>
<input id="ydgdict_post_button" type="submit" value="save entries" name="ydgdict_submit" />