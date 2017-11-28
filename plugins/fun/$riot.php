<?php

  $cmd = array('id' => 'plugion.fun.riot',
               'level' => 'moderator broadcaster',
               'help' => 'Bot goes crazy for some seconds.',
               'syntax' => '$riot <text>');
  
  if($execute) {
    if($this->access($cmd['level'])) {
      if(isset($this->data[4])) {
        $previous = array($this->config['COLOR']);
        for($i=0;$i<3;$i++) {
          $color = $this->colors[array_rand($this->colors)];
          while(in_array($color, $previous)) { 
            $color = $this->colors[array_rand($this->colors)]; 
          }
          $this->say($this->target, true, '/color '.$color);
          sleep(2);
          $this->say($this->target, false, '/me warns: '.$this->message.' or RIOT');
          array_push($previous, $color);
        }
        $this->say($this->target, true, '/color '.$this->config['COLOR']);
      }
    }
  }
  
?>