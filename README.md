
1chan
=====

Движок, основанный на [слитом движке 1chan.ru](https://github.com/jlbyrey/1chan) и [его форке от devarped](https://gitgud.io/devarped/pierwszykanal). Как и оригинал, форк лицензирован на условиях GNU Affero General Public License, версия 3.

Так как открытие исходных кодов не входило в первоначальные планы, история изменений движка до момента его загрузки недоступна.

# Отличия от оригинала

## Техническое
* **Вместо Realplexor используется Socket.io**
* Полная доменонезависимость (за исключением экзотических случаев вроде подключения по IP-адресу, так как JS ожидает наличие реалплексора на поддомене pipe)
* Кодировка БД сменена на utf8mb4 для защиты от порванной разметки
* Внесена правка для работы движка на PHP 8.x
* Также внесены правки в конфигурацию sphinx для работы на современных версиях (протестировано на 3.7.1)

## Добавлено
* Онлайн-ссылки, принимающие любой сайт (требует включения в администраторской)
* CSS-твики с [1chan.ca](https://1chan.ca/), среди них непроработанная защита от постов-"стен"
* Превью Youtube-видео по нажатию на ссылку
* Говорилка
* Новые смайлики, в том числе уже бывшие на 1chan.ca
* Время с точностью до секунд
* Вставка картинок с RGhost заменена на Imgur
* Добавлены скрипты инсталляции и выполнения фоновых задач

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

## Требования к софту
Ниже указаны версии софта, на котором производилось тестирования движка. Работа на более старых версиях возможна, но не гарантируется.
* nginx 1.21
* PHP 8.1
* MariaDB 10.5
* Redis 5.0
* Sphinx 3.7.1
* NodeJS 20.6.0
* ImageMagick и соответствующий модуль в PHP

## Конфигурация
Основная конфигурация производится через файл `.env`. Пример конфигурации приведен в `.env.example`.
**Доступ к файлу `.env` должен быть ограничен!**

### Настройки socket.io
* `SIO_SRV_IP` – Адрес, с которого сервер socket.io будет принимать сигналы. Как правило, всегда это `127.0.0.1`.
* `SIO_TOKEN` – Введите сюда любые случайные символы (требуется для различения в случае работы одного сервиса на несколько сайтов).
* `SIO_HOST` и `SIO_PORT` – Адрес, с которого сервер socket.io будет принимать внешние подключения. Должны совпадать с конфигурацией nginx, приведенной ниже:

```apacheconf
    location /socket.io/ {
      proxy_pass http://127.0.0.1:9393;
      proxy_http_version 1.1;
      proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
      proxy_set_header Upgrade $http_upgrade;
      proxy_set_header Connection "Upgrade";
    }
```

### Настройки MySQL
Самоочевидны. Базу данных с кодировкой `utf8mb4` потребуется предварительно создать вручную и прописать её название в поле `SQL_NAME`.

### Настройки Redis
Также самоочевидны. Убедитесь, что redis [демонизирован](https://gist.github.com/hackedunit/a53f0b5376b3772d278078f686b04d38).

### Настройки Sphinx
Установите Sphinx любым способом. Убедитесь, что Sphinx **не** демонизирован. Сервисный скрипт возьмёт это на себя.
Также обратите внимание, что править вручную конфигурация Sphinx, как это было в [оригинальной версии](https://github.com/jlbyrey/1chan) **не требуется**. Файл конфигурации (`scripts/1chan.generated.conf`) генерируется автоматически из данных `.env`-файла. Для ручного изменения конфигурации требуется править файл шаблона (`scripts/1chan.template.conf`). Из приведённых ниже настроек, вероятнее всего потребуется отредактировать только выделенные **жирным**.
* `SPHINX_HOST`, `SPHINX_PORT` – хост и порт, который будет слушать `searchd`.
* **`SPHINX_DATA_DIR`** - директория данных Sphinx. При отсутствии будет создана автоматически.
* **`SPHINX_BIN_DIR`** – директория, где хранятся исполнимые файлы Sphinx – `indexer` и `searchd`.
* `INDEXER_SCHEDULE` – с какой частотой будет запускаться индексатор (в формате cron). По умолчанию индексатор будет запускаться [раз в сутки, в полночь](https://crontab.guru/#0_0_*_*_*) (как в оригинальной версии)
* `INDEXER_MEM_LIMIT`, `INDEXER_MAX_IOPS`, `SEARCHD_READ_TIMEOUT`, `SEARCHD_MAX_CHILDREN` – если знаете, можете изменить, я не знаю.
* `SEARCHD_START` – если `"true"` (строка), то при запуске сервисного скрипта автоматически будет запускаться демон `searchd`. Измените, если предпочитаете запускать `searchd` иным способом (в этом случае позаботьтесь, чтобы при запуске он подтягивал файл конфигурации `scripts/1chan.generated.conf`)
* `SEARCHD_LOG_FILE` – имя файла лога `searchd`. Сам файл будет храниться в `SPHINX_DATA_DIR`.
* `SEARCHD_PID_FILE` – имя PID-файла `searchd`.  Сам файл будет храниться в `SPHINX_BIN_DIR`.

### Настройки проверки статуса сайтов и ссылок
* `SERVER_STATUS_SCHEDULE` – с какой частотой будет выполняться проверка ссылок (в формате cron). По умолчанию будет запускаться [раз в две минуты](https://crontab.guru/#*/2_*_*_*_*). 
* `SERVER_STATUS_PROXY` – прокси, который будет использоваться для пингования ссылок. Например, `socks5h://127.0.0.1:9050` – для подключения через TOR.
* `SERVER_STATUS_TIMEOUT` – время [мс], по истечении которого при отсутствии ответа от сервера сайт будет считаться недоступным.

### Кастомизация CSS
Для определения собственных URL картинок маскотов и прочего используются файлы `/www/css/1chan-light.custom.css` и `/www/css/omsk.custom.css`. Примеры файлов приведены в той же директории. 

### instance-config.php
Содержит дополнительные настройки. Пример файла – `instance-config.example.php`.
* `COMMON_ROOM_CONTROLWORD` – контрольное слово для общего чата;
* `RATE_BUTTON_ORDER` – порядок кнопок «↑» и «↓». (`up|down` или `down|up`, в зависимости от того, какой традиции вы собираетесь следовать).

## Скрипты автоматизации
Для работы скриптов требуется из директории `/scripts` выполнить `npm install`.
**Доступ к директории `/scripts` извне должен быть ограничен!**

### Скрипт установки
Позволяет загрузить дамп базы данных, а также создать учетную запись администратора.
**Запускается командой `npm run installation`.**
В процессе выполнения будет проверено наличие таблиц в базе данных и при отсутствии хотя бы одной из них будет загружен чистый дамп.
Также при отсутствии учетных записей админов в хранилище Redis будет предложено создать учетную запись.

### Сервисный скрипт
Запускается командой `npm start`. Выполняет следующие функции:
* Автоматически запускает `searchd` и периодически запускает индексатор Sphinx;
* Периодически запускает проверку статуса ссылок;
* Запускает сервис socket.io.

Для работы сервисного скрипта в фоновом режиме с помощью [pm2](https://www.npmjs.com/package/pm2) запустите **`npm run daemon`**. Также рекомендуется установить pm2 глобально для отслеживания процесса (`pm2 list`) и чтения логов (`pm2 log 1chan-service`).

# Обновление
Движок оптимизирован для vichan-подобного обновления. Для обновления его до актуальной версии из репозитория достаточно выполнить `git pull`, находясь в корневой папке движка.
