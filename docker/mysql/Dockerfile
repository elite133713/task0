FROM mysql:8.0

RUN chown -R mysql:root /var/lib/mysql/

ADD my.cnf /etc/my.cnf.d/my.cnf

USER mysql

CMD ["mysqld","--default-authentication-plugin=mysql_native_password"]

EXPOSE 3306
