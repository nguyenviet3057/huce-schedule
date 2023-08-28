@extends('adminlte::page')

@section('content')
    <table class="table table-bordered table-striped table-hover">
        <thead>
            <tr>
                <th>#</th>
                <th>Mã học phần</th>
                <th>Mã lớp học</th>
                <th>Lớp ghép</th>
                <th>Giảng viên</th>
                <th>Kỳ học</th>
                <th>Ca học</th>
                <th>Thứ</th>
                <th>Ngày bắt đầu</th>
                <th>Ngày kết thúc</th>
                <th>Tuần</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $key => $item)
            <tr>
                <td>{{ $key + 1 }}</td>
                <td>{{ $item->CLASS_CODE }}</td>
                <td>{{ $item->ENROLL_CLASS }}</td>
                <td>{{ $item->MERGED_CLASS }}</td>
                <td>{{ $item->LEC_CODE }}</td>
                <td>{{ $item->SEMESTER_ID }}</td>
                <td>{{ $item->SESSION }}</td>
                <td>{{ $item->DAY_STUDY }}</td>
                <td>{{ $item->START_DATE }}</td>
                <td>{{ $item->END_DATE }}</td>
                <td>{{ $item->WEEKS }}</td>
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
                "ordering": false,
                "searching": true,
                "info": true
            });
        });
    </script>
@endsection