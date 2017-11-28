<?php

  $cmd = array('id' => 'plugin.fun.unlucky',
               'level' => '',
               'help' => 'Unlucky game counter',
               'syntax' => '!unlucky');
  
  if($execute) {
    if($this->access($cmd['level'])) {
      $this->say($this->target, false, 'Another unlucky play! - An amount of '.(isset($this->tmp['stats'][$this->target][$this->command]) ? $this->tmp['stats'][$this->target][$this->command] : 0).' plays have already been made in this channel');
    }
  }
  
?>