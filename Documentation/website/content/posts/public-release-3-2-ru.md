Title: открытая версия 0.3.2
Date: 2018-12-31 13:20
Slug: release-0.3.2
Lang: ru

Встречайте Umbrella Linux 0.3.2.

Это промежуточный релиз на пути к поддержке Ubuntu "Bionic".

Обновление работающей системы потребует:
1) остановки bcfg2 сервера (`/etc/init.d/bcfg2-server stop`);
2) сброса базы данных bcfg2-reports (DROP/CREATE);
3) очиски кэша пакетов в /var/lib/bcfg2 (`rm Packages/cache/*`)
4) обновления пакетов bcfg2 (`apt-get update`/`upgrade`)
5) обновления /var/lib/bcfg2 (`git pull -a`);
6) миграции SSL ключей и сертификатов при помощи скрипта в
`Documentation/hacks/migration`;
7) удаления SSLCA/ ;
8) и, наконец, запуска bcfg2 сервера.

### Список основных изменений:

* Ubuntu 14.04 "Trusty" больше не поддерживается.
* пакет bcfg2 обновлён до версии 1.4.0pre2 с
  [дополнительными изменениями](https://github.com/metlov/bcfg2).
* значительное ускорение установки новой системы.

Чтобы попробовать Umbrella Linux смотрите 
[инструкции по установке](/umbrella-linux/ru/installation/).
