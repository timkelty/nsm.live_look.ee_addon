#!/bin/bash

# This script creates symlinks from the local GIT repo into your EE2 install. It also copies some of the extension icons.

dirname=`dirname "$0"`

echo "
You are about to create symlinks for NSM Live Look
--------------------------------------------------

The symlinks use absolute paths so they are for *development purposes only*.

The following directories must be writable:

system/expressionengine/third_party

Enter the full path to your ExpressionEngine 2 system folder without a trailing slash [ENTER]:
"
read ee_system_folder

cd "$dirname"
echo "Changed working directory to $dirname"
ln -s "$dirname/system/expressionengine/third_party/nsm_live_look" "$ee_system_folder/expressionengine/third_party/nsm_live_look"
echo "Linked \"$ee_system_folder/expressionengine/third_party/nsm_live_look\""

