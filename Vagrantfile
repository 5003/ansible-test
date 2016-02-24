required_plugins = %w{
  sahara
  vagrant-cachier
}
required_plugins.each do |plugin|
  system "vagrant plugin install #{plugin}" unless Vagrant.has_plugin? plugin
end
Vagrant.configure(2) do |config|
  config.vm.box_check_update = false
  if Vagrant.has_plugin?("vagrant-cachier")
    config.cache.scope = :machine
  end
  config.vm.define "dev" do |devenv|
    devenv.vm.box = "dev"
    devenv.vm.box_url = "file:///C:/Users/Public/boxes/opscode_centos-7.2_chef-provisionerless.box"
    devenv.vm.network "private_network", ip: "88.88.88.88"
    devenv.vm.synced_folder "..", "/vf"
  end
end