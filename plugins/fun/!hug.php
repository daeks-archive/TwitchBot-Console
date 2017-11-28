<?php

  $cmd = array('id' => 'plugin.fun.hug',
               'level' => '',
               'help' => 'Hugs the user',
               'syntax' => '!hug (<username>)');
  
  if($execute) {
    if($this->access($cmd['level'])) {
      if(isset($this->data[4])) {
        $stmt = $this->db()->select('PLUGIN_STATS', "BOTID=".$this->config['ID']." and CHANNELID=".$this->db[$this->target]['ID']." and NAME='!hug'");  
        if($stmt->rowCount() > 0) {
          $stats = $stmt->fetch();
          $amount = $stats['VALUE'];
          if(isset($this->tmp['stats'][$this->target][$this->command])) {
            $amount += $this->tmp['stats'][$this->target][$this->command];
          }
          $this->say($this->target, false, ''.$this->username.' hugs '.$this->data[4].' B) An amount of '.$amount.' hugs have already been sent in this channel');
        } else {
          $this->say($this->target, false, ''.$this->username.' hugs '.$this->data[4].' B) An amount of '.(isset($this->tmp['stats'][$this->target][$this->command]) ? $this->tmp['stats'][$this->target][$this->command] : 0).' hugs have already been sent in this channel');
        }
      } else {
        $this->say($this->target, false, ''.$this->config['NAME'].' hugs '.$this->username.' - You will not stay alone. B)');
      }
    }
  }
  
?>