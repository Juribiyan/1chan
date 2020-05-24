1chan.pl
=====

Движок сайта [1chan.pl](https://1chan.pl/), основанный на [слитом движке 1chan.ru](https://github.com/jlbyrey/1chan). Как и оригинал, форк лицензирован на условиях GNU Affero General Public License, версия 3.

Так как открытие исходных кодов не входило в первоначальные планы, история изменений движка до момента его загрузки недоступна.

# Отличия от оригинала

## Техническое
* Полная доменонезависимость (за исключением экзотических случаев вроде подключения по IP-адресу, так как JS ожидает наличие реалплексора на поддомене pipe)
* Кодировка БД сменена на utf8mb4 для защиты от порванной разметки
* Внесена правка для работы движка на PHP 7.x
* Также внесены правки в конфигурацию sphinx для работы на современных версиях

## Добавлено
* Онлайн-ссылки, принимающие любой сайт (требует включения в администраторской)
* CSS-твики с [1chan.ca](https://1chan.ca/), среди них непроработанная защита от постов-"стен"
* Превью Youtube-видео по нажатию на ссылку
* Говорилка
* Новые смайлики, в том числе уже бывшие на 1chan.ca
* Время с точностью до секунд
* Вставка картинок с RGhost заменена на Imgur

## Багфиксы
* Страница при отправке сообщений с включённой капчей больше не перезагружается
* Починен общий чат
* Починено отображение названий новостей в "последних комментариях"
* Убрана пустая полоска у низа Чио-Чичи

## Баги и удалённый функционал
* Убраны все иконки принадлежности, кроме "Анонима"
* Убран Jabber-бот по причине невостребованности и возможной поломки им функциональности сайта
* В связи с изменением поведения онлайн-ссылок убрана часть функционала, связанная с их разграничением на категории
* Удалены потенциально небезопасные фичи разметки, такие, как использование HTML-тегов и вставка картинок с любого сайта
* Ради безопасности отключена возможность вставлять картинки со сторонних источников
* При добавлении ссылок в спамлист возможно отсутствие ответа "Ссылка запрещена". Впрочем, ссылки спамлистом всё равно не пропускаются.

# Установка
Мало чем отличается от [оригинальной версии](https://github.com/jlbyrey/1chan). Как было сказано выше, движок также способен работать на более новых версиях программ, тестировалось на PHP 7.4, MariaDB 10.4 и Sphinx 2.2. Требуется Redis не старее 4.0.

В отличие от оригиналнього движка, данный форк читает настройки из файла `instance-config.php`. Скопируйте содержимое `instance-config.php.example` в него, и измените его согласно своим настройкам.

Для работы онлайн-ссылок зайдите в /admin/, "Каналы" и пропишите указанные на странице строчки. Для автоматический их чистки на основе доступности сервера (а также проверки доступности ссылок в футере) скопируйте `cron.sh.example` в `cron.sh`, заменив путь к движку на свой, и добавьте его запуск в crontab каждые N минут.

# Обновление
Движок оптимизирован для vichan-подобного обновления. Для обновления его до актуальной версии из репозитория достаточно выполнить `git pull`, находясь в корневой папке движка.
