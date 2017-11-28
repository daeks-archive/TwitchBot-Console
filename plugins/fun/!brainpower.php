<?php

  $cmd = array('id' => 'plugin.fun.prainpower',
               'level' => '',
               'help' => 'Displays the brainpower',
               'syntax' => '!brainpower');
  
  if($execute) {
    if($this->access($cmd['level'])) {
      $this->say($this->target, false, 'There\'s currently only '.rand(0, 49).'% brain available <3');
    }
  }
  
?>