FROM danielfh/php7-cli-composer

RUN apt-get update && \
    apt-get install -y openjdk-7-jre && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/* && \
    rm -rf /tmp/*

RUN mkdir -p /opt/danielfh/phpstorm/{home,console} && useradd -d /opt/danielfh/phpstorm/home phpstorm

ADD ./console /opt/danielfh/phpstorm/console

RUN cd /opt/danielfh/phpstorm/console && composer install

RUN ln -s /opt/danielfh/phpstorm/console/bin/console /usr/local/bin/phpstorm-console

WORKDIR /opt/danielfh/phpstorm/home

ENTRYPOINT ["/usr/local/bin/phpstorm-console"]
