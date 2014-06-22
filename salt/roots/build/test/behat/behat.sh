#!/bin/sh

cleanup()
{
    # kill sahi
    echo -n "Killing SAHI... ";
    kill $PID
    echo "done";

    # return error code of the last executed command.
    return $?
}

control_c()
{
  echo -en "\n*** Ouch! Exiting ***\n"
  cleanup
  exit $?
}
# trap keyboard interrupt (control-c)
#trap 'control_c' SIGINT

echo -n "Starting SAHI... ";
java net.sf.sahi.Proxy /home/vagrant/sahi /home/vagrant/sahi/userdata > /dev/null &
# pid of last executed command
PID=$!
echo "done [$PID]";

/usr/local/bin/behat $*
cleanup
