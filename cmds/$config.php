<?php

  $cmd = array('id' => 'tb.config',
               'level' => 'broadcaster owner',
               'help' => 'Channel configuration',
               'syntax' => '$config [enable|disable] (*|<plugin>|<channel>|autostart|mute)');
  
  if($execute) {
    if($this->access($cmd['level'])) {
      if(isset($this->data[5])) {
        if($this->data[4] == 'enable') {
          if($this->data[5] == 'autostart' && $this->owner()) {
            $this->db()->update('BOTS', array(strtoupper($this->data[5]) => 1, 'UPDATEBY' => strtolower($this->username)), "ID=".$this->config['ID']);
            $this->say($this->target, true, '@'.$this->username.' enabled '.strtoupper($this->data[5]));
          } else if($this->data[5] == 'mute') {
            if($this->db[$this->target]['ID'] > 0) {
              $this->db()->insert('CONFIG', array('BOTID' => $this->config['ID'], 'CHANNELID' => $this->db[$this->target]['ID'], 'NAME' => 'MUTE', 'VALUE' => 1, 'INSERTBY' => strtolower($this->username)));
              $this->say($this->target, true, '@'.$this->username.' channel '.$this->target.' muted');
              $this->reinit($this->target);
            } else {
              $this->db()->insert('CONFIG', array('BOTID' => $this->config['ID'], 'NAME' => 'MUTE', 'VALUE' => 1, 'INSERTBY' => strtolower($this->username)));
              $this->say($this->target, true, '@'.$this->username.' bot muted');
              $this->reinit();
            }
          } else if($this->data[5] == '*') {
            if($this->db[$this->target]['ID'] > 0) {
              $this->db()->delete('CONFIG', "BOTID = ".$this->config['ID']." and CHANNELID=".$this->db[$this->target]['ID']." and NAME = 'SILENT'");
              $this->db()->update('PLUGINS', array('ENABLED' => 1, 'UPDATEBY' => strtolower($this->username)), "BOTID=".$this->config['ID']." and CHANNELID=".$this->db[$this->target]['ID']);
              $this->say($this->target, true, '@'.$this->username.' everything enabled for channel '.$this->target);
              $this->reinit($this->target);
            } else {
              $this->db()->delete('CONFIG', "BOTID = ".$this->config['ID']." and CHANNELID=0 and NAME = 'SILENT'");
              $this->db()->update('PLUGINS', array('ENABLED' => 1, 'UPDATEBY' => strtolower($this->username)), "BOTID=".$this->config['ID']." and CHANNELID=0");
              $this->say($this->target, true, '@'.$this->username.' everything enabled for BOT');
              $this->reinit();
            }
          } else if(substr($this->data[5], 0, 1) == '#' && $this->owner() && strtolower($this->data[5]) != '#'.strtolower($config['NAME'])) {
            $stmt = $this->db()->select('CHANNELS', "BOTID=".$this->config['ID']." and ENABLED=0 and NAME='".strtolower($this->data[5])."'");
            if($stmt->rowCount() > 0) {
              $channel = $stmt->fetch();
              $this->db()->update('CHANNELS', array('ENABLED' => 1, 'UPDATEBY' => strtolower($this->username)), "BOTID=".$this->config['ID']." and ID=".$channel['ID']);
              if(!isset($this->db[$channel['NAME']])) {
                $this->load($channel['ID'], $channel['NAME']);
              }
            }
          } else {
            if($this->db[$this->target]['ID'] > 0) {
              if(!isset($this->db[$this->target]['plugins'][strtolower($this->data[5])])) {
                if(file_exists(PLUGINS_PATH.DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR.strtolower($this->data[5]).'.php') || file_exists(PLUGINS_PATH.DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR.strtolower($this->data[5]))) {
                  $this->db()->insert('PLUGINS', array('BOTID' => $this->config['ID'], 'CHANNELID' => $this->db[$this->target]['ID'], 'NAME' => strtolower($this->data[5]), 'INSERTBY' => strtolower($this->username)));
                  $this->say($this->target, true, '@'.$this->username.' enabled PLUGIN '.$this->data[5].' for '.$this->target);
                  $this->reinit($this->target);
                }
              }
            } else {
              if(!isset($this->db['#'.strtolower($this->config['NAME'])]['plugins'][strtolower($this->data[5])])) {
                if(file_exists(PLUGINS_PATH.DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR.strtolower($this->data[5]).'.php') || file_exists(PLUGINS_PATH.DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR.strtolower($this->data[5]))) {
                  $this->db()->insert('PLUGINS', array('BOTID' => $this->config['ID'], 'NAME' => strtolower($this->data[5]), 'INSERTBY' => strtolower($this->username)));
                  $this->say($this->target, true, '@'.$this->username.' enabled PLUGIN '.$this->data[5]);
                  $this->reinit();
                }
              }
            }
          }
        } else if ($this->data[4] == 'disable') {
          if($this->data[5] == 'autostart' && $this->owner()) {
            $this->db()->update('BOTS', array(strtoupper($this->data[5]) => 0, 'UPDATEBY' => strtolower($this->username)), "ID=".$this->config['ID']);
            $this->say($this->target, true, '@'.$this->username.' disabled '.strtoupper($this->data[5]));
          } else if($this->data[5] == 'mute') {
            if($this->db[$this->target]['ID'] > 0) {
              $this->db()->delete('CONFIG', "BOTID = ".$this->config['ID']." and CHANNELID=".$this->db[$this->target]['ID']." and NAME = 'MUTE'");
              $this->reinit($this->target);
              $this->say($this->target, true, '@'.$this->username.' channel '.$this->target.' unmuted');
            } else {
              $this->db()->delete('CONFIG', "BOTID = ".$this->config['ID']." and CHANNELID=0 and NAME = 'MUTE'");
              $this->reinit();
              $this->say($this->target, true, '@'.$this->username.' bot unmuted');
            }
          } else if($this->data[5] == '*') {
            if($this->db[$this->target]['ID'] > 0) {
              $this->db()->insert('CONFIG', array('BOTID' => $this->config['ID'], 'CHANNELID' => $this->db[$this->target]['ID'], 'NAME' => 'SILENT', 'VALUE' => 1, 'INSERTBY' => strtolower($this->username)));
              $this->db()->update('PLUGINS', array('ENABLED' => 0, 'UPDATEBY' => strtolower($this->username)), "BOTID=".$this->config['ID']." and CHANNELID=".$this->db[$this->target]['ID']);
              $this->say($this->target, true, '@'.$this->username.' everything disabled for channel '.$this->target);
              $this->reinit($this->target);
            } else {
              $this->db()->insert('CONFIG', array('BOTID' => $this->config['ID'], 'NAME' => 'SILENT', 'VALUE' => 1, 'INSERTBY' => strtolower($this->username)));
              $this->db()->update('PLUGINS', array('ENABLED' => 0, 'UPDATEBY' => strtolower($this->username)), "BOTID=".$this->config['ID']." and CHANNELID=0");
              $this->say($this->target, true, '@'.$this->username.' everything disabled for BOT');
              $this->reinit();
            }
          } else if((substr($this->data[5], 0, 1) == '#' && $this->owner() || strtolower($this->data[5]) == $this->target) && strtolower($this->data[5]) != '#'.strtolower($config['NAME'])) {
            $stmt = $this->db()->select('CHANNELS', "BOTID=".$this->config['ID']." and ENABLED=1 and NAME='".strtolower($this->data[5])."'");
            if($stmt->rowCount() > 0) {
              $channel = $stmt->fetch();
              $this->db()->update('CHANNELS', array('ENABLED' => 0, 'UPDATEBY' => strtolower($this->username)), "BOTID=".$this->config['ID']." and ID=".$channel['ID']);
              if(isset($this->db[$channel['NAME']])) {
                $this->unload($channel['ID'], $channel['NAME']);
              }
            }
          } else {
            if($this->db[$this->target]['ID'] > 0) {
              if(isset($this->db[$this->target]['plugins'][strtolower($this->data[5])])) {
                $this->db()->delete('PLUGINS', "BOTID=".$this->config['ID']." and CHANNELID=".$this->db[$this->target]['ID']." and NAME='".strtolower($this->data[5])."'");
                $this->say($this->target, true, '@'.$this->username.' disabled plugin '.$this->data[5].' for '.$this->target);
                $this->reinit($this->target);
              }
            } else {
              if(isset($this->db['#'.strtolower($this->config['NAME'])]['plugins'][strtolower($this->data[5])])) {
                $this->db()->delete('PLUGINS', "BOTID=".$this->config['ID']." and CHANNELID=0 and NAME='".strtolower($this->data[5])."'");
                $this->say($this->target, true, '@'.$this->username.' disabled plugin '.$this->data[5]);
                $this->reinit();
              }
            }
          }
        }
      }
    }
  }
  
?>