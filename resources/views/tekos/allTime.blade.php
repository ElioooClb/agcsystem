<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200 title_page">
            Heures déclarées des techniciens
        </h2>
    </x-slot>


    <!--/Preloader-->
    <div class="wrapper box-layout theme-1-active pimary-color-blue">
        <div class="container-fluid">
            <div class="col-sm-12">
                <div class="panel panel-default workSiteListing">
                    <!-- Début [SPECGT4] ajout checkbox -->
                    <!-- Check if the currently authenticated user is an admin -->
                    @if (Auth::user()->role_id == 1)
                        <div class="details">
                            <input type="checkbox" name="checkDetails" id="checkDetails">
                            <label for="checkDetails">Détails</label>
                        </div>
                    @endif
                    <!-- Fin [SPECGT4] ajout checkbox -->
                    <div class="panel-wrapper collapse in">
                        <div class="panel-body">
                            <div class="table-wrap">
                                <div class="table-responsive">
                                    <table id="example" class="table table-hover display pb-30">
                                        <thead>
                                            <tr>
                                                <th>Nom</th>
                                                <th>Mail</th>
                                                <th>Fonction</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tfoot>
                                            <tr>
                                                <th>Nom</th>
                                                <th>Mail</th>
                                                <th>Fonction</th>
                                                <th>Action</th>
                                            </tr>
                                        </tfoot>
                                        <tbody>
                                            @foreach ($users as $user)
                                                @if ($user->email != null)
                                                    <tr>
                                                        <td>{{ $user->name }}</td>
                                                        <td>{{ $user->email }}</td>
                                                        <td>{{ $user->fonction }}</td>

                                                        <!-- Début [SPECGT4] ajout variantes de l'url -->
                                                        <td>
                                                            <a class="btn btn-primary seeLink"
                                                                data-url-tekostime="{{ route('tekosTime.show', $user->id) }}"
                                                                data-url-time="{{ route('time.shows', $user->id) }}"
                                                                href="{{ route('tekosTime.show', $user->id) }}">Voir</a>
                                                        </td>
                                                        <!-- Fin [SPECGT4] ajout variantes de l'url -->
                                                    </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <!-- /#wrapper -->
    @push('table')
        <!-- Début [SPECGT4] modification de l'url en fonction de l'état de la checkbox -->
        <script>
            // Define a function to update the href of the links based on the checkbox state
            const updateLinks = (isChecked) => {
                // Select all the links with the class "seeLink"
                const links = document.querySelectorAll(".seeLink");
                // Loop through each link
                links.forEach(link => {
                    // If the checkbox is checked
                    if (isChecked) {
                        // Set the href of the link to the value of the 'data-url-time' attribute
                        link.href = link.getAttribute('data-url-time');
                    } else {
                        // Otherwise, set the href of the link to the value of the 'data-url-tekostime' attribute
                        link.href = link.getAttribute('data-url-tekostime');
                    }
                });
            };

            // When the page is loaded
            window.addEventListener("pageshow", function() {
                // Check if the checkbox is checked
                const isChecked = document.querySelector("#checkDetails").checked;
                // Update the links based on the checkbox state
                updateLinks(isChecked);
            });

            // When the checkbox state changes
            document.querySelector("#checkDetails").addEventListener("change", function() {
                // Update the links based on the new checkbox state
                updateLinks(this.checked);
            });
        </script>
        <!-- Fin [SPECGT4] modification de l'url en fonction de l'état de la checkbox -->
    @endpush

</x-app-layout>
