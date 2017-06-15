#!/usr/bin/env bash

test=$(ping -c 4 www.stackoverflow.com | tail -1| awk '{print $4}' | cut -d '/' -f 2)

echo $(date +"%Y%m%d%H%M") $test >> pingtest$(date +"%Y%m%d").txt
