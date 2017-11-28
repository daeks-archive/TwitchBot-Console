<?php

  $cmd = array('id' => 'plugin.ranks.top',
               'level' => '',
               'help' => 'Display Top 3',
               'syntax' => '!top');
  
  if($execute) {
    if($this->access($cmd['level'])) {
      $stmt = $this->db()->query("SELECT *, FIND_IN_SET( experience, (SELECT GROUP_CONCAT( experience ORDER BY experience DESC ) FROM plugin_ranks WHERE BOTID=".$this->config['ID']." and CHANNELID=".$this->db[$this->target]['ID']." and enabled=1)) AS RANK FROM plugin_ranks WHERE BOTID=".$this->config['ID']." and CHANNELID=".$this->db[$this->target]['ID']." and ENABLED=1 order by level DESC, EXPERIENCE DESC limit 0,3");
      if($stmt->rowCount() > 0) {
        $output = '';
        foreach($stmt->fetchAll() as $user) {
          $prefix = '';
          if($user['RANK'] == 1) {
            $prefix = 'GOLD ';
          } else if($user['RANK'] == 2) {
            $prefix = 'SILVER ';
          } else if($user['RANK'] == 3) {
            $prefix = 'BRONZE ';
          }
          $output .= $prefix.$user['NAME'].' (#'.$user['RANK'].' / L'.$user['LEVEL'].'), ';
        }
        $this->say($this->target, true, 'TOP 3 in '.$this->target.': '.rtrim(trim($output), ','));
      } 
    }
  }
  
?>