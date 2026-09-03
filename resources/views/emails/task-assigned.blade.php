<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tugas Baru</title>
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
                            style="background: linear-gradient(135deg, #3b82f6 0%, #6366f1 100%); padding: 40px 40px 30px; text-align: center;">
                            <div
                                style="width: 60px; height: 60px; background-color: rgba(255,255,255,0.2); border-radius: 16px; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 16px;">
                                <span style="font-size: 28px;">📋</span>
                            </div>
                            <h1 style="color: #ffffff; margin: 0; font-size: 24px; font-weight: 700;">Tugas Baru!</h1>
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style="padding: 40px;">
                            <p style="color: #334155; font-size: 16px; line-height: 1.6; margin: 0 0 20px;">
                                Halo <strong>{{ $user->name }}</strong>,
                            </p>

                            <p style="color: #64748b; font-size: 15px; line-height: 1.6; margin: 0 0 24px;">
                                Anda mendapat tugas baru yang perlu dikerjakan:
                            </p>

                            <!-- Task Card -->
                            <div
                                style="background-color: #f8fafc; border-radius: 12px; padding: 24px; margin-bottom: 24px; border-left: 4px solid #3b82f6;">
                                <h2 style="color: #1e293b; font-size: 18px; margin: 0 0 12px; font-weight: 600;">
                                    {{ $task->title }}
                                </h2>

                                @if($task->description)
                                    <p style="color: #64748b; font-size: 14px; line-height: 1.6; margin: 0 0 16px;">
                                        {{ Str::limit($task->description, 200) }}
                                    </p>
                                @endif

                                <table role="presentation" cellspacing="0" cellpadding="0" style="width: 100%;">
                                    <tr>
                                        <td width="50%" style="padding: 6px 0;">
                                            <span
                                                style="color: #94a3b8; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">📅
                                                Deadline</span><br>
                                            <span style="color: #334155; font-size: 14px; font-weight: 600;">
                                                {{ $task->deadline ? $task->deadline->format('d M Y') : 'Tidak ada' }}
                                                @if($task->deadline_time)
                                                    {{ $task->deadline_time }}
                                                @endif
                                            </span>
                                        </td>
                                        <td width="50%" style="padding: 6px 0;">
                                            <span
                                                style="color: #94a3b8; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">🎯
                                                Prioritas</span><br>
                                            <span
                                                style="color: {{ $task->priority === 'high' ? '#ef4444' : ($task->priority === 'medium' ? '#f59e0b' : '#22c55e') }}; font-size: 14px; font-weight: 600; text-transform: capitalize;">
                                                {{ $task->priority === 'high' ? 'Tinggi' : ($task->priority === 'medium' ? 'Sedang' : 'Rendah') }}
                                            </span>
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            <!-- Button -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0"
                                style="margin-bottom: 24px;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ route('tasks.show', $task->id) }}"
                                            style="display: inline-block; background: linear-gradient(135deg, #3b82f6 0%, #6366f1 100%); color: #ffffff; text-decoration: none; padding: 14px 36px; border-radius: 12px; font-weight: 600; font-size: 15px; box-shadow: 0 4px 14px rgba(59, 130, 246, 0.4);">
                                            Lihat Detail Tugas
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p
                                style="color: #94a3b8; font-size: 13px; line-height: 1.6; margin: 0; text-align: center;">
                                Segera kerjakan tugas ini sebelum deadline ya! 💪
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