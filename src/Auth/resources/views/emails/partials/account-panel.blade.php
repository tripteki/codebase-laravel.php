@if (! empty($userName) || ! empty($userEmail))
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 12px;border:1px solid #4b5563;border-radius:10px;background:#1f2937;border-collapse:separate;">
    <tr>
        <td style="padding:10px 14px;font-size:11px;font-weight:600;letter-spacing:0.08em;text-transform:uppercase;color:#9ca3af;border-bottom:1px solid #4b5563;background:#111827;">{{ __('auth.email_user_information') }}</td>
    </tr>
    @if (! empty($userName))
    <tr>
        <td style="padding:8px 14px;{{ ! empty($userEmail) ? 'border-bottom:1px solid #374151;' : '' }}">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td style="font-size:12px;font-weight:600;color:#9ca3af;white-space:nowrap;">{{ $userNameLabel ?? __('auth.full_name') }}</td>
                    <td class="mail-info-value" align="right" style="font-size:13px;font-weight:600;color:#f9fafb;text-align:right;">{{ $userName }}</td>
                </tr>
            </table>
        </td>
    </tr>
    @endif
    @if (! empty($userEmail))
    <tr>
        <td style="padding:8px 14px;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td style="font-size:12px;font-weight:600;color:#9ca3af;white-space:nowrap;">{{ __('auth.email_address') }}</td>
                    <td class="mail-info-value" align="right" style="text-align:right;">
                        <span style="display:inline-block;padding:5px 11px;border-radius:999px;font-size:13px;font-weight:600;background:#111827;color:#e5e7eb;border:1px solid #4b5563;">{{ $userEmail }}</span>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    @endif
</table>
@endif
