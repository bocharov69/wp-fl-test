<?php
/**
 * Template Name: Real Estate Catalog
 * 
 * @package Understrap
 */


get_header();

$container = get_theme_mod('understrap_container_type');

// Query arguments to get posts of the custom post type 't_realestate'
$paged = get_query_var('paged') ? get_query_var('paged') : 1;
$args = array(
    'post_type' => 't_realestate',
    'posts_per_page' => 4, // Number of posts to display per page
    'post_status' => 'publish', // Ensure the posts are published
    'paged' => $paged, // Pagination
);

$query = new WP_Query($args);

?>

<div class="wrapper" id="index-wrapper">

    <div class="<?php echo esc_attr($container); ?>" id="content" tabindex="-1">

        <div class="row">

            <?php
            // Do the left sidebar check and open div#primary.
            get_template_part('global-templates/left-sidebar-check');
            ?>

            <main class="site-main" id="main">
                <h1>Каталог недвижимости</h1>

                <?php
                if ($query->have_posts()) {
                    // Start the Loop.
                    while ($query->have_posts()) {
                        $query->the_post();
                        get_template_part('loop-templates/content', 'search');
                    }

                    // Pagination 
                    $total_pages = $query->max_num_pages;
                    if ($total_pages > 1) {
                        echo '<div class="live-search-pagination rounded border overflow-hidden d-inline-flex">';
                        for ($i = 1; $i <= $total_pages; $i++) {
                            $ifcurrent = 'text-primary';
                            if ($i == $paged)
                                $ifcurrent = 'text-white bg-primary';
                            echo '<a href="#" style="padding: .5rem .75rem;" class="' . $ifcurrent . ' live-search-page border-right text-center" data-page="' . $i . '">' . $i . '</a> ';
                        }
                        echo '<a href="#" style="padding: .5rem .75rem;" class="live-search-page text-primary border-right text-center" data-page="' . $paged + 1 . '">»</a> ';
                        echo '</div>';
                    }
                } else {
                    get_template_part('loop-templates/content', 'none');
                }
                ?>

            </main>

            <?php
            // Do the right sidebar check and close div#primary.
            get_template_part('global-templates/right-sidebar-check');
            ?>

        </div><!-- .row -->

    </div><!-- #content -->

</div><!-- #index-wrapper -->

<?php
get_footer();
