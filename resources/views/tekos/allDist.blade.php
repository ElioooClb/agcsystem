<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200 title_page">
            Historiques des heures déclarées des techniciens supprimés
        </h2>
    </x-slot>


    <!--/Preloader-->
    <div class="wrapper box-layout theme-1-active pimary-color-blue">
        <div class="container-fluid">
            <div class="col-sm-12">
                <div class="panel panel-default workSiteListing">
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
                                                @if ($user->email == null)
                                                    <tr>
                                                        <td>{{ $user->name }}</td>
                                                        <td>{{ $user->email }}</td>
                                                        <td>{{ $user->fonction }}</td>

                                                        <td><a class="btn btn-primary"
                                                                href="{{ route('tekosTime.show', $user->id) }}">Voir</a>
                                                        </td>
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
    @endpush

</x-app-layout>
