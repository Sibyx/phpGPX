FROM php:8.4-cli

RUN apt-get update && apt-get install -y \
		libxml2-dev \
		git \
		unzip \
		curl \
	&& docker-php-ext-install dom simplexml \
	&& pecl install xdebug \
	&& docker-php-ext-enable xdebug \
	&& apt-get clean && rm -rf /var/lib/apt/lists/*

RUN echo "xdebug.mode=debug,coverage" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
	&& echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
	&& echo "xdebug.start_with_request=trigger" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN curl -sL https://phpdoc.org/phpDocumentor.phar -o /usr/local/bin/phpdoc \
	&& chmod +x /usr/local/bin/phpdoc

WORKDIR /app