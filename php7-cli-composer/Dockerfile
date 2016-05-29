FROM php:7.0.6-cli

RUN apt-get update
RUN apt-get install --no-install-recommends -y git-core unzip

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

CMD ["/usr/bin/true"]
