<?php

if (!defined('ABSPATH'))
{
   exit;
}

// $dias = [28/02/2024, 29, 30, 31];
// é mais que today?
// $horarios= [4:00 ,4:13, 4:20];

class CourtManager
{
   private $courts;
   private $time_slots = ['29/02/2024 09:00', '09:30', '10:00', '10:30', '11:00', '11:30', '12:00', '12:30', '13:00', '13:30', '14:00', '14:30', '15:00', '15:30', '16:00', '16:30'];
   private $participants_per_court = [];
   private $sports;

   public function __construct()
   {
      $this->initialize_sports();
      $this->initialize_courts();
      $this->initialize_participants();
   }

   public function get_sports()
   {
      return $this->sports;
   }

   public function get_available_courts($sport)
   {
      $available_courts = [];
      foreach ($this->courts as $court_id => $court_details)
      {
         if (($court_details['sport'] == $sport || $court_details['fixed'] !== 'yes'))
         {
            $available_courts[$court_id] = $court_details['name'];
         }
      }

      return $available_courts;
   }

   public function get_available_time_slots($court_id, $sport)
   {
      global $wpdb;

      $table_name = $wpdb->prefix . 'court_manager_participants';
      $available_time_slots = [];

      foreach ($this->time_slots as $time_slot)
      {
         $participant_count = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table_name WHERE court_id = %s AND time_slot = %s",
            $court_id,
            $time_slot
         ));

         if ($participant_count < 10 && $this->is_court_time_slot_available_for_sport($court_id, $time_slot, $sport))
         {
            $available_time_slots[] = $time_slot;
         }
      }

      return $available_time_slots;
   }

   public function add_participant($court_id, $time_slot, $sport, $user_data)
   {
      global $wpdb;
      $table_name = $wpdb->prefix . 'court_manager_participants';

      $participant_exists = $wpdb->get_var($wpdb->prepare(
         "SELECT COUNT(*) FROM $table_name WHERE court_id = %s AND time_slot = %s AND user_data LIKE %s",
         $court_id,
         $time_slot,
         '%' . $wpdb->esc_like(wp_json_encode($user_data)) . '%'
      ));

      if ($participant_exists > 0)
      {
         return false;
      }

      $participant_count = $wpdb->get_var($wpdb->prepare(
         "SELECT COUNT(*) FROM $table_name WHERE court_id = %s AND time_slot = %s",
         $court_id,
         $time_slot
      ));

      if ($participant_count >= 10)
      {
         return false;
      }

      if (!$this->is_court_time_slot_available_for_sport($court_id, $time_slot, $sport))
      {
         return false;
      }

      $wpdb->insert(
         $table_name,
         [
            'court_id'  => $court_id,
            'time_slot' => $time_slot,
            'user_data' => wp_json_encode($user_data),
            'sport'     => $sport
         ]
      );

      return true;
   }

   private function is_court_time_slot_available_for_sport($court_id, $time_slot, $sport)
   {
      global $wpdb;
      $table_name = $wpdb->prefix . 'court_manager_participants';

      $court_details = $this->courts[$court_id];
      if ($court_details['fixed'] === 'yes' && $court_details['sport'] !== $sport)
      {
         return false;
      }

      $existing_sport = $wpdb->get_var($wpdb->prepare(
         "SELECT sport FROM $table_name WHERE court_id = %s AND time_slot = %s LIMIT 1",
         $court_id,
         $time_slot
      ));

      if (!empty($existing_sport) && $existing_sport !== $sport)
      {
         return false;
      }

      $participant_count = $wpdb->get_var($wpdb->prepare(
         "SELECT COUNT(*) FROM $table_name WHERE court_id = %s AND time_slot = %s AND sport = %s",
         $court_id,
         $time_slot,
         $sport
      ));

      if ($participant_count >= 10)
      {
         return false;
      }

      return true;
   }

   private function update_participants($court_id, $time_slot, $user_data)
   {
      global $wpdb;

      $table_name = $wpdb->prefix . 'court_manager_participants';

      $wpdb->insert(
         $table_name,
         [
            'court_id' => $court_id,
            'time_slot'  => $time_slot,
            'user_data'  => wp_json_encode($user_data)
         ]
      );
   }

   private function initialize_participants()
   {
      global $wpdb;

      $table_name = $wpdb->prefix . 'court_manager_participants';
      $results    = $wpdb->get_results("SELECT * FROM $table_name", OBJECT);

      $this->participants_per_court = [];

      foreach ($results as $row)
      {
         $court_id = $row->court_id;
         $time_slot  = $row->time_slot;
         $user_data  = json_decode($row->user_data, true);

         if (!isset($this->participants_per_court[$court_id][$time_slot]))
         {
            $this->participants_per_court[$court_id][$time_slot] = [];
         }

         $this->participants_per_court[$court_id][$time_slot][] = $user_data;
      }
   }

   private function initialize_sports()
   {
      $sports_posts = get_posts([
         'post_type'      => 'cf_sports',
         'posts_per_page' => -1,
         'orderby'        => 'title',
         'order'          => 'ASC'
      ]);

      $sports = [];

      foreach ($sports_posts as $post)
      {
         $sports[$post->post_name] = $post->post_title;
      }

      $this->sports = $sports;
   }

   private function initialize_courts()
   {
      $courts_posts = get_posts([
         'post_type'      => 'cf_courts',
         'posts_per_page' => -1,
         'orderby'        => 'title',
         'order'          => 'ASC'
      ]);

      $courts = [];

      foreach ($courts_posts as $post)
      {
         $id    = $post->ID;
         $name  = $post->post_title;
         $sport = get_post_meta($id, 'cf_sport', true);
         $fixed = get_post_meta($id, 'cf_fixed', true);

         $courts[$id] = [
            'name' => $name,
            'sport' => $sport,
            'fixed' => $fixed,
         ];
      }

      $this->courts = $courts;
   }

   private function update_courts()
   {
      update_option('court_manager_courts', $this->courts);
   }

   private function get_courts()
   {
      $this->courts = get_option('court_manager_courts', []);
   }

   private function reset_participants()
   {
      global $wpdb;

      $table_name = $wpdb->prefix . 'court_manager_participants';
      $wpdb->query("TRUNCATE TABLE $table_name");
   }
}
