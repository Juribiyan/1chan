- [x] Заменить Realplexor на Socket.io
- [x] Переписать скрипт проверки статуса доступности серверов на JS, тем самым избавившись от cron
- [x] Избавиться от креденциалей в файле конфига Sphinx, вместо этого динамически создавать временный файл, считывая переменные из .env и подставляя их в шаблон
- [x] Избавиться от хардкода, где можно
  - [x] Сделать список досок параметрическим
  - [x] Сделать список смайликов параметрическим
- [x] Избавиться от дупликации кода в layoutах и сделать переключение стилей без перезагрузки
- [x] Протестировать работу со свежей версией Sphinx
- [x] Сделать процедуру установки, первого запуска и авторизации админа менее костыльной
  - [x] Добавить страницу авторизации
- [ ] Перенести часть фич из 1chan-X в клиентский JS
  - [ ] Заполнить перечень фич
- [ ] Дописать README
  - [x] Написать гайд по деплою сервисного скрипта
  - [x] Привести пример конфига nginx
- [x] Возможность сортировать доски и прочие списки
- [x] Возможность загрузки иконок хоумбордов и смайликов из админки
- [x] Исключить директорию смайликов и хоумбордов из индекса, создавать её из шаблона при инсталляции
- [ ] Добавить проверку на пробелы в именах файлов смайликов