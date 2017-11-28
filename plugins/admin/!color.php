<?php

  $cmd = array('id' => 'plugin.admin.color',
               'level' => 'broadcaster owner',
               'help' => 'Changes the color of the BOT',
               'syntax' => '!color <color>');
  
  if($execute) {
    if($this->access($cmd['level'])) {
      if(isset($this->data[4])) {
        if(in_array($this->data[4], $this->colors)) {
          $this->say($this->target, true, '/color '.$this->data[4]);
        } else {
          $this->say($this->target, true, '@'.$this->username.' invalid color - Valid colors: '.implode(' ', $this->colors));
        }
      } else {
        $this->say($this->target, true, '@'.$this->username.' My color is '.$this->config['COLOR'].' - Valid colors: '.implode(' ', $this->colors));
      }
    }
  }
  
?>