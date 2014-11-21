VAGRANTFILE_API_VERSION = "2"

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|
  # Configure the box
  config.vm.box = "ubuntu/trusty64"
  config.vm.hostname = "PizzeriaDev"

  # Configure a private network IP
  config.vm.network :private_network, ip: "192.168.10.10"

  # Configure a few VirtualBox settings
  config.vm.provider "virtualbox" do |vb|
    vb.name = 'pizzeria'
    vb.customize ["modifyvm", :id, "--memory", "2048"]
    vb.customize ["modifyvm", :id, "--cpus", "1"]
    vb.customize ["modifyvm", :id, "--natdnsproxy1", "on"]
    vb.customize ["modifyvm", :id, "--natdnshostresolver1", "on"]
    vb.customize ["modifyvm", :id, "--ostype", "Ubuntu_64"]
  end

  # Configure port Ffrwarding to the box
  config.vm.network "forwarded_port", guest: 80, host: 8000
  config.vm.network "forwarded_port", guest: 443, host: 44300
  config.vm.network "forwarded_port", guest: 3306, host: 33060
  config.vm.network "forwarded_port", guest: 5432, host: 54320
  config.vm.network "forwarded_port", guest: 35729, host: 35729

  # Configure the public key for SSH access
  config.vm.provision "shell" do |s|
    s.inline = "echo $1 | tee -a /home/vagrant/.ssh/authorized_keys"
    s.args = [File.read(File.expand_path("~/.ssh/id_rsa.pub"))]
  end

  # Copy the SSH private key to the box
  config.vm.provision "shell" do |s|
    s.privileged = false
    s.inline = "echo \"$1\" > /home/vagrant/.ssh/$2 && chmod 600 /home/vagrant/.ssh/$2"
    s.args = [File.read(File.expand_path("~/.ssh/id_rsa")), 'id_rsa']
  end

  # Register the configured shared folders
  config.vm.synced_folder Dir.pwd, "/home/vagrant/groupeat"

  # Run the base provisioning script
  config.vm.provision "shell", path: "./server/provision.sh"

  # Install the configured Nginx site
  config.vm.provision "shell", path: "./server/nginx.sh"
end
