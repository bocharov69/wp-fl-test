<?php
/**
 * Template Name: Real Estate Page
 * Template Post Type: t_realestate
 * @package Understrap
 */

// Exit if accessed directly.
defined('ABSPATH') || exit;

get_header();

$container = get_theme_mod('understrap_container_type');

?>

<div class="wrapper" id="page-wrapper">

    <div class="<?php echo esc_attr($container); ?>" id="content" tabindex="-1">

        <div class="row">

            <?php
            // Do the left sidebar check and open div#primary.
            get_template_part('global-templates/left-sidebar-check');
            ?>

            <main class="site-main" id="main">

                <?php
                while (have_posts()) {
                    the_post();
                    get_template_part('loop-templates/content', 'page');
                    ?>

                    <div class="acf-fields">
                        <h5 class="font-weight-bold mt-4 mb-1">Здание</h5>
                        <?php if ($fields = get_field_objects()): ?>
                            <?php foreach ($fields as $field): ?>
                                <div class="acf-field mt-2">
                                    <?php if ($field['type'] === 'group'): ?>
                                        <?php if ($field['value'] == '') break; ?>
                                        <div class="acf-field-group bg-light mt-5 p-3 rounded">
                                            <label class="acf-field-group-label font-weight-bold h5"><?= $field['label'] ?></label>
                                            <?php foreach ($field['value'] as $key => $value): ?>
                                                <div class="mb-2">
                                                    <label class="acf-field-group-label"><?= $key ?></label>
                                                    <?php if (is_array($value) && $value['type'] === 'image'): ?>
                                                        <div class="form-control"
                                                            style="display: flex; padding: .75rem; height: auto; justify-content: center">
                                                            <?= wp_get_attachment_image($value['ID'], 'full') ?>
                                                        </div>
                                                    <?php else: ?>
                                                        <div class="acf-field-text form-control"><?= $value ?></div>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php elseif ($field['type'] === 'image'): ?>
                                        <?php if ($field['value'] == '') break; ?>
                                        <label class="acf-field-label small"><?= $field['label'] ?></label>
                                        <div class="form-control"
                                            style="display: flex; padding: .75rem; height: auto; justify-content: center">
                                            <?= wp_get_attachment_image($field['value']['ID'], 'full') ?>
                                        </div>
                                    <?php else: ?>
                                        <?php if ($field['value'] == '') break; ?>
                                        <label class="acf-field-label small"><?= $field['label'] ?></label>
                                        <div class="acf-field-text form-control"><?= $field['value'] ?></div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>




                    <!-- function r($var)
                                    {
                                        echo '<pre>';
                                        print_r($var);
                                        echo '</pre>';
                                    } 
                                    r($field)
                                     -->

                    <!-- // don't forget to replace 'image' with your field name
                    $imageID = get_field('image');
                    // can be one of the built-in sizes ('thumbnail', 'medium', 'large', 'full' or a custom size)
                    $size = 'full';

                    if ($imageID) {
                        // creates the img tag
                        echo wp_get_attachment_image($imageID, $size);
                    } -->
                    <?php
                    // If comments are open or we have at least one comment, load up the comment template.
                    if (comments_open() || get_comments_number()) {
                        comments_template();
                    }
                }
                ?>

            </main>

            <?php
            // Do the right sidebar check and close div#primary.
            get_template_part('global-templates/right-sidebar-check');
            ?>

        </div><!-- .row -->

    </div><!-- #content -->

</div><!-- #page-wrapper -->

<?php
get_footer();
