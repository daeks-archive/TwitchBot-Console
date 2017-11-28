<?php

  $cmd = array('id' => 'plugin.filters.filters',
               'level' => 'broadcaster owner',
               'help' => 'Filters Configuration',
               'syntax' => '$filters [add|edit|delete] <filter> (<setting> <value>)');
  
  if($execute) {
    if($this->access($cmd['level'])) {
      if(isset($this->data[7])) {
        if($this->data[4] == 'edit') {
          $modes = explode(' ', $this->db[$this->target]['config']['@plugins']['filters']['mode']);
          if(($mode = array_search(strtoupper($this->data[5]), $modes)) !== false) {
            $settings = explode(' ', $this->db[$this->target]['config']['@plugins']['filters']['settings']);
            if(($setting = array_search(strtoupper($this->data[6]), $settings)) !== false) {
              $submessage = '';
              for($i=7;$i<sizeof($this->data);$i++) {
                $submessage .= $this->data[$i].' ';
              }
            
              if($this->db[$this->target]['ID'] > 0) {
                $stmt = $this->db()->select('CONFIG', "BOTID=".$this->config['ID']." and CHANNELID=".$this->db[$this->target]['ID']." and PLUGINID=".$this->db[$this->target]['@plugins']['filters']['ID']." and NAME='".strtoupper($this->data[6])."'");
                if($stmt->rowCount() > 0) {
                  $config = $stmt->fetch();
                  $this->db()->update('CONFIG', array('VALUE' => $submessage, 'UPDATEBY' => strtolower($this->username)),"ID=".$config['ID']);
                  $this->say($this->target, true, '@'.$this->username.' filter config changed');
                } else {
                  $this->db()->insert('CONFIG', array('BOTID' => $this->config['ID'], 'CHANNELID' => $this->db[$this->target]['ID'], 'PLUGINID' => $this->db[$this->target]['@plugins']['filters']['ID'], 'NAME' => strtoupper($this->data[6]), 'VALUE' => $submessage, 'INSERTBY' => strtolower($this->username)));
                  $this->say($this->target, true, '@'.$this->username.' filter config added');
                }
              } else {
                $stmt = $this->db()->select('CONFIG', "BOTID=".$this->config['ID']." and CHANNELID=0 and PLUGINID=".$this->db[$this->target]['@plugins']['filters']['ID']." and NAME='".strtoupper($this->data[6])."'");
                if($stmt->rowCount() > 0) {
                  $config = $stmt->fetch();
                  $this->db()->update('CONFIG', array('VALUE' => $submessage, 'UPDATEBY' => strtolower($this->username)),"ID=".$config['ID']);
                  $this->say($this->target, true, '@'.$this->username.' filter config changed');
                } else {
                  $this->db()->insert('CONFIG', array('BOTID' => $this->config['ID'], 'PLUGINID' => $this->db[$this->target]['@plugins']['filters']['ID'], 'NAME' => strtoupper($this->data[6]), 'VALUE' => $submessage, 'INSERTBY' => strtolower($this->username)));
                  $this->say($this->target, true, '@'.$this->username.' filter config added');
                }
              }
            } else {
              $this->say($this->target, true, '@'.strtolower($this->username).' filter config does not exist - Valid filter configs: '.$this->db[$this->target]['config']['@plugins']['filters']['settings']);
            }
          } else {
            $this->say($this->target, true, '@'.$this->username.' filter does not exist');
          }
        }
      } else if(isset($this->data[6])) {
        if($this->data[4] == 'edit') {
          $modes = explode(' ', $this->db[$this->target]['config']['@plugins']['filters']['mode']);
          if(($key = array_search(strtoupper($this->data[5]), $modes)) !== false) {
            if($this->db[$this->target]['ID'] > 0) {
              $stmt = $this->db()->select('CONFIG', "BOTID=".$this->config['ID']." and CHANNELID=".$this->db[$this->target]['ID']." and PLUGINID=".$this->db[$this->target]['@plugins']['filters']['ID']." and NAME='".strtoupper($this->data[6])."'");
              if($stmt->rowCount() > 0) {
                $config = $stmt->fetch();
                $this->db()->delete('CONFIG', "ID=".$config['ID']);
                $this->say($this->target, true, '@'.$this->username.' filter config deleted');
              }
            } else {
              $stmt = $this->db()->select('CONFIG', "BOTID=".$this->config['ID']." and CHANNELID=0 and PLUGINID=".$this->db[$this->target]['@plugins']['filters']['ID']." and NAME='".strtoupper($this->data[6])."'");
              if($stmt->rowCount() > 0) {
                $config = $stmt->fetch();
                $this->db()->delete('CONFIG', "ID=".$config['ID']);
                $this->say($this->target, true, '@'.$this->username.' filter config deleted');
              }
            }
          } else {
            $this->say($this->target, true, '@'.$this->username.' filter does not exist');
          }
        }
      } else if(isset($this->data[5])) {
        if($this->data[4] == 'add') {
          $enabled = explode(' ', $this->db[$this->target]['config']['@plugins']['filters']['modes']);
          if(($key = array_search(strtoupper($this->data[5]), $enabled)) !== false) {
            $modes = explode(' ', $this->db[$this->target]['config']['@plugins']['filters']['mode']);
            if(($key = array_search(strtoupper($this->data[5]), $modes)) !== false) {
              $this->say($this->target, true, '@'.$this->username.' filter already exists');
            } else {
              array_push($modes, strtoupper($this->data[5]));
              $mode = trim(implode(' ', $modes));
              if($this->db[$this->target]['ID'] > 0) {
                $stmt = $this->db()->select('CONFIG', "BOTID=".$this->config['ID']." and CHANNELID=".$this->db[$this->target]['ID']." and PLUGINID=".$this->db[$this->target]['@plugins']['filters']['ID']." and NAME='MODE'");
                if($stmt->rowCount() > 0) {
                  $config = $stmt->fetch();
                  $this->db()->update('CONFIG', array('VALUE' => $mode, 'UPDATEBY' => strtolower($this->username)), "ID=".$config['ID']);
                } else {
                  $this->db()->insert('CONFIG', array('BOTID' => $this->config['ID'], 'CHANNELID' => $this->db[$this->target]['ID'], 'PLUGINID' => $this->db[$this->target]['@plugins']['filters']['ID'], 'NAME' => 'MODE', 'VALUE' => $mode, 'INSERTBY' => strtolower($this->username)));
                }
                $this->reinit($this->target);
                $this->say($this->target, true, '@'.$this->username.' filter added for '.$this->target);
              } else {
                $stmt = $this->db()->select('CONFIG', "BOTID=".$this->config['ID']." and CHANNELID=0 and PLUGINID=".$this->db[$this->target]['@plugins']['filters']['ID']." and NAME='MODE'");
                if($stmt->rowCount() > 0) {
                  $config = $stmt->fetch();
                  $this->db()->update('CONFIG', array('VALUE' => $mode, 'UPDATEBY' => strtolower($this->username)),"ID=".$config['ID']);
                } else {
                  $this->db()->insert('CONFIG', array('BOTID' => $this->config['ID'], 'PLUGINID' => $this->db[$this->target]['@plugins']['filters']['ID'], 'NAME' => 'MODE', 'VALUE' => $mode, 'INSERTBY' => strtolower($this->username)));
                }
                $this->reinit();
                $this->say($this->target, true, '@'.$this->username.' filter added for all channels');
              }
            }
          } else {
            $this->say($this->target, true, '@'.$this->username.' invalid filter - Valid filters: '.implode(' ', array_diff(explode(' ', $this->db[$this->target]['config']['@plugins']['filters']['modes']), explode(' ', $this->db[$this->target]['config']['@plugins']['filters']['mode']))));
          }
        } else if($this->data[4] == 'delete') {
          $modes = explode(' ', $this->db[$this->target]['config']['@plugins']['filters']['mode']);
          if(($key = array_search(strtoupper($this->data[5]), $modes)) !== false) {
            unset($modes[$key]);
            
            $mode = trim(implode(' ', $modes));
            if($this->db[$this->target]['ID'] > 0) {
              $stmt = $this->db()->select('CONFIG', "BOTID=".$this->config['ID']." and CHANNELID=".$this->db[$this->target]['ID']." and PLUGINID=".$this->db[$this->target]['@plugins']['filters']['ID']." and NAME='MODE'");
              if($stmt->rowCount() > 0) {
                $config = $stmt->fetch();
                $this->db()->update('CONFIG', array('VALUE' => $mode, 'UPDATEBY' => strtolower($this->username)),"ID=".$config['ID']);
              } else {
                $this->db()->insert('CONFIG', array('BOTID' => $this->config['ID'], 'CHANNELID' => $this->db[$this->target]['ID'], 'PLUGINID' => $this->db[$this->target]['@plugins']['filters']['ID'], 'NAME' => 'MODE', 'VALUE' => $mode, 'INSERTBY' => strtolower($this->username)));
              }
              $this->reinit($this->target);
              $this->say($this->target, true, '@'.$this->username.' filter edited for '.$this->target);
            } else {
              $stmt = $this->db()->select('CONFIG', "BOTID=".$this->config['ID']." and CHANNELID=0 and PLUGINID=".$this->db[$this->target]['@plugins']['filters']['ID']." and NAME='MODE'");
              if($stmt->rowCount() > 0) {
                $config = $stmt->fetch();
                $this->db()->update('CONFIG', array('VALUE' => $mode, 'UPDATEBY' => strtolower($this->username)),"ID=".$config['ID']);
              } else {
                $this->db()->insert('CONFIG', array('BOTID' => $this->config['ID'], 'PLUGINID' => $this->db[$this->target]['@plugins']['filters']['ID'], 'NAME' => 'MODE', 'VALUE' => $mode, 'INSERTBY' => strtolower($this->username)));
              }
              $this->reinit();
              $this->say($this->target, true, '@'.$this->username.' filter edited for all channels');
            }
          } else {
            $this->say($this->target, true, '@'.$this->username.' filter does not exist');
          }
        }
      } else if(isset($this->data[4])) {
        if($this->data[4] == 'add') {
          $this->say($this->target, true, '@'.$this->username.' Valid filters: '.implode(' ', array_diff(explode(' ', $this->db[$this->target]['config']['@plugins']['filters']['modes']), explode(' ', $this->db[$this->target]['config']['@plugins']['filters']['mode']))));
        }
      } else {
        $modes = explode(' ', $this->db[$this->target]['config']['@plugins']['filters']['mode']);
        if(sizeof($modes) > 0) {
          if ($this->db[$this->target]['ID'] < 0) {
            $this->say($this->target, true, 'Default loaded filters: '.$this->db[$this->target]['config']['@plugins']['filters']['mode']);
          } else {
            $this->say($this->target, true, 'Loaded filters for '.$this->target.': '.$this->db[$this->target]['config']['@plugins']['filters']['mode']);
          }
        }
      }
    }
  }
  
?>