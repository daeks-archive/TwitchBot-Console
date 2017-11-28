<?php
    
 /*
  *  Copyright (c) daeks, distributed
  *  as-is and without warranty under the MIT License. See
  *  [root]/LICENSE for more. This information must remain intact.
  */
  
  if(!extension_loaded('pthreads')) {
    if(!dl('php_pthreads.dll')) {
      die('Unable to load extension pthreads');
    }
  }
  
  //////////////////////////////////////////////////////////////////
  // Global Definitions
  
  define('DB_PATH', 'db');
  define('CMDS_PATH', 'cmds');
  define('LIBS_PATH', 'libs');
  define('PLUGINS_PATH', 'plugins');
  define('LOG_PATH', 'logs');
  define('CACHE_PATH', 'cache');
  
  define('SKELETON', 'TB');
  define('SKELETON_FILE', 'tb.class.php');
    
  require_once('config.php');
  require_once(DB_PATH.DIRECTORY_SEPARATOR.DATABASE.'.php');
  require_once(SKELETON_FILE);
      
  //////////////////////////////////////////////////////////////////
  // Global Error Handler

  register_shutdown_function('CatchFatalError');
  function CatchFatalError() {
    global $argv;
    $error = error_get_last();
    $ignore = E_WARNING | E_NOTICE | E_USER_WARNING | E_USER_NOTICE | E_STRICT | E_DEPRECATED | E_USER_DEPRECATED;
    if (($error['type'] & $ignore) == 0) {
      if(json_encode($error, JSON_FORCE_OBJECT) != null) {
        file_put_contents(CACHE_PATH.DIRECTORY_SEPARATOR.SKELETON.'-'.$argv[1].'.err', json_encode($error, JSON_FORCE_OBJECT), LOCK_EX);
      }
    }
  }
  
  set_error_handler('exceptions_error_handler');
  function exceptions_error_handler($severity, $message, $filename, $lineno) {
    if (error_reporting() == 0) {
      return;
    }
    if (error_reporting() & $severity) {
      throw new ErrorException($message, 0, $severity, $filename, $lineno);
    }
  }
  
  //////////////////////////////////////////////////////////////////
  // Includes & Definitions
  
  set_time_limit(0);
  error_reporting(E_ALL);
  ini_set('display_errors', 'on');
  ini_set('output_buffering', 'off');
  ini_set('zlib.output_compression', false);
  while (@ob_end_flush());
  ini_set('implicit_flush', true);
  ob_implicit_flush(true);

  //////////////////////////////////////////////////////////////////
  // TB
  
  $class = SKELETON;
  $tb = new $class($argv[1]);
  $tb->run();
  exit($tb->exit);
  
?>