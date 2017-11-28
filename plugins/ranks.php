<?php

  $cmd = array('id' => 'plugin.ranks', 
               'level' => '',
               'help' => 'Ranks Plugin');
  
  if(isset($execute) && $execute == true) {
    if(isset($init) && $init == true) {
      $tmp = $this->tmp;
      $tmp['ranks'][$channelname] = array();
      $this->tmp = $tmp;
    } else {
      if($this->access($cmd['level'])) {
        switch($this->mode) {  
          case 'PRIVMSG':
            $tmp = &$this->tmp;
            if(isset($tmp['ranks'][$this->target][strtolower($this->username)])) {
              if($tmp['ranks'][$this->target][strtolower($this->username)] < 10) {
                $tmp['ranks'][$this->target][strtolower($this->username)] += 1;
              }
            } else {
              $tmp['ranks'][$this->target][strtolower($this->username)] = 1;
            }
            $this->tmp = $tmp;
          break;
          case 'PING':
            $tmp = $this->tmp;
            if ($this->db[$this->target]['ID'] > 0) {
              $rankupdates = '';
              $db = $this->db();
              foreach($tmp['ranks'][$this->target] as $username => $chatlines) {
                if($chatlines > 0) {
                  $stmt = $db->select('PLUGIN_RANKS', "BOTID=".$this->config['ID']." and CHANNELID=".$this->db[$this->target]['ID']." and NAME='".strtolower($username)."'");
                  $exp = round(rand(0,1) * 1) + 4;
                  $exp = $exp*5;
                  if($stmt->rowCount() > 0) {
                    $user = $stmt->fetch();
                    $newexp = $user['EXPERIENCE'] + $exp;
                    $level = 0;
                    
                    if($newexp > 100) {
                      $level = 1;
                      if($newexp > (((($level * 20) * $level * 0.8) + $level * 100) - 16)) {
                        while($newexp > (((($level * 20) * $level * 0.8) + $level * 100) - 16)) {
                          $level++;
                        }
                        $level -= 1;
                      }
                    }
                    
                    if($level > $user['LEVEL']) {
                      $rankupdates .= ' '.$user['NAME'].' ('.$level.'),';
                    }
                    
                    $stmt = $db->update('PLUGIN_RANKS', array('EXPERIENCE' => $newexp, 'LEVEL' => $level), "ID=".$user['ID']);
                  } else {
                    $db->insert('PLUGIN_RANKS', array('BOTID' => $this->config['ID'], 'CHANNELID' => $this->db[$this->target]['ID'], 'NAME' => strtolower($username), 'EXPERIENCE' => $exp));
                  }
                  $tmp['ranks'][$this->target][$username] -= 5;
                }
                
                if($tmp['ranks'][$this->target][$username] < 0) {
                  unset($tmp['ranks'][$this->target][$username]);
                }
              }
              if($rankupdates != '') {
                $this->say($this->target, true, '/me > Level UP KAPOW : '.rtrim($rankupdates, ','));
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