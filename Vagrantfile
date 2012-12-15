# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant::Config.run do |config|
  config.vm.box = "php-ci"
  config.vm.box_url = "http://static.jubianchi.fr/boxes/php-ci.box"

  config.vm.customize ["modifyvm", :id, "--memory", 1024]

  config.vm.forward_port 8080, 8181
  config.vm.forward_port 9000, 9001
end
