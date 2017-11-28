
--
-- Tabellenstruktur für Tabelle `archive`
--

CREATE TABLE `archive` (
  `ID` int(11) NOT NULL,
  `BOTID` int(11) NOT NULL,
  `CHANNELID` int(11) NOT NULL DEFAULT '0',
  `TYPE` varchar(255) NOT NULL,
  `NAME` varchar(255) NOT NULL,
  `SUBNAME` varchar(255) DEFAULT NULL,
  `VALUE` double(11,3) NOT NULL DEFAULT '0.000',
  `ENABLED` int(11) NOT NULL DEFAULT '1',
  `INSERTED` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `UPDATED` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `ARCHIVED` timestamp NULL DEFAULT NULL,
  `INSERTBY` varchar(255) DEFAULT NULL,
  `UPDATEBY` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `bots`
--

CREATE TABLE `bots` (
  `ID` int(11) NOT NULL,
  `USERID` int(11) NOT NULL DEFAULT '0',
  `NAME` varchar(255) NOT NULL,
  `LOGO` varchar(255) DEFAULT NULL,
  `BANNER` varchar(255) DEFAULT NULL,
  `OAUTH` varchar(255) NOT NULL,
  `PORT` int(10) DEFAULT NULL,
  `COLOR` varchar(255) NOT NULL,
  `OWNER` varchar(255) NOT NULL,
  `ENABLED` int(1) NOT NULL DEFAULT '1',
  `AUTOSTART` int(1) NOT NULL DEFAULT '1',
  `INSERTED` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `INSERTBY` varchar(255) DEFAULT NULL,
  `UPDATED` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `UPDATEBY` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `channels`
--

CREATE TABLE `channels` (
  `ID` int(11) NOT NULL,
  `BOTID` int(11) NOT NULL,
  `NAME` varchar(255) NOT NULL,
  `LOGO` varchar(255) DEFAULT NULL,
  `BANNER` varchar(255) DEFAULT NULL,
  `ENABLED` int(11) NOT NULL DEFAULT '1',
  `INSERTED` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `INSERTBY` varchar(255) DEFAULT NULL,
  `UPDATED` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `UPDATEBY` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `commands`
--

CREATE TABLE `commands` (
  `ID` int(11) NOT NULL,
  `BOTID` int(11) NOT NULL DEFAULT '0',
  `CHANNELID` int(11) NOT NULL DEFAULT '0',
  `PLUGINID` int(11) NOT NULL DEFAULT '0',
  `COMMANDID` int(11) NOT NULL DEFAULT '0',
  `NAME` varchar(255) NOT NULL,
  `VALUE` varchar(255) NOT NULL,
  `LEVEL` varchar(255) DEFAULT NULL,
  `ENABLED` int(11) NOT NULL DEFAULT '1',
  `INSERTED` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `INSERTBY` varchar(255) DEFAULT NULL,
  `UPDATED` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `UPDATEBY` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `config`
--

CREATE TABLE `config` (
  `ID` int(11) NOT NULL,
  `BOTID` int(11) NOT NULL DEFAULT '0',
  `CHANNELID` int(11) NOT NULL DEFAULT '0',
  `PLUGINID` int(11) NOT NULL DEFAULT '0',
  `COMMANDID` int(11) DEFAULT '0',
  `NAME` varchar(255) NOT NULL,
  `VALUE` varchar(255) NOT NULL,
  `ENABLED` int(11) NOT NULL DEFAULT '1',
  `INSERTED` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `INSERTBY` varchar(255) DEFAULT NULL,
  `UPDATED` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `UPDATEBY` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `locale`
--

CREATE TABLE `locale` (
  `ID` int(11) NOT NULL,
  `BOTID` int(11) NOT NULL DEFAULT '0',
  `CHANNELID` int(11) NOT NULL DEFAULT '0',
  `LOCALE` varchar(2) NOT NULL DEFAULT 'en',
  `NAME` varchar(255) NOT NULL,
  `VALUE` text NOT NULL,
  `ENABLED` int(11) NOT NULL DEFAULT '1',
  `INSERTED` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `INSERTBY` varchar(255) DEFAULT NULL,
  `UPDATED` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `UPDATEBY` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `logs`
--

CREATE TABLE `logs` (
  `ID` int(11) NOT NULL,
  `PARENTID` int(11) DEFAULT NULL,
  `BOTID` int(11) NOT NULL DEFAULT '0',
  `CHANNELID` int(11) NOT NULL DEFAULT '0',
  `TIME` double(11,3) NOT NULL,
  `TYPE` varchar(255) NOT NULL,
  `NAME` varchar(255) DEFAULT NULL,
  `VALUE` text NOT NULL,
  `LOCALE` varchar(255) DEFAULT NULL,
  `PLUGINS` text,
  `DUMP` text,
  `ARCHIVED` timestamp NULL DEFAULT NULL,
  `INSERTED` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `INSERTBY` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `log_plugins`
--

CREATE TABLE `log_plugins` (
  `ID` int(11) NOT NULL,
  `BOTID` int(11) NOT NULL,
  `CHANNELID` int(11) DEFAULT NULL,
  `LOGID` int(11) DEFAULT NULL,
  `NAME` varchar(255) NOT NULL,
  `TIME` double(11,3) NOT NULL,
  `ARCHIVED` timestamp NULL DEFAULT NULL,
  `INSERTED` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `INSERTBY` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `plugins`
--

CREATE TABLE `plugins` (
  `ID` int(11) NOT NULL,
  `BOTID` int(11) NOT NULL DEFAULT '0',
  `CHANNELID` int(11) NOT NULL DEFAULT '0',
  `NAME` varchar(255) NOT NULL,
  `ENABLED` int(11) NOT NULL DEFAULT '1',
  `POSITION` int(11) NOT NULL DEFAULT '1',
  `LEVEL` varchar(255) DEFAULT NULL,
  `INSERTED` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `UPDATED` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `INSERTBY` varchar(255) DEFAULT NULL,
  `UPDATEBY` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `plugin_commands`
--

CREATE TABLE `plugin_commands` (
  `ID` int(11) NOT NULL,
  `BOTID` int(11) NOT NULL,
  `CHANNELID` int(11) NOT NULL DEFAULT '0',
  `NAME` varchar(255) NOT NULL,
  `VALUE` varchar(400) NOT NULL,
  `LEVEL` varchar(255) DEFAULT NULL,
  `INSERTED` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `UPDATED` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `INSERTBY` varchar(255) DEFAULT NULL,
  `UPDATEBY` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `plugin_credits`
--

CREATE TABLE `plugin_credits` (
  `ID` int(11) NOT NULL,
  `BOTID` int(11) NOT NULL,
  `CHANNELID` int(11) NOT NULL DEFAULT '0',
  `NAME` varchar(255) NOT NULL,
  `VALUE` int(11) NOT NULL DEFAULT '0',
  `ENABLED` int(11) NOT NULL DEFAULT '1',
  `INSERTED` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `UPDATED` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `INSERTBY` varchar(255) DEFAULT NULL,
  `UPDATEBY` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `plugin_filters`
--

CREATE TABLE `plugin_filters` (
  `ID` int(11) NOT NULL,
  `BOTID` int(11) NOT NULL DEFAULT '0',
  `CHANNELID` int(11) NOT NULL DEFAULT '0',
  `FILTER` varchar(255) NOT NULL,
  `NAME` varchar(255) NOT NULL,
  `VALUE` text NOT NULL,
  `VIOLATION` text,
  `INSERTED` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `INSERTBY` varchar(255) DEFAULT NULL,
  `UPDATED` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `UPDATEBY` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `plugin_loyality`
--

CREATE TABLE `plugin_loyality` (
  `ID` int(11) NOT NULL,
  `BOTID` int(11) NOT NULL,
  `CHANNELID` int(11) NOT NULL DEFAULT '0',
  `NAME` varchar(255) NOT NULL,
  `VALUE` int(11) NOT NULL DEFAULT '0',
  `ENABLED` int(11) NOT NULL DEFAULT '1',
  `INSERTED` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `UPDATED` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `INSERTBY` varchar(255) DEFAULT NULL,
  `UPDATEBY` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `plugin_quotes`
--

CREATE TABLE `plugin_quotes` (
  `ID` int(11) NOT NULL,
  `BOTID` int(11) NOT NULL,
  `CHANNELID` int(11) NOT NULL DEFAULT '0',
  `VALUE` text NOT NULL,
  `INSERTED` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `UPDATED` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `INSERTBY` varchar(255) DEFAULT NULL,
  `UPDATEBY` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `plugin_ranks`
--

CREATE TABLE `plugin_ranks` (
  `ID` int(11) NOT NULL,
  `BOTID` int(11) NOT NULL,
  `CHANNELID` int(11) NOT NULL DEFAULT '0',
  `NAME` varchar(255) NOT NULL,
  `EXPERIENCE` int(11) NOT NULL DEFAULT '0',
  `LEVEL` int(11) NOT NULL DEFAULT '0',
  `ENABLED` int(11) NOT NULL DEFAULT '1',
  `INSERTED` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `UPDATED` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `INSERTBY` varchar(255) DEFAULT NULL,
  `UPDATEBY` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `plugin_stats`
--

CREATE TABLE `plugin_stats` (
  `ID` int(11) NOT NULL,
  `BOTID` int(11) NOT NULL,
  `CHANNELID` int(11) NOT NULL DEFAULT '0',
  `NAME` varchar(255) NOT NULL,
  `VALUE` int(11) NOT NULL DEFAULT '0',
  `ENABLED` int(11) NOT NULL DEFAULT '1',
  `INSERTED` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `UPDATED` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `INSERTBY` varchar(255) DEFAULT NULL,
  `UPDATEBY` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `plugin_timers`
--

CREATE TABLE `plugin_timers` (
  `ID` int(11) NOT NULL,
  `BOTID` int(11) NOT NULL,
  `CHANNELID` int(11) NOT NULL DEFAULT '0',
  `NAME` varchar(255) NOT NULL,
  `VALUE` text NOT NULL,
  `SCHEDULE` int(11) NOT NULL,
  `CHAT` int(11) NOT NULL DEFAULT '1',
  `MODE` varchar(255) NOT NULL,
  `ALIAS` varchar(255) DEFAULT NULL,
  `ENABLED` int(11) NOT NULL DEFAULT '1',
  `INSERTED` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `UPDATED` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `INSERTBY` varchar(255) DEFAULT NULL,
  `UPDATEBY` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `regulars`
--

CREATE TABLE `regulars` (
  `ID` int(11) NOT NULL,
  `BOTID` int(11) NOT NULL,
  `CHANNELID` int(11) NOT NULL DEFAULT '0',
  `NAME` varchar(255) NOT NULL,
  `INSERTED` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `UPDATED` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `INSERTBY` varchar(255) DEFAULT NULL,
  `UPDATEBY` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `users`
--

CREATE TABLE `users` (
  `ID` int(11) NOT NULL,
  `NAME` varchar(255) NOT NULL,
  `OAUTH` varchar(255) NOT NULL,
  `EMAIL` varchar(255) DEFAULT NULL,
  `LOGO` varchar(255) DEFAULT NULL,
  `BANNER` varchar(255) DEFAULT NULL,
  `LEVEL` varchar(255) DEFAULT NULL,
  `SCOPE` varchar(255) DEFAULT NULL,
  `ENABLED` int(11) NOT NULL DEFAULT '1',
  `INSERTED` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `INSERTBY` varchar(255) DEFAULT NULL,
  `UPDATED` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `UPDATEBY` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `archive`
--
ALTER TABLE `archive`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `BOTID` (`BOTID`),
  ADD KEY `NAME` (`NAME`),
  ADD KEY `ENABLED` (`ENABLED`),
  ADD KEY `BOTID_2` (`BOTID`),
  ADD KEY `CHANNELID` (`CHANNELID`);

--
-- Indizes für die Tabelle `bots`
--
ALTER TABLE `bots`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `ENABLED` (`ENABLED`),
  ADD KEY `AUTOSTART` (`AUTOSTART`),
  ADD KEY `USERID` (`USERID`);

--
-- Indizes für die Tabelle `channels`
--
ALTER TABLE `channels`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `BOTID` (`BOTID`),
  ADD KEY `ENABLED` (`ENABLED`);

--
-- Indizes für die Tabelle `commands`
--
ALTER TABLE `commands`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `BOTID` (`BOTID`),
  ADD KEY `CHANNELID` (`CHANNELID`),
  ADD KEY `PLUGINID` (`PLUGINID`),
  ADD KEY `NAME` (`NAME`),
  ADD KEY `ENABLED` (`ENABLED`),
  ADD KEY `COMMANDID` (`COMMANDID`);

--
-- Indizes für die Tabelle `config`
--
ALTER TABLE `config`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `BOTID` (`BOTID`),
  ADD KEY `CHANNELID` (`CHANNELID`),
  ADD KEY `PLUGINID` (`PLUGINID`),
  ADD KEY `NAME` (`NAME`),
  ADD KEY `ENABLED` (`ENABLED`),
  ADD KEY `COMMANDID` (`COMMANDID`);

--
-- Indizes für die Tabelle `locale`
--
ALTER TABLE `locale`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `BOTID` (`BOTID`),
  ADD KEY `CHANNELID` (`CHANNELID`),
  ADD KEY `LOCALE` (`LOCALE`),
  ADD KEY `NAME` (`NAME`),
  ADD KEY `ENABLED` (`ENABLED`);

--
-- Indizes für die Tabelle `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `BOTID` (`BOTID`),
  ADD KEY `CHANNELID` (`CHANNELID`),
  ADD KEY `NAME` (`NAME`),
  ADD KEY `TIME` (`TIME`),
  ADD KEY `INSERTED` (`INSERTED`),
  ADD KEY `INSERTBY` (`INSERTBY`),
  ADD KEY `PARENTID` (`PARENTID`),
  ADD KEY `LOCALE` (`LOCALE`),
  ADD KEY `TYPE` (`TYPE`),
  ADD KEY `ARCHIVED` (`ARCHIVED`);

--
-- Indizes für die Tabelle `log_plugins`
--
ALTER TABLE `log_plugins`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `BOTID` (`BOTID`),
  ADD KEY `CHANNELID` (`CHANNELID`),
  ADD KEY `NAME` (`NAME`),
  ADD KEY `INSERTED` (`INSERTED`),
  ADD KEY `LOGID` (`LOGID`),
  ADD KEY `TIME` (`TIME`),
  ADD KEY `ARCHIVED` (`ARCHIVED`);

--
-- Indizes für die Tabelle `plugins`
--
ALTER TABLE `plugins`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `BOTID` (`BOTID`),
  ADD KEY `CHANNELID` (`CHANNELID`),
  ADD KEY `NAME` (`NAME`),
  ADD KEY `ENABLED` (`ENABLED`);

--
-- Indizes für die Tabelle `plugin_commands`
--
ALTER TABLE `plugin_commands`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `BOTID` (`BOTID`),
  ADD KEY `CHANNELID` (`CHANNELID`),
  ADD KEY `NAME` (`NAME`);

--
-- Indizes für die Tabelle `plugin_credits`
--
ALTER TABLE `plugin_credits`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `BOTID` (`BOTID`),
  ADD KEY `CHANNELID` (`CHANNELID`),
  ADD KEY `NAME` (`NAME`),
  ADD KEY `VALUE` (`VALUE`),
  ADD KEY `ENABLED` (`ENABLED`);

--
-- Indizes für die Tabelle `plugin_filters`
--
ALTER TABLE `plugin_filters`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `BOTID` (`BOTID`),
  ADD KEY `CHANNELID` (`CHANNELID`),
  ADD KEY `FILTER` (`FILTER`),
  ADD KEY `NAME` (`NAME`);

--
-- Indizes für die Tabelle `plugin_loyality`
--
ALTER TABLE `plugin_loyality`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `BOTID` (`BOTID`),
  ADD KEY `CHANNELID` (`CHANNELID`),
  ADD KEY `NAME` (`NAME`),
  ADD KEY `VALUE` (`VALUE`),
  ADD KEY `ENABLED` (`ENABLED`);

--
-- Indizes für die Tabelle `plugin_quotes`
--
ALTER TABLE `plugin_quotes`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `BOTID` (`BOTID`),
  ADD KEY `CHANNELID` (`CHANNELID`);

--
-- Indizes für die Tabelle `plugin_ranks`
--
ALTER TABLE `plugin_ranks`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `BOTID` (`BOTID`),
  ADD KEY `CHANNELID` (`CHANNELID`),
  ADD KEY `NAME` (`NAME`),
  ADD KEY `EXPERIENCE` (`EXPERIENCE`),
  ADD KEY `LEVEL` (`LEVEL`),
  ADD KEY `ENABLED` (`ENABLED`);

--
-- Indizes für die Tabelle `plugin_stats`
--
ALTER TABLE `plugin_stats`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `BOTID` (`BOTID`),
  ADD KEY `CHANNELID` (`CHANNELID`),
  ADD KEY `NAME` (`NAME`),
  ADD KEY `ENABLED` (`ENABLED`);

--
-- Indizes für die Tabelle `plugin_timers`
--
ALTER TABLE `plugin_timers`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `BOTID` (`BOTID`),
  ADD KEY `CHANNELID` (`CHANNELID`),
  ADD KEY `NAME` (`NAME`),
  ADD KEY `ENABLED` (`ENABLED`),
  ADD KEY `ALIAS` (`ALIAS`);

--
-- Indizes für die Tabelle `regulars`
--
ALTER TABLE `regulars`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `BOTID` (`BOTID`),
  ADD KEY `CHANNELID` (`CHANNELID`),
  ADD KEY `NAME` (`NAME`);

--
-- Indizes für die Tabelle `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `NAME` (`NAME`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `archive`
--
ALTER TABLE `archive`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7386;
--
-- AUTO_INCREMENT für Tabelle `bots`
--
ALTER TABLE `bots`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT für Tabelle `channels`
--
ALTER TABLE `channels`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT für Tabelle `commands`
--
ALTER TABLE `commands`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT für Tabelle `config`
--
ALTER TABLE `config`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;
--
-- AUTO_INCREMENT für Tabelle `locale`
--
ALTER TABLE `locale`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
--
-- AUTO_INCREMENT für Tabelle `logs`
--
ALTER TABLE `logs`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1213826;
--
-- AUTO_INCREMENT für Tabelle `log_plugins`
--
ALTER TABLE `log_plugins`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12754235;
--
-- AUTO_INCREMENT für Tabelle `plugins`
--
ALTER TABLE `plugins`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;
--
-- AUTO_INCREMENT für Tabelle `plugin_commands`
--
ALTER TABLE `plugin_commands`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;
--
-- AUTO_INCREMENT für Tabelle `plugin_credits`
--
ALTER TABLE `plugin_credits`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11585;
--
-- AUTO_INCREMENT für Tabelle `plugin_filters`
--
ALTER TABLE `plugin_filters`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7570;
--
-- AUTO_INCREMENT für Tabelle `plugin_loyality`
--
ALTER TABLE `plugin_loyality`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;
--
-- AUTO_INCREMENT für Tabelle `plugin_quotes`
--
ALTER TABLE `plugin_quotes`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;
--
-- AUTO_INCREMENT für Tabelle `plugin_ranks`
--
ALTER TABLE `plugin_ranks`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11645;
--
-- AUTO_INCREMENT für Tabelle `plugin_stats`
--
ALTER TABLE `plugin_stats`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=567;
--
-- AUTO_INCREMENT für Tabelle `plugin_timers`
--
ALTER TABLE `plugin_timers`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT für Tabelle `regulars`
--
ALTER TABLE `regulars`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT für Tabelle `users`
--
ALTER TABLE `users`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `archive`
--
ALTER TABLE `archive`
  ADD CONSTRAINT `STATS_BOTID` FOREIGN KEY (`BOTID`) REFERENCES `bots` (`ID`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `STATS_CHANNELID` FOREIGN KEY (`CHANNELID`) REFERENCES `channels` (`ID`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints der Tabelle `channels`
--
ALTER TABLE `channels`
  ADD CONSTRAINT `CHANNELS_BOTID` FOREIGN KEY (`BOTID`) REFERENCES `bots` (`ID`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints der Tabelle `commands`
--
ALTER TABLE `commands`
  ADD CONSTRAINT `COMMANDS_BOTID` FOREIGN KEY (`BOTID`) REFERENCES `bots` (`ID`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `COMMANDS_CHANNELID` FOREIGN KEY (`CHANNELID`) REFERENCES `channels` (`ID`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `COMMANDS_PLUGINID` FOREIGN KEY (`PLUGINID`) REFERENCES `plugins` (`ID`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints der Tabelle `config`
--
ALTER TABLE `config`
  ADD CONSTRAINT `CONFIG_BOTID` FOREIGN KEY (`BOTID`) REFERENCES `bots` (`ID`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `CONFIG_CHANNELID` FOREIGN KEY (`CHANNELID`) REFERENCES `channels` (`ID`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `CONFIG_COMMANDID` FOREIGN KEY (`COMMANDID`) REFERENCES `commands` (`ID`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `CONFIG_PLUGINID` FOREIGN KEY (`PLUGINID`) REFERENCES `plugins` (`ID`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints der Tabelle `locale`
--
ALTER TABLE `locale`
  ADD CONSTRAINT `LOCALE_BOTID` FOREIGN KEY (`BOTID`) REFERENCES `bots` (`ID`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `LOCALE_CHANNELID` FOREIGN KEY (`CHANNELID`) REFERENCES `channels` (`ID`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints der Tabelle `plugins`
--
ALTER TABLE `plugins`
  ADD CONSTRAINT `PLUGINS_BOTID` FOREIGN KEY (`BOTID`) REFERENCES `bots` (`ID`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `PLUGINS_CHANNELID` FOREIGN KEY (`CHANNELID`) REFERENCES `channels` (`ID`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints der Tabelle `plugin_commands`
--
ALTER TABLE `plugin_commands`
  ADD CONSTRAINT `PLUGINS_COMMANDS_BOTID` FOREIGN KEY (`BOTID`) REFERENCES `bots` (`ID`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `PLUGINS_COMMANDS_CHANNELID` FOREIGN KEY (`CHANNELID`) REFERENCES `channels` (`ID`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints der Tabelle `plugin_credits`
--
ALTER TABLE `plugin_credits`
  ADD CONSTRAINT `PLUGIN_CREDITS_BOTID` FOREIGN KEY (`BOTID`) REFERENCES `bots` (`ID`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `PLUGIN_CREDITS_CHANNELID` FOREIGN KEY (`CHANNELID`) REFERENCES `channels` (`ID`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints der Tabelle `plugin_filters`
--
ALTER TABLE `plugin_filters`
  ADD CONSTRAINT `PLUGIN_FILTERS_BOTID` FOREIGN KEY (`BOTID`) REFERENCES `bots` (`ID`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `PLUGIN_FILTERS_CHANNELID` FOREIGN KEY (`CHANNELID`) REFERENCES `channels` (`ID`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints der Tabelle `plugin_loyality`
--
ALTER TABLE `plugin_loyality`
  ADD CONSTRAINT `PLUGIN_LOYALITY_BOTID` FOREIGN KEY (`BOTID`) REFERENCES `bots` (`ID`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `PLUGIN_LOYALITY_CHANNEL_ID` FOREIGN KEY (`CHANNELID`) REFERENCES `channels` (`ID`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints der Tabelle `plugin_quotes`
--
ALTER TABLE `plugin_quotes`
  ADD CONSTRAINT `PLUGIN_QUOTES_BOTID` FOREIGN KEY (`BOTID`) REFERENCES `bots` (`ID`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `PLUGIN_QUOTES_CHANNELID` FOREIGN KEY (`CHANNELID`) REFERENCES `channels` (`ID`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints der Tabelle `plugin_ranks`
--
ALTER TABLE `plugin_ranks`
  ADD CONSTRAINT `PLUGIN_RANKS_BOTID` FOREIGN KEY (`BOTID`) REFERENCES `bots` (`ID`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `PLUGIN_RANKS_CHANNELID` FOREIGN KEY (`CHANNELID`) REFERENCES `channels` (`ID`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints der Tabelle `plugin_stats`
--
ALTER TABLE `plugin_stats`
  ADD CONSTRAINT `PLUGIN_STATS_BOTID` FOREIGN KEY (`BOTID`) REFERENCES `bots` (`ID`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `PLUGIN_STATS_CHANNELID` FOREIGN KEY (`CHANNELID`) REFERENCES `channels` (`ID`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints der Tabelle `plugin_timers`
--
ALTER TABLE `plugin_timers`
  ADD CONSTRAINT `PLUGIN_TIMERS_BOTID` FOREIGN KEY (`BOTID`) REFERENCES `bots` (`ID`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `PLUGIN_TIMERS_CHANNELID` FOREIGN KEY (`CHANNELID`) REFERENCES `channels` (`ID`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints der Tabelle `regulars`
--
ALTER TABLE `regulars`
  ADD CONSTRAINT `SUPERUSERS_BOTID` FOREIGN KEY (`BOTID`) REFERENCES `bots` (`ID`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `SUPERUSERS_CHANNELID` FOREIGN KEY (`CHANNELID`) REFERENCES `channels` (`ID`) ON DELETE CASCADE ON UPDATE NO ACTION;
