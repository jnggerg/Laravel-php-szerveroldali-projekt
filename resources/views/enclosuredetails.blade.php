<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            <p>{{ $enclosure->name }} kifutó adatai:</p>
            <p align="right"><a href="/dashboard"><button>Visszaa főoldalra</button></a></p>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <p>Kifutó maximum kapacitása: {{ $enclosure->limit }} </p>
                    <p align="right">
                        Válaszüzenetek: <br>
                        @if (session('success'))
                            {{ session('success') }}
                        @endif
                        @if (session('error'))
                            {{ session('error') }}
                        @endif
                        @if ($errors->any())
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li align="right">{{ $error }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </p>


                </div>
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    Jelenlegi állatok száma:
                    {{ $enclosure->animals()->count() }}
                </div>
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    Az állatok:
                    <table cellpadding="8">
                        <thead>
                            <tr>
                                <th>Állat neve</th>
                                <th>Állatfaj</th>
                                <th>Ragadozó?</th>
                                <th>Születés ideje</th>
                                <th>Kép az állatról</th>
                                @if ($current_user->isAdmin())
                                    <th>Állat Szerkesztése</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $animals = $enclosure->animals()->orderBy('species')->orderBy('born_at', 'asc')->get();
                                $contains_predator = false;
                            @endphp
                            @foreach ($animals as $animal)
                                <tr>
                                    <th>{{ $animal->name }}</th>
                                    <th>{{ $animal->species }}</th>
                                    @if ($animal->is_predator)
                                        <th>igen</th>
                                        @php
                                            $contains_predator = true;
                                        @endphp
                                    @else
                                        <th>nem</th>
                                    @endif
                                    <th>{{ $animal->born_at }}</th>
                                    @if ($animal->kep !== null)
                                    <th><img src="{{ asset('storage/' . $animal->kep) }}" alt="{{ $animal->name }}"
                                            style="max-width: 100px;"></th>
                                    @else
                                    <th><img src="{{ asset('storage/' . "animals/placeholder.png") }}" alt="{{ $animal->name }}"
                                        style="max-width: 100px;"></th>

                                    @endif
                                    @if ($current_user->isAdmin())
                                        <form
                                            action="{{ route('enclosuredetails.edit_animal', ['enclosure' => $enclosure->id, 'animal' => $animal->id]) }}"
                                            method="POST" style="width: 50px; height: 15px;"
                                            enctype="multipart/form-data">
                                            @csrf
                                            <th><input type="text" name="name" placeholder="Állat neve"
                                                    value="{{$animal->name }}" required
                                                    style="color: black; background: white; width: 100px; height: 15px;">
                                            </th>
                                            <th><input type="text" name="species" placeholder="Állatfaj"
                                                    value="{{$animal->species}}" required
                                                    style="color: black; background: white; width: 100px; height: 5px;">
                                            </th>
                                            <th><input type="date" name="born_at" placeholder="Születési ideje"
                                                    value="{{ \Carbon\Carbon::parse($animal->born_at)->format('Y-m-d')}}"
                                                    required
                                                    style="color: black; background: white; width: 150px; height: 5px;">
                                            </th>
                                            <th><input type="hidden" name="is_predator"
                                                    value="{{ $animal->is_predator ? 0 : 1 }}"></th>
                                            <th><input type="file" name="image" accept="image/*"
                                                    style="width: 100px; height: 30px;"></th>
                                            <th><button type="submit">Submit</button></th>
                                        </form>
                                        <form
                                            action="{{ route('enclosuredetails.del_animal', ['enclosure' => $enclosure->id, 'animal' => $animal->id]) }}"
                                            method="POST">
                                            @csrf
                                            <th><button type="submit">Archiválás</button></th>
                                        </form>
                                    @endif

                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if ($current_user->isAdmin())
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        Új állat létrehozása
                        <form action="{{ route('enclosuredetails.new_animal', ['enclosure' => $enclosure->id]) }}"
                            method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="text" name="name" placeholder="Állat neve" value="{{ old('name') }}"
                                required style="color: black; background: white;">
                            <input type="text" name="species" placeholder="Állatfaj" value="{{ old('species') }}"
                                required style="color: black; background: white;">
                            <input type="date" name="born_at" placeholder="Születési ideje"
                                value="{{ old('born_at') }}" required style="color: black; background: white;">
                            @if ($enclosure->animals()->count() > 0)
                                <!-- Ha üres az enclosure, el lehet dönteni hogy ragadozók legyenek vagy ne-->
                                <input type="hidden" name="is_predator" value="{{ $contains_predator ? 1 : 0 }}">
                            @else
                                Legyen Ragadozó?
                                <input type="hidden" name="is_predator" value="0">
                                <input type="checkbox" name="is_predator" placeholder="Ragadozó?" value="1"
                                    {{ old('is_predator') ? 'checked' : '' }}>
                            @endif
                            <input type="file" name="image" value="{{ old('image') }}" required
                                style="color: black; background: white;" accept="image/*">
                            <button type="submit">Submit</button>
                        </form>

                        @if (session('success'))
                            {{ session('success') }}
                        @endif
                        @if (session('error'))
                            {{ session('error') }}
                        @endif
                        @if ($errors->any())
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        @endif
                @endif
            </div>
        </div>
    </div>
    </div>
</x-app-layout>
