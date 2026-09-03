<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengingat Deadline</title>
</head>

<body
    style="margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f8fafc;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0"
        style="background-color: #f8fafc; padding: 40px 20px;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellspacing="0" cellpadding="0"
                    style="background-color: #ffffff; border-radius: 16px; box-shadow: 0 4px 24px rgba(0, 0, 0, 0.08); overflow: hidden;">
                    <!-- Header -->
                    <tr>
                        <td
                            style="background: linear-gradient(135deg, {{ $reminderType === 'today' ? '#ef4444' : '#f59e0b' }} 0%, {{ $reminderType === 'today' ? '#f97316' : '#fbbf24' }} 100%); padding: 40px 40px 30px; text-align: center;">
                            <div
                                style="width: 60px; height: 60px; background-color: rgba(255,255,255,0.2); border-radius: 16px; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 16px;">
                                <span style="font-size: 28px;">{{ $reminderType === 'today' ? '⚠️' : '⏰' }}</span>
                            </div>
                            <h1 style="color: #ffffff; margin: 0; font-size: 24px; font-weight: 700;">
                                {{ $reminderType === 'today' ? 'Deadline Hari Ini!' : 'Deadline Besok!' }}
                            </h1>
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style="padding: 40px;">
                            <p style="color: #334155; font-size: 16px; line-height: 1.6; margin: 0 0 20px;">
                                Halo <strong>{{ $user->name }}</strong>,
                            </p>

                            <p style="color: #64748b; font-size: 15px; line-height: 1.6; margin: 0 0 24px;">
                                @if($reminderType === 'today')
                                    <strong style="color: #ef4444;">⚠️ Perhatian!</strong> Tugas berikut harus diselesaikan
                                    <strong>hari ini</strong>:
                                @else
                                    Ini pengingat bahwa tugas berikut akan <strong>deadline besok</strong>:
                                @endif
                            </p>

                            <!-- Task Card -->
                            <div
                                style="background-color: {{ $reminderType === 'today' ? '#fef2f2' : '#fffbeb' }}; border-radius: 12px; padding: 24px; margin-bottom: 24px; border-left: 4px solid {{ $reminderType === 'today' ? '#ef4444' : '#f59e0b' }};">
                                <h2 style="color: #1e293b; font-size: 18px; margin: 0 0 12px; font-weight: 600;">
                                    {{ $task->title }}
                                </h2>

                                @if($task->description)
                                    <p style="color: #64748b; font-size: 14px; line-height: 1.6; margin: 0 0 16px;">
                                        {{ Str::limit($task->description, 150) }}
                                    </p>
                                @endif

                                <table role="presentation" cellspacing="0" cellpadding="0" style="width: 100%;">
                                    <tr>
                                        <td width="50%" style="padding: 6px 0;">
                                            <span
                                                style="color: #94a3b8; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">📅
                                                Deadline</span><br>
                                            <span
                                                style="color: {{ $reminderType === 'today' ? '#ef4444' : '#f59e0b' }}; font-size: 14px; font-weight: 700;">
                                                {{ $task->deadline ? $task->deadline->format('d M Y') : 'Tidak ada' }}
                                                @if($task->deadline_time)
                                                    {{ $task->deadline_time }}
                                                @endif
                                            </span>
                                        </td>
                                        <td width="50%" style="padding: 6px 0;">
                                            <span
                                                style="color: #94a3b8; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">📊
                                                Status</span><br>
                                            <span style="color: #334155; font-size: 14px; font-weight: 600;">
                                                {{ $task->status_label }}
                                            </span>
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            @if($reminderType === 'today')
                                <!-- Urgent Box -->
                                <div
                                    style="background-color: #fef2f2; border: 1px solid #fecaca; border-radius: 12px; padding: 16px 20px; margin-bottom: 24px;">
                                    <p
                                        style="color: #991b1b; font-size: 14px; line-height: 1.5; margin: 0; font-weight: 500;">
                                        🔴 Segera selesaikan dan submit tugas Anda sebelum deadline berakhir!
                                    </p>
                                </div>
                            @endif

                            <!-- Button -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0"
                                style="margin-bottom: 24px;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ route('tasks.show', $task->id) }}"
                                            style="display: inline-block; background: linear-gradient(135deg, {{ $reminderType === 'today' ? '#ef4444' : '#f59e0b' }} 0%, {{ $reminderType === 'today' ? '#f97316' : '#fbbf24' }} 100%); color: #ffffff; text-decoration: none; padding: 14px 36px; border-radius: 12px; font-weight: 600; font-size: 15px; box-shadow: 0 4px 14px rgba({{ $reminderType === 'today' ? '239, 68, 68' : '245, 158, 11' }}, 0.4);">
                                            Kerjakan Sekarang
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p
                                style="color: #94a3b8; font-size: 13px; line-height: 1.6; margin: 0; text-align: center;">
                                Semangat! Kamu pasti bisa menyelesaikannya! 💪
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td
                            style="background-color: #f8fafc; padding: 24px 40px; text-align: center; border-top: 1px solid #e2e8f0;">
                            <p style="color: #94a3b8; font-size: 13px; margin: 0;">
                                &copy; {{ date('Y') }} InternHub. All rights reserved.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>