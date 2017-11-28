  <?php

  $cmd = array('id' => 'plugin.loyality.loyality',
               'level' => 'broadcaster owner',
               'help' => 'Loyality Configuration',
               'syntax' => '$loyality (<username>|[add|remove] <username> [<amount>]');
  
  if($execute) {
    if($this->access($cmd['level'])) {
      if(isset($this->data[6])) {
        if($this->data[4] == 'add') {
          if(is_numeric($this->data[6])) {
            $stmt = $this->db()->select('PLUGIN_LOYALITY', "BOTID=".$this->config['ID']." and CHANNELID=".$this->db[$this->target]['ID']." and ENABLED=1 and NAME='".strtolower($this->data[5])."'");
            if($stmt->rowCount() > 0) {
              $user = $stmt->fetch();
              $newvalue = $user['VALUE'] + $this->data[6];
              
              $stmt = $this->db()->update('PLUGIN_LOYALITY', array('VALUE' => $newvalue), "ID=".$user['ID']);
            } else {
              $this->db()->insert('PLUGIN_LOYALITY', array('BOTID' => $this->config['ID'], 'CHANNELID' => $this->db[$this->target]['ID'], 'NAME' => strtolower($this->data[5]), 'VALUE' => $this->data[6]));
            }
            if($this->data[6] == 1) {
              $this->say($this->target, true, 'Added '.$this->data[6].' '.$this->db[$this->target]['config']['@plugins']['loyality']['unit'].' to '.$this->data[5].' in '.$this->target);
            } else {
              $this->say($this->target, true, 'Added '.$this->data[6].' '.$this->db[$this->target]['config']['@plugins']['loyality']['units'].' to '.$this->data[5].' in '.$this->target);
            }
          }
        } else if($this->data[4] == 'remove') {
          if(is_numeric($this->data[6])) {
            $stmt = $this->db()->select('PLUGIN_LOYALITY', "BOTID=".$this->config['ID']." and CHANNELID=".$this->db[$this->target]['ID']." and ENABLED=1 and NAME='".strtolower($this->data[5])."'");
            if($stmt->rowCount() > 0) {
              $user = $stmt->fetch();
              $newvalue = $user['VALUE'] - $this->data[6];
              
              $stmt = $this->db()->update('PLUGIN_LOYALITY', array('VALUE' => $newvalue), "ID=".$user['ID']);
            } else {
              $this->db()->insert('PLUGIN_LOYALITY', array('BOTID' => $this->config['ID'], 'CHANNELID' => $this->db[$this->target]['ID'], 'NAME' => strtolower($this->data[5]), 'VALUE' => $this->data[6]*(-1)));
            }
            if($this->data[6] == 1) {
              $this->say($this->target, true, 'Removed '.$this->data[6].' '.$this->db[$this->target]['config']['@plugins']['loyality']['unit'].' to '.$this->data[5].' in '.$this->target);
            } else {
              $this->say($this->target, true, 'Removed '.$this->data[6].' '.$this->db[$this->target]['config']['@plugins']['loyality']['units'].' to '.$this->data[5].' in '.$this->target);
            }
          }
        }
      } else if(isset($this->data[5])) {
        if($this->data[4] == 'add') {
            $stmt = $this->db()->select('PLUGIN_LOYALITY', "BOTID=".$this->config['ID']." and CHANNELID=".$this->db[$this->target]['ID']." and ENABLED=1 and NAME='".strtolower($this->data[5])."'");
            if($stmt->rowCount() > 0) {
              $user = $stmt->fetch();
              $newvalue = $user['VALUE'] + 1;
              
              $stmt = $this->db()->update('PLUGIN_LOYALITY', array('VALUE' => $newvalue), "ID=".$user['ID']);
            } else {
              $this->db()->insert('PLUGIN_LOYALITY', array('BOTID' => $this->config['ID'], 'CHANNELID' => $this->db[$this->target]['ID'], 'NAME' => strtolower($this->data[5]), 'VALUE' => 1));
            }
            $this->say($this->target, true, 'Added 1 '.$this->db[$this->target]['config']['@plugins']['loyality']['unit'].' to '.$this->data[5].' in '.$this->target);
        } else if($this->data[4] == 'remove') {
          $stmt = $this->db()->select('PLUGIN_LOYALITY', "BOTID=".$this->config['ID']." and CHANNELID=".$this->db[$this->target]['ID']." and ENABLED=1 and NAME='".strtolower($this->data[5])."'");
          if($stmt->rowCount() > 0) {
            $user = $stmt->fetch();
            $newvalue = $user['VALUE'] - 1;
            
            $stmt = $this->db()->update('PLUGIN_LOYALITY', array('VALUE' => $newvalue), "ID=".$user['ID']);
          } else {
            $this->db()->insert('PLUGIN_LOYALITY', array('BOTID' => $this->config['ID'], 'CHANNELID' => $this->db[$this->target]['ID'], 'NAME' => strtolower($this->data[5]), 'VALUE' => -1));
          }
          $this->say($this->target, true, 'Removed 1 '.$this->db[$this->target]['config']['@plugins']['loyality']['unit'].' to '.$this->data[5].' in '.$this->target);
        }
      } else if(isset($this->data[4])) {
        $stmt = $this->db()->select('PLUGIN_LOYALITY', "BOTID=".$this->config['ID']." and CHANNELID=".$this->db[$this->target]['ID']." and ENABLED=1 and NAME='".strtolower($this->data[4])."'");
        if($stmt->rowCount() > 0) {
          $user = $stmt->fetch();
          if($user['VALUE'] == 1) {
            $this->say($this->target, true, $user['NAME'].' own '.$user['VALUE'].' '.$this->db[$this->target]['config']['@plugins']['loyality']['unit'].' in '.$this->target);
          } else {
            $this->say($this->target, true, $user['NAME'].' owns '.$user['VALUE'].' '.$this->db[$this->target]['config']['@plugins']['loyality']['units'].' in '.$this->target);
          }
        } else {
          $this->say($this->target, true, $this->data[4].' owns 0 '.$this->db[$this->target]['config']['@plugins']['loyality']['units'].' in '.$this->target);
        }
      }
    }
  }
  
?>