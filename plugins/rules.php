<?php

  $cmd = array('id' => 'plugin.rules', 
               'level' => '',
               'help' => 'Timeout Rule Plugin');
  
  if(isset($execute) && $execute == true) {
    if(isset($init) && $init == true) {

    } else {
      if($this->access($cmd['level'])) {
        switch($this->mode) {  
          case 'CLEARCHAT':
            $this->say($this->target, true, 'Good Bye, '.ltrim($this->data[3],':').' - Please read and follow the channels rules!');
          break;
          default:
        }
      }
    }
  }

?>