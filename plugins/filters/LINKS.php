<?php

  if(isset($execute) && $execute == true) {
    if(isset($init) && $init == true) {
      
    } else {
      switch($this->mode) {  
        case 'PRIVMSG':
          $regexp = '/\b(?:(?:https?|ftp|file):\/\/|www\.|ftp\.)[-A-Z0-9+&@#\/%=~_|$?!:,.]*[A-Z0-9+&@#\/%=~_|$]/i';
          if(preg_match($regexp, $this->command.' '.$this->message, $match)) {
            $nurl = parse_url($match[0]);
            $socket = @fsockopen($nurl['host'], (isset($nurl['port'])? $nurl['port'] : 80), $errno, $errstr, 5);
            if($socket) {
              fclose($socket);
              $this->say(null, true, 'Found URL in '.$this->target.' from '.$this->username.': '.$match[0]);
              if ($this->db[$this->target]['ID'] > 0) {
                $this->db()->insert('PLUGIN_FILTERS', array('BOTID' => $this->config['ID'], 'CHANNELID' => $this->db[$this->target]['ID'], 'FILTER' => 'LINKS', 'NAME' => strtolower($this->username), 'VIOLATION' => $match[0], 'VALUE' => ltrim($this->data[3], ':').' '.$this->message));
              } else {
                $this->db()->insert('PLUGIN_FILTERS', array('BOTID' => $this->config['ID'], 'FILTER' => 'LINKS', 'NAME' => strtolower($this->username), 'VIOLATION' => $match[0], 'VALUE' => ltrim($this->data[3], ':').' '.$this->message));
              }
         
              //$this->say($this->target, true, '/timeout '.$this->username.' 1');
              //$this->say($this->target, true, 'No links allowed, '.$this->username.' [warning]');
              $violation = true;
            }
          }
        break;
        default:
      }
    }
  }

?>