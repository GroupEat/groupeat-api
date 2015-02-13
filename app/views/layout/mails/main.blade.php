<table id="@yield('mailId', 'groupeat-mail')" width="100%" border="0" cellpadding="0" cellspacing="0" align="center" class="full2" object="drag-module" bgcolor="#303030" c-style="not6BG" style="background-color: rgb(48, 48, 48);">
    <tbody>
    <tr mc:repeatable="">
        <td style="background-color: #2c2c2c; -webkit-background-size: cover; -moz-background-size: cover; -o-background-size: cover; background-size: cover; background-position: center center; background-repeat: no-repeat;" id="not6">
            <div mc:hideable="">
                <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" class="mobile2">
                    <tbody>
                    <tr>
                        <td width="100%" align="center">
                            <div class="sortable_inner ui-sortable">
                                <table width="600" border="0" cellpadding="0" cellspacing="0" align="center" class="full" object="drag-module-small">
                                    <tbody>
                                    <tr>
                                        <td width="600" height="30">
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                                <table width="600" border="0" cellpadding="0" cellspacing="0" align="center" class="mobile2" bgcolor="#e84c3d" style="border-top-left-radius: 5px; border-top-right-radius: 5px; background-color: rgb(232, 76, 61);" object="drag-module-small">
                                    <tbody>
                                    <tr>
                                        <td width="600" valign="middle" align="center" class="logo">

                                            <table width="540" border="0" cellpadding="0" cellspacing="0" align="center" style="text-align: center; border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;" class="fullCenter2">
                                                <tbody>
                                                <tr>
                                                    <td width="100%" height="30">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td width="100%">
                                                        <span object="image-editable" width="391" alt="" border="0" mc:edit="39" style="color: #ffffff; font-size: 33px; font-family: 'proxima_novathin', Helvetica; font-weight: bold;">
                                                          GroupEat
                                                        </span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td width="100%" height="30">
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>

                            <table width="600" border="0" cellpadding="0" cellspacing="0" align="center" class="mobile" object="drag-module-small" style="-webkit-border-bottom-left-radius: 5px; -moz-border-bottom-left-radius: 5px; border-bottom-left-radius: 5px; -webkit-border-bottom-right-radius: 5px; -moz-border-bottom-right-radius: 5px; border-bottom-right-radius: 5px;">
                                <tbody>
                                <tr>
                                    <td width="600" align="center" style="border-bottom-left-radius: 5px; border-bottom-right-radius: 5px; background-color: rgb(255, 255, 255);" bgcolor="#ffffff" c-style="not6Body">
                                        <div class="sortable_inner ui-sortable">
                                            <table width="600" border="0" cellpadding="0" cellspacing="0" align="center" class="mobile2" bgcolor="#ffffff" c-style="not6Body" object="drag-module-small" style="background-color: rgb(255, 255, 255);">
                                                <tbody>
                                                <tr>
                                                    <td width="600" valign="middle" align="center">

                                                        <table width="540" border="0" cellpadding="0" cellspacing="0" align="center" style="text-align: center; border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;" class="fullCenter2">
                                                            <tbody>
                                                            <tr>
                                                                <td width="100%" height="30">
                                                                </td>
                                                            </tr>
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>

                                            @section('firstLineWrapper')
                                                @include('partials.mails.first-line')
                                                @include('partials.mails.vertical-space')
                                            @show

                                            @section('beforeButtonWrapper')
                                                @include('partials.mails.before-button')
                                            @show

                                            @section('buttonWrapper')
                                                @include('partials.mails.button')
                                            @show

                                            @section('afterButtonWrapper')
                                                @include('partials.mails.after-button')
                                            @show

                                            <table width="600" border="0" cellpadding="0" cellspacing="0" align="center" class="mobile2" bgcolor="#ffffff" c-style="not6Body" object="drag-module-small" style="border-bottom-left-radius: 5px; border-bottom-right-radius: 5px; background-color: rgb(255, 255, 255);">
                                                <tbody>
                                                <tr>
                                                    <td width="600" valign="middle" align="center" style="-webkit-border-bottom-left-radius: 5px; -moz-border-bottom-left-radius: 5px; border-bottom-left-radius: 5px; -webkit-border-bottom-right-radius: 5px; -moz-border-bottom-right-radius: 5px; border-bottom-right-radius: 5px;">

                                                        <table width="540" border="0" cellpadding="0" cellspacing="0" align="center" style="text-align: center; border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;" class="fullCenter2">
                                                            <tbody>
                                                            <tr>
                                                                <td width="100%" height="30">
                                                                </td>
                                                            </tr>
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>

                            <table width="600" border="0" cellpadding="0" cellspacing="0" align="center" class="full2" object="drag-module-small">
                                <tbody>
                                <tr>
                                    <td width="600" height="30">
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </td>
    </tr>
    </tbody>
</table>
