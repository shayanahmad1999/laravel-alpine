@extends('layouts.app')
 @section('title', content: 'Products')
@section('content')

<div class="py-4">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Product List</h1>

        <button class="flex items-center cursor-pointer bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded">
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

</div>

@endsection