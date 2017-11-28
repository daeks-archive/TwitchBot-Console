<?php

  $cmd = array('id' => 'plugin.fun.love',
               'level' => '',
               'help' => 'Bot loves nobody',
               'syntax' => '!love <botname>');
  
  if($execute) {
    if($this->access($cmd['level'])) {
      if(isset($this->data[4])) {
        if(strtolower($this->data[4]) == strtolower($this->config['NAME'])) {
          $this->say($this->target, false, '@'.$this->username.' - He/She lies, my one and only love is '.ucfirst(ltrim($this->target, '#')).' <3');
        }
      }
    }
  }
  
?>