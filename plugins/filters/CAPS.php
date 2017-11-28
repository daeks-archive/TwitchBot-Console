<?php

  if(isset($execute) && $execute == true) {
    if(isset($init) && $init == true) {
      if(!isset($db[$channelname]['config']['@plugins']['filters']['caps_limit'])) {
        $db[$channelname]['config']['@plugins']['filters']['caps_limit'] = 25;
      }
      if(!isset($db[$channelname]['config']['@plugins']['filters']['caps_percent'])) {
        $db[$channelname]['config']['@plugins']['filters']['caps_percent'] = 50;
      }
    } else {
      switch($this->mode) {  
        case 'PRIVMSG':
          $message = str_ireplace(' ', '', ltrim($this->data[3], ':').$this->message);
          $caps = 0;
          foreach(str_split($message) as $char) {
            if(ctype_upper($char)) {
              $caps++;
            }
          }
          if($caps >= $this->db[$this->target]['config']['@plugins']['filters']['caps_limit'] && round(($caps/strlen($message)*100),0) > $this->db[$this->target]['config']['@plugins']['filters']['caps_percent']) {
            $this->say(null, true, 'Caps limit ('.$caps.'/'.round(($caps/strlen($message)*100),0).'%) exceeded in '.$this->target.' from '.$this->username.': '.ltrim($this->data[3], ':').' '.$this->message);
            if ($this->db[$this->target]['ID'] > 0) {
              $this->db()->insert('PLUGIN_FILTERS', array('BOTID' => $this->config['ID'], 'CHANNELID' => $this->db[$this->target]['ID'], 'FILTER' => 'CAPS', 'NAME' => strtolower($this->username), 'VALUE' => ltrim($this->data[3], ':').' '.$this->message, 'VIOLATION' => $caps.'/'.round(($caps/strlen($message)*100),0).'%'));
            } else {
              $this->db()->insert('PLUGIN_FILTERS', array('BOTID' => $this->config['ID'], 'FILTER' => 'CAPS', 'NAME' => strtolower($this->username), 'VALUE' => ltrim($this->data[3], ':').' '.$this->message, 'VIOLATION' => $caps.'/'.round(($caps/strlen($message)*100),0).'%'));
            }
            $violation = true;
          }  
        break;
        default:
      }
    }
  }

?>