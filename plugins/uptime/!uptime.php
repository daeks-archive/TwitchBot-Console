<?php

  $cmd = array('id' => 'plugin.uptime.uptime',
               'level' => 'broadcaster owner',
               'help' => 'Displays streams or bots uptime',
               'syntax' => '!uptime [botname]');
  
  if(isset($execute) && $execute == true) {
    if($this->access($cmd['level'])) {
      if(isset($this->data[4])) {
        if(strtolower($this->data[4]) == strtolower($this->config['NAME'])) {
          if(isset($this->tmp['uptime']['#'.strtolower($this->data[4])]['chat'])) {
            $time = time()-$this->tmp['uptime']['#'.strtolower($this->config['NAME'])]['chat'];
            $this->say($this->target, true, 'My uptime: '.sprintf("%02dh %02dm %02ds", floor($time/3600), ($time/60)%60, $time%60));
          }
        } else if(substr($this->data[4], 0, 1) == '#') {
          if(isset($this->tmp['uptime'][strtolower($this->data[4])]['stream'])) {
            $time = time()-$this->tmp['uptime'][strtolower($this->data[4])]['stream'];
            $this->say($this->target, true, 'Stream Uptime: '.sprintf("%02dh %02dm %02ds", floor($time/3600), ($time/60)%60, $time%60));
          } else {
            $stream = array();
            $ch = curl_init(); 
            curl_setopt($ch, CURLOPT_URL, 'https://api.twitch.tv/kraken/streams/'.ltrim(strtolower($this->data[4]), '#'));
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
            $return = curl_exec($ch);
            if($return != '') {
              $stream = json_decode($return, true);
            }
            
            if(isset($stream['stream']['created_at'])) {
              $tmp = $this->tmp;
              $tmp['uptime'][strtolower($this->data[4])]['stream'] = strtotime($stream['stream']['created_at']);
              $this->tmp = $tmp;
            } else {
              $tmp = $this->tmp;
              unset($tmp['uptime'][strtolower($this->data[4])]['stream']);
              $this->tmp = $tmp;
            }
            
            if(isset($this->tmp['uptime'][strtolower($this->data[4])]['stream'])) {
              $time = time()-$this->tmp['uptime'][strtolower($this->data[4])]['stream'];
              $this->say($this->target, true, 'Stream Uptime: '.sprintf("%02dh %02dm %02ds", floor($time/3600), ($time/60)%60, $time%60));
            } else if(isset($this->tmp['uptime'][strtolower($this->data[4])]['chat'])) {
              $time = time()-$this->tmp['uptime'][strtolower($this->data[4])]['chat'];
              $this->say($this->target, true, 'Chat Uptime: '.sprintf("%02dh %02dm %02ds", floor($time/3600), ($time/60)%60, $time%60));
            }
          }
        }
      } else {
        if(isset($this->tmp['uptime'][$this->target]['stream'])) {
          $time = time()-$this->tmp['uptime'][$this->target]['stream'];
          $this->say($this->target, true, 'Stream Uptime: '.sprintf("%02dh %02dm %02ds", floor($time/3600), ($time/60)%60, $time%60));
        } else {
          $stream = array();
          $ch = curl_init(); 
          curl_setopt($ch, CURLOPT_URL, 'https://api.twitch.tv/kraken/streams/'.ltrim(strtolower($this->target), '#'));
          curl_setopt($ch, CURLOPT_HEADER, 0);
          curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
          curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
          curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
          $return = curl_exec($ch);
          if($return != '') {
            $stream = json_decode($return, true);
          }
          
          if(isset($stream['stream']['created_at'])) {
            $tmp = $this->tmp;
            $tmp['uptime'][$this->target]['stream'] = strtotime($stream['stream']['created_at']);
            $this->tmp = $tmp;
          } else {
            $tmp = $this->tmp;
            unset($tmp['uptime'][$this->target]['stream']);
            $this->tmp = $tmp;
          }
          if(isset($this->tmp['uptime'][$this->target]['stream'])) {
            $time = time()-$this->tmp['uptime'][$this->target]['stream'];
            $this->say($this->target, true, 'Stream Uptime: '.sprintf("%02dh %02dm %02ds", floor($time/3600), ($time/60)%60, $time%60));
          } else if(isset($this->tmp['uptime'][$this->target]['chat'])) {
            $time = time()-$this->tmp['uptime'][$this->target]['chat'];
            if($this->target == '#'.strtolower($this->config['NAME'])) {
              $this->say($this->target, true, 'My Uptime: '.sprintf("%02dh %02dm %02ds", floor($time/3600), ($time/60)%60, $time%60));
            } else {
              $this->say($this->target, true, 'Chat Uptime: '.sprintf("%02dh %02dm %02ds", floor($time/3600), ($time/60)%60, $time%60));
            }
          }
        }
      }
    }
  }
  
?>