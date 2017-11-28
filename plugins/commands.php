<?php

  $cmd = array('id' => 'plugin.commands', 
               'level' => '',
               'help' => 'Custom Commands Plugin');
  
  if(isset($execute) && $execute == true) {
    if(isset($init) && $init == true) {
      $db[$channelname]['config']['@plugins']['commands'] = array();
      if(!isset($db[$channelname]['config']['@plugins']['commands']['trigger'])) {
        $db[$channelname]['config']['@plugins']['commands']['trigger'] = '!';
      }
    
      $db[$channelname]['commands'] = array();
      $where = '(botid = '.$this->config['ID'].' or botid=0) and (channelid = '.$channelid.' or channelid=0) order by botid desc, channelid desc';
      foreach ($this->db()->select('PLUGIN_COMMANDS', $where)->fetchAll() as $config) {
        if(!isset($db[$channelname]['commands'][strtolower($config['NAME'])])) {
          $db[$channelname]['commands'][strtolower($config['NAME'])] = $config;
        }
      }
    } else {  
      if($this->access($cmd['level'])) {
        switch($this->mode) {  
          case 'PRIVMSG':
            if($this->command != null && (isset($this->db[$this->target]['commands'][$this->command]) || isset($this->db[$this->target]['@commands'][$this->command]))) {
              if(isset($this->db[$this->target]['@commands'][$this->command])) {
                if($this->db[$this->target]['@commands'][$this->command]['ENABLED'] == 1 && $this->access($this->db[$this->target]['@commands'][$this->command]['LEVEL'])) {
                  if(isset($this->db[$this->target]['commands'][$this->db[$this->target]['@commands'][$this->command]['VALUE']])) {
                    $command = $this->db[$this->target]['commands'][$this->db[$this->target]['@commands'][$this->command]['VALUE']];
                    if($this->access($command['LEVEL'])) {
                      $command['VALUE'] = str_ireplace('@user@', $this->username, $command['VALUE']);
                      if(isset($this->data[4])) {
                        $command['VALUE'] = str_ireplace('@touser@', $this->data[4], $command['VALUE']);
                      } else {
                        $command['VALUE'] = str_ireplace('@touser@', 'nobody', $command['VALUE']);
                      }
                      $this->say($this->target, false, ''.$command['VALUE']);
                    }
                  } else {
                    $command = $this->db[$this->target]['commands'][$this->command];
                    if($this->access($command['LEVEL'])) {
                      $command['VALUE'] = str_ireplace('@user@', $this->username, $command['VALUE']);
                      if(isset($this->data[4])) {
                        $command['VALUE'] = str_ireplace('@touser@', $this->data[4], $command['VALUE']);
                      } else {
                        $command['VALUE'] = str_ireplace('@touser@', 'nobody', $command['VALUE']);
                      }
                      $this->say($this->target, false, ''.$command['VALUE']);
                    }
                  }
                }
              } else {
                $command = $this->db[$this->target]['commands'][$this->command];
                if($this->access($command['LEVEL'])) {
                  $command['VALUE'] = str_ireplace('@user@', $this->username, $command['VALUE']);
                  if(isset($this->data[4])) {
                    $command['VALUE'] = str_ireplace('@touser@', $this->data[4], $command['VALUE']);
                  } else {
                    $command['VALUE'] = str_ireplace('@touser@', 'nobody', $command['VALUE']);
                  }
                  $this->say($this->target, false, ''.$command['VALUE']);
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
?>