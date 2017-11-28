<?php

  $cmd = array('id' => 'plugin.fun.poke',
               'level' => '',
               'help' => 'Pokes the user',
               'syntax' => '!poke (<username>)');
  
  if($execute) {
    if($this->access($cmd['level'])) {
      if(isset($this->data[4])) {
        $this->say($this->target, true, '/color BlueViolet');
        $this->say($this->target, false, '/me pokes '.$this->data[4].' <3');
        $this->say($this->target, true, '/color '.$this->config['COLOR']);
      } else {
        $this->say($this->target, true, '/color BlueViolet');
        $this->say($this->target, false, '/me pokes '.$this->username.' <3');
        $this->say($this->target, true, '/color '.$this->config['COLOR']);
      }
    }
  }
  
?>