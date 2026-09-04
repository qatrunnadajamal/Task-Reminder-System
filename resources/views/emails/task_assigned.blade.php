<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Task Reminder</title>
</head>

<body style="margin:0; padding:0; background-color:#f4f6f9; font-family:Arial, sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="padding:30px 0;">
    <tr>
        <td align="center">

            <!-- Container -->
            <table width="600" cellpadding="0" cellspacing="0"
                style="background:#ffffff; border-radius:8px; overflow:hidden; border:1px solid #dddddd;">

                <!-- Header -->
                <tr>
                    <td style="background:#C0C0C0; padding:20px; text-align:center;">
                        <h2 style="margin:0; font-size:20px; color:#000000;">
                            Task Reminder
                        </h2>
                    </td>
                </tr>

                <!-- Body -->
                <tr>
                    <td style="padding:30px; color:#333333;">

                        <!-- Greeting -->
                        <p style="font-size:15px; margin:0 0 15px;">
                            Dear <strong>{{ $assignee->name ?? 'User' }}</strong>,
                        </p>

                        <!-- Creator Message -->
                        <p style="font-size:15px; margin:0 0 20px; line-height:22px;">
                            <strong>{{ $task->user->name ?? 'The task creator' }}</strong>
                            has set a reminder for you.
                        </p>

                        <!-- Task Box -->
                        <table width="100%" cellpadding="0" cellspacing="0"
                            style="border:1px solid #dee2e6; background:#E8E8E8; margin-bottom:20px;">

                            <tr>
                                <td width="5" bgcolor="#5C5D5F"></td>

                                <td style="padding:15px;">

                                    <p style="margin:0; font-size:14px;">
                                        <strong>Task Name:</strong><br>
                                        {{ $title ?? ($task->title ?? 'Task') }}
                                    </p>

                                    <p style="margin:15px 0 0; font-size:14px;">
                                        <strong>Due:</strong><br>

                                        @if(!empty($task->due_task))
                                            {{ \Carbon\Carbon::parse($task->due_task)->format('M d, Y h:i A') }}
                                        @else
                                            Not specified
                                        @endif

                                    </p>

                                </td>
                            </tr>

                        </table>

                        <!-- Reminder Text -->
                        <p style="font-size:13px; color:#666666; margin-bottom:25px;">
                            Please complete it before the deadline to avoid delays.
                        </p>

                        <!-- Button -->
                        <table align="center" cellpadding="0" cellspacing="0">
                            <tr>
                                <td bgcolor="#5C5D5F" style="border-radius:4px;">

                                    <a href="{{ route('task.invitation.accept', ['token' => $invitation->token]) }}"
                                        style="display:inline-block;
                                               padding:12px 24px;
                                               color:#ffffff;
                                               text-decoration:none;
                                               font-size:14px;
                                               font-weight:bold;">
                                        Add to My Task
                                    </a>

                                </td>
                            </tr>
                        </table>

                    </td>
                </tr>

                <!-- Footer -->
                <tr>
                    <td style="background:#f8f9fa; text-align:center; padding:15px; font-size:12px; color:#777777; border-top:1px solid #e5e5e5;">
                        This is an automated email. Please do not reply.
                    </td>
                </tr>

            </table>

        </td>
    </tr>
</table>

</body>
</html>