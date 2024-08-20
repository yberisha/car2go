<?php

namespace Internship\PostTypes;

use Internship\Includes\Setup;

class Car {

    public static function getSingleCarData($carSlug) {
        $args = array(
            'post_type' => 'cars',
            'name' => $carSlug, // Filter by the car slug (used in the URL)
            'posts_per_page' => 1 
        );

        $query = new \WP_Query($args);

        if ($query->have_posts()) {
            $query->the_post();
            $carID = get_the_ID();
            $carDetails = get_field('car_details', $carID);

            $carData = [
                'title' => get_the_title($carID),
                'description' => $carDetails['description'],
                'car_image' => $carDetails['car_image'],
                'car_features' => $carDetails['car_features']
            ];

            wp_reset_postdata(); 

            return $carData;
        }
        
    }
}