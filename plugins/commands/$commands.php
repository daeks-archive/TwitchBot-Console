<?php

  $cmd = array('id' => 'plugin.commands.commands',
               'level' => 'moderator broadcaster owner',
               'help' => 'Commands Configuration',
               'syntax' => '$commands [add|edit|delete|enable|disable] (*|<command>|<command> <text>) - Variables: @USER@, @TOUSER@');
  
  if($execute) {
    if($this->access($cmd['level'])) {
      if(isset($this->data[6])) {
        if($this->data[4] == 'add') {
          $subcommand = strtolower($this->data[5]);
          $submessage = '';
          for($i=6;$i<sizeof($this->data);$i++) {
            $submessage .= $this->data[$i].' ';
          }
          $trigger = explode(' ', $this->db[$this->target]['config']['@plugins']['commands']['trigger']);
          
          if(in_array(substr($subcommand, 0, 1), $trigger)) {
            $found = false;
         
            if(file_exists(CMDS_PATH.DIRECTORY_SEPARATOR.$subcommand.'.php') || isset($this->db[$this->target]['commands'][$subcommand])) {
              $found = true;
            }
            foreach($this->db[$this->target]['plugins'] as $name => $plugin) {
              if(file_exists(PLUGINS_PATH.DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.$subcommand.'.php')) {
                $found = true;
                break;
              }
            }
                      
            if(!$found) {
              if(strlen(trim($submessage)) < $this->db[$this->target]['config']['limit']) {
                $channelid = 0;
                if ($this->db[$this->target]['ID'] > 0) {
                  $channelid = $this->db[$this->target]['ID'];
                }             
                
                $this->db()->insert('PLUGIN_COMMANDS', array('BOTID' => $this->config['ID'], 'CHANNELID' => $channelid, 'NAME' => $subcommand, 'VALUE' => $submessage, 'INSERTBY' => strtolower($this->username)));
                if($channelid == 0) {
                  $this->reinit();
                  $this->say($this->target, true, '@'.$this->username.' command added for all channels');
                } else {
                  $this->reinit($this->target);
                  $this->say($this->target, true, '@'.$this->username.' command added for '.$this->target);
                }
              } else {
                $this->say($this->target, true, '@'.$this->username.' command excided '.$this->db[$this->target]['config']['limit'] .' chars');
              }
            } else {
              $this->say($this->target, true, '@'.$this->username.' command already exists');
            }
          }
        } else if($this->data[4] == 'edit') {
          $subcommand = strtolower($this->data[5]);
          $submessage = '';
          for($i=6;$i<sizeof($this->data);$i++) {
            $submessage .= $this->data[$i].' ';
          }
          $trigger = explode(' ', $this->db[$this->target]['config']['@plugins']['commands']['trigger']);
          
          if(in_array(substr($subcommand, 0, 1), $trigger)) {
            if(isset($this->db[$this->target]['commands'][$subcommand])) {
              if(strlen(trim($submessage)) < $this->db[$this->target]['config']['limit'] ) {
                if($this->db[$this->target]['ID'] > 0) {
                  $stmt = $this->db()->select('PLUGIN_COMMANDS', "BOTID=".$this->config['ID']." and CHANNELID=".$this->db[$this->target]['ID']." and NAME='".$subcommand."'");
                  if($stmt->rowCount() > 0) {
                    $customcmd = $stmt->fetch();
                    $this->db()->update('PLUGIN_COMMANDS', array('NAME' => $subcommand, 'VALUE' => $submessage, 'UPDATEBY' => strtolower($this->username)),"ID=".$customcmd['ID']);
                    $this->reinit($this->target);
                    $this->say($this->target, true, '@'.$this->username.' command edited for '.$this->target);
                  }
                } else {
                  $stmt = $this->db()->select('PLUGIN_COMMANDS', "BOTID=".$this->config['ID']." and CHANNELID=0 and NAME='".$subcommand."'");
                  if($stmt->rowCount() > 0) {
                    $customcmd = $stmt->fetch();
                    $this->db()->update('PLUGIN_COMMANDS', array('NAME' => $subcommand, 'VALUE' => $submessage, 'UPDATEBY' => strtolower($this->username)),"ID=".$customcmd['ID']);
                    $this->reinit();
                    $this->say($this->target, true, '@'.$this->username.' command edited for all channels');
                  }
                }
              } else {
                $this->say($this->target, true, '@'.$this->username.' command excided '.$this->db[$this->target]['config']['limit'] .' chars');
              }
            }
          }
        }
      } else {
        if(isset($this->data[5])) {
          if($this->data[4] == 'delete') {
            $subcommand = strtolower($this->data[5]);
            $trigger = explode(' ', $this->db[$this->target]['config']['@plugins']['commands']['trigger']);
            if(in_array(substr($subcommand, 0, 1), $trigger)) {
              if(isset($this->db[$this->target]['commands'][$subcommand])) {
                if($this->db[$this->target]['ID'] > 0) {
                  $stmt = $this->db()->delete('PLUGIN_COMMANDS', "BOTID=".$this->config['ID']." and CHANNELID=".$this->db[$this->target]['ID']." and NAME='".$subcommand."'");
                  $this->reinit($this->target);
                  $this->say($this->target, true, '@'.$this->username.' command deleted for '.$this->target);
                } else {
                  $stmt = $this->db()->delete('PLUGIN_COMMANDS', "BOTID=".$this->config['ID']." and CHANNELID=0 and NAME='".$subcommand."'");
                  $this->reinit();
                  $this->say($this->target, true, '@'.$this->username.' command deleted for all channels');
                }
              }
            }
          } else if($this->data[4] == 'enable') {
            $command = strtolower($this->data[5]);
            if($command == '*') {
              $db->delete('COMMANDS', "BOTID=".$this->config['ID']." and CHANNELID=".$this->db[$this->target]['ID']." and PLUGINID=".$this->db[$this->target]['@plugins']['commands']['ID']." and NAME=VALUE");
            }
          } else if($this->data[4] == 'disable') {
            $command = strtolower($this->data[5]);
            if($command == '*') {
              $db = $this->db();
              if($this->db[$this->target]['ID'] > 0) {
                foreach ($db->select('PLUGIN_COMMANDS', "BOTID=".$this->config['ID']." and CHANNELID=".$this->db[$this->target]['ID'])->fetchAll() as $cmd) {
                  $this->db()->insert('COMMANDS', array('BOTID' => $this->config['ID'], 'PLUGINID' => $this->db[$this->target]['@plugins']['commands']['ID'], 'CHANNELID' => $this->db[$this->target]['ID'], 'NAME' => $cmd['NAME'], 'VALUE' => $cmd['NAME'], 'ENABLED' => 0, 'INSERTBY' => strtolower($this->username)));
                }
              } else {
                foreach ($db->select('PLUGIN_COMMANDS', "BOTID=".$this->config['ID'])->fetchAll() as $cmd) {
                  $this->db()->insert('COMMANDS', array('BOTID' => $this->config['ID'], 'PLUGINID' => $this->db[$this->target]['@plugins']['commands']['ID'], 'NAME' => $cmd['NAME'], 'VALUE' => $cmd['NAME'], 'ENABLED' => 0, 'INSERTBY' => strtolower($this->username)));
                }
              }
              unset($db);
            }
          }
        } else {
          if(sizeof($this->db[$this->target]['commands']) > 0) {
            $this->say($this->target, true, 'Available commands: '.implode(' ', array_keys($this->db[$this->target]['commands'])));
          }
        }
      }
    } else {
      if(sizeof($this->db[$this->target]['commands']) > 0) {
        $this->say($this->target, true, 'Available commands: '.implode(' ', array_keys($this->db[$this->target]['commands'])));
      }
    }
  }
  
?>