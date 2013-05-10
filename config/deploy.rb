set :application, "USA Rugby Stats"
set :repository, "git@github.com:AllPlayers/USARugby-Stats.git"
set :branch, "origin/master"
set :deploy_to, "/mnt/apci"
role :web, "ap01.allplayers.com", "ap02.allplayers.com"
role :app, "ap01.allplayers.com", "ap02.allplayers.com"

set :migrate_target,  :current_usarugbystats
set(:latest_release)  { fetch(:current_path) }
set(:release_path)    { fetch(:current_path) }
set(:current_release) { fetch(:current_path) }

set(:current_revision)  { capture("cd #{current_path}; git rev-parse --short HEAD").strip }
set(:latest_revision)   { capture("cd #{current_path}; git rev-parse --short HEAD").strip }
set(:previous_revision) { capture("cd #{current_path}; git rev-parse --short HEAD@{1}").strip }

# if you want to clean up old releases on each deploy uncomment this:
# after "deploy:restart", "deploy:cleanup"
namespace :deploy do
  desc "Deploy your application"
  task :default do
    update
    restart
  end

  desc "Setup your git-based deployment app"
  task :setup, :except => { :no_release => true } do
    dirs = [deploy_to, shared_path]
    dirs += shared_children.map { |d| File.join(shared_path, d) }
    run "#{try_sudo} mkdir -p #{dirs.join(' ')} && #{try_sudo} chmod g+w #{dirs.join(' ')}"
    run "git clone #{repository} #{current_path}"
    run "ln -sf #{shared_path}/config.php #{current_path}/app/config.php"
    run "ln -sf #{current_path} /mnt/apci/usarugbystats"
    composer.install
    start
    start_god
  end

  task :update do
    transaction do
      update_code
    end
  end

  desc "Update the deployed code."
  task :update_code, :except => { :no_release => true } do
    run "cd #{current_path}; git fetch origin; git reset --hard #{branch}"
    finalize_update
  end

  desc "Update the database (overwritten to avoid symlink)"
  task :migrations do
    transaction do
      update_code
    end
    run "cd #{current_path}; bin/doctrine migrations:migrate -n"
    restart
  end

  task :finalize_update, :except => { :no_release => true } do
    run "chmod -R g+w #{latest_release}" if fetch(:group_writable, true)
    run <<-CMD
      ln -sf #{shared_path}/config.php #{current_path}/app/config.php
    CMD
  end
  
  desc "Stop god monitor"
  task :stop_god_monitor do
    run "cd #{current_path}; god terminate"
  end

  desc "Restart resque monitor"
  task :restart_resque_monitor do
    run "cd #{current_path}; god restart resque"
  end
  
  desc "Start god monitor"
  task :start_god do
    run "cd #{current_path}; god -c config/resque.god"
  end

  desc "Clear apc cache"
  task :reset_cache do
    run "wget -O - -q -t 1 http://localhost:8080/sites/scripts/apc_clear.php"
  end
  
  namespace :rollback do
    desc "Moves the repo back to the previous version of HEAD"
    task :repo, :except => { :no_release => true } do
      set :branch, "HEAD@{1}"
      deploy.default
    end

    desc "Rewrite reflog so HEAD@{1} will continue to point to at the next previous release."
    task :cleanup, :except => { :no_release => true } do
      run "cd #{current_path}; git reflog delete --rewrite HEAD@{1}; git reflog delete --rewrite HEAD@{1}"
    end

    desc "Rolls back to the previously deployed version."
    task :default do
      rollback.repo
      rollback.cleanup
    end
  end

  namespace :composer do
    desc "Install composer dependencies"
    task :install do
      run "cd #{current_path}; curl -s http://getcomposer.org/installer | php && ./composer.phar install"
    end

    desc "Update composer dependencies"
    task :update do
      run "cd #{current_path}; rm -rf vendor; rm composer.lock; curl -s http://getcomposer.org/installer | php && ./composer.phar update"
    end

  end
end

namespace :destroy do
  desc "Remove current app directory."
  task :remove_dir do
    run "cd #{deploy_to}; rm -rf current_usarugbystats"
  end
end

after "deploy", "deploy:restart_resque_monitor", "deploy:reset_cache"
