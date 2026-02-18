# UTrend Store Android App

Нативное Android-приложение, которое **получает товары напрямую с вашего сайта** `https://utrendstore.co.uk` через WooCommerce Store API и отображает их в отдельном интерфейсе.

## Что делает приложение

- Загружает список товаров с `https://utrendstore.co.uk/wp-json/wc/store/products`.
- Показывает карточки товаров: фото, название, цена.
- Поддерживает pull-to-refresh для обновления данных.
- При нажатии на карточку открывает страницу товара в Custom Tabs.
- Не использует WebView как основной UI (полностью отдельный интерфейс приложения).

## Технологии

- Kotlin + ViewBinding
- RecyclerView
- OkHttp + kotlinx.serialization
- Coil (загрузка изображений)
- Coroutines

## Запуск

1. Откройте проект в Android Studio.
2. Дождитесь синхронизации Gradle.
3. Укажите Android SDK (если не задан):
   - через Android Studio, или
   - в `local.properties`: `sdk.dir=/path/to/Android/Sdk`
4. Запустите модуль `app` на устройстве или эмуляторе.

## Публикация в Google Play

1. Build → Generate Signed Bundle / APK.
2. Выберите Android App Bundle (AAB).
3. Подпишите релиз keystore.
4. Загрузите AAB в Google Play Console.

## Важно перед продакшеном

- Поставить фирменные иконки и скриншоты.
- Подготовить privacy policy URL для карточки приложения.
- При необходимости подключить пагинацию и фильтры каталога.
