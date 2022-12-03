@extends('admin.layouts.master')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.13.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.13.1/jquery-ui.js"></script>
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
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <form method="POST" action="{{ route('products.update', $product->product_no) }}" enctype="multipart/form-data">
                @method('PUT')
                @csrf
                <div class="card-body">
                    {{-- <div class="form-group row">
                        <label for="inputEmail3" class="col-sm-2 col-form-label">Category</label>
                        <div class="col-sm-10">
                            <select class="form-control select2" name="category_no[]" multiple="multiple">
                                <option value="1">Select category</option>
                                {!! $options !!}
                            </select>
                        </div>
                    </div> --}}
                    <div class="form-group row">
                        <label for="inputEmail3" class="col-sm-2 col-form-label">Name</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="product_name" value="{{ $product->product_name }}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="inputEmail3" class="col-sm-2 col-form-label">custom_product_code</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="custom_product_code" value="{{ $product->custom_product_code }}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="inputEmail3" class="col-sm-2 col-form-label">internal_product_name</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="internal_product_name" value="{{ $product->internal_product_name }}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="inputEmail3" class="col-sm-2 col-form-label">product_material</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="product_material" value="{{ $product->product_material }}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="inputEmail3" class="col-sm-2 col-form-label">detail_image</label>
                        <div class="col-sm-10">
                            <input type="file" class="form-control mb-2" name="detail_image">
                            <img src="{{ $product->detail_image }}" width="100" alt="">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="inputEmail3" class="col-sm-2 col-form-label">manufacturer_code</label>
                        <div class="col-sm-10">
                            <select class="form-control" name="manufacturer_code" id="">
                                @foreach ($manufacturers->manufacturers as $item)
                                    <option value="{{ $item->manufacturer_code }}" {{ $item->manufacturer_code == $product->manufacturer_code ? 'selected' : '' }}>{{ $item->manufacturer_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="inputEmail3" class="col-sm-2 col-form-label">Additional Info</label>
                        <div class="col-sm-10">
                            @if (is_array($product->additional_information))
                                @foreach ($product->additional_information as $item)
                                    <div class="form-group row">
                                        <label for="" class="col-sm-3">{{ $item->name }}</label>
                                        <div class="col-sm-6">
                                            <input type="text" class="form-control" name="additional[{{ $item->key }}]" value="{{ $item->value }}">
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="inputEmail3" class="col-sm-2 col-form-label">tax_type</label>
                        <div class="col-sm-10">
                            <select class="form-control" name="tax_type" id="">
                                @foreach (config('aos.tax') as $key => $item)
                                    <option value="{{ $item }}" {{ $item == $product->tax_type ? 'selected' : '' }}>{{ $key }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="inputEmail3" class="col-sm-2 col-form-label">tax_amount</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="tax_amount" value="{{ $product->tax_amount }}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="inputEmail3" class="col-sm-2 col-form-label">maximum_quantity</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="maximum_quantity" value="{{ $product->maximum_quantity }}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="inputEmail3" class="col-sm-2 col-form-label">made_in_code</label>
                        <div class="col-sm-10">
                            <select class="form-control" name="made_in_code" id="">
                                @foreach ($origin as $item)
                                    <option value="{{ $item->made_in_code }}" {{ $item->made_in_code == $product->made_in_code ? 'selected' : '' }}>{{ $item->origin_place_name[1] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="inputEmail3" class="col-sm-2 col-form-label">release_date</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control datepicker" name="release_date" value="{{ $product->release_date }}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="inputEmail3" class="col-sm-2 col-form-label">selling</label>
                        <div class="col-sm-10">
                            <div class="custom-control custom-checkbox">
                                <input class="custom-control-input" name="selling" type="checkbox" id="customSelling" {{ $product->selling == 'T' ? 'checked' : '' }}>
                                <label for="customSelling" class="custom-control-label">selling</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="inputEmail3" class="col-sm-2 col-form-label">Display</label>
                        <div class="col-sm-10">
                            <div class="custom-control custom-checkbox">
                                <input class="custom-control-input" name="display" type="checkbox" id="customDisplay" {{ $product->display == 'T' ? 'checked' : '' }}>
                                <label for="customDisplay" class="custom-control-label">Display</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="inputEmail3" class="col-sm-2 col-form-label">supplier_code</label>
                        <div class="col-sm-10">
                            <select class="form-control" name="supplier_code" id="">
                                @foreach ($suppliers->suppliers as $item)
                                    <option value="{{ $item->supplier_code }}" {{ $item->supplier_code == $product->supplier_code ? 'selected' : '' }}>{{ $item->supplier_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="inputEmail3" class="col-sm-2 col-form-label">summary_description</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" name="summary_description" rows="2">{{ $product->summary_description }}</textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="inputEmail3" class="col-sm-2 col-form-label">description</label>
                        <div class="col-sm-10">
                            <textarea type="text" class="form-control editor" id="description" name="description">{{ $product->description }}</textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="inputEmail3" class="col-sm-2 col-form-label">supply_product_name</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="supply_product_name" value="{{ $product->supply_product_name }}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="inputEmail3" class="col-sm-2 col-form-label">product_tag</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="product_tag" value="{{ $product->product_tag }}">
                        </div>
                    </div>
                    {{-- <div class="form-group row">
                        <label for="inputEmail3" class="col-sm-2 col-form-label">Icons</label>
                        <div class="col-sm-10">
                            <div class="flex icons-flex">
                                @foreach ($icons->icons as $k => $item)
                                    <div class="item-icon">
                                        <input class="" name="icons[]" value="{{ $item->code }}" {{ !empty($product->icon) && in_array($item->code, $product->icon) ? 'checked' : '' }} type="checkbox" id="customCheckbox{{ $k }}">
                                        <label for="customCheckbox{{ $k }}" class=""> <img src="{{ $item->path }}" height="50" alt=""></label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div> --}}
                    {{-- <div class="form-group row">
                        <label for="inputEmail3" class="col-sm-2 col-form-label">Variants</label>
                        <div class="col-sm-10">
                            <div class="wrap-variants">
                                @foreach ($product->variants as $k => $item)
                                    <div class="form-group row">
                                        <label for="" class="col-sm-3">{{ $item->variant_code }}</label>
                                        <div class="col-sm-9">
                                            <table class="table table-bordered">
                                                <tr>
                                                    <td>custom code</td>
                                                    <td><input type="text" class="form-control" value="{{ $item->custom_variant_code }}"></td>
                                                    <td><button type="button" class="btn btn-danger del-variant" data-code="{{ $item->variant_code }}">Delete</button></td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div> --}}
                    <div class="form-group row">
                        <label for="inputEmail3" class="col-sm-2 col-form-label">Add image</label>
                        <div class="col-sm-10">
                            <div class="dropzone" id="dropzone"></div>
                            <div class="image-add row">
                                @if (!empty($product->additionalimages))
                                    @foreach ($product->additionalimages as $item)
                                        <div class="col-lg-3 col-md-4 col-6 item-addition">
                                            <input type="hidden" name="additional_image[]" value="{{ $item->big }}">
                                            <a href="#" class="d-block mb-4 h-100">
                                                <img class="img-fluid img-thumbnail" src="{{ $item->big }}" alt="">
                                            </a>
                                            <button type="button" class="btn btn-danger del-addition-image">X</button>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="form-group text-center">
                        <button class="btn btn-primary">Save</button>
                    </div>
                </div>
                {{-- <input type="hidden" name="old_cate" value="{{ json_encode($listIdCate) }}"> --}}
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
        url: "/admin/product/upload-image",
        paramName: "file", // The name that will be used to transfer the file
        maxFilesize: 2, // MB
        acceptedFiles: 'image/*',
        headers: {
            'X-CSRF-TOKEN': $('input[name="_token"]').val()
        },
        params: {
            idProduct: {{ $product->product_no }},
        },
        success: function(file, response){
            console.log(response);
            $('.image-add').append(`
                <div class="col-lg-3 col-md-4 col-6 item-addition">
                    <input type="hidden" name="additional_image[]" value="${response}">
                    <a href="#" class="d-block mb-4 h-100">
                        <img class="img-fluid img-thumbnail" src="${file.dataURL}" alt="">
                    </a>
                    <button type="button" class="btn btn-danger del-addition-image">X</button>
                </div>
            `);
        }
    });
    $(document).ready(function () {
        // remove addition image
        $(".image-add").on("click",".del-addition-image", function(){
            $(this).parent().remove();
        });
        // delete variant
        $('.del-variant').click(function (e) {
            let code = $(this).data('code');
            if (confirm("Want delete?")) {
                $.ajax({
                    type: "POST",
                    url: "/admin/product/delete-variant",
                    data: {
                        code: code,
                        idProduct: {{ $product->product_no }},
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('input[name="_token"]').val()
                    },
                    success: function (response) {
                        console.log(response)
                    }
                });
            }
        });
        // select2
        $('.select2').select2();
        // datepicker
        $( ".datepicker" ).datepicker({
            dateFormat: 'yy-mm-dd'
        });
    });
</script>
@endsection
