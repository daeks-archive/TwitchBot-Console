<?php

  $cmd = array('id' => 'plugin.fun.boyfriends',
               'level' => '',
               'help' => 'Displays the amount of the boyfriend queue',
               'syntax' => '!boyfriends');
  
  if($execute) {
    if($this->access($cmd['level'])) {
      $subcommand = rtrim($this->command, 's');
      $this->say($this->target, false, ucfirst(ltrim($this->target, '#')).' could choose between '.(isset($this->tmp['stats'][$this->target][$subcommand]) ? $this->tmp['stats'][$this->target][$subcommand] : 0).' possible boyfriends. This takes some time to evaluate. Please be patient. Kappa');
    }
  }
  
?>