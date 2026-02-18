# Keep minimal WebView classes for reflection use.
-keepclassmembers class * {
    @android.webkit.JavascriptInterface <methods>;
}
