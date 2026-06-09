@if (! empty($url))
    <table class="cta-wrap" role="presentation" cellspacing="0" cellpadding="0" border="0" align="center">
        <tr>
            <td align="center" class="cta-cell">
                <a href="{{ $url }}" class="btn btn-primary">{{ $label }}</a>
            </td>
        </tr>
    </table>
@endif
