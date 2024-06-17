@extends('layouts.setting-dashboard')

@section('content')
<div>
    <div>
        <div class="container-fluid">
            <div class="title-header">
                <div>
                    <h3 class="m-0">ตำแหน่ง: {{$userPosition->name}}</h3>
                </div>
                <div aria-label="breadcrumb">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="#">ตำแหน่ง</a></li>
                        <li class="breadcrumb-item active">{{$userPosition->name}}</li>
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
                            <h4 class="card-title">รายละเอียดข้อมูลตำแหน่ง</h4>
                        </div>
                        <form
                            action="{{ route('setting.general.user-position.update', ['id' => $userPosition->id]) }}"
                            method="POST">
                            <div class="card-body">
                                @method('PUT')
                                @csrf
                                <div class="row gy-2">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>ตำแหน่ง <span class="fw-bold text-danger">*</span></label>
                                            <input type="text" name="name"
                                                value="{{old('name') ?? $userPosition->name}}"
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

</script>
@endpush
@endsection