<?php


// FREE USE FUNCTION
// CALL THIS FROM WHEREVER YOUD LIKE TO DISPLAY THE ENTRY PAGE CONTENT
function ydgdict_display_entry_page_content()
{
    ob_start();
    
    $entry = get_post();
    

    ?>   
<style>

#family-list
	{  column-width: 120px;
    /* background: blue; */
    /* column-gap: 30px; */
    min-height: 300px;
    /* height: 50px; */
    column-fill: ;
    /* display: flex; */
    /* flex-flow: column; */
    /* flex-wrap: wrap; */
    /* margin: auto; */
    /* width: fit-content;*/}
	
		.ydgdict_fam-link
	{    display: block;
    margin-top: 5px;
    margin-bottom: 5px;
    padding-left: 3px;
    line-height: 1.3em;
    margin-bottom: 14px;
	overflow-wrap: anywhere;}
	
	a.ydgdict-fam-level-1
	{      padding-left: 10px;
    color: black;
    /* font-weight: bold; */
    /* background: #ffffff; */
    
   }
	a.ydgdict-fam-level-2
	{     margin-left: 20px;
    color: #3d7788;
    border-left: solid .1px gray;}
	.ydgdict-fam-level-3
	{  margin-left:30px;
		color:gray;
	border-left: dashed .1px gray;
}
	.ydgdict-fam-level-4
	{ color:gray; margin-left:40px;}
	.ydgdict-fam-level-5
	{color:gray; margin-left:50px;}
	


</style>
    
    <script>
        function myFunction(clickID) 
        {
            var general = document.getElementById("general-content");
            var examples = document.getElementById("examples-content");
            var dig = document.getElementById("dig-content");
            var family= document.getElementById("family-content")

            var generalHeader = document.getElementById("general-header")
            var examplesHeader = document.getElementById("examples-header")
            var digHeader = document.getElementById("dig-header")
            var familyHeader = document.getElementById("family-header")

            var clickedContent= document.getElementById(clickID+"-content");
            var clickedHeader= document.getElementById(clickID+"-header");

		// reset all values
            general.style.display="none";
            examples.style.display="none";
            dig.style.display="none";
            family.style.display="none";
            generalHeader.style.background="#efefef";
            examplesHeader.style.background="#efefef";
            digHeader.style.background="#efefef";
            familyHeader.style.background="#efefef";
            generalHeader.style.color="black";
            examplesHeader.style.color="black";
            digHeader.style.color="black";
            familyHeader.style.color="black";

		//"activate" the parts needed

            clickedContent.style.display="block";
            clickedHeader.style.background="black";
            clickedHeader.style.color="#efefef";
        }
    </script>   
   

    
    
    <div class="header-wrapper">
		<div class="header-item" style="background:black; color:white;"id="general-header" onclick="myFunction('general')">Translations</div>
		<div class="header-item" id="examples-header" onclick="myFunction('examples')">Examples</div>
		<div class="header-item" id="dig-header" onclick="myFunction('dig')">Read more</div>
		<div class="header-item" id="family-header" onclick="myFunction('family')">Relatives</div>
	</div> <!-- end header-wrapper -->
	<div class="ydgdict-entry-contents ydgdict_glossary_entry_translation_wrapper" style="display:block" id="general-content">
      		<!------ ***** blanked out subheader  <div class="ydgdict_glossary_entry_sub_headers" ><strong>Translations:</strong></div>    -->
        <?php include( YDGDICT_PLUGIN_DIR . 'public/ydgdict_x_glossary_part_the_ideas.php' ); ?>
	</div>
   <div class="ydgdict-entry-contents ydgdict_glossary_entry_example_wrapper" id="examples-content">
	 		<!-- ***** blanked out subheader   <div class="ydgdict_glossary_entry_sub_headers"><strong>Examples:</strong></div>   -->
        <div class="temporary-under-construction">coming soon</div>
            <ul style="color:gray;">
                <li>Wir arbeiten fleißig daran, dass <em><strong>die Beispielsätze</strong></em> auch hier gezeigt werden!</li>
                <li>We are working hard to have <em><strong>the example sentences</strong></em> displayed here too!</li>
            </ul>
            <ul style="color:gray;">
                <li>...mit <em><strong>Aufnahmen</strong></em> und allem. So wie in den Artikeln!</li>
                <li>...with <em><strong>voice recordings</strong></em> and everything. Just like in the articles!</li>
            </ul>
            <ul style="color:gray;">
                <li>Bitte habt aber ein wenig <em><strong>Geduld</strong></em>. Das könnte noch etwas dauern.</li>
                <li>But please be <em><strong>patient</strong></em>. It might take a while yet.</li>
            </ul>
    </div>
    <div class="ydgdict-entry-contents" id="dig-content">
    
       
         	<!---- ***** blanked out subheader   <div class="ydgdict_glossary_entry_sub_headers"> <strong>Where to find more:<?php // if( count( $blog_post_ids ) > 1 ) echo "s"; ?></strong></div>     -->
          
    
        <?php $blog_post_ids = $entry->blog_post_ids; ?>
        <?php foreach ($blog_post_ids as $blog_post_id ) : ?>
            <div class="ydgdict_glossary_entry_desc_container">

                <?php $blog_post = get_post( $blog_post_id['post_id'] ); ?>
                <span class="ydgdict_dig_header ydgdict_glossary_button" ><?php echo $blog_post->post_title; ?></span>
                <?php include( YDGDICT_PLUGIN_DIR . 'public/ydgdict_x_glossary_part_the_description.php' ); ?>

            </div><!-- div.ydgdict_glossary_entry_desc_container -->
        <?php endforeach; ?>
    	</div>   
   <!--- the family relations --->
   <div class="ydgdict-entry-contents" id="family-content">
	  <?php 
// blanked out version that only gets the direct children
    // $args = array(
      //  'post_type'      => 'entry',
       // 'posts_per_page' => -1,
        //'post_parent'    => $entry->ID
   // );
  //  $children = new WP_Query( $args );

    // if ( $children->have_posts() ) : 
    ?>

      <!---- blanked out subheader   <div class="ydgdict_glossary_entry_sub_headers"><strong>Related:</strong></div> -
        <div class="ydgdict_glossary_entry_related">
            <ul>

            <?php // while ( $children->have_posts() ) : $children->the_post(); ?>

                <li><a href="<?php  // the_permalink(); ?>" title="<?php // the_title(); ?>"><?php // the_title(); ?></a></li>

            <?php // endwhile; ?>

            </ul>
        </div> 
    <?php //endif; wp_reset_postdata(); 
?>
-->
	   
	   	<div id="family-list">
		
	 
	   <?php
	
	

if ($entry->post_parent)	{
	$ancestors=get_post_ancestors($entry->ID);
	$root=count($ancestors)-1;
	$parent = $ancestors[$root]; // THis is ONLY the IDs, not the post objects	
	echo '<a class="ydgdict-fam-level-0" href="'; 
			 	echo get_permalink($ancestors[$root] );
			 	echo '" title="the translation">';
			 	echo get_the_title($ancestors[$root]);
				 echo '</a><br>';
/*	if ( $entry->ideas)
		{
		$first_idea = $entry->ideas[0];
		print ($first_idea['idea']);
		}
		; */
	
} else {
	$parent = $entry->ID;
}


	 $rec_level = '0';
	function get_posts_children($parent_id, $level) //
	{
    $children = array();
	$rec_level = $level+1;
    // grab the posts children
    $posts = get_posts( array( 'numberposts' => -1, 'post_status' => 'publish', 'post_type' => 'entry', 'post_parent' => $parent_id, 'suppress_filters' => false ));
		
    // now grab the grand children
   		 foreach( $posts as $child ){ 
			 // print the entry before searching its children
				 echo '<a class="ydgdict_fam-link ydgdict-fam-level-'.$rec_level.'" href="'; // VERY crud!! :D does the indent depending on level
			 	echo get_permalink( $child );
			 	echo '" title="the translation">';
			 	echo $child->post_title;
				 echo '</a>';
				 
       		 $gchildren = get_posts_children($child->ID,$rec_level); // recursion!! hurrah
      		 
       		 if( !empty($gchildren) ) {   
				
          		  $children = array_merge($children, $gchildren);  // merge the grand children into the children array
       		 	}
  		  }
    // merge in the direct descendants we found earlier
    $children = array_merge($children,$posts);
    return $children;
	wp_reset_postdata();
	}

// example of using above, lets call it and print out the results
$descendants = get_posts_children($parent, 0); //$entry->ID
	   
	   ?>
	
	      </div>  
    </div>

  



    <?php
    
    return ob_get_clean();
}



function ydgdict_entry_adjacent_where( $where, $in_same_term, $excluded_terms, $taxonomy, $post )
{
    if ( 'entry' != $post->post_type ) return $where;
    
    global $wpdb;
    return preg_replace( '/post_date\s(\>|\<).+?(?=AND)/', $wpdb->prepare( "post_excerpt $1 '%s' ", $post->post_excerpt ), $where );
}
add_filter( 'get_previous_post_where', 'ydgdict_entry_adjacent_where', 10, 5 );
add_filter( 'get_next_post_where', 'ydgdict_entry_adjacent_where', 10, 5 );



function ydgdict_entry_adjacent_sort( $order_by, $post )
{
    if ( 'entry' != $post->post_type ) return $order_by;
    
    return str_replace( 'post_date', 'post_excerpt', $order_by );
}
add_filter( 'get_previous_post_sort', 'ydgdict_entry_adjacent_sort', 10, 2 );
add_filter( 'get_next_post_sort', 'ydgdict_entry_adjacent_sort', 10, 2 );