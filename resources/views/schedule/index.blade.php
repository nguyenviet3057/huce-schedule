@extends('adminlte::page')

@section('content')
    <table class="table table-bordered table-striped table-hover">
        <thead>
            <tr>
                <th>#</th>
                <th>Giảng viên</th>
                <th>Lớp học phần</th>
                <th>Mã học phần</th>
                <th>Tên môn học</th>
                <th>Tổng số</th>
                <th>Ngày bắt đầu</th>
                <th>Ngày kết thúc</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $item)
            <tr>
                <td>{{ $item->id }}</td>
                <td>{{ $item->teacher }}</td>
                <td>{{ $item->class_name }}</td>
                <td>{{ $item->subject_code }}</td>
                <td>{{ $item->subject_name }}</td>
                <td>{{ $item->total }}</td>
                <td>{{ $item->begin }}</td>
                <td>{{ $item->end }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
  
@endsection

@section('js')
    <script>
        $(function () {
            $('.table').DataTable({
                "paging": true,
                "ordering": true,
                "searching": true,
                "info": true
            });
        });
    </script>
@endsection