# Contents of Vagrantfile
Vagrant.configure("2") do |config|
    ## Chose your base box
    config.vm.box = "trusty64"
    #config.vm.box_url = "http://files.vagrantup.com/precise64.box"
    config.vm.box_url = "https://cloud-images.ubuntu.com/vagrant/trusty/current/trusty-server-cloudimg-amd64-vagrant-disk1.box"

    config.vm.hostname = "dev.nathejk.dk"
    config.vm.network :private_network, ip: "192.168.50.3"

    ## For masterless, mount your salt file root
    config.vm.synced_folder "salt/roots/", "/srv/salt/"

    ## Use all the defaults:
    #config.vm.provision :shell, :path => "vagrant/bootstrap.sh"
    config.vm.provision :salt do |salt|
        salt.minion_config = "salt/minion"
        salt.run_highstate = true
    end
    
    config.vm.provider :virtualbox do |vb|
        # Don't boot with headless mode
        # vb.gui = true

        # Use VBoxManage to customize the VM. For example to change memory:
        vb.customize ["modifyvm", :id, "--memory", "2048"]

        # No matter how much CPU is used in the VM, no more than 95% would be used on your own host machine.
        vb.customize ["modifyvm", :id, "--cpuexecutioncap", "95"]
    end
end
