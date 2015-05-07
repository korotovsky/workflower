set :stage_config_path, 'app/config/deploy'
set :deploy_config_path, 'app/config/deploy.rb'

# Load DSL and set up stages
require 'capistrano/setup'

# Include default deployment tasks
require 'capistrano/deploy'
require 'capistrano/console'
require 'capistrano/symfony'

# Include additional plugins
load 'app/config/deploy/lib/tasks/php.rb'
load 'app/config/deploy/lib/tasks/composer.rb'
load 'app/config/deploy/lib/tasks/migrations.rb'

load 'app/config/deploy.rb'
