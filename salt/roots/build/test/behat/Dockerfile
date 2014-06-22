FROM ubuntu:14.04

# Update and install packages
RUN apt-get update
RUN apt-get install -y curl git vim
RUN apt-get install -y -q php5-cli php5-curl phantomjs default-jre-headless

# Create "behat" user with password crypted "behat"
RUN useradd -d /home/behat -m -s /bin/bash behat
RUN echo "behat:behat" | chpasswd

# Add "behat" to "sudoers"
RUN echo "behat        ALL=(ALL:ALL) ALL" >> /etc/sudoers

# Install Behat
RUN mkdir /home/behat/composer
ADD composer.json /home/behat/composer/composer.json
RUN cd /home/behat/composer && curl http://getcomposer.org/installer | php
RUN cd /home/behat/composer && php composer.phar install --prefer-source

# Fix permissions
RUN chown -R behat:behat /home/behat

# Change user
USER behat
WORKDIR /home/behat
ENV HOME /home/behat
ENV PATH $PATH:/home/behat

# Sahi configuration
ADD browser_types.xml /home/behat/composer/vendor/sourceforge/sahi/userdata/config/browser_types.xml
ADD sahi.properties /home/behat/composer/vendor/sourceforge/sahi/config/sahi.properties
ADD phantomjs-sahi.js /home/behat/phantomjs-sahi.js
ENV SAHIHOME $HOME/composer/vendor/sourceforge/sahi
ENV CLASSPATH $CLASSPATH:$SAHIHOME/lib/sahi.jar:$SAHIHOME/extlib/rhino/js.jar:$SAHIHOME/extlib/apc/commons-codec-1.3.jar

ADD behat.sh /home/behat/behat.sh

# start Sahi running in background
#RUN java net.sf.sahi.Proxy "$SAHIHOME" "$SAHIHOME/userdata" &

#ENV PS1 "\e[1;33m\e[40m\u@\h\e[m:\w$ "
