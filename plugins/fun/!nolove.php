<?php

  $cmd = array('id' => 'plugin.fun.nolove',
               'level' => '',
               'help' => 'Displays the hate',
               'syntax' => '!nolove <username>');
  
  if($execute) {
    if($this->access($cmd['level'])) {
      if(isset($this->data[4])) {
          $this->say($this->target, false, 'There\'s absolutly no <3 between '.$this->username.' and '.$this->data[4]);
      }
    }
  }
  
?>