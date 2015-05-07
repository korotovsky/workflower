# config valid only for current version of Capistrano
lock '3.4.0'

set :application,           'workflower'
set :pty,                   true

set :app_path,              'app'
set :web_path,              'web'

set :scm,                   :git
set :repo_url,              ENV.fetch('REPO_URL', 'git@github.com:korotovsky/workflower.git')

set :branch,                ENV.fetch('BRANCH', 'master')

set :composer_install_flags, '--no-scripts --no-dev --no-interaction --optimize-autoloader'
set :composer_roles,        :app

set :format,                :pretty
set :log_level,             :debug

set :ssh_options,           { :forward_agent => true, :port => 22 }
set :keep_releases,         5

set :linked_files,  fetch(:linked_files, []).push(
    fetch(:app_path) + '/config/parameters.yml'
)
set :linked_dirs,   fetch(:linked_dirs, []).push(
    fetch(:app_path) + '/logs',
)

namespace :deploy do
    after :finishing, 'deploy:cleanup'
end
