# Will install Nathejk sites and dependencies
#        - unless: ls -al /etc/apache2/mods-enabled/ | grep -q rewrite
#            - console migrations:migrate
nathejk:
    cmd.run:
        - cwd: /vagrant
        - user: vagrant
        - names:
            - cd /vagrant/sites/dev.nathejk.dk && bower install
            - cd /vagrant/sites/tilmelding.nathejk.dk && bower install
            - cd /vagrant/sites/natpas.nathejk.dk && bower install
        - require:
            - sls: build

nathejk-db:
    cmd.run:
        - name: mysql nathejk < /vagrant/legacy/nathejk.sql
        - unless: mysqlshow nathejk | grep nathejk_
        - require:
            - sls: database

/var/www/dev.nathejk.dk:
  file.symlink:
    - target: /vagrant/sites/dev.nathejk.dk/web
    - require:
        - sls: webserver

/var/www/natpas.dev.nathejk.dk:
  file.symlink:
    - target: /vagrant/sites/natpas.nathejk.dk/web
    - require:
        - sls: webserver

/var/www/tilmelding.dev.nathejk.dk:
  file.symlink:
    - target: /vagrant/sites/tilmelding.nathejk.dk/web
    - require:
        - sls: webserver
