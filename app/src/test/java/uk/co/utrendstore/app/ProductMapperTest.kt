package uk.co.utrendstore.app

import org.junit.Assert.assertEquals
import org.junit.Assert.assertTrue
import org.junit.Test
import uk.co.utrendstore.app.data.ImageDto
import uk.co.utrendstore.app.data.PricesDto
import uk.co.utrendstore.app.data.ProductDto
import uk.co.utrendstore.app.data.toProductItem

class ProductMapperTest {

    @Test
    fun `maps wc product dto into ui model`() {
        val dto = ProductDto(
            id = 1,
            name = "Dress",
            permalink = "https://utrendstore.co.uk/product/dress",
            prices = PricesDto(
                price = "18000",
                currencySymbol = "€",
                currencyMinorUnit = 2,
                currencyPrefix = "",
                currencySuffix = " €"
            ),
            images = listOf(ImageDto(src = "https://img"))
        )

        val item = dto.toProductItem()

        assertEquals(1L, item.id)
        assertEquals("Dress", item.title)
        assertEquals("180.00 €", item.price)
        assertEquals("https://img", item.imageUrl)
        assertTrue(item.productUrl.contains("utrendstore.co.uk"))
    }
}
