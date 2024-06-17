@extends('layouts.setting-dashboard')

@section('content')
<div>
    <div>
        <div class="container-fluid">
            <div class="title-header">
                <div>
                    <h3 class="m-0">เพิ่มตำแหน่ง</h3>
                </div>
                <div>
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="#">ตำแหน่ง</a></li>
                        <li class="breadcrumb-item active">เพิ่มตำแหน่ง</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h4 class="card-title">รายละเอียดตำแหน่ง</h4>
                        </div>
                        <form action="{{route('setting.general.user-position.store')}}" method="POST">
                            @csrf
                            <div class="card-body">
                                <div class="row gy-2">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>ชื่อตำแหน่ง <span class="fw-bold text-danger">*</span></label>
                                            <input type="text" name="name" value="{{old('name')}}"
                                                class="form-control @error('name') is-invalid @enderror">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="cif-modal-footer">
                                <button type="submit"
                                    class="btn btn-primary">บันทึก</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
    $(function () {

    });

</script>
@endpush
@endsection