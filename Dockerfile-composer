FROM php:7.0-cli as my-php

RUN curl -sS https://getcomposer.org/installer | \
    php -- --install-dir=/usr/bin/ --filename=composer \
    && apt-get update \
    && apt-get install -y zlib1g-dev \
    && docker-php-ext-install zip

WORKDIR /workspace

# Ensure that a volume is mounted to /workspace when this container is run
COPY entry_point.sh /opt/bin/entry_point.sh
RUN chmod +x /opt/bin/entry_point.sh
ENTRYPOINT ["/opt/bin/entry_point.sh"]

# Run bash so that the container remains running after the ENTRYPOINT script finishes
CMD ["bash"]
