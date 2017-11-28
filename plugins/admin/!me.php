<?php

  $cmd = array('id' => 'tb.me',
               'level' => '',
               'help' => 'User Information',
               'syntax' => '!me <username>');

  if($execute) {
    if($this->access($cmd['level'])) {
      if(isset($this->data[4]) && $this->access('moderator broadcaster owner')) {
        $stmt = $this->db()->query("SELECT DISTINCT LOCALE, count(*) as TOTAL FROM logs WHERE name='PRIVMSG' and type='READ' and BOTID=".$this->config['ID']." and INSERTBY='".strtolower($this->data[4])."' group by LOCALE");  
        if($stmt->rowCount() > 0) {
          $language = array();
          $total = 0;
          foreach ($stmt->fetchAll() as $stat) {
            $language[$stat['LOCALE']] = $stat['TOTAL'];
            $total += $stat['TOTAL'];
          }
          
          $output = '';
          foreach($language as $lang => $count) {
            $output .= strtoupper($lang).' => '.sprintf('%.2f', round($count/$total*100, 2)).'% ';
          }
          
          $this->say($this->target, true, '@'.$this->data[4].' - Total Lines: '.$total.' - Language: '.rtrim($output, ' '));
        }

      } else {
        $stmt = $this->db()->query("SELECT DISTINCT LOCALE, count(*) as TOTAL FROM logs WHERE name='PRIVMSG' and type='READ' and BOTID=".$this->config['ID']." and INSERTBY='".strtolower($this->username)."' group by LOCALE");  
        if($stmt->rowCount() > 0) {
          $language = array();
          $total = 0;
          foreach ($stmt->fetchAll() as $stat) {
            $language[$stat['LOCALE']] = $stat['TOTAL'];
            $total += $stat['TOTAL'];
          }
          
          $output = '';
          foreach($language as $lang => $count) {
            $output .= strtoupper($lang).' => '.sprintf('%.2f', round($count/$total*100, 2)).'% ';
          }
          
          $this->say($this->target, true, '@'.$this->username.' - Total Lines: '.$total.' - Language: '.rtrim($output, ' '));
        }
      }
    }
  }

?>