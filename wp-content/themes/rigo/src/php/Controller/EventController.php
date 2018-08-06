<?php
namespace Rigo\Controller;

use Rigo\Types\Event;

class EventController{
    
    public function getHomeData(){
        return [
            'name' => 'Rigoberto'
        ];
    }
    
    public function getEvents(){
        $query = Event::all([ 
            'status' => 'publish']);
            
        if ( $query->have_posts() ) {
        	while ( $query->have_posts() ) {
        		$query->the_post();
        		
        		//Include the Meta Tags and Values
        		$query->post->meta_keys = get_post_meta($query->post->ID);
        		foreach($query->post->meta_keys as $key => $value){
        		    $query->post->meta_keys[$key] = maybe_unserialize($value[0]);
        		}
        		//Include the Featured Image
        		$query->post->thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id( $query->post->ID ), "large" );
        	}
        	/* Restore original Post Data */
        	wp_reset_postdata();
        }
        
        return $query->posts;
    }
    
    public function getEvent(){
        $query = Event::all([ 
            'status' => 'publish',
            'p' => $id]);
            //var_dump($query);
        if ( $query->have_posts() ) {
        	while ( $query->have_posts() ) {
        		$query->the_post();
        
        		//Include the Meta Tags and Values
        		$query->post->meta_keys = get_post_meta($query->post->ID);
        		
        		//Include the Featured Image
        		$query->post->thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id( $query->post->ID ), "large" );
        	}
        	/* Restore original Post Data */
        	wp_reset_postdata();
        }
        
        return $query->posts;
    }
    
    function registerRSVP( $request ) {
        // Here we are grabbing the 'id' path variable from the $request object. WP_REST_Request implements ArrayAccess, which allows us to grab properties as though it is an array.
        $id = $request['id'];
        $query = Event::all([
            'p' => $id]);
        
        if( $query->have_posts() ){

            while ( $query->have_posts() ) {
        		$query->the_post();
        		$data = $request->get_json_params();
        		//delete_post_meta($query->post->ID, '_rsvp');

                if ( isset( $data["username"] ) && isset( $data["answer"] ) ) {
                    
                    $completion = true;
                    
                    if( $data["answer"] === "yes" ){
                        $completion = $this->updateRSVPMetaFields("_rsvpYes", "_rsvpNo", $data["username"], $query->post->ID);
                    }else if($data["answer"] === "no"){
                        $completion = $this->updateRSVPMetaFields("_rsvpNo", "_rsvpYes", $data["username"], $query->post->ID);
                    }else{
                        return rest_ensure_response( "unable to RSVP" );
                    }
                    
                    if($completion){
                        return rest_ensure_response( "rsvp Saved" );
                    }else{
                        return rest_ensure_response( "RSVP already done!" );
                    }
                }else{
                    return rest_ensure_response( "unable to RSVP" );
                }
                
            }
        }else{
            return rest_ensure_response( new WP_Error('error finding event') );
        }
    }
    
    function updateRSVPMetaFields($rsvpArray1, $rsvpArray2, $userToInclude, $currentPostId){
        
        if ( ! add_post_meta( $currentPostId, $rsvpArray1, [$userToInclude], true ) ) { 
            $rsvp = get_post_meta($currentPostId, $rsvpArray1);
            $current_rsvp = $rsvp[0];
            $new_rsvp = array();
            
            $rsvp2 = get_post_meta($currentPostId, $rsvpArray2, false);
            
            if(count($rsvp2) >= 1){
                $current_rsvp2 = $rsvp2[0];
                
                foreach($current_rsvp2 as $key2 => $value2){
                    if( $userToInclude === $value2){
                        unset( $current_rsvp2[$key2]);
                        update_post_meta ( $currentPostId, $rsvpArray2,  $current_rsvp2 );
                    }
                }
            }
            
            foreach($current_rsvp as $key => $value){
                
                if( $userToInclude !== $value) {
                    $new_rsvp[]=$value;
                }else{
                    return false;
                }
            }
            $new_rsvp[]=$userToInclude;
            
            update_post_meta ( $currentPostId, $rsvpArray1,  $new_rsvp );
            return true;
        }else{
            
            $rsvp2 = get_post_meta($currentPostId, $rsvpArray2);
            
            if(count($rsvp2) >= 1){
                $current_rsvp2 = $rsvp2[0];
                
                foreach($current_rsvp2 as $key2 => $value2){
                    if( $userToInclude === $value2){
                        unset( $current_rsvp2[$key2]);
                        update_post_meta ( $currentPostId, $rsvpArray2,  $current_rsvp2 );
                    }
                }
            }
            
            return true;
        }
    }
    
    function saveEvent($request){
        echo "Ill be saved!";
    }
    
}
?>