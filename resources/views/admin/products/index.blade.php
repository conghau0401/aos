@extends('admin.layouts.master')

@section('content')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Products</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">Products</li>
          </ol>
        </div>
        @if (session('error'))
          <div class="alert alert-danger">
            {{ session('error') }}
          </div>
        @endif
      </div>
    </div><!-- /.container-fluid -->
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="card">
            <!-- /.card-header -->
            <div class="card-body">
              <form action="" id="frm-search" class="mb-3" method="get">
                <div class="input-group">
                  <div class="form-outline">
                    <input type="search" id="form1" class="form-control" name="name" value="{{ request()->get('name') }}" placeholder="Product name" />
                  </div>
                  <button type="submit" class="btn btn-primary btn-search">
                    <i class="fas fa-search"></i>
                  </button>
                </div>
              </form>
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th style="width: 10px">#</th>
                    <th>Product name</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($products->products as $product)
                  <tr>
                    <td>{{ $loop->iteration }}.</td>
                    <td>{{ $product->product_name }}</td>
                    <td>
                      <div class="flex flex-btn">
                        <a href="{{ route('products.show', $product->product_no) }}"><span class="btn btn-success">Edit</span></a>
                        {{-- <form action="{{ route('product.destroy', $product->product_no) }}" method="POST">
                          @csrf
                          @method('DELETE')
                          <button class="btn btn-danger" onclick="return confirm('Sure Want Delete?')">Delete</button>
                        </form> --}}
                      </div>
                    </td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
            <!-- /.card-body -->
            <div class="card-footer clearfix">
              {!! $links !!}
            </div>
          </div>
          <!-- /.card -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </div><!-- /.container-fluid -->
  </section>
  <!-- /.content -->
</div>
@endsection
