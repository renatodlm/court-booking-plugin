<?php

if (!defined('ABSPATH'))
{
   exit;
}

class CourtRegisterAjax
{
   public function __construct()
   {
      add_action('wp_ajax_court_form_fetch_times',  [$this, 'fetch_times_callback']);
      add_action('wp_ajax_nopriv_court_form_fetch_times',  [$this, 'fetch_times_callback']);

      add_action('wp_ajax_court_form_add_participant',  [$this, 'add_participant_callback']);
      add_action('wp_ajax_nopriv_court_form_add_participant',  [$this, 'add_participant_callback']);
   }

   public function fetch_times_callback()
   {
      $sport = isset($_POST['sport']) ? sanitize_text_field($_POST['sport']) : '';

      if (empty($sport))
      {
         wp_send_json_error([
            'message' => 'Parametro faltando.'
         ], WP_Http::FORBIDDEN);
      }

      $CourtManager     = new CourtManager();
      $available_courts = $CourtManager->get_available_courts($sport);

      if (empty($available_courts))
      {
         wp_send_json_error([
            'message' => 'Não existem quadras disponíveis',
         ]);
      }

      $available_time_slots = $CourtManager->get_available_time_slots($available_courts, $sport);

      wp_send_json_success($available_time_slots);

      wp_die();
   }

   public function add_participant_callback()
   {
      if (empty($_POST['name']) || empty($_POST['email']) || empty($_POST['phone']) || empty($_POST['rg']) || empty($_POST['sportSelect']) || empty($_POST['timeSelect']))
      {
         wp_send_json_error(['message' => 'Dados do formulário incompletos.']);
      }

      $CourtManager = new CourtManager();
      $sports       = $CourtManager->get_sports();
      $free         = $_POST['free'] ?? '';
      $sport        = $_POST['sportSelect'];
      $sport_label  = $sports[sanitize_text_field($_POST['sportSelect'])];

      if (empty($free))
      {
         $sport_label = 'Clínica de ' . $sport;
         $sport = 'clinique-' . $sport;
      }

      $time_slot = sanitize_text_field($_POST['timeSelect']);

      $user_data = [
         'name'  => sanitize_text_field($_POST['name']),
         'email' => sanitize_email($_POST['email']),
         'phone' => sanitize_text_field($_POST['phone']),
         'rg'    => sanitize_text_field($_POST['rg']),
      ];

      $available_courts = $CourtManager->get_available_courts($sport);

      $available_time_slots = $CourtManager->get_available_time_slots($available_courts, $sport);

      if (empty($available_time_slots))
      {
         wp_send_json_error(['message' => 'Não existe horários disponíveis.']);
      }

      $available_court_id = 0;

      foreach ($available_courts as $court_id => $value)
      {

         if (empty($available_time_slots[$court_id]))
         {
            continue;
         }

         if (in_array($time_slot, $available_time_slots[$court_id]))
         {
            $available_court_id = $court_id;
         }
      }

      if (empty($available_court_id))
      {
         wp_send_json_error(['message' => 'Horário não disponível.']);
      }

      $options     = get_option('court_booking_settings');
      $webhook_url = $options['court_booking_text_field_0'] ?? '';

      if (!empty($webhook_url))
      {
         $body_user_data = [
            'name'  => sanitize_text_field($_POST['name']),
            'email' => sanitize_email($_POST['email']),
            'phone' => sanitize_text_field($_POST['phone']),
            'rg'    => sanitize_text_field($_POST['rg']),
            'sport' => $sport_label,
            'time_slot' => $time_slot
         ];

         $request_body = [
            'body' =>
            [
               'NOME'                 => $body_user_data['name'],
               'EMAIL'                => $body_user_data['email'],
               'TELEFONE'             => $body_user_data['phone'],
               'RG'                   => $body_user_data['rg'],
               'OQUE DESEJA PRATICAR' => $body_user_data['sport'],
               'DATA/ HORÁRIO'        => $body_user_data['time_slot'],
               'QNTD PESSOAS'         => '1 Pessoa',
               'form_name'            => 'FormHome',
               // 'Date'                   => '29 de fevereiro de 2024',
               // 'Time'                   => '21:05',
               // 'Page URL'               => home_url(),
               // 'User Agent'             => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36',
               // 'Remote IP'              => '::1',
               // 'Powered by'             => 'Elementor',
               // 'form_id'                => 'b8b214e',
            ]
         ];


         $response = wp_remote_post($webhook_url, $request_body);

         if (is_wp_error($response))
         {
            $error_message = $response->get_error_message();
            wp_send_json_error(['message' => "Algo deu errado: $error_message"]);
         }
      }

      $result = $CourtManager->add_participant($available_court_id, $time_slot, $sport, $user_data);

      $mail_send = true;

      if (!empty($result))
      {
         $admin_email = get_option('court_form_admin_email', get_option('admin_email'));

         ob_start();
         include(plugin_dir_path(__FILE__) . '../templates/email-template.php');
         $email_content = ob_get_clean();

         $user_data['sport']     = $sport_label;
         $user_data['time_slot'] = $time_slot;
         $email_content          = str_replace(['{{name}}', '{{email}}', '{{phone}}', '{{rg}}', '{{sport}}', '{{time_slot}}'], array_values($user_data), $email_content);
         $subject                = 'Novo participante registrado';
         $headers                = array('Content-Type: text/html; charset=UTF-8');

         $mail_send = wp_mail($admin_email, $subject, $email_content, $headers);
      }
      else
      {
         wp_send_json_error(['message' => 'Erro registrar horário.']);
      }

      if (!$mail_send)
      {
         $log_dir = plugin_dir_path(__FILE__) . '/logs/';
         $log_file = $log_dir . date('Y-m-d') . '_error.log';

         if (!file_exists($log_dir))
         {
            wp_mkdir_p($log_dir);
         }

         $error_message = "Erro: Não foi possível enviar o e-mail de confirmação para o administrador. " .
            "Dados do participante: Nome: " . $user_data['name'] .
            ", E-mail: " . $user_data['email'] .
            ", Telefone: " . $user_data['phone'] .
            ", RG: " . $user_data['rg'] .
            ", Esporte: " . $sport_label .
            ", Horário: " . $time_slot .
            " | " . date('Y-m-d H:i:s') . "\n";

         file_put_contents($log_file, $error_message, FILE_APPEND);
      }

      wp_send_json_success(['message' => 'Horário registrado com sucesso.']);

      wp_die();
   }
}

new CourtRegisterAjax();
