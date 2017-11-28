<?php

  $cmd = array('id' => 'plugin.uptime.active',
               'level' => 'owner',
               'help' => 'Shows all active channels',
               'syntax' => '!active');
  
  if($execute) {
    if($this->access($cmd['level'])) {
      $active = array();
      foreach ($this->tmp['uptime'] as $key => $channel) {
        if(isset($this->tmp['uptime'][$key]['chat']) || isset($this->tmp['uptime'][$key]['stream'])) {
          array_push($active, $key);
        }
      }
      
      if(sizeof($active) > 0) {
        $this->say($this->target, true, ' - Active channels: '.implode(' ', $active));
      } else {
        $this->say($this->target, true, ' - No active channels found');
      }
    }
  }
  
?>