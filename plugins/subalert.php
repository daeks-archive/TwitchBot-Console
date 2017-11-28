<?php

  $cmd = array('id' => 'plugin.subalert', 
               'level' => '',
               'help' => 'Subscriber Notification Plugin');
  
  if(isset($execute) && $execute == true) {
    if(isset($init) && $init == true) {
    
    } else {
      if($this->access($cmd['level'])) {
        switch($this->mode) {  
          case 'PRIVMSG':
            if(strtolower($this->username) == 'twitchnotify') {
              $submessage = '';
              for($i=3;$i<sizeof($this->data);$i++) {
                $submessage .= $this->data[$i].' ';
              }
              $this->say($this->target, true, ltrim($submessage, ':'));
            }
            break;
          default:
        }
      }
    }
  }

?>