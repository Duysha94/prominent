package uk.co.utrendstore.app.data

import kotlinx.serialization.builtins.ListSerializer
import kotlinx.serialization.json.Json
import okhttp3.OkHttpClient
import okhttp3.Request

class StoreRepository(
    private val client: OkHttpClient = OkHttpClient(),
    private val json: Json = Json { ignoreUnknownKeys = true }
) {

    fun fetchProducts(limit: Int = 20): List<ProductItem> {
        val request = Request.Builder()
            .url("$BASE_URL/wp-json/wc/store/products?per_page=$limit")
            .get()
            .build()

        client.newCall(request).execute().use { response ->
            if (!response.isSuccessful) {
                throw IllegalStateException("HTTP ${response.code}")
            }

            val payload = response.body?.string().orEmpty()
            val products = json.decodeFromString(ListSerializer(ProductDto.serializer()), payload)
            return products.map { it.toProductItem() }
        }
    }

    companion object {
        private const val BASE_URL = "https://utrendstore.co.uk"
    }
}
