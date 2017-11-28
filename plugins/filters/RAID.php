<?php
  
  if(isset($execute) && $execute == true) {
    if(isset($init) && $init == true) {
      if(!isset($db[$channelname]['config']['@plugins']['filters']['raid_delay'])) {
        $db[$channelname]['config']['@plugins']['filters']['raid_delay'] = 3;
      }
      if(!isset($db[$channelname]['config']['@plugins']['filters']['raid_limit'])) {
        $db[$channelname]['config']['@plugins']['filters']['raid_limit'] = 50;
      }
      
      $tmp = $this->tmp;     
      $tmp['filters'][$channelname]['raid'] = array();
      $this->tmp = $tmp;
    } else {
      switch($this->mode) {  
        case 'PRIVMSG':
          $tmp = $this->tmp;

          $current = date("i");
          if(isset($tmp['filters'][$this->target]['raid']['cl-'.$current])) {
            $tmp['filters'][$this->target]['raid']['cl-'.$current] += 1;
          } else {
            $tmp['filters'][$this->target]['raid']['cl-'.$current] = 1;
          }

          while(sizeof($tmp['filters'][$this->target]['raid']) > $this->db[$this->target]['config']['@plugins']['filters']['raid_delay']) {
            array_shift($tmp['filters'][$this->target]['raid']);
          }

          $previous = array();
          for($i=0;$i<3;$i++) {
            if($current-$i < 0) {
              array_push($previous, (60-$i));
            } else {
              array_push($previous, ($current-$i));
            }
          }

          $total = 0;
          foreach($previous as $value) {
            if(isset($tmp['filters'][$this->target]['raid']['cl-'.$value])) {
              $total += $tmp['filters'][$this->target]['raid']['cl-'.$value];
            }
          }

          if($total >= $this->db[$this->target]['config']['@plugins']['filters']['raid_limit']) {
            if(!isset($tmp['filters'][$this->target]['slow'])) {
              $tmp['filters'][$this->target]['slow'] = time();
              $this->say(null, true, 'Raid START in '.$this->target.': '.$total.' messages in the last '.$this->db[$this->target]['config']['@plugins']['filters']['raid_delay'].' minutes');
            }
          } else {
            if(isset($tmp['filters'][$this->target]['slow'])) {
              unset($tmp['filters'][$this->target]['slow']);
              $this->say(null, true, 'Raid END in '.$this->target.': '.$total.' messages in the last '.$this->db[$this->target]['config']['@plugins']['filters']['raid_delay'].' minutes');
            }
          }
          $this->tmp = $tmp;
        break;
        case 'PING':
          $tmp = $this->tmp;
          
          $current = date("i");              
          while(sizeof($tmp['filters'][$this->target]['raid']) > $this->db[$this->target]['config']['@plugins']['filters']['raid_delay']) {
            array_shift($tmp['filters'][$this->target]['raid']);
          }
          
          $previous = array();
          for($i=0;$i<3;$i++) {
            if($current-$i < 0) {
              array_push($previous, (60-$i));
            } else {
              array_push($previous, ($current-$i));
            }
          }
          
          $total = 0;
          foreach($previous as $value) {
            if(isset($tmp['filters'][$this->target]['raid'][$value])) {
              $total += $tmp['filters'][$this->target]['raid'][$value];
            }
          }
          
          if($total < $this->db[$this->target]['config']['@plugins']['filters']['raid_limit']) {
            if(isset($tmp['filters'][$this->target]['slow'])) {
              unset($tmp['filters'][$this->target]['slow']);
              $this->say(null, true, 'Raid END in '.$this->target.': '.$total.' messages in the last '.$this->db[$this->target]['config']['@plugins']['filters']['raid_delay'].' minutes');
            }
          }
          $this->tmp = $tmp;
        break;
        default:
      }
    }
  }
?>