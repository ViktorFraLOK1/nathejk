Development setup
=================

First time use
--------------
Install VirtualBox <https://www.virtualbox.org/wiki/Downloads> and Vagrant <http://downloads.vagrantup.com/>. First time 
you launch the image the environment (webserver etc) will be installed, this will take a while (~20 minuttes).

<TEST!>
Det var da den mindst forklarende brugervejledning jeg nogensinde har læst:p
</TEST!>


Everyday use
------------
Open a console, change to the base directory (the directory where this file is located) and type ``vagrant up`` this 
will launch the virtual image. Your development copy of the website is now available at <http://dev.nathejk.dk/>

Go ahead do some work!

After work
----------
When you are done working, you can either
  - ``vagrant suspend`` save running state and suspend virtual machine.
  - ``vagrant halt`` shuts down virtual machine preserving disk content.
  - ``vagrant destroy`` delete virtual machine.

