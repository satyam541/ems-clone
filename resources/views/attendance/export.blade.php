<table class="table table-bordered ">
    <thead>
        <tr>
            <th>Employee</th>
            @foreach ($dateArray as $date)
                <th class="text-center">{{ Carbon\Carbon::parse($date)->format('D, d-M') }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach ($userArray as $name => $attendances)
            <tr>
                <td>{{ $name }}</td>
                @foreach ($attendances as $key => $attendance)
                    @if (empty($attendance['punch_in']))
                        <td>--:--</td>
                    @else
                        <td>{{ Carbon\Carbon::parse($attendance['punch_in'])->format('h:iA') }}<br>
                            <span class="text-danger">
                                {{ !empty($attendance['punch_out']) ? Carbon\Carbon::parse($attendance['punch_out'])->format('h:iA') : '' }}</span>
                        </td>
                    @endif
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>
