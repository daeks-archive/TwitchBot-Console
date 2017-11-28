<?php

  $cmd = array('id' => 'tb.ping',
               'level' => '',
               'help' => 'Pings the bot');

  if($execute) {
    if($this->access($cmd['level'])) {
      $this->say($this->target, false, '@'.$this->username.' pong');
    }
  }

?>