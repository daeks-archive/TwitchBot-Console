<?php

  $cmd = array('id' => 'plugin.timers', 
               'level' => '',
               'help' => 'Timers Plugin');
  
  if(isset($execute) && $execute == true) {
    if(isset($init) && $init == true) {
      $db[$channelname]['config']['@plugins']['timers'] = array();
      if(!isset($db[$channelname]['config']['@plugins']['timers']['timeout'])) {
        $db[$channelname]['config']['@plugins']['timers']['timeout'] = 300;
      }
      if(!isset($db[$channelname]['config']['@plugins']['timers']['interval'])) {
        $db[$channelname]['config']['@plugins']['timers']['interval'] = 600;
      }
      if(!isset($db[$channelname]['config']['@plugins']['timers']['mode'])) {
        $db[$channelname]['config']['@plugins']['timers']['mode'] = 'PRIVMSG';
      }
    
      $tmp = $this->tmp;
                  
      $tmp['timers'][$channelname] = array();
      $db[$channelname]['timers'] = array();
      foreach ($this->db()->select('PLUGIN_TIMERS', '(botid = '.$this->config['ID'].' or botid=0) and (channelid = '.$channelid.' or channelid=0) and enabled=1')->fetchAll() as $timer) {
        $db[$channelname]['timers'][strtolower($timer['NAME'])] = $timer;
      }
      $this->tmp = $tmp;
    } else {
      if($this->access($cmd['level'])) {
        $tmp = $this->tmp;
        switch($this->mode) {  
          case 'PRIVMSG':
            foreach ($this->db[$this->target]['timers'] as $key => $timer) {
              if($timer['MODE'] == $this->mode || $timer['MODE'] == '') {
                if(isset($tmp['timers'][$this->target][$timer['ID']])) {
                  $time = (time()-$tmp['timers'][$this->target][$timer['ID']]);
                  if($time > $timer['SCHEDULE']) {
                    //if($time < $this->db[$this->target]['config']['@plugins']['timers']['timeout']) {
                      $this->say($this->target, true, $timer['VALUE']);
                    //}
                    $tmp['timers'][$this->target][$timer['ID']] = time();
                  }
                } else {
                  $tmp['timers'][$this->target][$timer['ID']] = time();
                }
              }
              if($this->command != null) {
                if($this->command == $timer['ALIAS']) {
                  $this->say($this->target, true, $timer['VALUE']);
                }
              }
            }
          break;
          case 'PING':
            foreach ($this->db[$this->target]['timers'] as $key => $timer) {
              if($timer['MODE'] == $this->mode || $timer['MODE'] == '') {
                if(isset($tmp['timers'][$this->target][$timer['ID']])) {
                  $time = (time()-$tmp['timers'][$this->target][$timer['ID']]);
                  if($time > $timer['SCHEDULE']) {
                    $this->say($this->target, true, $timer['VALUE']);
                    $tmp['timers'][$this->target][$timer['ID']] = time();
                  }
                } else {
                  $tmp['timers'][$this->target][$timer['ID']] = time();
                }
              }
            }
          break;
          default:
        }
        $this->tmp = $tmp;
      }
    }
  }

?>