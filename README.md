# Utrend Store Android App

Android-приложение для публикации в Google Play, которое открывает сайт `https://utrendstore.co.uk/` внутри безопасного `WebView`.

## Что уже реализовано

- Kotlin + AndroidX проект.
- Встроенный `WebView` c поддержкой JavaScript и DOM storage.
- Pull-to-refresh (свайп вниз для обновления страницы).
- Корректная обработка кнопки «Назад» (возврат по истории сайта).
- Базовая брендированная тема и иконка.

## Требования

- Android Studio Iguana+ / Android Gradle Plugin 8.5+
- JDK 17
- Android SDK 34

## Запуск

1. Откройте папку проекта в Android Studio.
2. Дождитесь синхронизации Gradle.
3. Запустите `app` на эмуляторе или устройстве.

## Подготовка к Google Play

1. Измените `applicationId` в `app/build.gradle` при необходимости.
2. Обновите `versionCode`/`versionName` перед релизом.
3. Подпишите release-сборку своим keystore.
4. Подготовьте политику конфиденциальности и карточку приложения в Play Console.

## Дальше можно добавить

- Deep links (чтобы ссылки `utrendstore.co.uk` открывались сразу в приложении).
- Push-уведомления через Firebase.
- Локальный экран ошибок при отсутствии интернета.
