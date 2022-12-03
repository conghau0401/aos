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
      <div class="row">
        <div class="col-md-12">
          <div class="card">
            <div class="card-header text-right">
                <a href="{{ route('banners.create') }}" class="btn btn-info">Create</a>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th style="width: 10px">#</th>
                    <th>Banner name</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($banners as $banner)
                  <tr>
                    <td>{{ $loop->iteration }}.</td>
                    <td>{{ $banner->name }}</td>
                    <td>
                      <div class="flex flex-btn">
                        <a href="{{ route('banners.show', $banner->id) }}"><span class="btn btn-success">Edit</span></a>
                        <form action="{{ route('banners.destroy', $banner->id) }}" method="POST">
                          @csrf
                          @method('DELETE')
                          <button class="btn btn-danger" onclick="return confirm('Sure Want Delete?')">Delete</button>
                        </form>
                      </div>
                    </td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
            <!-- /.card-body -->
            <div class="card-footer clearfix">
              {{-- {{ $banners->links() }} --}}
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
