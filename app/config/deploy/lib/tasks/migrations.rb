# encoding: utf-8

namespace :deploy do
    namespace :migrations do
        desc 'Run Doctrine 2 migrations'
        task :migrate do
            on roles(:db) do
                within release_path do
                    hasNew = capture(:php, fetch(:symfony_console_path), 'doctrine:migrations:status', '--no-ansi', "| grep 'New Migrations'  | awk '{ print $4 }'")
                    latest = capture(:php, fetch(:symfony_console_path), 'doctrine:migrations:status', '--no-ansi', "| grep 'Latest Version'  | awk '{ print $6 }' | sed -e 's/[()]//g'")

                    if "#{hasNew}".to_i > 0
                        execute :php, fetch(:symfony_console_path), 'doctrine:migrations:migrate', '--no-interaction --no-ansi'
                    end

                    execute :echo, latest, '> migrations.log'
                end
            end
        end

        desc 'Run Doctrine 2 migrations rollback'
        task :rollback do
            on roles(:db) do
                version = nil

                within release_path do
                    version = capture(:cat, 'migrations.log')
                end

                within current_path do
                    execute :php, fetch(:symfony_console_path), 'doctrine:migrations:migrate', version, '--no-interaction --no-ansi'
                end
            end
        end
    end

    before :updated,  'deploy:migrations:migrate'
    before :reverted, 'deploy:migrations:rollback'
end