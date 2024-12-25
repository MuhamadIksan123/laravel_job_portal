<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Job Listing') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden p-10 shadow-sm sm:rounded-lg">

                <form method="POST" action="{{route('admin.company_jobs.update', $companyJob)}}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div>
                        <x-input-label for="name" :value="__('Name')" />
                        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" required
                            autofocus autocomplete="name" value="{{ $companyJob->name }}" />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="type" :value="__('Type')" />

                        <select name="type" id="type"
                            class="py-3 rounded-lg pl-3 w-full border border-slate-300">
                            <option value="Part-Time" {{ $companyJob->type == 'Part-Time' ? 'selected' : '' }}>Part-Time
                            </option>
                            <option value="Full-Time" {{ $companyJob->type == 'Full-Time' ? 'selected' : '' }}>Full-Time
                            </option>
                        </select>

                        <x-input-error :messages="$errors->get('type')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="salary IDR in month" :value="__('Salary IDR in month')" />
                        <x-text-input id="salary" class="block mt-1 w-full" type="number" name="salary" required
                            autofocus autocomplete="salary" value="{{ $companyJob->salary }}" />
                        <x-input-error :messages="$errors->get('salary')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="location" :value="__('Location')" />
                        <x-text-input id="location" class="block mt-1 w-full" type="text" name="location" required
                            autofocus autocomplete="location" value="{{ $companyJob->location }}" />
                        <x-input-error :messages="$errors->get('location')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="skill_level" :value="__('Skill_level')" />

                        <select name="skill_level" id="skill_level"
                            class="py-1 rounded-lg pl-3 w-full border border-slate-300">

                            <option value="Beginner" {{ $companyJob->skill_level == 'Beginner' ? 'selected' : '' }}>
                                Beginner</option>
                            <option value="Intermediate"
                                {{ $companyJob->skill_level == 'Intermediate' ? 'selected' : '' }}>Intermediate</option>
                            <option value="Expert" {{ $companyJob->skill_level == 'Expert' ? 'selected' : '' }}>Expert
                            </option>
                        </select>

                        <x-input-error :messages="$errors->get('skill_level')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="category" :value="__('Category')" />

                        <select name="category_id" id="category_id"
                            class="py-1 rounded-lg pl-3 w-full border border-slate-300">
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ $companyJob->category_id == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>

                        <x-input-error :messages="$errors->get('category')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="thumbnail" :value="__('Thumbnail')" />
                        <img src="{{ Storage::url($companyJob->thumbnail) }}" alt=""
                            class="rounded-2xl object-cover w-[120px] h-[90px]">
                        <x-text-input id="thumbnail" class="block mt-1 w-full" type="file" name="thumbnail" autofocus
                            autocomplete="thumbnail" />
                        <x-input-error :messages="$errors->get('thumbnail')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="about" :value="__('about')" />
                        <textarea name="about" id="about" cols="30" rows="5" class="border border-slate-300 rounded-xl w-full">{{ $companyJob->about }}</textarea>
                        <x-input-error :messages="$errors->get('about')" class="mt-2" />
                    </div>

                    <hr class="my-5">

                    <div class="mt-4">

                        <div class="flex flex-col gap-y-2">
                            <x-input-label for="responsibilities" :value="__('Responsibilities')" />
                            <!-- Loop untuk menampilkan responsibilities yang ada -->
                            @foreach ($companyJob->responsibilities as $responsibility)
                                <input type="text" class="py-3 rounded-lg border-slate-300 border"
                                    value="{{ $responsibility->name }}" placeholder="Write your responsibility"
                                    name="responsibilities[]">
                            @endforeach

                            {{-- <!-- Menambahkan input kosong jika responsibilities kurang dari 4 -->
                            @for ($i = $companyJob->responsibilities->count(); $i < 4; $i++)
                                <input type="text" class="py-3 rounded-lg border-slate-300 border"
                                    placeholder="Write your responsibility" name="responsibilities[]">
                            @endfor --}}
                        </div>
                        <x-input-error :messages="$errors->get('responsibilities')" class="mt-2" />
                    </div>

                    <hr class="my-5">

                    <div class="mt-4">

                        <div class="flex flex-col gap-y-2">
                            <x-input-label for="qualifications" :value="__('Qualifications')" />

                             @foreach ($companyJob->qualifications as $qualification)
                                <input type="text" class="py-3 rounded-lg border-slate-300 border"
                                    value="{{ $qualification->name }}" placeholder="Write your qualification"
                                    name="qualifications[]">
                            @endforeach

                            <!-- Menambahkan input kosong jika qualifications kurang dari 4 -->
                            {{-- @for ($i = $companyJob->qualifications->count(); $i < 4; $i++)
                                <input type="text" class="py-3 rounded-lg border-slate-300 border"
                                    placeholder="Write your qualification" name="qualifications[]">
                            @endfor --}}

                        </div>
                        <x-input-error :messages="$errors->get('qualifications')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <button type="submit" class="font-bold py-4 px-6 bg-indigo-700 text-white rounded-full">
                            Update Job
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
