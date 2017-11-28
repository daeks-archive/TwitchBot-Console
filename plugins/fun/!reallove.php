<?php

  $cmd = array('id' => 'plugin.fun.reallove',
               'level' => '',
               'help' => 'Displays the real love',
               'syntax' => '!reallove <username>');
  
  if($execute) {
    if($this->access($cmd['level'])) {
      if(isset($this->data[4])) {
        if(strtolower($this->data[4]) == strtolower(ltrim($this->target, '#')) && strtolower($this->username) == strtolower($this->config['OWNER'])) {
          $this->say($this->target, false, 'There\'s '.rand(90, 100).'% real <3 between '.$this->username.' and '.$this->data[4]);
        } else if(strtolower($this->data[4]) == strtolower(ltrim($this->target, '#')) || strtolower($this->data[4]) == strtolower($this->config['OWNER'])) {
          $this->say($this->target, false, 'There\'s '.rand(0, 39).'% real <3 between '.$this->username.' and '.$this->data[4]);
        } else {
          $this->say($this->target, false, 'There\'s '.rand(1, 100).'% real <3 between '.$this->username.' and '.$this->data[4]);
        }
      }
    }
  }
  
?>