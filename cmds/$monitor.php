<?php

  $cmd = array('id' => 'tb.monitor',
               'level' => 'owner',
               'help' => 'Bot Monitor',
               'syntax' => '$monitor (<channel|<plugin>|global)');
  
  if($execute) {
    if($this->access($cmd['level'])) {
      if(isset($this->data[4])) {
        if(substr($this->data[4], 0, 1) == '#') {
          $stmt = $this->db()->query("SELECT (SELECT sum(TIME)/COUNT(*) FROM logs WHERE BOTID=".$this->config['ID']." and CHANNELID=".$this->db[strtolower($this->data[4])]['ID'].") AS AVG_TIME FROM DUAL");  
          if($stmt->rowCount() > 0) {
            $monitor = $stmt->fetch();
            $this->say($this->target, true, '@'.$this->username.' - Average BOT Response: '.sprintf('%.3f', round($monitor['AVG_TIME'], 3)).'s for '.strtolower($this->data[4]));
          }
        } else if($this->data[4] == 'global') {
          $stmt = $this->db()->query("SELECT (SELECT sum(TIME)/COUNT(*) FROM logs) AS AVG_TIME FROM DUAL");  
          if($stmt->rowCount() > 0) {
            $monitor = $stmt->fetch();
            $this->say($this->target, true, '@'.$this->username.' - GLOBAL Average BOT Response: '.sprintf('%.3f', round($monitor['AVG_TIME'], 3)).'s');
          }
        } else {
          $stmt = $this->db()->query("SELECT (SELECT sum(TIME)/COUNT(*) FROM log_plugins where BOTID=".$this->config['ID']." and NAME='".strtolower($this->data[4])."') AS AVG_TIME FROM DUAL");  
          if($stmt->rowCount() > 0) {
            $monitor = $stmt->fetch();
            $this->say($this->target, true, '@'.$this->username.' - Average PLUGIN Response: '.sprintf('%.3f', round($monitor['AVG_TIME'], 3)).'s for '.strtolower($this->data[4]));
          }
        }
      } else {
        $stmt = $this->db()->query("SELECT (SELECT sum(TIME)/COUNT(*) FROM logs WHERE BOTID=".$this->config['ID'].") AS AVG_TIME FROM DUAL");  
        if($stmt->rowCount() > 0) {
          $monitor = $stmt->fetch();
          $this->say($this->target, true, '@'.$this->username.' - Average BOT Response: '.sprintf('%.3f', round($monitor['AVG_TIME'], 3)).'s');
        }
      }
    }
  }
  
?>