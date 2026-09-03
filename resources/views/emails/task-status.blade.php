<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Status Tugas</title>
</head>

<body
    style="margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f8fafc;">
    @php
        $colors = match ($statusType) {
            'approved' => ['#22c55e', '#10b981', '34, 197, 94'],
            'revision' => ['#f59e0b', '#fbbf24', '245, 158, 11'],
            'submitted' => ['#06b6d4', '#22d3ee', '6, 182, 212'],
            default => ['#6366f1', '#818cf8', '99, 102, 241'],
        };
        $icon = match ($statusType) {
            'approved' => '✅',
            'revision' => '🔄',
            'submitted' => '📤',
            default => '📋',
        };
        $title = match ($statusType) {
            'approved' => 'Tugas Disetujui!',
            'revision' => 'Perlu Revisi',
            'submitted' => 'Tugas Dikirim',
            default => 'Update Tugas',
        };
    @endphp

    <table role="presentation" width="100%" cellspacing="0" cellpadding="0"
        style="background-color: #f8fafc; padding: 40px 20px;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellspacing="0" cellpadding="0"
                    style="background-color: #ffffff; border-radius: 16px; box-shadow: 0 4px 24px rgba(0, 0, 0, 0.08); overflow: hidden;">
                    <!-- Header -->
                    <tr>
                        <td
                            style="background: linear-gradient(135deg, {{ $colors[0] }} 0%, {{ $colors[1] }} 100%); padding: 40px 40px 30px; text-align: center;">
                            <div
                                style="width: 60px; height: 60px; background-color: rgba(255,255,255,0.2); border-radius: 16px; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 16px;">
                                <span style="font-size: 28px;">{{ $icon }}</span>
                            </div>
                            <h1 style="color: #ffffff; margin: 0; font-size: 24px; font-weight: 700;">{{ $title }}</h1>
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style="padding: 40px;">
                            <p style="color: #334155; font-size: 16px; line-height: 1.6; margin: 0 0 20px;">
                                Halo <strong>{{ $user->name }}</strong>,
                            </p>

                            @if($statusType === 'approved')
                                <p style="color: #64748b; font-size: 15px; line-height: 1.6; margin: 0 0 24px;">
                                    Selamat! 🎉 Tugas Anda telah <strong style="color: #22c55e;">disetujui</strong>. Berikut
                                    detailnya:
                                </p>
                            @elseif($statusType === 'revision')
                                <p style="color: #64748b; font-size: 15px; line-height: 1.6; margin: 0 0 24px;">
                                    Tugas Anda <strong style="color: #f59e0b;">memerlukan revisi</strong>. Silakan perbaiki
                                    sesuai feedback:
                                </p>
                            @elseif($statusType === 'submitted')
                                <p style="color: #64748b; font-size: 15px; line-height: 1.6; margin: 0 0 24px;">
                                    Ada tugas baru yang <strong style="color: #06b6d4;">dikirim</strong> dan memerlukan
                                    review Anda:
                                </p>
                            @endif

                            <!-- Task Card -->
                            <div
                                style="background-color: #f8fafc; border-radius: 12px; padding: 24px; margin-bottom: 24px; border-left: 4px solid {{ $colors[0] }};">
                                <h2 style="color: #1e293b; font-size: 18px; margin: 0 0 16px; font-weight: 600;">
                                    {{ $task->title }}
                                </h2>

                                @if($statusType === 'approved' && $task->score)
                                    <div
                                        style="background-color: #dcfce7; border-radius: 8px; padding: 12px 16px; margin-bottom: 16px;">
                                        <span style="color: #166534; font-size: 14px; font-weight: 600;">
                                            🏆 Nilai: {{ $task->score }}/100
                                        </span>
                                    </div>
                                @endif

                                @if(($statusType === 'approved' || $statusType === 'revision') && $task->admin_feedback)
                                    <div
                                        style="background-color: {{ $statusType === 'revision' ? '#fef3c7' : '#f0fdf4' }}; border-radius: 8px; padding: 16px; margin-bottom: 16px;">
                                        <p
                                            style="color: #94a3b8; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; margin: 0 0 8px;">
                                            💬 Feedback dari Pembimbing
                                        </p>
                                        <p style="color: #334155; font-size: 14px; line-height: 1.6; margin: 0;">
                                            {{ $task->admin_feedback }}
                                        </p>
                                    </div>
                                @endif

                                @if($statusType === 'submitted' && $task->intern)
                                    <p style="color: #64748b; font-size: 14px; margin: 0 0 8px;">
                                        <strong>Dikirim oleh:</strong> {{ $task->intern->user->name ?? 'Intern' }}
                                    </p>
                                @endif

                                @if($task->submitted_at)
                                    <p style="color: #94a3b8; font-size: 13px; margin: 0;">
                                        📅 Dikirim: {{ $task->submitted_at->format('d M Y H:i') }}
                                    </p>
                                @endif
                            </div>

                            @if($statusType === 'revision')
                                <div
                                    style="background-color: #fef3c7; border: 1px solid #fde68a; border-radius: 12px; padding: 16px 20px; margin-bottom: 24px;">
                                    <p style="color: #92400e; font-size: 14px; line-height: 1.5; margin: 0;">
                                        ⚠️ Segera lakukan revisi dan submit ulang tugas Anda.
                                    </p>
                                </div>
                            @endif

                            <!-- Button -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0"
                                style="margin-bottom: 24px;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ route('tasks.show', $task->id) }}"
                                            style="display: inline-block; background: linear-gradient(135deg, {{ $colors[0] }} 0%, {{ $colors[1] }} 100%); color: #ffffff; text-decoration: none; padding: 14px 36px; border-radius: 12px; font-weight: 600; font-size: 15px; box-shadow: 0 4px 14px rgba({{ $colors[2] }}, 0.4);">
                                            @if($statusType === 'submitted')
                                                Review Tugas
                                            @else
                                                Lihat Detail
                                            @endif
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            @if($statusType === 'approved')
                                <p
                                    style="color: #94a3b8; font-size: 13px; line-height: 1.6; margin: 0; text-align: center;">
                                    Kerja bagus! Tetap semangat untuk tugas-tugas berikutnya! 🚀
                                </p>
                            @endif
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