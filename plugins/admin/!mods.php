<?php

  $cmd = array('id' => 'plugin.admin.mods',
               'level' => '',
               'help' => 'Display moderators',
               'syntax' => '!mods');
  
  if($execute) {
    if($this->access($cmd['level'])) {
      $this->say($this->target, false, '/mods');
    }
  }
  
?>