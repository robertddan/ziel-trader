#!/bin/bash

git config --global user.email "danrobert445@gmail.com"
git config --global user.name "Robert-Dan"

git add .
git status

read -p "Enter your commit message: " message
git commit -m "${message:=default value}"

#git remote add origin https://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@github.com/johnidevo/action.git
#git remote add origin https://robertddan:ATBBLeBCEAjMgjFkDehDxk2Bm8qe48C28E13@bitbucket.org/robo-workspace/roboziel.git

git remote add origin https://robertddan:ATBBLeBCEAjMgjFkDehDxk2Bm8qe48C28E13@bitbucket.org/robo-workspace/zieltrader-scrum.git

git push origin master
#git pull origin master
