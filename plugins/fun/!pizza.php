<?php

  $cmd = array('id' => 'plugin.fun.pizza',
               'level' => '',
               'help' => 'Pizza counter',
               'syntax' => '!pizza');
  
  if($execute) {
    if($this->access($cmd['level'])) {
      $this->say($this->target, false, 'Another pizza dies! - An amount of '.(isset($this->tmp['stats'][$this->target][$this->command]) ? $this->tmp['stats'][$this->target][$this->command] : 0).' pizzas have already been eaten in this channel');
    }
  }
  
?>