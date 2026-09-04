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
            <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff; border-radius:8px; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,0.08);">

                <!-- Header -->
                <tr>
                    <td style="background:#C0C0C0; padding:20px; text-align:center;">
                        <h2 style="color: #000000; margin:0; font-size:20px;">
                            Task Reminder
                        </h2>
                    </td>
                </tr>

                <!-- Body -->
                <tr>
                    <td style="padding:30px; color:#333333;">

                        <p style="font-size:15px; margin-bottom:20px;">
                            Dear <strong>{{ $recipientName ?? $task->user->name ?? 'User' }}</strong>, 
                        </p>
                        <p style="font-size:15px; margin:0 0 20px; line-height:22px;">
                            This is a friendly reminder from Task Reminder that your task deadline is approaching. Please review your task details and take any necessary action to ensure it is completed on time.
                        </p>
                        <!-- Task Box -->
                        <div style="border:1px solid #dee2e6; border-left:5px solid  #5C5D5F; padding:15px; background: #E8E8E8; margin-bottom:20px;">
                            <p style="margin:0; font-size:14px;">
                                <strong>Task Name:</strong> {{ $title ?? ($task->title ?? 'Task') }}
                            </p>
                            <p style="margin:8px 0 0; font-size:14px;">
                                <strong>Due In:</strong> Approximately <strong>2 hours</strong>
                            </p>
                        </div>

                        <p style="font-size:13px; color:#666;">
                            Please complete it before the deadline to avoid delays.
                        </p>
                    </td>
                </tr>

                <!-- Footer -->
                <tr>
                    <td style="background:#f8f9fa; text-align:center; padding:15px; font-size:12px; color:#777;">
                        This is an automated email. Please do not reply.
                    </td>
                </tr>

            </table>

        </td>
    </tr>
</table>

</body>
</html>