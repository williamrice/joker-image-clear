<?php
/*
Plugin Name: Joker Image Meta Clear
Description: A plugin that clears the title, caption, and description metadata for all images in the Media Library.
Version: 1.0
Author: Joker BS
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Function to clear image metadata
function clear_image_meta_data()
{
    // Query all media attachments
    $args = array(
        'post_type'      => 'attachment',
        'post_status'    => 'inherit',
        'posts_per_page' => -1, // Retrieve all attachments
        'post_mime_type' => 'image', // Target only images
    );

    $attachments = get_posts($args);

    if ($attachments) {
        foreach ($attachments as $attachment) {
            // Clear caption (stored in post_excerpt) and description (stored in post_content)
            error_log(print_r($attachment, true));
            wp_update_post(array(
                'ID'           => $attachment->ID,
                'post_title'   => '', // Title
                'post_excerpt' => '', // Caption
                'post_content' => '', // Description
            ));

            // Clear custom metadata like title (stored in _wp_attachment_image_alt)
            update_post_meta($attachment->ID, '_wp_attachment_image_alt', ''); // Clear ALT text
        }
    }
}

// Add an admin menu item
function clear_metadata_admin_menu()
{
    add_menu_page(
        'Joker Image Meta Clear', // Page title
        'Joker Image Meta Clear',       // Menu title
        'manage_options',       // Capability
        'clear-image-metadata', // Menu slug
        'clear_metadata_admin_page', // Callback function
        'dashicons-image-filter', // Icon
        100                     // Position
    );
}
add_action('admin_menu', 'clear_metadata_admin_menu');

// Admin page content
function clear_metadata_admin_page()
{
    // Check if the user clicked the button
    if (isset($_POST['clear_metadata_action']) && check_admin_referer('clear_metadata_nonce')) {
        clear_image_meta_data();
        echo '<div class="updated"><p>Image metadata cleared successfully!</p></div>';
    }
?>
    <div class="wrap">
        <h1>Clear Image Metadata</h1>
        <p>Click the button below to clear the title, caption, and description metadata for all images in the Media Library.</p>
        <form method="post">
            <?php wp_nonce_field('clear_metadata_nonce'); ?>
            <input type="hidden" name="clear_metadata_action" value="1">
            <button type="submit" class="button button-primary">Clear Metadata</button>
        </form>
    </div>
<?php
}
