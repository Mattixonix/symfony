FROM bitnami/symfony:latest

ADD data/crontab /etc/cron.d/hello-cron
RUN chmod 0644 /etc/cron.d/hello-cron
RUN touch /var/log/cron.log
RUN apt-get update
RUN apt-get -y install cron
CMD cron && php -S 0.0.0.0:8000 -t /app/public
