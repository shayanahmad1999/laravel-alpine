<div x-show="isModalOpen" class="fixed inset-0 bg-black bg-opicity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-2xl p-8">
        <h2 class="text-2xl font-bold mb-6">Create Product</h2>
        <form>
        <div class="mb-5">
            <label for="name" class="block mb-2 text-sm font-medium">NAME</label>
            <input type="text" id="name" class="w-full shadow-xs bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5" placeholder="Watch..." required />
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
            <div class="mb-5">
                <label for="price" class="block mb-2 text-sm font-medium">PRICE</label>
                <input type="number" id="price" step="0.1" class="shadow-xs bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="10.00" required />
            </div>
            <div class="mb-5">
                <label for="status" class="block mb-2 text-sm font-medium text-gray-900">STATUS</label>
                <select id="status" class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5">
                    <option>SELECT STATUS</option>
                    <option value="active">ACTIVE</option>
                    <option value="inactive">IN-ACTIVE</option>
                </select>
            </div>
        </div>
        <div class="mb-5">
            <label for="description" class="block mb-2 text-sm font-medium">DESCRIPTION</label>
            <textarea id="description" class="shadow-xs bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required placeholder="Description..."></textarea>
        </div>
        <div class="mb-5">
            <label for="featured-image" class="block mb-2 text-sm font-medium">FEATURED IMAGES</label>
            <div @click="$refs.fileInput.click()" @dragover.prevent @drop.prevent="handleDrop($event)"
            class="w-full border-2 border-dashed border-purple-400 px-5 py-20 rounded-lg text-center bg-gray-50 cursor-pointer hover:bg-gray-100 transition">
                <input @change="handleImage($event)" type="file" class="hidden" name="images[]" multiple accept="image/*" x-ref="fileInput">

                <p class="text-purple-600 font-semibold flex items-center justify-center">
                    <i data-lucide="upload" class="mr-2"></i> Click or Drag Image to Upload
                </p>
                <p class="text-xs text-gray-500 mt-1.5">you can select multiple images.</p>
            </div>
        </div>

        <div class="text-red-600 text-sm space-y-1">
            <template x-for="(error, index) in errors" :key="index">
                <div x-text="error" class="text-red-500"></div>
            </template>
        </div>

        <div class="mt-4 grid grid-cols-2 sm:grid-cols-3 gap-4">
            <template x-for="(img, index) in imagePreviews" :key="index">
                <div class="relative group w-full h-36 rounded overflow-hidden shadow-md border-gray-300">
                    <img :src="img.url" class="w-full h-full object-cover" />
                    <button @click="removeImage(index)" type="button" class="cursor-pointer z-999 absolute top-1 right-1 bg-red-500 text-white text-xs px-2 py-1 rounded opacity-90 group-hover:opacity-100 transition" title="Remove">X</button>
                </div>
            </template>
        </div>

        <div class="flex justify-end space-x-3 pt-4">
            <button @click="closeModal" type="button" class="cursor-pointer bg-gray-300 hover:bg-gray-700 text-black px-6 py-2 rounded shadow hover:text-white">Cancel</button>
            <button type="submit" class="cursor-pointer bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded shadow">Save</button>
        </div>
        </form>
    </div>
</div>