<?php

function wpbc_display_products_by_category($atts) {
    
    $atts = shortcode_atts(array(
        'category' => '',
        'limit' => 10,
    ), $atts, 'products_by_category');

   
    $args = array(
        'post_type' => 'product',
        'posts_per_page' => $atts['limit'],
        'tax_query' => array(
            array(
                'taxonomy' => 'product_cat',
                'field' => 'slug',
                'terms' => $atts['category'],
            ),
        ),
        'meta_query' => WC()->query->get_meta_query(),
    );

    $query = new WP_Query($args);

   
    ob_start();

    if ($query->have_posts()) {
        woocommerce_product_loop_start(); 
        while ($query->have_posts()) {
            $query->the_post();
            wc_get_template_part('content', 'product'); 
        }
        woocommerce_product_loop_end(); 
    } else {
        echo '<p>' . __('No products found in this category.', 'woocommerce') . '</p>'; 
    }


    wp_reset_postdata();

    return ob_get_clean();
}
add_shortcode('products_by_category', 'wpbc_display_products_by_category');