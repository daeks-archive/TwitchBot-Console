<?php

  $cmd = array('id' => 'plugin.filters', 
               'level' => 'user',
               'help' => 'Filter Plugin');
  
  if(isset($execute) && $execute == true) {
    if(isset($init) && $init == true) {
      $db[$channelname]['config']['@plugins']['filters'] = array();
      if(!isset($db[$channelname]['config']['@plugins']['filters']['mode'])) {
        $db[$channelname]['config']['@plugins']['filters']['mode'] = '';
      }
      if(!isset($db[$channelname]['config']['@plugins']['filters']['modes'])) {
        $db[$channelname]['config']['@plugins']['filters']['modes'] = 'LINKS LIMIT RAID CAPS BLACKLIST EMOTES SYMBOLS REPETITIONS';
      }
      
      $tmp = $this->tmp;
      if(!isset($tmp['filters'])) {
        $tmp['filters'] = array();
      }
      $this->tmp = $tmp;
      
      foreach($this->explode(' ', $db[$channelname]['config']['@plugins']['filters']['modes']) as $mode) {
        if(file_exists(PLUGINS_PATH.DIRECTORY_SEPARATOR.'filters'.DIRECTORY_SEPARATOR.strtoupper($mode).'.php')) {
          if($this->verify(PLUGINS_PATH.DIRECTORY_SEPARATOR.'filters'.DIRECTORY_SEPARATOR.strtoupper($mode).'.php')) {
            try {
              $execute = true;
              $init = true;
              include(PLUGINS_PATH.DIRECTORY_SEPARATOR.'filters'.DIRECTORY_SEPARATOR.strtoupper($mode).'.php');
            } catch (Exception $e) {
              $this->error($e);
            }
          }
        }
      }            
    } else {
      if($this->access($cmd['level']) || $this->target == '#'.strtolower($this->config['NAME'])) {
        $modes = $this->explode(' ', $this->db[$this->target]['config']['@plugins']['filters']['modes']);
        switch($this->mode) {  
          case 'PRIVMSG':
            foreach($this->explode(' ', $this->db[$this->target]['config']['@plugins']['filters']['mode']) as $mode) {
              if(isset($modes[strtoupper($mode)]) && file_exists(PLUGINS_PATH.DIRECTORY_SEPARATOR.'filters'.DIRECTORY_SEPARATOR.strtoupper($mode).'.php')) {
                if($this->verify(PLUGINS_PATH.DIRECTORY_SEPARATOR.'filters'.DIRECTORY_SEPARATOR.strtoupper($mode).'.php')) {
                  try {
                    $violation = false;
                    $execute = true;
                    $init = false;
                    include(PLUGINS_PATH.DIRECTORY_SEPARATOR.'filters'.DIRECTORY_SEPARATOR.strtoupper($mode).'.php');
                    if($violation) { break; }
                  } catch (Exception $e) {
                    $this->error($e);
                  }
                }
              }
            }            
          break;
          case 'PING':
            foreach($this->explode(' ', $this->db[$this->target]['config']['@plugins']['filters']['mode']) as $mode) {
              if(isset($modes[strtoupper($mode)]) && file_exists(PLUGINS_PATH.DIRECTORY_SEPARATOR.'filters'.DIRECTORY_SEPARATOR.strtoupper($mode).'.php')) {
                if($this->verify(PLUGINS_PATH.DIRECTORY_SEPARATOR.'filters'.DIRECTORY_SEPARATOR.strtoupper($mode).'.php')) {
                  try {
                    $violation = false;
                    $execute = true;
                    $init = false;
                    include(PLUGINS_PATH.DIRECTORY_SEPARATOR.'filters'.DIRECTORY_SEPARATOR.strtoupper($mode).'.php');
                    if($violation) { break; }
                  } catch (Exception $e) {
                    $this->error($e);
                  }
                }
              }
            }
          break;
          default:
        }
      }
    }
  }

?>