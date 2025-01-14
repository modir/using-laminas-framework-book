# Приложение Б. Введение в PHP-разработку в NetBeans IDE {#netbeans}

В этой книге мы используем интегрированную среду разработки (Integrated development environment или IDE) NetBeans для
разработки приложений на базе Laminas Framework. В [Приложении A. Настройка среды веб разработки](#devenv) мы
установили среду NetBeans.
В этом приложении мы дадим вам несколько полезных советов по использованию NetBeans для программирования на PHP.
Так, мы научимся запуску и интерактивной отладке веб-сайта на базе Laminas.

Q> *Что если я хочу использовать другую IDE (не NetBeans) для разработки моих приложений?*
Q>
Q> Что ж, вы можете использовать любую среду разработки, которую хотите. Проблема в том,
Q> что рассмотреть все IDE для PHP-программирования в этой книге невозможно. Автор дает
Q> инструкции только для NetBeans. Начинающему будет проще использовать эту среду разработки.
Q> Продвинутые разработчики могут использовать IDE на свой вкус.

## Конфигурация запуска

Для запуска и отладки сайта сперва нужно изменить свойства
сайта. Чтобы это сделать, на панели *Projects* (Проекты) в NetBeans нажмите
правой кнопкой мыши на имя проекта и в контекстном меню выберите пункт *Properties* (Свойства).
Откроется диалоговое окно *Properties* (см. рисунок Б.1).

![Рисунок Б.1. Properties | Sources](../en/images/netbeans/properties_sources.png)

На левой панели окна нажмите на вкладку *Sources* (Исходный код). Затем в поле
*Web Root* (Корневой каталог веб-приложения) справа укажите ваш каталог *APP_DIR/public*.
Это можно сделать с помощью кнопки *Browse* (Поиск). В следующем диалоговом окне
нажмите на каталог *public*, а затем на кнопку *Select Folder* (Выбрать каталог)
(см. рисунок Б.2):

![Рисунок Б.2. Поиск каталогов](../en/images/netbeans/properties_browse_folders.png)

После этого, выберите *Run Configuration* (Конфигурация запуска) на панели слева. Справа должны отобразиться
настройки запуска для вашего сайта (рисунок Б.3).

![Figure B.3. Properties | Run Configuration](../en/images/netbeans/properties_run_config.png)

Как видите на панели справа, текущая конфигурация - "default" (по умолчанию). Как вариант, вы можете
создать несколько конфигураций запуска.

Измените значения полей следующим образом:

* В поле *Run As* (Запустить как...) выберите "Local Website (running on local web server)".

* В поле *Project URL* (URL проекта) введите "http://localhost". Если вы настроили ваш
  виртуальный хост на обработку другого порта (например, порта 8080), введите его номер таким образом: "http://localhost:8080".

* Оставьте поле *Index File* (Индексный файл) пустым, потому что модуль Apache *mod_rewrite* скроет
  наш файл *index.php*.

* В поле *Arguments* (Аргументы) вы можете указать, какие GET-параметры передавать вашему сайту
  через строку URL. Как правило, это поле оставляется пустым.

После этого, нажмите кнопку *OK*, чтобы закрыть окно *Properties*.

## Запуск веб-сайта

Запустить веб-сайт - значит открыть его в вашем браузере. Для этого
нажмите кнопку *Run* (Запустить) на панели инструментов запуска) (рисунок Б.4). Также
вы можете воспользоваться кнопкой *F6* на клавиатуре.

![Рисунок Б.4. Панель инструментов запуска](../en/images/netbeans/launch_toolbar.png)

Если с вашей конфигурацией запуска все в порядке, запустится ваш браузер по умолчанию,
и в окне браузера вы сможете увидеть страницу веб-сайта *Home*.

Того же эффекта можно было добиться, набрав в браузере "http://localhost/", но
панель инструментов NetBeans позволяет сделать это одним щелчком мыши.

## Отладка сайта в NetBeans

"Общепринятым" методом отладки в PHP является использование функции `var_dump()`
в куске кода, который вы хотите проверить:

`var_dump($var);`

`exit;`

Эти строки выведут браузеру значение переменной `$var`, а затем остановят выполнение
программы. Хотя таким образом можно сделать отладку даже сложного сайта, это плохая
идея, так как вам придется вводить в исходном файле команды вывода информации о переменных,
затем обновлять веб-страницу в браузере, чтобы увидеть результат, после этого вновь изменять
исходный файл - и так до тех пор, пока вы не установите причину проблемы.

В то же время при отладке сайта в среде NetBeans, интерпретатор останавливает поток
выполнения программы на каждой строчке, где вы поставите *точку останова*. Так
вы можете удобным образом извлекать информацию о текущем состоянии программы, например, значения
локальных переменных и стека вызовов. Информацию об отладке NetBeans предоставляет в графическом виде.

W> Для возможности отладки сайта нужно установить расширение XDebug.
W> Если вы еще этого не сделали, обратитесь к [Приложение A. Настройка среды веб разработки](#devenv) за
W> дополнительной информацией об установке расширения.

Для начала сессии отладки, в окне NetBeans нажмите на кнопку *Debug* (Отладить проект) на панели инструментов (рисунок Б.4).
Вы также можете воспользоваться комбинацией клавиш *CTRL+F5* на клавиатуре.

Если все в порядке, вы должны будете увидеть текущий счетчик команд в первой
строчке кода файла *index.php* (см. рисунок Б.5):

![Рисунок Б.5. Сессия отладки](../en/images/netbeans/debug_session.png)

На время, пока программа находится в состоянии паузы, окно вашего браузера зависнет,
так как браузер ждет данных от веб-сервера. Как только вы продолжите выполнение программы,
браузер получит данные и отобразит страницу.

## Панель инструментов отладки

Вы можете возобновить/приостановить выполнение программы с помощью *панели инструментов отладки* (см. рисунок Б.6):

![Рисунок Б.6. Панель инструментов отладки](../en/images/netbeans/debug_toolbar.png)

Кнопка *Finish Debugger Session* (Закончить сессию отладки) на панели позволяет остановить отладчик. Нажмите
ее по завершении отладки программы. Это также можно сделать комбинацией клавиш *SHIFT+F5*.

Нажатие кнопки *Continue* (Продолжить) (или клавиши *F5*) продолжает выполнение программы
до следующей точки останова или до конца программы, если точек останова больше нет.

Кнопка *Step Over* (Шаг вперед), или нажатие клавиши *F8*, двигает счетчик команд на
следующую строку программы.

Кнопка *Step Into* (Шаг внутрь), или нажатие клавиши *F7*, двигает счетчик команд на
следующую строку программы, и если это точка входа функции, заходит в тело
функции. Используйте эту кнопку, если вам нужно тщательно изучить ваш код.

Кнопка *Step Out* (Шаг наружу), или *CTRL+F7*, позволяет продолжить выполнение программы до
возвращения из текущей функции.

Наконец, *Run to Cursor* (Выполнить до курсора), клавиша *F4*, позволяет продолжить выполнение программы до строчки кода, где
находится курсор. Это может быть удобным, если вы хотите пропустить какой-то кусок кода и сделать паузу на определенной
строчке вашей программы.

## Точки останова

Как правило, вы устанавливаете одну или несколько точек останова в строках, для которых вы хотите
выполнить пошаговую отладку. Чтобы установить точку останова, наведите курсор на нужную строку кода
и нажмите на номер этой строки слева. Либо вы можете поставить курсор в любое место этой строки
и нажать комбинацию клавиш *CTRL+F8*.

При установке точки останова, строка выделяется красным цветом, и слева от нее
появляется маленький красный прямоугольник (см. рисунок Б.7):

![Рисунок Б.7. Установка точки останова](../en/images/netbeans/breakpoint.png)

T> Будьте осторожны: не устанавливайте точку останова на пустую строку или строку с комментариями.
T> Такая точка будет проигнорирована XDebug, а также помечена "сломанным"
T> квадратом (см. рисунок Б.8):

![Рисунок Б.8. Неактивная точка останова](../en/images/netbeans/breakpoint_on_comment.png)

Вы можете перемещаться между точками остановами нажатием клавиши *F5*. Эта клавиша
продолжает выполнение программы до столкновения со следующей точкой останова. Как только
поток выполнения программы доходит до точки останова, PHP-интерпретатор приостанавливается
и вы можете просмотреть состояние программы.

Полный список установленных вами точек останова можно найти в окне *Breakpoints* (см. рисунок 6.9).
Оно находится в нижней части окна NetBeans.
Здесь вы можете добавить новые точки останова или снять ранее поставленные.

![Рисунок Б.9. Окно Breakpoints](../en/images/netbeans/breakpoints_window.png)

## Наблюдение за переменными

После остановки интерпретатора вы можете посмотреть значения переменных.
Простой способ просмотреть переменную - навести курсор мыши на ее имя в коде и секунду подождать.
Если значение переменной возможно оценить, оно отобразиться в сплывающем окошке.

Другой способ наблюдения за переменными - через окно *Variables* (Переменные; см. рисунок Б.10),
которое находится в нижней части окна NetBeans и содержит три столбца: *Name* (Имя),
*Type* (Тип) и *Value* (Значение).

![Рисунок Б.10. Окно Variables](../en/images/netbeans/variables_window.png)

В основном, вы будете сталкиваться с тремя типами переменных *super globals*, *locals* and *$this*:

* *Суперглобальные* переменные - специальные переменные PHP вроде `$_GET`, `$_POST`, `$_SERVER`, `$_COOKIES`
  и т.п. Они, как правило, содержат информацию о сервере и параметры, передаваемые браузером как часть
  HTTP-запроса.

* *Локальные* - переменные, "живущие" в рамках текущей функции (или метода класса).
  Например, если в приложении Hello World вы установите точку останова внутри `IndexController::aboutAction()`,
  переменная `$appName` будет локальной.

* Переменная *$this* указывает на текущий экземпляр класса, если код выполняется
  в контексте PHP-класса.

Некоторые переменные можно "развернуть" (чтобы это сделать, нужно нажать на иконку треугольника
слева от имени переменной). Например, развернув переменную *$this*, можно просмотреть все поля
экземпляра класса, а развернув переменную массива - просмотреть его элементы.

Используя окно *Variables* возможно не только смотреть значение переменной, но и изменить
его "на лету". Чтобы это сделать, наведите курсор на столбец значений и щелкните мышью.
Появится окно, где вы сможете задать переменной новое значение.

## Стек вызовов

*Стек вызовов* (Call stack) отображает список вложенных функций, код которых выполняется
в данный момент (см. рисунок Б.11). Каждая строка стека вызовов (или стековый кадр)
содержит полное имя класса, имя метода класса и номер строки. Двигаясь по стеку,
вы можете лучше понять текущее состояние выполнения программы.

![Рисунок Б.11. Окно Call Stack](../en/images/netbeans/call_stack_window.png)

Как вы видите из рисунка Б.11, в данный момент выполняется метод `IndexController::aboutAction()`,
он в свою очередь был вызван методом `AbstractActionController::onDispatch()` и т.д.
Мы можем пройти по стеку вызовов до файла *index.php*, который является вершиной стека.
Вы также можете нажать на стековый кадр, чтобы посмотреть, какой участок кода сейчас выполняется.

## Параметры отладки

NetBeans позволяет вам настроить некоторые аспекты поведения отладчика. Чтобы открыть окно
*Options* (Параметры), выберите пункт *Tools->Options* (Инструменты->Параметры). В появившемся диалоговом
окне нажмите на вкладку *PHP*, а затем на *Debugging* (рисунок Б.12).

![Рисунок Б.12. Параметры отладки](../en/images/netbeans/options_php_debugging.png)

Как правило, менять эти настройки вы не будете, но вам нужно знать, что
они делают. Итак, есть следующие параметры отладки:

* Параметры *Debugger Port* (Порт отладчика) и *Session ID* (ID сессии) определяет, как NetBeans подключается к XDebug.
  По умолчанию, порт имеет номер 9000. Номер порта должен быть таким же, как и у порта отладчика, который вы задали
  в файле *php.ini* при установке XDebug. Имя сессии по умолчанию - "netbeans-xdebug". Это значение обычно не меняется.

* Параметр *Stop at First Line* (Остановить на первой строке) останавливает отладчик на первой строке файла
  *index.php* file вместо первой точки останова. Это может мешать, так что, возможно, вы захотите убрать галочку с этого параметра.

* Группа параметров *Watches and Balloon Evaluation* (Наблюдения и оценка по всплывающем окне) по умолчанию выключена, так как
  может привести к ошибке XDebug. Включайте эти параметры только если точно знаете, что делаете.

    * Параметр *Maximum Depth of Structures* (Максимальная глубина структур) устанавливает, будут ли вложенные структуры
      (то есть, вложенные массивы, объекты в объектах и т.д.) видимыми или нет. По умолчанию, значение глубины
	  установлено на 3.

    * Параметр *Maximum Number of Children* (Максимальная глубина дочерних элементов) определяет, сколько элементов массива отображать
      в окне *Variables*. Если установить его значение, скажем, на 30, вы будете видеть только 30 первых элементов массива даже если
	  тот содержит больше 30 элементов.

* Параметр *Show Requested URLs* (Показать запрашиваемые URL) отображает обрабатываемый в данный момент URL в
  окне *Output*.

* Параметр *Debugger Console* (Консоль отладки) позволяет увидеть результат отладки PHP-скриптов в
  том же окне *Output*. Если вы планируете использовать эту опцию, рекомендуется добавить
  параметр`output_buffering = Off` в раздел `[xdebug]` файла *php.ini* file,
  иначе результат может выводиться с задержкой.

## Профилирование

Когда ваш сайт готов и уже работает, вы, должно быть, захотите сделать его настолько быстрым и эффективным,
насколько это возможно. XDebug предоставляет вам возможность *профилирования* вашего сайта. Профилирование означает
определение того, сколько времени занимает выполнение того или иного метода класса (или функции). Это позволяет найти
в коде узкие места и решить проблемы производительности.

W> Для использования программы профилирования XDebug, нужно установить следующий параметр конфигурации XDebug в файле *xdebug.ini*:
W>
W> `xdebug.profiler_enable = 1`

К сожалению, для PHP у NetBeans нет встроенного инструмента для отображения результатов профилирования.
Поэтому для этого нужно установить стороннюю программу. Ниже мы дадим инструкции по установке простого
веб-инструмента [Webgrind](https://github.com/jokkedk/webgrind).
Webgrind может работать на любой платформе, так как сам инструмент написан на PHP.

Установка Webgrind весьма прямолинейна.

Для начала нужно скачать его со страницы проекта и распаковать в какой-нибудь каталог.
В Linux это можно сделать следующими командами:

`cd ~`

`wget https://github.com/jokkedk/webgrind/archive/master.zip`

`unzip master.zip`

Эти команды сменят ваш рабочий каталог на домашний каталог, затем скачают архив с Webgrind
из Интернета и распакуют его.

После этого нужно сообщить веб-серверу Apache, где найти файлы Webgrind. Это значит, что
вам придется настроить отдельный виртуальный хост. Мы уже изучили виртуальный хосты в
[Приложение A. Настройка среды веб разработки](#devenv). Не забудьте перезапустить веб-сервера
после настройки.

Наконец, откройте Webgrind в браузере, перейдя на URL установки. Например, если вы настроили виртуальный хост
на обработку порта 8080, введите "http://localhost:8080" в адресной строке и нажмите Enter.
Появится веб-страница Webgrind (см. рисунок Б.13):

![Рисунок Б.13. Страница Webgrind](../en/images/netbeans/webgrind.png)

В верхней части страницы вы можете выбрать, какой процент самых "тяжелых" функций будет показываться.
По умолчанию, это значение равно 90. Установив значение ниже, можно скрыть функции, которые вызываются реже.

![Рисунок Б.14. Выбор в Webgrind](../en/images/netbeans/webgrind-select.png)

Выпадающий список в правой части этого поля позволяет выбрать файл профилированных данных для анализа.
По умолчанию, выбрана опция "Auto (newest)", и Webgrind использует файл с последней временной меткой.
Возможно, вам понадобится выбрать другой файл, например, если ваши веб-страницы используют асинхронные
AJAX-запросы.

Крайний справа выпадающий список позволяет установить единицы, используемые для измерения
данных. Возможные варианты это: проценты (по умолчанию), милисекунды и микросекунды.

После того, как вы выберите процент функций, имя файла и единицы измерения, нажмите кнопку *Update* (Обновить),
чтобы Webgrind отобразил для вас данные (вычисление может занять несколько секунд). Когда вычисление закончится,
вы увидите таблицу вызовов функций, отсортированную по убыванию "веса" функции.
Самые тяжелые функции будут наверху.

Таблица имеет следующие столбцы:

* Первый столбец (*Function*) показывает имя класса, а за ним имя метода (в случае вызова метода)
  или имя функции (в случае обычной функции).

* Второй столбец содержит знаки параграфа, на которые можно нажать, чтобы открыть соответствующий
  исходный файл PHP

* Столбец *Invocation Count* показывает, сколько раз была вызвана функция.

* Столбец *Total Self Cost* показывает общее время, затраченное на выполнение встроенного PHP-кода в функции
 (не считая время, затраченное на выполнение других нестандартных функций).

* Столбец *Total Inclusive Cost* содержит общее время выполнения для функции, включая
  как встроенный PHP-код, так и любые другие вызываемые пользовательские функции.

Нажав на заголовок столбца можно отсортировать данные в возрастающем или убывающем порядке.

Также вы можете кликнуть на иконку треугольника рядом с именем функции, чтобы развернуть
список вызовов функций. Этот список позволяет посмотреть, кто вызывал эту функцию и сколько
времени было затрачено, и содержит следующие столбцы:

* *Calls* - "родительские" функции или методы класса, вызывающие эту (дочернюю) функцию;
* *Total Call Cost* - общее время выполнения этой функции при вызове из родительской функции;
* *Count* - количество вызовов родителем дочерней функции.

Цветная полоса в верхней части страницы отображает вклад различных типов функций:

* *Голубой* обозначает внутренние (встроенные) PHP-функции;
* *Бледно-лиловый* - это время, затраченное на подключение PHP-файлов;
* *Зеленый* показывает вклад ваших методов классов;
* *Orange* обозначает время, затраченное на стандартные "процедурные" функции (функции, не являющиеся частью классов).

T> Обратите внимание на то, что при профилировании создается новый файл данных в каталоге */tmp* для каждого HTTP-запроса.
T> Это может вызывать "истощение" места на диске, что можно исправить лишь перезапуском системы.
T> Таким образом, когда вы закончили профилирование вашего приложения, рекомендуется отключить
T> профилирование, изменив файл *php.ini*, закомментировав параметр `xdebug.profiler_enable` как показано ниже,
T> и затем перезапустить веб-сервер Apache.
T>
T> `;xdebug.profiler_enable = 0`
T>

## Выводы

В этом приложении мы научились использовать среду разработки NetBeans для запуска веб-сайта и его интерактивной отладки
в пошаговом режиме. Для запуска сайта сначала необходимо изменить свойства сайта (конфигурацию запуска).

Для отладки сайта у вас должно быть установлено расширение XDebug. При отладке сайта в NetBeans, PHP-движок останавливает
выполнение программы на каждой строке, где вы поставите точку останова. Информация об отладке (вроде локальных переменных и
стека вызовов) выводится в окне NetBeans в графическом виде.

Помимо отладки, расширение XDebug предоставляет возможность профилирования веб-сайтов. Используя профилирование, вы можете
посмотреть, сколько времени было затрачено на выполнение определенной функции или метода класса. Это позволяет определить
в коде узкие места и, следовательно, проблемы с производительностью.
