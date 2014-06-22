
phpmyadmin:
    pkg.installed:
        - require:
            - pkg: apache2
            - sls: database

/etc/phpmyadmin/conf.d/autologon.php:
    file.managed:
        - source: salt://database/phpmyadmin-config.php
        - user: root
        - group: root
        - mode: 644


/var/www/phpmyadmin.dev.nathejk.dk:
    file.symlink:
        - target: /usr/share/phpmyadmin
