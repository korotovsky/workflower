# encoding: utf-8

namespace :composer do
    after :install, "symfony:build_bootstrap"
end
