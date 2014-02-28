Vagrant.configure("2") do |config|
    config.vm.box = "precise32"
    config.vm.box_url = "http://files.vagrantup.com/precise32.box"
    config.vm.hostname = 'phpswitch'

    config.vm.provider "virtualbox" do |vbox|
        vbox.customize ["modifyvm", :id, "--memory", 1024]
        vbox.customize ["modifyvm", :id, "--cpus", 2]
        vbox.customize ["modifyvm", :id, "--natdnshostresolver1", "on"]
    end

    config.vm.provision :shell, :inline => 'sudo apt-get install php5-cli php5-curl git build-essential libxml2-dev'
    config.vm.provision :shell, :inline => 'sudo sed -i "s/;phar.readonly = On/phar.readonly = Off/" /etc/php5/cli/php.ini'
end
