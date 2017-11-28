<?php

  $cmd = array('id' => 'tb.alias',
               'level' => 'moderator broadcaster owner',
               'help' => 'Alias Configuration',
               'syntax' => '$alias [add|edit|delete|enable|disable] (<alias>|<alias> <command>)');
  
  if($execute) {
    if($this->access($cmd['level'])) {
      if(isset($this->data[6])) {
        if($this->data[4] == 'add') {
          $alias = strtolower($this->data[5]);
          $subcommand = strtolower($this->data[6]);
          
          $trigger = explode(' ', $this->db[$this->target]['config']['trigger']);
          if(in_array(substr($alias, 0, 1), $trigger)) {
            $found = false;
         
            if(file_exists(CMDS_PATH.DIRECTORY_SEPARATOR.$alias.'.php') || isset($this->db[$this->target]['@commands'][$alias])) {
              $found = true;
            }
            foreach($this->db[$this->target]['plugins'] as $name => $plugin) {
              if(file_exists(PLUGINS_PATH.DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.$alias.'.php')) {
                $found = true;
                break;
              }
            }
                      
            if(!$found) {
              $channelid = 0;
              if ($this->db[$this->target]['ID'] > 0) {
                $channelid = $this->db[$this->target]['ID'];
              }             
              
              if($alias != $subcommand) {
                $this->db()->insert('COMMANDS', array('BOTID' => $this->config['ID'], 'CHANNELID' => $channelid, 'NAME' => $alias, 'VALUE' => $subcommand, 'INSERTBY' => strtolower($this->username)));
                if($channelid == 0) {
                  $this->reinit();
                  $this->say($this->target, true, '@'.$this->username.' alias added for all channels');
                } else {
                  $this->reinit($this->target);
                  $this->say($this->target, true, '@'.$this->username.' alias added for '.$this->target);
                }
              }
            } else {
              $this->say($this->target, true, '@'.$this->username.' alias already exists');
            }
          }
        } else if($this->data[4] == 'edit') {
          $alias = strtolower($this->data[5]);
          $subcommand = strtolower($this->data[6]);
          if(isset($this->db[$this->target]['@commands'][$alias])) {
            if($this->db[$this->target]['ID'] > 0) {
              $stmt = $this->db()->select('COMMANDS', "BOTID=".$this->config['ID']." and CHANNELID=".$this->db[$this->target]['ID']." and NAME='".$alias."'");
              if($stmt->rowCount() > 0) {
                $alias = $stmt->fetch();
                $this->db()->update('COMMANDS', array('NAME' => $alias, 'ENABLED' => 1, 'VALUE' => $subcommand, 'UPDATEBY' => strtolower($this->username)),"ID=".$alias['ID']);
                $this->reinit($this->target);
                $this->say($this->target, true, '@'.$this->username.' alias edited for '.$this->target);
              }
            } else {
              $stmt = $this->db()->select('COMMANDS', "BOTID=".$this->config['ID']." and CHANNELID=0 and NAME='".$alias."'");
              if($stmt->rowCount() > 0) {
                $alias = $stmt->fetch();
                $this->db()->update('COMMANDS', array('NAME' => $alias, 'ENABLED' => 1, 'VALUE' => $subcommand, 'UPDATEBY' => strtolower($this->username)),"ID=".$alias['ID']);
                $this->reinit();
                $this->say($this->target, true, '@'.$this->username.' alias edited for all channels');
              }
            }
          }
        }
      } else {
        if(isset($this->data[5])) {
          if($this->data[4] == 'delete') {
            $alias = strtolower($this->data[5]);
            if($this->db[$this->target]['ID'] > 0) {
              $stmt = $this->db()->delete('COMMANDS', "BOTID=".$this->config['ID']." and CHANNELID=".$this->db[$this->target]['ID']." and NAME='".$alias."'");
              $this->reinit($this->target);
              $this->say($this->target, true, '@'.$this->username.' alias deleted for '.$this->target);
            } else {
              $stmt = $this->db()->delete('COMMANDS', "BOTID=".$this->config['ID']." and CHANNELID=0 and NAME='".$alias."'");
              $this->reinit();
              $this->say($this->target, true, '@'.$this->username.' alias deleted for all channels');
            }
          } else if($this->data[4] == 'enable') {
            $command = strtolower($this->data[5]);
            if($this->db[$this->target]['ID'] > 0) {
              $stmt = $this->db()->select('COMMANDS', "BOTID=".$this->config['ID']." and CHANNELID=".$this->db[$this->target]['ID']." and NAME='".$command."'");
              if($stmt->rowCount() > 0) {
                $cmd = $stmt->fetch();
                $this->db()->update('COMMANDS', array('ENABLED' => 1, 'UPDATEBY' => strtolower($this->username)),"ID=".$cmd['ID']);
                $this->reinit($this->target);
                $this->say($this->target, true, '@'.$this->username.' alias enabled for '.$this->target);
              } else {
                $this->say($this->target, true, '@'.$this->username.' alias already enabled for '.$this->target);
              }
            } else {
              $stmt = $this->db()->select('COMMANDS', "BOTID=".$this->config['ID']." and CHANNELID=0 and NAME='".$command."'");
              if($stmt->rowCount() > 0) {
                $cmd = $stmt->fetch();
                $this->db()->update('COMMANDS', array('ENABLED' => 1, 'UPDATEBY' => strtolower($this->username)),"ID=".$cmd['ID']);
                $this->reinit();
                $this->say($this->target, true, '@'.$this->username.' alias enabled for all channels');
              }
            }
          } else if($this->data[4] == 'disable') {
            $command = strtolower($this->data[5]);
            if($this->db[$this->target]['ID'] > 0) {
              $stmt = $this->db()->select('COMMANDS', "BOTID=".$this->config['ID']." and CHANNELID=".$this->db[$this->target]['ID']." and NAME='".$command."'");
              if($stmt->rowCount() > 0) {
                $cmd = $stmt->fetch();
                $this->db()->update('COMMANDS', array('ENABLED' => 0, 'UPDATEBY' => strtolower($this->username)),"ID=".$cmd['ID']);
                $this->reinit($this->target);
                $this->say($this->target, true, '@'.$this->username.' alias disabled for '.$this->target);
              } else {
                $this->db()->insert('COMMANDS', array('BOTID' => $this->config['ID'], 'CHANNELID' => $this->db[$this->target]['ID'], 'NAME' => $command, 'VALUE' => $command, 'ENABLED' => 0, 'INSERTBY' => strtolower($this->username)));
                $this->reinit($this->target);
                $this->say($this->target, true, '@'.$this->username.' alias disabled for '.$this->target);
              }
            } else {
              $stmt = $this->db()->select('COMMANDS', "BOTID=".$this->config['ID']." and CHANNELID=0 and NAME='".$cmd."'");
              if($stmt->rowCount() > 0) {
                $cmd = $stmt->fetch();
                $this->db()->update('COMMANDS', array('ENABLED' => 0, 'UPDATEBY' => strtolower($this->username)),"ID=".$cmd['ID']);
                $this->reinit();
                $this->say($this->target, true, '@'.$this->username.' alias disabled for all channels');
              } else {
                $this->db()->insert('COMMANDS', array('BOTID' => $this->config['ID'], 'NAME' => $command, 'VALUE' => $command, 'ENABLED' => 0, 'INSERTBY' => strtolower($this->username)));
                $this->reinit($this->target);
                $this->say($this->target, true, '@'.$this->username.' alias disabled for all channels');
              }
            }
          }
        }
      }
    }
  }
  
?>