# Simple Role Syntax
# ==================
# Supports bulk-adding hosts to roles, the primary server in each group
# is considered to be the first unless any hosts have the primary
# property set.  Don't declare `role :all`, it's a meta role.

role :app, ENV.fetch('DEPLOY_APP_HOST')
role :web, ENV.fetch('DEPLOY_APP_HOST')
role :db,  ENV.fetch('DEPLOY_APP_HOST')


# Extended Server Syntax
# ======================
# This can be used to drop a more detailed server definition into the
# server list. The second argument is a, or duck-types, Hash and is
# used to set extended properties on the server.

server ENV.fetch('DEPLOY_APP_HOST'), user: ENV.fetch('DEPLOY_APP_USER'), roles: %w{app web db}


# Custom Options
# ==================
# For prod: set :deploy_to,     '/data/www/#{fetch(:application)}_#{fetch(:symfony_env)}'

set :symfony_env,           'prod'
set :deploy_to,             "/data/www/#{fetch(:application)}_#{fetch(:symfony_env)}"
set :controllers_to_clear,  ['app_dev.php', 'app_staging.php', 'app_test.php']
