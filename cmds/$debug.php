<?php

  $cmd = array('id' => 'tb.debug',
               'level' => 'owner',
               'help' => 'Bot Master Configuration',
               'syntax' => '$debug [set|delete|print|#channel|pluginname|global|reinit|say] [key[=value]|set|delete] key[=value]');
  
  if($execute) {
    if($this->access($cmd['level'])) {
      if(isset($this->data[5]) && substr($this->data[5], 0, 1) != '@') {
        if($this->data[4] == 'say') {
          $submessage = '';
          for($i=6;$i<sizeof($this->data);$i++) {
            $submessage .= $this->data[$i].' ';
          }
          if(substr($this->data[5], 0, 1) == '#') {
            $this->send('PRIVMSG '.strtolower($this->data[5]).' :'.$submessage);
          } else {
            $this->send('PRIVMSG #'.strtolower($this->config['NAME']).' /w '.$this->data[5].' '.$submessage);
          }
        } else if($this->data[4] == 'set') {
          $config = split("=",$this->data[5]);
          if($this->db[$this->target]['ID'] > 0) {
            $stmt = $this->db()->select('CONFIG', "BOTID = ".$this->config['ID']." and CHANNELID=".$this->db[$this->target]['ID']." and NAME ='".strtoupper($config[0])."'");
            if($stmt->rowCount() > 0) {
              $this->db()->update('CONFIG', array('VALUE' => $config[1], 'UPDATEBY' => strtolower($this->username)),"BOTID = ".$this->config['ID']." and CHANNELID=".$this->db[$this->target]['ID']." and NAME ='".strtoupper($config[0])."'");
            } else {
              $this->db()->insert('CONFIG', array('BOTID' => $this->config['ID'], 'CHANNELID' => $this->db[$this->target]['ID'], 'NAME' => strtoupper($config[0]), 'VALUE' => $config[1], 'INSERTBY' => strtolower($this->username)));
            }
            $this->say($this->target, true, '@'.$this->username." - Key '".strtolower($config[0])."' set to '".$config[1]."' for ".$this->target);
            $this->reinit($this->target);
          } else {
            $stmt = $this->db()->select('CONFIG', "BOTID = ".$this->config['ID']." and CHANNELID=0 and NAME ='".strtoupper($config[0])."'");
            if($stmt->rowCount() > 0) {
              $this->db()->update('CONFIG', array('VALUE' => $config[1], 'UPDATEBY' => strtolower($this->username)),"BOTID = ".$this->config['ID']." and CHANNELID=0 and NAME ='".strtoupper($config[0])."'");
            } else {
              $this->db()->insert('CONFIG', array('BOTID' => $this->config['ID'], 'NAME' => strtoupper($config[0]), 'VALUE' => $config[1], 'INSERTBY' => strtolower($this->username)));
            }
            $this->say($this->target, true, '@'.$this->username." - Key '".strtolower($config[0])."' set to '".$config[1]."' for ".$this->target);
            $this->reinit();
          }
        } else if($this->data[4] == 'delete') {
          if($this->db[$this->target]['ID'] > 0) {
            $stmt = $this->db()->select('CONFIG', "BOTID = ".$this->config['ID']." and CHANNELID=".$this->db[$this->target]['ID']." and NAME ='".strtoupper($this->data[5])."'");
            if($stmt->rowCount() > 0) {
              $this->db()->delete('CONFIG', "BOTID = ".$this->config['ID']." and CHANNELID=".$this->db[$this->target]['ID']." and NAME ='".strtoupper($this->data[5])."'");
              $this->say($this->target, true, '@'.$this->username." - Key '".strtolower($this->data[5])."' deleted for ".$this->target);
              $this->reinit($this->target);
            } 
          } else {
            $stmt = $this->db()->select('CONFIG', "BOTID = ".$this->config['ID']." and CHANNELID=0 and NAME ='".strtoupper($this->data[5])."'");
            if($stmt->rowCount() > 0) {
              $this->db()->delete('CONFIG', "BOTID = ".$this->config['ID']." and CHANNELID=0 and NAME ='".strtoupper($this->data[5])."'");
              $this->say($this->target, true, '@'.$this->username." - Key '".strtolower($this->data[5])."' deleted");
              $this->reinit();
            } 
          }
        } else if($this->data[4] == 'global') {
          if($this->data[5] == 'set') {
            $config = split("=",$this->data[6]);
            $stmt = $this->db()->select('CONFIG', "BOTID=0 and CHANNELID=0 and PLUGINID=0 and COMMANDID=0 and NAME ='".strtoupper($config[0])."'");
            if($stmt->rowCount() > 0) {
              $this->db()->update('CONFIG', array('VALUE' => $config[1], 'UPDATEBY' => strtolower($this->username)),"BOTID=0 and CHANNELID=0 and NAME ='".strtoupper($config[0])."'");             
            } else {
              $this->db()->insert('CONFIG', array('NAME' => strtoupper($config[0]), 'VALUE' => $config[1], 'INSERTBY' => strtolower($this->username)));
            }
            $this->say($this->target, true, '@'.$this->username." - Key '".strtolower($config[0])."' set to '".$config[1]."' for GLOBAL Please restart bots.");
            $this->reinit();
          } else if($this->data[5] == 'delete') {
            $stmt = $this->db()->select('CONFIG', "BOTID=0 and CHANNELID=0 and PLUGINID=0 and COMMANDID=0 and NAME ='".strtoupper($this->data[6])."'");
            if($stmt->rowCount() > 0) {
              $this->db()->delete('CONFIG', "BOTID=0 and CHANNELID=0 and PLUGINID=0 and COMMANDID=0 and NAME ='".strtoupper($this->data[6])."'");
              $this->say($this->target, true, '@'.$this->username." - Key '".strtolower($this->data[6])."' deleted for GLOBAL. Please restart bots.");
              $this->reinit();
            }
          } else if($this->data[5] == 'say') {
            if(isset($this->data[6])) {
              $submessage = '';
              for($i=6;$i<sizeof($this->data);$i++) {
                $submessage .= $this->data[$i].' ';
              }
              foreach ($this->db()->select('BOTS', 'ENABLED=1')->fetchAll() as $bot) {
                if(strtolower($this->config['NAME']) != strtolower($bot['NAME'])) {
                  $this->say($channel, true, '/w '.$bot['NAME'].' '.$submessage);
                }
              }
              foreach($this->db as $channel => $db) {
                $this->say($channel, true, '[GLOBAL] '.$submessage);
              }
            }
          }
        } else if($this->data[4] == 'print') {
          if(isset($this->data[6])) {
            if($this->data[6] == '*') {
              $this->log('PRINT '.json_encode($this->db[strtolower($this->data[5])], JSON_FORCE_OBJECT | JSON_UNESCAPED_UNICODE));
              $this->log('PRINT '.json_encode($this->tmp[strtolower($this->data[5])], JSON_FORCE_OBJECT | JSON_UNESCAPED_UNICODE));
            } else {
              if(isset($this->db[strtolower($this->data[5])][strtolower($this->data[6])])) {
                $this->log('PRINT '.json_encode($this->db[strtolower($this->data[5])][strtolower($this->data[6])], JSON_FORCE_OBJECT | JSON_UNESCAPED_UNICODE));
              }
              if(isset($this->tmp[strtolower($this->data[6])][strtolower($this->data[5])])) {
                $this->log('PRINT '.json_encode($this->tmp[strtolower($this->data[6])][strtolower($this->data[5])], JSON_FORCE_OBJECT | JSON_UNESCAPED_UNICODE));
              }
            }
          } else if(isset($this->data[5])) {
            if($this->data[5] == '*') {
              $this->log('PRINT '.json_encode($this->db, JSON_FORCE_OBJECT | JSON_UNESCAPED_UNICODE));
              $this->log('PRINT '.json_encode($this->tmp, JSON_FORCE_OBJECT | JSON_UNESCAPED_UNICODE));
            } else {
              if(isset($this->db[strtolower($this->data[5])])) {
                $this->log('PRINT '.json_encode($this->db[strtolower($this->data[5])], JSON_FORCE_OBJECT | JSON_UNESCAPED_UNICODE));
              }
              if(isset($this->tmp[strtolower($this->data[5])])) {
                $this->log('PRINT '.json_encode($this->tmp[strtolower($this->data[5])], JSON_FORCE_OBJECT | JSON_UNESCAPED_UNICODE));
              }
            }
          }
          $this->say($this->target, true, '@'.$this->username.' - done.');
        } else {
          if(isset($this->data[6]) && substr($this->data[4], 0, 1) != '@') {
            if(substr($this->data[4], 0, 1) == '#' && isset($this->db[strtolower($this->data[4])])) {
              if($this->data[5] == 'set') {
                $config = split("=",$this->data[6]);
                $stmt = $this->db()->select('CONFIG', "BOTID = ".$this->config['ID']." and CHANNELID=".$this->db[$this->data[4]]['ID']." and NAME ='".strtoupper($config[0])."'");
                if($stmt->rowCount() > 0) {
                  $this->db()->update('CONFIG', array('VALUE' => $config[1], 'UPDATEBY' => strtolower($this->username)),"BOTID = ".$this->config['ID']." and CHANNELID=".$this->db[strtolower($this->data[4])]['ID']." and NAME ='".strtoupper($config[0])."'");
                } else {
                  $this->db()->insert('CONFIG', array('BOTID' => $this->config['ID'], 'CHANNELID' => $this->db[strtolower($this->data[4])]['ID'], 'NAME' => strtoupper($config[0]), 'VALUE' => $config[1], 'INSERTBY' => strtolower($this->username)));
                }
                $this->say($this->target, true, '@'.$this->username." - Key '".strtolower($config[0])."' set to '".$config[1]."' for ".strtolower($this->data[4]));
                $this->reinit(strtolower($this->data[4]));
              } else if($this->data[5] == 'delete') {
                $stmt = $this->db()->select('CONFIG', "BOTID = ".$this->config['ID']." and CHANNELID=".$this->db[$this->data[4]]['ID']." and NAME ='".strtoupper($this->data[6])."'");
                if($stmt->rowCount() > 0) {
                  $this->db()->delete('CONFIG', "BOTID = ".$this->config['ID']." and CHANNELID=".$this->db[strtolower($this->data[4])]['ID']." and NAME ='".strtoupper($this->data[6])."'");
                  $this->say($this->target, true, '@'.$this->username." - Key '".strtolower($this->data[6])."' deleted for ".strtolower($this->data[4]));
                  $this->reinit(strtolower($this->data[4]));
                } 
              }
            } else {
              if(isset($this->db[$this->target]['@plugins'][$this->data[4]])) {
                if($this->data[5] == 'set') {
                  $config = split("=",$this->data[6]);
                  $stmt = $this->db()->select('CONFIG', "BOTID = ".$this->config['ID']." and CHANNELID=".$this->db[$this->target]['ID']." and PLUGINID = ".$this->db[$this->target]['@plugins'][$this->data[4]]['ID']." and NAME ='".strtoupper($config[0])."'");
                  if($stmt->rowCount() > 0) {
                    $this->db()->update('CONFIG', array('VALUE' => $config[1], 'UPDATEBY' => strtolower($this->username)),"BOTID = ".$this->config['ID']." and CHANNELID=".$this->db[$this->target]['ID']." and PLUGINID = ".$this->db[$this->target]['@plugins'][$this->data[4]]['ID']." and NAME ='".strtoupper($config[0])."'");
                  } else {
                    $this->db()->insert('CONFIG', array('BOTID' => $this->config['ID'], 'CHANNELID' => $this->db[$this->target]['ID'], 'PLUGINID' => $this->db[$this->target]['@plugins'][$this->data[4]]['ID'], 'NAME' => strtoupper($config[0]), 'VALUE' => $config[1], 'INSERTBY' => strtolower($this->username)));
                  }
                  $this->say($this->target, true, '@'.$this->username." - Key '".strtolower($config[0])."' set to '".$config[1]."' for ".$this->data[4].' in '.$this->target);
                  $this->reinit();
                } else if($this->data[5] == 'delete') {
                  $stmt = $this->db()->select('CONFIG', "BOTID = ".$this->config['ID']." and CHANNELID=".$this->db[$this->target]['ID']." and PLUGINID = ".$this->db[$this->target]['@plugins'][$this->data[4]]['ID']." and NAME ='".strtoupper($this->data[6])."'");
                  if($stmt->rowCount() > 0) {
                    $this->db()->delete('CONFIG', "BOTID = ".$this->config['ID']." and CHANNELID=".$this->db[$this->target]['ID']." and PLUGINID = ".$this->db[$this->target]['@plugins'][$this->data[4]]['ID']." and NAME ='".strtoupper($this->data[6])."'");
                    $this->say($this->target, true, '@'.$this->username." - Key '".strtolower($this->data[6])."' deleted for ".$this->data[4].' in '.$this->target);
                    $this->reinit();
                  }
                } else {
                  if(isset($this->data[7]) && substr($this->data[7], 0, 1) != '@') {
                    if(substr($this->data[5], 0, 1) == '#' && isset($this->db[strtolower($this->data[5])])) {
                      if($this->data[6] == 'set') {
                        $config = split("=",$this->data[7]);
                        $stmt = $this->db()->select('CONFIG', "BOTID = ".$this->config['ID']." and CHANNELID=".$this->db[$this->data[5]]['ID']." and PLUGINID = ".$this->db[strtolower($this->data[5])]['@plugins'][$this->data[4]]['ID']." and NAME ='".strtoupper($config[0])."'");
                        if($stmt->rowCount() > 0) {
                          $this->db()->update('CONFIG', array('VALUE' => $config[1], 'UPDATEBY' => strtolower($this->username)),"BOTID = ".$this->config['ID']." and CHANNELID=".$this->db[strtolower($this->data[5])]['ID']." and PLUGINID = ".$this->db[strtolower($this->data[5])]['@plugins'][$this->data[4]]['ID']." and NAME ='".strtoupper($config[0])."'");
                        } else {
                          $this->db()->insert('CONFIG', array('BOTID' => $this->config['ID'], 'CHANNELID' => $this->db[strtolower($this->data[5])]['ID'], 'PLUGINID' => $this->db[strtolower($this->data[5])]['@plugins'][$this->data[4]]['ID'], 'NAME' => strtoupper($config[0]), 'VALUE' => $config[1], 'INSERTBY' => strtolower($this->username)));
                        }
                        $this->say($this->target, true, '@'.$this->username." - Key '".strtolower($config[0])."' set to '".$config[1]."' for ".$this->data[4]." in ".strtolower($this->data[5]));
                        $this->reinit(strtolower($this->data[5]));
                      } else if($this->data[6] == 'delete') {
                        $stmt = $this->db()->select('CONFIG', "BOTID = ".$this->config['ID']." and CHANNELID=".$this->db[$this->data[5]]['ID']." and PLUGINID = ".$this->db[strtolower($this->data[5])]['@plugins'][$this->data[4]]['ID']." and NAME ='".strtoupper($this->data[7])."'");
                        if($stmt->rowCount() > 0) {
                          $this->db()->delete('CONFIG', "BOTID = ".$this->config['ID']." and CHANNELID=".$this->db[strtolower($this->data[5])]['ID']." and PLUGINID = ".$this->db[strtolower($this->data[5])]['@plugins'][$this->data[4]]['ID']." and NAME ='".strtoupper($this->data[7])."'");
                          $this->say($this->target, true, '@'.$this->username." - Key '".strtolower($this->data[6])."' deleted for ".$this->data[4]." in ".strtolower($this->data[5]));
                          $this->reinit(strtolower($this->data[5]));
                        }
                      }
                    }
                  }
                }
              }
            }
          }
        }
      } else {
        if(isset($this->data[4])) {
          if($this->data[4] == 'reinit') {
            $this->reinit();
            $this->say($this->target, true, '@'.$this->username.' - Reloaded configuration.');
          }
        } else {
          $this->say($this->target, true, '@'.$this->username.' - You are my master! <3');
          print_r($this->db);
          print_r($this->tmp);
        }
      }
    }
  }
  
?>