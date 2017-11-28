<?php

  $cmd = array('id' => 'tb.plugins',
               'level' => 'broadcaster owner',
               'help' => 'Plugin configuration',
               'syntax' => '$plugins ([add|delete] <plugin> | [enable|disable] <*>)');
  
  if($execute) {
    if($this->access($cmd['level'])) {
      if(isset($this->data[5])) {
        if($this->data[4] == 'add') {
          if($this->db[$this->target]['ID'] > 0) {
            if(!isset($this->db[$this->target]['@plugins'][strtolower($this->data[5])])) {
              if(!isset($this->db[$this->target][strtolower($this->data[5])])) {
                if(file_exists(PLUGINS_PATH.DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR.strtolower($this->data[5]).'.php') || file_exists(PLUGINS_PATH.DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR.strtolower($this->data[5]))) {
                  $this->db()->insert('PLUGINS', array('BOTID' => $this->config['ID'], 'CHANNELID' => $this->db[$this->target]['ID'], 'NAME' => strtolower($this->data[5]), 'INSERTBY' => strtolower($this->username)));
                  $this->say($this->target, true, '@'.$this->username.' added PLUGIN '.$this->data[5].' for '.$this->target);
                  $this->reinit($this->target);
                } else {
                  $plugins = array();
                  foreach (scandir(PLUGINS_PATH) as $key => $value) { 
                    if (!in_array($value,array(".",".."))) { 
                      if(is_dir(PLUGINS_PATH.DIRECTORY_SEPARATOR.$value)) {
                        if(!in_array($value, $plugins) && !isset($this->db[$this->target]['@plugins'][$value])) {
                          array_push($plugins, $value);
                        }
                      } else {
                        if(!in_array(str_replace('.php', '', $value), $plugins) && !isset($this->db[$this->target]['@plugins'][str_replace('.php', '', $value)])) {
                          array_push($plugins, str_replace('.php', '', $value));
                        }
                      }           
                    }
                  }
                  $this->say($this->target, true, '@'.$this->username.' invalid PLUGIN - Valid Plugins: '.implode(' ', $plugins));
                }
              } else {
                $this->say($this->target, true, '@'.$this->username.' invalid PLUGIN - System Variable');
              }
            }
          } else {
            if(!isset($this->db['#'.strtolower($this->config['NAME'])]['@plugins'][strtolower($this->data[5])])) {
              if(!isset($this->db[$this->target][strtolower($this->data[5])])) {
                if(file_exists(PLUGINS_PATH.DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR.strtolower($this->data[5]).'.php') || file_exists(PLUGINS_PATH.DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR.strtolower($this->data[5]))) {
                  $this->db()->insert('PLUGINS', array('BOTID' => $this->config['ID'], 'NAME' => strtolower($this->data[5]), 'INSERTBY' => strtolower($this->username)));
                  $this->say($this->target, true, '@'.$this->username.' added PLUGIN '.$this->data[5]);
                  $this->reinit();
                } else {
                  $plugins = array();
                  foreach (scandir(PLUGINS_PATH) as $key => $value) { 
                    if (!in_array($value,array(".",".."))) { 
                      if(is_dir(PLUGINS_PATH.DIRECTORY_SEPARATOR.$value)) {
                        if(!in_array($value, $plugins) && !isset($this->db['#'.strtolower($this->config['NAME'])]['@plugins'][$value])) {
                          array_push($plugins, $value);
                        }
                      } else {
                        if(!in_array(str_replace('.php', '', $value), $plugins) && !isset($this->db['#'.strtolower($this->config['NAME'])]['@plugins'][str_replace('.php', '', $value)])) {
                          array_push($plugins, str_replace('.php', '', $value));
                        }
                      }           
                    }
                  }
                  $this->say($this->target, true, '@'.$this->username.' invalid PLUGIN - Available Plugins: '.implode(' ', $plugins));
                }
              } else {
                $this->say($this->target, true, '@'.$this->username.' invalid PLUGIN - System Variable');
              }
            }
          }
        } else if($this->data[4] == 'enable') {
          if($this->data[5] == '*') {
            if($this->db[$this->target]['ID'] > 0) {            
              $stmt = $this->db()->select('PLUGINS', "BOTID=".$this->config['ID']." and CHANNELID=".$this->db[$this->target]['ID']);
              if($stmt->rowCount() > 0) {
                foreach($stmt->fetchAll() as $plugin) {
                  $stmt2 = $this->db()->select('PLUGINS', "BOTID=".$this->config['ID']." and CHANNELID=0 and ENABLED=1 and NAME='".$plugin['NAME']."'");
                  if($stmt2->rowCount() > 0) {
                    $this->db()->delete('PLUGINS', 'ID='.$plugin['ID']);
                  } else {
                    $this->db()->update('PLUGINS', array('ENABLED' => 1, 'UPDATEBY' => strtolower($this->username)), "ID=".$plugin['ID']);
                  }
                }
              }                
              //$this->db()->update('PLUGINS', array('ENABLED' => 1, 'UPDATEBY' => strtolower($this->username)), "BOTID=".$this->config['ID']." and CHANNELID=".$this->db[$this->target]['ID']);
              $this->say($this->target, true, '@'.$this->username.' all added plugins enabled for channel '.$this->target);
              $this->reinit($this->target);
            } else {
              $this->db()->update('PLUGINS', array('ENABLED' => 1), "BOTID=".$this->config['ID']." and CHANNELID=0");
              $this->say($this->target, true, '@'.$this->username.' all added plugins enabled for BOT');
              $this->reinit();
            }
          }
        } else if ($this->data[4] == 'delete') {
          if($this->db[$this->target]['ID'] > 0) {
            if(isset($this->db[$this->target]['@plugins'][strtolower($this->data[5])])) {
              $this->db()->delete('PLUGINS', "BOTID=".$this->config['ID']." and CHANNELID=".$this->db[$this->target]['ID']." and NAME='".strtolower($this->data[5])."'");
              $this->say($this->target, true, '@'.$this->username.' deleted PLUGIN '.$this->data[5].' for '.$this->target);
              $this->reinit($this->target);
            }
          } else {
            if(isset($this->db['#'.strtolower($this->config['NAME'])]['@plugins'][strtolower($this->data[5])])) {
              $this->db()->delete('PLUGINS', "BOTID=".$this->config['ID']." and CHANNELID=0 and NAME='".strtolower($this->data[5])."'");
              $this->say($this->target, true, '@'.$this->username.' deleted PLUGIN '.$this->data[5]);
              $this->reinit();
            }
          }
        } else if ($this->data[4] == 'disable') {
          if($this->data[5] == '*') {
            if($this->db[$this->target]['ID'] > 0) {
              foreach($this->db[$this->target]['@plugins'] as $pluginname => $plugin) {
                $stmt = $this->db()->select('PLUGINS', "BOTID=".$this->config['ID']." and CHANNELID=".$this->db[$this->target]['ID']." and NAME='".$pluginname."'");
                if($stmt->rowCount() > 0) {
                  $subplugin = $stmt->fetch();
                  $this->db()->update('PLUGINS', array('ENABLED' => 0, 'UPDATEBY' => strtolower($this->username)), "ID=".$subplugin['ID']);
                } else {
                  $this->db()->insert('PLUGINS', array('BOTID' => $this->config['ID'], 'CHANNELID' => $this->db[$this->target]['ID'], 'NAME' => $pluginname, 'ENABLED' => 0, 'INSERTBY' => strtolower($this->username)));
                }
              }
              //$this->db()->update('PLUGINS', array('ENABLED' => 0, 'UPDATEBY' => strtolower($this->username)), "BOTID=".$this->config['ID']." and CHANNELID=".$this->db[$this->target]['ID']);
              $this->say($this->target, true, '@'.$this->username.' all added plugins disabled for channel '.$this->target);
              $this->reinit($this->target);
            } else {
              $this->db()->update('PLUGINS', array('ENABLED' => 0, 'UPDATEBY' => strtolower($this->username)), "BOTID=".$this->config['ID']." and CHANNELID=0");
              $this->say($this->target, true, '@'.$this->username.' all added plugins disabled for BOT');
              $this->reinit();
            }
          }
        }
      } else {
        $output = '';
        foreach($this->db[$this->target]['@plugins'] as $name => $plugin) {
          if(file_exists(PLUGINS_PATH.DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR.$name.'.php') || file_exists(PLUGINS_PATH.DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR.$name)) {
            $output .= $name.', ';
          }
        }
        if ($this->db[$this->target]['ID'] < 0) {
          $this->say($this->target, true, 'Default loaded plugins: '.substr($output, 0, -2));
        } else {
          $this->say($this->target, true, 'Loaded plugins for '.$this->target.': '.substr($output, 0, -2));
        }
      }
    }
  }
  
?>