<?php

  $cmd = array('id' => 'tb.language',
               'level' => 'owner',
               'help' => 'Language Detection',
               'syntax' => '$language <text>');
  
  if($execute) {
    if($this->access($cmd['level'])) {
      if(isset($this->data[4])) {
        $cstart = microtime(true);
        $this->say($this->target, true, '@'.$this->username.' - Detected language: '.$this->locale->predict($this->message).' - '.sprintf('%.3f', round((microtime(true)-$cstart),3)).'ms');
      }
    }
  }
  
?>