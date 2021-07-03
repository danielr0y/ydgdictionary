<?php





function ydgdict_separate_notes_from_ideas( $idea ){
    preg_match('/^(.*?)(?:\((([^()]++|\((?3)\))++(?<!\(ly|\(s|\(es))\)\s?)?$/', $idea, $matches);   
    $idea = ( isset($matches[1]) ) ? $matches[1] : "";
    $note = ( isset($matches[2]) ) ? $matches[2] : "";
    $idea_and_note = array( 'idea_id' => NULL, 'idea' => $idea, 'idea_note' => $note);
    return $idea_and_note; 
}





function ydgdict_parse_line( $data, $blog_post_id )
{
    $line = array( 
            "entry" => "",
            "entry_type" => "",
            'gender' => "",
            'plural' => "",
            'aux' => "",
            'past_part' => "",
            'preterit' => "",
            'pres_part' => "",
            "blog_post_ids" => array( array( 'post_id' => $blog_post_id, 'primary_post' => 1, 'post_title' => 'this post' ) ),
            "ideas" => array() 
    );

    // categorise the entries
    preg_match('/^(?:(?:(?P<gender>[dD](?:er|ie|as))\s+(?P<noun>\S+)(?:,\s*die\s(?P<plural>\S+))?)|(?P<phrase>(?:\S+(?:\s+|$)){2,})|(?P<verb>\S+(?:en|rn|ln)\b)|(?P<adjective>\S{0,6}(?:ge|durch|über|unter|um|wider|miss|ver|zer|voll|be|emp|ent|er)\S+(?:t|en)|\S+(?:(?:ig|haft|los|lich|isch|end|voll|bar)\b|\s*\(adj\S{0,6}\)))|(?P<sonstiges>\S+))\s*$/', 
            $data[0], $matches);

    // find the match and note the word type
    foreach ($matches as $key => $match) {
        // skip int() ie. only look at named keys
        if ( is_int( $key ) ) continue;
        // examine only non-empty items
        elseif ( !empty( $match ) ) {
            if ( 'gender' == $key ){
                $line['gender'] = $match;
            }elseif ('plural' == $key ) {
                $line['plural'] = $match;
            }else {
                $line['entry_type'] = $key;
                $line['entry'] = $match;
            }
        }
    }

    $ideas = ( count($data) > 1 ) ? $data[1] : "";
    
    // break the string up into semi-colin deliminated parts ie.) multiple ideas for the same entry
    $line['ideas'] = preg_split('/[;]+/', $ideas);

    // loop through the idea array, looking for notes and outputting everything to the screen
    $max = count($line['ideas']);
    for ($i=0; $i < $max; $i++) {
        // this needs to be checked to see if it's empty so I may as well store the trimmed value
        $line['ideas'][$i] = trim( $line['ideas'][$i] ); 

        // is this idea empty?
        if ( $line['ideas'][$i] == "" ) continue;

        // separate the note from the idea
        $line['ideas'][$i] = ydgdict_separate_notes_from_ideas( $line['ideas'][$i] );
    }
    return $line;
}





function ydgdict_get_csv_lines( $file, $blog_post_id )
{
    // open a stream
    if (($handle = fopen($file, 'r')) !== FALSE) {
        // check if it's not already UTF-8
        $firstline = fgets($handle);
        rewind( $handle );
        if ( mb_detect_encoding( $firstline, 'UTF-8', true ) != 'UTF-8' ){
            // convert the stream data from UTF-16 to UTF-8
            stream_filter_append( $handle, 'convert.iconv.UTF-16/UTF-8' );
        }

        $lines = array( );
        // create an array from each line as an = deliminated csv file
        while (($data = fgetcsv($handle, 0, "=")) !== FALSE) {            
            // skip line if there's no entry
            if ( trim($data[0]) == "" ) continue;

            // add parsed_line to the data to send back to javascript
            $lines[] = ydgdict_parse_line( $data, $blog_post_id );
        }
        fclose($handle);
    }
    return $lines;
}