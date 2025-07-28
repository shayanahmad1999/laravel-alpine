@extends('layouts.app')
 @section('title', content: 'Products')
@section('content')

<div x-data="productManager()" class="py-4">
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
                <tr class="border-b border-gray-300">
                    <td class="px-4 py-2"></td>
                    <td class="px-4 py-2"></td>
                    <td class="px-4 py-2"></td>
                    <td class="px-4 py-2"></td>
                    <td class="px-4 py-2"></td>
                    <td class="px-4 py-2"></td>
                    <td class="px-4 py-2"></td>
                </tr>
            </tbody>
        </table>
    </div>
    @include('products.partials.product-modal')
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

                openModal(type) {
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