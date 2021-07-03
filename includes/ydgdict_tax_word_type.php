<?php



function ydgdict_init_taxonomy_word_type()
{
        $labels = [
            "name" => "Word Types",
            "singular_name" => "Word Type",
        ];
    
        $args = [
            "labels"            => $labels,
            "public"            => true,
            "hierarchical"      => true,
            'rewrite'           => array( 'slug' => 'dictionary' ),
            "show_admin_column" => true,
            // "show_in_rest"      => true,
            // "rest_base"         => "word_types",
            // "capabilities"      => TODO: might be smart to set these. if they're changed they'll be recreated on activaton/update
        ];
    register_taxonomy( "word_type", [ "entry" ], $args );
}
add_action( 'init', 'ydgdict_init_taxonomy_word_type' );





function ydgdict_rewrite_rules_array( $rules )
{
    return $rules;
}
// add_filter( 'word_type_rewrite_rules', 'ydgdict_rewrite_rules_array' );






function ydgdict_init_word_type_terms()
{    
    wp_insert_term(
        'noun',
        'word_type',
        array(
            'description' => 'noun',
            'slug'        => 'noun',
        )
    );
	wp_insert_term(
        'root',
        'word_type',
        array(
            'description' => 'Indo-European root',
            'slug'        => 'indo-european-root',
        )
    );
    wp_insert_term(
        'term',
        'word_type',
        array(
            'description' => 'Grammar Term',
            'slug'        => 'grammar-term',
        )
    );
    wp_insert_term(
        'phrase',
        'word_type',
        array(
            'description' => 'phrase',
            'slug'        => 'phrase',
        )
    );
    wp_insert_term(
        'adjective',
        'word_type',
        array(
            'description' => 'adjective',
            'slug'        => 'adjective',
        )
    );
    wp_insert_term(
        'adverb',
        'word_type',
        array(
            'description' => 'adverb',
            'slug'        => 'adverb',
        )
    );
    wp_insert_term(
        'particle',
        'word_type',
        array(
            'description' => 'particle',
            'slug'        => 'particle',
        )
    );
    wp_insert_term(
        'preposition',
        'word_type',
        array(
            'description' => 'preposition',
            'slug'        => 'preposition',
        )
    );
    wp_insert_term(
        'conjunction',
        'word_type',
        array(
            'description' => 'conjunction',
            'slug'        => 'conjunction',
        )
    );
    wp_insert_term(
        'pronoun',
        'word_type',
        array(
            'description' => 'pronoun',
            'slug'        => 'pronoun',
        )
    );
    wp_insert_term(
        'verb',
        'word_type',
        array(
            'description' => 'verb',
            'slug'        => 'verb',
        )
    );

    $parent_term = term_exists( 'verb', 'word_type' );
    $parent_term_id = $parent_term['term_id'];
    wp_insert_term(
        'ab verb',
        'word_type',
        array(
            'description' => 'seperable',
            'slug'        => 'ab_verb',
            'parent'      => $parent_term_id,
        )
    );
    wp_insert_term(
        'an verb',
        'word_type',
        array(
            'description' => 'seperable',
            'slug'        => 'an_verb',
            'parent'      => $parent_term_id,
        )
    );
    wp_insert_term(
        'auf verb',
        'word_type',
        array(
            'description' => 'seperable',
            'slug'        => 'auf_verb',
            'parent'      => $parent_term_id,
        )
    );
    wp_insert_term(
        'aus verb',
        'word_type',
        array(
            'description' => 'seperable',
            'slug'        => 'aus_verb',
            'parent'      => $parent_term_id,
        )
    );
    wp_insert_term(
        'be verb',
        'word_type',
        array(
            'description' => 'inseperable',
            'slug'        => 'be_verb',
            'parent'      => $parent_term_id,
        )
    );
    wp_insert_term(
        'bei verb',
        'word_type',
        array(
            'description' => 'seperable',
            'slug'        => 'bei_verb',
            'parent'      => $parent_term_id,
        )
    );
    wp_insert_term(
        'durch verb',
        'word_type',
        array(
            'description' => 'dont use this one. use one of its children',
            'slug'        => 'durch_verb',
            'parent'      => $parent_term_id,
        )
    );
    wp_insert_term(
        'ein verb',
        'word_type',
        array(
            'description' => 'seperable',
            'slug'        => 'ein_verb',
            'parent'      => $parent_term_id,
        )
    );
    wp_insert_term(
        'emp verb',
        'word_type',
        array(
            'description' => 'inseperable',
            'slug'        => 'emp_verb',
            'parent'      => $parent_term_id,
        )
    );
    wp_insert_term(
        'ent verb',
        'word_type',
        array(
            'description' => 'inseperable',
            'slug'        => 'ent_verb',
            'parent'      => $parent_term_id,
        )
    );
    wp_insert_term(
        'er verb',
        'word_type',
        array(
            'description' => 'inseperable',
            'slug'        => 'er_verb',
            'parent'      => $parent_term_id,
        )
    );
    wp_insert_term(
        'ge verb',
        'word_type',
        array(
            'description' => 'inseperable',
            'slug'        => 'ge_verb',
            'parent'      => $parent_term_id,
        )
    );
    wp_insert_term(
        'her verb',
        'word_type',
        array(
            'description' => 'seperable',
            'slug'        => 'her_verb',
            'parent'      => $parent_term_id,
        )
    );
    wp_insert_term(
        'hin verb',
        'word_type',
        array(
            'description' => 'seperable',
            'slug'        => 'hin_verb',
            'parent'      => $parent_term_id,
        )
    );
    wp_insert_term(
        'hinter verb',
        'word_type',
        array(
            'description' => 'dont use this one. use one of its children',
            'slug'        => 'hinter_verb',
            'parent'      => $parent_term_id,
        )
    );
    wp_insert_term(
        'los verb',
        'word_type',
        array(
            'description' => 'separable',
            'slug'        => 'los_verb',
            'parent'      => $parent_term_id,
        )
    );
    wp_insert_term(
        'miss verb',
        'word_type',
        array(
            'description' => 'inseparable',
            'slug'        => 'miss_verb',
            'parent'      => $parent_term_id,
        )
    );
    wp_insert_term(
        'mit verb',
        'word_type',
        array(
            'description' => 'separable',
            'slug'        => 'mit_verb',
            'parent'      => $parent_term_id,
        )
    );
    wp_insert_term(
        'nach verb',
        'word_type',
        array(
            'description' => 'separable',
            'slug'        => 'nach_verb',
            'parent'      => $parent_term_id,
        )
    );
    wp_insert_term(
        'um verb',
        'word_type',
        array(
            'description' => 'dont use this one. use one of its children',
            'slug'        => 'um_verb',
            'parent'      => $parent_term_id,
        )
    );
    wp_insert_term(
        'unter verb',
        'word_type',
        array(
            'description' => 'dont use this one. use one of its children',
            'slug'        => 'unter_verb',
            'parent'      => $parent_term_id,
        )
    );
    wp_insert_term(
        'ver verb',
        'word_type',
        array(
            'description' => 'inseparable',
            'slug'        => 'ver_verb',
            'parent'      => $parent_term_id,
        )
    );
    wp_insert_term(
        'vor verb',
        'word_type',
        array(
            'description' => 'separable',
            'slug'        => 'vor_verb',
            'parent'      => $parent_term_id,
        )
    );
    wp_insert_term(
        'weg verb',
        'word_type',
        array(
            'description' => 'separable',
            'slug'        => 'weg_verb',
            'parent'      => $parent_term_id,
        )
    );
    wp_insert_term(
        'weiter verb',
        'word_type',
        array(
            'description' => 'separable',
            'slug'        => 'weiter_verb',
            'parent'      => $parent_term_id,
        )
    );
    wp_insert_term(
        'wieder verb',
        'word_type',
        array(
            'description' => 'separable',
            'slug'        => 'wieder_verb',
            'parent'      => $parent_term_id,
        )
    );
    wp_insert_term(
        'wider verb',
        'word_type',
        array(
            'description' => 'dont use this one. use one of its children',
            'slug'        => 'wider_verb',
            'parent'      => $parent_term_id,
        )
    );
    wp_insert_term(
        'zer verb',
        'word_type',
        array(
            'description' => 'inseparable',
            'slug'        => 'zer_verb',
            'parent'      => $parent_term_id,
        )
    );
    wp_insert_term(
        'zu verb',
        'word_type',
        array(
            'description' => 'separable',
            'slug'        => 'zu_verb',
            'parent'      => $parent_term_id,
        )
    );
    wp_insert_term(
        'zurück verb',
        'word_type',
        array(
            'description' => 'separable',
            'slug'        => 'zurück_verb',
            'parent'      => $parent_term_id,
        )
    );
    wp_insert_term(
        'zusammen verb',
        'word_type',
        array(
            'description' => 'separable',
            'slug'        => 'zusammen_verb',
            'parent'      => $parent_term_id,
        )
    );
    wp_insert_term(
        'über verb',
        'word_type',
        array(
            'description' => 'dont use this one. use one of its children',
            'slug'        => 'über_verb',
            'parent'      => $parent_term_id,
        )
    );




    $parent_term = term_exists( 'durch_verb', 'word_type' );
    $parent_term_id = $parent_term['term_id'];
    wp_insert_term(
        'durch (insep)',
        'word_type',
        array(
            'description' => 'inseparable',
            'slug'        => 'durch_insep_verb',
            'parent'      => $parent_term_id,
        )
    );
    wp_insert_term(
        'durch (sep)',
        'word_type',
        array(
            'description' => 'separable',
            'slug'        => 'durch_sep_verb',
            'parent'      => $parent_term_id,
        )
    );

    
    $parent_term = term_exists( 'hinter_verb', 'word_type' );
    $parent_term_id = $parent_term['term_id'];
    wp_insert_term(
        'hinter (insep)',
        'word_type',
        array(
            'description' => 'inseparable',
            'slug'        => 'hinter_insep_verb',
            'parent'      => $parent_term_id,
        )
    );
    wp_insert_term(
        'hinter (sep)',
        'word_type',
        array(
            'description' => 'separable',
            'slug'        => 'durch_sep_verb',
            'parent'      => $parent_term_id,
        )
    );

    
    $parent_term = term_exists( 'um_verb', 'word_type' );
    $parent_term_id = $parent_term['term_id'];
    wp_insert_term(
        'um (insep)',
        'word_type',
        array(
            'description' => 'inseparable',
            'slug'        => 'um_insep_verb',
            'parent'      => $parent_term_id,
        )
    );
    wp_insert_term(
        'um (sep)',
        'word_type',
        array(
            'description' => 'separable',
            'slug'        => 'um_sep_verb',
            'parent'      => $parent_term_id,
        )
    );

    
    $parent_term = term_exists( 'unter_verb', 'word_type' );
    $parent_term_id = $parent_term['term_id'];
    wp_insert_term(
        'unter (insep)',
        'word_type',
        array(
            'description' => 'inseparable',
            'slug'        => 'unter_insep_verb',
            'parent'      => $parent_term_id,
        )
    );
    wp_insert_term(
        'unter (sep)',
        'word_type',
        array(
            'description' => 'separable',
            'slug'        => 'unter_sep_verb',
            'parent'      => $parent_term_id,
        )
    );

    
    $parent_term = term_exists( 'wider_verb', 'word_type' );
    $parent_term_id = $parent_term['term_id'];
    wp_insert_term(
        'wider (insep)',
        'word_type',
        array(
            'description' => 'inseparable',
            'slug'        => 'wider_insep_verb',
            'parent'      => $parent_term_id,
        )
    );
    wp_insert_term(
        'wider (sep)',
        'word_type',
        array(
            'description' => 'separable',
            'slug'        => 'wider_sep_verb',
            'parent'      => $parent_term_id,
        )
    );

    
    $parent_term = term_exists( 'über_verb', 'word_type' );
    $parent_term_id = $parent_term['term_id'];
    wp_insert_term(
        'über (insep)',
        'word_type',
        array(
            'description' => 'inseparable',
            'slug'        => 'über_insep_verb',
            'parent'      => $parent_term_id,
        )
    );
    wp_insert_term(
        'über (sep)',
        'word_type',
        array(
            'description' => 'separable',
            'slug'        => 'über_sep_verb',
            'parent'      => $parent_term_id,
        )
    );

}