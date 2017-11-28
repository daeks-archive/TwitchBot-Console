<?php

  $cmd = array('id' => 'plugin.fun.boyfriend',
               'level' => '',
               'help' => 'Adds yourself to possible boyfriend list',
               'syntax' => '!boyfriend');
  
  if($execute && false) {
    if($this->access($cmd['level'])) {
      $this->say($this->target, true, '/color '.$this->colors[array_rand($this->colors)]);
      sleep(1);
      $this->say($this->target, true, '@'.$this->username.' I have listed your request as possible boyfriend for '.ucfirst(ltrim($this->target, '#').' Kappa'));
      $this->say($this->target, true, '/color '.COLOR);
    }
  }
  
?>