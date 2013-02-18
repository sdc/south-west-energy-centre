if ENV['environment'] == "production"
    set :application, "swec"
    role :app,  "energycentre.southdevon.ac.uk"
    role :web,  "energycentre.southdevon.ac.uk"
    role :db,   "energycentre.southdevon.ac.uk", :primary => true

#    role :app,  "southwestenergycentre.com"
#    role :web,  "southwestenergycentre.com"
#    role :db,   "southwestenergycentre.com", :primary => true

#    role :app,  "212.219.197.14"
#    role :web,  "212.219.197.14"
#    role :db,   "212.219.197.14", :primary => true

else
    set :application, "swec_dev"
    role :app,  "webdev.southdevon.ac.uk"
    role :web,  "webdev.southdevon.ac.uk"
    role :db,   "webdev.southdevon.ac.uk", :primary => true
end

default_run_options[:pty] = true

set :repository,"git@github.com:sdc/south-west-energy-centre.git"
set :branch,    "master"
set :deploy_to, "/srv/#{application}"
set :scm, :git

namespace :deploy do
    %W(start stop restart migrate finalize_update).each do |event|
        task event do
            # don't
        end
    end
end

# PV
#task :after_deploy do
# BC
after "deploy:create_symlink" do
  run "cp #{shared_path}/configuration.php #{current_path}/"
  run "cp #{shared_path}/includes/connection.php #{current_path}/includes/connection.php"
  run "cp #{shared_path}/.htaccess #{current_path}/"
  ["images","attachments"].each do |d|
    run "rm -rvf #{current_path}/#{d}"
    run "ln -s #{shared_path}/#{d} #{current_path}/#{d}"
  end
  run "chmod a+rw -R #{current_path}"
end
