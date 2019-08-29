# Платный переход в группу для DLE-Billing

![Version](https://img.shields.io/badge/Version-1.3.2-blue.svg?style=flat-square "Version")
[![DLE Billing](https://img.shields.io/badge/DLE--Billing-0.7.3--0.7.4-red.svg?style=flat-square "DLE Billing")](https://github.com/Japing/dle-billing)
![DLE](https://img.shields.io/badge/DLE-13.0--13.2-green.svg?style=flat-square "DLE")

Плагин позволяет пользователям оплачивать переход между группами. Каждая группа имеет свои настройки: статус, исходная группа, время перехода, цена.

**Автор:** mr_Evgen (evgeny.tc@gmail.com)  
**Адаптировал:** Japing

**Версия DLE-Billing:** 0.7.3-0.7.4  
**Версия DLE:** 13.0-13.2
 
### **Установка плагина:**
1. Скачать архив с плагинам
2. Заходить в **Админ панель** -> **Утилиты** -> **Управление плагинами** и загрузить скачанный архив
3. В файле **main.tpl** вашего шаблона, перед тегом `</head>`  добавить:  
`<script type="text/javascript" src="{THEME}/billing/js/paygroups.js"></script>`
3. Настроить плагин в админ.панели: **/admin.php?mod=billing&c=paygroups**.

### **Обновление:**
- Обновите плагин через систему плагинов

P.S Выкладываю плагин с согласия автора **mr_Evgen (evgeny.tc@gmail.com)**

------------
### Список изменений

#### v1.3.2 (29.08.2019)
- Модуль адаптирован под DLE 13.0 и выше и к системе плагинов DLE.
