<?php

  $cmd = array('id' => 'plugin.credits.credits',
               'level' => '',
               'help' => 'Display Credits',
               'syntax' => '!credits');
  
  if($execute) {
    if($this->access($cmd['level'])) {
      $stmt = $this->db()->select('PLUGIN_CREDITS', "BOTID=".$this->config['ID']." and CHANNELID=".$this->db[$this->target]['ID']." and NAME='".strtolower($this->username)."' and ENABLED=1");  
      if($stmt->rowCount() > 0) {
        $user = $stmt->fetch();
        if($user['VALUE'] == 1) {
          $this->say($this->target, true, '@'.$this->username.' - You own '.$user['VALUE'].' '.$this->db[$this->target]['config']['@plugins']['credits']['unit'].' in '.$this->target);
        } else {
          $this->say($this->target, true, '@'.$this->username.' - You own '.$user['VALUE'].' '.$this->db[$this->target]['config']['@plugins']['credits']['units'].' in '.$this->target);
        }
      } else {
        $this->say($this->target, true, '@'.$this->username.' - You have no '.$this->db[$this->target]['config']['@plugins']['credits']['units'].' in '.$this->target);
      }
    }
  }
  
?>