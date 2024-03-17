<?php

if (!defined('ABSPATH'))
{
   exit;
}

class CourtRegister
{
   public function __construct()
   {
      add_action('init', [$this, 'create_participants_table']);
      add_action('init', [$this, 'register_custom_post_types']);
      add_action('add_meta_boxes', [$this, 'add_custom_meta_boxes']);
      add_action('save_post', [$this, 'save_post_meta']);
      add_filter('manage_cf_courts_posts_columns',  [$this, 'add_courts_columns']);
      add_action('manage_cf_courts_posts_custom_column',  [$this, 'display_courts_columns_content'], 10, 2);
   }

   public function create_participants_table()
   {
      global $wpdb;

      $table_name = $wpdb->prefix . 'court_manager_participants';

      if ($wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") != $table_name)
      {
         $charset_collate = $wpdb->get_charset_collate();

         $sql = "CREATE TABLE $table_name (
              id mediumint(9) NOT NULL AUTO_INCREMENT,
              court_id tinytext NOT NULL,
              time_slot tinytext NOT NULL,
              sport tinytext NOT NULL,
              user_data text NOT NULL,
              PRIMARY KEY  (id)
          ) $charset_collate;";

         require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
         dbDelta($sql);
      }
   }

   public function register_custom_post_types()
   {
      register_post_type('cf_courts', [
         'labels'      => [
            'name'          => 'Courts',
            'singular_name' => 'Court'
         ],
         'public'       => true,
         'has_archive'  => false,
         'supports'     => ['title'],
         'menu_icon'    => 'dashicons-palmtree',
         'show_in_menu' => 'court_booking',
      ]);

      register_post_type('cf_sports', [
         'labels'      => [
            'name'          => 'Sports',
            'singular_name' => 'Sport'
         ],
         'public'       => true,
         'has_archive'  => false,
         'supports'     => ['title'],
         'menu_icon'    => 'dashicons-universal-access',
         'show_in_menu' => 'court_booking',
      ]);
   }

   public function add_custom_meta_boxes()
   {
      add_meta_box('cf_sports_meta', 'Detalhes do Esporte', [$this, 'cf_sports_meta_callback'], 'cf_courts', 'side', 'default');
      add_meta_box('cf_courts_meta', 'Detalhes do Court', [$this, 'cf_courts_meta_callback'], 'cf_courts', 'side', 'default');
   }

   public function cf_sports_meta_callback($post)
   {
      $sports = get_posts([
         'post_type'      => 'cf_sports',
         'posts_per_page' => -1,
         'orderby'        => 'title',
         'order'          => 'ASC'
      ]);

      $current_sport = get_post_meta($post->ID, 'cf_sport', true);

?>
      <select name="cf_sport" id="cf_sport">
         <?php

         foreach ($sports as $sport)
         {
            $slug     = $sport->post_name;
            $selected = selected($current_sport, $slug, false);

            echo "<option value='{$slug}' {$selected}>{$sport->post_title}</option>";
         }

         ?>
      </select>
   <?php

   }

   public function cf_courts_meta_callback($post)
   {
      $fixed = get_post_meta($post->ID, 'cf_fixed', true);

   ?>
      <label for="cf_fixed">Este esporte é fixo?</label>
      <input type="checkbox" id="cf_fixed" name="cf_fixed" value="yes" <?php checked($fixed, 'yes'); ?> />
<?php

   }

   public function save_post_meta($post_id)
   {
      if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
      {
         return;
      };

      if (!current_user_can('edit_post', $post_id))
      {
         return;
      };

      if (isset($_POST['cf_fixed']))
      {
         update_post_meta($post_id, 'cf_fixed', $_POST['cf_fixed']);
      }
      else
      {
         delete_post_meta($post_id, 'cf_fixed');
      }

      if (isset($_POST['cf_sport']))
      {
         update_post_meta($post_id, 'cf_sport', $_POST['cf_sport']);
      }
   }

   public function add_courts_columns($columns)
   {
      $columns['cf_sport'] = __('Esporte principal', 'court_form');
      $columns['fixed']    = __('Fixo', 'court_form');

      return $columns;
   }

   public function display_courts_columns_content($column, $post_id)
   {
      $CourtManager = new CourtManager();
      $sports       = $CourtManager->get_sports();

      switch ($column)
      {
         case 'cf_sport':
            $sport = get_post_meta($post_id, 'cf_sport', true);
            echo esc_html($sports[$sport]);
            break;
         case 'fixed':
            $fixed = get_post_meta($post_id, 'cf_fixed', true) === 'yes' ? __('Sim', 'court_form') : __('Não', 'court_form');
            echo esc_html($fixed);
            break;
      }
   }
}

new CourtRegister();
