<?php


function wpbc_add_admin_menu()
{
    add_menu_page(
        'WooCategory',
        'WooCategory',
        'manage_options',
        'woocommerce-product-by-category',
        'wpbc_admin_page_html',
        'dashicons-admin-generic',
        20
    );
}
add_action('admin_menu', 'wpbc_add_admin_menu');

function wpbc_custom_admin_css()
{
    ?>
    <style>
        #toplevel_page_woocommerce-product-by-category .wp-menu-image {
            background-image: url('<?php echo plugins_url('icon.png', __FILE__); ?>');
            background-size: 20px 20px;
            background-repeat: no-repeat;
            background-position: center;
            width: 20px;
            height: 20px;
            display: inline-block;
        }

        #toplevel_page_woocommerce-product-by-category .wp-menu-image:before {
            content: '';
        }
    </style>
    <?php
}
add_action('admin_head', 'wpbc_custom_admin_css');

function wpbc_admin_page_html()
{
    $categories = get_terms(array(
        'taxonomy' => 'product_cat',
        'hide_empty' => false,
    ));

    if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
        ?>
        <div class="notice notice-error">
            <p>WooCategory requires WooCommerce to be installed and active.</p>
        </div>
        <?php
    } else {
        ?>
        <div class="wrap">
            <h1>Display WooCommerce Products By Category</h1>
            <form method="post" action="">
                <?php
                wp_nonce_field('wpbc_generate_code', 'wpbc_nonce');
                ?>
                <table class="form-table">
                    <tr>
                        <th scope="row">Category</th>
                        <td>
                            <select name="wpbc_category_slug">
                                <option value="">Select a category</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo esc_attr($category->slug); ?>" <?php selected($saved_category_slug, $category->slug); ?>>
                                        <?php echo esc_html($category->name); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <p class="description">Select the category for the products.</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Number of Products</th>
                        <td>
                            <input type="number" name="wpbc_limit" value="<?php echo esc_attr($saved_limit); ?>" />
                            <p class="description">Enter the number of products to display.</p>
                        </td>
                    </tr>
                </table>
                <?php submit_button('Generate Code Snippet'); ?>
            </form>

            <?php
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (!isset($_POST['wpbc_nonce']) || !wp_verify_nonce($_POST['wpbc_nonce'], 'wpbc_generate_code')) {
                    die('Security check');
                }

                $category_slug = sanitize_text_field($_POST['wpbc_category_slug']);
                $limit = intval($_POST['wpbc_limit']);

                if (empty($category_slug) || !term_exists($category_slug, 'product_cat')) {
                    echo '<div class="notice notice-error is-dismissible"><p><strong>Error:</strong> Please select a valid category.</p></div>';
                } else {
                    update_option('wpbc_category_slug', "");
                    update_option('wpbc_limit', '');

                    ?>
                    <h2>Generated Shortcode</h2>
                    <pre
                        style="background-color: white; border: 1px solid lightgray; border-radius: 8px; padding: 10px; display: inline-block;">[products_by_category category="<?php echo esc_attr($category_slug); ?>" limit="<?php echo esc_attr($limit); ?>"]</pre>
                    <?php
                }
            }
            ?>

            <span class="description">Use this code snippet where you want to display the products.</span>
        </div>
        <?php
    }
}
?>