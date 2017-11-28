<?php

  $cmd = array('id' => 'plugin.fun.kiss',
               'level' => '',
               'help' => 'Kiss for the user',
               'syntax' => '!kiss (<username>)');
  
  if($execute) {
    if($this->access($cmd['level'])) {
      if(isset($this->data[4])) {
        $this->say($this->target, false, ''.$this->username.' sends '.$this->data[4].' an air-kiss <3 An amount of '.(isset($this->tmp['stats'][$this->target][$this->command]) ? $this->tmp['stats'][$this->target][$this->command] : 0).' kisses have already been sent in this channel');
      } else {
        $this->say($this->target, false, ''.$this->config['NAME'].' kisses '.$this->username.' - You will not stay alone. B)');
      }
    }
  }
  
?>