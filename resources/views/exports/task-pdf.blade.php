<!DOCTYPE html>
<html>
<head>
    <title>Task PDF</title>
    <style>
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid black; padding: 8px; }
    </style>
</head>
<body>

<h2>List of Task Reminder</h2>
<br>
<div class="report-date">
     Generated on: {{ now()->format('d M Y') }}
</div>

 <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <!-- TABLE HEADER -->
                    <thead class="table-light">
                    <tr>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Due Date</th>
                        <th>Status</th>
                        <th>Priority</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($data as $task)
                <tr>
                    <td>{{ $task->title }}</td>
                    <td>{!! $task->description !!}</td>
                    <td>{{ $task->due_task }}</td>
                    <td>{{ $task->status }}</td>
                    <td>{{ $task->priority_level }}</td>
                </tr>
                @endforeach
                </tbody>
</table>

</body>
</html>