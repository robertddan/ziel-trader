#!/bin/bash

git config --global user.email "danrobert445@gmail.com"
git config --global user.name "Robert-Dan"

git add .
git status

read -p "Enter your commit message: " message
git commit -m "${message:=default value}"

#git remote add github https://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@github.com/johnidevo/roboziel.git
#git remote add bucket https://robertddan:ATBBLeBCEAjMgjFkDehDxk2Bm8qe48C28E13@bitbucket.org/robo-workspace/roboziel-scrum.git

git push github master
git push bucket master
