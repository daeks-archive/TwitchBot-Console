<?php

  $cmd = array('id' => 'tb.commands',
               'level' => '',
               'help' => 'Commands Configuration',
               'syntax' => '$commands ([enable|disable] <command>)');
  
  if($execute) {
    if($this->access($cmd['level'])) {
      if(!isset($this->data[4])) {
        $output = array();
        foreach (scandir(CMDS_PATH) as $include){
          if(is_file(CMDS_PATH.DIRECTORY_SEPARATOR.$include) && strpos($include, '..') == 0 && strtoupper(pathinfo($include, PATHINFO_EXTENSION)) == 'PHP'){
            $name = basename(CMDS_PATH.DIRECTORY_SEPARATOR.$include, '.'.pathinfo(CMDS_PATH.DIRECTORY_SEPARATOR.$include, PATHINFO_EXTENSION));
            try {
              $execute = false;
              include(CMDS_PATH.DIRECTORY_SEPARATOR.$include);
              if($this->access($cmd['level'])) {
                $name = str_replace('BOTNAME', strtolower($this->config['NAME']), $name);
                array_push($output, $name);
              }  
            } catch (Exception $e) {
              $this->error($e);
            }  
          }
        }
        
        $trigger = explode(' ', $this->db[$this->target]['config']['trigger']);
                  
        foreach($this->db[$this->target]['@plugins'] as $ext => $plugin) {
          if(is_dir(PLUGINS_PATH.DIRECTORY_SEPARATOR.$ext)) {
            foreach (scandir(PLUGINS_PATH.DIRECTORY_SEPARATOR.$ext) as $include){
              if(is_file(PLUGINS_PATH.DIRECTORY_SEPARATOR.$ext.DIRECTORY_SEPARATOR.$include) && strpos($include, '..') == 0 && strtoupper(pathinfo($include, PATHINFO_EXTENSION)) == 'PHP'){
                $name = basename(PLUGINS_PATH.DIRECTORY_SEPARATOR.$ext.DIRECTORY_SEPARATOR.$include, '.'.pathinfo(PLUGINS_PATH.DIRECTORY_SEPARATOR.$ext.DIRECTORY_SEPARATOR.$include, PATHINFO_EXTENSION));
                if(in_array(substr($name, 0, 1), $trigger) && $this->verify(PLUGINS_PATH.DIRECTORY_SEPARATOR.$ext.DIRECTORY_SEPARATOR.$include)) {
                  try {
                    $execute = false;
                    include(PLUGINS_PATH.DIRECTORY_SEPARATOR.$ext.DIRECTORY_SEPARATOR.$include);
                    if($this->access($cmd['level'])) {
                      $name = str_replace('BOTNAME', strtolower($this->config['NAME']), $name);
                      array_push($output, $name);
                    }  
                  } catch (Exception $e) {
                    $this->error($e);
                  }
                }
              }
            }
          }
        }
        
        $this->say($this->target, true, 'Available commands: '.implode(' ', $output));
      } else {
        if(isset($this->data[5])) {
          if($this->data[4] == 'enable') {
            $command = strtolower($this->data[5]);
            if($command == '*') {
              $db = $this->db();
              if($this->db[$this->target]['ID'] > 0) {
                $db->delete('COMMANDS', "BOTID=".$this->config['ID']." and CHANNELID=".$this->db[$this->target]['ID']." and NAME=VALUE");
                $db->update('COMMANDS', array('ENABLED' => 1, 'UPDATEBY' => strtolower($this->username)), "BOTID=".$this->config['ID']." and CHANNELID=".$this->db[$this->target]['ID']." and NAME!=VALUE");
                $this->reinit($this->target);
                $this->say($this->target, true, '@'.$this->username.' ALL commands enabled for '.$this->target);
              } else {
                $db->delete('COMMANDS', "BOTID=".$this->config['ID']." and NAME=VALUE");
                $db->update('COMMANDS', array('ENABLED' => 1, 'UPDATEBY' => strtolower($this->username)), "BOTID=".$this->config['ID']." and NAME!=VALUE");
                $this->reinit($this->target);
                $this->say($this->target, true, '@'.$this->username.' ALL commands enabled for all channels');
              }
              unset($db);
            } else {
              if(!file_exists(CMDS_PATH.DIRECTORY_SEPARATOR.$command.'.php')) {
                if($this->db[$this->target]['ID'] > 0) {
                  $stmt = $this->db()->select('COMMANDS', "BOTID=".$this->config['ID']." and CHANNELID=".$this->db[$this->target]['ID']." and NAME='".$command."'");
                  if($stmt->rowCount() > 0) {
                    $cmd = $stmt->fetch();
                    $this->db()->delete('COMMANDS', "ID=".$cmd['ID']);
                    $this->reinit($this->target);
                    $this->say($this->target, true, '@'.$this->username.' command enabled for '.$this->target);
                  } else {
                    $this->say($this->target, true, '@'.$this->username.' command already enabled for '.$this->target);
                  }
                } else {
                  $stmt = $this->db()->select('COMMANDS', "BOTID=".$this->config['ID']." and CHANNELID=0 and NAME='".$command."'");
                  if($stmt->rowCount() > 0) {
                    $cmd = $stmt->fetch();
                    $this->db()->delete('COMMANDS', "ID=".$cmd['ID']);
                    $this->reinit();
                    $this->say($this->target, true, '@'.$this->username.' command enabled for all channels');
                  } else {
                    $this->say($this->target, true, '@'.$this->username.' command already enabled for all channels');
                  }
                }
              }
            }
          } else if($this->data[4] == 'disable') {
            $command = strtolower($this->data[5]);
            if($command == '*') {
              $db = $this->db();
              if($this->db[$this->target]['ID'] > 0) {
                $found = array();
                foreach ($db->select('COMMANDS', "BOTID=".$this->config['ID']." and CHANNELID=".$this->db[$this->target]['ID'])->fetchAll() as $cmd) {
                  array_push($found, $cmd['NAME']);
                  $db->update('COMMANDS', array('ENABLED' => 0, 'UPDATEBY' => strtolower($this->username)),"ID=".$cmd['ID']);
                }
                
                foreach($this->db[$this->target]['@plugins'] as $ext => $plugin) {
                  if(is_dir(PLUGINS_PATH.DIRECTORY_SEPARATOR.$ext)) {
                    foreach (scandir(PLUGINS_PATH.DIRECTORY_SEPARATOR.$ext) as $include){
                      if(is_file(PLUGINS_PATH.DIRECTORY_SEPARATOR.$ext.DIRECTORY_SEPARATOR.$include) && strpos($include, '..') == 0 && strtoupper(pathinfo($include, PATHINFO_EXTENSION)) == 'PHP'){
                        $name = basename(PLUGINS_PATH.DIRECTORY_SEPARATOR.$ext.DIRECTORY_SEPARATOR.$include, '.'.pathinfo(PLUGINS_PATH.DIRECTORY_SEPARATOR.$ext.DIRECTORY_SEPARATOR.$include, PATHINFO_EXTENSION));
                        if(!in_array($name, $found)) {
                          $db->insert('COMMANDS', array('BOTID' => $this->config['ID'], 'CHANNELID' => $this->db[$this->target]['ID'], 'PLUGINID' => $this->db[$this->target]['@plugins'][$ext]['ID'], 'NAME' => $name, 'VALUE' => $name, 'ENABLED' => 0, 'INSERTBY' => strtolower($this->username)));
                        }
                      }
                    }
                  }
                }
                $this->reinit($this->target);
                $this->say($this->target, true, '@'.$this->username.' ALL commands disabled for '.$this->target);
              } else {
                $found = array();
                foreach ($db->select('COMMANDS', "BOTID=".$this->config['ID'])->fetchAll() as $cmd) {
                  array_push($found, $cmd['NAME']);
                  $db->update('COMMANDS', array('ENABLED' => 0, 'UPDATEBY' => strtolower($this->username)),"ID=".$cmd['ID']);
                }
                
                foreach($this->db[$this->target]['@plugins'] as $ext => $plugin) {
                  if(is_dir(PLUGINS_PATH.DIRECTORY_SEPARATOR.$ext)) {
                    foreach (scandir(PLUGINS_PATH.DIRECTORY_SEPARATOR.$ext) as $include){
                      if(is_file(PLUGINS_PATH.DIRECTORY_SEPARATOR.$ext.DIRECTORY_SEPARATOR.$include) && strpos($include, '..') == 0 && strtoupper(pathinfo($include, PATHINFO_EXTENSION)) == 'PHP'){
                        $name = basename(PLUGINS_PATH.DIRECTORY_SEPARATOR.$ext.DIRECTORY_SEPARATOR.$include, '.'.pathinfo(PLUGINS_PATH.DIRECTORY_SEPARATOR.$ext.DIRECTORY_SEPARATOR.$include, PATHINFO_EXTENSION));
                        if(!in_array($name, $found)) {
                          $db->insert('COMMANDS', array('BOTID' => $this->config['ID'], 'PLUGINID' => $this->db[$this->target]['@plugins'][$ext]['ID'], 'NAME' => $name, 'VALUE' => $name, 'ENABLED' => 0, 'INSERTBY' => strtolower($this->username)));
                        }
                      }
                    }
                  }
                }
                $this->reinit();
                $this->say($this->target, true, '@'.$this->username.' ALL commands disabled for all channels');
              }
              unset($db);
            } else {
              if(!file_exists(CMDS_PATH.DIRECTORY_SEPARATOR.$command.'.php')) {
                if($this->db[$this->target]['ID'] > 0) {
                  $stmt = $this->db()->select('COMMANDS', "BOTID=".$this->config['ID']." and CHANNELID=".$this->db[$this->target]['ID']." and NAME='".$command."'");
                  if($stmt->rowCount() > 0) {
                    $cmd = $stmt->fetch();
                    $this->db()->update('COMMANDS', array('ENABLED' => 0, 'UPDATEBY' => strtolower($this->username)),"ID=".$cmd['ID']);
                    $this->reinit($this->target);
                    $this->say($this->target, true, '@'.$this->username.' command disabled for '.$this->target);
                  } else {
                    $this->db()->insert('COMMANDS', array('BOTID' => $this->config['ID'], 'CHANNELID' => $this->db[$this->target]['ID'], 'NAME' => $command, 'VALUE' => $command, 'ENABLED' => 0, 'INSERTBY' => strtolower($this->username)));
                    $this->reinit($this->target);
                    $this->say($this->target, true, '@'.$this->username.' command disabled for '.$this->target);
                  }
                } else {
                  $stmt = $this->db()->select('COMMANDS', "BOTID=".$this->config['ID']." and CHANNELID=0 and NAME='".$cmd."'");
                  if($stmt->rowCount() > 0) {
                    $cmd = $stmt->fetch();
                    $this->db()->update('COMMANDS', array('ENABLED' => 0, 'UPDATEBY' => strtolower($this->username)),"ID=".$cmd['ID']);
                    $this->reinit();
                    $this->say($this->target, true, '@'.$this->username.' command disabled for all channels');
                  } else {
                    $this->db()->insert('COMMANDS', array('BOTID' => $this->config['ID'], 'NAME' => $command, 'VALUE' => $command, 'ENABLED' => 0, 'INSERTBY' => strtolower($this->username)));
                    $this->reinit($this->target);
                    $this->say($this->target, true, '@'.$this->username.' command disabled for all channels');
                  }
                }
              }
            }
          }
        }
      }
    }
  }
  
?>