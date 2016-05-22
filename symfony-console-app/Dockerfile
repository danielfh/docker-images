FROM php:7.0.6-cli

RUN apt-get update
RUN apt-get install --no-install-recommends -y git-core unzip

ADD ./download-composer.php /tmp/download-composer.php

RUN php /tmp/download-composer.php && rm /tmp/download-composer.php
RUN php composer-setup.php --install-dir=/usr/bin --filename=composer && rm composer-setup.php

RUN useradd -ms /bin/bash symfony-console

ADD ./app /usr/local/share/danielfh/console

RUN chown -R symfony-console:symfony-console /usr/local/share/danielfh/console

RUN ln -s /usr/local/share/danielfh/console/console /usr/local/bin/console

USER symfony-console

WORKDIR /usr/local/share/danielfh/console

RUN /usr/bin/composer install

CMD ["/usr/local/bin/console"]
