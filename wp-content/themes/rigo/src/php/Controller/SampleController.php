<?php
namespace Rigo\Controller;

use Rigo\Types\Course;

class SampleController{
    
    public function getHomeData(){
        return [
            'name' => 'Rigoberto'
        ];
    }
    
    public function getDraftCourses(){
        $query = Course::all(['post_status' => 'published' ]);
        return $query->posts;
    }
    
}
?>