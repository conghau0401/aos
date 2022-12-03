@extends('admin.layouts.master')
@php
    $number = 1;
@endphp

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
      <div class="row">
        <div class="col-md-12">
          <div class="card">
            <div class="card-header text-right">
              <a href="{{ route('category.create') }}" class="btn btn-info">Create</a>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th style="width: 10px">#</th>
                    <th>Category name</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($categories as $category)
                    <tr>
                      <td>{{ $number }}.</td>
                      <td><a class="text-dark" href="{{ route('category.show', $category->category_no) }}">{{ $category->category_name }}</a></td>
                      <td>
                        <div class="flex flex-btn">
                          <a href="{{ route('category.show', $category->category_no) }}"><span class="btn btn-success">Edit</span></a>
                          <form action="{{ route('category.destroy', $category->category_no) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger" onclick="return confirm('Sure Want Delete?')">Delete</button>
                          </form>
                        </div>
                      </td>
                    </tr>
                    @php
                        $number++;
                    @endphp
                    @foreach ($category->_children as $categorySub1)
                      <tr>
                        <td>{{ $number }}.</td>
                        <td>|--- <a href="{{ route('category.show', $categorySub1->category_no) }}">{{ $categorySub1->category_name }}</a></td>
                        <td>
                          <div class="flex flex-btn">
                            <a href="{{ route('category.show', $categorySub1->category_no) }}"><span class="btn btn-success">Edit</span></a>
                            <form action="{{ route('category.destroy', $categorySub1->category_no) }}" method="POST">
                              @csrf
                              @method('DELETE')
                              <button class="btn btn-danger" onclick="return confirm('Sure Want Delete?')">Delete</button>
                            </form>
                          </div>
                        </td>
                      </tr>
                      @php
                        $number++;
                      @endphp
                      @foreach ($categorySub1->_children as $categorySub2)
                        <tr>
                          <td>{{ $number }}.</td>
                          <td>|---|--- <a href="{{ route('category.show', $categorySub2->category_no) }}">{{ $categorySub2->category_name }}</a></td>
                          <td>
                            <div class="flex flex-btn">
                              <a href="{{ route('category.show', $categorySub2->category_no) }}"><span class="btn btn-success">Edit</span></a>
                              <form action="{{ route('category.destroy', $categorySub2->category_no) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger" onclick="return confirm('Sure Want Delete?')">Delete</button>
                              </form>
                            </div>
                          </td>
                        </tr>
                        @php
                          $number++;
                        @endphp
                        @foreach ($categorySub2->_children as $categorySub3)
                          <tr>
                            <td>{{ $number }}.</td>
                            <td>|---|---|--- <a href="{{ route('category.show', $categorySub3->category_no) }}">{{ $categorySub3->category_name }}</a></td>
                            <td>
                              <div class="flex flex-btn">
                                <a href="{{ route('category.show', $categorySub3->category_no) }}"><span class="btn btn-success">Edit</span></a>
                                <form action="{{ route('category.destroy', $categorySub3->category_no) }}" method="POST">
                                  @csrf
                                  @method('DELETE')
                                  <button class="btn btn-danger" onclick="return confirm('Sure Want Delete?')">Delete</button>
                                </form>
                              </div>
                            </td>
                          </tr>
                          @php
                            $number++;
                          @endphp
                        @endforeach
                      @endforeach
                    @endforeach
                  @endforeach
                </tbody>
              </table>
            </div>
            <!-- /.card-body -->
            <div class="card-footer clearfix">

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
