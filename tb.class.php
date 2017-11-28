<?php

 /*
  *  Copyright (c) daeks, distributed
  *  as-is and without warranty under the MIT License. See
  *  [root]/LICENSE for more. This information must remain intact.
  */

  require_once(LIBS_PATH.DIRECTORY_SEPARATOR.'language'.DIRECTORY_SEPARATOR.'classifier.php');

  //////////////////////////////////////////////////////////////////
  // TB
  
  class TB extends Collectable {
  
    private $version;
    private $config;
    private $uuid;
    private $run;
    
    public $exit;
    
    private $socket;
    private $db;
    private $tmp;
    
    private $data;
    private $tags;
    private $mode;
    private $target;
    private $username;
    private $command;
    private $log;
    
    private $locale;
    
    private $colors = array('Blue', 'Coral', 'DodgerBlue', 'SpringGreen', 'YellowGreen', 'Green', 'OrangeRed', 'Red', 'GoldenRod', 'HotPink', 'CadetBlue', 'SeaGreen', 'Chocolate', 'BlueViolet', 'Firebrick');
    private $levels = array('' => 'all', 'all' => 'all', 'user' => 'user', 'regular' => 'regular', 'follower' => 'follower', 'subscriber' => 'subscriber', 'turbo' => 'turbo', 'moderator' => 'moderator', 'broadcaster' => 'broadcaster', 'owner' => 'owner');
    
    private $defaults = array('LOCALE' => 'en', 'TRIGGER' => '! $', 'DELAY' => 3, 'LIMIT' => 350, 'MUTE' => 0);
    private $tmp_tables = array('delay' => 'int', 'moderator' => 'array', 'follower' => 'array', 'regular' => 'array', 'log' => 'array');
    
    //////////////////////////////////////////////////////////////////
    // Construct BOT
    
    public function __construct($id) {
      try {
        $this->exit = 0;
        $this->log = array();
        $stmt = $this->db()->select('BOTS', "ID = ".$id);
        if($stmt->rowCount() > 0) {
          $config = $stmt->fetch();
          $config['IRC'] = $config['NAME'].'!'.$config['NAME'].'@'.$config['NAME'].'.tmi.twitch.tv';
          $this->config = $config;
          
          $this->version = date('YmdHi', filemtime(SKELETON_FILE));
          $this->run = true;
          $this->uuid = uniqid();
                    
          $this->db = array();
          
          $locale = new NGramProfiles(CACHE_PATH.DIRECTORY_SEPARATOR.$this->config['ID'].'.ngrams');
          if( !$locale->exists() ) {
            $locale->train('en',  LIBS_PATH.DIRECTORY_SEPARATOR.'language'.DIRECTORY_SEPARATOR.'english.raw');
            $locale->train('de',  LIBS_PATH.DIRECTORY_SEPARATOR.'language'.DIRECTORY_SEPARATOR.'german.raw');
            $locale->save();
          } else {
            $locale->load();
          }
          $this->locale = $locale;
          
          $tmp = array();
          foreach($this->tmp_tables as $table => $type) {
            $tmp[$table] = array();
          }
          $tmp['delay']['#'.strtolower($this->config['NAME'])] = time();
          $this->tmp = $tmp;
        } else {
          $this->error(new Exception('Bot with ID '.$id.' does not exist'));
        }
        $this->log("Thread: ".$this->uuid." has been constructed");
      } catch (Exception $e) {
        $this->error($e);
      }  
    }
    
    //////////////////////////////////////////////////////////////////
    // Run BOT
    
    public function run() {
      try {
        $this->log("Thread: ".$this->uuid." has started");       
        $this->socket = fsockopen(IRC_SERVER, IRC_PORT, $errno, $errstr, 30);
        if (!$this->socket) {
          $this->error(new Exception($errstr.'('.$errno.')'));
        } else {
          $this->send('PASS oauth:'.$this->config['OAUTH']);
          $this->send('NICK '.$this->config['NAME']);
          $this->send('USER '.$this->config['NAME'].' '.$this->config['NAME'].' '.$this->config['NAME'].' '.$this->config['NAME']);
          $this->send('CAP REQ :twitch.tv/commands');
          $this->send('CAP REQ :twitch.tv/membership');
          $this->send('CAP REQ :twitch.tv/tags');

          while($this->run) { 
            $line = $this->listen();
            iF($line != null) {
              $this->log('['.$line['time'].'] ['.$line['dump'].'] - '.$line['input']);
            }
          };
        }
        $this->log("Thread: ".$this->uuid." has finished");
      } catch (Exception $e) {
        $this->error($e);
      }
    }
    
    function load($channelid, $channelname, $channelauth = null) {
      $this->init($channelid, $channelname);
      
      $tmp = $this->tmp;
      foreach($this->tmp_tables as $table => $type) {
        if($type == 'array') {
          $tmp[$table][$channelname] = array();
        } else if($type == '') {
          $tmp[$table][$channelname] = '';
        } else if($type == 'int') {
          $tmp[$table][$channelname] = 0;
        } else {
          $tmp[$table][$channelname] = null;
        }
      }
      $this->tmp = $tmp;
      $this->lookupregular(strtolower($channelname));

      $this->log('JOIN '.$channelname);      
      $this->send('JOIN '.strtolower($channelname));
    }
    
    function unload($channelid, $channelname) {
      $db = $this->db;
      if(isset($db[$channelname])) {
        unset($db[$channelname]);
      }      
      $this->db = $db;
      
      $tmp = $this->tmp;
      foreach($this->tmp_tables as $table => $type) {
        if(isset($tmp[$table][$channelname])) {
          unset($tmp[$table][$channelname]);
        } 
      }
      $this->tmp = $tmp;
      
      $this->send('PART '.strtolower($channelname));
    }
    
    function init($channelid, $channelname) {
      $db = $this->db;

      $db[$channelname] = array();
      $db[$channelname]['ID'] = $channelid;
      $db[$channelname]['ENABLED'] = 1;
      
      $db[$channelname]['config'] = array();
      $where = '(botid = '.$this->config['ID'].' or botid=0) and (channelid = '.$channelid.' or channelid=0) and enabled=1 and pluginid=0 order by botid desc, channelid desc';
      foreach ($this->db()->select('CONFIG', $where)->fetchAll() as $config) {
        if(!isset($db[$channelname]['config'][strtolower($config['NAME'])])) {
          $db[$channelname]['config'][strtolower($config['NAME'])] = $config['VALUE'];
        }
      }
      
      foreach($this->defaults as $key => $value) {
        if(!isset($db[$channelname]['config'][strtolower($key)])) {
          $db[$channelname]['config'][strtolower($key)] = $value;
        }
      }
      
      $db[$channelname]['@plugins'] = array();
      $db[$channelname]['config']['@plugins'] = array();
      $where = '(botid = '.$this->config['ID'].' or botid=0) and (channelid = '.$channelid.' or channelid=0) order by botid desc, channelid desc';
      $ignore = array();
      foreach ($this->db()->select('PLUGINS', $where)->fetchAll() as $plugin) {
        if($plugin['ENABLED'] == 1) {
          if(!isset($ignore[$plugin['NAME']])) {
            if(!isset($db[$channelname]['@plugins'][strtolower($plugin['NAME'])])) {
              $db[$channelname]['@plugins'][strtolower($plugin['NAME'])] = $plugin;
              
              if(file_exists(PLUGINS_PATH.DIRECTORY_SEPARATOR.strtolower($plugin['NAME']).'.php')) {
                if($this->verify(PLUGINS_PATH.DIRECTORY_SEPARATOR.strtolower($plugin['NAME']).'.php')) {
                  try {
                    $execute = true;
                    $init = true;
                    include(PLUGINS_PATH.DIRECTORY_SEPARATOR.strtolower($plugin['NAME']).'.php');
                  } catch (Exception $e) {
                    $this->error($e);
                  }
                }
              } 
              
              if(!isset($db[$channelname]['config']['@plugins'][strtolower($plugin['NAME'])])) {
                $db[$channelname]['config']['@plugins'][strtolower($plugin['NAME'])] = array();
              }
              $subwhere = '(botid = '.$this->config['ID'].' or botid=0) and (channelid = '.$channelid.' or channelid=0) and enabled=1 and pluginid='.$plugin['ID'].' order by botid desc, channelid desc, pluginid desc';
              $overwrite = array();
              foreach ($this->db()->select('CONFIG', $subwhere)->fetchAll() as $subconfig) {
                if(!in_array(strtolower($subconfig['NAME']), $overwrite)) {
                  if(is_numeric($subconfig['VALUE'])) {
                    $subconfig['VALUE'] = intval($subconfig['VALUE']);
                  }
                  $db[$channelname]['config']['@plugins'][strtolower($plugin['NAME'])][strtolower($subconfig['NAME'])] = $subconfig['VALUE'];
                  array_push($overwrite, strtolower($subconfig['NAME']));
                }
              }
            }
          }
        } else {
          $ignore[$plugin['NAME']] = array();
        }
      }
      
      uasort($db[$channelname]['@plugins'], function($a, $b) { return $a['POSITION'] - $b['POSITION']; });
      
      $db[$channelname]['@locale'] = array();
      $where = '(botid = '.$this->config['ID'].' or botid=0) and (channelid = '.$channelid.' or channelid=0) and enabled=1 order by botid desc, channelid desc';
      foreach ($this->db()->select('LOCALE', $where)->fetchAll() as $locale) {
        if(!isset($db[$channelname]['@locale'][strtolower($locale['LOCALE'])])) {
          $db[$channelname]['@locale'][strtolower($locale['LOCALE'])] = array();
        }
        if(!isset($db[$channelname]['@locale'][strtolower($locale['LOCALE'])][strtolower($locale['NAME'])])) {
          $db[$channelname]['@locale'][strtolower($locale['LOCALE'])][strtolower($locale['NAME'])] = $locale['VALUE'];
        }
      }
      
      $db[$channelname]['@commands'] = array();
      $db[$channelname]['config']['@commands'] = array();      
      $where = '(botid = '.$this->config['ID'].' or botid=0) and (channelid = '.$channelid.' or channelid=0) order by botid desc, channelid desc';
      foreach ($this->db()->select('COMMANDS', $where)->fetchAll() as $command) {
        if(!isset($db[$channelname]['@commands'][strtolower($command['NAME'])])) {
          $db[$channelname]['@commands'][strtolower($command['NAME'])] = $command;
          
          if(!isset($db[$channelname]['config']['@commands'][strtolower($command['NAME'])])) {
            $db[$channelname]['config']['@commands'][strtolower($command['NAME'])] = array();
          }
          $subwhere = '(botid = '.$this->config['ID'].' or botid=0) and (channelid = '.$channelid.' or channelid=0) and enabled=1 and commandid='.$command['ID'].' order by botid desc, channelid desc, commandid desc';
          $overwrite = array();
          foreach ($this->db()->select('CONFIG', $subwhere)->fetchAll() as $subconfig) {
            if(!in_array(strtolower($subconfig['NAME']), $overwrite)) {
              if(is_numeric($subconfig['VALUE'])) {
                $subconfig['VALUE'] = intval($subconfig['VALUE']);
              }
              $db[$channelname]['config']['@commands'][strtolower($command['NAME'])][strtolower($subconfig['NAME'])] = $subconfig['VALUE'];
              array_push($overwrite, strtolower($subconfig['NAME']));
            }
          }
        }
      }
      
      $this->db = $db;
    }
    
    //////////////////////////////////////////////////////////////////
    // Interactive Functions
    
    function listen() {
      $line = fgets($this->socket, 4096);
      if(strlen(trim($line)) > 0) {
        $lstart = microtime(true);
        $line = str_replace(array(chr(10), chr(13), chr(1)), '', $line);
        $benchmark = '';
        flush();

        if(substr($line, 0, 1) == '@') {
          $tags = array();
          foreach(explode(',', substr($line, 1, strpos($line, ' '))) as $value) {
            $tag = explode('=', $value);
            if(isset($tag[1])) {
              if(is_numeric(trim($tag[1]))) {
                $tag[1] = intval(trim($tag[1]));
              }
              $tags[$tag[0]] = trim($tag[1]);
            }
          }
          $this->tags = $tags;
          $this->data = explode(' ', substr($line, strpos($line, ' ')+1));
        } else {
          $this->tags = array();
          $this->data = explode(' ', $line);
        }

        if($this->data[0] != 'PING') {
          if(sizeof($this->data) >= 3) {
            $tmp = explode('!', ltrim($this->data[0], ':'));
            if(isset($tmp[0])) {
              $this->username = $tmp[0];
            } else {
              $this->username = ltrim($this->data[0], ':');
            }
            $this->mode = $this->data[1];
            $this->target = strtolower($this->data[2]);
            $this->command = null;
                        
            switch($this->mode) {  
              case 'JOIN':
                if(ltrim($this->data[0], ':') == strtolower($this->config['IRC'])) {
                  if($this->target == '#'.strtolower($this->config['NAME'])) {
                    if($this->target != '#'.strtolower($this->config['OWNER'])) {
                      $this->say($this->target, true, '/mod '.$this->config['OWNER']);
                    }
                    $this->say(null, true, $this->nls('tb.loaded', 'Loaded v{0}', $this->version));
                    if(file_exists(CACHE_PATH.DIRECTORY_SEPARATOR.get_class().'-'.$this->config['ID'].'.error.db')) {
                      $error = json_decode(file_get_contents(CACHE_PATH.DIRECTORY_SEPARATOR.get_class().'-'.$this->config['ID'].'.error.db'), true);
                      if(isset($error['message'])) {
                        rename(CACHE_PATH.DIRECTORY_SEPARATOR.get_class().'-'.$this->config['ID'].'.error.db', CACHE_PATH.DIRECTORY_SEPARATOR.get_class().'-'.$this->config['ID'].'.lasterror.db');
                        $this->log('Error: '.$error['message']. ' in '.basename($error['file'], ".php").' on line '.$error['line']);
                        $this->say(null, true, 'Error: '.$error['message']. ' in '.basename($error['file'], ".php").' on line '.$error['line']);
                      } else {
                        unlink(CACHE_PATH.DIRECTORY_SEPARATOR.get_class().'-'.$this->config['ID'].'.error.db');
                      }
                    }
                  } else {
                    $this->say(null, true, $this->nls('tb.joined', 'Joined {0}', $this->target));
                  }
                }
                break;
              case 'PART':
                if(ltrim($this->data[0], ':') == strtolower($this->config['IRC'])) {
                  $this->say(null, true, $this->nls('tb.parted', 'Parted {0}', $this->target));
                }
                break;
              case '353':
                $this->target = strtolower($this->data[4]);
              break;
              case '366':
                $this->target = strtolower($this->data[3]);
              break;
              case '376':
                // Start joining channels
                $this->load(0, '#'.strtolower($this->config['NAME']));
                $this->say(null, true, '/color '.$this->config['COLOR']);
              
                foreach ($this->db()->select('CHANNELS', 'ENABLED=1 AND BOTID='.$this->config['ID'])->fetchAll() as $channel) {
                  $this->load($channel['ID'], strtolower($channel['NAME']));
                }
                break;
              case 'WHISPER':
                $this->message = '';
                if(isset($this->data[3])) {
                  for($i=3;$i<sizeof($this->data);$i++) {
                    $this->message .= $this->data[$i].' ';
                  }
                  foreach ($this->db()->select('BOTS', 'ENABLED=1')->fetchAll() as $bot) {
                    if(strtolower($this->username) == strtolower($bot['NAME'])) {
                      foreach($this->db as $channel => $db) {
                        $this->say($channel, true, '[GLOBAL] '.ltrim($this->message, ':'));
                      }
                    }
                  }
                }
              break;
              case 'NOTICE':
                if(isset($this->db[$this->target]) && $this->db[$this->target]['ENABLED'] == 1) {
                  if($this->tags['msg-id'] == 'room_mods') {
                    $tmp = $this->tmp;
                    for($i=9; $i<sizeof($this->data); $i++) {
                      if(!in_array(strtolower(rtrim($this->data[$i], ',')), $tmp['moderator'][$this->target])) {
                        array_push($tmp['moderator'][$this->target], strtolower(rtrim($this->data[$i], ',')));
                      }
                    }
                    $this->tmp = $tmp;
                  }
                }
              break;
              case 'MODE':
                if(isset($this->db[$this->target]) && $this->db[$this->target]['ENABLED'] == 1) {
                  if($this->data[3] == '+o') {
                    if(!in_array(strtolower($this->data[4]), $this->tmp['moderator'][$this->target])) {
                      $tmp = $this->tmp;
                      array_push($tmp['moderator'][$this->target], strtolower($this->data[4]));
                      $this->tmp = $tmp;
                    }
                  }
                  if($this->data[3] == '-o') {
                    if(($key = array_search(strtolower($this->data[4]), $this->tmp['moderator'][$this->target])) !== false) {
                      $tmp = $this->tmp;
                      unset($tmp['moderator'][$this->target][$key]);
                      $this->tmp = $tmp;
                    }
                  }
                }
                break;
              case 'PRIVMSG':
                if(isset($this->db[$this->target]) && $this->db[$this->target]['ENABLED'] == 1) {
                  if(isset($this->data[3])) {
                    $this->command = str_ireplace(array(strtolower($this->config['NAME']), strtolower($this->username)), array('BOTNAME', 'USERNAME'), strtolower(ltrim($this->data[3], ':')));
                    $this->trigger = substr($this->command, 0, 1);
                    $this->message = '';
                    if(isset($this->data[4])) {
                      for($i=4;$i<sizeof($this->data);$i++) {
                        $this->message .= $this->data[$i].' ';
                      }
                    }
                    $this->message = str_ireplace($this->config['NAME'], 'BOTNAME', $this->message);
                  }
                }
              break;
              default:
            }
            
            if(isset($this->db[$this->target]) && $this->db[$this->target]['ENABLED'] == 1) {
              $this->command = ltrim($this->command, '<');
              foreach($this->db[$this->target]['@plugins'] as $name => $plugin) {
                if($this->access($plugin['LEVEL'])) {
                  if(file_exists(PLUGINS_PATH.DIRECTORY_SEPARATOR.$name.'.php')) {
                    if($this->verify(PLUGINS_PATH.DIRECTORY_SEPARATOR.$name.'.php')) {
                      try {
                        $execute = true;
                        $init = false;
                        $pstart = microtime(true);
                        include(PLUGINS_PATH.DIRECTORY_SEPARATOR.$name.'.php');
                        $benchmark .= $name.'('.sprintf('%.3f', round((microtime(true)-$pstart),3)).') ';
                      } catch (Exception $e) {
                        $this->error($e);
                      }
                    }
                  }
                  if($this->mode == 'PRIVMSG') {
                    if($this->command != null && $this->db[$this->target]['config']['mute'] == 0) {
                      if (file_exists(PLUGINS_PATH.DIRECTORY_SEPARATOR.$name)) {
                        if($this->mode == 'PRIVMSG') {
                          if(isset($this->db[$this->target]['@commands'][$this->command])) {
                            if($this->db[$this->target]['@commands'][$this->command]['ENABLED'] == 1 && $this->access($this->db[$this->target]['@commands'][$this->command]['LEVEL'])) {
                              if(file_exists(PLUGINS_PATH.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.$this->db[$this->target]['@commands'][$this->command]['VALUE'].'.php')) {
                                if($this->verify(PLUGINS_PATH.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.$this->db[$this->target]['@commands'][$this->command]['VALUE'].'.php')) {
                                  try {
                                    $execute = true;
                                    include(PLUGINS_PATH.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.$this->db[$this->target]['@commands'][$this->command]['VALUE'].'.php');
                                  } catch (Exception $e) {
                                    $this->error($e);
                                  }
                                }
                              }
                            }
                          } else {
                            if(!ctype_alpha($this->trigger)) {
                              if(file_exists(PLUGINS_PATH.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.$this->command.'.php')) {
                                if($this->verify(PLUGINS_PATH.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.$this->command.'.php')) {
                                  try {
                                    $execute = true;
                                    include(PLUGINS_PATH.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.$this->command.'.php');
                                  } catch (Exception $e) {
                                    $this->error($e);
                                  }
                                }
                              }
                            }
                          }
                        }
                      }
                    }
                  }
                }
              }
              if($this->mode == 'PRIVMSG') {
                if($this->command != null && file_exists(CMDS_PATH.DIRECTORY_SEPARATOR.$this->command.'.php')) {
                  if($this->verify(CMDS_PATH.DIRECTORY_SEPARATOR.$this->command.'.php')) {
                    try {
                      $execute = true;
                      include(CMDS_PATH.DIRECTORY_SEPARATOR.$this->command.'.php');
                    } catch (Exception $e) {
                      $this->error($e);
                    }
                  }
                }
              }
            } 
          }
        } else {
          $this->send('PONG '.$this->data[1], true);
                            
          foreach ($this->db()->select('CHANNELS', 'BOTID='.$this->config['ID'])->fetchAll() as $config) {
            if($config['ENABLED'] == 0) {
              if(isset($this->db[strtolower($config['NAME'])])) {
                $this->unload($config['ID'], strtolower($config['NAME']));
              }
            } else if($config['ENABLED'] == 1) {
              if(!isset($this->db[strtolower($config['NAME'])])) {
                $this->load($config['ID'], strtolower($config['NAME']));
              }
            }
          }
          
          $tmp = $this->tmp;
          $tmp['delay']['#'.strtolower($this->config['NAME'])] = time();
          $this->tmp = $tmp;
          
          $this->mode = 'PING';
          $this->username = $this->config['NAME'];
          foreach($this->db as $channelname => $config) {
            $this->target = $channelname;
            foreach($config['@plugins'] as $name => $plugin) {
              if($this->access($plugin['LEVEL'])) {
                if(file_exists(PLUGINS_PATH.DIRECTORY_SEPARATOR.$name.'.php')) {
                  if($this->verify(PLUGINS_PATH.DIRECTORY_SEPARATOR.$name.'.php')) {
                    try {
                      $execute = true;
                      $init = false;
                      $pstart = microtime(true);
                      include(PLUGINS_PATH.DIRECTORY_SEPARATOR.$name.'.php');
                      $benchmark .= $channelname.'-'.$name.'('.sprintf('%.3f', round((microtime(true)-$pstart),3)).') ';
                    } catch (Exception $e) {
                      $this->error($e);
                    }
                  }
                }
              }
            }
          }
        }
        flush();
        return array('input' => $line, 'time' => sprintf('%.3f', round((microtime(true)-$lstart),3)), 'dump' => rtrim($benchmark, ' '));
      }
      return null;
    }
       
    function say($channel, $force, $message) {
      if($channel == null) {
        $channel = '#'.strtolower($this->config['NAME']);
      } else {
        if($this->db[$channel]['config']['mute'] <> 0) {
          $log = $this->log;
          array_push($log, 'MUTED PRIVMSG '.$channel.' :'.$message);
          $this->log = $log;
          return;
        }
      }
      if($force || $this->moderator($channel) || $this->regular($channel) || $this->broadcaster($channel) || $this->owner() || (time()-$this->tmp['delay'][$channel]) > $this->db[$channel]['config']['delay']) {
        $tmp = $this->tmp;
        $tmp['delay'][$channel] = time();
        array_push($tmp['log'][$channel], array(time(), 'PRIVMSG '.$channel.' :'.$message));
        foreach($tmp['log'][$channel] as $key => $log) {
          if(time()-$log[0] > 30) {
             unset($tmp['log'][$channel][$key]);
          }
        }
        
        if($this->power($channel)) {
          if(sizeof($tmp['log'][$channel]) < 50) {
            $this->send('PRIVMSG '.$channel.' :'.$message, true);
          } else {
            $log = $this->log;
            array_push($log, 'LIMIT PRIVMSG '.$channel.' :'.$message);
            $this->log = $log;
          }
        } else {
          if(sizeof($tmp['log'][$channel]) < 15) {
            $this->send('PRIVMSG '.$channel.' :'.$message, true);
          } else {
            $log = $this->log;
            array_push($log, 'LIMIT PRIVMSG '.$channel.' :'.$message);
            $this->log = $log;
          }
        }
        $this->tmp = $tmp;
      }
    }
    
    function whisper($username, $force, $message) {
      $this->say(null, $force, '/w '.$username.' '.$message);
    }
    
    //////////////////////////////////////////////////////////////////
    // Access Functions
    
    function access($levels = '') {
      $hasaccess = false;
      if($levels == '' || strtoupper($levels) == 'ALL') {
        $hasaccess = true;
      } else {
        foreach(explode(' ', strtoupper($levels)) as $level) {
          if($level == 'USER') {
              if(!$this->subscriber($this->target) && !$this->regular($this->target) && !$this->moderator($this->target) && !$this->broadcaster($this->target) && !$this->owner()) {
              $hasaccess = true;
              break;
            }
          } else if($level == 'FOLLOWER') {
            if($this->follower($this->target) || $this->moderator($this->target) || $this->broadcaster($this->target) || $this->owner()) {
              $hasaccess = true;
              break;
            }
          } else if($level == 'REGULAR') {
            if($this->regular($this->target) || $this->subscriber($this->target) || $this->moderator($this->target) || $this->broadcaster($this->target) || $this->owner()) {
              $hasaccess = true;
              break;
            }
          } else if($level == 'SUBSCRIBER') {
            if($this->subscriber($this->target) || $this->moderator($this->target) || $this->broadcaster($this->target) || $this->owner()) {
              $hasaccess = true;
              break;
            }
          } else if($level == 'TURBO') {
            if($this->turbo($this->target) || $this->moderator($this->target) || $this->broadcaster($this->target) || $this->owner()) {
              $hasaccess = true;
              break;
            }
          } else if($level == 'MODERATOR') {
            if($this->moderator($this->target) || $this->broadcaster($this->target) || $this->owner()) {
              $hasaccess = true;
              break;
            }
          } else if($level == 'BROADCASTER') {
            if($this->broadcaster($this->target) || $this->owner()) {
              $hasaccess = true;
              break;
            }
          } else if($level == 'OWNER') {
            if($this->owner()) {
              $hasaccess = true;
              break;
            }
          }
        }
      }
      return $hasaccess;
    }
    
    function user($channel) {
      return !$this->regular($channel) && !$this->moderator($channel) && !$this->broadcaster($channel) && !$this->owner();
    }
    
    function turbo($channel) {
      if(isset($this->tags['turbo']) && $this->tags['turbo'] > 0) {
        return true;
      } else {
        return false;
      }
    }
    
    function subscriber($channel) {
      if(isset($this->tags['subscriber']) && $this->tags['subscriber'] > 0) {
        return true;
      } else {
        return false;
      }
    }
    
    function follower($channel) {
      if(in_array(strtolower($this->username), array_map('strtolower', $this->tmp['follower'][$channel]))) {
        return true;
      } else {
        $this->lookupfollower($channel);
        return in_array(strtolower($this->username), array_map('strtolower', $this->tmp['follower'][$channel]));
      }
    }
        
    function lookupfollower($channel) {
      $follow = array();
      $ch = curl_init(); 
      curl_setopt($ch, CURLOPT_URL, 'https://api.twitch.tv/kraken/users/'.$this->username.'/follows/channels/'.ltrim($this->target, '#'));
      curl_setopt($ch, CURLOPT_HEADER, 0);
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
      $tmp = curl_exec($ch);
      if($tmp != '') {
        $follow = json_decode($tmp, true);
      }
      
      if(isset($follow['created_at'])) {
        $tmp = $this->tmp;
        array_push($tmp['follower'][$channel], strtolower($this->username));
        $this->tmp = $tmp;
      }
    }
    
    function regular($channel) {
      return in_array(strtolower($this->username), array_map('strtolower', $this->tmp['regular'][$channel]));
    }
    
    function lookupregular($channel) {
        $tmp = $this->tmp;
        $tmp['regular'][$channel] = array();
        foreach ($this->db()->select('REGULARS', '(botid = '.$this->config['ID'].' or botid=0) and (channelid = '.$this->db[$channel]['ID'].' or channelid=0)')->fetchAll() as $regular) {
          array_push($tmp['regular'][$channel], strtolower($regular['NAME']));
        }
        $this->tmp = $tmp;
    }

    function moderator($channel) {
      if(in_array(strtolower($this->username), array_map('strtolower', $this->tmp['moderator'][$channel]))) {
        return true;
      } else {
        if(isset($this->tags['mod']) && $this->tags['mod'] > 0) {
          $tmp = $this->tmp;
          array_push($tmp['moderator'][$channel], strtolower($this->username));
          $this->tmp = $tmp;
          return true;
        }
      }
    }
    
    function power($channel) {
      if(in_array(strtolower($this->config['NAME']), array_map('strtolower', $this->tmp['moderator'][$channel])) || (strtolower(ltrim($channel, '#')) == strtolower($this->config['NAME']))) {
        return true;
      }
    }
    
    function broadcaster($channel) {
      return (strtolower(ltrim($channel, '#')) == strtolower($this->username)) ? true : false;
    }
    
    function owner() {
      return $this->moderator('#'.strtolower($this->config['NAME'])) || (strtolower($this->username) == strtolower($this->config['OWNER']));
    }
    
    //////////////////////////////////////////////////////////////////
    // Helper Functions    
        
    function db() {
      return new db();
    }
    
    function explode($delimiter, $string) {
      $output = array();
      foreach(explode($delimiter, $string) as $key => $value) {
        $output[$value] = $value;
      }
      return $output;
    }
    
    function send($cmd, $append = false) {
      fputs($this->socket, $cmd."\r\n");
      if(!$append) {
        $this->log($cmd);
      } else {
        $log = $this->log;
        array_push($log, $cmd);
        $this->log = $log;
      }
      flush();
    }
    
    function verify($file) {
      if(!file_exists(CACHE_PATH.DIRECTORY_SEPARATOR.strtolower(str_replace(DIRECTORY_SEPARATOR, '.', $file)).'.sha1')) {
        $output = shell_exec('php -l "'.$file.'"');
        $output = str_replace(array(chr(10), chr(13)), ' ', $output);
        $syntaxError = preg_replace("/Errors parsing.*$/", "", $output, -1, $count);
        if($count == 0) {
          $this->log(trim(str_replace('\\\\', '\\', str_replace('.php', '', $output))));
          file_put_contents(CACHE_PATH.DIRECTORY_SEPARATOR.strtolower(str_replace(DIRECTORY_SEPARATOR, '.', $file)).'.sha1', sha1(file_get_contents($file)), LOCK_EX);
          touch(CACHE_PATH.DIRECTORY_SEPARATOR.strtolower(str_replace(DIRECTORY_SEPARATOR, '.', $file)).'.sha1', filemtime($file));
          return true;
        } else {
          $this->say(null, true, ' - '.trim(str_replace('\\\\', '\\', str_replace('.php', '', $output))));
          return false;
        }
      } else {
        if(filemtime($file) == filemtime(CACHE_PATH.DIRECTORY_SEPARATOR.strtolower(str_replace(DIRECTORY_SEPARATOR, '.', $file)).'.sha1')) {
          return true;
        } else {
          if(file_get_contents(CACHE_PATH.DIRECTORY_SEPARATOR.strtolower(str_replace(DIRECTORY_SEPARATOR, '.', $file)).'.sha1') == sha1(file_get_contents($file))) {
            return true;
          } else {
            $output = shell_exec('php -l "'.$file.'"');
            $output = str_replace(array(chr(10), chr(13)), ' ', $output);
            $syntaxError = preg_replace("/Errors parsing.*$/", "", $output, -1, $count);
            if($count == 0) {
              $this->log(trim(str_replace('\\\\', '\\', str_replace('.php', '', $output))));
              file_put_contents(CACHE_PATH.DIRECTORY_SEPARATOR.strtolower(str_replace(DIRECTORY_SEPARATOR, '.', $file)).'.sha1', sha1(file_get_contents($file)), LOCK_EX);
              touch(CACHE_PATH.DIRECTORY_SEPARATOR.strtolower(str_replace(DIRECTORY_SEPARATOR, '.', $file)).'.sha1', filemtime($file));
              return true;
            } else {
              $this->say(null, true, ' - '.trim(str_replace('\\\\', '\\', str_replace('.php', '', $output))));
              return false;
            }
          }
        }
      }
      return false;
    }
    
    //////////////////////////////////////////////////////////////////
    // Multilanguage
    
    function nls($code, $msg, ...$vars) {
      if(isset($this->db[$this->target]['@locale'])) {
        iF(isset($this->db[$this->target]['@locale'][strtolower($this->db[$this->target]['config']['locale'])])) {
          iF(isset($this->db[$this->target]['@locale'][strtolower($this->db[$this->target]['config']['locale'])][strtolower($code)])) {
            $msg = $this->db[$this->target]['@locale'][strtolower($this->db[$this->target]['config']['locale'])][strtolower($code)];
          } else {
            if($msg != null) {
              $this->db()->insert('LOCALE', array('LOCALE' => strtolower($this->db[$this->target]['config']['locale']), 'NAME' => $code, 'VALUE' => $msg));
            }
          }
        }
      }
      if($msg != null) {
        foreach($vars as $key => $value) {
          $msg = str_ireplace('{'.$key.'}', $value, $msg);
        }
      } else {
        $msg = $code. ' - Attributes: '.implode(' ', $vars);
      }
      return $msg;
    }
        
    //////////////////////////////////////////////////////////////////
    // Logging
    
    function error($e) {
      $this->dumperror($e);
      $this->log('ERROR '.$e->getMessage(). ' in '.str_replace(dirname(__FILE__).DIRECTORY_SEPARATOR, '', str_replace('.php', '', $e->getFile())).' on line '.$e->getLine());
      $this->say($this->target, true, 'Error: '.$e->getMessage(). ' in '.str_replace(dirname(__FILE__).DIRECTORY_SEPARATOR, '', str_replace('.php', '', $e->getFile())).' on line '.$e->getLine());
    }
    
    function dumperror($e) {
      file_put_contents(CACHE_PATH.DIRECTORY_SEPARATOR.get_class().'-'.$this->config['ID'].'.error.db', json_encode($e, JSON_FORCE_OBJECT), LOCK_EX);
    }
    
    function log($msg) {
      try {
        file_put_contents(LOG_PATH.DIRECTORY_SEPARATOR.$this->config['NAME'].'-'.date('Ymd').'.log', '['.date("Y-m-d H:i:s").'] '.$msg.PHP_EOL, FILE_APPEND | LOCK_EX);
        $logid = $this->dblog($msg);    
        foreach($this->log as $log) {
          file_put_contents(LOG_PATH.DIRECTORY_SEPARATOR.$this->config['NAME'].'-'.date('Ymd').'.log', '['.date("Y-m-d H:i:s").'] '.$log.PHP_EOL, FILE_APPEND | LOCK_EX);
          $this->dblog($log, $logid);
        }
        $this->log = array();
      } catch (Exception $e) {
        $this->dumperror($e);
        $msg = 'LOGERROR '.$e->getMessage(). ' in '.str_replace(dirname(__FILE__).DIRECTORY_SEPARATOR, '', str_replace('.php', '', $e->getFile())).' on line '.$e->getLine();
        file_put_contents(LOG_PATH.DIRECTORY_SEPARATOR.$this->config['NAME'].'-'.date('Ymd').'.log', '['.date("Y-m-d H:i:s").'] '.$msg.PHP_EOL, FILE_APPEND | LOCK_EX);
      }  
    }
    
    function dblog($msg, $parent = null) {
      $value = $msg;
      $times = array(0);
      $plugins = null;
      $locale = null;
      $parts = explode(' - ', $msg);

      if(sizeof($parts) > 1) {
        $value = '';
        for($i=1;$i<sizeof($parts);$i++) {
          $value .= trim($parts[$i]).' - ';
        }
        $value = rtrim($value, ' - ');
        $times = explode(' ', str_replace(array("[","]"), array("",""), trim($parts[0])));
        for($i=1;$i<sizeof($times);$i++) {
          $plugins .= $times[$i].' ';
        }
        $plugins = trim($plugins);
      }

      $name = '';
      $username = null;
      $channelid = 0;
      $type = 'READ';
      
      if(substr($value, 0, 1) == '@') {
        $value = substr($value, strpos($value, ' ')+1);
        $data = explode(' ', $value);
        
        $name = strtoupper(str_replace(array("[","]"), array("",""), rtrim($data[1], ':')));
        
        $tmp = explode('!', ltrim($data[0], ':'));
        if(isset($tmp[0])) {
          $username = $tmp[0];
        } else {
          $username = ltrim($data[0], ':');
        }
        $channelid = 0;
        if(isset($this->db[strtolower($data[2])])) {
          $channelid = $this->db[strtolower($data[2])]['ID'];
        }
        
        if($name == 'PRIVMSG') {
          $message = '';
          for($i=3;$i<sizeof($data);$i++) {
            $message .= $data[$i].' ';
          }
          $locale = $this->locale->predict($message);
        }
      } else if(substr($value, 0, 1) == ':') {
        $data = explode(' ', $value);
        $name = strtoupper(str_replace(array("[","]"), array("",""), rtrim($data[1], ':')));
        
        $tmp = explode('!', ltrim($data[0], ':'));
        if(isset($tmp[0])) {
          $username = $tmp[0];
        } else {
          $username = ltrim($data[0], ':');
        }
        $channelid = 0;
        if(isset($this->db[strtolower($data[2])])) {
          $channelid = $this->db[strtolower($data[2])]['ID'];
        }
        
        if($name == 'PRIVMSG') {
          $message = '';
          for($i=3;$i<sizeof($data);$i++) {
            $message .= $data[$i].' ';
          }
          $locale = $this->locale->predict($message);
        }
      } else {
        $value = $msg;
        $plugins = null;
        if(substr($value, 0, 1) == '[') {
          $parts = explode(' - ', $value);

          if(sizeof($parts) > 1) {
            $value = '';
            for($i=1;$i<sizeof($parts);$i++) {
              $value .= trim($parts[$i]).' - ';
            }
            $value = rtrim($value, ' - ');
            $times = explode(' ', str_replace(array("[","]"), array("",""), trim($parts[0])));
            for($i=1;$i<sizeof($times);$i++) {
              $plugins .= $times[$i].' ';
            }
            $plugins = trim($plugins);
          }
        }
        
        $data = explode(' ', $value);
        $name = strtoupper(str_replace(array("[","]"), array("",""), rtrim($data[0], ':')));
        if(strtoupper($name) == 'NO') {
          $name = 'SYNTAX';
        }
        $type = 'WRITE';
        $username = null;
        if(isset($this->db[strtolower($data[1])])) {
          $channelid = $this->db[strtolower($data[1])]['ID'];
        }
        if(isset($data[2])) {
          if(isset($this->db[strtolower($data[2])])) {
            $channelid = $this->db[strtolower($data[2])]['ID'];
          }
        }
      }
      if($plugins == '') {
        $plugins = null;
      }
      
     $logid = $this->db()->insert('LOGS', array('PARENTID' => $parent, 'BOTID' => $this->config['ID'], 'CHANNELID' => $channelid, 'INSERTBY' => strtolower($username), 'TYPE' => $type, 'TIME' => doubleval($times[0]), 'PLUGINS' => $plugins, 'NAME' => $name, 'VALUE' => trim($value), 'LOCALE' => $locale, 'DUMP' => $msg));
     
     if($plugins != null) {
      $db = $this->db();
      foreach(explode(' ', $plugins) as $plugin) {
        $parts = explode('-', $plugin);
        $stat = explode('(', $plugin);
        if(sizeof($parts) > 1) {
          if(isset($this->db[strtolower($parts[0])])) {
            $channelid = $this->db[strtolower($parts[0])]['ID'];
          }
          $stat = explode('(', $parts[1]);
        }
        $this->db()->insert('LOG_PLUGINS', array('BOTID' => $this->config['ID'], 'CHANNELID' => $channelid, 'LOGID' => $logid, 'NAME' => $stat[0], 'TIME' => doubleval(rtrim($stat[1],')'))));
      }
      unset($db);
     }
     
     return $logid;
    }
    
    //////////////////////////////////////////////////////////////////
    // Public Thread Functions
    
    public function ping() {
      return (isset($this->tmp['delay']['#'.strtolower($this->config['NAME'])]) ? $this->tmp['delay']['#'.strtolower($this->config['NAME'])] : 0);
    }
    
    public function status($channel = null) {
      if($channel != null) {
        $where = "BOTID=".$this->config['ID']." and ID=".trim($channel);
        if(!is_numeric(trim($channel))) {
          $where = "BOTID=".$this->config['ID']." and NAME='".trim($channel)."'";
        }
        $stmt = $this->db()->select('CHANNELS', $where);
        if($stmt->rowCount() > 0) {
          $config = $stmt->fetch();
          if(isset($this->db[strtolower($config['NAME'])])) {
            $db = $this->db;
            $db[strtolower($config['NAME'])]['ENABLED'] = $config['ENABLED'];
            $this->db = $db;
            if($config['ENABLED'] == 0) {
              $this->log('INIT PARTED '.$config['NAME']);
              return "WAITING";
            } else {
              if($this->power(strtolower($config['NAME']))) {
                return "MODDED";
              } else {
                return "JOINED";
              }
            }
          } else {
            return "PARTED";
          }
        }
      }
    }
    
    public function reinit($channel = null) {
      if($channel == null) {
        $this->init(0, '#'.strtolower($this->config['NAME']));
        $this->lookupregular('#'.strtolower($this->config['NAME']));
      
        foreach ($this->db()->select('CHANNELS', 'ENABLED=1 AND BOTID='.$this->config['ID'])->fetchAll() as $config) {
          $this->init($config['ID'], strtolower($config['NAME']));
          $this->lookupregular(strtolower($config['NAME']));
        }
      } else {
        if($channel == '#'.strtolower($this->config['NAME'])) {
          $this->init(0, '#'.strtolower($this->config['NAME']));
          $this->lookupregular('#'.strtolower($this->config['NAME']));
        } else {
          $where = "ENABLED=1 AND BOTID=".$this->config['ID']." and ID=".$channel;
          if(!is_numeric($channel)) {
            $where = "ENABLED=1 AND BOTID=".$this->config['ID']." and NAME='".$channel."'";
          }
          $stmt = $this->db()->select('CHANNELS', $where);
          if($stmt->rowCount() > 0) {
            $config = $stmt->fetch();
            $this->init($config['ID'], strtolower($config['NAME']));
            $this->lookupregular(strtolower($config['NAME']));
          }
        }
      }
      return "OK";
    }
    
    public function destroy() {
      $this->run = false;
      fclose($this->socket);
      $this->log("Thread: ".$this->uuid." has been interrupted");   
    }
  }

?>