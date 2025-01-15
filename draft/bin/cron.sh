#!/bin/bash

for i in {1..2}
do

  if [ $(screen -ls | grep -o '[0-9]*\.sqlite' -c) -gt 1 ]; 
  then
    echo "killall sqlite -gt 1";
    killall screen;
  fi;

  if [ $(screen -ls | grep -o '[0-9]*\.stream' -c) -gt 1 ]; 
  then
    echo "killall stream -gt 1";
    killall screen;
  fi;

  screen -wipe;
  
  if [ $(screen -ls | grep -o '[0-9]*\.sqlite' -c) -gt 0 ]; 
  then
    echo "Process sqlite running ..." $(date '+%d/%m/%Y %H:%M:%S');
  else
    echo "Restart sqlite process " $(date '+%d/%m/%Y %H:%M:%S');
    screen -d -S sqlite -m /usr/local/Cellar/php/7.4.11/bin/php ~/Development/sticks/draft/bin/console app:oanda:sqlite
  fi;

  if [ $(screen -ls | grep -o '[0-9]*\.stream' -c) -gt 0 ]; 
  then
    echo "Process stream running ..." $(date '+%d/%m/%Y %H:%M:%S');
  else
    echo "Restart stream process " $(date '+%d/%m/%Y %H:%M:%S');
    screen -d -S stream -m /usr/local/Cellar/php/7.4.11/bin/php ~/Development/sticks/draft/bin/console app:oanda:stream
  fi;

  sleep 20

done
