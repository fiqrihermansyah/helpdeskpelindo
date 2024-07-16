@extends(Auth::user()->roles->contains('id', 1) ? 'layout.layout' : 'layouts.app')

@section("title", "Daftar User")

@section("content")
<!-- component -->
<body class="antialiased font-sans">
    <div class="container mx-auto px-4 sm:px-8">
        <div class="py-8">
            <div>
                <h1 class="text-5xl font-bold text-gray-900 py-4 text-center tracking-wide leading-snug">Daftar User</h1>
            </div>
            <form action="" method="GET">
                <div class="flex">
                    <span class="h-full absolute pl-2 py-3">
                        <svg viewBox="0 0 24 24" class="h-4 w-4 fill-current text-gray-500">
                            <path d="M10 4a6 6 0 100 12 6 6 0 000-12zm-8 6a8 8 0 1114.32 4.906l5.387 5.387a1 1 0 01-1.414 1.414l-5.387-5.387A8 8 0 012 10z"></path>
                        </svg>
                    </span>
                    <input placeholder="Search" name="search" id="search" class="appearance-none rounded-md border border-gray-400 border-b block pl-8 pr-6 bg-white text-sm placeholder-gray-400 text-gray-700 focus:bg-white focus:placeholder-gray-600 focus:text-gray-700 focus:outline-none" />
                    <div class="px-2">
                        <button type="submit" class="absolute px-4 h-10 text-gray-700 bg-gray-200 hover:bg-gray-300 rounded-md">
                            <svg viewBox="0 0 24 24" class="h-4 w-4 fill-current">
                                <path d="M15.414 16.586a8 8 0 10-1.414 1.414l5.387 5.387a1 1 0 001.414-1.414l-5.387-5.387zM4 10a6 6 0 1112 0 6 6 0 01-12 0z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </form>
        </div>
        <div class="-mx-8 sm:-mx-8 px-4 sm:px-8 py-4 overflow-x-auto">
            <div class="inline-block min-w-full shadow rounded-lg overflow-hidden">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs text-gray-800 uppercase bg-blue-50">
                        <tr>
                            <th scope="col" class="px-5 py-3">Nama</th>
                            <th scope="col" class="px-5 py-3">NIPP</th>
                            <th scope="col" class="px-5 py-3">Divisi</th>
                            <th scope="col" class="px-5 py-3">Nomor Handphone</th>
                            <th scope="col" class="px-5 py-3">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $item)
                        <tr>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <p class="text-gray-900 whitespace-no-wrap">{{ $item->nama }}</p>
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <p class="text-gray-900 whitespace-no-wrap">{{ $item->nipp }}</p>
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <p class="text-gray-900 whitespace-no-wrap">{{ $item->divisi->nama_divisi }}</p>
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <p class="text-gray-900 whitespace-no-wrap">{{ $item->nomor_hp }}</p>
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <form action="{{ route('attach.roles') }}" method="POST">
                                    @csrf
                                    <input name="user_id" type="hidden" value="{{ $item->id }}">
                                    @foreach($roles as $role)
                                    <div>
                                        <input class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500" type="checkbox" id="role{{ $role->id }}" name="role_ids[]" value="{{ $role->id }}" {{ $item->roles->contains('id', $role->id) ? 'checked' : '' }}>
                                        <label class="pl-1" for="role{{ $role->id }}">{{ $role->name }}</label>
                                    </div>
                                    @endforeach
                                    <button class="text-blue-500 hover:text-white border border-blue-500 hover:bg-blue-600 focus:ring-3 focus:outline-none focus:ring-blue-100 font-medium rounded-lg text-sm px-3 py-2 text-center mt-2" type="submit">Attach Roles</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="px-5 py-5 bg-white border-t flex flex-col items-center">
                    {{ $users->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
</body>
@endsection
