FROM bitnami/symfony:latest

RUN apt update && apt install -y cron systemctl

RUN echo "*/5 * * * * bitnami /app/bin/console smartiveapp:thumbnails-push-dropbox" >> /var/spool/cron/crontabs/root
