<?php

  $cmd = array('id' => 'plugin.credits', 
               'level' => '',
               'help' => 'Credits Plugin');
  
  if(isset($execute) && $execute == true) {
    if(isset($init) && $init == true) {
      $db[$channelname]['config']['@plugins']['credits'] = array();
      if(!isset($db[$channelname]['config']['@plugins']['credits']['unit'])) {
        $db[$channelname]['config']['@plugins']['credits']['unit'] = 'credit';
      }
      if(!isset($db[$channelname]['config']['@plugins']['credits']['units'])) {
        $db[$channelname]['config']['@plugins']['credits']['units'] = 'credits';
      }
      if(!isset($db[$channelname]['config']['@plugins']['credits']['amount'])) {
        $db[$channelname]['config']['@plugins']['credits']['amount'] = 1;
      }
    
      $tmp = $this->tmp;
      $tmp['credits'][$channelname] = array();
      $this->tmp = $tmp;
    } else {
      if($this->access($cmd['level'])) {
        switch($this->mode) {  
          case 'PRIVMSG':
            $tmp = $this->tmp;
            if(isset($tmp['credits'][$this->target][strtolower($this->username)])) {
              $tmp['credits'][$this->target][strtolower($this->username)] += 1;
            } else {
              $tmp['credits'][$this->target][strtolower($this->username)] = 1;
            }
            $this->tmp = $tmp;
          break;
          case 'PING':
            $tmp = $this->tmp;
            if ($this->db[$this->target]['ID'] > 0) {
              $db = $this->db();
              foreach($tmp['credits'][$this->target] as $username => $chatlines) {
                if($chatlines > 0) {
                  $stmt = $db->select('PLUGIN_CREDITS', "BOTID=".$this->config['ID']." and CHANNELID=".$this->db[$this->target]['ID']." and NAME='".strtolower($username)."'");
                  if($stmt->rowCount() > 0) {
                    $user = $stmt->fetch();
                    $newvalue = $user['VALUE'] + $this->db[$this->target]['config']['@plugins']['credits']['amount'];
                    
                    $stmt = $db->update('PLUGIN_CREDITS', array('VALUE' => $newvalue), "ID=".$user['ID']);
                  } else {
                    $db->insert('PLUGIN_CREDITS', array('BOTID' => $this->config['ID'], 'CHANNELID' => $this->db[$this->target]['ID'], 'NAME' => strtolower($username), 'VALUE' => 1));
                  }
                }
                
                $tmp['credits'][$this->target] = array();
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