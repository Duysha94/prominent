# UTrend Store Android App

Android-приложение для сайта [utrendstore.co.uk](https://utrendstore.co.uk/) c публикацией в Google Play.

## Что внутри

- WebView-приложение, открывающее только домен `https://utrendstore.co.uk`.
- Индикатор загрузки страниц.
- Поддержка кнопки "Назад" внутри WebView.
- Базовая защита сети (только HTTPS).
- Готовая конфигурация `release` сборки с ProGuard.

## Как запустить

1. Установите Android Studio (Hedgehog+).
2. Откройте папку проекта.
3. Дождитесь синхронизации Gradle.
4. Запустите `app` на эмуляторе/телефоне.

## Сборка релиза

1. В Android Studio: **Build → Generate Signed Bundle / APK**.
2. Выберите **Android App Bundle (AAB)**.
3. Создайте keystore и подпишите релиз.
4. Загрузите `.aab` в Google Play Console.

## Что нужно добавить перед публикацией

- Собственную иконку бренда в `mipmap*`.
- Политику конфиденциальности (URL) в карточке приложения Google Play.
- Скриншоты приложения для Google Play.
