#!/usr/bin/env bash

ROOT=`realpath "$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"`

test=$(ping -c 4 www.stackoverflow.com | tail -1| awk '{print $4}' | cut -d '/' -f 2)

echo $(date +"%Y%m%d%H%M") ${test} >> ${ROOT}/pingtest$(date +"%Y%m%d").txt
