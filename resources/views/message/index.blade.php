<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200 title_page">
            Gestion des messages
        </h2>

    </x-slot>


    <!--/Preloader-->
    <div class="wrapper box-layout theme-1-active pimary-color-blue">
        <div class="container-fluid">
            <div class="my-4 col-sm-12">
                <a class="btn btn-primary" href="{{ route('message.create') }}">Ajouter un nouveau message</a>
            </div>
            <div class="col-sm-12">
                <div class="panel panel-default workSiteListing">
                    <div class="panel-wrapper collapse in">
                        <div class="panel-body">
                            <div class="table-wrap">
                                <div class="table-responsive">
                                    <table id="example" class="table table-hover display pb-30">
                                        <thead>
                                            <tr>
                                                <th>Message</th>

                                                <th>Date de création</th>
                                                <th>Publier le message</th>

                                                <th>Action</th>

                                            </tr>
                                        </thead>
                                        <tfoot>
                                            <tr>
                                                <th>Message</th>

                                                <th>Date de création</th>
                                                <th>Publier le message</th>

                                                <th>Action</th>
                                            </tr>
                                        </tfoot>
                                        <tbody>
                                            @foreach ($message as $item)
                                                <td>{{ $item->message }}</td>

                                                <td>{{ $item->created_at }}</td>
                                                <td class="sorting_disabled">
                                                    <label class="pr-5 mr-3 switch switch-success published-carousel "
                                                        data-id="{{ $item->id }}">
                                                        <input type="checkbox"
                                                            @if ($item->is_published) checked @endif>
                                                        <span class="slider"></span>
                                                    </label>

                                                </td>
                                                <td>
                                                    <a class="btn btn-primary"
                                                        href="{{ route('message.edit', $item->id) }}">Editer</a> |
                                                    {{-- <a class="btn btn-danger" href="{{route('message.destroy', $item->id)}}">Supprimer</a> --}}
                                                    <a href="#" title="Supprimer"
                                                        class="btn btn-danger btn-icon left-icon" data-toggle="modal"
                                                        data-target="#modal1{{ $item->id }}">Supprimer</a>
                                                </td>

                                                <div class="modal fade" id="modal1{{ $item->id }}" tabindex="-1"
                                                    role="dialog" aria-labelledby="modalLabel{{ $item->id }}"
                                                    aria-hidden="true">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title"
                                                                    id="modalLabel{{ $item->id }}">
                                                                    Suppression
                                                                    de {{ $item->message }}</h5>
                                                                <button type="button" class="close"
                                                                    data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                Etes-vous sûr de supprimer ce message ?
                                                                Cette action est irréversible.
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-dismiss="modal">Annuler</button>
                                                                <a href="{{ route('message.destroy', $item->id) }}"
                                                                    type="button" class="btn btn-danger">Supprimer</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                </tr>
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

    <script>
        $('.published-carousel').on('change', function(event) {
            var checkBoxes = $("input[name=recipients\\[\\]]");
            checkBoxes.prop("checked", !checkBoxes.prop("checked"));
            id = $(this).data('id');
            $.ajax({
                type: 'POST',
                url: "Publication-slider",
                data: {
                    '_token': $('input[name=_token]').val(),
                    'id': id
                },
            });
        });
    </script>

    @push('table')
    @endpush

</x-app-layout>
