
<?php

  if(isset($execute) && $execute == true) {
    if(isset($init) && $init == true) {

    } else {
      switch($this->mode) {  
        case 'PRIVMSG':
          if(strlen($this->command.' '.$this->message) > $this->db[$this->target]['config']['limit']) {
            $this->say(null, true, 'Spam found in '.$this->target.': '.strlen($this->command.' '.$this->message).' chars');
            if ($this->db[$this->target]['ID'] > 0) {
              $this->db()->insert('PLUGIN_FILTERS', array('BOTID' => $this->config['ID'], 'CHANNELID' => $this->db[$this->target]['ID'], 'FILTER' => 'LIMIT', 'NAME' => strtolower($this->username), 'VALUE' => $this->command.' '.$this->message, 'VIOLATION' => strlen($this->command.' '.$this->message)));
            } else {
              $this->db()->insert('PLUGIN_FILTERS', array('BOTID' => $this->config['ID'], 'FILTER' => 'LIMIT', 'NAME' => strtolower($this->username), 'VALUE' => $this->command.' '.$this->message, 'VIOLATION' => strlen($this->command.' '.$this->message)));
            }
             
             //$this->say($this->target, true, '/timeout '.$this->username);
             //$this->say($this->target, true, 'No Spam, '.$this->username.' - '.strlen($output).' chars [warning]');
             $violation = true;
          }
        break;
        default:
      } 
    }
  }

?>