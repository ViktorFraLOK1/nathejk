# Contents of ./salt/roots/top.sls
# 
base:
  '*':
      - dev
      - webserver
      - database
      - database.phpmyadmin
      - build
      - nathejk
      - mail
