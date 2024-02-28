<?php

if (!defined('ABSPATH'))
{
   exit;
}

class CourtLogger
{
   public static function create_error($message)
   {
      $log_dir = get_stylesheet_directory() . '/logs/';
      $log_file = $log_dir . current_time('Y-m-d') . '_error.log';

      if (!file_exists($log_dir))
      {
         wp_mkdir_p($log_dir);
      }

      $error_message = $message;

      file_put_contents($log_file, $error_message, FILE_APPEND);
   }
}
