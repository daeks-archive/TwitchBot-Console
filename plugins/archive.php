<?php

  $cmd = array('id' => 'plugin.archive', 
               'level' => '',
               'help' => 'Archive Plugin');
  
  if(isset($execute) && $execute == true) {
    if(isset($init) && $init == true) {

    } else {
      if($this->access($cmd['level'])) {
        switch($this->mode) {  
          case 'PING':
            $db = $this->db();
        
            $users = $db->query('select distinct INSERTBY from logs where BOTID='.$this->config['ID']." and CHANNELID=".$this->db[$this->target]['ID']);
            $stmt0 = $db->select('ARCHIVE', "BOTID=".$this->config['ID']." and CHANNELID=".$this->db[$this->target]['ID']." and TYPE = 'TOTAL' and NAME='USERS'");
            if($stmt0->rowCount() > 0) {
              $entry = $stmt0->fetch();
              $db->update('ARCHIVE', array('VALUE' => $users->rowCount()), "ID=".$entry['ID']);
            } else {
              $db->insert('ARCHIVE', array('BOTID' => $this->config['ID'], 'CHANNELID' => $this->db[$this->target]['ID'], 'NAME' => 'USERS', 'TYPE' => 'TOTAL', 'VALUE' => $users->rowCount()));
            }      
            
            $locales = $db->query('select LOCALE, count(*) as TOTAL from logs where BOTID='.$this->config['ID']." and CHANNELID=".$this->db[$this->target]['ID']." and TYPE='READ' and NAME='PRIVMSG' and INSERTED < CURDATE() group by LOCALE");
            foreach($locales->fetchAll() as $row) {
              $stmt0 = $db->select('ARCHIVE', "BOTID=".$this->config['ID']." and CHANNELID=".$this->db[$this->target]['ID']." and TYPE = 'TOTAL' and NAME='LOCALE' and SUBNAME='".strtoupper($row['LOCALE'])."'");
              if($stmt0->rowCount() > 0) {
                $entry = $stmt0->fetch();
                $db->update('ARCHIVE', array('VALUE' => $row['TOTAL']), "ID=".$entry['ID']);
              } else {
                $db->insert('ARCHIVE', array('BOTID' => $this->config['ID'], 'CHANNELID' => $this->db[$this->target]['ID'], 'NAME' => 'LOCALE', 'TYPE' => 'TOTAL', 'SUBNAME' => strtoupper($row['LOCALE']), 'VALUE' => $row['TOTAL']));
              }
            }
            
            $chatters = $db->query('select distinct INSERTBY from logs where BOTID='.$this->config['ID']." and CHANNELID=".$this->db[$this->target]['ID']." and TYPE='READ' and NAME='PRIVMSG'");
            $stmt0 = $db->select('ARCHIVE', "BOTID=".$this->config['ID']." and CHANNELID=".$this->db[$this->target]['ID']." and TYPE = 'TOTAL' and NAME='CHATTERS'");
            if($stmt0->rowCount() > 0) {
              $entry = $stmt0->fetch();
              $db->update('ARCHIVE', array('VALUE' => $chatters->rowCount()), "ID=".$entry['ID']);
            } else {
              $db->insert('ARCHIVE', array('BOTID' => $this->config['ID'], 'CHANNELID' => $this->db[$this->target]['ID'], 'NAME' => 'CHATTERS', 'TYPE' => 'TOTAL', 'VALUE' => $chatters->rowCount()));
            }           
            
            $stmt0 = $db->query("select NAME, date_format(INSERTED, '%d.%m.%Y') as DAY, avg(time) as AVERAGE from log_plugins where BOTID=".$this->config['ID']." and CHANNELID=".$this->db[$this->target]['ID']." and INSERTED < CURDATE() and ARCHIVED is null group by NAME, date_format(INSERTED, '%d.%m.%Y')");
            if($stmt0->rowCount() > 0) {
              foreach($stmt0->fetchAll() as $row) {
                $data = array();
                $data['BOTID'] = $this->config['ID'];
                $data['CHANNELID'] = $this->db[$this->target]['ID'];
                $data['TYPE'] = 'PLUGIN';
                $data['NAME'] = strtoupper($row['NAME']);
                $data['ARCHIVED'] = date('Y-m-d', strtotime($row['DAY']));
                $data['VALUE'] = $row['AVERAGE'];
                $db->insert('ARCHIVE', $data);
              }
              $db->query("update log_plugins set ARCHIVED = now() where ARCHIVED is null and INSERTED < CURDATE() and BOTID=".$this->config['ID']." and CHANNELID=".$this->db[$this->target]['ID']);
            }
                  
            $stmt1 = $db->query("select count(*) as TOTAL, date_format(INSERTED, '%d.%m.%Y') as DAY from logs where ARCHIVED is null and INSERTED < CURDATE() and BOTID=".$this->config['ID']." and CHANNELID=".$this->db[$this->target]['ID']." group by date_format(INSERTED, '%d.%m.%Y')");
            if($stmt1->rowCount() > 0) {
              foreach($stmt1->fetchAll() as $row) {
                $data = array();
                $data['BOTID'] = $this->config['ID'];
                $data['CHANNELID'] = $this->db[$this->target]['ID'];
                $data['TYPE'] = 'TOTAL';
                $data['NAME'] = 'EVENTS';
                $data['ARCHIVED'] = date('Y-m-d', strtotime($row['DAY']));
                $data['VALUE'] = $row['TOTAL'];
                $db->insert('ARCHIVE', $data);
              }
              
              $modes = array('READ@CLEARCHAT', 
                     'READ@HOSTTARGET', 
                     'READ@JOIN', 
                     'READ@PART', 
                     'READ@PRIVMSG', 
                     'WRITE@PRIVMSG', 
                     'READ@WHISPER', 
                     'READ@NOTICE');

              foreach($modes as $tmp) {
                $keys = explode('@', $tmp);
                $stmt2 = $db->query("select count(*) as TOTAL, date_format(INSERTED, '%d.%m.%Y') as DAY from logs where TYPE='".$keys[0]."' and NAME='".$keys[1]."' and ARCHIVED is null and INSERTED < CURDATE() and BOTID=".$this->config['ID']." and CHANNELID=".$this->db[$this->target]['ID']." group by date_format(INSERTED, '%d.%m.%Y')");
                if($stmt2->rowCount() > 0) {
                  foreach($stmt2->fetchAll() as $row) {
                    $data = array();
                    $data['BOTID'] = $this->config['ID'];
                    $data['CHANNELID'] = $this->db[$this->target]['ID'];
                    $data['TYPE'] = $keys[0];
                    $data['NAME'] = $keys[1];
                    $data['ARCHIVED'] = date('Y-m-d', strtotime($row['DAY']));
                    $data['VALUE'] = $row['TOTAL'];
                    $db->insert('ARCHIVE', $data);
                  }
                }
              }
              $db->query("update logs set ARCHIVED = now() where ARCHIVED is null and INSERTED < CURDATE() and BOTID=".$this->config['ID']." and CHANNELID=".$this->db[$this->target]['ID']);
            }
            unset($db);
          break;
          default:
        }
      }
    }
  }

?>