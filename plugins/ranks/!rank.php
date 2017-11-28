<?php

  $cmd = array('id' => 'plugin.ranks.rank',
               'level' => '',
               'help' => 'Display Rank',
               'syntax' => '!rank');
  
  if($execute) {
    if($this->access($cmd['level'])) {
      if(isset($this->data[4]) && $this->access('moderator broadcaster owner')) {
        $stmt = $this->db()->query("SELECT *, FIND_IN_SET( experience, (SELECT GROUP_CONCAT( experience ORDER BY experience DESC ) FROM plugin_ranks WHERE BOTID=".$this->config['ID']." and CHANNELID=".$this->db[$this->target]['ID']." and enabled=1)) AS RANK FROM plugin_ranks WHERE BOTID=".$this->config['ID']." and CHANNELID=".$this->db[$this->target]['ID']." and NAME='".strtolower($this->data[4])."'");  
        if($stmt->rowCount() > 0) {
          $user = $stmt->fetch();
          $nextlevel = ((((($user['LEVEL']+1) * 20) * ($user['LEVEL']+1) * 0.8) + ($user['LEVEL']+1) * 100) - 16);
          $this->say($this->target, true, 'Rank for '.$this->data[4].': #'.$user['RANK'].' - Level '.$user['LEVEL'].' ('.$user['EXPERIENCE'].' / '.$nextlevel.' XP) in '.$this->target);
        } else {
          $this->say($this->target, true, $this->data[4].' has no rank in '.$this->target);
        }
      } else {
        $stmt = $this->db()->query("SELECT *, FIND_IN_SET( experience, (SELECT GROUP_CONCAT( experience ORDER BY experience DESC ) FROM plugin_ranks WHERE BOTID=".$this->config['ID']." and CHANNELID=".$this->db[$this->target]['ID']." and ENABLED=1)) AS RANK FROM plugin_ranks WHERE BOTID=".$this->config['ID']." and CHANNELID=".$this->db[$this->target]['ID']." and NAME='".strtolower($this->username)."' and ENABLED=1");  
        if($stmt->rowCount() > 0) {
          $user = $stmt->fetch();
          $nextlevel = ((((($user['LEVEL']+1) * 20) * ($user['LEVEL']+1) * 0.8) + ($user['LEVEL']+1) * 100) - 16);
          $this->say($this->target, true, $this->username.': #'.$user['RANK'].' - Level '.$user['LEVEL'].' ('.$user['EXPERIENCE'].' / '.$nextlevel.' XP) in '.$this->target);
        } else {
          $this->say($this->target, true, $this->username.' - You have no rank in '.$this->target);
        }
      }
    }
  }
  
?>