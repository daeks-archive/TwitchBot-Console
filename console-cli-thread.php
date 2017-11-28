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
    $error = error_get_last();
    $ignore = E_WARNING | E_NOTICE | E_USER_WARNING | E_USER_NOTICE | E_STRICT | E_DEPRECATED | E_USER_DEPRECATED;
    if (($error['type'] & $ignore) == 0) {
      file_put_contents(CACHE_PATH.DIRECTORY_SEPARATOR.'console.error.db', json_encode($error, JSON_FORCE_OBJECT), LOCK_EX);
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
  // TB Console
  
  $threads = new TBPool();
  $console = new TBConsole(filemtime(__FILE__), $threads);
  
  class TBConsole {
    
    private $version;
    private $socket;
    private $threads;
    private $observer;
  
    function __construct($version, $threads) {
      
      $this->version = date('YmdHi', $version);
      $this->threads = $threads;
      
      $ob = new TBObserver($threads);
      $this->threads->submit($ob);
      $this->observer = $ob;
      
      foreach ($this->db()->select('BOTS', 'ENABLED=1 AND AUTOSTART=1')->fetchAll() as $config) {
        $output = $this->start($config);
      }
      
      $this->listen();      
    }
       
    function listen() {
      $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
      socket_set_option($this->socket, SOL_SOCKET, SO_REUSEADDR, 1);
      socket_bind($this->socket, CLI_SERVER, CLI_PORT);
      socket_listen($this->socket, 5);
      
      $restart = false;
      
      do {
        $msgsock = socket_accept($this->socket);
        $msg = "";
        socket_write($msgsock, $msg, strlen($msg));
        $output = '{"version" : "'.$this->version.'"';
        do {
          $buf = socket_read ($msgsock, 2048, PHP_NORMAL_READ);
          if (!$buf = trim ($buf)) { continue; }
          $args = explode(' ', $buf);
          
          if ($args[0] == 'add' && sizeof($args) == 5) {
            $stmt = $this->db()->select('BOTS', "NAME = '".$args[1]."'");
              if($stmt->rowCount() == 0) {
                $config = array('NAME' => $args[1], 'OAUTH' => $args[2], 'COLOR' => $args[3], 'OWNER' => $args[4]);
                $config['ID'] = $this->db()->insert('BOTS', $config);
                $output .= ', "id" : '.$config['ID'].', "name" : "'.$config['NAME'].'", "action" : "add", "state" : "OK"';
                $output .= ', "subaction" : {'.$this->start($config).'}';
              } else {
                $output .= '"id" : '.$config['ID'].', "name" : "'.$config['NAME'].'", "action" : "add", "state" : "ERROR", "msg" : "ALREADY_EXISTS"';
              }
          }
          
          if ($args[0] == 'delete' && sizeof($args) == 2 && $args[1] > 0) {
            $stmt = $this->db()->select('BOTS', "ID = ".$args[1]);
            if($stmt->rowCount() > 0) {
              $output .= ', "subaction" : {'.$this->stop($stmt->fetch()).'}';
              $this->db()->delete('BOTS', "ID = ".$args[1]);
              $output .= ', "id" : '.$config['ID'].', "name" : "'.$config['NAME'].'", "action" : "delete", "state" : "OK"';
            }
          }
          
          if ($args[0] == 'enable' && sizeof($args) == 2 && $args[1] > 0) {
            $stmt = $this->db()->select('BOTS', "ENABLED = 0 AND ID = ".$args[1]);
            if($stmt->rowCount() > 0) {
              $this->db()->update('BOTS', array('ENABLED' => 1), "ID = ".$args[1]);
              $output .= ', "id" : '.$config['ID'].', "name" : "'.$config['NAME'].'", "action" : "enable", "state" : "OK"';
            }
          }
          
          if ($args[0] == 'disable' && sizeof($args) == 2 && $args[1] > 0) {
            $stmt = $this->db()->select('BOTS', "ENABLED = 1 AND ID = ".$args[1]);
            if($stmt->rowCount() > 0) {
              $this->db()->update('BOTS', array('ENABLED' => 0), "ID = ".$args[1]);
              $output .= ', "id" : '.$config['ID'].', "name" : "'.$config['NAME'].'", "action" : "disable", "state" : "OK"';
              $output .= ', "subaction" : {'.$this->stop($stmt->fetch()).'}';
            }
          }
          
          if ($args[0] == 'start' && sizeof($args) == 2) {
            if($args[1] == '*') {
              $stmt = $this->db()->select('BOTS');
              if($stmt->rowCount() > 0) {
                $config = $stmt->fetch();
                
                if($config['ENABLED'] == 1) {
                  $output .= ', '.$this->start($config);
                }
              }
            } else {
              if($args[1] > 0) {
                $stmt = $this->db()->select('BOTS', "ID = ".$args[1]);
                if($stmt->rowCount() > 0) {
                  $config = $stmt->fetch();
                  
                  if($config['ENABLED'] == 1) {
                    $output .= ', '.$this->start($config);
                  } else {
                    $output .= ', "id" : '.$config['ID'].', "name" : "'.$config['NAME'].'", "action" : "start", "state" : "ERROR", "msg" : "DISABLED"';
                  }
                }
              }
            } 
          }
          
          if ($args[0] == 'stop' && sizeof($args) == 2) {
            if($args[1] == '*') {
              $stmt = $this->db()->select('BOTS');
              if($stmt->rowCount() > 0) {
                $output .= ', '.$this->stop($stmt->fetch());                          
              }
            } else {
              if($args[1] > 0) {
                $stmt = $this->db()->select('BOTS', "ID = ".$args[1]);
                if($stmt->rowCount() > 0) {
                  $output .= ', '.$this->stop($stmt->fetch());                          
                }
              }
            }
          }
          
          if ($args[0] == 'crash' && sizeof($args) == 2) {
            $stmt = $this->db()->select('BOTS', "ID = ".$args[1]);
            if($stmt->rowCount() > 0) {
              $config = $stmt->fetch();
              if(array_key_exists($config['ID'], $this->threads->get())) {
                $this->threads->get($config['ID'])[1]->destroy();
                $output .= ', "id" : '.$config['ID'].', "name" : "'.$config['NAME'].'", "action" : "crash", "state" : "OK"';
              } else {
                $output .= ', "id" : '.$config['ID'].', "name" : "'.$config['NAME'].'", "action" : "crash", "state" : "ERROR", "msg" : "NOT_STARTED"';
              }  
            }
          }
          
          if ($args[0] == 'status' && sizeof($args) == 2) {
            $where = "ID = ".$args[1];
            $stmt = $this->db()->select('BOTS', $where);
            if($stmt->rowCount() > 0) {
              $config = $stmt->fetch();
              if(array_key_exists($config['ID'], $this->threads->get())) {
                $output .= ', "id" : '.$config['ID'].', "name" : "'.$config['NAME'].'", "action" : "status", "state" : "RUNNING"';
              } else {
                if($config['ENABLED'] == 1) {
                  $output .= ', "id" : '.$config['ID'].', "name" : "'.$config['NAME'].'", "action" : "status", "state" : "STOPPED"';
                } else {
                  $output .= ', "id" : '.$config['ID'].', "name" : "'.$config['NAME'].'", "action" : "status", "state" : "DISABLED"';
                }
              }                   
            }
          } 
          
          if ($args[0] == 'status' && sizeof($args) == 3) {
            $where = "ID = ".$args[1];
            $stmt = $this->db()->select('BOTS', $where);
            if($stmt->rowCount() > 0) {
              $config = $stmt->fetch();
              if(array_key_exists($config['ID'], $this->threads->get())) {
                $stmt2 = $this->db()->select('CHANNELS', "ID = ".$args[2]." and BOTID = ".$args[1]);
                if($stmt2->rowCount() > 0) {
                  $subconfig = $stmt2->fetch();
                  $status = $this->threads->synchronized(function($threads, $config, $subconfig){
                      return $threads->get($config['ID'])[1]->status($subconfig['ID']);
                  }, $this->threads, $config, $subconfig);
                  $output .= ', "id" : '.$config['ID'].', "name" : "'.$config['NAME'].'", "action" : "status", "state" : "'.$status.'"';
                } else {
                  $output .= ', "id" : '.$config['ID'].', "name" : "'.$config['NAME'].'", "action" : "status", "state" : "ERROR", "msg" : "NOT_FOUND"';
                }
              } else {
                $output .= ', "id" : '.$config['ID'].', "name" : "'.$config['NAME'].'", "action" : "status", "state" : "ERROR", "msg" : "NOT_STARTED"';
              }                   
            }
          }
          
          if ($args[0] == 'reinit' && sizeof($args) == 2) {
            $where = "ID = ".$args[1];
            if($args[1] == '*') {
              $where = '';
            }
            $stmt = $this->db()->select('BOTS', $where);
            if($stmt->rowCount() > 0) {
              $config = $stmt->fetch();
              if(array_key_exists($config['ID'], $this->threads->get())) {
                $status = $this->threads->synchronized(function($threads, $config){
                    return $threads->get($config['ID'])[1]->reinit();
                }, $this->threads, $config);
                $output .= ', "id" : '.$config['ID'].', "name" : "'.$config['NAME'].'", "action" : "reinit", "state" : "'.$status.'"';
              } else {
                $output .= ', "id" : '.$config['ID'].', "name" : "'.$config['NAME'].'", "action" : "reinit", "state" : "ERROR", "msg" : "NOT_STARTED"';
              }                   
            }
          }
          
          if ($args[0] == 'reinit' && sizeof($args) == 3) {
            $where = "ID = ".$args[1];
            $stmt = $this->db()->select('BOTS', $where);
            if($stmt->rowCount() > 0) {
              $config = $stmt->fetch();
              if(array_key_exists($config['ID'], $this->threads->get())) {
                $stmt2 = $this->db()->select('CHANNELS', "ID = ".$args[2]." and BOTID = ".$args[1]);
                if($stmt2->rowCount() > 0) {
                  $subconfig = $stmt2->fetch();
                  $status = $this->threads->synchronized(function($threads, $config, $subconfig){
                      return $threads->get($config['ID'])[1]->reinit($subconfig['ID']);
                  }, $this->threads, $config, $subconfig);
                  $output .= ', "id" : '.$config['ID'].', "name" : "'.$config['NAME'].'", "action" : "reinit", "state" : "'.$status.'"';
                } else {
                  $output .= ', "id" : '.$config['ID'].', "name" : "'.$config['NAME'].'", "action" : "reinit", "state" : "ERROR", "msg" : "NOT_FOUND"';
                }
              } else {
                $output .= ', "id" : '.$config['ID'].', "name" : "'.$config['NAME'].'", "action" : "reinit", "state" : "ERROR", "msg" : "NOT_STARTED"';
              }                   
            }
          }
          
          if ($args[0] == 'restart' && sizeof($args) == 2 && $args[1] > 0) {
            $stmt = $this->db()->select('BOTS', "ID = ".$args[1]);
            if($stmt->rowCount() > 0) {
              $config = $stmt->fetch();
              if($config['ENABLED'] == 1) {
                $output .= ', "stop" : {'.$this->stop($config).'}';
                $output .= ', "start" : {'.$this->start($config).'}';
              } else {
                $output .= ', "id" : '.$config['ID'].', "name" : "'.$config['NAME'].'", "action" : "start", "state" : "ERROR", "msg" : "DISABLED"';
              }
            }                 
          }
          
          if ($args[0] == 'monitor' && sizeof($args) == 1) {
            $output .= ', "action" : "monitor", "state" : "OK"';
            $output .= ', "list" :[';
            foreach($this->threads->get() as $key => $thread) {
              if($thread[0]['ID'] > 0) {
                if(!$thread[1]->isRunning()) {
                  $output .= '{"id" : '.$key.', "name" : "'.$thread[0]['NAME'].'", "state" : "FAILED"}, ';
                } else {
                  $output .= '{"id" : '.$key.', "name" : "'.$thread[0]['NAME'].'", "state" : "RUNNING"}, ';
                }
              }
            }
            $output = rtrim($output, ', ');
            $output .= ']';
          }
          
          if ($args[0] == 'debug' && sizeof($args) == 1) {
            $this->threads->synchronized(function($threads){
              print_r($threads);
            }, $this->threads);
          }
                              
          if ($args[0] == 'quit' && sizeof($args) == 1) {
            break;
          }
          
          if ($args[0] == 'shutdown' && sizeof($args) == 1) {
            $this->observer->destroy();
            while( $this->observer->isRunning() ) { }
            $this->threads->synchronized(function($threads){
              $threads->destroy();
            }, $this->threads);
            socket_close ($msgsock);
            break 2;
          }
          
          if ($args[0] == 'restart' && sizeof($args) == 1) {
            $this->observer->destroy();
            while( $this->observer->isRunning() ) { }
            $this->threads->synchronized(function($threads){
              $threads->destroy();
            }, $this->threads);
            socket_close ($msgsock);
            $restart = true;
            break 2;
          }
          
          $output = $output."}\r\n";
          socket_write ($msgsock, $output, strlen ($output));
        } while (true);
        socket_close ($msgsock);
      } while (true);
      socket_close ($this->socket);
      if ($restart) {
        exit(1);
      }
    }
       
    function db() {
      return new db();
    }
    
    function start($config) {
      $output = '"id":'.$config['ID'].', "action": "start", "state" : "RUNNING"';
      if(!array_key_exists($config['ID'], $this->threads->get())) {
        $output = shell_exec('php -l "'.SKELETON_FILE.'"');
        $output = str_replace(array(chr(10), chr(13)), ' ', $output);
        $syntaxError = preg_replace("/Errors parsing.*$/", "", $output, -1, $count);
        if($count == 0) {
          $class = SKELETON;
          $bot = new $class($config['ID']);
          $this->threads->synchronized(function($threads, $config, $bot){
            $threads->add($config['ID'], array($config, $bot));
          }, $this->threads, $config, $bot);
          $output = '"id":'.$config['ID'].', "action": "start", "state" : "STARTED"';
        } else {
          $output = '"id":'.$config['ID'].', "action": "start", "state" : "ERROR", "msg" : "'.trim(str_replace('.php', '', $output)).'"';
        }
      }
      return $output;
    }
    
    function stop($config) {
      $output = '"id":'.$config['ID'].', "action": "stop", "state" : "ERROR", "msg" : "NOT_STARTED"';
      if(array_key_exists($config['ID'], $this->threads->get())) {
        $this->threads->get($config['ID'])[1]->destroy();
        $this->threads->synchronized(function($threads, $config){
          $threads->delete($config['ID']);
        }, $this->threads, $config);
        $output = '"id":'.$config['ID'].', "stop": "start", "state" : "STOPPED"';
      }
      return $output;
    }
  }
  
  //////////////////////////////////////////////////////////////////
  // TB Threads
  
  class TBPool extends Threaded {
    private $uuid;
    private $queue;
    private $pool;
    
    public function __construct() {
      $this->uuid = uniqid();
      $this->queue = array();
      $this->pool = new Pool(BOTS);
      if(file_exists(LOG_PATH.DIRECTORY_SEPARATOR.'pool.log')) {
        unlink(LOG_PATH.DIRECTORY_SEPARATOR.'pool.log');
      }
      $this->log('Pool: '.$this->uuid.' has been constructed'); 
    }
    
    function submit($thread) {
      $this->pool->submit($thread);
      $this->log('Pool: '.$this->uuid.' - thread '.get_class($thread).' has been submitted');
    }
  
    public function get($key = null) {
      if($key != null) {
        if(isset($this->queue[$key])) {
          return $this->queue[$key];
        } else {
          return null;
        }
      } else {
        return $this->queue;
      }
    }
    
    public function add($key, $thread) {
      $tmp = $this->queue;
      if(!isset($tmp[$key])) {
        $tmp[$key] = $thread;
        $this->queue = $tmp;
        $this->pool->submit($thread[1]);
        $this->log('Pool: '.$this->uuid.' - thread '.$thread[0]['ID'].' / '.get_class($thread[1]).' has been added');
        return true;
      }
      return false;
    }
    
    public function delete($key) {
      if(isset($this->queue[$key])) {
        $tmp = $this->queue;
        unset($tmp[$key]);
        $this->queue = $tmp;
        $this->log('Pool: '.$this->uuid.' - thread '.$key.' has been deleted');
        return true;
      }
      return false;
    }
    
    function log($msg) {
      $msg = str_replace(array(chr(10), chr(13)), '', $msg);
      file_put_contents(LOG_PATH.DIRECTORY_SEPARATOR.'pool.log', $msg.PHP_EOL, FILE_APPEND | LOCK_EX);
    }
    
    public function destroy() {
      $this->log('Pool: '.$this->uuid.' has been interupted');
      foreach($this->queue as $thread) {
        $thread[1]->destroy();
        while($thread[1]->isRunning()) { sleep(1); }
      }
      $this->pool->shutdown();
      $this->log('Pool: '.$this->uuid.' has finished');
    }
  }
  
  //////////////////////////////////////////////////////////////////
  // TB Observer
  
  class TBObserver extends Collectable {
  
    private $uuid;
    private $run;
    
    private $threads;
    
    //////////////////////////////////////////////////////////////////
    // Construct Observer
    
    public function __construct($threads) {
        $this->run = false;
        $this->uuid = uniqid();
        $this->threads = $threads;
        if(file_exists(LOG_PATH.DIRECTORY_SEPARATOR.'observer.log')) {
          unlink(LOG_PATH.DIRECTORY_SEPARATOR.'observer.log');
        }
        $this->log('Observer: '.$this->uuid.' has been constructed');   
    }
    
    //////////////////////////////////////////////////////////////////
    // Run Observer
    
    public function run() {
      $this->log('Observer: '.$this->uuid.' has started');     
      while($this->run) {
        sleep(5);
        $this->threads->synchronized(function($threads){        
          foreach($threads->get() as $thread) {
            if($thread[0]['ID'] > 0) {
              if(!$thread[1]->isRunning()) {
                if(file_exists(CACHE_PATH.DIRECTORY_SEPARATOR.$thread[0]['ID'].'.pid')) {
                  $this->log('Observer: '.$this->uuid.' - STOPPED bot '.$thread[0]['ID'].' with name '. $thread[0]['NAME']);
                  unlink(CACHE_PATH.DIRECTORY_SEPARATOR.$thread[0]['ID'].'.pid');
                  $threads->delete($thread[0]['ID']);
                } else {
                  $this->log('Observer: '.$this->uuid.' - CRASHED bot '.$thread[0]['ID'].' with name '. $thread[0]['NAME'].' - '.json_encode($thread[1]->getTerminationInfo(), JSON_FORCE_OBJECT));
                  file_put_contents(CACHE_PATH.DIRECTORY_SEPARATOR.get_class($thread[1]).'-'.$thread[0]['ID'].'.error.db', json_encode($thread[1]->getTerminationInfo(), JSON_FORCE_OBJECT), LOCK_EX);
                  $threads->delete($thread[0]['ID']);
                  $output = shell_exec('php -l "'.SKELETON_FILE.'"');
                  $output = str_replace(array(chr(10), chr(13)), ' ', $output);
                  $syntaxError = preg_replace("/Errors parsing.*$/", "", $output, -1, $count);
                  if($count == 0) {
                    $class = SKELETON;
                  	$bot = new $class($thread[0]['ID']);
                  	$threads->add($thread[0]['ID'], array($thread[0], $bot));         
                    $output = "RESTARTED Bot ".$thread[0]['ID']." with name '".$thread[0]['NAME']."'";
                  } else { 
                    $output = trim(str_replace('.php', '', $output));
                  }
                  $this->log('Observer: '.$this->uuid.' - '.$output);
                }
              } else {
                if($thread[1]->ping() > 0) {
                  if((time() - $thread[1]->ping()) > 600) {
                    $this->log('Observer: '.$this->uuid.' - TIMEOUT bot '.$thread[0]['ID'].' with name '. $thread[0]['NAME'].' - '.date('Y-m-d H:i:s', $thread[1]->ping()));
                    file_put_contents(CACHE_PATH.DIRECTORY_SEPARATOR.get_class($thread[1]).'-'.$thread[0]['ID'].'.error.db', json_encode($thread[1]->getTerminationInfo(), JSON_FORCE_OBJECT), LOCK_EX);
                    $threads->delete($thread[0]['ID']);
                    $output = shell_exec('php -l "'.SKELETON_FILE.'"');
                    $output = str_replace(array(chr(10), chr(13)), ' ', $output);
                    $syntaxError = preg_replace("/Errors parsing.*$/", "", $output, -1, $count);
                    if($count == 0) {
                      $class = SKELETON;
                      $bot = new $class($thread[0]['ID']);
                      $threads->add($thread[0]['ID'], array($thread[0], $bot));         
                      $output = "RESTARTED Bot ".$thread[0]['ID']." with name '".$thread[0]['NAME']."'";
                    } else { 
                      $output = trim(str_replace('.php', '', $output));
                    }
                    $this->log('Observer: '.$this->uuid.' - '.$output);
                  }
                }
              }
            }
          }
        }, $this->threads);
      }
      $this->log('Observer: '.$this->uuid.' has finished');
    }
     
    function log($msg) {
      $msg = str_replace(array(chr(10), chr(13)), '', $msg);
      file_put_contents(LOG_PATH.DIRECTORY_SEPARATOR.'observer.log', $msg.PHP_EOL, FILE_APPEND | LOCK_EX);
    }
        
    public function destroy() {
      $this->run = false;
      $this->log('Observer: '.$this->uuid.' has been interupted');
    }
    
  }

?>