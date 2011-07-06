#!/bin/sh
json=`cat mergy.json | sed -e 's/[{}]/''/g' | awk -v k="text" '{n=split($0,a,","); for (i=1; i<=n; i++) print a[i]}'`
remote=`echo $json | grep remote | awk -F'"' {'print $4'}`

php mergy.php $@
