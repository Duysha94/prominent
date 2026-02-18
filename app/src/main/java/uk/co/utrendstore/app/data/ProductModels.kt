package uk.co.utrendstore.app.data

import kotlinx.serialization.SerialName
import kotlinx.serialization.Serializable
import java.util.Locale

@Serializable
data class ProductDto(
    val id: Long,
    val name: String,
    val permalink: String,
    val prices: PricesDto,
    val images: List<ImageDto> = emptyList()
)

@Serializable
data class PricesDto(
    val price: String,
    @SerialName("currency_symbol") val currencySymbol: String,
    @SerialName("currency_minor_unit") val currencyMinorUnit: Int,
    @SerialName("currency_prefix") val currencyPrefix: String = "",
    @SerialName("currency_suffix") val currencySuffix: String = ""
)

@Serializable
data class ImageDto(
    val src: String
)

data class ProductItem(
    val id: Long,
    val title: String,
    val price: String,
    val imageUrl: String?,
    val productUrl: String
)

fun ProductDto.toProductItem(): ProductItem {
    val divider = 10.0.pow(prices.currencyMinorUnit)
    val amount = prices.price.toDoubleOrNull()?.div(divider)
    val formattedAmount = if (amount == null) "" else String.format(Locale.US, "%.2f", amount)
    val priceLabel = "${prices.currencyPrefix}${formattedAmount}${prices.currencySuffix}".trim()

    return ProductItem(
        id = id,
        title = name,
        price = priceLabel.ifBlank { prices.currencySymbol },
        imageUrl = images.firstOrNull()?.src,
        productUrl = permalink
    )
}

private fun Double.pow(power: Int): Double {
    var result = 1.0
    repeat(power) {
        result *= 10.0
    }
    return result
}
