@forelse ($products as $index =>  $product)
    <tr class="border-b border-gray-300">
        <td class="px-4 py-2">{{ $index + 1 }}</td>
        <td class="px-4 py-2">{{ $product->name }}</td>
        <td class="px-4 py-2">{{ $product->sku }}</td>
        <td class="px-4 py-2">{{ number_format($product->price) }}</td>
        <td 
            x-data="{ status: '{{ $product->status }}' }" 
            class="px-4 py-2"
            >
            <button 
                @click="
                fetch('/products/{{ $product->id }}/status', {
                    method: 'PUT',
                    headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ 
                    status: (status === 'active') ? 'inactive' : 'active' 
                    })
                })
                .then(response => response.json())
                .then(data => status = data.status);
                "
                :class="{
                    'bg-green-500': status === 'active',
                    'bg-red-500': status === 'inactive'
                }"
                class="text-white px-3 py-1 rounded"
            >
                <span class="uppercase" x-text="status"></span>
            </button>
        </td>
        <td class="px-4 py-2">
            @if ($product->productImages->count())
                <img src="{{ asset('storage/' . $product->productImages[0]->featured_image) }}" alt="image" class="w-30 h-18 object-cover rounded">
            @endif
        </td>
        <td class="px-4 py-2 space-x-2">
            <button @click="openModal('view', {{ $product }})" title="View" class="cursor-pointer bg-blue-500 hover:bg-blue-600 text-white p-2 rounded transition">
                <i data-lucide="eye" class="w-4 h-4"></i>
            </button>
            <button @click="openModal('edit', {{ $product }})" title="Edit" class="cursor-pointer bg-green-500 hover:bg-green-600 text-white p-2 rounded transition">
                <i data-lucide="pencil" class="w-4 h-4"></i>
            </button>
            <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="inline-block"
                onsubmit="return confirm('are you sure want to delete this product?')" >
                @csrf
                @method('DELETE')
                <button type="submit" title="Delete" class="cursor-pointer bg-red-500 hover:bg-red-600 text-white p-2 rounded transition">
                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                </button>
            </form>
        </td>
    </tr>
@empty
    <tr class="border-b border-gray-300">
        <td colspan="7" class="px-4 py-2">No Products Found.</td>
    </tr>
@endforelse