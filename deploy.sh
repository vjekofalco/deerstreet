#!/bin/sh
# Environment variables and ssh-agent are configured in jenkins

set -e

# Configuration partly read from environment varibales in jenkins
: ${dpl_user?"You need to set dpl_user in jenkins"}
: ${dpl_host?"You need to set dpl_host in jenkins"}
: ${dpl_project?"You need to set dpl_project in jenkins"}
: ${dpl_path?"You need to set dpl_path in jenkins"}

dpl_user=$dpl_user
dpl_host=$dpl_host
dpl_project=$dpl_project
dpl_path=$dpl_path
dpl_to=$dpl_path/$dpl_project
dpl_revision=`date +%Y%m%d%H%M%S`

# Subroutines
run() {
	echo "*** Run: $1"
	ssh $dpl_user@$dpl_host $1
}

system() {
	echo "*** Run: $1"
	$1
}

# Tasks
# Ensure that the shared, releases and tmp directories exist
run "mkdir -vpm 755 $dpl_to/shared $dpl_to/releases $dpl_to/tmp"

# Check if shared resources exist
# run "[ -f $dpl_to/shared/config/autoload/oauth2.stage.local.php ]"

# Transfer latest build
system "rsync -avz --delete ./* $dpl_user@$dpl_host:$dpl_to/tmp"

# Create new release directory
run "mkdir -vpm 755 $dpl_to/releases/$dpl_revision"

# Move latest build from tmp to new release directory
run "cp -R $dpl_to/tmp/* $dpl_to/releases/$dpl_revision"

# Change permissions
#run "find $dpl_to -type d | xargs chmod 755"
#run "find $dpl_to -type f | xargs chmod 644"

# Symlink shared resources
run "ln -s $dpl_to/shared/excelPlace $dpl_to/releases/$dpl_revision/excelPlace"

# Publish
run "rm -rf $dpl_to/current"
run "ln -s $dpl_to/releases/$dpl_revision $dpl_to/current"

# Clean old releases
run "(ls -td -1 $dpl_to/releases/* | head -n 5 ; ls -td -1 $dpl_to/releases/*) | sort | uniq -u | xargs rm -rf"
