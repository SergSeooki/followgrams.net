<?php

    add_action( 'init', 'myprefix_autocomplete_init' );  
    function myprefix_autocomplete_init() {  
        // Register our jQuery UI style and our custom javascript file  
        wp_register_script( 'my_acsearch', get_template_directory_uri() . '/nectar/assets/functions/ajax-search/wpss-search-suggest.js', array('jquery','jquery-ui-autocomplete'),null,true);  
        wp_localize_script( 'my_acsearch', 'MyAcSearch', array('url' => admin_url( 'admin-ajax.php' ))); 
		
        // Function to fire whenever search form is displayed  
        add_action( 'get_search_form', 'myprefix_autocomplete_search_form' );  
      
        // Functions to deal with the AJAX request - one for logged in users, the other for non-logged in users.  
        add_action( 'wp_ajax_myprefix_autocompletesearch', 'myprefix_autocomplete_suggestions' );  
        add_action( 'wp_ajax_nopriv_myprefix_autocompletesearch', 'myprefix_autocomplete_suggestions' );  
    }  
	
	function myprefix_autocomplete_search_form(){  
        wp_enqueue_script( 'my_acsearch' );  
    }  
	
	add_action( 'wp_ajax_{action}', 'my_hooked_function' );  
	add_action( 'wp_ajax_nopriv_{action}', 'my_hooked_function' );
	
	
	function myprefix_autocomplete_suggestions(){  

		$search_term = $_REQUEST['term'];
		$search_term = apply_filters('get_search_query', $search_term);
		
        $search_array = array(
        	's'=> $search_term, 
        	'showposts'   => 6,
        	'post_type' => 'any', 
        	'post_status' => 'publish', 
        	'post_password' => '',
        	'suppress_filters' => true
		);
		
	    $query = http_build_query($search_array);
		
	    $posts = get_posts( $query );

        // Initialise suggestions array  
        $suggestions=array();  
      
        global $post;  
        foreach ($posts as $post): setup_postdata($post);  
            // Initialise suggestion array  
            $suggestion = array();  
            $suggestion['label'] = esc_html($post->post_title);  
            $suggestion['link'] = get_permalink();  
			$suggestion['image'] = (has_post_thumbnail( $post->ID )) ? get_the_post_thumbnail($post->ID, 'thumbnail', array('title' => '')) : '<i class="icon-salient-pencil"></i>' ; 
      		
			if(get_post_type($post->ID) == 'post'){
				
				$suggestion['post_type'] = 'Blog Post'; 
				
			} else if(get_post_type($post->ID) == 'page'){
				
				$suggestion['post_type'] = 'Page'; 
				
			} else if(get_post_type($post->ID) == 'portfolio'){
				
				$suggestion['post_type'] = 'Portfolio Item'; 
				
				//show custom thumbnail if in use
				$custom_thumbnail = get_post_meta($post->ID, '_nectar_portfolio_custom_thumbnail', true); 
				if(!empty($custom_thumbnail) ){
					$attachment_id = pn_get_attachment_id_from_url($custom_thumbnail);
					$suggestion['image'] = wp_get_attachment_image($attachment_id,'portfolio-widget');
				}
				
			} else if(get_post_type($post->ID) == 'product'){
				
				$suggestion['post_type'] = 'Product'; 
			}
			
            // Add suggestion to suggestions array  
            $suggestions[]= $suggestion;  
        endforeach;  
      
        // JSON encode and echo  
        $response = $_GET["callback"] . "(" . json_encode($suggestions) . ")";  
        echo $response;  
      
        // Don't forget to exit!  
        exit;  
    }  	
	
?>