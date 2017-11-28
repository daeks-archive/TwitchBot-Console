<?php

  $cmd = array('id' => 'plugin.timers.timers',
               'level' => 'broadcaster owner',
               'help' => 'Timers Configuration',
               'syntax' => '$timers (add|edit|config|delete|alias) <timer> (<value>|<setting> <value>)');
  
  if($execute) {
    if($this->access($cmd['level'])) {
      if(isset($this->data[6])) {
        $name = strtolower($this->data[5]);
        if($this->data[4] == 'add') {
          if(!isset($this->db[$this->target]['timers'][$name])) {
            $submessage = '';
            for($i=6;$i<sizeof($this->data);$i++) {
              $submessage .= $this->data[$i].' ';
            }
          
            if($this->db[$this->target]['ID'] > 0) {
              $stmt = $this->db()->insert('PLUGIN_TIMERS', array('BOTID' => $this->config['ID'], 'CHANNELID' => $this->db[$this->target]['ID'], 'NAME' => $name, 'VALUE' => $submessage, 'MODE' => $this->db[$this->target]['config']['@plugins']['timers']['mode'], 'SCHEDULE' => $this->db[$this->target]['config']['@plugins']['timers']['interval'], 'INSERTBY' => strtolower($this->username)));
              $this->reinit($this->target);
              $this->say($this->target, true, '@'.$this->username.' timer added for '.$this->target.' with interval '.$this->db[$this->target]['config']['@plugins']['timers']['interval'].' seconds and mode '.$this->db[$this->target]['config']['@plugins']['timers']['mode']);
            } else {
              $stmt = $this->db()->insert('PLUGIN_TIMERS', array('BOTID' => $this->config['ID'], 'NAME' => $name, 'VALUE' => $submessage, 'MODE' => $this->db[$this->target]['config']['@plugins']['timers']['mode'], 'SCHEDULE' => $this->db[$this->target]['config']['@plugins']['timers']['interval'], 'INSERTBY' => strtolower($this->username)));
              $this->reinit();
              $this->say($this->target, true, '@'.$this->username.' timer added for all channels with interval '.$this->db[$this->target]['config']['@plugins']['timers']['interval'].' seconds and mode '.$this->db[$this->target]['config']['@plugins']['timers']['mode']);
            }
          } else {
            $this->say($this->target, true, '@'.$this->username.' timer already exists');
          }
        } else if($this->data[4] == 'edit') {
          if(isset($this->db[$this->target]['timers'][$name])) {
            $submessage = '';
            for($i=6;$i<sizeof($this->data);$i++) {
              $submessage .= $this->data[$i].' ';
            }
          
            if($this->db[$this->target]['ID'] > 0) {
              $stmt = $this->db()->update('PLUGIN_TIMERS', array('VALUE' => $submessage, 'UPDATEBY' => strtolower($this->username)), "BOTID=".$this->config['ID']." and CHANNELID=".$this->db[$this->target]['ID']." and NAME='".$name."'");
              $this->reinit($this->target);
              $this->say($this->target, true, '@'.$this->username.' timer edited for '.$this->target);
            } else {
              $stmt = $this->db()->update('PLUGIN_TIMERS', array('VALUE' => $submessage, 'UPDATEBY' => strtolower($this->username)), "BOTID=".$this->config['ID']." and CHANNELID=0 and NAME='".$name."'");
              $this->reinit();
              $this->say($this->target, true, '@'.$this->username.' timer edited for all channels');
            }
          } else {
            $this->say($this->target, true, '@'.$this->username.' timer does not exist');
          }
        } else if ($this->data[4] == 'config') {
          if(isset($this->data[7])) {
            $name = strtolower($this->data[5]);
            if(isset($this->db[$this->target]['timers'][$name])) {
              $options = array('MODE' => 'MODE', 'INTERVAL' => 'SCHEDULE', 'SCHEDULE' => 'SCHEDULE');
              if(isset($options[strtoupper($this->data[6])])) {
                if($this->db[$this->target]['ID'] > 0) {
                  $stmt = $this->db()->update('PLUGIN_TIMERS', array($options[strtoupper($this->data[6])] => strtoupper($this->data[7]), 'UPDATEBY' => strtolower($this->username)), "BOTID=".$this->config['ID']." and CHANNELID=".$this->db[$this->target]['ID']." and NAME='".$name."'");
                  $this->reinit($this->target);
                  $this->say($this->target, true, '@'.$this->username.' timer config set for '.$this->target);
                } else {
                  $stmt = $this->db()->update('PLUGIN_TIMERS', array($options[strtoupper($this->data[6])] => strtoupper($this->data[7]), 'UPDATEBY' => strtolower($this->username)), "BOTID=".$this->config['ID']." and CHANNELID=0 and NAME='".$name."'");
                  $this->reinit();
                  $this->say($this->target, true, '@'.$this->username.' timer config set for all channels');
                }
              } else {
                $this->say($this->target, true, '@'.$this->username.' config option invalid - Valid options: '.implode(' ', array_keys($options)));
              }
            } else {
              $this->say($this->target, true, '@'.$this->username.' timer does not exist');
            }
          }
        } else if ($this->data[4] == 'alias') {
          $name = strtolower($this->data[5]);
          if(isset($this->db[$this->target]['timers'][$name])) {
            $trigger = explode(' ', $this->db[$this->target]['config']['trigger']);
            if(in_array(substr($this->data[6], 0, 1), $trigger)) {
              if($this->db[$this->target]['ID'] > 0) {
                $stmt = $this->db()->update('PLUGIN_TIMERS', array('ALIAS' => strtolower($this->data[6]), 'UPDATEBY' => strtolower($this->username)), "BOTID=".$this->config['ID']." and CHANNELID=".$this->db[$this->target]['ID']." and NAME='".$name."'");
                $this->reinit($this->target);
                $this->say($this->target, true, '@'.$this->username.' timer alias set for '.$this->target);
              } else {
                $stmt = $this->db()->update('PLUGIN_TIMERS', array('ALIAS' => strtolower($this->data[6]), 'UPDATEBY' => strtolower($this->username)), "BOTID=".$this->config['ID']." and CHANNELID=0 and NAME='".$name."'");
                $this->reinit();
                $this->say($this->target, true, '@'.$this->username.' timer alias set for all channels');
              }
            }
          }
        }
      } else if(isset($this->data[5])) {
        if($this->data[4] == 'delete') {
          $name = strtolower($this->data[5]);
          if(isset($this->db[$this->target]['timers'][$name])) {
            if($this->db[$this->target]['ID'] > 0) {
              $stmt = $this->db()->delete('PLUGIN_TIMERS', "BOTID=".$this->config['ID']." and CHANNELID=".$this->db[$this->target]['ID']." and NAME='".$name."'");
              $this->reinit($this->target);
              $this->say($this->target, true, '@'.$this->username.' timer deleted for '.$this->target);
            } else {
              $stmt = $this->db()->delete('PLUGIN_TIMERS', "BOTID=".$this->config['ID']." and CHANNELID=0 and NAME='".$name."'");
              $this->reinit();
              $this->say($this->target, true, '@'.$this->username.' timer deleted for all channels');
            }
          }
        } else if($this->data[4] == 'config') {
          $name = strtolower($this->data[5]);
          if(isset($this->db[$this->target]['timers'][$name])) {
              $this->say($this->target, true, '@'.$this->username.' config for '.$name.': mode='.$this->db[$this->target]['timers'][$name]['MODE'].', interval='.$this->db[$this->target]['timers'][$name]['SCHEDULE']);
          } else {
            $this->say($this->target, true, '@'.$this->username.' timer does not exist');
          }
        } else if ($this->data[4] == 'alias') {
          $name = strtolower($this->data[5]);
          if(isset($this->db[$this->target]['timers'][$name])) {
            if($this->db[$this->target]['ID'] > 0) {
              $stmt = $this->db()->update('PLUGIN_TIMERS', array('ALIAS' => null, 'UPDATEBY' => strtolower($this->username)), "BOTID=".$this->config['ID']." and CHANNELID=".$this->db[$this->target]['ID']." and NAME='".$name."'");
              $this->reinit($this->target);
              $this->say($this->target, true, '@'.$this->username.' timer alias removed for '.$this->target);
            } else {
              $stmt = $this->db()->update('PLUGIN_TIMERS', array('ALIAS' => null, 'UPDATEBY' => strtolower($this->username)), "BOTID=".$this->config['ID']." and CHANNELID=0 and NAME='".$name."'");
              $this->reinit();
              $this->say($this->target, true, '@'.$this->username.' timer alias removed for all channels');
            }
          }
        }
      } else {
        if(isset($this->data[4])) {
         if(isset($this->db[$this->target]['timers'][$this->data[4]])) {
            $this->say($this->target, true, $this->db[$this->target]['timers'][$this->data[4]]['VALUE']);
          }
        } else {
          if(sizeof($this->db[$this->target]['timers']) > 0) {
            if ($this->db[$this->target]['ID'] < 0) {
              $this->say($this->target, true, 'Default loaded timers: '.implode(' ', array_keys($this->db[$this->target]['timers'])));
            } else {
              $this->say($this->target, true, 'Timed messages for '.$this->target.': '.implode(' ', array_keys($this->db[$this->target]['timers'])));
            }
          } else {
            $this->say($this->target, true, 'No timed messages defined for '.$this->target);
          }
        }
      }
    }
  }
  
?>