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
   $options     = get_option('court_booking_settings');
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
   <form action="options.php" method="post" style="padding-right:15px;">
      <?php

      $active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'configs';

      ?>
      <div class="wrap">
         <h1>
            <?php

            echo esc_html(get_admin_page_title());

            ?>
         </h1>

         <h2 class="nav-tab-wrapper">
            <a href="?page=court_booking_settings&tab=configs" class="nav-tab <?php echo $active_tab == 'configs' ? 'nav-tab-active' : ''; ?>">
               <?php

               esc_html_e('Configurações', 'court-booking');

               ?>
            </a>
            <a href="?page=court_booking_settings&tab=times" class="nav-tab <?php echo $active_tab == 'times' ? 'nav-tab-active' : ''; ?>">
               <?php

               esc_html_e('Horários', 'court-booking');

               ?>
            </a>
            <a href="?page=court_booking_settings&tab=registers" class="nav-tab <?php echo $active_tab == 'registers' ? 'nav-tab-active' : ''; ?>">
               <?php

               esc_html_e('Registros', 'court-booking');

               ?>
            </a>
         </h2>

         <?php

         if ($active_tab == 'registers')
         {

         ?>
            <h3>
               <?php

               esc_html_e('Registros', 'court-booking');

               ?>
            </h3>
            <p>
               <?php

               esc_html_e('Lista de registros realizados.', 'court-booking');

               ?>
            </p>

            <?php

            global $wpdb;

            $CourtManager = new CourtManager();
            $courts       = $CourtManager->get_courts();
            $sports       = $CourtManager->get_sports();
            $table_name   = $wpdb->prefix . 'court_manager_participants';

            $query = $wpdb->prepare(
               "SELECT * FROM $table_name",
            );

            $results = $wpdb->get_results($query);

            ?>
            <div style="margin-top: 30px;margin-bottom:30px">
               <table id="courtRegister" class="datatable display">
                  <thead>
                     <tr>
                        <th>ID</th>
                        <th>Quadra</th>
                        <th>Horário</th>
                        <th>Esporte</th>
                        <th>Nome</th>
                        <th>E-mail</th>
                        <th>Telefone</th>
                        <th>Documento</th>
                        <td>Ação</td>
                     </tr>
                  </thead>
                  <tbody>
                     <?php

                     foreach ($results as $row)
                     {

                        $user_data   = json_decode($row->user_data, true);
                        $sport_label = $sports[htmlspecialchars(str_replace('clinique-', '', $row->sport))] ?? $row->sport;

                        $court_name = $courts[htmlspecialchars($row->court_id)]['name'] ?? htmlspecialchars($row->court_id);

                        if (str_contains('clinique-', $row->sport))
                        {
                           $sport_label = 'Clínica de ' . $sport_label;
                        }

                     ?>
                        <tr data-court-id="<?php echo htmlspecialchars($row->id); ?>">
                           <td><?php echo htmlspecialchars($row->id); ?></td>
                           <td><?php echo $court_name; ?></td>
                           <td><?php echo htmlspecialchars($row->time_slot); ?></td>
                           <td><?php echo $sports[htmlspecialchars($row->sport)] ?? $row->sport; ?></td>
                           <td><?php echo htmlspecialchars($user_data['name']); ?></td>
                           <td><?php echo htmlspecialchars($user_data['email']); ?></td>
                           <td><?php echo htmlspecialchars($user_data['phone']); ?></td>
                           <td><?php echo htmlspecialchars($user_data['rg']); ?></td>
                           <td>
                              <button type="button" class="remove-datetime remove-btn">
                                 <?php

                                 esc_html_e('Remover', 'court-booking');

                                 ?>
                              </button>
                           </td>
                        </tr>
                     <?php

                     }

                     ?>
                  </tbody>
               </table>
            </div>
         <?php

         }
         else
         {

         ?>
            <div class="<?php echo $active_tab == 'configs' ? 'show' : 'hidden'; ?>">
               <?php

               settings_fields('courtBooking');
               do_settings_sections('courtBooking');

               ?>
            </div>

            <div class="<?php echo $active_tab == 'times' ? 'show' : 'hidden'; ?>">
               <h3>
                  <?php

                  esc_html_e('Adicionar horários', 'court-booking');

                  ?>
               </h3>
               <p>
                  <?php

                  esc_html_e('Lista de registros realizados.', 'court-booking');

                  ?>
               </p>

               <div id="date-time-picker" style="margin-top: 30px;margin-bottom:30px">
                  <label for="">
                     <?php

                     esc_html_e('Selecionar data', 'court-booking');

                     ?>
                  </label>
                  <input type="date" id="date-picker">
                  <label for="">
                     <?php

                     esc_html_e('Selecionar horário', 'court-booking');

                     ?>
                  </label>
                  <input type="time" id="time-picker">
                  <button type="button" id="add-datetime">
                     <?php

                     esc_html_e('Adicionar', 'court-booking');

                     ?>
                  </button>
               </div>

               <table id="courtTimes" class="datatable display">
                  <thead>
                     <tr>
                        <th>Times</th>
                        <th>Action</th>
                     </tr>
                  </thead>
                  <tbody>
                     <?php

                     foreach ($datetimes as $datetime)
                     {

                     ?>
                        <tr>
                           <td>
                              <?php echo $datetime; ?>
                           </td>
                           <td>
                              <button type="button" class="remove-datetime" data-datetime="<?php echo htmlspecialchars($datetime); ?>">
                                 <?php

                                 esc_html_e('Remover', 'court-booking');

                                 ?>
                              </button>
                           </td>
                        </tr>
                     <?php

                     }

                     ?>
                  </tbody>
               </table>
            </div>
         <?php

         }

         ?>
      </div>

      <?php

      submit_button();

      ?>
   </form>
<?php

}
