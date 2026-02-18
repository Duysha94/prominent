package uk.co.utrendstore.app.ui

import android.view.LayoutInflater
import android.view.ViewGroup
import androidx.recyclerview.widget.RecyclerView
import coil.load
import uk.co.utrendstore.app.R
import uk.co.utrendstore.app.data.ProductItem
import uk.co.utrendstore.app.databinding.ItemProductBinding

class ProductAdapter(
    private val onClick: (ProductItem) -> Unit
) : RecyclerView.Adapter<ProductAdapter.ProductViewHolder>() {

    private val items = mutableListOf<ProductItem>()

    fun submitList(list: List<ProductItem>) {
        items.clear()
        items.addAll(list)
        notifyDataSetChanged()
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ProductViewHolder {
        val inflater = LayoutInflater.from(parent.context)
        val binding = ItemProductBinding.inflate(inflater, parent, false)
        return ProductViewHolder(binding)
    }

    override fun onBindViewHolder(holder: ProductViewHolder, position: Int) {
        holder.bind(items[position])
    }

    override fun getItemCount(): Int = items.size

    inner class ProductViewHolder(
        private val binding: ItemProductBinding
    ) : RecyclerView.ViewHolder(binding.root) {

        fun bind(item: ProductItem) {
            binding.productTitle.text = item.title
            binding.productPrice.text = item.price
            binding.productImage.load(item.imageUrl) {
                crossfade(true)
                placeholder(R.drawable.ic_launcher_foreground)
                error(R.drawable.ic_launcher_foreground)
            }
            binding.root.setOnClickListener { onClick(item) }
        }
    }
}
