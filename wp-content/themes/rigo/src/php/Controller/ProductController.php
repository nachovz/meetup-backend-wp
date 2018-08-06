<?php
namespace Rigo\Controller;

use \WP_Query;

class ProductController{
  
    public function getAllProducts(){
        $args = array(
            'post_type' => 'product'
        );

        $loop = new WP_Query( $args );
    
        return $loop->posts;
    }
}
?>