VAGRANTFILE_API_VERSION = "2"

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|

  # Configure the Virtualbox provider for development
  config.vm.provider "virtualbox" do |vb|
    vb.customize ["modifyvm", :id, "--memory", "2048"]
    vb.customize ["modifyvm", :id, "--cpus", "1"]
    vb.customize ["modifyvm", :id, "--natdnsproxy1", "on"]
    vb.customize ["modifyvm", :id, "--natdnshostresolver1", "on"]
    vb.customize ["modifyvm", :id, "--ostype", "Ubuntu_64"]
  end

  # Register the configured shared folders
  config.vm.synced_folder Dir.pwd, "/home/vagrant/groupeat/current"

  # Configure the box
  config.vm.box = "ubuntu/trusty64"
  config.vm.hostname = "PizzeriaDev"

  # Configure a private network IP
  config.vm.network :private_network, ip: "192.168.10.10"

  # Configure port Forwarding to the box
  config.vm.network "forwarded_port", guest: 80, host: 8000
  config.vm.network "forwarded_port", guest: 443, host: 44300
  config.vm.network "forwarded_port", guest: 5432, host: 54320

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

  # Copy the Git config into the VM
  config.vm.provision :file, source: '~/.gitconfig', destination: '/home/vagrant/.gitconfig' if File.exist?(ENV['HOME'] + '/.gitconfig')

  # Run the base provisioning script
  config.vm.provision "shell" do |s|
    domain = 'groupeat.dev'
    postgresPassword = 'groupeat'
    s.path = "./scripts/provision.sh"
    s.args = [domain, postgresPassword]
  end

  # Install the project Composer dependencies
  config.vm.provision "shell", inline: "cd ~vagrant/groupeat/current; /usr/local/bin/composer install"
end
