#!/bin/bash

git config --global user.email "danrobert445@gmail.com"
git config --global user.name "Robert-Dan"

git add .
git status

#"${FOO:=default value}"
read -p "Enter your commit message: " message
git commit -m "${message:=default value}"


git remote add origin ...
git push origin master

#git pull origin master
