Workflower
==========================

This application allows you sync changes in Google Drive, Dropbox and Yandex.Disk to PivotalTracker as comments to stories with attachments and direct links to these files.

Useful if you want to transfer files from designers (PSD files, for example) to Frontend-developers automatically without any unnecessary communication.

You will have latest versions of files in your story with direct links for downloading them.

Installation
==========================

1) Create a Workflower with Composer:

`$ composer create-project korotovsky/workflower workflower`

2) Install dependencies:

`$ composer install`

3) Create database schema (note: for production you should use migrations):

`$ php app/console doctrine:schema:update --force`

4) Add the next lines to `/etc/crontab` and restart it.

`*  *    * * *   <USER>  cd <APP_PATH> && php app/console workflower:transfer:discover --env=prod`

`*  *    * * *   <USER>  cd <APP_PATH> && php app/console workflower:transfer:sync --env=prod`

The first command discovers changes in remote storages. E.g. Google Drive, Dropbox or Yandex.Disk.

The second command syncs known changes from remote storage to PivotalTracker.

Deployment
==========================

This project uses Capistrano 3 as a deployment system. To deploy your project just do these commands:

 1) `$ sudo bundle install`

 2) `$ bundle exec`

 3) `$ export DEPLOY_APP_HOST=123.123.123.123`

 4) `$ export DEPLOY_APP_USER=user`

 5) `$ export REPO_URL=git@github.com:korotovsky/workflower.git`

 6) `$ bundle exec cap production deploy BRANCH=master`

That's it!

What files will be synced?
==========================

All files which are placed to directory named `whateveryouwant_XXXXXXXX`
where `XXXXXXXX` is story no. in PivotalTracker.


Security note
==========================

1) There is no firewall to protect web frontend. If you want to protect it, you should ether implement your own firewall or use Basic Realm in your Web-server.

2) All files which are matched for syncing will be **published** to everyone (by known direct link) by changing security settings in Google Drive, Dropbox or Yandex.Disk.

3) Be careful, this application stores your access tokens for Google Drive, Dropbox and Yandex.Disk. And also has access tokens to PivotalTracker. Keep it safe!

Licence
==========================

Copyright (c) 2015 Dmitrii Korotovskii

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is furnished
to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
