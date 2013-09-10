app_root = ENV['RESQUE_APP_ROOT']
app_root ||= "/mnt/apci/usarugbystats"
num_workers = 5

num_workers.times do |num|
  God.watch do |w|
    w.dir      = "#{app_root}"
    w.name     = "resque-#{num}"
    w.group    = 'resque'
    w.interval = 30.seconds
    w.env      = {'QUEUE' => 'get_team_players,get_player_roles,create_game,update_game_score,get_group_above,get_groups,get_group', 'APP_INCLUDE' => 'autoload.php'}
    w.start    = "php bin/resque"

    # restart if memory gets too high
    w.transition(:up, :restart) do |on|
      on.condition(:memory_usage) do |c|
        c.above = 350.megabytes
        c.times = 2
      end
    end

    # determine the state on startup
    w.transition(:init, { true => :up, false => :start }) do |on|
      on.condition(:process_running) do |c|
        c.running = true
      end
    end

    # determine when process has finished starting
    w.transition([:start, :restart], :up) do |on|
      on.condition(:process_running) do |c|
        c.running = true
        c.interval = 5.seconds
      end

      # failsafe
      on.condition(:tries) do |c|
        c.times = 5
        c.transition = :start
        c.interval = 5.seconds
      end
    end

    # start if process is not running
    w.transition(:up, :start) do |on|
      on.condition(:process_running) do |c|
        c.running = false
      end
    end
  end
end
