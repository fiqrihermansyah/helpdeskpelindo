<!-- resources/views/tiket/ajukan.blade.php -->

@extends(Auth::user()->roles->contains('id', 1) ? 'layout.layout' : 'layouts.app')

@section("title","Ajukan Topik")

@section("content")
    <body>
        <h1 class="text-5xl font-bold text-gray-900 py-4 text-center tracking-wide leading-snug">Pengajuan Topik</h1>
        <div class='flex container mx-auto sm:px-12'>
            <a href='{{ route('user.tiketsaya') }}' class="text-black text-md focus:ring-3 focus:ring-blue-300 font-semibold rounded-lg px-5 py-2 mb-2 bg-blue-200 hover:bg-blue-300">
                <svg width="24px" height="24px" viewBox="0 0 1024 1024" xmlns="http://www.w3.org/2000/svg">
                    <path fill="#000000" d="M224 480h640a32 32 0 1 1 0 64H224a32 32 0 0 1 0-64z"/>
                    <path fill="#000000" d="m237.248 512 265.408 265.344a32 32 0 0 1-45.312 45.312l-288-288a32 32 0 0 1 0-45.312l288-288a32 32 0 1 1 45.312 45.312L237.248 512z"/>
                </svg>
            </a>
        </div>
        <form action='{{ route('user.store') }}' method='POST' enctype="multipart/form-data">
            @csrf
            <div class="mt-2 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6 px-60 pb-10 min-w-7xl">
                <div class="col-span-full">
                    <label for="judul" class="block text-sm font-medium leading-6 text-gray-900">Judul:</label>
                    <div class="mt-2">
                        <input required type="text" name="judul" value="{{ Session::get('judul')}}" id="judul" autocomplete="judul" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6">
                    </div>
                </div>

                <div class="col-span-full">
                    <label for="pengaju" class="block text-sm font-medium leading-6 text-gray-900">Pengaju:</label>
                    <div class="mt-2">
                        <input required type="text" name="pengaju" value="{{ Auth::user()->nama }}" id="pengaju" autocomplete="pengaju" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6">
                    </div>
                </div>

                <div class="col-span-full">
                    <label for="aplikasi" class="block text-sm font-medium leading-6 text-gray-900">Aplikasi:</label>
                    <div class="mt-2">
                        <select id="aplikasi" name="aplikasi" autocomplete="aplikasi" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:max-w-xs sm:text-sm sm:leading-6">
                            <option>Simop Kapal</option>
                            <option>FS</option>
                            <option>Sampah Kapal</option>
                            <option>Kepil</option>
                            <option>Rupa-rupa</option>
                            <option>Barang</option>
                        </select>
                    </div>
                </div>

                <div class="sm:col-span-3">
                    <label for="prioritas" class="block text-sm font-medium leading-6 text-gray-900">Prioritas:</label>
                    <div class="mt-2">
                        <select id="prioritas_id" name="prioritas_id" autocomplete="prioritas_id" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:max-w-xs sm:text-sm sm:leading-6">
                            @foreach ($prioritasOptions as $option)
                                <option value="{{ $option->id }}">{{ $option->nama_prioritas }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="sm:col-span-3">
                    <label for="divisi" class="block text-sm font-medium leading-6 text-gray-900">Divisi:</label>
                    <div class="mt-2">
                        <select id="divisi_id" name="divisi_id" autocomplete="divisi_id" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:max-w-xs sm:text-sm sm:leading-6">
                            @foreach ($divisiOptions as $option)
                                <option value="{{ $option->id }}">{{ $option->nama_divisi }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-span-full">
                    <label for="street-address" class="block text-sm font-medium leading-6 text-gray-900">Deskripsi:</label>
                    <div class="mt-2">
                        <textarea required id="deskripsi" name="deskripsi" value="{{ Session::get('deskripsi') }}" rows="3" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6"></textarea>
                    </div>
                    <p class="mt-3 text-sm leading-6 text-gray-600">Tulis deskripsi untuk tiket yang akan diaju.</p>
                </div>
                
                <div class="col-span-full">
                    <label for="cover-photo" class="block text-sm font-medium leading-6 text-gray-900">Lampiran:</label>
                    <div class="mt-2 flex justify-center rounded-lg border border-dashed border-gray-900/25 px-6 py-10">
                        <div class="text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-300" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M1.5 6a2.25 2.25 0 012.25-2.25h16.5A2.25 2.25 0 0122.5 6v12a2.25 2.25 0 01-2.25 2.25H3.75A2.25 2.25 0 011.5 18V6zM3 16.06V18c0 .414.336.75.75.75h16.5A.75.75 0 0021 18v-1.94l-2.69-2.689a1.5 1.5 0 00-2.12 0l-.88.879.97.97a.75.75 0 11-1.06 1.06l-5.16-5.159a1.5 1.5 0 00-2.12 0L3 16.061zm10.125-7.81a1.125 1.125 0 112.25 0 1.125 1.125 0 01-2.25 0z" clip-rule="evenodd"/>
                            </svg>
                            <div class="mt-4 flex text-sm leading-6 text-gray-600">
                                <label for="formFile" class="relative cursor-pointer rounded-md bg-white font-semibold text-blue-600 focus-within:outline-none focus-within:ring-2 focus-within:ring-blue-600 focus-within:ring-offset-2 hover:text-blue-500">
                                    <input id="formFile" name="formFile" type="file" class='form-control'>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <button type='submit' name='submit' class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-md mt-5">Ajukan</button>
                </div>
            </div>
        </form>
    </body>
@endsection
