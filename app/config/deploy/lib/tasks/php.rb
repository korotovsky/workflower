# encoding: utf-8

namespace :php do
    desc 'Stop php5-fpm service'
    task :stop do
        on roles :web do
            execute :sudo, 'service', 'php5-fpm', 'stop', "; true"
        end
    end

    desc 'Start php5-fpm service'
    task :start do
        on roles :web do
            execute :sudo, 'service', 'php5-fpm', 'start', "; true"
        end
    end

    desc 'Restart php5-fpm service'
    task :restart do
        on roles :web, in: :sequence, wait: 1 do
            execute :sudo, 'service', 'php5-fpm', 'restart', "; true"
        end
    end
end

namespace :deploy do
    after :published, 'php:restart'
end