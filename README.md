1chan.pl
=====

Движок сайта [1chan.pl](https://1chan.pl/), основанный на [слитом движке 1chan.ru](https://github.com/jlbyrey/1chan). Как и оригинал, форк лицензирован на условиях GNU Affero General Public License, версия 3.

Так как открытие исходных кодов не входило в первоначальные планы, история изменений движка до момента его загрузки недоступна.

# Отличия от оригинала
## Техническое
* Почти полная доменонезависимость (единственное исключение - адрес реалплексора в production.js)
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
* Ради безопасности отключена возможность вставлять картинки со сторонних источников
* При добавлении ссылок в спамлист возможно отсутствие ответа "Ссылка запрещена". Впрочем, ссылки спамлистом всё равно не пропускаются.

# Установка
Не отличается от [оригинальной версии](https://github.com/jlbyrey/1chan). Как было сказано выше, движок также способен работать на более новых версиях программ, тестировалось на PHP 7.2, MariaDB 10.1 и Sphinx 2.2.

Для работы онлайн-ссылок зайдите в /admin/, "Каналы" и пропишите указанные на странице строчки.

Также не забудьте сменить контрольный пароль общей чат-комнаты, делается это в `app/models/chat/chatrooms.model.php`, 53 строчка.

Ещё не следуеть забывать сменить адрес реалплексора в  `www/js/production.js`, о чём не сказано в основной инструкции. Если у сайта есть зеркала, то для того, чтобы адрес реалплексора у каждого зеркала сайта был свой, воспользуйтесь сторонними средствами, например [sub_module для nginx](https://nginx.org/ru/docs/http/ngx_http_sub_module.html).

Работа Telegram-бота в данный момент реализована с помощью [pyFeedsTgBot](https://github.com/shpaker/pyFeedsTgBot), получающего нужную информацию из RSS-лент. Ранее для этого использовася проприетарный сервис [Manybot](https://t.me/Manybot).

# Обновление
Для обновления выполните `git pull`, находясь в корневой папке движка. Перед этим посмотрите историю коммитов для файлов, указанных в `.gitignore`. Если эти файлы были изменены после ваших правок, самостоятельно перенесите сделанные в них изменения в свою версию файла.
