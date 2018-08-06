<?php
namespace Rigo\Controller;

use Rigo\Types\Meetup;

class MeetupController{
    
    public function getHomeData(){
        return [
            'name' => 'Rigoberto'
        ];
    }
    
    public function getMeetups(){
        $query = Meetup::all([ 
            'post_status'   => 'publish', 
            'orderby'       => 'date',
            'order'         => 'ASC']);
        return $query->posts;
    }
    
}
?>