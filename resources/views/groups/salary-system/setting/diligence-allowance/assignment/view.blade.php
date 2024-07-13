@extends('layouts.dashboard')

@section('content')
<div>
    <div>
        <div class="container-fluid">
            <div class="title-header">
                <div>
                    <h3 class="m-0">เพิ่มค่าทักษะ</h3>
                </div>
                <div aria-label="breadcrumb">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a
                                href="{{route('groups.salary-system.setting.diligence-allowance')}}">เบี้ยขยัน</a>
                        </li>
                        <li class="breadcrumb-item active">เบี้ยขยัน</li>
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
                            <h4 class="card-title">เบี้ยขยัน</h4>
                        </div>
                        <form
                            action="{{route('groups.salary-system.setting.diligence-allowance.assignment.update',['id' => $diligenceAllowanceClassify->id ])}}"
                            method="POST">
                            <div class="card-body">
                                @method('PUT')
                                @csrf
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>เบี้ยขยัน <span class="fw-bold text-danger">*</span></label>
                                            <input type="text" name="cost"
                                                value="{{old('cost') ?? $diligenceAllowanceClassify->cost}}"
                                                class="form-control @error('name') is-invalid @enderror">
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="cif-modal-footer">
                                <button type="submit" class="btn btn-primary">บันทึก</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script src="{{ asset('assets/js/helpers/helper.js?v=1') }}"></script>
<script>
    $('.select2').select2()
</script>
@endpush
@endsection