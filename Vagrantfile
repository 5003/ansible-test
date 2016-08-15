required_plugins = %w{
  sahara
  vagrant-cachier
}
required_plugins.each do |plugin|
  system "vagrant plugin install #{plugin}" unless Vagrant.has_plugin? plugin
end
$profile_script = <<SCRIPT
    BASH_PROFILE_PATH=/etc/profile.d/x_5003_profile.sh
    export BASH_PROFILE_PATH
    wget -qO- https://github.com/5003/config/raw/master/setup.sh | bash
SCRIPT
$devenv_script = <<SCRIPT
      if type direnv &> /dev/null
        then
        type ~/.direnvrc || ln -s /vf/.direnvrc ~/
      fi
SCRIPT
Vagrant.configure(2) do |config|
  config.vm.box_check_update = false
=begin
  if Vagrant.has_plugin?("vagrant-cachier")
    config.cache.scope = :machine
  end
=end
  config.vm.box = "bento/centos-7.2"
  config.vm.box_url = "file:///C:/Users/Public/boxes/opscode_centos-7.2_chef-provisionerless.box"
  config.vm.define :dev do |devenv|
    devenv.vm.network "private_network", ip: "88.88.88.88"
    devenv.vm.synced_folder ".", "/vagrant"
    devenv.vm.synced_folder ".", "/vagrant_win", :mount_options => ["dmode=777", "fmode=666"]
    devenv.vm.synced_folder "..", "/vf"
    devenv.vm.provision :shell, inline: $devenv_script
  end
  config.vm.define :ubu do |ubuntu|
    ubuntu.vm.box = "bento/ubuntu-16.04"
    ubuntu.vm.box_url = "file:///C:/Users/Public/boxes/opscode_ubuntu-16.04_chef-provisionerless.box"
    ubuntu.vm.network "private_network", ip: "77.77.77.77"
  end
  config.vm.provision :shell, inline: $profile_script
  config.vm.provider :virtualbox do |vbox|
    vbox.memory = ENV['VBOX_MEMORY'] || 480
    vbox.cpus = ENV['VBOX_CPUS'] || 2
    vbox.customize ["modifyvm", :id, "--nictype1", "virtio"]
    vbox.customize ["modifyvm", :id, "--nictype2", "virtio"]
  end
end