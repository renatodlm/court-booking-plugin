<?php

if (!defined('ABSPATH'))
{
   exit;
}

class CourtLogger
{
   public static function create_error($message)
   {
      $args     = func_get_args();
      $log   = "";

      $log_dir = __DIR__ . '/logs/';
      $log_file = $log_dir . current_time('Y-m-d') . '_error.log';

      foreach ($args as $key => $arg)
      {
         $log .= "\n[ ";

         if ($key < 10)
         {
            $log .= '0';
         }
         $log .= "{$key} ] ";

         $log .= var_export($arg, 1);
      }

      error_log($log, 3, $log_file);
   }
}
