package uk.co.utrendstore.app

import android.net.Uri
import android.os.Bundle
import android.view.View
import androidx.appcompat.app.AppCompatActivity
import androidx.browser.customtabs.CustomTabsIntent
import androidx.recyclerview.widget.LinearLayoutManager
import kotlinx.coroutines.CoroutineScope
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.Job
import kotlinx.coroutines.cancel
import kotlinx.coroutines.launch
import kotlinx.coroutines.withContext
import uk.co.utrendstore.app.data.StoreRepository
import uk.co.utrendstore.app.databinding.ActivityMainBinding
import uk.co.utrendstore.app.ui.ProductAdapter

class MainActivity : AppCompatActivity() {

    private lateinit var binding: ActivityMainBinding
    private val repository = StoreRepository()
    private val activityScope = CoroutineScope(Dispatchers.Main + Job())

    private val adapter = ProductAdapter { item ->
        val tabIntent = CustomTabsIntent.Builder().build()
        tabIntent.launchUrl(this, Uri.parse(item.productUrl))
    }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityMainBinding.inflate(layoutInflater)
        setContentView(binding.root)

        binding.recyclerView.layoutManager = LinearLayoutManager(this)
        binding.recyclerView.adapter = adapter

        binding.swipeRefresh.setOnRefreshListener { loadProducts(forceRefresh = true) }

        loadProducts(forceRefresh = false)
    }

    override fun onDestroy() {
        activityScope.cancel()
        super.onDestroy()
    }

    private fun loadProducts(forceRefresh: Boolean) {
        if (!forceRefresh) {
            binding.progressBar.visibility = View.VISIBLE
        }
        binding.errorText.visibility = View.GONE

        activityScope.launch {
            runCatching {
                withContext(Dispatchers.IO) {
                    repository.fetchProducts(limit = 30)
                }
            }.onSuccess { products ->
                adapter.submitList(products)
                binding.errorText.visibility = if (products.isEmpty()) View.VISIBLE else View.GONE
                if (products.isEmpty()) {
                    binding.errorText.text = getString(R.string.empty_products)
                }
            }.onFailure {
                binding.errorText.visibility = View.VISIBLE
                binding.errorText.text = getString(R.string.load_error)
            }

            binding.progressBar.visibility = View.GONE
            binding.swipeRefresh.isRefreshing = false
        }
    }
}
