required_plugins = %w{
  sahara
  vagrant-cachier
}
required_plugins.each do |plugin|
  system "vagrant plugin install #{plugin}" unless Vagrant.has_plugin? plugin
end
Vagrant.configure(2) do |config|
  config.vm.box_check_update = false
=begin
  if Vagrant.has_plugin?("vagrant-cachier")
    config.cache.scope = :machine
  end
=end
  config.vm.define "dev" do |devenv|
    devenv.vm.box = "bento/centos-7.2"
    devenv.vm.box_url = "file:///C:/Users/Public/boxes/opscode_centos-7.2_chef-provisionerless.box"
    devenv.vm.network "private_network", ip: "88.88.88.88"
    devenv.vm.synced_folder ".", "/vagrant"
    devenv.vm.synced_folder ".", "/vagrant_win", :mount_options => ["dmode=777", "fmode=666"]
    devenv.vm.synced_folder "..", "/vf"
  end
  config.vm.provision :shell do |setup|
    setup.path = "https://github.com/5003/config/raw/master/setup.sh"
  end
  config.vm.provider :virtualbox do |vbox|
    vbox.memory = ENV['VBOX_MEMORY'] || 480
    vbox.cpus = ENV['VBOX_CPUS'] || 2
    vbox.customize ["modifyvm", :id, "--nictype1", "virtio"]
    vbox.customize ["modifyvm", :id, "--nictype2", "virtio"]
  end
end