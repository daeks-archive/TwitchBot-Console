<?php

  $cmd = array('id' => 'tb.botname',
               'level' => 'owner',
               'help' => 'Channel control for the Bot & version information',
               'syntax' => '$BOTNAME (join <channel> (mute)|part <channel>|quit|restart)');

  if(isset($execute) && $execute == true) {
    if($this->access($cmd['level'])) {
      if(isset($this->data[5])) {
        if($this->data[4] == 'join') {
          if(isset($this->data[5]) && substr($this->data[5], 0, 1) == '#' && strtolower($this->data[5]) != '#'.strtolower($this->config['NAME'])) {
            if(!isset($this->db[strtolower($this->data[5])])) {
              $stmt = $this->db()->select('CHANNELS', "BOTID=".$this->config['ID']." and NAME='".strtolower($this->data[5])."'");
              if($stmt->rowCount() == 0) {
                $channelid = $this->db()->insert('CHANNELS', array('BOTID' => $this->config['ID'], 'NAME' => strtolower($this->data[5]), 'INSERTBY' => strtolower($this->username)));
                if(isset($this->data[6]) && strtolower($this->data[6]) == 'mute') {
                  $this->db()->insert('CONFIG', array('BOTID' => $this->config['ID'], 'CHANNELID' => $channelid, 'NAME' => 'MUTE', 'VALUE' => 1, 'INSERTBY' => strtolower($this->username)));
                }
                $this->load($channelid, strtolower($this->data[5]));
              } else {
                $channel = $stmt->fetch();
                if($channel['ENABLED'] == 1) {
                  $this->load($channel['ID'], $channel['NAME']);
                } else {
                  $this->say($this->target, true, '@'.$this->username.' channel '.$channel['NAME'].' is disabled for BOT. Use $config enable '.$channel['NAME']);
                }
              }
            } 
          }
        } else if($this->data[4] == 'leave' || $this->data[4] == 'part') {
          if(isset($this->data[5]) && substr($this->data[5], 0, 1) == '#' && strtolower($this->data[5]) != '#'.strtolower($this->config['NAME'])) {
            if(isset($this->db[strtolower($this->data[5])])) {
              $stmt = $this->db()->select('CHANNELS', "BOTID=".$this->config['ID']." and NAME='".strtolower($this->data[5])."'");
              if($stmt->rowCount() > 0) {
                $channel = $stmt->fetch();
                $stmt = $this->db()->delete('CHANNELS', "BOTID=".$this->config['ID']." and ID=".$channel['ID']);
                $this->unload($channel['ID'], strtolower($this->data[5]));
              }
            } 
          }
        } 
      } else {
        if(isset($this->data[4])) {
          if($this->data[4] == 'quit') {
            if($this->target == '#'.strtolower($this->config['NAME'])) {
              $this->say(null, true, '@'.$this->username.' - shutting down');
              $this->log('Good Bye');
              file_put_contents(CACHE_PATH.DIRECTORY_SEPARATOR.$this->config['ID'].'.pid', $this->config['ID']);
              $this->destroy();
            }
          } else if($this->data[4] == 'restart') {
            if($this->target == '#'.strtolower($this->config['NAME'])) {
              $this->say(null, true, '@'.$this->username.' - restarting');
              $this->exit = 1;
              $this->log('Good Bye');
              $this->destroy();
            }
          }
        } else {
          $this->say($this->target, false, '@'.$this->username.' I am '.$this->config['NAME'].' v2 (build '.$this->version.') - (c) daeks '.date('Y'));
        }
      }
    } else {
      $this->say($this->target, false, '@'.$this->username.' I am '.$this->config['NAME'].' v2 (build '.$this->version.') - (c) daeks '.date('Y'));
    }
  }

?>