# Will install MySQL in the Virtual machine
mysql-server:
    pkg.installed

mysql-client:
    pkg.installed

mytop:
    pkg.installed

database-setup:
    cmd.run:
        - name: echo "CREATE DATABASE nathejk; CREATE USER 'nathejk'@'localhost' IDENTIFIED BY 'vabbes'; GRANT ALL PRIVILEGES ON nathejk.* TO 'nathejk'@'localhost'; FLUSH PRIVILEGES;" | mysql
        - unless: echo "SELECT NOW()" | mysql -unathejk -pvabbes nathejk 
        - require:
            - pkg: mysql-client

