; <?
; this is the INI file for the sigaretnik

[common]
       txtpreviewlen = 150                            ; sets the number of characters for text preview windows
            timezone = "Europe/Samara"                ; default timezone
     sessionsavepath = "data/sessions"                ; folder where to store session system data files
            temppath = "data/temp"                    ; folder where to store temporary files
        projectspath = "data/projects"                ; the projects folder path
    enableajaxrecode = 0                              ; set to 1 to solve ajax codepage errors if any, else set to 0
     oldrecordsthres = 0                              ; old records threshold in days. set to 0 to disable old records presence check
        defaulttheme = "modern";

[database]
              dbname = "alchemy"                      ; database name
              dbuser = "root"                         ; database user
          dbpassword = ""                             ; database password
              dbhost = "localhost"                    ; database server
            sqllimit = 200                            ; SQL Output Limit. place zero value here to disable this.
     displaydberrors = 1;                             ; set to 1 to enable display of DB errors

[pictures]
          thumbwidth = 150                            ; picture thumbnail width
         thumbheight = 150                            ; picture thumbnail height
        previewwidth = 640                            ; picture preview width
       previewheight = 480                            ; picture preview height

[sms_transport]
     sms_HTTPS_LOGIN = "beauty2014" ; Ваш логин для HTTPS-протокола
  sms_HTTPS_PASSWORD = "123456789"  ; Ваш пароль для HTTPS-протокола
   sms_HTTPS_ADDRESS = "https://lcab.smsintel.ru/"; HTTPS-Адрес, к которому будут обращаться скрипты. Со слэшем на конце.
    sms_HTTP_ADDRESS = "http://lcab.smsintel.ru/"; HTTP-Адрес, к которому будут обращаться скрипты. Со слэшем на конце.
    sms_HTTPS_METHOD = "file_get_contents"; метод, которым отправляется запрос (curl или file_get_contents)
       sms_USE_HTTPS = 1; 1 - использовать HTTPS-адрес, 0 - HTTP

  ; Класс попытается автоматически определить кодировку ваших скриптов. 
  ; Если вы хотите задать ее сами в параметре HTTPS_CHARSET, то укажите HTTPS_CHARSET_AUTO_DETECT значение FALSE
  sms_HTTPS_CHARSET_AUTO_DETECT = false;
   sms_HTTPS_CHARSET = "utf-8" ; кодировка ваших скриптов. cp1251 - для Windows-1251, либо же utf-8 для, сообственно - utf-8 :)

[prostor]
        prostor_host = "gate.prostor-sms.ru";
        prostor_port = "80";
       prostor_login = "t89219951525";
    prostor_password = "680121";

; ?>
