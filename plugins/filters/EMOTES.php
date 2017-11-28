<?php

  if(isset($execute) && $execute == true) {
    if(isset($init) && $init == true) {
      if(!isset($db[$channelname]['config']['@plugins']['filters']['emotes_limit'])) {
        $db[$channelname]['config']['@plugins']['filters']['emotes_limit'] = 25;
      }
      
      $tmp = $this->tmp;           
      $tmp['filters'][$channelname]['emotes'] = array();
      $emotes = array();
      $ch = curl_init(); 
      curl_setopt($ch, CURLOPT_URL, 'https://api.twitch.tv/kraken/chat/'.ltrim($channelname, '#').'/emoticons');
      curl_setopt($ch, CURLOPT_HEADER, 0);
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
      $result = curl_exec($ch);
      if($result != '') {
        $emotes = json_decode($result, true);
      }
            
      if(isset($emotes['emoticons'])) {
        foreach($emotes['emoticons'] as $emote) {
          array_push($tmp['filters'][$channelname]['emotes'], array('NAME' => str_replace('\\', '', $emote['regex']), 'ENABLED' => ($emote['state'] == 'active' ? 1 : 0), 'LEVEL' => ($emote['subscriber_only'] == '' ? '' : 'SUBSCRIBER')));
        }
      }
      
      $this->tmp = $tmp;
    } else {
      switch($this->mode) {  
        case 'PRIVMSG':
          $emotes = 0;
          foreach(explode(' ', ltrim($this->data[3], ':').' '.$this->message) as $word) {
            foreach($this->tmp['filters'][$this->target]['emotes'] as $key => $emote) {
              if($emote['NAME'] == $word) {
                $emotes++;
              }
            }
          }
          if($emotes >= $this->db[$this->target]['config']['@plugins']['filters']['emotes_limit']) {
            $this->say(null, true, 'Emotes limit exceeded in '.$this->target.' from '.$this->username.': '.ltrim($this->data[3], ':').' '.$this->message);
            if ($this->db[$this->target]['ID'] > 0) {
              $this->db()->insert('PLUGIN_FILTERS', array('BOTID' => $this->config['ID'], 'CHANNELID' => $this->db[$this->target]['ID'], 'FILTER' => 'EMOTES', 'NAME' => strtolower($this->username), 'VALUE' => ltrim($this->data[3], ':').' '.$this->message, 'VIOLATION' => $emotes));
            } else {
              $this->db()->insert('PLUGIN_FILTERS', array('BOTID' => $this->config['ID'], 'FILTER' => 'EMOTES', 'NAME' => strtolower($this->username), 'VALUE' => ltrim($this->data[3], ':').' '.$this->message, 'VIOLATION' => $emotes));
            }
            $violation = true;
          } 
        break;
        default:
      }
    }
  }

?>