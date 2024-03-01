<?php

add_action('admin_menu', 'court_booking_add_admin_menu');
add_action('admin_init', 'court_booking_settings_init');

function court_booking_add_admin_menu()
{
   add_menu_page('Court Booking', 'Court Booking', 'manage_options', 'court_booking', 'court_booking_options_page', 'dashicons-book-alt', 5);

   add_submenu_page(
      'court_booking',
      'Court Booking Settings',
      'Settings',
      'manage_options',
      'court_booking_settings',
      'court_booking_options_page'
   );
}

function court_booking_settings_init()
{
   register_setting('courtBooking', 'court_booking_settings');

   add_settings_section(
      'court_booking_courtBooking_section',
      __('Adicione o shortcode em qualquer lugar do site [court_booking_form]', 'court-booking'),
      'court_booking_settings_section_callback',
      'courtBooking'
   );

   add_settings_field(
      'court_booking_redirect_url',
      __('Redirect URL', 'court-booking'),
      'court_booking_redirect_url_render',
      'courtBooking',
      'court_booking_courtBooking_section'
   );

   add_settings_field(
      'court_booking_text_field_0',
      __('Google Sheets Webhook URL', 'court-booking'),
      'court_booking_text_field_0_render',
      'courtBooking',
      'court_booking_courtBooking_section'
   );

   add_settings_field(
      'court_booking_datetimes_json',
      __('Booking Datetimes JSON', 'court-booking'),
      'court_booking_datetimes_json_render',
      'courtBooking',
      'court_booking_courtBooking_section'
   );
}

function court_booking_redirect_url_render()
{
   $options = get_option('court_booking_settings');
   $redirectUrl = isset($options['redirect_url']) ? $options['redirect_url'] : '';
?>
   <input type='text' name='court_booking_settings[redirect_url]' value='<?php echo esc_attr($redirectUrl); ?>'>
<?php
}

function court_booking_text_field_0_render()
{
   $options = get_option('court_booking_settings');
?>
   <input type='text' name='court_booking_settings[court_booking_text_field_0]' value='<?php echo $options['court_booking_text_field_0']; ?>'>
<?php
}

function court_booking_datetimes_json_render()
{
   $options = get_option('court_booking_settings');
   $datetimesJson = isset($options['datetimes_json']) ? $options['datetimes_json'] : '[]';
?>
   <input type="hidden" name="court_booking_settings[datetimes_json]" id="datetimes-field" value='<?php echo esc_attr($datetimesJson); ?>'>
<?php
}

function court_booking_settings_section_callback()
{
   echo __('Configure your Court Booking Plugin settings here.', 'court-booking');
}

function court_booking_options_page()
{
   $options       = get_option('court_booking_settings', '[]');
   $datetimesJson = isset($options['datetimes_json']) ? $options['datetimes_json'] : '[]';
   $datetimes     = json_decode($datetimesJson, true);
?>
   <form action='options.php' method='post'>
      <h2>Court Booking Settings</h2>
      <?php
      settings_fields('courtBooking');
      do_settings_sections('courtBooking');
      ?>
      <h3>Add Booking Times</h3>
      <div id="date-time-picker">
         <label for="">Select Date</label>
         <input type="date" id="date-picker">
         <label for="">Select Time</label>
         <input type="time" id="time-picker">
         <button type="button" id="add-datetime">Adicionar</button>
      </div>
      <ul id="datetime-list" style="max-height: 600px; overflow-y: auto;">
         <?php foreach ($datetimes as $datetime) : ?>
            <li>
               <?php echo $datetime; ?>
               <button type="button" class="remove-datetime" data-datetime="<?php echo htmlspecialchars($datetime); ?>">Remove</button>
            </li>
         <?php endforeach; ?>
      </ul>

      <?php submit_button(); ?>
   </form>
<?php
}
