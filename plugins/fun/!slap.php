<?php

  $cmd = array('id' => 'plugin.fun.slap',
               'level' => '',
               'help' => 'Slaps an user',
               'syntax' => '!slap <username>');
  
  if($execute) {
    if($this->access($cmd['level'])) {
      if(isset($this->data[4])) {
        $this->say($this->target, false, '/me slaps '.$this->data[4]);
      }
    }
  }
  
?>