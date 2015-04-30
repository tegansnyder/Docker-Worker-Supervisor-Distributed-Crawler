#!/usr/bin/env bash

[ $(id -u) = 0 ] || { echo "You must be root (or use 'sudo')" ; exit 1; }

fwrule=`ipfw -a list | grep "deny ip from any to any"`
fwrule_id=`echo $fwrule | awk '{ print $1 }'`
if [ "$fwrule" != "" ]; then
    echo "Found blocking firewall rule: $(tput setaf 1)${fwrule}$(tput sgr0)"
    printf "Deleting rule ${fwrule_id} ... "
    ipfw delete ${fwrule_id}
    if [ $? == 0 ]; then
	echo "$(tput setaf 2)[OK]$(tput sgr0)"
    else
    	echo "$(tput setaf 1)[FAIL]$(tput sgr0)"
        exit 1
    fi
else
    echo "No rules found. You are good to go"
fi

docker_interface=$(sudo -u $(logname) VBoxManage showvminfo boot2docker-vm | grep -o -E 'vboxnet\d\d?')
if [ -z "${docker_interface}" ]; then
    echo "No docker VM found!"
    exit 1
else
    echo "Found docker interface at $(tput setaf 1)${docker_interface}$(tput sgr0). Changing routes ..."

    current_route=$(netstat -rn | grep 192.168.59)
    if [ -z "${current_route}" ]; then
        # no route, let's add it!
        route -nv add -net 192.168.59 -interface ${docker_interface}
    else
        route -nv change -net 192.168.59 -interface ${docker_interface}
    fi

    if [ $? == 0 ]; then
        echo "$(tput setaf 2)[OK]$(tput sgr0)"
    else
        echo "$(tput setaf 1)[FAIL]$(tput sgr0)"
        exit 1
    fi
fi
