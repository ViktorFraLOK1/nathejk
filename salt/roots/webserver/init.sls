# Will install Apache2 & PHP5 in the Virtual machine
apache2:
    pkg:
        - installed
    service:
        - running
        - watch:
            - pkg: apache2
            - file: /etc/apache2/sites-enabled/virtualhost.conf
            - file: /etc/apache2/apache2.conf
    cmd.run:
        - names:
            - a2enmod rewrite
            - a2enmod vhost_alias
        - unless: ls -al /etc/apache2/mods-enabled/ | grep -q rewrite
        - watch_in:
            - service: apache2

php:
    pkg.installed:
        - names:
            - libapache2-mod-php5
            - php5
            - php5-curl
            - php5-mysql
            - php5-mcrypt
            - php-pear
    cmd.run:
        - name: php5enmod mcrypt
        - unless: ls -al /etc/php5/apache2/conf.d/ | grep -q mcrypt

pear-mail:
    cmd.run:
        - name: pear install pear/Mail
        - unless: pear list | grep -q Mail
        - require:
            - pkg: php-pear

pear-db:
    cmd.run:
        - name: pear install pear/DB
        - unless: pear list | grep -q DB
        - require:
            - pkg: php-pear

pear-http:
    cmd.run:
        - name: pear install pear/HTTP
        - unless: pear list | grep -q HTTP
        - require:
            - pkg: php-pear

pear-url2:
    cmd.run:
        - name: pear install pear/Net_URL2
        - unless: pear list | grep -q Net_URL2
        - require:
            - pkg: php-pear

/etc/apache2/sites-enabled/virtualhost.conf:
    file.managed:
        - source: salt://webserver/virtualhost.conf
        - user: root
        - group: root
        - mode: 644

/etc/apache2/apache2.conf:
    file.managed:
        - source: salt://webserver/apache2.conf
        - user: root
        - group: root
        - mode: 644

