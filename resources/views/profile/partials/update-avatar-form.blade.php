<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Photo de profil') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __("Mettez à jour votre photo de profil.") }}
        </p>
    </header>

    <form method="post" action="{{ route('avatar.store') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
        @csrf

        <div class="flex items-center gap-4">
            <div class="flex-shrink-0">
                <x-user-avatar :user="$user" size="lg" id="avatar-preview-img" />
            </div>

            <div class="flex-1">
                <input type="file" name="avatar" id="avatar" class="block w-full text-sm text-gray-500
                    file:mr-4 file:py-2 file:px-4
                    file:rounded-full file:border-0
                    file:text-sm file:font-semibold
                    file:bg-violet-50 file:text-violet-700
                    hover:file:bg-violet-100"
                    accept="image/*"
                    onchange="previewImage(this)"
                />
                <p class="mt-1 text-sm text-gray-500">
                    PNG, JPG ou GIF jusqu'à 2MB
                </p>
                @error('avatar')
                    <p class="mt-2 text-sm text-danger">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <script>
            function previewImage(input) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        var previewImg = document.getElementById('avatar-preview-img');
                        if (previewImg) {
                            previewImg.src = e.target.result;
                        } else {
                            console.error('Image preview element not found');
                        }
                    }
                    reader.readAsDataURL(input.files[0]);
                }
            }
        </script>

        @if (session('status') === 'avatar-updated')
            <p
                x-data="{ show: true }"
                x-show="show"
                x-transition
                x-init="setTimeout(() => show = false, 2000)"
                class="text-sm text-gray-600 dark:text-gray-400"
            >{{ __('Photo de profil mise à jour.') }}</p>
        @endif

        <div class="d-flex justify-content-center gap-3">
            <x-primary-button>{{ __('Enregistrer') }}</x-primary-button>
            @if($user->avatar)
                <form method="post" action="{{ route('avatar.destroy', $user->avatar) }}" class="d-inline">
                    @csrf
                    @method('delete')
                    <x-danger-button>{{ __('Supprimer') }}</x-danger-button>
                </form>
            @endif
        </div>
    </form>
</section>
