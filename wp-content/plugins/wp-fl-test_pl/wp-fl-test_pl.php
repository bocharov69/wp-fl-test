<?php
/* 
Plugin Name: wp-fl-test_pl 

*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}



function t_register_taxonomy()
{

    $args = array(
        'labels' => array(
            'name' => 'Район',
            'menu_name' => 'Район'
        ),
        'rewrite' => array(
            'slug' => 'district',
        ),
        'public' => true,
    );
    register_taxonomy('t_district', null, $args);
}

function t_register_post_type()
{

    $labels = array(
        'name' => 'Недвижимость',
        'singular_name' => 'Недвижимость',
        'menu_name' => 'Недвижимость',
        'all_items' => 'Вся недвижимость',
        'search_items' => 'Искать недвижимость',
        'edit_item' => 'Изменить недвижимость',
        'view_item' => 'Просмотр недвижимость',
        'update_item' => 'Обновить недвижимость',
        'add_new' => 'Добавить новую недвижимость',
        'add_new_item' => 'Добавить новую недвижимость',
        'new_item_name' => 'Название новой недвижимости',
        'popular_items' => 'Популярная недвижимость',
        'parent_item' => 'Родительская недвижимость',
        'parent_item_colon' => 'Родительская недвижимость:',
        'separate_items_with_commas' => 'Разделяйте недвижимость запятыми',
        'add_or_remove_items' => 'Добавить или удалить недвижимость',
        'choose_from_most_used' => 'Выбрать из часто используемой недвижимости',
        'not_found' => 'Недвижимость не найдена',
        'back_to_items' => '← Назад к недвижимости',
    );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'hierarchical' => true,
        'taxonomies' => array('t_district'),
        'menu_icon' => 'dashicons-building',
        'menu_position' => 2,
        'supports' => array('title', 'editor', 'thumbnail', 'custom-fields'),
        'rewrite' => array(
            'slug' => 'real_estate',
        ),
        'capability_type' => 't_realestate',
        'map_meta_cap' => true,
    );

    register_post_type('t_realestate', $args);
}


function create_cutom_types()
{
    t_register_taxonomy();
    t_register_post_type();
}

add_action('init', 'create_cutom_types');
add_action('admin_init', function () {
    $admin = get_role('administrator');
    if ($admin) {
        $admin->add_cap('edit_t_realestate');
        $admin->add_cap('edit_t_realestates');
        $admin->add_cap('edit_others_t_realestates');
        $admin->add_cap('edit_private_t_realestates');
        $admin->add_cap('edit_published_t_realestates');
        $admin->add_cap('read_t_realestate');
        $admin->add_cap('read_private_t_realestates');
        $admin->add_cap('publish_t_realestates');
        $admin->add_cap('delete_t_realestate');
        $admin->add_cap('delete_others_t_realestates');
        $admin->add_cap('delete_private_t_realestates');
        $admin->add_cap('delete_published_t_realestates');
    }
});

// Registering t_realestate template
add_filter('template_include', function ($template) {
    if (file_exists(plugin_dir_path(__FILE__) . 'templates/t_realestate.php') && (get_post_type() === 't_realestate'))
        $template = plugin_dir_path(__FILE__) . 'templates/t_realestate.php';
    return $template;
});

// Registering page template
add_filter('theme_page_templates', function ($templates) {
    $template_path = plugin_dir_path(__FILE__) . 'templates/t_realestate-catalog.php';

    if (file_exists($template_path)) {
        $templates['t_realestate-catalog.php'] = 'Real Estate Catalog';
    }

    return $templates;
});
add_filter('template_include', function ($template) {
    if (is_page_template('t_realestate-catalog.php')) {
        $plugin_template = plugin_dir_path(__FILE__) . 'templates/t_realestate-catalog.php';
        if (file_exists($plugin_template)) {
            return $plugin_template;
        }
    }
    return $template;
});


function live_search_enqueue_scripts()
{
    wp_enqueue_script('live-search-js', plugin_dir_url(__FILE__) . 'live-search.js', array('jquery'), '1.6', true);
    wp_localize_script('live-search-js', 'liveSearchParams', array(
        'ajax_url' => admin_url('admin-ajax.php'),
    ));
}
add_action('wp_enqueue_scripts', 'live_search_enqueue_scripts');

// AJAX handler for live search
function live_search_ajax_handler()
{
    $query = isset($_POST['query']) ? sanitize_text_field($_POST['query']) : '';
    $acf_criteria = isset($_POST['acf_criteria']) ? array_map('sanitize_text_field', $_POST['acf_criteria']) : array();
    $paged = isset($_POST['paged']) ? absint($_POST['paged']) : 1;

    $args = array(
        'post_type' => array('post', 't_realestate'),
        's' => $query,
        'posts_per_page' => 4,
        'paged' => $paged,
        'meta_query' => array('relation' => 'AND')
    );

    // Build meta_query to filter posts based on ACF fields
    foreach ($acf_criteria as $key => $value) {
        if (!empty($value)) {
            $args['meta_query'][] = array(
                'key' => sanitize_key($key),
                'value' => sanitize_text_field($value),
                'compare' => 'LIKE' // Comparison operator (change to '=' for exact match)
            );
        }
    }

    $search_query = new WP_Query($args);

    ob_start();

    if ($search_query->have_posts()) {
        while ($search_query->have_posts()):
            $search_query->the_post();
            get_template_part('loop-templates/content', 'search' );
        endwhile;

        // Pagination 
        $total_pages = $search_query->max_num_pages;
        if ($total_pages > 1) {
            echo '<div class="live-search-pagination rounded border overflow-hidden d-inline-flex">';
            for ($i = 1; $i <= $total_pages; $i++) {
                $ifcurrent = 'text-primary';
                if($i == $paged) 
                    $ifcurrent = 'text-white bg-primary';
                echo '<a href="#" style="padding: .5rem .75rem;" class="' . $ifcurrent . ' live-search-page border-right text-center" data-page="' . $i . '">' . $i . '</a> ';
            }
            echo '<a href="#" style="padding: .5rem .75rem;" class="live-search-page text-primary border-right text-center" data-page="' . $paged + 1 . '">»</a> ';
            echo '</div>';
        }
    } else {
        get_template_part('404-emb');
    }

    $output = ob_get_clean();
    echo json_encode(array('content' => $output));

    wp_die();
}
add_action('wp_ajax_live_search', 'live_search_ajax_handler');
add_action('wp_ajax_nopriv_live_search', 'live_search_ajax_handler');

// Shortcode for live search form
function live_search_shortcode()
{
    $acf_fields = array();
    $field_group_key = 'group_66eab646656e3'; // ACF group key

    $field_group = acf_get_field_group($field_group_key);

    if ($field_group) {
        $fields = acf_get_fields($field_group['ID']);
        if ($fields) {
            foreach ($fields as $field) {
                if ($field['name'] != 'image')
                    $acf_fields[$field['name']] = $field['label'];
            }
        } else {
            $acf_fields['error'] = 'No fields found for the field group.';
        }
    } else {
        $acf_fields = array('error' => 'Field group not found.');
    }

    ob_start(); ?>
    <div id="live-search-form"
        style="max-width: 350px; display: flex; flex-flow: row wrap; justify-content: space-between; align-items: center">
        <input type="text" id="live-search-input" style="flex: 0 0 80%" placeholder="Search..." />
        <button id="live-search-submit" style="flex: 0 0 18%; ">Search</button>
        <details id="search-criteria" class="mt-3 mb-4" style="width: 100%">
            <summary>Фильтры недвижимости</summary>
            <?php if (!empty($acf_fields) && !isset($acf_fields['error'])): ?>
                <div id="acf-criteria">
                    <?php foreach ($acf_fields as $name => $label): ?>
                        <label style="margin-top: 10px; display: flex; justify-content: space-between; align-items: center"
                            for="<?php echo esc_attr($name); ?>">
                            <?php echo esc_html($label); ?>:
                            <input type="text" class="acf-input" name="acf_criteria[<?php echo esc_attr($name); ?>]"
                                id="<?php echo esc_attr($name); ?>" />
                        </label>
                    <?php endforeach; ?>
                </div>
            <?php elseif (isset($acf_fields['error'])): ?>
                <p><?php echo esc_html($acf_fields['error']); ?></p>
            <?php else: ?>
                <p>No custom fields found.</p>
            <?php endif; ?>
        </details>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('live_search', 'live_search_shortcode');

// Widget Class
class Live_Search_Widget extends WP_Widget
{

    public function __construct()
    {
        parent::__construct(
            'live_search_widget',
            __('Enhanced Live Search Widget', 'text_domain'),
        );
    }

    public function widget($args, $instance)
    {
        echo $args['before_widget'];
        echo do_shortcode('[live_search]');
        echo $args['after_widget'];
    }
}

// Register the widget
function register_live_search_widget()
{
    register_widget('Live_Search_Widget');
}
add_action('widgets_init', 'register_live_search_widget');
