@include('includes.uploader')


@section('js-lib')
    @parent
    <script src="{{ asset('js/lib/jquery/tinymce/tinymce.min.js') }}"></script>
@stop


@section('js')
    @parent
    <script>
        $(function () {
            tinymce.init({
                selector: ".tinymce-editor",
                theme: "modern",
                @if (!empty($full))
                plugins: [
                    "advlist autolink lists link image charmap preview hr",
                    "searchreplace visualblocks visualchars code fullscreen",
                    "insertdatetime nonbreaking table contextmenu directionality",
                    "paste textcolor colorpicker textpattern"
                ],
                toolbar1: "undo redo | styleselect | fontselect | fontsizeselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | forecolor backcolor | preview",
                @else
                plugins: [
                    "autolink lists image preview",
                    "paste textpattern",
                ],
                menubar: false,
                toolbar1: "undo redo | bold italic underline | blockquote bullist numlist | image | preview",
                @endif
                image_advtab: true,
                image_dimensions: false,
                language: "zh_CN",
                convert_urls: false,
                file_picker_callback: function (callback, value, meta) {
                    var upload = $('<input type="file" name="file" data-url="' + "{{ url('V1') }}" + '" multiple>'),
                            self = $(tinymce.activeEditor.getContainer());

                    if (meta.filetype == 'file') {
                    } else if (meta.filetype == 'image') {
                        upload.attr('accept', 'image/*').fileupload({
                            dataType: 'json',
                            done: function (e, data) {
                                var result = data.result;

                                callback(result.url, {alt: result.name});
                                self.before('<input type="hidden" name="detailFilePaths[]" value="' + result.path + '">');
                            }
                        }).trigger('click');
                    } else if (meta.filetype == 'media') {
                        alert('eee');
                    }
                }
            });
        });
    </script>
@stop
