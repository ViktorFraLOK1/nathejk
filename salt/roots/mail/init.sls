# Will install Apache2 & PHP5 in the Virtual machine
postfix:
    pkg:
        - installed
    service:
        - running
        - watch:
            - pkg: postfix
            - file: /etc/postfix/main.cf

dovecot-imapd:
    pkg:
        - installed
    service:
        - name: dovecot
        - running

webmail:
    pkg.installed:
        - names:
            - roundcube
            - roundcube-plugins
            - roundcube-mysql
            - mailutils
    user.present:
        - name: mailrecipient
        - password: $6$4IpKoB0i$dXblZ2fomGIjsInI8XvYot3cpyd4h1AkymRkI4Upnh3QvI8O66cMp.D/T.27IO7nMIoaEKQ99Zh8rxbc81/lb1

/etc/postfix/main.cf:
    file.managed:
        - source: salt://mail/main.cf
        - user: root
        - group: root
        - mode: 644

/etc/postfix/transport.db:
    file.managed:
        - source: salt://mail/transport.db
        - user: root
        - group: root
        - mode: 644

/usr/share/roundcube/plugins/autologon/autologon.php:
    file.managed:
        - source: salt://mail/autologon.php
        - user: root
        - group: root
        - mode: 644

/etc/roundcube/main.inc.php:
    file.managed:
        - source: salt://mail/main.inc.php
        - user: root
        - group: root
        - mode: 644

/var/www/mail.dev.nathejk.dk:
    file.symlink:
        - target: /usr/share/roundcube
