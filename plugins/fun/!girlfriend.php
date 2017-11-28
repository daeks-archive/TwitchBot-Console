<?php

  $cmd = array('id' => 'plugin.fun.girlfriend',
               'level' => '',
               'help' => 'Adds yourself to possible girlfriend list',
               'syntax' => '!girlfriend');
  
  if($execute) {
    if($this->access($cmd['level'])) {
      $this->say($this->target, true, '/color '.$this->colors[array_rand($this->colors)]);
      sleep(1);
      $this->say($this->target, true, '@'.$this->username.' I have listed your request as possible girlfriend for '.ucfirst(ltrim($this->target, '#').' Kappa'));
      $this->say($this->target, true, '/color '.$this->config['COLOR']);
    }
  }
  
?>