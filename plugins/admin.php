<?php

  $cmd = array('id' => 'plugin.admin', 
               'level' => '',
               'help' => 'Administration Plugin');
  
  if(isset($execute) && $execute == true) {
    if(isset($init) && $init == true) {

    } else {
      if($this->access($cmd['level'])) {
        switch($this->mode) {  
          case 'NOTICE':
            $tags = array('mod_success' => '', 
                          'unmod_success' => '', 
                          'timeout_success' => '', 
                          'ban_success' => '', 
                          'unban_success' => '', 
                          'room_mods' => '', 
                          'subs_on' => '', 
                          'subs_off' => '', 
                          'slow_on' => '', 
                          'slow_off' => '', 
                          'host_on' => '');
            if(isset($this->tags['msg-id']) && isset($tags[$this->tags['msg-id']])) {
              $submessage = '';
              for($i=3;$i<sizeof($this->data);$i++) {
                $submessage .= $this->data[$i].' ';
              }
              $this->say($this->target, true, ltrim($submessage,':'));
            }
          break;
          default:
        }
      }
    }
  }

?>