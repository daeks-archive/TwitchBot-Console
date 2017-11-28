<?php

  $cmd = array('id' => 'plugin.botalert', 
               'level' => '',
               'help' => 'Bot Welcome Notification Plugin');
  
  if(isset($execute) && $execute == true) {
    if(isset($init) && $init == true) {
    
    } else {
      if($this->access($cmd['level'])) {
        switch($this->mode) {  
          case 'JOIN':
            if(ltrim($this->data[0], ':') == strtolower($this->config['IRC'])) {
              if($this->target != '#'.strtolower($this->config['NAME'])) {
                $this->say($this->target, true, '/me is dancing again. HeyGuys');
              }
            }
            break;
          case 'PART':
            if(ltrim($this->data[0], ':') == strtolower($this->config['IRC'])) {
              $this->say($this->target, true, '/me is dancing somewhere else. PJSalt');
            }
            break;
          default:
        }
      }
    }
  }

?>