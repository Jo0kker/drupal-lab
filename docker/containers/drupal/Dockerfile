# Adapté de : https://github.com/docker-library/drupal/blob/ff8962fc943001457c6919fa42e3d875b9fab9f7/7/apache/Dockerfile

FROM php:7.4-apache

# install the PHP extensions we need
RUN set -ex; \
	\
	if command -v a2enmod; then \
		a2enmod rewrite; \
	fi; \
	\
	savedAptMark="$(apt-mark showmanual)"; \
	\
	apt-get update; \
	apt-get install -y --no-install-recommends \
		libjpeg-dev \
		libpng-dev \
		libpq-dev \
		libmemcached-dev \
		zlib1g-dev \
		libzip-dev \
	; \
	pecl install memcached-3.1.5 xdebug-2.9.5 apcu-5.1.18 ; \
    docker-php-ext-enable memcached xdebug apcu ; \
	\
	docker-php-ext-configure gd --with-jpeg=/usr; \
	docker-php-ext-install -j "$(nproc)" \
		gd \
		opcache \
		pdo_mysql \
		mysqli \
		pdo_pgsql \
		zip \
	; \
	\
# reset apt-mark's "manual" list so that "purge --auto-remove" will remove all build dependencies
	apt-mark auto '.*' > /dev/null; \
	apt-mark manual $savedAptMark; \
	ldd "$(php -r 'echo ini_get("extension_dir");')"/*.so \
		| awk '/=>/ { print $3 }' \
		| sort -u \
		| xargs -r dpkg-query -S \
		| cut -d: -f1 \
		| sort -u \
		| xargs -rt apt-mark manual; \
	\
	apt-get purge -y --auto-remove -o APT::AutoRemove::RecommendsImportant=false; \
	rm -rf /var/lib/apt/lists/*

RUN apt-get update && apt-get install -y --no-install-recommends mariadb-client git unzip msmtp

RUN { \
		echo 'account default'; \
		echo 'host mail'; \
		echo 'port 25'; \
		echo 'from dev@docker'; \
		echo 'logfile /dev/stdout'; \
	} > /etc/msmtprc
RUN echo 'sendmail_path=/usr/bin/msmtp -t' > /usr/local/etc/php/conf.d/msmtp.ini

# set recommended PHP.ini settings
# see https://secure.php.net/manual/en/opcache.installation.php
#RUN { \
#		echo 'opcache.memory_consumption=128'; \
#		echo 'opcache.interned_strings_buffer=8'; \
#		echo 'opcache.max_accelerated_files=4000'; \
#		echo 'opcache.fast_shutdown=1'; \
#		echo 'opcache.enable_cli=1'; \
#	} > /usr/local/etc/php/conf.d/opcache-recommended.ini

WORKDIR /var/www/html



RUN usermod -u 1000 www-data

COPY 000-default.conf /etc/apache2/sites-enabled/
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN mkdir /var/www/.composer && chown www-data: /var/www/.composer
RUN mkdir /var/www/.console && chown www-data: /var/www/.console




RUN { \
		echo 'memory_limit=192M'; \
		echo 'upload_max_filesize=512M'; \
		echo 'post_max_size=512M'; \
	} > /usr/local/etc/php/conf.d/custom.ini

RUN { \
		echo 'xdebug.remote_enable=true'; \
		echo 'xdebug.remote_host=172.17.0.1'; \
		echo 'xdebug.remote_port=9000'; \
		echo 'xdebug.remote_autostart=1'; \
	} >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

RUN mkdir /etc/apache2/ssl/
ADD ./ssl.* /etc/apache2/ssl/
RUN a2enmod ssl
RUN apache2ctl restart
CMD ["apache2-foreground"]