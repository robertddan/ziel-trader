#!/bin/bash

git config --global user.email "danrobert445@gmail.com"
git config --global user.name "Robert-Dan"

git add .
git status

read -p "Enter your commit message: " message
git commit -m "${message:=default value}"

#git remote add origin https://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@github.com/johnidevo/action.git

git remote add github https://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@github.com/johnidevo/roboziel.git
git remote add bucket https://robertddan:ATBBLeBCEAjMgjFkDehDxk2Bm8qe48C28E13@bitbucket.org/robo-workspace/roboziel-scrum.git

git push origin master
#git pull origin master
