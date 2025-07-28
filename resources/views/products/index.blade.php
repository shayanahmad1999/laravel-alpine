@extends('layouts.app')
 @section('title', content: 'Products')
@section('content')

<div x-data="productManager()" x-init="init()" class="py-4">

    @if (session('success'))
        <div 
            x-data="{ show: true }" 
            x-init="setTimeout(() => show = false, 4000)" 
            x-show="show" 
            x-transition 
            class="bg-green-200 text-green-900 px-3 border border-green-400 py-2 rounded mb-4"
        >
            {{ session('success') }}
        </div>
    @elseif (session('error'))
        <div 
            x-data="{ show: true }" 
            x-init="setTimeout(() => show = false, 4000)" 
            x-show="show" 
            x-transition 
            class="bg-red-200 text-red-900 px-3 border border-red-400 py-2 rounded mb-4"
        >
            {{ session('error') }}
        </div>
    @endif


    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Product List</h1>

        <button @click="openModal('create')" class="flex items-center cursor-pointer bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded">
            <i data-lucide="plus-circle" class="h-4 w-4 mr-1"></i>Add Product
        </button>
    </div>
    
    <div class="bg-white shadow-md rounded overflow-x-auto">
        <table class="w-full text-sm text-left rtl:text-right ">
            <thead>
                <tr class="bg-gray-100 text-left border-b border-gray-400">
                    <th class="px-6 py-3">
                        #
                    </th>
                    <th class="px-4 py-2 font-semibold">
                        NAME
                    </th>
                    <th class="px-4 py-2 font-semibold">
                        SKU
                    </th>
                    <th class="px-4 py-2 font-semibold flex items-center">
                        PRICE <i data-lucide="circle-dollar-sign" class="w-4 h-4 ml-1"></i>
                    </th>
                    <th class="px-4 py-2 font-semibold">
                        STATUS
                    </th>
                    <th class="px-4 py-2 font-semibold">
                        FEATURE IMAGES
                    </th>
                    <th class="px-4 py-2 font-semibold">
                        ACTION
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $product)
                    <tr class="border-b border-gray-300">
                        <td class="px-4 py-2">{{ $product->index + 1 }}</td>
                        <td class="px-4 py-2">{{ $product->name }}</td>
                        <td class="px-4 py-2">{{ $product->sku }}</td>
                        <td class="px-4 py-2">{{ number_format($product->price) }}</td>
                        <td class="px-4 py-2 capitalize">{{ $product->status }}</td>
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
            </tbody>
        </table>
    </div>
    @include('products.partials.product-modal')

    @if ($errors->any())
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.store('productStore', {
                    isModalOpen: true,
                });
            });
        </script>
    @endif
</div>

@endsection

@push('scripts')
    <script>
        function productManager(){
            return {
                isModalOpen: false,
                mode: 'create',
                modalTitle: 'Create Product',
                form: productManager.defaultForm(),
                imagePreviews: [],
                errors: [],
                isView: false,

                init() {
                    if (Alpine.store('productStore')?.isModalOpen) {
                        this.openModal('create');
                        Alpine.store('productStore').isModalOpen = false;
                    }
                },

                openModal(type, product = null) {
                    this.mode = type;
                    this.isView = type === 'view'
                    this.modalTitle = this.isView ? 'Product Details' : (type === 'edit' ? 'Update Product' : 'Create Product');
                    this.errors = [];
                    this.form = productManager.defaultForm();

                    if(product) {
                        Object.assign(this.form, {
                            id: product.id,
                            name: product.name,
                            price: product.price,
                            status: product.status,
                            description: product.description,
                            existingImages: product.product_images.map(img => img.featured_image)
                        });
                        this.imagePreviews = product.product_images.map(img => ({
                            url: `/storage/${img.featured_image}`,
                            type: 'existing',
                            path: img.featured_image
                        }));
                    } else {
                        this.imagePreviews = [];
                    }

                    this.isModalOpen = true;
                },

                closeModal(){
                    this.isModalOpen = false;
                },

                handleImage(event) {
                    const files = Array.from(event.target.files);
                    this.processFilesHandling(files);
                },

                handleDrop(event) {                    
                    const files = Array.from(event.dataTransfer.files);
                    this.processFilesHandling(files);

                    const dataTransfer = new DataTransfer();
                    files.forEach(file => dataTransfer.items.add(file));
                    this.$refs.fileInput.files = dataTransfer.files;
                },

                processFilesHandling(files) {
                    files.forEach(file => {
                        if(file.type.startsWith('image')) {
                            this.form.images.push(file)
                            this.imagePreviews.push({
                                url: URL.createObjectURL(file),
                                type: 'new',
                                file
                            });
                        } else {
                            this.errors.push(`${file.name} is not valid image file.`)
                        }
                    })
                },
                removeImage(index) {
                    const image = this.imagePreviews[index];
                    if (image.type === 'existing') {
                        this.form.existingImages = this.form.existingImages.filter(
                            path => path !== image.featured_image
                        );
                    } else if (image.type === 'new') {
                        const findIndex = this.form.images.findIndex(
                            file => URL.createObjectURL(file) === image.url
                        );
                        if (findIndex !== -1) {
                            this.form.images.splice(findIndex, 1);
                        }
                    }

                    URL.revokeObjectURL(image.url);
                    this.imagePreviews.splice(index, 1);
                }
            };
        }

        productManager.defaultForm = function() {
            return {
                name: '',
                price: '',
                status: '',
                description: '',
                images: [],
                existingImages: []
            }
        }
    </script>
@endpush