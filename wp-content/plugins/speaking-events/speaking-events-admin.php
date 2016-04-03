<?php

add_filter("manage_edit-".TYPE."_columns", CPTPREFIX."_edit_columns");  
add_action("manage_posts_custom_column",  CPTPREFIX."_custom_columns");  
  
function cse_edit_columns($columns){  
        $columns = array(  
            "cb" => "<input type=\"checkbox\" />",  
            "title" => "Talk",  
            "description" => "Description", 
            "event" => "Event",  
            "eventdate" => "Date",  
            "mo" => "Updated",  
        );  
  
        return $columns;  
}  
  
function cse_custom_columns($column){  
        global $post;  
        switch ($column)  
        {  
        	case "event":
        		$custom = get_post_custom();  
                echo $custom["eventname"][0];  
                break;  
            case "eventdate":
        		$custom = get_post_custom();  
                echo $custom["eventdate"][0];  
                break; 
            case "description":  
                the_excerpt();  
                break;  
           case "mod":  
                $dateFinished= get_the_modified_date('\<\s\t\r\o\n\g\>Y\<\/\s\t\r\o\n\g\>: F, j');  
                $custom= get_post_custom();
                if($custom['bookStatus'][0] == -1 && $dateFinished){
                	echo $dateFinished;
                }  
                break;  
        }  
}

?>