#!/bin/bash
echo "delete .DS_Store"
find . -name ".DS_Store" -type f -print -delete

echo "delete ._.DS_Store"
find . -name "._.DS_Store" -type f -print -delete

echo "delete .fuse"
find . -name ".fuse_hidden*" -type f -print -delete

echo "delete .php-cs-fixer"
find . -name ".php-cs-fixer.cache" -type f -print -delete

echo "delete __MACOSX"
sudo rm -vRf **/__MACOSX


