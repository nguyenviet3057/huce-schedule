<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <form action="{{ route('submit-alpha') }}" method="POST">
        @csrf
        <table border="1">
            <thead>
                <th height="30">
                    @foreach ($teachers as $teacher)
                        <td width="100">{{ $teacher->teacher_name }}</td>
                    @endforeach
                </th>
            </thead>
            <tbody>
                @foreach ($subjects as $subject_key => $subject)
                <tr height="40">
                    <td>
                        {{ $subject->subject_code. ' - '. $subject->subject_name }}
                    </td>
                    @foreach ($teachers as $teacher)
                        @if (in_array($teacher->teacher_name, $subject->teachers))
                            <td style="background-color: green; text-align-last: center;">
                                @foreach ($subject->alphas as $alpha)
                                    @if ($alpha->teacher_id == $teacher->id)
                                    <input type="text" name="subject[{{ $subject_key }}]" value="{{ $subject->subject_code }}" hidden>
                                    <input type="number" name="alpha[{{ $subject_key }}][{{ $teacher->id }}]" style="max-width: 80px;" value="{{ $alpha->total_classes }}" required>
                                    @endif
                                @endforeach
                            </td>
                        @else
                            <td></td>
                        @endif
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>
        <button type="submit">Submit</button>
    </form>
</body>
</html>