@include('partials.mails.vertical-space')

<table width="600" border="0" cellpadding="0" cellspacing="0" align="center" class="mobile2" bgcolor="#ffffff" c-style="not6Body" object="drag-module-small" style="background-color: rgb(255, 255, 255);">
    <tbody>
    <tr>
        <td width="600" valign="middle" align="center">
            <table border="0" cellpadding="0" cellspacing="0" align="center" style="text-align: center; border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;" class="fullCenter2">
                <tbody>
                <tr>
                    <td align="center" height="45" c-style="not6ButBG" bgcolor="#e77e23" style="border-radius: 5px; padding-left: 30px; padding-right: 30px; font-weight: bold; font-family: Helvetica, Arial, sans-serif; color: rgb(255, 255, 255); background-color: rgb(231, 126, 35);" t-style="not6ButText" mc:edit="42">
                        <multiline>
                            <span style="font-family: 'proxima_nova_rgbold', Helvetica; font-weight: normal;">
                                <a id="@yield('buttonId', 'mail-button')" href="@yield('buttonUrl', '#')" style="color: rgb(255, 255, 255); font-size: 15px; text-decoration: none; line-height: 34px; width: 100%;" t-style="not6ButText" object="link-editable">
                                    @yield('button')
                                </a>
                            </span>
                        </multiline>
                    </td>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>
    </tbody>
</table>
