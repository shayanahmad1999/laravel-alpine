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

    <div class="flex items-center mb-4 gap-3">
        <form method="GET" action="{{ route('products.index') }}" class="flex space-x-4 mb-4 w-full">
            <select name="sku" class="w-1/4 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5">
                <option value="">SELECT SKU</option>
                @foreach ($skus as $sku)
                    <option value="{{ $sku }}" @if(request('sku') == $sku) selected @endif>{{ $sku }}</option>
                @endforeach
            </select>

            <input type="text" name="name" value="{{ request('name') }}" placeholder="Search by Name..." class="w-1/4 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5" />

            <input type="number" step="0.01" name="price" value="{{ request('price') }}" placeholder="Search by Price..." class="w-1/4 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5" />

            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">Search</button>
            <a href={{ route('products.index') }} class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded">Reset</a>
        </form>
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
                @include('products.partials.product-table', ['products' => $products])
            </tbody>
        </table>
    </div>
    <div class="mt-3 py-3">{{$products->links()}}</div>
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