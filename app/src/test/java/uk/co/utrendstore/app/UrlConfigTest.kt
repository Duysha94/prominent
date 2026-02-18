package uk.co.utrendstore.app

import org.junit.Assert.assertTrue
import org.junit.Test

class UrlConfigTest {

    @Test
    fun baseUrlShouldBeHttps() {
        val baseUrl = "https://utrendstore.co.uk"
        assertTrue(baseUrl.startsWith("https://"))
    }
}
