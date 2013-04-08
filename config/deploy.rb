set :application, "USA Rugby Stats"
set :repository, "git@github.com:AllPlayers/USARugby-Stats.git"
set :branch, "origin/master"
set :deploy_to, "/mnt/apci/usarugbystats"
role :web, "pdup-ap01.allplayers.com", "pdup-ap02.allplayers.com"
role :app, "pdup-ap01.allplayers.com", "pdup-ap02.allplayers.com"

set :migrate_target,  :current
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
    start
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
    migrate
    restart
  end

  task :finalize_update, :except => { :no_release => true } do
    run "chmod -R g+w #{latest_release}" if fetch(:group_writable, true)
    run <<-CMD
      ln -sf #{shared_path}/config.php #{latest_release}/app/config.php
    CMD
  end
  
  desc "Stop resque workers"
  task :stop_resque_workers do
    run "cd #{current_path}; rake workers:killall"
  end
  
  desc "Start god monitor"
  task :start_god do
    run "cd #{current_path}; god -c config/resque.god"
  end

  desc "Clear apc cache"
  task :reset_cache
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
end

after "deploy", "deploy:reset_cache"
