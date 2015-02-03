@include('partials.mails.vertical-space')

<table width="600" border="0" cellpadding="0" cellspacing="0" align="center" class="mobile2" bgcolor="#ffffff" c-style="not6Body" object="drag-module-small" style="background-color: rgb(255, 255, 255);">
    <tbody>
    <tr>
        <td width="600" valign="middle" align="center">
            <table width="540" border="0" cellpadding="0" cellspacing="0" align="center" style="text-align: center; border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;" class="fullCenter2">
                <tbody>
                <tr>
                    <td valign="middle" width="100%" style="text-align: left; font-family: Helvetica, Arial, sans-serif; font-size: 14px; color: rgb(63, 67, 69); line-height: 24px;" t-style="not6Text" mc:edit="41">
                        <p object="text-editable"><!--[if !mso]><!-->
                            <span style="font-family: 'proxima_nova_rgregular', Helvetica; font-weight: normal;">
                                <singleline>
                                    @yield('afterButton')
                                </singleline>
                            </span>
                        </p>
                    </td>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>
    </tbody>
</table>
