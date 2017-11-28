<?php

  if(isset($execute) && $execute == true) {
    if(isset($init) && $init == true) {
      if(!isset($db[$channelname]['config']['@plugins']['filters']['symbols_limit'])) {
        $db[$channelname]['config']['@plugins']['filters']['symbols_limit'] = 25;
      }
    } else {
      switch($this->mode) {  
        case 'PRIVMSG':
          $message = str_ireplace(' ', '', $this->command.$this->message);
          $symbols = 0;
          foreach(str_split($message) as $char) {
            if(!ctype_alnum($char)) {
              $symbols++;
            }
          }
          if($symbols >= $this->db[$this->target]['config']['@plugins']['filters']['symbols_limit']) {
            $this->say(null, true, 'Symbols limit exceeded in '.$this->target.' from '.$this->username.': '.ltrim($this->data[3], ':').' '.$this->message);
            if ($this->db[$this->target]['ID'] > 0) {
              $this->db()->insert('PLUGIN_FILTERS', array('BOTID' => $this->config['ID'], 'CHANNELID' => $this->db[$this->target]['ID'], 'FILTER' => 'SYMBOLS', 'NAME' => strtolower($this->username), 'VALUE' => ltrim($this->data[3], ':').' '.$this->message, 'VIOLATION' => $symbols));
            } else {
              $this->db()->insert('PLUGIN_FILTERS', array('BOTID' => $this->config['ID'], 'FILTER' => 'SYMBOLS', 'NAME' => strtolower($this->username), 'VALUE' => ltrim($this->data[3], ':').' '.$this->message, 'VIOLATION' => $symbols));
            }
            $violation = true;
          }  
        break;
        default:
      }
    }
  }

?>