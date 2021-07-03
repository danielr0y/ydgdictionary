<?php

$both = ydgdict_get_word_types_and_prefixes();
$types = $both['types'];
$prefixes = ydgdict_remove_child_prefixes( $both['prefixes'] );

if ( is_tax( 'word_type' ) ) $term = get_queried_object();
$is_prefix_verb = false;

?>
	<div class="ydgdict-prev-next-mobile">
		<div class="ydgdict-prev-next nav-previous">«<?php previous_post_link(); ?></div>
		<div class="nav-next ydgdict-prev-next"><?php next_post_link(); ?>»</div>
	</div>
<div id="ydgdict_glossary_search" > <!--- blanked outthrough display:none to avoid double search -->

	
<!-- blanked out previous next from dictionary 
	<div class="ydgdict-prev-next-desktop ydgdict-prev-next nav-previous"><?php// previous_post_link(); ?></div>
	 <div class="ydgdict-prev-next-desktop nav-next ydgdict-prev-next"><?php //next_post_link(); ?></div>
	-->
	
    <div id="ydgdict_search_toggle"></div>
    <form id="ydgdict_glossary_search_form" class="show" style="display:none; method="get" action="<?php echo get_post_type_archive_link( 'entry' ) ; ?>"> 
 
        <input id="ydgdict_glossary_filter_search" class="ydgdict_search_grid-item" placeholder="new search" type="search" name="s" value="<?php echo get_query_var( "s" ); ?>" />

        <input type="submit" id="ydgdict_glossary_filter_search_button" class="ydgdict_search_grid-item" value="search" />

        <?php $ydgdict_s_dest = (int)get_query_var( 's_dest', 0 ); ?>
        <select id="ydgdict_glossary_filter_search_dest" class="ydgdict_search_grid-item ydgdict_glossary_filter_search_dest" name="<?php if ( $ydgdict_s_dest ) echo "s_dest"; ?>">
            <option value="0" <?php if ( 0 == $ydgdict_s_dest ) echo "selected"; ?>>Search everywhere</option>
            <option value="1" <?php if ( 1 == $ydgdict_s_dest ) echo "selected"; ?>>only in vocab</option>
            <option value="2" <?php if ( 2 == $ydgdict_s_dest ) echo "selected"; ?>>only in translations</option>
        </select>

        <select id="ydgdict_glossary_filters_type" class="ydgdict_search_grid-item ydgdict_glossary_filters_type" name="<?php if ( '' != get_query_var( "word_type" ) ) echo "word_type"; ?>" >
            <option value selected>all word types</option>
        <?php foreach ( $types as $type ) : ?>
            <option value="<?php echo $type->slug; ?>" <?php if ( isset( $term ) ) { if ( $term == $type || $is_prefix_verb = $term->parent == $type->term_id ) echo "selected"; }?>><?php echo $type->name; ?>s</option>
        <?php endforeach; ?>
        </select>

    <!-- <template> -->
        <select id="ydgdict_glossary_filters_prefix" class="ydgdict_search_grid-item ydgdict_glossary_filters_prefix" name="<?php if ( $is_prefix_verb ) echo "word_type"; ?>" <?php if ( "verb" != get_query_var( "word_type" ) && ! $is_prefix_verb ) echo "disabled"; ?>>
            <option value >all verbs</option>
        <?php foreach ( $prefixes as $prefix ) : ?>
            <option value="<?php echo $prefix->slug; ?>" <?php if ( isset( $term ) ) { if ( $term == $prefix ) echo $selected = "selected"; } ?>><?php echo $prefix->name; ?>s</option>
        <?php endforeach; ?>
        </select>
    <!-- </template> -->

    </form>
	  <!-- Form end -->
	
   
	<div style="clear: both;">	</div>
</div>

<!-- "material to fade in and out the search ------>
<style>



.show {
}

.hide {
}
</style>

<script>
function fadeIn(el){
el.classList.add('show');
  el.style.display="flex";
  el.classList.remove('hide');  
}

function fadeOut(el){
  el.classList.add('hide');
  el.style.display="none";
  el.classList.remove('show');
}

var btn = document.getElementById('ydgdict_search_toggle'),
    searchBox = document.getElementById('ydgdict_glossary_search_form');

btn.addEventListener('click', function(){
  if (searchBox.className.indexOf('hide') !== -1) {
  //  fadeIn(searchBox);
  
  }
  else {
   // fadeOut(searchBox);

  }
});
</script>