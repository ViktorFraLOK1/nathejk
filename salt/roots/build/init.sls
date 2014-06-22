npm:
  pkg.installed

/usr/local/bin/node:
  file.symlink:
    - target: /usr/bin/nodejs

global-npm-packages:
  npm.installed:
    - names:
        - grunt-cli
        - gulp
        - bower
    - require:
      - pkg: npm
