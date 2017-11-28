<?php

  $cmd = array('id' => 'plugin.stats', 
               'level' => '',
               'help' => 'Statistic Plugin');
  
  if(isset($execute) && $execute == true) {
    if(isset($init) && $init == true) {    
      $db[$channelname]['config']['@plugins']['stats'] = array();
      if(!isset($db[$channelname]['config']['@plugins']['stats']['trigger'])) {
        $db[$channelname]['config']['@plugins']['stats']['trigger'] = '!';
      }
    
      $tmp = $this->tmp;
      $tmp['stats'][$channelname] = array();
      $this->tmp = $tmp;
    } else {
      if($this->access($cmd['level'])) {
        switch($this->mode) {  
          case 'PRIVMSG':
            $tmp = $this->tmp;
            $trigger = $this->explode(' ', $this->db[$this->target]['config']['@plugins']['stats']['trigger']);
            if(isset($trigger[substr($this->command, 0, 1)])) {
              if(isset($tmp['stats'][$this->target][$this->command])) {
                $tmp['stats'][$this->target][$this->command] += 1;
              } else {
                $tmp['stats'][$this->target][$this->command] = 1;
              }
            }
            $this->tmp = $tmp;
          break;
          case 'PING':
            $tmp = $this->tmp;
            if ($this->db[$this->target]['ID'] > 0) {
              $db = $this->db();
              foreach($tmp['stats'][$this->target] as $command => $amount) {
                if($amount > 0) {
                  $stmt = $db->select('PLUGIN_STATS', "BOTID=".$this->config['ID']." and CHANNELID=".$this->db[$this->target]['ID']." and ENABLED=1 and NAME='".$command."'");
                  if($stmt->rowCount() > 0) {
                    $object = $stmt->fetch();
                    $newvalue = $object['VALUE'] + $amount;
                    $stmt = $db->update('PLUGIN_STATS', array('VALUE' => $newvalue), "ID=".$object['ID']);
                  } else {
                    $db->insert('PLUGIN_STATS', array('BOTID' => $this->config['ID'], 'CHANNELID' => $this->db[$this->target]['ID'], 'NAME' => $command, 'VALUE' => $amount, 'INSERTBY' => strtolower($this->username)));
                  }
                }
                $tmp['stats'][$this->target] = array();
              }
            }
            unset($db);
            $this->tmp = $tmp;
          break;
          default:
        }
      }
    }
  }

?>