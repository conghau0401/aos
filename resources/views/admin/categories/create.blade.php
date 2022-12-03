@extends('admin.layouts.master')

@section('content')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Categories</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">Categories</li>
          </ol>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <form method="POST" action="{{ route('category.store') }}">
                @csrf
                <div class="card-body">
                    <div class="form-group row">
                        <label for="inputEmail3" class="col-sm-2 col-form-label">Category</label>
                        <div class="col-sm-10">
                            <select class="form-control select2" name="parent_category_no">
                                <option value="0">Select category</option>
                                {!! $options !!}
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="inputEmail3" class="col-sm-2 col-form-label">Name</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="category_name" value="">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="inputEmail3" class="col-sm-2 col-form-label">Description</label>
                        <div class="col-sm-10">
                            <textarea name="category_description" id="" rows="3" class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="inputEmail3" class="col-sm-2 col-form-label">Display</label>
                        <div class="col-sm-10">
                            <div class="custom-control custom-checkbox">
                                <input class="custom-control-input" name="use_display" type="checkbox" id="customCheckbox2">
                                <label for="customCheckbox2" class="custom-control-label">Display</label>
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
@endsection
