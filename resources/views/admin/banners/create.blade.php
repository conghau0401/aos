@extends('admin.layouts.master')

@section('content')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Banners</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">Banners</li>
          </ol>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <form method="POST" action="{{ route('banners.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    <div class="form-group row">
                        <label for="inputEmail3" class="col-sm-2 col-form-label">Name</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="name" value="">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="inputEmail3" class="col-sm-2 col-form-label">Add image</label>
                        <div class="col-sm-10">
                            <div class="dropzone" id="dropzone"></div>
                            <div class="image-add row">
                            </div>
                        </div>
                    </div>
                    <div class="form-group text-center">
                        <button class="btn btn-primary">Save</button>
                    </div>
                </div>
            </form>
            <!-- /.col -->
        </div>
        <!-- /.row -->
        </div><!-- /.container-fluid -->
    </section>
<!-- /.content -->
</div>
<script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
<link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />

<script>
    Dropzone.autoDiscover = false;
    $("div#dropzone").dropzone({
        url: "/admin/banner/upload-image",
        paramName: "file", // The name that will be used to transfer the file
        maxFilesize: 2, // MB
        acceptedFiles: 'image/*',
        headers: {
            'X-CSRF-TOKEN': $('input[name="_token"]').val()
        },
        params: {

        },
        success: function(file, response){
            console.log(response);
            $('.image-add').append(`
                <div class="col-lg-3 col-md-4 col-6">
                    <div class="item-addition">
                        <input type="hidden" name="images[]" value="${response}">
                        <a href="#" class="d-block mb-4">
                            <img class="img-fluid img-thumbnail" src="${file.dataURL}" alt="">
                        </a>
                        <div class="form-group">
                            <input type="text" class="form-control" name="name_image[]" placeholder="Name">
                        </div>
                        <div class="form-group">
                            <input type="text" class="form-control" name="url[]" placeholder="Link">
                        </div>
                        <button type="button" class="btn btn-danger del-addition-image">X</button>
                    </div>
                </div>
            `);
        }
    });

    $(document).ready(function () {
        // remove addition image
        $(".image-add").on("click",".del-addition-image", function(){
            $(this).parent().parent().remove();
        });
    });
</script>
@endsection
